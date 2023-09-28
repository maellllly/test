@extends('layouts.page')

@section('title', '420')

@section('content_header', '420 Error Page')

@section('content')

  <div class="error-page">
    <h2 class="headline text-warning"> 420</h2>

    <div class="error-content">
      <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! There's something wrong.</h3>

      <p>
        We could not find your BU Head Approver.
        Please contact IT-Appsdev: Lance (Local: 1820) to correctly assign your account.
        Meanwhile, you may <a href="http://proport.ics.com.ph">return to dashboard</a>.
      </p>

    </div>
    <!-- /.error-content -->
  </div>

@stop

