require(['jquery-noconflict'], function(jQuery) {
  
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;
  var totalHighlightsS1 = 0;
  var totalHighlightsS2 = 0;
  var totalHighlightsS3 = 0;
  var totalHighlightsS4 = 0;
  var totalHighlightsS5 = 0;
  var selected = [];
  
 $('.checkfield').parents('.cml_field').css({'position':'absolute','z-index':'-999'});

  // Preprocess
  
  // Array with available relation slots
  // Because all fields are hardcoded, they must be re-used.
  $('.eventextraction1').each(function() {
    $(this)[0].highlightWords = [];
    $(this)[0].typeId = 0; // // next unused slot
    for(i=0;i<10;i++) {
      $(this)[0].highlightWords.push(0);
    }
  });
  $('.eventextraction2').each(function() {
    $(this)[0].highlightWords2 = [];
    $(this)[0].typeId2 = 0; // // next unused slot
    for(i=0;i<10;i++) {
      $(this)[0].highlightWords2.push(0);
    }
  });
  $('.eventextraction3').each(function() {
    $(this)[0].highlightWords3 = [];
    $(this)[0].typeId3 = 0; // // next unused slot
    for(i=0;i<10;i++) {
      $(this)[0].highlightWords3.push(0);
    }
  });
  $('.eventextraction4').each(function() {
    $(this)[0].highlightWords4 = [];
    $(this)[0].typeId4 = 0; // // next unused slot
    for(i=0;i<10;i++) {
      $(this)[0].highlightWords4.push(0);
    }
  });
  $('.eventextraction5').each(function() {
    $(this)[0].highlightWords5 = [];
    $(this)[0].typeId5 = 0; // // next unused slot
    for(i=0;i<10;i++) {
      $(this)[0].highlightWords5.push(0);
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
    
    $('.ev' + id).val(""); // reset hidden term range
    
    $('.passage').find('.event' + id + 'term').replaceWith(function() { // remove terms from passages
      return $(this).contents();
    });
    
    // remove any partial selections to maintain color usage
    $('.passage').find('#selection').replaceWith(function() {
      return $(this).contents();
    });
    
    if (id.indexOf("a") > -1) {
      $('.eventextraction1')[0].highlightWords[id] = 0;
      $('.eventextraction1')[0].typeId = jQuery.inArray(0,$('.eventextraction1')[0].highlightWords); // get next unused relation slot
      totalHighlightsS1 --;
    }
    if (id.indexOf("b") > -1) {
      $('.eventextraction2')[0].highlightWords2[id] = 0;
      $('.eventextraction2')[0].typeId2 = jQuery.inArray(0,$('.eventextraction2')[0].highlightWords2); // get next unused relation slot
      totalHighlightsS2 --;
    }
    if (id.indexOf("c") > -1) {
      $('.eventextraction3')[0].highlightWords3[id] = 0;
      $('.eventextraction3')[0].typeId3 = jQuery.inArray(0,$('.eventextraction3')[0].highlightWords3); // get next unused relation slot
      totalHighlightsS3 --;
    }
    if (id.indexOf("d") > -1) {
      $('.eventextraction4')[0].highlightWords4[id] = 0;
      $('.eventextraction4')[0].typeId4 = jQuery.inArray(0,$('.eventextraction4')[0].highlightWords4); // get next unused relation slot
      totalHighlightsS4 --;
    }
    if (id.indexOf("e") > -1) {
      $('.eventextraction5')[0].highlightWords5[id] = 0;
      $('.eventextraction5')[0].typeId5 = jQuery.inArray(0,$('.eventextraction5')[0].highlightWords5); // get next unused relation slot
      totalHighlightsS5 --;
    } 
   
   var trueHigh = 0;
    if (selected.indexOf("s1") > -1) {
      if (totalHighlightsS1 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s2") > -1) {
      if (totalHighlightsS2 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s3") > -1) {
      if (totalHighlightsS3 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s4") > -1) {
      if (totalHighlightsS4 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s5") > -1) {
      if (totalHighlightsS5 > 0) {
        trueHigh ++;
      }
    }
    
    if (selected.indexOf("none") > -1 && selected.length == 1) {
      $('.checkfield').val("done");
    }
    else {
      $('.checkfield').val("");
    }
    
    if (selected.indexOf("none") > -1) {
        if (selected.length - 1 == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        } 
    }
    else {
        if (selected.length == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        }
    }
    
    
 //   alert(selected.join());
 //   alert($('.checkfield').val());

  });
  
  // split passages
  $(".eventextraction1 .passage").each(function() {
    var words = $(this).text().split(" ");
    var passage = "";
    $.each(words, function(i, v) {
     passage += "<span class='word'>" + v + "</span>";
    });
    $(this).html(passage);
  });
  $(".eventextraction2 .passage").each(function() {
    var words = $(this).text().split(" ");
    var passage = "";
    $.each(words, function(i, v) {
     passage += "<span class='word'>" + v + "</span>";
    });
    $(this).html(passage);
  });
  $(".eventextraction3 .passage").each(function() {
    var words = $(this).text().split(" ");
    var passage = "";
    $.each(words, function(i, v) {
     passage += "<span class='word'>" + v + "</span>";
    });
    $(this).html(passage);
  });
  $(".eventextraction4 .passage").each(function() {
    var words = $(this).text().split(" ");
    var passage = "";
    $.each(words, function(i, v) {
     passage += "<span class='word'>" + v + "</span>";
    });
    $(this).html(passage);
  });
  $(".eventextraction5 .passage").each(function() {
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
    var id = $(passage).parents('.eventextraction1')[0].typeId;
    if(!$(start).parents('#selection').length && id != -1) { // if no selection is made and maximum matches is not reached
      
      $(passage).children('#selection').contents().unwrap();
      start.wrapAll("<span class='term event" + id + "aterm' id='selection' />");

      $(passage).find('span:not(#selection)').bind('mouseover', function(e) {
        highlightMultiple(start, $(e.target), passage, id);
      });

    } else { // remove when click on selection
      $(passage).children('#selection').replaceWith(function() {
        return $(this).contents();
      });
    }
  }
  
  function highlightTerm2(passage, start) {
    var id = $(passage).parents('.eventextraction2')[0].typeId2;
    if(!$(start).parents('#selection').length && id != -1) { // if no selection is made and maximum matches is not reached
      
      $(passage).children('#selection').contents().unwrap();
      start.wrapAll("<span class='term event" + id + "bterm' id='selection' />");

      $(passage).find('span:not(#selection)').bind('mouseover', function(e) {
        highlightMultiple2(start, $(e.target), passage, id);
      });

    } else { // remove when click on selection
      $(passage).children('#selection').replaceWith(function() {
        return $(this).contents();
      });
    }
  }
  
  function highlightTerm3(passage, start) {
    var id = $(passage).parents('.eventextraction3')[0].typeId3;
    if(!$(start).parents('#selection').length && id != -1) { // if no selection is made and maximum matches is not reached
      
      $(passage).children('#selection').contents().unwrap();
      start.wrapAll("<span class='term event" + id + "cterm' id='selection' />");

      $(passage).find('span:not(#selection)').bind('mouseover', function(e) {
        highlightMultiple3(start, $(e.target), passage, id);
      });

    } else { // remove when click on selection
      $(passage).children('#selection').replaceWith(function() {
        return $(this).contents();
      });
    }
  }
  
  function highlightTerm4(passage, start) {
    var id = $(passage).parents('.eventextraction4')[0].typeId4;
    if(!$(start).parents('#selection').length && id != -1) { // if no selection is made and maximum matches is not reached
      
      $(passage).children('#selection').contents().unwrap();
      start.wrapAll("<span class='term event" + id + "dterm' id='selection' />");

      $(passage).find('span:not(#selection)').bind('mouseover', function(e) {
        highlightMultiple4(start, $(e.target), passage, id);
      });

    } else { // remove when click on selection
      $(passage).children('#selection').replaceWith(function() {
        return $(this).contents();
      });
    }
  }
  
  function highlightTerm5(passage, start) {
    var id = $(passage).parents('.eventextraction5')[0].typeId5;
    if(!$(start).parents('#selection').length && id != -1) { // if no selection is made and maximum matches is not reached
      
      $(passage).children('#selection').contents().unwrap();
      start.wrapAll("<span class='term event" + id + "eterm' id='selection' />");

      $(passage).find('span:not(#selection)').bind('mouseover', function(e) {
        highlightMultiple5(start, $(e.target), passage, id);
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
      $(start).wrapAll("<span class='term event" + id + "aterm' id='selection'/>");
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
      $(start).nextUntil(end.next()).andSelf().wrapAll("<span class='term event" + id + "aterm' id='selection' />");
    }
  }
  
  function highlightMultiple2(start, end, passage, id) {
    // ignore margins between elements
    $(passage).find('#selection').contents().unwrap();
    if(start.is(end)) { // single element
      $(start).wrapAll("<span class='term event" + id + "bterm' id='selection'/>");
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
      $(start).nextUntil(end.next()).andSelf().wrapAll("<span class='term event" + id + "bterm' id='selection' />");
    }
  }
  
  function highlightMultiple3(start, end, passage, id) {
    // ignore margins between elements
    $(passage).find('#selection').contents().unwrap();
    if(start.is(end)) { // single element
      $(start).wrapAll("<span class='term event" + id + "cterm' id='selection'/>");
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
      $(start).nextUntil(end.next()).andSelf().wrapAll("<span class='term event" + id + "cterm' id='selection' />");
    }
  }
  
    function highlightMultiple4(start, end, passage, id) {
    // ignore margins between elements
    $(passage).find('#selection').contents().unwrap();
    if(start.is(end)) { // single element
      $(start).wrapAll("<span class='term event" + id + "dterm' id='selection'/>");
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
      $(start).nextUntil(end.next()).andSelf().wrapAll("<span class='term event" + id + "dterm' id='selection' />");
    }
  }
  
  
    function highlightMultiple5(start, end, passage, id) {
    // ignore margins between elements
    $(passage).find('#selection').contents().unwrap();
    if(start.is(end)) { // single element
      $(start).wrapAll("<span class='term event" + id + "eterm' id='selection'/>");
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
      $(start).nextUntil(end.next()).andSelf().wrapAll("<span class='term event" + id + "eterm' id='selection' />");
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
    var typeId = $('.eventextraction1')[0].typeId;
    //console.log(typeId);
    // add terms in relation selection
    $('.ev' + typeId + 'a').val(selectionIndex($('.eventextraction1').find('#passage1')));
    $('#opinion' + typeId + 'a .term').text($('#passage1 #selection .word').not(":last").append(" ").end().text());
    $('#opinion' + typeId + 'a').slideDown();
    
    // remove selection ids
    $('#selection').removeAttr('id');

    // set relation slot as occupied; find next slot;
    $('.eventextraction1')[0].highlightWords[typeId] = 1;
    $('.eventextraction1')[0].typeId = jQuery.inArray(0,$('.eventextraction1')[0].highlightWords);  // next unused slot
  
    totalHighlightsS1 ++;   
    
    var trueHigh = 0;
    if (selected.indexOf("s1") > -1) {
      if (totalHighlightsS1 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s2") > -1) {
      if (totalHighlightsS2 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s3") > -1) {
      if (totalHighlightsS3 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s4") > -1) {
      if (totalHighlightsS4 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s5") > -1) {
      if (totalHighlightsS5 > 0) {
        trueHigh ++;
      }
    }
    
     if (selected.indexOf("none") > -1 && selected.length == 1) {
      $('.checkfield').val("done");
    }
    else {
      $('.checkfield').val("");
    }
    
    if (selected.indexOf("none") > -1) {
        if (selected.length - 1 == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        } 
    }
    else {
        if (selected.length == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        }
    }
    
    
  }
  
  function endSelection2() {
    var typeId = $('.eventextraction2')[0].typeId2;
    //console.log(typeId);
    // add terms in relation selection
    $('.ev' + typeId + 'b').val(selectionIndex($('.eventextraction2').find('#passage2')));
    $('#opinion' + typeId + 'b .term').text($('#passage2 #selection .word').not(":last").append(" ").end().text());
    $('#opinion' + typeId + 'b').slideDown();
    
    // remove selection ids
    $('#selection').removeAttr('id');

    // set relation slot as occupied; find next slot;
    $('.eventextraction2')[0].highlightWords2[typeId] = 1;
    $('.eventextraction2')[0].typeId2 = jQuery.inArray(0,$('.eventextraction2')[0].highlightWords2);  // next unused slot
  
    totalHighlightsS2 ++;   
    
    var trueHigh = 0;
    if (selected.indexOf("s1") > -1) {
      if (totalHighlightsS1 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s2") > -1) {
      if (totalHighlightsS2 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s3") > -1) {
      if (totalHighlightsS3 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s4") > -1) {
      if (totalHighlightsS4 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s5") > -1) {
      if (totalHighlightsS5 > 0) {
        trueHigh ++;
      }
    }
    
     if (selected.indexOf("none") > -1 && selected.length == 1) {
      $('.checkfield').val("done");
    }
    else {
      $('.checkfield').val("");
    }
    
    if (selected.indexOf("none") > -1) {
        if (selected.length - 1 == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        } 
    }
    else {
        if (selected.length == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        }
    }
    
    
  }
  
  function endSelection3() {
    var typeId = $('.eventextraction3')[0].typeId3;
    //console.log(typeId);
    // add terms in relation selection
    $('.ev' + typeId + 'c').val(selectionIndex($('.eventextraction3').find('#passage3')));
    $('#opinion' + typeId + 'c .term').text($('#passage3 #selection .word').not(":last").append(" ").end().text());
    $('#opinion' + typeId + 'c').slideDown();
    
    // remove selection ids
    $('#selection').removeAttr('id');

    // set relation slot as occupied; find next slot;
    $('.eventextraction3')[0].highlightWords3[typeId] = 1;
    $('.eventextraction3')[0].typeId3 = jQuery.inArray(0,$('.eventextraction3')[0].highlightWords3);  // next unused slot
  
    totalHighlightsS3 ++;   
    
    var trueHigh = 0;
    if (selected.indexOf("s1") > -1) {
      if (totalHighlightsS1 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s2") > -1) {
      if (totalHighlightsS2 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s3") > -1) {
      if (totalHighlightsS3 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s4") > -1) {
      if (totalHighlightsS4 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s5") > -1) {
      if (totalHighlightsS5 > 0) {
        trueHigh ++;
      }
    }
    
    if (selected.indexOf("none") > -1 && selected.length == 1) {
      $('.checkfield').val("done");
    }
    else {
      $('.checkfield').val("");
    }
    
    if (selected.indexOf("none") > -1) {
        if (selected.length - 1 == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        } 
    }
    else {
        if (selected.length == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        }
    }
    
    
  }

   function endSelection4() {
    var typeId = $('.eventextraction4')[0].typeId4;
    //console.log(typeId);
    // add terms in relation selection
    $('.ev' + typeId + 'd').val(selectionIndex($('.eventextraction4').find('#passage4')));
    $('#opinion' + typeId + 'd .term').text($('#passage4 #selection .word').not(":last").append(" ").end().text());
    $('#opinion' + typeId + 'd').slideDown();
    
    // remove selection ids
    $('#selection').removeAttr('id');

    // set relation slot as occupied; find next slot;
    $('.eventextraction4')[0].highlightWords4[typeId] = 1;
    $('.eventextraction4')[0].typeId4 = jQuery.inArray(0,$('.eventextraction4')[0].highlightWords4);  // next unused slot
  
    totalHighlightsS4 ++;   
     
     var trueHigh = 0;
    if (selected.indexOf("s1") > -1) {
      if (totalHighlightsS1 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s2") > -1) {
      if (totalHighlightsS2 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s3") > -1) {
      if (totalHighlightsS3 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s4") > -1) {
      if (totalHighlightsS4 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s5") > -1) {
      if (totalHighlightsS5 > 0) {
        trueHigh ++;
      }
    }
    
     if (selected.indexOf("none") > -1 && selected.length == 1) {
      $('.checkfield').val("done");
    }
    else {
      $('.checkfield').val("");
    }
    
    if (selected.indexOf("none") > -1) {
        if (selected.length - 1 == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        } 
    }
    else {
        if (selected.length == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        }
    }
    
    
  }
  
  function endSelection5() {
    var typeId = $('.eventextraction5')[0].typeId5;
    //console.log(typeId);
    // add terms in relation selection
    $('.ev' + typeId + 'e').val(selectionIndex($('.eventextraction5').find('#passage5')));
    $('#opinion' + typeId + 'e .term').text($('#passage5 #selection .word').not(":last").append(" ").end().text());
    $('#opinion' + typeId + 'e').slideDown();
    
    // remove selection ids
    $('#selection').removeAttr('id');

    // set relation slot as occupied; find next slot;
    $('.eventextraction5')[0].highlightWords5[typeId] = 1;
    $('.eventextraction5')[0].typeId5 = jQuery.inArray(0,$('.eventextraction5')[0].highlightWords5);  // next unused slot
  
    totalHighlightsS5 ++;   
    
    
    var trueHigh = 0;
    if (selected.indexOf("s1") > -1) {
      if (totalHighlightsS1 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s2") > -1) {
      if (totalHighlightsS2 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s3") > -1) {
      if (totalHighlightsS3 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s4") > -1) {
      if (totalHighlightsS4 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s5") > -1) {
      if (totalHighlightsS5 > 0) {
        trueHigh ++;
      }
    }
    
     if (selected.indexOf("none") > -1 && selected.length == 1) {
      $('.checkfield').val("done");
    }
    else {
      $('.checkfield').val("");
    }
    
    if (selected.indexOf("none") > -1) {
        if (selected.length - 1 == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        } 
    }
    else {
        if (selected.length == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        }
    }
    
    
  }
  
  
  $('body').on('click', 'input.event', function() {
    selected = [];
    $("input.event:checked").each(function() {
      if (selected.indexOf($(this).val()) == -1)
        selected.push($(this).val());
    });
    
    var trueHigh = 0;
    if (selected.indexOf("s1") > -1) {
      if (totalHighlightsS1 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s2") > -1) {
      if (totalHighlightsS2 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s3") > -1) {
      if (totalHighlightsS3 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s4") > -1) {
      if (totalHighlightsS4 > 0) {
        trueHigh ++;
      }
    }
    if (selected.indexOf("s5") > -1) {
      if (totalHighlightsS5 > 0) {
        trueHigh ++;
      }
    }
    
     if (selected.indexOf("none") > -1 && selected.length == 1) {
      $('.checkfield').val("done");
    }
    else {
      $('.checkfield').val("");
    }
    
    if (selected.indexOf("none") > -1) {
        if (selected.length - 1 == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        } 
    }
    else {
        if (selected.length == trueHigh) {
          $('.checkfield').val("done");
        }
        else {
          $('.checkfield').val("");
        }
    }
    
    
    
  });
           
  $("#passage1 span:not(#selection)").mousedown(function(e) {
  
  highlightTerm($(this).parents('.passage'), $(e.target));
   
  }).mouseup(function() {
    $('span').unbind('mouseover');
    if($(this).parents('.eventextraction1').find('#passage1 #selection').length) {
      endSelection();
    }
  });

  $("#passage2 span:not(#selection)").mousedown(function(e) {
  highlightTerm2($(this).parents('.passage'), $(e.target));
    
  }).mouseup(function() {
    $('span').unbind('mouseover');
    if($(this).parents('.eventextraction2').find('#passage2 #selection').length) {
      endSelection2();
    }
  });
  
  $("#passage3 span:not(#selection)").mousedown(function(e) {
  highlightTerm3($(this).parents('.passage'), $(e.target));
    
  }).mouseup(function() {
    $('span').unbind('mouseover');
    if($(this).parents('.eventextraction3').find('#passage3 #selection').length) {
      endSelection3();
    }
  });
  
 $("#passage4 span:not(#selection)").mousedown(function(e) {
  highlightTerm4($(this).parents('.passage'), $(e.target));
    
  }).mouseup(function() {
    $('span').unbind('mouseover');
    if($(this).parents('.eventextraction4').find('#passage4 #selection').length) {
      endSelection4();
    }
  });
  
  $("#passage5 span:not(#selection)").mousedown(function(e) {
  highlightTerm5($(this).parents('.passage'), $(e.target));
    
  }).mouseup(function() {
    $('span').unbind('mouseover');
    if($(this).parents('.eventextraction5').find('#passage5 #selection').length) {
      endSelection5();
    }
  });
  

});

