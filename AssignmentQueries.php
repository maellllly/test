<?php

namespace App\Traits;

use DB;
use App;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Ticket;
use App\CarbonCopy;
use App\ESDAccount;
use App\Assignment;
use App\CRMAccount;

trait AssignmentQueries
{

    public function insertAssignment($_ticketID, $_owner_id)
    {
        if(!empty($_owner_id)) {
            foreach ($_owner_id as $key => $account_id) {
                $_insertAssignment = new Assignment();
                $_insertAssignment->ticket_id = $_ticketID;
                $_insertAssignment->owner_id = $account_id;
                $_insertAssignment->date_assigned = \Carbon\Carbon::now()->format('m/d/Y H:i:s'); 
                $_insertAssignment->is_read = 0;
                $_insertAssignment->is_answered = 0;
                $_insertAssignment->is_deleted = 0;
                $_insertAssignment->save();
            }
        }
    }

    public function checkReplyStatus($request) //Check if reply count is equal to answered 
    {
        $_assignee = Assignment::ticketID($this->tixParamUrl)->notDeleted();
        $_assigneeAns = Assignment::ticketID($this->tixParamUrl)->notDeleted()->answered();

        if($_assignee->count() == $_assigneeAns->count()) {
            $request->request->add(['statusID' => '2']);
        } else {
            $request->request->add(['statusID' => '1']);
        }

        $this->updateTicketStatus($request);
    }

    public function updateReadStatus(Request $request)
    {
        $_checkQry = Assignment::validBuyer($request->ticketID);

        if($_checkQry->count() > 0) {
            $_checkQry = $_checkQry->get();

            if($_checkQry[0]->is_deleted == 0 && $_checkQry[0]->is_answered == 0 && $_checkQry[0]->is_read == 0) {
                $_makeRead = Assignment::find($_checkQry[0]->assignment_id);
                $_makeRead->is_read = 1;
                $_makeRead->save();

                $request->request->add(['history' => \Common::instance()->getCurrentUserName().' seen Ticket#'.sprintf('%04d', $request->ticketID).'.']);
                $this->insertHistory($request);
                $this->lastUpdate($request->ticketID);
            }
        }
    }

    public function updateAnsweredStatus(Request $request)
    {

        $_assignID = Assignment::ownDetail($this->tixParamUrl)->pluck('assignment_id')[0];
        $_updateIsAnswered = Assignment::find($_assignID);

        if($_updateIsAnswered->is_answered == 1) {
                $request->request->add(['history' => \Common::instance()->getCurrentUserName().
                        ' posted a new reply (ReplyID:'.$request->replyID.')']);
        } else {
            if(!empty($request->is_answered)) {
                $_updateIsAnswered->is_answered = 1;
                $_updateIsAnswered->save();

                $request->request->add(['history' => \Common::instance()->getCurrentUserName().
                            ' posted a new reply (ReplyID:'.$request->replyID.') and tagged herself as Answered']);
            } else {
                $request->request->add(['history' => \Common::instance()->getCurrentUserName().
                    ' posted a new reply (ReplyID:'.$request->replyID.') without tagging herself as Answered.']);
            }
        }

        return $request;
    }

    public function updateAssignment(Request $request)
    {
        $buyer_id = $request->buyerID;
        $assign_id = $request->assignmentID;
        $assign_remarks = $request->assignmentRemarks;

        $request->request->add(['ticketID' => $this->tixParamUrl, 'cycle' => '2']);
        $temp = array();

        if(!empty($buyer_id)) { //Assigning Buyer
            foreach ($buyer_id as $key => $ownerID) {
                $checkAssign = Assignment::where([
                                    ['owner_id', '=', $ownerID],
                                    ['ticket_id', '=', $this->tixParamUrl]
                                ])->get();

                if($checkAssign->isNotEmpty()) {
                    $_updateAssignment = Assignment::find($checkAssign->pluck('assignment_id')[0]);
                    $_updateAssignment->is_answered = 0;
                    $_updateAssignment->is_deleted = 0;
                    $_updateAssignment->save();
                } else {
                    $this->insertAssignment($this->tixParamUrl, explode(',', $ownerID), 0);
                }
            }

            $request->request->add(['history' => \Common::instance()->getCurrentUserName().' assigned '.ESDAccount::getAccountName(serialize($buyer_id)).'.']);
            $this->insertHistory($request);
        }

        if(!empty($assign_id)) {
            foreach ($assign_id as $key => $assignmentID) {
                $_updateAssignment = Assignment::find($assignmentID);
                array_push($temp, $_updateAssignment->owner_id);
                $_updateAssignment->is_answered = 0;
                $_updateAssignment->is_read = 0;
                $_updateAssignment->is_deleted = 1;
                $_updateAssignment->save();
            }

            $request->request->add(['history' => \Common::instance()->getCurrentUserName().' untagged '.ESDAccount::getAccountName(serialize($temp)).'.']);
            $this->insertHistory($request);
        }

        if(!empty($assign_remarks)) {

            $request->request->add(['replyContent' => $assign_remarks]);
            $_tempCont = $this->insertReply($request);
            $_replyID = $_tempCont[0];
            $_replyContent = $_tempCont[1];
            $request->request->add(['history' => \Common::instance()->getCurrentUserName().' posted a remark (ReplyID:'.$_replyID.').']);
            $this->insertHistory($request);

            $_ticketDetail = Ticket::getTicketDetails($this->tixParamUrl)[0];

            $request->request->add(['ticketDetail' => $_ticketDetail]);

            $to = $_ticketDetail->ao_email;

            $cc = CarbonCopy::getCCEmail($this->tixParamUrl).','.Assignment::getAssignedEmail($this->tixParamUrl).','.$this->procEmail;
        
            $_subject = \Common::instance()->getCurrentUserName().' updated the assignment of '.$_ticketDetail->subject;

            $_content = $this->renderEmailContent($_replyContent, $this->url.'/view-request/'.base64_encode($this->tixParamUrl));

            $this->insertEmailNotif($_subject, $_content, $to, $cc);
        }
        
        $this->checkReplyStatus($request);

        \Session::flash('message', 'Ticket Assignment has been updated!');
        \Session::flash('status', 'success');

        $this->saveLogs('Updated Assignment of Ticket#'.$this->tixParamUrl); 

        $this->lastUpdate($this->tixParamUrl);

        return redirect()->back();
    }

    public function resetAssignmentStatus($_ticketID)
    {
        foreach (Assignment::getAssignedDetail($_ticketID)->pluck('assignment_id') as $key => $assignmentID) {
            $_resetStatus = Assignment::find($assignmentID);
            $_resetStatus->is_answered = 0;
            $_resetStatus->is_read = 0;
            $_resetStatus->save();
        }
    }

    public function resetIndiAssignment($_resetListID) 
    {
        if(!empty($_resetListID)) {
            foreach($_resetListID as $key => $assignID) {
                $_resetStatus = Assignment::find($assignID);
                $_resetStatus->is_answered = 0;
                $_resetStatus->is_read = 0;
                $_resetStatus->save();
            }
        }
    }  
}