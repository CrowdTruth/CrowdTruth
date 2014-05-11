require(['jquery-noconflict'], function(jQuery) {
    
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;

  eventsInput = document.getElementsByClassName('events validates-required events validates');
  addedEventsTextarea = document.getElementsByClassName('addedevents validates-required addedevents validates');
  hiddenTimes = document.getElementsByClassName("times"); 
  confirmed = document.getElementsByClassName("confirmed"); 
  //confirmedevents = document.getElementsByClassName("confirmedevents validates-required confirmedevents validates");
  var index = 0;
  var newLine = false;
  var otherChar = false;
  var consecutiveEnter = 0;
  var radio = [];
  
  $(document).ready(function(){
    eventsInput[0].readOnly = true;
    //  confirmedevents[0].readOnly = true;
  });
    
  addedEventsTextarea[0].onkeydown=function(e) {
    //alert($('.span6').find('video').get(0).currentTime);
    var keyValue = e.which;
    var written = addedEventsTextarea[0].value;
    arrayOfLines = written.split("\n");
    noEvents = arrayOfLines.length;
    if (keyValue == "13") {
      newLine = true;
      otherChar = false;
      consecutiveEnter ++;
      if (consecutiveEnter >= 2) {
        var written = addedEventsTextarea[0].value;
        addedEventsTextarea[0].value = written.substring(written.indexOf("\n") + 1);
      }
      else {
        $('.times').val($('.times').val() + $('.span6').find('video').get(0).currentTime.toFixed(2) + "---");
      }
      if (arrayOfLines[noEvents - 1] != "") {
        eventsInput[0].value += arrayOfLines[arrayOfLines.length - 1] + ";  ";
        confirmed[0].value += arrayOfLines[arrayOfLines.length - 1] + "---confirm ### ";
        var $input = $("<input type='text' readonly/>").attr("class", "typedvalue").attr("id", "typedvalue"+index).attr("value", arrayOfLines[arrayOfLines.length - 1]).attr("title", arrayOfLines[arrayOfLines.length - 1]);
        //$("#conf").append($input);
        $('#results').append('<tr><td id="first' + index + '" width="65%">' + '</td><td id="opt' + index + '"></td></tr>');
        $('#first' + index).append($input);
        var val = 0;
        var radioBtn;
        for(i=0; i<2; i++)
        {
          if (val == 0) {
            radioBtn = $('<input type="radio" class="radioopt" name="rbtnCount' + index + '" value="confirm" checked> Confirm </input>');
        radioBtn.appendTo('#opt' + index);
            val++;
            
          }
          else {
            radioBtn = $('<input type="radio" class="radioopt" name="rbtnCount' + index + '" value="delete"> Delete </input>');
            val = 0;
          }
          radioBtn.appendTo('#opt' + index);
        }
        index ++;
        radio = [];
      elem = document.getElementsByTagName('input');
      for (var i = 0; i < elem.length; ++i) {
        if (elem[i].type == 'radio') {
          radio.push(elem[i]);
        }
      }
      }
    }
    else {
      otherChar = true;
      consecutiveEnter = 0;
    }
    
    if (newLine == true && otherChar == true) {  
      newLine = false;
      var written = addedEventsTextarea[0].value;
      addedEventsTextarea[0].value = written.substring(written.indexOf("\n") + 1);
    }
//    alert(radio);
  }
    
$('body').on('click', 'input.radioopt', function() {
//  alert(radio[0].checked);
//  alert(radio.length);
  var allString = confirmed[0].value;
  var values = confirmed[0].value.split(' ### ');
  //alert(confirmed[0].value.split(' ### '));
  for (i = 0; i < values.length - 1; i ++) {
    var newSplit = values[i].split('---');
//    alert(newSplit[1]);
    if (radio[i * 2].checked && newSplit[1] == "confirm") {
    //   alert("1");
      confirmed[0].value = allString.replace(values[i], newSplit[0] + "---confirm");
    }
    else if (radio[i * 2].checked && newSplit[1] == "delete") {
    //  alert("2");
       confirmed[0].value = allString.replace(values[i], newSplit[0] + "---confirm");
    }
    else if (radio[i * 2 + 1].checked && newSplit[1] == "confirm") {
    //  alert("3");
      confirmed[0].value = allString.replace(values[i], newSplit[0] + "---delete");
    }
    else if (radio[i * 2 + 1].checked && newSplit[1] == "delete") {
    //    alert("4");
      confirmed[0].value = allString.replace(values[i], newSplit[0] + "---delete");
    }
  }
});
     
});
