require(['jquery-noconflict'], function(jQuery) {
  
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;
  var totalHighlights = 0;
  // Preprocess
  
  // Array with available relation slots
  // Because all fields are hardcoded, they must be re-used.
  $('.eventextraction').each(function() {
    $(this)[0].events = [];
    $(this)[0].typeId = 0; // // next unused slot
    for(i=0;i<30;i++) {
      $(this)[0].events.push(0);
    }
  });
  var color = ['#8dd3c7','#ffffb3','#bebada','#fb8072','#80b1d3','#fdb462','#b3de69','#fccde5','#d9d9d9','#bc80bd'
               ,'#ccebc5','#ffed6f','#a6cee3','#1f78b4','#b2df8a','#33a02c','#fb9a99','#e31a1c','#fdbf6f','#ff7f00'
               ,'#cab2d6','#6a3d9a','#ffff99','#b15928','#fbb4ae','#b3cde3','#ccebc5','#decbe4','#fed9a6','#ffffcc'];
  
  // remove margin between relations list
  $('div.event').css('margin','0px');
  // Add remove buttons to relations
  $('select.event').after(function() {
    return '<span id=\'' + $(this).attr('name').split('[')[1].split(']')[0] + '\' class=\'remove\'>[x]</span>';
  });
  
  // remove relation function
  $('.remove').click(function() {
    $(this).siblings('.term').remove();
    eventextraction =  $(this).parents('.eventextraction');

    var id = $(this).attr('id');
    $(eventextraction).find('.' + id + 'a').val("");
    $(this).siblings('.' + id).addClass('hidden').parent().parent().addClass('hidden');
    $(this).siblings('.' + id).prop('selectedIndex',0); // reset selection
    $(eventextraction).find('.' + id + 'term').replaceWith(function() { 
      return $(this).contents();
    });
    
    // remove any partial selections to maintain color usage
    $(eventextraction).find('#selection').replaceWith(function() {
      return $(this).contents();
    });
    
    $(eventextraction)[0].events[id.substr(5)] = 0;
    $(eventextraction)[0].typeId = jQuery.inArray(0,$(eventextraction)[0].events); // get next unused relation slot

    totalHighlights --;
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
  function endSelection(eventextraction) {
    var typeId = $(eventextraction)[0].typeId;
  //  alert(typeId); 
    // add terms in relation selection
    $(eventextraction).find('.ev' + typeId + 'a').val(selectionIndex($(eventextraction).find('#passage')));
    $(eventextraction).find('.event' + typeId).before('<span class=\'term event' + typeId + 'term\'>' + $(eventextraction).find('#passage #selection .word').not(":last").append(" ").end().text() + '</span>');
    $(eventextraction).find('.event' + typeId).removeClass('hidden').parent().parent().removeClass('hidden');
    
    // remove selection ids
    $(eventextraction).find('#selection').removeAttr('id');

    // set relation slot as occupied; find next slot;
    $(eventextraction)[0].events[typeId] = 1;
    $(eventextraction)[0].typeId = jQuery.inArray(0,$(eventextraction)[0].events);  // next unused slot
  
    totalHighlights ++;
  }
    
  // highlighting triggers
  $(".passage span:not(#selection)").mousedown(function(e) {
    highlightTerm($(this).parents('.passage'), $(e.target));
    
  }).mouseup(function() {
    $('span').unbind('mouseover');
    if($(this).parents('.eventextraction').find('#passage #selection').length) {
      endSelection($(this).parents('.eventextraction'));
    }
  });

    
  $(".eventextraction .verification").blur(function(e){
   // alert($(this).val() + "--------------");
    if ($(this).val() != totalHighlights) {
      $(this).val("");
    }
    });
  
});


