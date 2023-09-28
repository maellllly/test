<div class="row">
  <div class="modal fade" id="mdl_reset_buyer" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
       @include('layouts.components.preloader-round')
        <div class="modal-header">
          <h4 class="modal-title">Choose to reset the buyer's status</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tblResetbuyer" class="table">
            <tbody>
            @if($_ticketAssignment->where('is_answered', '1')->count() > 0)
              @foreach($_ticketAssignment->where('is_answered', '1') as $buyer)
                <tr style="background-color:#d6d6d6">
                  <td><center><input type="checkbox" class="checkboxReset chkReset " value="{{ $buyer->assignment_id.'|'.$buyer->AccountName }}"></center></td>
                  <td>
                    <img style="cursor:pointer;" class="circular-portrait img-bordered-answered-sm" alt="{{ $buyer->AccountName }}" title="{{ $buyer->AccountName }}" 
                         src="{{ $buyer->GAvatar }}"/>
                    {{ $buyer->AccountName }}
                  </td>
                </tr>
                @endforeach
              @else 
                <tr>
                  <td>No buyer has an answered status yet.</td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
        <div class="modal-footer justify-content-right">
          <button type="button" id="btnFinalSendReset" class="btn btn-primary align-right" title="Send reply and reset engineer's answered status to pending.">Send and Reset</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
  </div>
</div>
