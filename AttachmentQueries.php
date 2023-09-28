<?php

namespace App\Traits;

use DB;
use App;
use File;
use Config;
use Storage;
use Session;
use DateTime;
use Response;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


use App\Attachment;
use App\AttachmentStream;

trait AttachmentQueries
{
    
    public function insertAttachment($request)
    {

       $stream_id = "";
       $files = Storage::files('\public\TempFolders\\'.$request->unique);

       $fileStreamPath = '\\\192.168.0.150\APPSDEVSERVER\ESDAttachments\ESDAttachments';
       $i = 0;

        if (!empty($files)) {
            foreach($files as $key => $file)
            {
                if(@File::copy(storage_path('app\\').$file, $fileStreamPath.'\\'.basename($file)) === true) {
                    File::copy(storage_path('app\\').$file, storage_path('app\ESDAttachments').'\\'.basename($file)); 
                    File::copy(storage_path('app\\').$file, $fileStreamPath.'\\'.basename($file)); 

                    $request->request->add(['streamID' => $this->getFileStreamID(basename($file))]);

                    $fileID = $this->insertAttachDetails($request);
                }
            }
            
            Storage::deleteDirectory('/public/TempFolders/'.$request->unique);
        }
    }

    public function insertAttachDetails($request)
    {

        $insertAttachDetails = new Attachment();

        $insertAttachDetails->stream_id = $request->streamID;
        $insertAttachDetails->ticket_id = $request->ticketID;
        if(!empty($request->replyID)) {
           $insertAttachDetails->reply_id = $request->replyID;
        } 
        $insertAttachDetails->save();

        return $insertAttachDetails->fileID;
    }

    public function getFileStreamID($filename)
    {

        return AttachmentStream::WHERE('name', '=', $filename)->pluck('stream_id')[0];

    }

    public function cleanSNote($content)
    {
        if(!empty($content)) {
            $dom = new \DomDocument();
            libxml_use_internal_errors(true);

            $dom->loadHtml('<?xml encoding="UTF-8">'.trim($content));    
            $images = $dom->getElementsByTagName('img');

            $domain_path = 'http://proport.ics.com.ph/';

            foreach($images as $k => $img) {
                $data = $img->getAttribute('src');
                
                if(Str::contains($data, 'base64')) {
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                } else {
                    $data = str_replace('amp;', '', $data);

                    if(@file_get_contents($data) === true) {
                        $data = 'data:image/jpg;base64,'.base64_encode(file_get_contents($data));
                        $data = file_get_contents($data);
                    } 
                }

                $data = base64_decode($data);
                $path_fname = 'public/img/summernote_img/'.Str::random(5).Carbon::now()->format('mys').$k.'.png';
                file_put_contents($path_fname, $data);
                $img->removeAttribute('src');
                $img->setAttribute('src', $domain_path.$path_fname);
            }

            $sHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            $sHtml .= $dom->saveHTML( $dom->documentElement ); // important!

            return $sHtml;
        }
    }

    public function viewFile($file_name)
    {     
        $filePath = storage_path('app\ESDAttachments\\');

        ob_end_clean();

        return Response::download($filePath.base64_decode($file_name));
    }

    public function appsdevDownload()
    {     
        $filePath = storage_path('app\\');

        ob_end_clean();
        $file_name = base64_encode('ProPort.7z');
        
        return Response::download($filePath.base64_decode($file_name));
    }


    public function dropzone(Request $request) 
    {

        $file = $request->file('file');

        $fileName = $file->getClientOriginalName();
        $file->move(storage_path('app/public/TempFolders/'.$request->unique),$fileName);
    }

    public function attachmentDelete(Request $request)
    {
        Storage::disk('public')->delete('TempFolders/'.$request->unique.'/'.$request->filename);
    }
}