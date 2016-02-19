require(['jquery-noconflict'], function(jQuery) {
  
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;

  var highlightedSpans = $('.highlights').html();
  console.log(highlightedSpans);
  var highlightedSpansList = [];
  $('.highlights').html("");

  var highlightedSpansArray = highlightedSpans.split('__##__');  
  console.log(highlightedSpansArray);
  for(i = 0; i < highlightedSpansArray.length; i++) {
    highlightedSpansArray[i] = highlightedSpansArray[i].replace(/_###_/g,'|');
    console.log(highlightedSpansArray[i]);
    var tmp = highlightedSpansArray[i].split('|');
    var tempVal = "p" + tmp[1] + "-" + tmp[2];
    highlightedSpansList.push([parseInt(tmp[1].replace(/ /g,'')),'<span class="term event' + i + 'term" id="' + tempVal + '" range="' + tmp[1] + '-' + tmp[2] + '">']);
    highlightedSpansList.push([parseInt(tmp[2].replace(/ /g,'')),'</span>']);
    
    $('.ev' + i + 'a').val(tmp[0]);
    $('#opinion' + i + ' .term').text(tmp[0]);
    $('#opinion' + i).slideDown();
  }

  function sort_by_column(a,b) {
    return ((a[0] > b[0]) ? -1 : ((a[0] < b[0]) ? 1 : 0));
  }
  highlightedSpansList.sort(sort_by_column);
 

  $(".eventextraction #passage").html(function(i, val) {
    var output = val;
    for(i = 0; i < highlightedSpansList.length; i++) {
      output = [output.slice(0, highlightedSpansList[i][0]), highlightedSpansList[i][1], output.slice(highlightedSpansList[i][0])].join('');
    }
    return output;
  });
});
