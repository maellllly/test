<?php

namespace App\Traits;

use DB;
use App;
use URL;
use Config;
use Session;
use DateTime;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;

use App\Reply;
use App\Ticket;
use App\ESDAccount;
use App\CarbonCopy;
use App\Assignment;

trait ReplyQueries
{

    public function postReply(Request $request)
    {
        $_tempCont = $this->insertReply($request);

        $_replyID = $_tempCont[0];
        $_replyContent = $_tempCont[1];

        $_replyType = $request->replyType;

        $request->request->add(['ticketID' => $this->tixParamUrl, 'replyID' => $_replyID]);

        $this->insertAttachment($request);
        
        $_ticketDetail = Ticket::getTicketDetails($this->tixParamUrl)[0];
        
        $request->request->add(['ticketDetail' => $_ticketDetail]);

        $cc = Session('userData')->Email;

        if($_replyType == 1) { // Normal Reply
            $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.').';

            $_subject = \Common::instance()->getCurrentUserName().' replied('.$_replyID.'):'.$_ticketDetail->subject;
        } else if($_replyType == 2) { // Reply with Close
            $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.') ';
            $_history .= 'and marked this ticket as Closed.';

            $request->request->add(['statusID' => '3']);
            $this->updateTicketStatus($request);

            $_subject = \Common::instance()->getCurrentUserName().' replied('.$_replyID.') & closed '.$_ticketDetail->subject;
        } else if ($_replyType == 3) { // Reply with reset Engineer Status
            $_tempList = explode(',', $request->buyerResetList);

            $_resetListID = array_map(function($val) {
                return explode('|', $val)[0];
            }, $_tempList);

            $this->resetIndiAssignment($_resetListID);

            $_resetListName = array_map(function($val) {
                return explode('|', $val)[1];
            }, $_tempList);

            $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.') ';
            $_history .= ' and reset '.implode($_resetListName, ', ').' status from Answered to Pending.';

            $_subject = \Common::instance()->getCurrentUserName().' replied('.$_replyID.') & reset status:'.$_ticketDetail->subject;
        }

        if(Session('userData')->role_name == 'buyer' && $_replyType != 3 || Session('userData')->role_name == 'admin' && $_replyType != 3) {
            $request = $this->updateAnsweredStatus($request);
            $this->insertHistory($request);
            $this->checkReplyStatus($request);
        } else {
            $request->request->add(['history' => $_history]);
            $this->insertHistory($request);
            
            if($_replyType == 3) {
                $this->checkReplyStatus($request);
            }
        }

        if(Session('userData')->role_name == 'requestor' || Session('userData')->role_name == 'super_user') {
            $_content = 'Hi ESD-Procurement Team, <br><br>'.$_history;

        } else {
            if(!empty(Ticket::getTicketDetails($this->tixParamUrl)[0]->requestor_nickname)) {
                $_content = 'Hi '.Ticket::getTicketDetails($this->tixParamUrl)[0]->requestor_nickname. ', <br><br>'.$_history;
            } else {
                $_content = 'Hi '.Ticket::getTicketDetails($this->tixParamUrl)[0]->requestor_name. ', <br><br>'.$_history;
            }
        }

        $_ccID = $request->ccID;

        if(!empty($request->ccID)) {
            $this->insertCarbonCopy($this->tixParamUrl,$_ccID);
            
            $_history = \Common::instance()->getCurrentUserName().' looped in ('.ESDAccount::getAccountName(serialize($_ccID)).').';
            $request->request->add(['history' => $_history]);
            $this->insertHistory($request);

            $_content .= '<br><br>'.$_history;
        }

        $_content .='<br><br>Please check and confirm.<br><br>';

        $to = Assignment::getAssignedEmail($this->tixParamUrl).','.CarbonCopy::getCCEmail($this->tixParamUrl).','.$_ticketDetail->requestor_email.','.$_ticketDetail->ao_email.','.Session('userData')->Email;

        $to = implode(',', array_diff(array_unique(array_filter(explode(',', $to))), [Session('userData')->Email]));

        // $_content = $this->renderEmailContent($_content, $this->url.'/view-request/'.base64_encode($this->tixParamUrl));
        $_content = $this->renderEmailContent($_replyContent, $this->url.'/view-request/'.base64_encode($this->tixParamUrl));

        $this->insertEmailNotif($_subject, $_content, $to, $cc);

        $this->saveLogs('Posted a new reply in Ticket#'.$this->tixParamUrl.'.');

        $this->lastUpdate($this->tixParamUrl);
        
        Session::flash('message', 'Reply has been posted.');
        Session::flash('status', 'success');

        return redirect()->back();
    }

    public function insertReply($request)
    {
        $_replyContent = $this->cleanSNote($request->replyContent);

        $_insertReply = new Reply();
        $_insertReply->ticket_id = base64_decode(basename(URL::previous()));
        $_insertReply->user_id = Session('userData')->account_id;
        $_insertReply->reply = $_replyContent;
        $_insertReply->date_replied = \Carbon\Carbon::now()->format('m/d/Y H:i:s'); 
        $_insertReply->is_deleted = 0;
        $_insertReply->save();

        return ['0' => $_insertReply->reply_id, '1' => $_replyContent];
    }

}