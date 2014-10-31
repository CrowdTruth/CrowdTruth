require(['jquery-noconflict'], function(jQuery) {
  
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;
  
  (function($) {
    
    var helpers = {
      createLine: function(first, second, options){
        
        // use center of first and second term
        x1 = $(first).offset().left + $(first).width() / 2;
        y1 = $(first).offset().top + $(first).height() / 2;
        x2 = $(second).offset().left + $(second).width() / 2;
        y2 = $(second).offset().top + $(second).height() / 2;
        
        // if term spans more than one sentence:
        // use the center of the first word in the term
        if($(first).height() > 40) {
          x1 = $(first).first().find('.word').offset().left + $(first).find('.word').first().width() / 2;
          y1 = $(first).first().find('.word').offset().top + $(first).find('.word').first().height() / 2;
        }
        if($(second).height() > 40) {
          x2 = $(second).find('.word').first().offset().left + $(second).find('.word').first().width() / 2;
          y2 = $(second).find('.word').first().offset().top + $(second).find('.word').first().height() / 2;
        }
        
        // Check if browser is Internet Exploder ;)
        var isIE = navigator.userAgent.indexOf("MSIE") > -1;
        if (x2 < x1){
          var temp = x1;
          x1 = x2;
          x2 = temp;
          temp = y1;
          y1 = y2;
          y2 = temp;
        }
        var line = document.createElement("div");
        
        
        // Formula for the distance between two points
        // http://www.mathopenref.com/coorddist.html
        var length = Math.sqrt((x1-x2)*(x1-x2) + (y1-y2)*(y1-y2));
        
        line.style.width = length + "px";
        line.style.borderBottom = options.stroke + "px solid";
        line.style.borderColor = options.color;
        line.style.position = "absolute";
        line.style.zIndex = options.zindex;
        line.className = options.class;
        

          var angle = Math.atan((y2-y1)/(x2-x1));
          line.style.top = y1 + 0.5*length*Math.sin(angle) + "px";
          line.style.left = x1 - 0.5*length*(1 - Math.cos(angle)) + "px";
          line.style.MozTransform = line.style.WebkitTransform = line.style.msTransform = line.style.OTransform= "rotate(" + angle + "rad)";
          
        return line;
      }
    }
        
        
        $.fn.line = function( x1, y1, x2, y2, options, callbacks) {
          return $(this).each(function(){
            if($.isFunction(options)){
              callback = options;
              options = null;
            }else{
              callback = callbacks;
            }
            options = $.extend({}, $.fn.line.defaults, options);
            
            $(this).append(helpers.createLine(x1,y1,x2,y2,options)).promise().done(function(){
              if($.isFunction(callback)){
                callback.call();
              }
            });
            
            
          });
        };
    $.fn.line.defaults = {  zindex : 10000,
                            color : '#000000',
                            stroke: "1",
                           };
  })(jQuery);
  

 
  // Preprocess
    
  
  // Array with available relation slots
  // Because all fields are hardcoded, they must be re-used.
  $('.alignment').each(function() {
    $(this)[0].relations = [];
    $(this)[0].relId = 0; // // next unused slot
    for(i=0;i<30;i++) {
      $(this)[0].relations.push(0);
    }
  });
  var color = ['#8dd3c7','#ffffb3','#bebada','#fb8072','#80b1d3','#fdb462','#b3de69','#fccde5','#d9d9d9','#bc80bd'
               ,'#ccebc5','#ffed6f','#a6cee3','#1f78b4','#b2df8a','#33a02c','#fb9a99','#e31a1c','#fdbf6f','#ff7f00'
               ,'#cab2d6','#6a3d9a','#ffff99','#b15928','#fbb4ae','#b3cde3','#ccebc5','#decbe4','#fed9a6','#ffffcc'];
  
  // remove margin between relations list
  $('div.relation').css('margin','0px');
  // Add remove buttons to relations
  $('select.relation').after(function() {
    return '<span id=\'' + $(this).attr('name').split('[')[1].split(']')[0] + '\' class=\'remove\'>[x]</span>';
  });
  
  // remove relation function
  $('.remove').click(function() {
    $(this).siblings('.term').remove();
    alignment =  $(this).parents('.alignment');

    var id = $(this).attr('id');
    $(alignment).find('.' + id + 'a').val("");
    $(alignment).find('.' + id + 'b').val("");
    $(this).siblings('.' + id).addClass('hidden').parent().parent().addClass('hidden');
    $(this).siblings('.' + id).prop('selectedIndex',0); // reset selection
    $(alignment).find('.' + id + 'term').replaceWith(function() {
      return $(this).contents();
    });
    
    // remove any partial selections to maintain color usage
    $(alignment).find('#selection').replaceWith(function() {
      return $(this).contents();
    });
    
    $(alignment)[0].relations[id.substr(3)] = 0;
    $(alignment)[0].relId = jQuery.inArray(0,$(alignment)[0].relations); // get next unused relation slot
    redrawLines();
    
    // if less then 4 matches show notPossible field
    var relCount = 0;
    for(i in $(alignment)[0].relations) {
      if($(alignment)[0].relations[i] == 1) {
        relCount++;
      }
    }
    if(relCount < 3) {
      $(alignment).find('.notPossible').show();
      $(alignment).find('.relTitle').hide();
    }
  });

  // split passages
  $(".alignment .passage").each(function() {
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
    var id = $(passage).parents('.alignment')[0].relId;
    if(!$(start).parents('#selection').length && id != -1) { // if no selection is made and maximum matches is not reached
      
      $(passage).children('#selection').contents().unwrap();
      start.wrapAll("<span class='term rel" + id + "term' id='selection' />");

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
      $(start).wrapAll("<span class='term rel" + id + "term' id='selection'/>");
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
      $(start).nextUntil(end.next()).andSelf().wrapAll("<span class='term rel" + id + "term' id='selection' />");
    }
  }
  

  // update relation lines
  function redrawLines() {
    $('.alignment').each(function() {
      for(i in $(this)[0].relations) {
        if($(this)[0].relations[i] == 1) {
          $(this).children('div.rel' + i + 'term').remove();
          $(this).line( $(this).find('#passage1 .rel' + i + 'term'), $(this).find('#passage2 .rel' + i + 'term'), {color: color[i], stroke:6, zindex:3, class: 'rel' + i + 'term'});
        }
      }
    });
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
  function endSelection(alignment) {
    var relId = $(alignment)[0].relId;
    
    // add terms in relation selection
    $(alignment).find('.rel' + relId + 'a').val(selectionIndex($(alignment).find('#passage1')));
    $(alignment).find('.rel' + relId + 'b').val(selectionIndex($(alignment).find('#passage2')));
    $(alignment).find('.rel' + relId).before('<span class=\'term rel' + relId + 'term\'>' + $(alignment).find('#passage1 #selection .word').not(":last").append(" ").end().text() + '</span>');
    $(alignment).find('.rel' + relId).after('<span class=\'term rel' + relId + 'term\'>' + $(alignment).find('#passage2 #selection .word').not(":last").append(" ").end().text() + '</span>');
    $(alignment).find('.rel' + relId).removeClass('hidden').parent().parent().removeClass('hidden');
    
    // remove selection ids
    $(alignment).find('#selection').removeAttr('id');

    // set relation slot as occupied; find next slot;
    $(alignment)[0].relations[relId] = 1;
    $(alignment)[0].relId = jQuery.inArray(0,$(alignment)[0].relations);  // next unused slot
    redrawLines();

    // if more then three relation exists, hide explanation field
    var relCount = 0;
    for(i in $(alignment)[0].relations) {
      if($(alignment)[0].relations[i] == 1) {
        relCount++;
      }
      if(relCount > 2) {
        $(alignment).find('.relTitle').show();
        $(alignment).find('#noSelection').hide();
        $(alignment).find('.notPossible').hide();
        break;
      }
    }
  }
    
  // highlighting triggers
  $(".passage span:not(#selection)").mousedown(function(e) {
    highlightTerm($(this).parents('.passage'), $(e.target));
  }).mouseup(function() {
    $('span').unbind('mouseover');
    if($(this).parents('.alignment').find('#passage1 #selection').length && $(this).parents('.alignment').find('#passage2 #selection').length) {
      endSelection($(this).parents('.alignment'));
    }
  });
  
});