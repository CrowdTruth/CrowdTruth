require(['jquery-noconflict'], function(jQuery) {
  
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;

  $('.checkboxes input').on('change', function() {
    var that = this;
    
    // CF Fix: remove highlights if subquestion is hidden
    if($(this).attr('value') == 'none' && !$(this).prop('checked')) { 
      $('.checkboxes .other').closest('.cml_row').css("background-color", "");
    }
    
    // highlight option if selected
    $(this).closest('.cml_row').css("background-color", function() {
        return that.checked ? "#99FF99" : "";
    });    
  });
  
  // highlight tagged terms in examles
  $('.examples').html(function(i, val) {
    val = val.replace(/{/g, "<span class='t1'>").replace(/}/g, "</span>");
    val = val.replace(/\[/g, "<span class='t2'>").replace(/\]/g, "</span>");
    return val;
  });
});