<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <meta name="description" content="Developed by Lance Aaron A. Aranda">

  <meta name="author" content="appsdev">
  <link rel="icon" href="{{ asset('/public/img/assets/proport-icon_trns.ico') }}">
  <title>
    @yield('title', config('appsdev_conf.title', 'Appsdev Team'))
    @yield('title_postfix', config('appsdev_conf.title_postfix', ''))
  </title>

  <!--
    ||           //\\      ||\\    ||    =====   ||======= 
    ||          //  \\     || \\   ||  //     \\ ||          
    ||         //____\\    ||  \\  ||  ||        ||=======  
    ||        //      \\   ||   \\ ||  ||        ||           
    \\=====  //        \\  ||    \\||  \\=====// ||=======  
 
    ||           //\\      ||\\    ||    =====   
    ||          //  \\     || \\   ||  //     \\        
    ||         //____\\    ||  \\  ||  ||          
    ||        //      \\   ||   \\ ||  ||  =====        
    \\=====  //        \\  ||    \\||  \\=====//    

    ||\\        //||      //\\      ||          //\\     ||  //     //\\     //=======
    || \\      // ||     //  \\     ||         //  \\    || //     //  \\    ||
    ||  \\    //  ||    //____\\    ||        //____\\   ||//     //____\\   ||_______
    ||   \\  //   ||   //      \\   ||       //      \\  ||\\    //      \\          ||
    ||    \\//    ||  //        \\  \\===== //        \\ || \\  //        \\  =======//
 -->

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('public/adminlte/plugins/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/public/css/fontawesome/css/all.min.css') }}">

   <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css">

  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/basic.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('/public/adminlte/dist/css/adminlte.min.css') }}">

  
  <link rel="stylesheet" href="{{ asset('/public/adminlte/plugins/select2/select2.css') }}">
  <link rel="stylesheet" href="{{ asset('/public/adminlte/plugins/select2/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/public/adminlte/plugins/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/public/adminlte/plugins/sweetalert2/sweetalert.min.css') }}">

  <link rel="stylesheet" href="{{ asset('/public/css/appsdev.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  @yield('adminlte_css')  
</head>

<body class="hold-transition sidebar-mini @yield('body_class')">

  @yield('body')

<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->


{{-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> --}}
<script src="{{ asset('public/adminlte/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap -->
<script src="{{ asset('public/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js"></script>

<!-- SlimScroll -->
<script src="{{ asset('public/adminlte/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
@yield('adminlte_js')

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script src="{{ asset('public/adminlte/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('public/adminlte/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('public/adminlte/plugins/sweetalert2/sweetalert.min.js') }}"></script>

<script src="{{ asset('public/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('public/js/timeago.js') }}"></script>
{{-- <script src="https://adminlte.io/themes/dev/AdminLTE/dist/js/adminlte.min.js"></script> --}}

