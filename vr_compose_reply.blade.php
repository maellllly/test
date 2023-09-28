<div class="card card-outline" id="dvReply" hidden>
  @include('layouts.components.preloader-cat', ['content' => ''])
  <div class="card-header">
    <h3 class="card-title">Compose New Message</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body" style="margin-bottom:-40px">
    <form id="formReply" action="{{ url('post-reply') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data" role="form">
      <div class="form-group">
        <select class="form-control select2" name="ccID[]" id="ccID" style="width: 100%;" multiple>
          @foreach($_cc as $carbonCopy)
            <option value="{{ $carbonCopy->account_id }}">{{ $carbonCopy->AccountName }}</option>
          @endforeach
        </select>
      </div>
      @if(\App\Assignment::validBuyer($_ticketDetail[0]->ticket_id)->count() > 0)
        @if(App\Assignment::ownDetail($_ticketDetail[0]->ticket_id)->pluck('is_answered')[0] == 0)
        <div class="form-group" style="margin-left:4px"> 
          <input type="checkbox" name="is_answered" checked> Mark self as answered.
        </div>
        @endif
      @endif
      <div class="form-group">
        <textarea name="replyContent" id="replyContent" rows="10" cols="80"></textarea>
      </div>
      <input type="hidden" id="unique1" name="unique" value="{{ Str::random(10).\Carbon\Carbon::now()->format('mdyHis') }}">
      <input type="hidden" id="replyType" name="replyType" value="1">
      <input type="hidden" id="buyerResetList" name="buyerResetList">
      @csrf
    </form>
    <div class="form-group" style="margin-top:-10px">
      <form method="post" action="{{ route('dropzone')}}" enctype="multipart/form-data" class="dropzone" id="dropzone">
        <div class="dz-message" data-dz-message><span>Drop or Select files here to upload</span></div>
        <input type="hidden" id="unique" name="unique" value="{{ Str::random(10).\Carbon\Carbon::now()->format('mdyHis') }}">
        @csrf
      </form>  
    </div>
  </div>
  <div class="card-footer">
    <div class="float-right">
      <button type="button" class="btn btn-default" id="btnCancel"><i class="fas fa-times"></i> Discard</button>
    </div>

    <div class="btn-group">
      <button type="button" class="btn btn-primary btnPostReply" id="btnPostReply" title="Just a normal reply."> Send</button>
      <button type="button" class="btn btn-primary btnPostReply dropdown-toggle dropdown-icon" style="border-left: 1px solid #185abc;" title="More send options" data-toggle="dropdown" aria-expanded="false">
        <span class="sr-only">Toggle Dropdown</span>
        <div class="dropdown-menu" role="menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-1px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
          <a class="dropdown-item" id="btnSendClose" title="Send reply and close this ticket.">Send and Close</a>
          <a class="dropdown-item" id="btnSendReset" title="Send reply and reset buyer's answered status to pending.">Send and Reset</a>
        </div>
      </button>
    </div>
    

  </div>
</div>