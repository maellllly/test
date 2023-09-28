<div class="row">
  <div class="modal fade" id="mdl_assignment" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        @include('layouts.components.preloader-round')
        <div class="modal-header">
          <h4 class="modal-title">Assignment Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="frmUpdateAssignment" method="post" action="{{ url('update-assignment') }}">
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Choose a buyer to be removed</h3>
                </div>
                <div class="card-body">
                  <table id="tblAssignee" class="table tblAssignment">
                    <thead hidden>
                      <th></th>
                      <th></th>
                    </thead>
                    <tbody>
                      @forelse($_ticketAssignment as $buyer)
                        <tr>
                          <td><center><input type="checkbox" name="assignmentID[]" class="chkboxAssignee" value="{{ $buyer->assignment_id }}"></center></td>
                          <td>
                            <img style="cursor:pointer;" class="circular-portrait" alt="{{ $buyer->AccountName }}" title="{{ $buyer->AccountName }}" 
                                 src="{{ $buyer->GAvatar }}"/>
                            {{ $buyer->AccountName }}
                          </td>
                        </tr>
                      @empty
                      <tr>
                        <td hidden></td>
                        <td colspan="2"><center>No Assignee Yet</center></td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Choose a buyer to be assigned</h3>
                </div>
                <div class="card-body">
                  <table id="tblAddAssignee" class="table tblAssignment">
                    <thead hidden>
                      <th></th>
                      <th></th>
                    </thead>
                    <tbody>
                      @foreach($_buyer as $buyer)
                        <tr>
                          <td><center><input type="checkbox" name="buyerID[]" class="chkboxAddAssignee" value="{{ $buyer->account_id }}"></center></td>
                          <td>
                            <img style="cursor:pointer;" class="circular-portrait" alt="{{ $buyer->AccountName }}" title="{{ $buyer->AccountName }}" 
                                 src="{{ $buyer->GAvatar }}"/>
                            {{ $buyer->AccountName }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" id="assignmentRemarks" name="assignmentRemarks" rows="3" autocomplete="off"></textarea>
              </div>
            </div>
          </div>
        </div>
        @csrf
        </form>
        <div class="modal-footer justify-content-right">
          <button type="button" id="btnUpdateAssignment" class="btn btn-primary align-right">Update Assignment</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
  </div>
</div>
