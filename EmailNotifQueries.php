<?php

namespace App\Traits;

use DB;
use App;
use File;
use Config;
use Session;
use DateTime;
use Response;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Ticket;
use App\ESDAccount;
use App\EmailNotification;

trait EmailNotifQueries
{
    
    function generateEmailNotif($request)
    {

        $email_content = $this->generateEmailContent($request);
        $email_link = $this->url.'/view-request/'.base64_encode($request->ticketID);

        return $this->renderEmailContent($email_content, $email_link);
    }

    function generateEmailContent($request)
    {
        // 1 create | 2 reply (buyer to requestor) | 3 reply (requestor to buyer) | 4 close request |
        // 5 reopened request | 6 For Approval NF | 7 Approved NF | 8 Declined NF
        $_content = "Hi ESD-Procurement Team,<br><br>";

        $_cycle = $request->cycle;

        $_currentUser = \Common::instance()->getCurrentUserName();


        if($_cycle == 1 ) {
            $_content = "Hi ESD-Procurement Team,<br><br>";
            $_content .=  $_currentUser. ' created a new '.\Common::instance()->getTransType($request->tTypeID).' request.<br><br>';
        } else if($_cycle == 2) {
            $_content = "Hi Approver,<br><br>";
            $_content .= $_currentUser.' created a new'.\Common::instance()->getTransType($request->tTypeID).' request that needs your approval.<br><br>';
        } else if($_cycle == 3) {
            $_content = "Hi Adel,<br><br>";
            $_content .= $_currentUser.' endorsed this new NF Request.<br><br>';
        } else if($_cycle == 4) {
            $_content = "Hi ".Ticket::getTicketDetails($this->tixParamUrl)[0]->requestor_name.",<br><br>";
            $_content .= $_currentUser.' declined your request.<br><br>';
        } else if($_cycle == 5) {
            $_content = "Hi ESD-Procurement Team,<br><br>"; // Will Customize the TO:
            $_content .= $_currentUser. ' closed this request.<br><br>';
        } else if($_cycle == 6) {
            $_content = "Hi ESD-Procurement Team,<br><br>"; // Will Customize the TO:
            $_content .= $_currentUser. ' reopened this request .<br><br>';
        } 
        
        $_content .= "Please check and confirm.<br><br>";

        return $_content;
        
    }

    function insertEmailNotif($_subject, $_content, $_to, $_cc)
    {
        // creator | subject | message | sendTo | sendCC | sendBCC | dateCreated | status | dateSEnt 
        $_insertEmail = new EmailNotification();
        $_insertEmail->creator = Session('userData')->AccountName;
        $_insertEmail->subject = $_subject;
        $_insertEmail->message = $_content;
        $_insertEmail->sendTo = $_to;
        $_insertEmail->sendCC = $_cc;
        $_insertEmail->sendBCC = $this->bcc;
        $_insertEmail->dateCreated = Carbon::now()->format('m/d/Y H:i:s');
        $_insertEmail->status = 0;
        $_insertEmail->sys_type = 'ProPort';
        $_insertEmail->save();

        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('POST', 'https://proport.ics.com.ph/api/postMail', [
        //     'form_params' => [
        //         'creator' => base64_encode(Session('userData')->AccountName),
        //         'subject' => base64_encode($_subject),
        //         'message' => base64_encode($_content),
        //         'sendTo' => base64_encode($_to),
        //         'sendCC' => base64_encode($_cc),
        //         'sendBCC' => base64_encode($this->bcc),
        //         'sysType' => base64_encode('ProPortCloud')
        //     ]
        // ]);
    }

    function postMail(Request $request)
    {
        $_insertEmail = new EmailNotification();
        $_insertEmail->creator = base64_decode($request->creator);
        $_insertEmail->subject = base64_decode($request->subject);
        $_insertEmail->message = base64_decode($request->message);
        $_insertEmail->sendTo = base64_decode($request->sendTo);
        $_insertEmail->sendCC = base64_decode($request->sendCC);
        $_insertEmail->sendBCC = base64_decode($request->sendBCC);
        $_insertEmail->dateCreated = Carbon::now()->format('m/d/Y H:i:s');
        $_insertEmail->status = 0;
        $_insertEmail->sys_type = base64_decode($request->sysType);
        $_insertEmail->save();
    }

    function renderEmailContent($email_content, $email_link)
    {
        return view('mail.email_template',compact('email_content', 'email_link'))->render();
    }
}