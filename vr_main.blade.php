@extends('layouts.page')

@section('title', 'View Request')

@section('content_header', 'Request Detail')

@section('css')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/basic.css">
@stop

@section('content')


@include('modals.mdl_reset_buyer')
@include('modals.mdl_assignment')
@include('modals.mdl_history')

<!-- Start of New Template --> 
<section class="content">
  <div class="container-fluid">
    <div class="row">
      @include('requestor.view_request.vr_header')
    </div>

    <div class="row" id="dvMainRow">

      <div class="col-md-3" id="dvInformation">
        @include('requestor.view_request.vr_information')
      </div>

      <div class="col-md-9" id="dvThread">
        @include('requestor.view_request.vr_buttons') 
        @include('requestor.view_request.vr_compose_reply')

        <div style="background-color: white">
          @include('requestor.view_request.vr_thread')
          @include('requestor.view_request.vr_request')
        </div>
      </div>

    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</section>
<!-- End of New Template -->

<form id="frmTicketStatus" method="post" action="{{ url('tag-update') }}">
  @csrf
  <input type="hidden" id="updateType" name="updateType">
</form>

@stop

@section('js')

    <script type="text/javascript">

      $('div#dvThread a').attr('target', '_blank');

      $('.content-header').attr('hidden', 'hidden');
      $(document.body).removeClass('sidebar-collapse');
      $(document.body).addClass('sidebar-collapse');

      $(function() {

          $('#ccID').select2({
            placeholder: 'CC:',
            theme: 'bootstrap4'
          });

          $('#unique1').val($('#unique').val());
          $('#formReply').removeAttr('hidden', '');

          $(document).on('click', '.btnExpand', function() {
            $(this).removeClass('btnExpand');
            $(this).addClass('btnCompress');
            $(this).attr('title', 'Compress thread');
            
            $(this).find('i').removeClass();
            $(this).find('i').addClass('fas fa-compress-arrows-alt');
            collapseInformation();
          })

          $(document).on('click', '.btnCompress', function() {
            $(this).removeClass('btnCompress');
            $(this).addClass('btnExpand');
            $(this).attr('title', 'Expand thread');
            
            $(this).find('i').removeClass();
            $(this).find('i').addClass('fas fa-expand-arrows');
            showInformation();
          })

          $('#btnReply').on('click', function() {
            summernote('#replyContent');
            $('#dvReply').removeAttr('hidden');
            $('#btnReply').hide();
          })

          $('#btnCancel').on('click', function() {
            $('#attachment').val('');
            $('#listFile').empty();
            $('#replyContent').summernote('destroy');
            $('#replyContent').val('');
            $('#dvReply').attr('hidden', 'hidden');
            $('#btnReply').show();
          })

          $('#btnBUApprove').on('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This ticket will now be tagged as Pending. Buyers will now be reply to this request.",
                type: 'question',
                width: '350px',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
              }).then((result) => {
              if (result.value) {
                var adelGroup = ['BU1', 'BU2', 'BU5', 'BU10', 'CE01'];
                if($.inArray('<?php echo Session('userData')->AccountGroup ?>', adelGroup) >= 0 && '{{ Session('userData')->account_id <> '415'}}') {
                  $('#updateType').val('10');
                } else {
                  $('#updateType').val('20');
                }
                $('#frmTicketStatus').submit();
              }
            })
          })

          $('#btnFinalApprove').on('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This ticket will now be tagged as Pending. Buyers will now be reply to this request.",
                type: 'question',
                width: '350px',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
              }).then((result) => {
              if (result.value) {
                $('#updateType').val('30');
                $('#frmTicketStatus').submit();
              }
            })
          })

          $('#btnBUDecline').on('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Buyers won't still be able to reply to this request unless approved.",
                type: 'warning',
                width: '350px',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, decline it!'
              }).then((result) => {
              if (result.value) {
                $('#updateType').val('40');
                $('#frmTicketStatus').submit();
              }
            })
          })

          $('#btnFinalDecline').on('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Buyers won't still be able to reply to this request unless approved.",
                type: 'warning',
                width: '350px',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, decline it!'
              }).then((result) => {
              if (result.value) {
                $('#updateType').val('50');
                $('#frmTicketStatus').submit();
              }
            })
          })

          $('#btnClose').on('click', function() {
              Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to reply in this ticket!",
                type: 'warning',
                width: '350px',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, close it!'
              }).then((result) => {
              if (result.value) {
                $('#updateType').val('60');
                $('#frmTicketStatus').submit();
              }
            })
          })

          $('#btnReopen').on('click', function() {
              Swal.fire({
                title: 'Are you sure?',
                text: "This ticket will be tagged as Pending!",
                type: 'warning',
                width: '350px',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reopen it!'
              }).then((result) => {
              if (result.value) {
                $('#updateType').val('70');
                $('#frmTicketStatus').submit();
              }
            })
          })

          $('#btnPostReply').on('click', function() {
            $('#replyType').val('1');
            $('#preloader-cat').removeAttr('hidden', '');
            $('#formReply').submit();
          })

          $('#btnSendClose').on('click', function() {
            if ('{{ Session('userData')->role_name }}' != 'requestor' || 
                '{{ Session('userData')->account_id }}' != '{{ $_ticketDetail[0]->ao_id }}' ||
                '{{ Session('userData')->account_id }}' != '{{ $_ticketDetail[0]->requestor_id }}') {
              Swal.fire({
                type: 'error',
                title: 'Sorry, you are not allowed to use this function!',
                width: '350px',
                showConfirmButton: false,
                timer: 2000
              })
            } else if('{{ $_ticketDetail[0]->status_id }}' == 2) {
              $('#replyType').val('2');
              $('#preloader-cat').removeAttr('hidden', '');
              $('#formReply').submit();
            } else {
              Swal.fire({
                type: 'error',
                title: "Sorry, only tickets with status Answered can be tagged as Closed!",
                width: '400px',
              })
            }
          })

          $('#btnSendReset').on('click', function() {
            $('#mdl_reset_buyer').modal('show');
          })

      })

      function summernote(idClass) {
        $(idClass).summernote({
          height: '250px',
        }).on("summernote.enter", function(we, e) {
          $(this).summernote("pasteHTML", "<br><br>");
          e.preventDefault();
        });
      } 

      function collapseInformation() {
        $('.panel-collapse').addClass('show');

        $("div#dvInformation").find('.card').each(function( ) {
          $(this).removeClass();
          $(this).addClass('card collapsed-card');
        });

        $("div#dvInformation").find('.card-body').each(function( ) {
          $(this).css('display', 'none');
        });

        $('div#dvInformation').find('i').removeClass();
        $('div#dvInformation').find('i').addClass('fas fa-plus');

        $('#dvMainRow').removeClass('row');

        $('#dvThread').addClass('col-md-12');

        $('#dvInformation').removeClass('col-md-3');
        $('#dvInformation').addClass('row');

        $('#dvTransHistory').addClass('col-sm-6');
        $('#dvDetails').addClass('col-sm-3');

        $('#dvAttachment').addClass('col-sm-3');
      }

      function showInformation() {
        $("div#dvInformation").find('.card').each(function( ) {
          $(this).removeClass();
          $(this).addClass('card');
        });

        $("div#dvInformation").find('.card-body').each(function( ) {
          $(this).css('display', 'block');
        });

        $('div#dvInformation').find('i').removeClass();
        $('div#dvInformation').find('i').addClass('fas fa-minus');

        $('.panel-collapse').removeClass('show');

        $('#dvMainRow').removeClass('row');
        $('#dvMainRow').addClass('row');

        $('#dvThread').removeClass('col-md-12');
        $('#dvThread').addClass('col-md-9');

        $('#dvInformation').removeClass('row');
        $('#dvInformation').addClass('col-md-3');

        $('#dvTransHistory').removeClass('col-sm-6');
        $('#dvDetails').removeClass('col-sm-3');

        $('#dvAttachment').removeClass('col-sm-3');
      }
    </script>

    <script src="{{ asset('public/js/view_request/vr_assignment.js') }}"></script>
    <script type="text/javascript">
      Dropzone.options.dropzone =
         {
            maxFilesize: 20,
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.txt,.xlsx,.xls,.csv,.word,.doc,.docs,.docx,.pdf,.xml,.eml,.sdd,.zip,.7z,.rar,.exe",
            addRemoveLinks: true,
            timeout: 50000,
            renameFile: function(file) {
                var dt = new Date();
                var time = dt.getTime();
                return time+'_'+file.name;
            },
            removedfile: function(file) 
            {
                var name = file.upload.filename;
                var unique = $('#unique').val();
                $.ajax({
                    type: 'POST',
                    url: '{{ url('attachment-delete') }}',
                    data: {filename: name, unique: unique, "_token": "{{ csrf_token() }}"},
                    success: function (data){
                        console.log("File has been successfully removed!!");
                    },
                    error: function(e) {
                        console.log(e);
                    }
              });
                    var fileRef;
                    return (fileRef = file.previewElement) != null ? 
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
            success: function(file, response) 
            {
              // obj = JSON.parse(response);
              // console.log();
              // file.previewElement.id = obj.filename;
            },
            error: function(file, message) {
              console.log(message);
                $(file.previewElement).addClass("dz-error").find('.dz-error-message').text(message);
            }
      };


    </script>

    <script type="text/javascript"> //Codes for Transaction History
      $('#btnViewTransaction').on('click', function() {
        $('#tbodyHistory').html('');

        $('.preloader-round').removeAttr('hidden', '');
        $.get('{{ url('getHistory') }}?tid='+btoa('{{ $_ticketDetail[0]->ticket_id }}'), function(data) {

        }).done(function(data) {
          // console.log(data);
          listbody = '';
          $.each(data.data, function(key, val) {
            listbody += "<tr>" +
              "<td style='width:39em'>"+val.history+'</td>' +  
              '<td>'+moment(val.history_created).format("MM/DD/YYYY hh:mm a");+'</td>' +  
              "</tr>";
          })

          $('#tbodyHistory').html(listbody);

          $('.preloader-round').attr('hidden', 'hidden');
        }).fail(function() {

        })

        $('#mdl_history').modal('show');
      })
    </script>
  
@stop

