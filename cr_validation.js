
$(function() {

  $('#btnSend').on('click', function() {

      // intelliSub();
      a = validateAOID();
      b = validateCustomerName();
      // c = validateProjectName();
      d = validateRequestType();
      e = validateBrand();
      f = validateSubject();

      if(a & b & d & e & f) {
        $('#preloader-milktea').removeAttr('hidden', '');
        $('#btnSend').hide();
        $('#formRequest').submit();
      } else {
        Swal.fire({
          type: 'error',
          title: 'Please fill in all required fields!',
          showConfirmButton: false,
          width: '333px',
          timer: 1300
        })
      }
  })
})

$('#projectName').on('focusout', function() {
  validateProjectName();
  intelliSub();
})

$('#subject').on('focusout', function() {
  validateSubject();
})

$('#aoID').on('select2:close', function() {
  validateAOID();
  intelliSub();
})

$('#requestTypeID').on('select2:close', function() {
  intelliTemp();
  validateRequestType();
  intelliSub();
})

$('#brandID').on('select2:close', function() {
  intelliTemp();
  validateBrand();
  intelliSub();
})


function validateAOID() {

  if($('#aoID option[value]:selected').text()=='') { 
    validity = false;
    $('#aoID').addClass('is-invalid');
  } else {
    validity = true;
    $('#aoID').removeClass('is-invalid');
  }

  return validity;
}

function validateCustomerName() {

  if(!$('#customerName').val().trim()) { 
    validity = false;
    $('#customerName').addClass('is-invalid');
  } else {
    validity = true;
    $('#customerName').removeClass('is-invalid');
  }

  return validity;
}

function validateProjectName() {

  if(!$('#projectName').val().trim()) { 
    validity = false;
    $('#projectName').addClass('is-invalid');
  } else {
    validity = true;
    $('#projectName').removeClass('is-invalid');
  }

  return validity;
}

function validateRequestType() {

  if($('#requestTypeID option[value]:selected').text()=='') { 
    validity = false;
    $('#requestTypeID').addClass('is-invalid');
  } else {
    validity = true;
    $('#requestTypeID').removeClass('is-invalid');
  }

  return validity;
}

function validateBrand() {

  if($('#brandID option[value]:selected').text()=='') { 
    validity = false;
    $('#brandID').addClass('is-invalid');
  } else {
    validity = true;
    $('#brandID').removeClass('is-invalid');
  }

  return validity;
}

function validateSubject() {

  if(!$('#subject').val().trim()) { 
    validity = false;
    $('#subject').addClass('is-invalid');
  } else {
    validity = true;
    $('#subject').removeClass('is-invalid');
  }

  return validity;
}

function intelliTemp() {

  var brandID = $('#brandID').select2('data');

  $.trim(brandID = jQuery.map(brandID, function(n, i){
    return ''+n.id;
  }) + "");

  rTypeID = $('#requestTypeID').val();
  tTypeID = $('#tTypeID').val();

  if(rTypeID == 1 && tTypeID == 1) {
    if($('#temp1').val() == 1) { 
      $('#temp1').val(0);
      $('#requestContent').summernote('code', '');
    }
  }

  if(rTypeID == 1 && tTypeID == 2 && $('#temp1').val() == 0) {
    if($('#temp2').val() == 1 || $('#temp3').val() == 1 || $('#temp4').val() == 1) { 
      $('#requestContent').summernote('code', '');
    }

    $('#temp2').val(0);
    $('#temp3').val(0);
    $('#temp4').val(0);

    $('#temp1').val(1);
    $('#requestContent').summernote('code', costNFInquiryTemplate());
  } 

  if (rTypeID == 2 && $('#temp2').val() == 0) {
    if($('#temp1').val() == 1 || $('#temp3').val() == 1 || $('#temp4').val() == 1) { 
      $('#requestContent').summernote('code', '');
    }

    $('#temp1').val(0);
    $('#temp3').val(0);
    $('#temp4').val(0);

    $('#temp2').val(1);
    $('#requestContent').summernote('code', demoUnitTemplate());
  } 

  if(rTypeID == 3) {
    if($('#temp1').val() == 1 || $('#temp2').val() == 1 || 
      brandID.indexOf(449) == -1 && $('#temp3').val() == 1 || 
      brandID.indexOf(130) == -1 && $('#temp4').val() == 1) { 
        $('#requestContent').summernote('code', '');
    }

    if(brandID.indexOf(449) >= 0 && $('#temp3').val() == 0) {
      if($('#temp1').val() == 1 || $('#temp2').val() == 1 || $('#temp4').val() == 1) { 
        $('#requestContent').summernote('code', '');
      }

      $('#temp1').val(0);
      $('#temp2').val(0);
      $('#temp4').val(0);

      $('#temp3').val(1);
      $('#requestContent').summernote('code', serviceSchedAPCTemplate());
    } else if(brandID.indexOf(130) >= 0 && $('#temp4').val() == 0) {
      if($('#temp1').val() == 1 || $('#temp2').val() == 1 || $('#temp3').val() == 1) { 
        $('#requestContent').summernote('code', '');
      }

      $('#temp1').val(0);
      $('#temp2').val(0);
      $('#temp3').val(0);

      $('#temp4').val(1);
      $('#requestContent').summernote('code', serviceSchedEmerTemplate());
    } 
  }

  if(rTypeID == 8) {
    if($('#temp1').val() == 1 || $('#temp2').val() == 1 || $('#temp3').val() == 1 || $('#temp4').val() == 1) {
      $('#requestContent').summernote('code', '');
    }

    $('#temp1').val(0);$('#temp2').val(0);$('#temp3').val(0);$('#temp4').val(0);
  }
}

function intelliSub() {
  var temp = '';

  var tType = $('#tTypeID').val();

  var brandName = $('#brandID').select2('data');

  brandName = jQuery.map(brandName, function(n, i) {
    return ' '+n.text;
  }) + "";

  var customerName = $('#customerName').val();
  var reqType = $('#requestTypeID :selected').text();
  var projName = $('#projectName').val();

  if(!$('#customerName').val().trim()) { 
     customerName = 'Customer Name';
  }

  if($('#requestTypeID option[value]:selected').text()=='') { 
     reqType = 'Request Type';
  } else if(reqType == 'Cost Inquiry') {
     reqType = 'RFQ';
  }

  if($('#brandID option[value]:selected').text()=='') { 
      brandName = ' Brand';
  }

  temp = reqType+' - '+customerName+':'+projName+' ('+brandName.substring(1)+')';
  if(tType == 2) {
    temp = 'NF: '+reqType+' - '+customerName+':'+projName+' ('+brandName.substring(1)+')';
  } 

  $('#subject').val(temp);
}
