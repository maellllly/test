<?php

namespace App\Traits;

use DB;
use App;
use File;
use Config;
use Session;
use DateTime;
use Response;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;

use App\Ticket;
use App\History;
use App\LibHeads;
use App\LibBrand;
use App\ESDAccount;
use App\UserNotification;

trait JsonQueries
{

    public function isHead()
    {
        return LibHeads::where('account_id', Session('userData')->account_id)->count();
    }

    public function searchTix(Request $request)
    {
        $_aoID = base64_decode($request->aoID);

        return response()->json(['data' => Ticket::ticketQry()->joinCarbon()
                                                    ->where('ticket.account_owner_id', $_aoID)
                                                    ->orWhere('ticket.requestor_id', $_aoID)
                                                    ->orWhere('cc.account_id', $_aoID)
                                                    ->get()]);
    }
   
    public function getTicket(Request $request)
    {  
        $_role = Session::get('userData')->role_name;
        $_statusID = $request->sid;
        $_tTypeID = $request->tid;

        if($_role == 'admin' || $_role == 'super_user') {
            if ($_statusID == '9') {
                $query = Ticket::getWithAssignment()->Deleted()->transactionID($_tTypeID);
            } else if ($_statusID == '10') {
                $query = Ticket::ticketQry();
            } else {
                $query = Ticket::ticketQry()->transactionID($_tTypeID)->statusID($_statusID);
            }
        }

        if ($_role == 'buyer') {
            // 1   Pending | 2   Answered
            if($_statusID == '1') {
                $query = Ticket::getWithAssignment()->Unanswered()->NotDeleted()->transactionID($_tTypeID)->notPendingDeclined();
            } else if ($_statusID == '2') {
                $query = Ticket::getWithAssignment()->Answered()->NotDeleted()->transactionID($_tTypeID)->notClosed();
            } else if($_statusID == '3') {
                $query = Ticket::getWithAssignment()->NotDeleted()->transactionID($_tTypeID)->statusID($_statusID);
            } else if (in_array($_statusID, ['5', '6', '7', '8'])) {
                $query = Ticket::ticketQry()->transactionID($_tTypeID)->statusID($_statusID);
            } else if ($_statusID == '9') {
                $query = Ticket::getWithAssignment()->Deleted()->transactionID($_tTypeID);
            } else if ($_statusID == '10') {
                $query = Ticket::ticketQry();
            }
        }  

        if ($_role == 'requestor') {

            if(Session('userData')->account_id == 415) {

                if ($_statusID == '10') {
                    $query = Ticket::ticketQry()->accountGroupAdel();
                } else {
                    $query = Ticket::ticketQry()->transactionID($_tTypeID)->statusID($_statusID)->accountGroupAdel();
                }
            } else {

                if ($_statusID == '10') {
                    $query = Ticket::ticketQry()->joinCarbon()->Where(function($query) {
                        $query->AccountGroup()->orWhere('cc.account_id', Session('userData')->account_id);
                    });
                } else {
                    if(Session('userData')->AccountGroup == 'BU2' || Session('userData')->AccountGroup == 'BU5' || Session('userData')->AccountGroup == 'BU10') { 
                        $query = Ticket::ticketQry()->transactionID($_tTypeID)->statusID($_statusID)->AccountGroup();
                    } else {
                        if($this->isHead() >= 1) {
                            $query = Ticket::ticketQry()->transactionID($_tTypeID)->statusID($_statusID)->AccountGroup();
                        } else {
                            $query = Ticket::ticketQry()->joinCarbon()
                                                        ->transactionID($_tTypeID)->statusID($_statusID)->TixAccOwner()
                                                        ->orWhere->TixReqOwner()->transactionID($_tTypeID)->statusID($_statusID)
                                                        ->orWhere->CC()->transactionID($_tTypeID)->statusID($_statusID);
                        }
                    }
                }
            }
        }

        return Datatables::of($query)
                        ->orderColumn('status_id', 'last_updated $1')
                        ->blacklist(['status_id'])
                        ->filterColumn('ticket_content', function($query, $keyword) {
                            $query->whereRaw("CONCAT( ticket_content, ticket_reply, esao.AccountName, esrid.AccountName, OwnerName) LIKE ?", ["%{$keyword}%"]);
                        })
                        ->make(true);
    }

