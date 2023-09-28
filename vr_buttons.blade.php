@if(Session('userData')->AccountGroup == 'BU10' || Session('userData')->AccountGroup == 'BU2')
  @if($_ticketDetail[0]->status_id !== '3')
    <button type="button" class="btn btn-primary btn-sm mb-3" id="btnReply"><i class="fas fa-reply"></i> Reply</button>
  @endif
@else 
  @if($_isvalid && $_ticketDetail[0]->status_id !== '3' || Session('userData')->role_name == 'super_user' || \Common::instance()->is_head() > 0)
    <button type="button" class="btn btn-primary btn-sm mb-3" id="btnReply"><i class="fas fa-reply"></i> Reply</button>
  @endif
@endif



<button type="button" class="btn btn-secondary btn-sm mb-3 btnExpand" title="Expand thread"><i class="fas fa-expand-arrows"></i> </button>



@if(Session('userData')->role_name == 'admin' || Session('userData')->role_name == 'buyer')
  @if(in_array($_ticketDetail[0]->status_id, ['1', '2']))
    <button title="Update Assignment" type="button" id="btnAssign" class="btn btn-olive btn-sm mb-3">
      <i class="fas fa-user-edit"></i> 
    </button>
  @endif
@endif


@if(Session('userData')->AccountGroup == 'BU10' || Session('userData')->AccountGroup == 'BU2')
  @if($_ticketDetail[0]->status_id == '2')
    <button title="Close request" type="button" id="btnClose" title="Close request." class="btn btn-sm btn-warning mb-3">
      <i class="far fa-grin-alt"></i>
    </button>
  @elseif($_ticketDetail[0]->status_id == '3')
    <button title="Reopen request" type="button" id="btnReopen" title="Reopen request." class="btn btn-sm btn-warning mb-3">
      <i class="fas fa-envelope-open"></i> 
    </button>
  @endif
@else
  @if(Session('userData')->account_id == $_ticketDetail[0]->account_owner_id || 
      Session('userData')->account_id == $_ticketDetail[0]->requestor_id)
      @if($_ticketDetail[0]->status_id == '2')
        <button title="Close request" type="button" id="btnClose" title="Close request." class="btn btn-sm btn-warning mb-3">
          <i class="far fa-grin-alt"></i>
        </button>
      @elseif($_ticketDetail[0]->status_id == '3')
        <button title="Reopen request" type="button" id="btnReopen" title="Reopen request." class="btn btn-sm btn-warning mb-3">
          <i class="fas fa-envelope-open"></i> 
        </button>
      @endif
  @endif
@endif


@if(\Common::instance()->is_head() > 0)
  @if($_ticketDetail[0]->transaction_type_id == '2')
    @if($_ticketDetail[0]->status_id == '5' && $_ticketDetail[0]->transaction_type_id == '2')
      <button title="Approve request" type="button" id="btnBUApprove" title="Approve request." class="btn btn-sm btn-success mb-3">
        <i class="fas fa-check"></i> 
      </button>

      <button title="Decline request" type="button" id="btnBUDecline" title="Decline request." class="btn btn-sm btn-danger mb-3">
        <i class="fas fa-times"></i>
      </button>
    @elseif($_ticketDetail[0]->status_id == '7' && Session('userData')->account_id == 415)
      <button title="Approve request" type="button" id="btnFinalApprove" title="Approve request." class="btn btn-sm btn-success mb-3">
        <i class="fas fa-check"></i>
      </button>

      <button title="Decline request" type="button" id="btnFinalDecline" title="Decline request." class="btn btn-sm btn-danger mb-3">
        <i class="fas fa-times"></i>
      </button>
    @elseif($_ticketDetail[0]->status_id == '6' && $_ticketDetail[0]->transaction_type_id == '2')
      <button title="Approve request" type="button" id="btnBUApprove" title="Approve request." class="btn btn-sm btn-success mb-3">
        <i class="fas fa-check"></i> 
      </button>
    @elseif($_ticketDetail[0]->status_id == '8' && $_ticketDetail[0]->transaction_type_id == '2')
      <button title="Approve request" type="button" id="btnFinalApprove" title="Approve request." class="btn btn-sm btn-success mb-3">
        <i class="fas fa-check"></i> 
      </button>
    @endif
  @endif
@endif
