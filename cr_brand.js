function createBrand(url) {

  $('#brandID').val(null).trigger('change');
  $('#brandID').empty();
  // $('#brandID').select2('destroy');

  $('#dvSpinBrand').removeAttr('hidden');

  $.ajax(url).done(function(data) {
    $.each(data.data, function(key, val) {
      if(val.brand_id !== 456 && val.brand_id !== 457) {
        $('#brandID').append("<option value='"+val.brand_id+"'>"+val.brand+"</option>");
      }
    })
  }).fail(function(data){
    createBrand(url);
  });



  setTimeout(function() {
    $('#dvSpinBrand').attr('hidden', '');
  }, 500);

  // renderSelect2();
}

function renderSelect2() {
  $('.select2').select2({
      theme: 'bootstrap4'
  });
}