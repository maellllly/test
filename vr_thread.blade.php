@php $_attachment = $_attachment->groupBy('reply_id'); @endphp

@foreach($_ticketReply as $key => $ticketReply)
  @if($key == array_key_first($_ticketReply->toArray()))
    @php $_show = ['collapsed', 'show']; @endphp
    @else 
    @php $_show = ['', '']; @endphp
  @endif

  <div id="accordionRep_{{ $ticketReply->reply_id }}">
   <div class="card card-no-shadow">
      <div class="card-header card-thread collapsed {{ $_show[0] }}" data-toggle="collapse" data-parent="#accordionRep_{{ $ticketReply->reply_id }}" href="#collapseRep_{{ $ticketReply->reply_id }}" style="cursor:pointer">
        <h4 class="card-title">
          <div class="user-block">
            <img class="img-circle" src="{{ $ticketReply->GAvatar }}">
            <span class="username">
              {{ $ticketReply->AccountName }}
            </span>
          </div>
        </h4>
        <small class="description" style="float:right">
          {{ Carbon\Carbon::parse($ticketReply->date_replied)->format('l, M d, h:i A') }}
          ({{ Carbon\Carbon::parse($ticketReply->date_replied)->diffForHumans() }})
        </small>
      </div>
      <div id="collapseRep_{{ $ticketReply->reply_id }}" class="panel-collapse collapse in {{ $_show[1] }}">
        <div class="card-body" style="overflow-x:auto;max-height:33em;">
          {!! $ticketReply->reply !!}
        </div>
        @if($_attachment->has($ticketReply->reply_id))
          <div class="card-footer" style="border: dashed 1px">
            @foreach($_attachment[$ticketReply->reply_id] as $_file)
              @if($ticketReply->reply_id == $_file->reply_id)
                <a href="{{ route('viewFile', ['file_name' => base64_encode($_file->name)]) }}" class="btn-link text-secondary"><i class="{{ \Common::instance()->fileIcon($_file->file_type) }}"></i> {{ \Str::after($_file->name, '_') }}</a><br>
              @endif
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
@endforeach