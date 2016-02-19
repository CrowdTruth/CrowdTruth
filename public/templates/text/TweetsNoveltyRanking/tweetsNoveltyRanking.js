require(['jquery-noconflict'], function(jQuery) {
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  
  var tweet1isgreyed = false;
  var tweet2isgreyed = false;
  var clickedForVal = false;
  var selectedForVal = false;
  var globalID = '';
  var globalID2 = '';
  divValLocJS = document.getElementsByClassName("divValLocator");
  divValLocJS = document.getElementsByClassName("divValLocator2");
  
  //rgb(30, 144, 255) = blue
  //rgb(255, 0, 0) = red
  //rgb(0, 128, 0) = green  
  var $ = window.jQuery;
  $('.novelanswer').on('click', function() {
    novelbanner =  $(this).parents('.jsawesome');
    greyed1validator = $(novelbanner).find('.tweet1isgreyed');
    greyed2validator = $(novelbanner).find('.tweet2isgreyed');
    tweet1isgreyed = $(greyed1validator).attr("value");
    tweet2isgreyed = $(greyed2validator).attr("value");
    if ($(this).css("background-color") == "rgb(0, 128, 0)") {
      //Tweet 1 is less novel than tweet 2
      novelbanner =  $(this).parents('.jsawesome');
      novelvalidator = $(novelbanner).find('.clickedforval');
      selectedvalidator = $(novelbanner).find('.selectedforval');
      //greyed1validator = $(novelbanner).find('.tweet1isgreyed');
      //tweet1isgreyed = $(greyed1validator).attr("value");
      
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "red");
      $(novelbanner).find('.tweetbody1').css("background-color", "");
      $(novelbanner).find('.tweetbody2').css("background-color", "");
      $(novelbanner).find('.novelanswer2').css("background-color", "green");
      $(novelbanner).find('.novelanswer').html("Tweet 1 has LESS new information than Tweet 2");
      $(novelbanner).find('.novelanswer2').html("Tweet 2 has MORE new information than Tweet 1");
      
      $(novelbanner).find('.tweet1isgreyed').val('false');
      $(novelbanner).find('.tweet2isgreyed').val('false');
      $(novelbanner).find('.clickedforval').val('true');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('true');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('false');
      $(novelbanner).find('.tweet2irrelevant').val('false');   
      
      clickedForVal = $(novelvalidator).attr("value");
      selectedForVal = $(selectedvalidator).attr("value");
      if (clickedForVal === 'true' && selectedForVal === 'true') 
      {$(novelbanner).find(".forvalidation").val('validated');}
    }
    else if ($(this).css("background-color") == "rgb(255, 0, 0)") {
      //Equal novelty
      novelbanner =  $(this).parents('.jsawesome');
      novelvalidator = $(novelbanner).find('.clickedforval');
      selectedvalidator = $(novelbanner).find('.selectedforval');
      
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "rgb(30, 144, 255)");
      $(novelbanner).find('.tweetbody1').css("background-color", "");
      $(novelbanner).find('.tweetbody2').css("background-color", "");
      $(novelbanner).find('.novelanswer2').css("background-color", "rgb(30, 144, 255)");
      $(novelbanner).find('.novelanswer').html("Tweets have an EQUAL amount of new information");
      $(novelbanner).find('.novelanswer2').html("Tweets have an EQUAL amount of new information");
      
      $(novelbanner).find('.tweet1isgreyed').val('false');
      $(novelbanner).find('.tweet2isgreyed').val('false');
      $(novelbanner).find('.clickedforval').val('true');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('true');
      $(novelbanner).find('.tweet1irrelevant').val('false');
      $(novelbanner).find('.tweet2irrelevant').val('false');
      
      clickedForVal = $(novelvalidator).attr("value");
      selectedForVal = $(selectedvalidator).attr("value");
      
      if (clickedForVal === 'true' && selectedForVal === 'true') {$(novelbanner).find(".forvalidation").val('validated');}
    }
    else if ($(this).css("background-color") == "rgb(30, 144, 255)") {
      novelbanner =  $(this).parents('.jsawesome');
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "");
      $(novelbanner).find('.tweetbody2').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find('.novelanswer2').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find(".novelanswer").html("Tweet 1 is RELEVANT to the event");
      $(novelbanner).find(".novelanswer2").html("Tweet 2 is IRRELEVANT to the event");
      
      $(novelbanner).find('.tweet1isgreyed').val('false');
      $(novelbanner).find('.tweet2isgreyed').val('true');
      $(novelbanner).find('.clickedforval').val('false');
      
      $(novelbanner).find(".forvalidation").val('validated');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('false');
      $(novelbanner).find('.tweet2irrelevant').val('true');
    }
    else if (tweet2isgreyed === 'true' && tweet1isgreyed === 'false') {
      novelbanner =  $(this).parents('.jsawesome');
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "rgb(128,128,128)");
      $(novelbanner).find('.tweetbody1').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find('.tweetbody2').css("background-color", "");
      $(novelbanner).find('.novelanswer2').css("background-color", "");
      $(novelbanner).find(".novelanswer").html("Tweet 1 is IRRELEVANT to the event");
      $(novelbanner).find(".novelanswer2").html("Tweet 2 is RELEVANT to the event");
      
      $(novelbanner).find('.tweet1isgreyed').val('true');
      $(novelbanner).find('.tweet2isgreyed').val('false');
      $(novelbanner).find('.clickedforval').val('false');
      
      $(novelbanner).find(".forvalidation").val('validated');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('true');
      $(novelbanner).find('.tweet2irrelevant').val('false');
    }
    else if (tweet1isgreyed === 'true' && tweet2isgreyed === 'false') {
      //Both tweets are IRRELEVANT
      novelbanner =  $(this).parents('.jsawesome');
      novelvalidator = $(novelbanner).find('.clickedforval');
      selectedvalidator = $(novelbanner).find('.selectedforval');
      
      $(novelbanner).find('.tweetbody2').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find('.novelanswer2').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find(".novelanswer").html("Tweet 1 is IRRELEVANT to the event");
      $(novelbanner).find(".novelanswer2").html("Tweet 2 is IRRELEVANT to the event");
      
      $(novelbanner).find('.tweet1isgreyed').val('true');
      $(novelbanner).find('.tweet2isgreyed').val('true');
      $(novelbanner).find('.clickedforval').val('true');
      
      $(novelbanner).find(".forvalidation").val('validated');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('true');
      $(novelbanner).find('.tweet2irrelevant').val('true');
      
      clickedForVal = $(novelvalidator).attr("value");
      selectedForVal = $(selectedvalidator).attr("value");
      
      if (clickedForVal === 'true' && selectedForVal === 'true') {$(novelbanner).find(".forvalidation").val('validated');}
    }
    else {
      //Change tweet 1 to green and tweet 2 to red
      novelbanner =  $(this).parents('.jsawesome');
      novelvalidator = $(novelbanner).find('.clickedforval');
      selectedvalidator = $(novelbanner).find('.selectedforval');
      
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "rgb(0, 128, 0)");
      $(novelbanner).find('.tweetbody1').css("background-color", "");
      $(novelbanner).find('.tweetbody2').css("background-color", "");
      $(novelbanner).find(".novelanswer2").css("background-color", "red");
      $(novelbanner).find(".novelanswer").html("Tweet 1 has MORE new information than Tweet 2");
      $(novelbanner).find(".novelanswer2").html("Tweet 2 is has LESS new information than Tweet 1");
      
      $(novelbanner).find('.tweet1isgreyed').val('false');
      $(novelbanner).find('.tweet2isgreyed').val('false');
      $(novelbanner).find('.clickedforval').val('true');
      
      $(novelbanner).find('.tweet1novel').val('true');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('false');
      $(novelbanner).find('.tweet2irrelevant').val('false');
      
      clickedForVal = $(novelvalidator).attr("value");
      selectedForVal = $(selectedvalidator).attr("value");
      
      if (clickedForVal === 'true' && selectedForVal === 'true') {$(novelbanner).find(".forvalidation").val('validated');} 
    }
  });
  
  //rgb(30, 144, 255) = blue
  //rgb(255, 0, 0) = red
  //rgb(0, 128, 0) = green
  //Second Header Button
  $('.novelanswer2').on('click', function() {   
    novelbanner =  $(this).parents('.jsawesome');
    greyed1validator = $(novelbanner).find('.tweet1isgreyed');
    greyed2validator = $(novelbanner).find('.tweet2isgreyed');
    tweet1isgreyed = $(greyed1validator).attr("value");
    tweet2isgreyed = $(greyed2validator).attr("value");
    novelvalidator = $(novelbanner).find('.clickedforval');
    selectedvalidator = $(novelbanner).find('.selectedforval');
    clickedForVal = $(novelvalidator).attr("value");
    selectedForVal = $(selectedvalidator).attr("value");
    if ($(this).css("background-color") == "rgb(0, 128, 0)") {
      //Tweet 1 is more novel than tweet 2
      novelbanner =  $(this).parents('.jsawesome');
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "rgb(255, 0, 0)");
      $(novelbanner).find('.novelanswer').css("background-color", "rgb(0, 128, 0)");
      $(novelbanner).find('.tweetbody1').css("background-color", "");
      $(novelbanner).find(".novelanswer").html("Tweet 1 has MORE new information than Tweet 2");
      $(novelbanner).find(".novelanswer2").html("Tweet 2 has LESS new information than Tweet 1");
      
      $(novelbanner).find('.tweet1isgreyed').val('false');
      $(novelbanner).find('.tweet2isgreyed').val('false');
      $(novelbanner).find('.clickedforval').val('true');
      
      $(novelbanner).find('.tweet1novel').val('true');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('false');
      $(novelbanner).find('.tweet2irrelevant').val('false');   
      if (clickedForVal === 'true' && selectedForVal === 'true') {$(novelbanner).find(".forvalidation").val('validated');}    
    }
    else if ($(this).css("background-color") == "rgb(255, 0, 0)") {
      //Change tweets to EQUALLY novel, both blue
      novelbanner =  $(this).parents('.jsawesome');
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "rgb(30, 144, 255)");
      $(novelbanner).find('.tweetbody1').css("background-color", "");
      $(novelbanner).find('.tweetbody2').css("background-color", "");
      $(novelbanner).find('.novelanswer').css("background-color", "rgb(30, 144, 255)");
      $(novelbanner).find(".novelanswer").html("Tweets have an EQUAL amount of new information");
      $(novelbanner).find(".novelanswer2").html("Tweets have an EQUAL amount of new information");
      
      $(novelbanner).find('.tweet1isgreyed').val('false');
      $(novelbanner).find('.tweet2isgreyed').val('false');
      $(novelbanner).find('.clickedforval').val('true');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('true');
      $(novelbanner).find('.tweet1irrelevant').val('false');
      $(novelbanner).find('.tweet2irrelevant').val('false'); 
      if (clickedForVal === 'true' && selectedForVal === 'true') {$(novelbanner).find(".forvalidation").val('validated');}
    }
    else if ($(this).css("background-color") == "rgb(30, 144, 255)") {
      //Change tweet 1 to IRRELEVANT and tweet to RELEVANT
      novelbanner =  $(this).parents('.jsawesome');
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "");
      $(novelbanner).find('.tweetbody1').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find('.novelanswer').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find(".novelanswer").html("Tweet 1 is IRRELEVANT to the event");
      $(novelbanner).find(".novelanswer2").html("Tweet 2 is RELEVANT to the event");
      
      $(novelbanner).find('.tweet1isgreyed').val('true');
      $(novelbanner).find('.tweet2isgreyed').val('false');
      $(novelbanner).find('.clickedforval').val('false');
      
      $(novelbanner).find(".forvalidation").val('validated');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('true');
      $(novelbanner).find('.tweet2irrelevant').val('false');   
    }
    else if (tweet1isgreyed === 'true' && tweet2isgreyed === 'false') {
      novelbanner =  $(this).parents('.jsawesome');
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "rgb(128,128,128)");
      $(novelbanner).find('.tweetbody1').css("background-color", "");
      $(novelbanner).find('.tweetbody2').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find('.novelanswer').css("background-color", "");
      $(novelbanner).find(".novelanswer").html("Tweet 1 is RELEVANT to the event");
      $(novelbanner).find(".novelanswer2").html("Tweet 2 is IRRELEVANT to the event");
      
      $(novelbanner).find('.tweet1isgreyed').val('false');
      $(novelbanner).find('.tweet2isgreyed').val('true');
      $(novelbanner).find('.clickedforval').val('false');
      
      $(novelbanner).find(".forvalidation").val('validated');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('false');
      $(novelbanner).find('.tweet2irrelevant').val('true');
    }
    else if (tweet1isgreyed === 'false' && tweet2isgreyed === 'true') {
      //Both tweets are IRRELEVANT
      novelbanner =  $(this).parents('.jsawesome');
      $(novelbanner).find('.tweetbody1').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find('.novelanswer').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find('.novelanswer2').css("background-color", "rgb(128,128,128)");
      $(novelbanner).find(".novelanswer").html("Tweet 1 is IRRELEVANT to the event");
      $(novelbanner).find(".novelanswer2").html("Tweet 2 is IRRELEVANT to the event");
      
      $(novelbanner).find('.tweet1isgreyed').val('true');
      $(novelbanner).find('.tweet2isgreyed').val('true');
      $(novelbanner).find('.clickedforval').val('true');
      
      $(novelbanner).find(".forvalidation").val('validated');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('false');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('true');
      $(novelbanner).find('.tweet2irrelevant').val('true');  
    }
    else {
      //Tweet 1 is less novel than tweet 2.
      novelbanner =  $(this).parents('.jsawesome');
      $(novelbanner).find(".forvalidation").val('');
      $(this).css("background-color", "rgb(0, 128, 0)");
      $(novelbanner).find('.tweetbody1').css("background-color", "");
      $(novelbanner).find('.tweetbody2').css("background-color", "");
      $(novelbanner).find(".novelanswer").css("background-color", "rgb(255, 0, 0)");
      $(novelbanner).find(".novelanswer").html("Tweet 1 has LESS new information than Tweet 2");
      $(novelbanner).find(".novelanswer2").html("Tweet 2 has MORE new information than Tweet 1");
      
      $(novelbanner).find('.tweet1isgreyed').val('false');
      $(novelbanner).find('.tweet2isgreyed').val('false');
      $(novelbanner).find('.clickedforval').val('true');
      
      $(novelbanner).find('.tweet1novel').val('false');
      $(novelbanner).find('.tweet2novel').val('true');
      $(novelbanner).find('.tweetsequal').val('false');
      $(novelbanner).find('.tweet1irrelevant').val('false');
      $(novelbanner).find('.tweet2irrelevant').val('false');  
      if (clickedForVal === 'true' && selectedForVal === 'true') {$(novelbanner).find(".forvalidation").val('validated');}
    }
  });
  
  var totalHighlights = 0;
  // Preprocess
  
  // Array with available relation slots
  // Because all fields are hardcoded, they must be re-used.
  $('.eventextraction').each(function() {
    $(this)[0].events = [];
    $(this)[0].typeId = 0; // // next unused slot
    for(i=0;i<9;i++) {
      $(this)[0].events.push(0);
    }
  });
  
  $('.eventextraction2').each(function() {
    $(this)[0].events2 = [];
    $(this)[0].typeId2 = 0; // // next unused slot
    for(i=0;i<9;i++) {
      $(this)[0].events2.push(0);
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
  
  $('select').attr("disabled", "disabled"); 
  
  // remove margin between relations list
  $('div.eventb').css('margin','0px');
  // Add remove buttons to relations
  $('select.eventb').after(function() {
    return '<span id=\'' + $(this).attr('name').split('[')[1].split(']')[0] + '\' class=\'remove2\'>[x]</span>';
  });
  
  $('select').attr("disabled", "disabled"); 
  
  // remove relation function
  $('.remove').click(function() {
    $(this).siblings('.term').remove();
    eventextraction =  $(this).parents('.eventextraction');
    
    var id = globalID;
    //var id = $(this).attr('id');
    var thenum = id.replace( /^\D+/g, '');
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
    
    $("select").val("event");
    
    $(eventextraction)[0].events[id.substr(5)] = 0;
    $(eventextraction)[0].typeId = jQuery.inArray(0,$(eventextraction)[0].events); // get next unused relation slot
    
    $(eventextraction).find('.ev' + thenum + 'a').val('NA');
    
    totalHighlights --;
  });
  
  $('.remove2').click(function() {
    $(this).siblings('.term').remove();
    eventextraction =  $(this).parents('.eventextraction2');
    
    var id = globalID2;
    //var id = $(this).attr('id');
    var thenum = id.replace( /^\D+/g, '');
    $(eventextraction).find('.' + id + 'b').val("");
    $(this).siblings('.' + id).addClass('hidden').parent().parent().addClass('hidden');
    $(this).siblings('.' + id).prop('selectedIndex',0); // reset selection
    $(eventextraction).find('.' + id + 'term').replaceWith(function() { 
      return $(this).contents();
    });
    
    // remove any partial selections to maintain color usage
    $(eventextraction).find('#selection').replaceWith(function() {
      return $(this).contents();
    });
    
    $("select").val("eventb");
    
    $(eventextraction)[0].events2[id.substr(6)] = 0;
    $(eventextraction)[0].typeId2 = jQuery.inArray(0,$(eventextraction)[0].events2); // get next unused relation slot
    $(eventextraction).find('.ev' + thenum + 'b').val('NA');
    
    totalHighlights --;
  });
  // split passages
  $(".eventextraction .passage1").each(function() {
    var words = $(this).text().split(" ");
    var passage = "";
    $.each(words, function(i, v) {
      passage += "<span class='word'>" + v + "</span>";
    });
    $(this).html(passage);
  });
  
  $(".eventextraction2 .passage2").each(function() {
    var words = $(this).text().split(" ");
    var passage = "";
    $.each(words, function(i, v) {
      passage += "<span class='word'>" + v + "</span>";
    });
    $(this).html(passage);
  });
  
  
  
  // Highlighting functions
  
  // highlight term
  function highlightTerm( passage, start) {
    var id = $(passage).parents('.eventextraction')[0].typeId;
    if(!$(start).parents('#selection').length && id != -1) { // if no selection is made and maximum matches is not reached
      
      $(passage).children('#selection').contents().unwrap();
      start.wrapAll("<span class='term event" + id + "term' id='selection' />");
      
      $(passage).find('span:not(#selection)').bind('mouseover', function(e) {
        //highlightMultiple(start, $(e.target), passage, id);
      });
      
    } else { // remove when click on selection
      $(passage).children('#selection').replaceWith(function() {
        return $(this).contents();
      });
    }
  }
  
  function highlightTerm2( passage, start) {
    var id = $(passage).parents('.eventextraction2')[0].typeId2;
    if(!$(start).parents('#selection').length && id != -1) { // if no selection is made and maximum matches is not reached
      
      $(passage).children('#selection').contents().unwrap();
      start.wrapAll("<span class='term eventb" + id + "term' id='selection' />");
      
      $(passage).find('span:not(#selection)').bind('mouseover', function(e) {
        //highlightMultiple(start, $(e.target), passage, id);
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
  
  function highlightMultiple2(start, end, passage, id) {
    // ignore margins between elements
    $(passage).find('#selection').contents().unwrap();
    if(start.is(end)) { // single element
      $(start).wrapAll("<span class='term eventb" + id + "term' id='selection'/>");
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
      $(start).nextUntil(end.next()).andSelf().wrapAll("<span class='term eventb" + id + "term' id='selection' />");
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
  
  function selectionIndex2(passage) {
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
    // add terms in relation selection
    $(eventextraction).find('.ev' + typeId + 'a').val(selectionIndex($(eventextraction).find('#passage1')));
    //$(eventextraction).find('.event' + typeId).before('<span class=\'term event' + typeId + 'term\'>' + $(eventextraction).find('#passage1 #selection .word').not(":last").append(" ").end().text() + '</span>');
    //$(eventextraction).find('.event' + typeId).removeClass('hidden').parent().parent().removeClass('hidden');
    
    // remove selection ids
    $(eventextraction).find('#selection').removeAttr('id');
    
    // set relation slot as occupied; find next slot;
    $(eventextraction)[0].events[typeId] = 1;
    $(eventextraction)[0].typeId = jQuery.inArray(0,$(eventextraction)[0].events);  // next unused slot
    
    totalHighlights ++;
  }
  
  function endSelection2(eventextraction) {
    var typeId = $(eventextraction)[0].typeId2;
    // add terms in relation selection
    $(eventextraction).find('.ev' + typeId + 'b').val(selectionIndex2($(eventextraction).find('#passage2')));
    //$(eventextraction).find('.eventb' + typeId).before('<span class=\'term eventb' + typeId + 'term\'>' + $(eventextraction).find('#passage2 #selection .word').not(":last").append(" ").end().text() + '</span>');
    //$(eventextraction).find('.eventb' + typeId).removeClass('hidden').parent().parent().removeClass('hidden');
    
    // remove selection ids
    $(eventextraction).find('#selection').removeAttr('id');
    
    // set relation slot as occupied; find next slot;
    $(eventextraction)[0].events2[typeId] = 1;
    $(eventextraction)[0].typeId2 = jQuery.inArray(0,$(eventextraction)[0].events2);  // next unused slot
    
    totalHighlights ++;
  }
  
  // highlighting triggers
  $(".passage1 span:not(#selection)").mousedown(function(e) {
    var checkEventSelection = $(e.target).parents().parents().attr('class');
    novelbanner =  $(this).parents('.jsawesome');
    greyed1validator = $(novelbanner).find('.tweet1isgreyed');
    novelvalidator = $(novelbanner).find('.clickedforval');
    selectedvalidator = $(novelbanner).find('.selectedforval');
    
    tweet1isgreyed = $(greyed1validator).attr("value");
    selectedForVal = $(selectedvalidator).attr("value");
    clickedForVal = $(novelvalidator).attr("value");
    
    var getSelectedEvent = $(e.target).parents().attr('class');    
    var extractedEventInt = getSelectedEvent.match(/\d+/);
    if (tweet1isgreyed === 'false' && checkEventSelection !== "passage1") {
      highlightTerm( $(this).parents('.passage1'), $(e.target));
      $(novelbanner).find('.selectedforval').val('true');
    }
    else {
      globalID = "event"+extractedEventInt;
      $(novelbanner).find('.remove').click();
    }
  }).mouseup(function() {
    selectedForVal = $(novelvalidator).attr("value");
    if (tweet1isgreyed === 'false') {
      $('span').unbind('mouseover');
      if($(this).parents('.eventextraction').find('#passage1 #selection').length) {
        endSelection($(this).parents('.eventextraction'));
      }
    }
    if (selectedForVal === 'true' && clickedForVal === 'true') {
      $(novelbanner).find(".forvalidation").val('validated');
    };
  });
  
  $(".passage2 span:not(#selection)").mousedown(function(e) {
    var checkEventSelection2 = $(e.target).parents().parents().attr('class');
    novelbanner =  $(this).parents('.jsawesome');
    greyed1validator = $(novelbanner).find('.tweet1isgreyed');
    novelvalidator = $(novelbanner).find('.clickedforval');
    selectedvalidator = $(novelbanner).find('.selectedforval');
    
    tweet2isgreyed = $(greyed1validator).attr("value");
    selectedForVal = $(selectedvalidator).attr("value");
    clickedForVal = $(novelvalidator).attr("value");
    
    var getSelectedEvent2 = $(e.target).parents().attr('class');
    var extractedEventInt2 = getSelectedEvent2.match(/\d+/);
    if (tweet2isgreyed === 'false' && checkEventSelection2 !== "passage2") {
      highlightTerm2( $(this).parents('.passage2'), $(e.target));
      $(novelbanner).find('.selectedforval').val('true');
    }
    else {
      globalID2 = "eventb"+extractedEventInt2;
      $(novelbanner).find('.remove2').click();
    }
  }).mouseup(function() {
    selectedForVal = $(novelvalidator).attr("value");
    if (tweet2isgreyed === 'false') {
      $('span').unbind('mouseover');
      if($(this).parents('.eventextraction2').find('#passage2 #selection').length) {
        endSelection2($(this).parents('.eventextraction2'));
      }
    }
    if (selectedForVal === 'true' && clickedForVal === 'true') {
      $(novelbanner).find(".forvalidation").val('validated');
    };
  });
  $(".eventextraction .verification").blur(function(e){
    if ($(this).val() != totalHighlights) {
      $(this).val("");
    }
  });
  
  
});