<!-- OPTIONAL SCRIPTS -->
{{-- <script src="{{ asset('public/adminlte/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('public/adminlte/dist/js/demo.js') }}"></script>
<script src="{{ asset('public/adminlte/dist/js/pages/dashboard3.js') }}"></script> --}}
<script type="text/javascript">

  $(function() {


    if('<?php echo Session::has('message') ?>') {

      message = '<?php echo Session::get('message') ?>';
      type = '<?php echo Session::get('status') ?>';

      Swal.fire({
        type: type,
        title: message,
        width: '370px',
        showConfirmButton: false,
        timer: 2000
      })
    }

    $('#btnProport').on('click', function() {
      Swal.fire({
        title: 'Procurement Portal',
        width: '360px',
        showConfirmButton: false,
        timer: 2000
      })
    })

    $('#gauth').on('click', function() {
      // $('.preloader-round').removeAttr('hidden', '');
      $('#preloader-milktea').removeAttr('hidden', '');
      window.location.href = '{{ route('googleRedirect') }}';
    })
    
    /** add active class and stay opened when selected */
    var url = window.location;

    // for sidebar menu entirely but not cover treeview
    $('ul.nav a').filter(function() {
       return this.href == url;
    }).addClass('active');

    $('ul.nav-treeview a').filter(function() {
      return this.href == url;
    }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');
  })

  function formatDate(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;

    var monthname = ['Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
            // var d = new Date(value.DateCreated);
    // var formatted = monthname[d.getMonth()]+" "+d.getDate()+", "+d.getFullYear();

    return monthname[date.getMonth()]+ " " + date.getDate() + ", " + date.getFullYear() + "  " + strTime;
  }

</script>

<script src="https://js.pusher.com/5.0/pusher.min.js"></script>
<script>

  // Enable pusher logging - don't include this in production
  // Pusher.logToConsole = true;
  var auth = {{ auth()->check() ? 'true' : 'false' }};
  if (auth) {
    // refreshNotif();
    setTimeout(function() {
      refreshSideCount();
    }, 500)

    var pusher = new Pusher('ceac740019ebaaa21620', {
      cluster: 'ap1',
      forceTLS: true
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('form-submitted', function(data) {
      
      counter = JSON.stringify(data['new_notif']);
      if(counter > 0) {
        // refreshNotif();
        refreshSideCount();
        $('#tickets').DataTable().ajax.reload();
      } else {
        alert('Null');
      }

    });
  }

  function refreshNotif() {
    notif = "<span class='dropdown-item dropdown-header markReadNav'>Mark all as read</span>";
    $('#dvNotif').html('');
    $('.preloader-ring').removeAttr('hidden', '');

    $.get('{{ url('countUnread') }}').done(function(data) {
       $('#spnBadgeCounter').text(data['count']);
        if(data['count'] > 0) {
        $('#spnHeaderCounter').text(data['count']+' Notifications');
      } else {
        $('#spnHeaderCounter').text(data['count']+' Notification');
      }
    }).fail(function(data) {
      refreshNotif();
    })

    $.get('{{ url('unreadNotification') }}').done(function(data) {
      $.each(data.data, function(key, val) {
          notif += "<div class='dropdown-divider'></div>";
          notif += "<a id="+val['ticket_id']+" class='dropdown-item list-notif pointer'>";
          notif += "<div class='media'>";
          notif += "<img src='"+val['CGAvatar']+"' alt='User Avatar' class='img-size-50 img-circle mr-3'>";
          notif += "<div class='media-body'><h3 class='dropdown-item-title'>"+val['CName'];
          notif += "<span class='float-right text-sm text-muted'><i class='far fa-circle'></i></span></h3>";
          notif += "<p class='text-sm'>"+val['notification']+"</p>";
          notif += "<p class='text-sm text-muted'><i class='far fa-clock mr-1'></i>"+$.timeago(val['created_date'])+"</p>";
          notif += "</div></div></a>";
          notif += "<div class='dropdown-divider'></div>";
          $('#dvNotif').html(notif);
      })
      $('.preloader-ring').attr('hidden', 'hidden');
    }).fail(function(data) {
      refreshNotif();
    })

  }

  function refreshSideCount() {
    $.get('{{ url('getSideCount') }}').done(function(data) {
        $('#fpendingctr').text(data.fpendingctr.count);
        $('#fansweredctr').text(data.fansweredctr.count);
        $('#fclosedctr').text(data.fclosedctr.count);
        if (typeof(data.freassignedctr) !== 'undefined') {
          $('#freassignedctr').text(data.freassignedctr.count);
        }


        $('#nfbuheadapprovalctr').text(data.nfbuheadapprovalctr.count);
        $('#nfbuheaddeclinedctr').text(data.nfbuheaddeclinedctr.count);

        $('#nffinalapprovalctr').text(data.nffinalapprovalctr.count);
        $('#nffinaldeclinedctr').text(data.nffinaldeclinedctr.count);

        $('#nfpendingctr').text(data.nfpendingctr.count);
        $('#nfansweredctr').text(data.nfansweredctr.count);
        $('#nfclosedctr').text(data.nfclosedctr.count);

        if(typeof(data.nfreassignedctr) !== 'undefined') {
          $('#nfreassignedctr').text(data.nfreassignedctr.count);
        }
    }).fail(function(data) {
      refreshSideCount();
    })
  }

  $(document).on('click', '.list-notif', function(){
    var ticketID = btoa($(this).attr('id'));

    window.open('{{ url('view-request') }}/'+ticketID, '_self');
  });

</script>

</body>
</html>
