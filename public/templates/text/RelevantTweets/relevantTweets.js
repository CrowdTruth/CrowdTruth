require(['jquery-noconflict'], function(jQuery) {
  
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;
  var totalHighlights = 0;
  var selected = [];
  
  $('.checkfield').parents('.cml_field').css({'position':'absolute','z-index':'-999'});

  // Preprocess
  
  // Array with available relation slots
  // Because all fields are hardcoded, they must be re-used.
  $('.eventextraction').each(function() {
    $(this)[0].highlightWords = [];
    $(this)[0].typeId = 0; // // next unused slot
    for(i=0;i<30;i++) {
      $(this)[0].highlightWords.push(0);
    }
  });
  var color = ['#8dd3c7','#ffffb3','#bebada','#fb8072','#80b1d3','#fdb462','#b3de69','#fccde5','#d9d9d9','#bc80bd'
               ,'#ccebc5','#ffed6f','#a6cee3','#1f78b4','#b2df8a'];

  
  // remove relation function
  $('.remove').click(function() {
    var id = $(this).attr('id');
    id = id.replace('remove','');
    
    $('#opinion' + id).hide(''); // hide relation
    $('#opinion' + id).children('.term').text(''); // remove text from term
    $('#opinion' + id + ' select').prop('selectedIndex',0); // reset selections
    
    $('.ev' + id + 'a').val(""); // reset hidden term range
    $('.passage').find('.event' + id + 'term').replaceWith(function() { // remove terms from passages
      return $(this).contents();
    });
    
    // remove any partial selections to maintain color usage
    $('.passage').find('#selection').replaceWith(function() {
      return $(this).contents();
    });
    
    $('.eventextraction')[0].highlightWords[id] = 0;
    $('.eventextraction')[0].typeId = jQuery.inArray(0,$('.eventextraction')[0].highlightWords); // get next unused relation slot

    totalHighlights --;
    
 //   alert(selected.join());
 //   alert($('.checkfield').val());
    if (selected.length == 1 && selected.indexOf('[NONE]') > -1) {
        $('.checkfield').val("done");
    }
    else if (selected.length == 0){
        $('.checkfield').val("");
    }
    else {
      if (totalHighlights == 0)
        $('.checkfield').val("");
      else
        $('.checkfield').val("done"); 
    }
  });
  
  // split passages
  $(".eventextraction .passage").each(function() {
    var words = $(this).text().split(" ");
    var passage = "";
    $.each(words, function(i, v) {
     passage += "<span class='word'>" + v + "</span>";
    });
    $(this).html(passage);
  });
  
  // Highlighting functions
  // highlight term
  function highlightTerm(passage, start) {
    var id = $(passage).parents('.eventextraction')[0].typeId;
    if(!$(start).parents('#selection').length && id != -1) { // if no selection is made and maximum matches is not reached
      
      $(passage).children('#selection').contents().unwrap();
      start.wrapAll("<span class='term event" + id + "term' id='selection' />");

      $(passage).find('span:not(#selection)').bind('mouseover', function(e) {
        highlightMultiple(start, $(e.target), passage, id);
      });

    } else { // remove when click on selection
      $(passage).children('#selection').replaceWith(function() {
        return $(this).contents();
      });
    }
  }
  
  // highlight range of terms
  function highlightMultiple(start, end, passage, id) {
    // ignore margins between elements
    $(passage).find('#selection').contents().unwrap();
    if(start.is(end)) { // single element
      $(start).wrapAll("<span class='term event" + id + "term' id='selection'/>");
    } else { // if range of elements
      if($(passage).find('span').index(start) > $(passage).find('span').index(end)) { // swap if end is before start
        var temp = end;
        end = start;
        start = temp;
      }
      
      
      if(!start.parent().not($('#selection')).is(end.parent().not($('#selection')))) {
        // common parent element
        var common = end.parents().not($('#selection')).has(start).first();
        
        if(start.parent('.term').not(common).length) { // if word has a parent term
          start = $(common).children().has(start);
          // $(start).parent('.term');
        }
        
        if(end.parent('.term').not(common).length) {
          end = $(common).children().has(end);
          //end = $(end).parent('.term');
        }
      }
      // highlight range
      $(start).nextUntil(end.next()).andSelf().wrapAll("<span class='term event" + id + "term' id='selection' />");
    }
  }

  
  // get word range index
  function selectionIndex(passage) {
    var selection = $(passage).find('#selection .word');
    var startId = $(passage).find('.word').index(selection.first());
    if(selection.length == 1) { // single word
      return startId;
    } else { // range of words
      return startId + "-" + (startId + selection.length - 1);
    }
  }
  
  // finish selection and link terms
  function endSelection() {
    var typeId = $('.eventextraction')[0].typeId;

    // add terms in relation selection
    $('.ev' + typeId + 'a').val(selectionIndex($('.eventextraction').find('#passage')));
    $('#opinion' + typeId + ' .term').text($('#passage #selection .word').not(":last").append(" ").end().text());
    $('#opinion' + typeId).slideDown();
       
    $('.event option').each(function() {
      if ( selected.indexOf($(this).val()) == -1 ) {
        $(this).hide();
      }
      else {
        $(this).show();
        if (selected.length == 1 || (selected.length == 2 && selected.indexOf('[NONE]') > -1)) {
          $(this).attr("selected", "selected");
        }
        else {
          $(this).removeAttr("selected");
        }
      }
    });
    
    // remove selection ids
    $('#selection').removeAttr('id');

    // set relation slot as occupied; find next slot;
    $('.eventextraction')[0].highlightWords[typeId] = 1;
    $('.eventextraction')[0].typeId = jQuery.inArray(0,$('.eventextraction')[0].highlightWords);  // next unused slot
  
    totalHighlights ++;
    
    if (selected.length == 1 && selected.indexOf('[NONE]') > -1) {
        $('.checkfield').val("done");
    }
    else if (selected.length == 0){
        $('.checkfield').val("");
    }
    else {
      if (totalHighlights == 0)
        $('.checkfield').val("");
      else
        $('.checkfield').val("done"); 
    }
    
  }
    
  $('body').on('click', 'input.event', function() {
    selected = [];
    $("input.event:checked").each(function() {
      selected.push($(this).val());
    });
    $('.event option').each(function() {
         //  alert($(this).val());
      if ( selected.indexOf($(this).val()) == -1 ) {
        $(this).hide();
      }
      else {
        $(this).show();
        if (selected.length == 1 || (selected.length == 2 && selected.indexOf('[NONE]') > -1)) {
          $(this).attr("selected", "selected");
        }
        else {
          $(this).removeAttr("selected");
        }
      }
         });
    if ((selected.length == 0) || (selected.length == 1 && selected.indexOf('[NONE]') > -1)) {
      $( "span.remove" ).each(function( index ) {
        var id = $(this).attr('id');
        id = id.replace('remove','');
        $('#opinion' + id).hide(''); // hide relation
        $('#opinion' + id).children('.term').text(''); // remove text from term
        $('#opinion' + id + ' select').prop('selectedIndex',0); // reset selections
        
        $('.ev' + id + 'a').val(""); // reset hidden term range
        $('.passage').find('.event' + id + 'term').replaceWith(function() { // remove terms from passages
          return $(this).contents();
        });
        
        $('.eventextraction')[0].highlightWords[id] = 0;
        $('.eventextraction')[0].typeId = jQuery.inArray(0,$('.eventextraction')[0].highlightWords); // get next unused relation slot
        totalHighlights --;
        
        if (selected.length == 1 && selected.indexOf('[NONE]') > -1) {
        $('.checkfield').val("done");
    }
    else if (selected.length == 0){
        $('.checkfield').val("");
    }
    else {
      if (totalHighlights == 0)
        $('.checkfield').val("");
      else
        $('.checkfield').val("done"); 
    }
    
      });
    }
  });
           
  $(".passage span:not(#selection)").mousedown(function(e) {
    //if only NONE selected, then do not activate 
    if (totalHighlights < 15)
    if (selected.length != 0) 
      if ((selected.length == 1 && selected.indexOf('[NONE]') == -1) || selected.length > 1)
        highlightTerm($(this).parents('.passage'), $(e.target));
    
  }).mouseup(function() {
    $('span').unbind('mouseover');
    if($(this).parents('.eventextraction').find('#passage #selection').length) {
      endSelection();
    }
  });

});

