require(['jquery-noconflict'], function(jQuery) {
  
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;
    
    $('.checkboxes').html(function() {
    var elems = $(this).children('.cml_row');
    var i = elems.length, j, temp;
    while ( --i )
    {
      j = Math.floor( Math.random() * (i - 1) );
      temp = elems[i];
      elems[i] = elems[j];
      elems[j] = temp;
    }
    
    $(this).children('.cml_row').remove();  
    for(var i=0; i < elems.length; i++) {
      if($(elems[i]).find('input').attr('value') != "No data available") {
        $(this).append(elems[i]);
      }
    }
  });

  
  
  $('.checkboxes input').on('change', function() {
    var that = this;
    $(this).closest('.cml_row').css("background-color", function() {
        return that.checked ? "#99FF99" : "";
    });    
  });
});