    public function getAvailableTicket(Request $request)
    {
        return response()->json(['data' => Ticket::TixAccOwner()->notInTicket(base64_decode($request->tid))
                                                        ->orWhere->TixReqOwner()->notInTicket(base64_decode($request->tid))->get()]);
    }

    public function getBrand(Request $request)
    {
        $_tTypeID = $request->tid;

        if($_tTypeID == 1) {
            $data = LibBrand::brandQry()->focus()->orderBy('brand', 'asc')->get();
        } else if($_tTypeID == 2) {
            $data = LibBrand::brandQry()->nonFocus()->topOthers()->orderBy('sortpreference', 'asc')->orderBy('brand', 'asc')->get();
        }

        return response()->json(['data' => $data]);

    }  

    public function unreadNotification()
    {
        return response()->json(['data' => UserNotification::OwnNotif()->Unread()->orderBy('user_notification.notification_id','desc')->take(10)->get()]);
    }

    public function countUnread()
    {
        return response()->json(['count' => UserNotification::OwnNotif()->Unread()->get()->count()]);
    }

    public function getHistory(Request $request)
    {
        $data = History::getHistory(base64_decode($request->tid));

        return response()->json(['data' => $data]);
    }

        //Sidebar Counter
    public function getSideCount()
    {
        $_role = Session::get('userData')->role_name;

        if($_role == 'buyer') {
           //Buyer && Focus
            $fpendingctr = Ticket::joinAssign()->Assigned()->NotDeleted()->Unanswered()->transactionID(1)->getCount();
            $fansweredctr = Ticket::joinAssign()->Assigned()->NotDeleted()->Answered()->transactionID(1)->notClosed()->getCount();
            $fclosedctr = Ticket::joinAssign()->Assigned()->NotDeleted()->transactionID(1)->statusID(3)->getCount();
            $freassignedctr = Ticket::joinAssign()->Assigned()->Deleted()->transactionID(1)->getCount();

            //Buyer && Non Focus
            $nfbuheadapprovalctr = Ticket::transactionID(2)->statusID(5)->getCount();
            $nfbuheaddeclinedctr = Ticket::transactionID(2)->statusID(6)->getCount();

            $nffinalapprovalctr = Ticket::transactionID(2)->statusID(7)->getCount();
            $nffinaldeclinedctr = Ticket::transactionID(2)->statusID(8)->getCount();
            $nfpendingctr = Ticket::joinAssign()->Assigned()->NotDeleted()->Unanswered()->transactionID(2)->notPendingDeclined()->getCount();
            $nfansweredctr = Ticket::joinAssign()->Assigned()->NotDeleted()->Answered()->transactionID(2)->notClosed()->getCount();
            $nfclosedctr = Ticket::joinAssign()->Assigned()->NotDeleted()->transactionID(2)->statusID(3)->getCount();
            $nfreassignedctr = Ticket::joinAssign()->Assigned()->Deleted()->transactionID(2)->getCount();

            return response()->json(['fpendingctr' => $fpendingctr, 'fansweredctr' => $fansweredctr, 'fclosedctr' => $fclosedctr, 'freassignedctr' => $freassignedctr,
                'nfbuheadapprovalctr' => $nfbuheadapprovalctr, 'nfbuheaddeclinedctr' => $nfbuheaddeclinedctr, 
                'nffinalapprovalctr' => $nffinalapprovalctr, 'nffinaldeclinedctr' => $nffinaldeclinedctr, 
                'nfpendingctr' => $nfpendingctr, 'nfansweredctr' => $nfansweredctr, 'nfclosedctr' => $nfclosedctr, 
                'nfreassignedctr' => $nfreassignedctr]);
        }

        if($_role == 'admin' || $_role == 'super_user') {
            //Focus
            $fpendingctr = Ticket::transactionID(1)->statusID(1)->getCount();
            $fansweredctr = Ticket::transactionID(1)->statusID(2)->getCount();
            $fclosedctr = Ticket::transactionID(1)->statusID(3)->getCount();
            $freassignedctr = Ticket::joinAssign()->Assigned()->Deleted()->transactionID(1)->getCount();

            //Non Focus
            $nfbuheadapprovalctr = Ticket::transactionID(2)->statusID(5)->getCount();
            $nfbuheaddeclinedctr = Ticket::transactionID(2)->statusID(6)->getCount();

            $nffinalapprovalctr = Ticket::transactionID(2)->statusID(7)->getCount();
            $nffinaldeclinedctr = Ticket::transactionID(2)->statusID(8)->getCount();

            $nfpendingctr = Ticket::transactionID(2)->statusID(1)->getCount();
            $nfansweredctr = Ticket::transactionID(2)->statusID(2)->getCount();
            $nfclosedctr = Ticket::transactionID(2)->statusID(3)->getCount();
            $nfreassignedctr = Ticket::joinAssign()->Assigned()->Deleted()->transactionID(2)->getCount();

            return response()->json(['fpendingctr' => $fpendingctr, 'fansweredctr' => $fansweredctr, 'fclosedctr' => $fclosedctr, 'freassignedctr' => $freassignedctr,
                'nfbuheadapprovalctr' => $nfbuheadapprovalctr, 'nfbuheaddeclinedctr' => $nfbuheaddeclinedctr, 
                'nffinalapprovalctr' => $nffinalapprovalctr, 'nffinaldeclinedctr' => $nffinaldeclinedctr, 
                'nfpendingctr' => $nfpendingctr, 'nfansweredctr' => $nfansweredctr, 'nfclosedctr' => $nfclosedctr, 
                'nfreassignedctr' => $nfreassignedctr]);
        }

        if($_role == 'requestor') {

            if(Session('userData')->account_id == 415) {
                    $fpendingctr = Ticket::joinESAO()->transactionID(1)->statusID(1)->accountGroupAdel()->getCount();
                    $fansweredctr = Ticket::joinESAO()->transactionID(1)->statusID(2)->accountGroupAdel()->getCount();
                    $fclosedctr = Ticket::joinESAO()->transactionID(1)->statusID(3)->accountGroupAdel()->getCount();

                    //Non Focus
                    $nfbuheadapprovalctr = Ticket::joinESAO()->transactionID(2)->statusID(5)->accountGroupAdel()->getCount();
                    $nfbuheaddeclinedctr = Ticket::joinESAO()->transactionID(2)->statusID(6)->accountGroupAdel()->getCount();

                    $nffinalapprovalctr = Ticket::joinESAO()->transactionID(2)->statusID(7)->accountGroupAdel()->getCount();
                    $nffinaldeclinedctr = Ticket::joinESAO()->transactionID(2)->statusID(8)->accountGroupAdel()->getCount();

                    $nfpendingctr = Ticket::joinESAO()->transactionID(2)->statusID(1)->accountGroupAdel()->getCount();
                    $nfansweredctr = Ticket::joinESAO()->transactionID(2)->statusID(2)->accountGroupAdel()->getCount();
                    $nfclosedctr = Ticket::joinESAO()->transactionID(2)->statusID(3)->accountGroupAdel()->getCount();
            } else {
                if(Session('userData')->AccountGroup == 'BU2' || Session('userData')->AccountGroup == 'BU5' || Session('userData')->AccountGroup == 'BU10') {
                    //Focus
                    $fpendingctr = Ticket::joinESAO()->transactionID(1)->statusID(1)->AccountGroup()->getCount();
                    $fansweredctr = Ticket::joinESAO()->transactionID(1)->statusID(2)->AccountGroup()->getCount();
                    $fclosedctr = Ticket::joinESAO()->transactionID(1)->statusID(3)->AccountGroup()->getCount();

                    //Non Focus
                    $nfbuheadapprovalctr = Ticket::joinESAO()->transactionID(2)->statusID(5)->AccountGroup()->getCount();
                    $nfbuheaddeclinedctr = Ticket::joinESAO()->transactionID(2)->statusID(6)->AccountGroup()->getCount();

                    $nffinalapprovalctr = Ticket::joinESAO()->transactionID(2)->statusID(7)->AccountGroup()->getCount();
                    $nffinaldeclinedctr = Ticket::joinESAO()->transactionID(2)->statusID(8)->AccountGroup()->getCount();

                    $nfpendingctr = Ticket::joinESAO()->transactionID(2)->statusID(1)->AccountGroup()->getCount();
                    $nfansweredctr = Ticket::joinESAO()->transactionID(2)->statusID(2)->AccountGroup()->getCount();
                    $nfclosedctr = Ticket::joinESAO()->transactionID(2)->statusID(3)->AccountGroup()->getCount();
                } else {
                    if($this->isHead() >= 1) {
                    //Focus
                    $fpendingctr = Ticket::joinESAO()->transactionID(1)->statusID(1)->AccountGroup()->getCount();
                    $fansweredctr = Ticket::joinESAO()->transactionID(1)->statusID(2)->AccountGroup()->getCount();
                    $fclosedctr = Ticket::joinESAO()->transactionID(1)->statusID(3)->AccountGroup()->getCount();

                    //Non Focus
                    $nfbuheadapprovalctr = Ticket::joinESAO()->transactionID(2)->statusID(5)->AccountGroup()->getCount();
                    $nfbuheaddeclinedctr = Ticket::joinESAO()->transactionID(2)->statusID(6)->AccountGroup()->getCount();

                    $nffinalapprovalctr = Ticket::joinESAO()->transactionID(2)->statusID(7)->AccountGroup()->getCount();
                    $nffinaldeclinedctr = Ticket::joinESAO()->transactionID(2)->statusID(8)->AccountGroup()->getCount();;


                    $nfpendingctr = Ticket::joinESAO()->transactionID(2)->statusID(1)->AccountGroup()->getCount();
                    $nfansweredctr = Ticket::joinESAO()->transactionID(2)->statusID(2)->AccountGroup()->getCount();
                    $nfclosedctr = Ticket::joinESAO()->transactionID(2)->statusID(3)->AccountGroup()->getCount();
                    } else {
                        //Focus
                        $fpendingctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(1)->statusID(1)
                                            ->orWhere->TixReqOwner()->transactionID(1)->statusID(1)
                                            ->orWhere->CC()->transactionID(1)->statusID(1)
                                            ->getCount();
                        $fansweredctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(1)->statusID(2)
                                            ->orWhere->TixReqOwner()->transactionID(1)->statusID(2)
                                            ->orWhere->CC()->transactionID(1)->statusID(2)
                                            ->getCount();
                        $fclosedctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(1)->statusID(3)
                                            ->orWhere->TixReqOwner()->transactionID(1)->statusID(3)
                                            ->orWhere->CC()->transactionID(1)->statusID(3)
                                            ->getCount();

                        //Non Focus


                        $nfbuheadapprovalctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(2)->statusID(5)
                                            ->orWhere->TixReqOwner()->transactionID(2)->statusID(5)
                                            ->orWhere->CC()->transactionID(2)->statusID(5)
                                            ->getCount();
                        $nfbuheaddeclinedctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(2)->statusID(6)
                                            ->orWhere->TixReqOwner()->transactionID(2)->statusID(6)
                                            ->orWhere->CC()->transactionID(2)->statusID(6)
                                            ->getCount();

                        $nffinalapprovalctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(2)->statusID(5)
                                            ->orWhere->TixReqOwner()->transactionID(2)->statusID(7)
                                            ->orWhere->CC()->transactionID(2)->statusID(7)
                                            ->getCount();
                        $nffinaldeclinedctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(2)->statusID(6)
                                            ->orWhere->TixReqOwner()->transactionID(2)->statusID(8)
                                            ->orWhere->CC()->transactionID(2)->statusID(8)
                                            ->getCount();


                        $nfpendingctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(2)->statusID(1)
                                            ->orWhere->TixReqOwner()->transactionID(2)->statusID(1)
                                            ->orWhere->CC()->transactionID(2)->statusID(1)
                                            ->getCount();
                        $nfansweredctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(2)->statusID(2)
                                            ->orWhere->TixReqOwner()->transactionID(2)->statusID(2)
                                            ->orWhere->CC()->transactionID(2)->statusID(2)
                                            ->getCount();
                        $nfclosedctr = Ticket::TixAccOwner()->joinCarbon()->transactionID(2)->statusID(3)
                                            ->orWhere->TixReqOwner()->transactionID(2)->statusID(3)
                                            ->orWhere->CC()->transactionID(2)->statusID(3)
                                            ->getCount();
                    }

                }
            }


            

            return response()->json(['fpendingctr' => $fpendingctr, 'fansweredctr' => $fansweredctr, 'fclosedctr' => $fclosedctr,
                'nfbuheadapprovalctr' => $nfbuheadapprovalctr, 'nfbuheaddeclinedctr' => $nfbuheaddeclinedctr, 
                'nffinalapprovalctr' => $nffinalapprovalctr, 'nffinaldeclinedctr' => $nffinaldeclinedctr, 
                'nfpendingctr' => $nfpendingctr, 'nfansweredctr' => $nfansweredctr, 'nfclosedctr' => $nfclosedctr]);
        }

       
    }
}