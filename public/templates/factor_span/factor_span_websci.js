require(['jquery-noconflict'], function(jQuery) {
    
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;
  
  selectedIds1 = new Array();
  selectedIds2 = new Array();
  selectedConfirmIds1 = new Array();
  selectedConfirmIds2 = new Array();
  sentence1 = document.getElementsByClassName("word_split1");
  sentence2 = document.getElementsByClassName("word_split2");
  confirmFirstFactor = document.getElementsByClassName('confirmFirstFactor validates-required confirmFirstFactor validates');
  confirmSecondFactor = document.getElementsByClassName('confirmSecondFactor validates-required confirmSecondFactor validates');
  chooseFirstFactor = document.getElementsByClassName('firstfactor validates-required firstFactor validates');
  chooseSecondFactor = document.getElementsByClassName('secondfactor validates-required secondFactor validates');
  firstSentence = document.getElementsByClassName('sentenceFirstFactor validates-required sentenceFirstFactor validates');
  secondSentence = document.getElementsByClassName('sentenceSecondFactor validates-required sentenceSecondFactor validates');
  radioText1 = document.getElementsByClassName('question1 validates-required question1 validates');
  radioText2 = document.getElementsByClassName('question2 validates-required question2 validates');
  hiddenFieldId1 = document.getElementsByClassName('wordid1');
  hiddenFieldId2 = document.getElementsByClassName('wordid2');
  hiddenFieldFactor1 = document.getElementsByClassName('factor1');
  hiddenFieldFactor2 = document.getElementsByClassName('factor2');
  allIds1 = document.getElementsByClassName('saveselectionids1');
  allIds2 = document.getElementsByClassName('saveselectionids2');
  confirmIds1 = document.getElementsByClassName('confirmids1');
  confirmIds2 = document.getElementsByClassName('confirmids2');
  sentenceText = document.getElementsByClassName('sentence');
  checkboxDoneFirst = document.getElementsByClassName('doneFirst validates-required doneFirst validates');
  b1 = document.getElementsByClassName('b1');
  b2 = document.getElementsByClassName('b2');
  noWordsFactor1 = 0;
  noWordsFactor2 = 0;
     
  Array.prototype.remove = function(x) { 
    for(i in this){
      if(this[i].toString() == x.toString()){
        this.splice(i,1);
      }
    }
  }
    
  Array.prototype.clear = function() {
    this.splice(0, this.length);
  };
  
Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] == obj) {
            return true;
        }
    }
    return false;
}
  
// this code dynamically reformats the rows' labels to accomodate the quantity of text rendered in the rows' data cells
  $(document).ready(function(){
    chooseFirstFactor[0].readOnly = true;
    chooseSecondFactor[0].readOnly = true;
    confirmFirstFactor[0].readOnly = true;
    confirmSecondFactor[0].readOnly = true;
    noWordsFactor1 = hiddenFieldFactor1[0].value.split(/-| /).length;
    termsInFactor1 = hiddenFieldFactor1[0].value.split(/-| /);
    noWordsFactor2 = hiddenFieldFactor2[0].value.split(/-| /).length;
    termsInFactor2 = hiddenFieldFactor2[0].value.split(/-| /);
    
    index1 = new Array();
    index1.push(parseInt(b1[0].value));
    if (noWordsFactor1 > 1) {
      for (i = 1; i < noWordsFactor1; i ++) {
        index1.push(parseInt(parseInt(index1[i - 1])) + termsInFactor1[i - 1].length + 1);
      }
    }
 //   alert(termsInFactor2);
    index2 = new Array();
    index2.push(parseInt(b2[0].value));
    if (noWordsFactor2 > 1) {
      for (i = 1; i < noWordsFactor2; i ++) {
  //      alert(parseInt(parseInt(index2[i - 1])) + termsInFactor2[i - 1].length + 1);
        index2.push(parseInt(parseInt(index2[i - 1])) + termsInFactor2[i - 1].length + 1);
      }
    }
   
    words = $(".word_split1").text().split(" ");
    $(".word_split1").empty();
    $.each(words, function(i, v) {
      if (!v.contains("-")) {
        $(".word_split1").append(" ");
        $(".word_split1").append($("<span class=\"word\">").text(v));
      }
      else {
        words2 = v.split("-");
        $(".word_split1").append(" ");
        for (i = 0; i < words2.length - 1; i ++) {
          $(".word_split1").append($("<span class=\"word\">").text(words2[i]));
          $(".word_split1").append($("<span class=\"word\">").text("-"));
        }
        $(".word_split1").append($("<span class=\"word\">").text(words2[words2.length-1]));
      }
    });

    var startOffset = 0;
    w = $(".word_split1").find("span").each(function() {
      var i = sentenceText[0].value.indexOf($(this).text(), startOffset);
      $(this).attr("id", "one" + i);
      for (j = 0; j < index1.length; j ++) {
        if (i == index1[j]) {
          hiddenFieldId1[0].value += i + " ";
          $(this).css( "background-color","lime" );  
        }
      }
      startOffset += $(this).text().length + 1;
    });
    
    words22 = $(".word_split2").text().split(" ");
    $(".word_split2").empty();
    $.each(words22, function(i, v) {
      if (!v.contains("-")) {
        $(".word_split2").append(" ");
        $(".word_split2").append($("<span class=\"word\">").text(v.substring(0, v.length)));
      }
      else {
        words2 = v.split("-");
        $(".word_split2").append(" ");
        for (i = 0; i < words2.length - 1; i ++) {
          $(".word_split2").append($("<span class=\"word\">").text(words2[i]));
          $(".word_split2").append($("<span class=\"word\">").text("-"));
        }
        $(".word_split2").append($("<span class=\"word\">").text(words2[words2.length-1]));
      }
    });
    
    var startOffset2 = 0;
    w = $(".word_split2").find("span").each(function() {
      var i = sentenceText[0].value.indexOf($(this).text(), startOffset2);
      $(this).attr("id", "two" + i);
      for (j = 0; j < index2.length; j ++) {
        if (i == index2[j]) {
          hiddenFieldId2[0].value += i + " ";
          $(this).css( "background-color","lime" );  
        }
      }
      startOffset2 += $(this).text().length + 1;
//      alert(startOffset2);
    });    
  });
  
  sentence1[0].onclick = function( event ) {
    if(event.target.nodeName == "SPAN" && event.target.id.substring(0,3) == "one") {
      if(radioText1[0].checked) {
        if (selectedConfirmIds1.contains(event.target.id.slice(3))) {
        //if ($.inArray(event.target.id.slice(3), selectedConfirmIds1)) {
          if (event.target.style.backgroundColor != "lime") {
            event.target.removeAttribute('style');
          }  
          selectedConfirmIds1.remove(event.target.id.slice(3));
          confirmIds1[0].value = printArray(selectedConfirmIds1);
          selection1 = updateHighlightedWords(selectedConfirmIds1, "one");
          confirmFirstFactor[0].value = selection1;
        }
        else{ 
          selectedConfirmIds1.push(parseInt(event.target.id.slice(3)));
          confirmIds1[0].value = printArray(selectedConfirmIds1);
          if (event.target.style.backgroundColor != "lime") {
            event.target.style.backgroundColor = "yellow";
          }
          selection1 = updateHighlightedWords(selectedConfirmIds1, "one");
          confirmFirstFactor[0].value = selection1;
        } 
      }
      if(radioText1[1].checked) {
        if (selectedIds1.contains(event.target.id.slice(3))) {
          if (event.target.style.backgroundColor != "lime") {
            event.target.removeAttribute('style');
          }  
          selectedIds1.remove(event.target.id.slice(3));
          allIds1[0].value = printArray(selectedIds1);
          selection1 = updateHighlightedWords(selectedIds1, "one");
          chooseFirstFactor[0].value = selection1;
        }
        else{ 
          selectedIds1.push(parseInt(event.target.id.slice(3)));
          allIds1[0].value = printArray(selectedIds1);
          if (event.target.style.backgroundColor != "lime") {
            event.target.style.backgroundColor = "yellow";
          }
          selection1 = updateHighlightedWords(selectedIds1, "one");
          chooseFirstFactor[0].value = selection1;
        } 
      }
    }
  } 
  
  sentence2[0].onclick = function( event ) {
    if(event.target.nodeName == "SPAN" && event.target.id.substring(0,3) == "two") {
      if(radioText2[0].checked) {
        if (selectedConfirmIds2.contains(event.target.id.slice(3))) {
        //if ($.inArray(event.target.id.slice(3), selectedConfirmIds2)) {
          if (event.target.style.backgroundColor != "lime") {
            event.target.removeAttribute('style');
          }  
          selectedConfirmIds2.remove(event.target.id.slice(3));
          confirmIds2[0].value = printArray(selectedConfirmIds2);
          selection1 = updateHighlightedWords(selectedConfirmIds2, "one");
          confirmSecondFactor[0].value = selection1;
        }
        else{ 
          selectedConfirmIds2.push(parseInt(event.target.id.slice(3)));
          confirmIds2[0].value = printArray(selectedConfirmIds2);
          if (event.target.style.backgroundColor != "lime") {
            event.target.style.backgroundColor = "yellow";
          }
          selection1 = updateHighlightedWords(selectedConfirmIds2, "two");
          confirmSecondFactor[0].value = selection1;
        } 
      }
      if(radioText2[1].checked) {
        if (selectedIds2.contains(event.target.id.slice(3))) {
          if (event.target.style.backgroundColor != "lime") {
            event.target.removeAttribute('style');
          }  
          selectedIds2.remove(event.target.id.slice(3));
          allIds2[0].value = printArray(selectedIds2);
          selection1 = updateHighlightedWords(selectedIds2, "two");
          chooseSecondFactor[0].value = selection1;
        }
        else{ 
          selectedIds2.push(parseInt(event.target.id.slice(3)));
          allIds2[0].value = printArray(selectedIds2);
          if (event.target.style.backgroundColor != "lime") {
            event.target.style.backgroundColor = "yellow";
          }
          selection1 = updateHighlightedWords(selectedIds2, "two");
          chooseSecondFactor[0].value = selection1;
        } 
      }
    }
  }  
           
  radioText1[0].onclick=function(ev) {
    $('span').each(function(){
      if($(this ).css( "background-color") == "rgb(255, 255, 0)" ){
        if ($(this ).attr( "id").contains("one")) {
            $(this ).removeAttr("style");
        }
      }
    });
    selectedIds1.clear();
    allIds1[0].value = "";
  }
    
  radioText2[0].onclick=function(ev) {
    $('span').each(function(){
      if($(this ).css( "background-color") == "rgb(255, 255, 0)" ){
        if ($(this ).attr( "id").contains("two")) {
          $(this ).removeAttr("style");
        }
      }
    });
    selectedIds2.clear();
    allIds2[0].value = "";
  }

  radioText1[1].onclick=function(ev) {
    $('span').each(function(){
      if($(this ).css( "background-color") == "rgb(255, 255, 0)" ){
        if ($(this ).attr( "id").contains("one")) {
          $(this ).removeAttr("style");
        }
      }
    });
    selectedConfirmIds1.clear();
    confirmIds1[0].value = "";
  }
     
  radioText2[1].onclick=function(ev) {
    $('span').each(function(){
      if($(this ).css( "background-color") == "rgb(255, 255, 0)" ){
        if ($(this ).attr( "id").contains("two")) {
          $(this ).removeAttr("style");
        }
      }
    });
    selectedConfirmIds2.clear();
    confirmIds2[0].value = "";
  }

    firstSentence[0].onblur=function(ev){
      ok = 0;
      
      sent = firstSentence[0].value;
      sentWords = sent.split(" ");
      if (sentWords.length < 4) {
        firstSentence[0].value = "";
      }
      if (!sent.contains(hiddenFieldFactor1[0].value)) {
        firstSentence[0].value = "";
      }
      if (sentenceText[0].value.contains(sent)) {
        firstSentence[0].value = "";
      }
    }
      
      
      secondSentence[0].onblur=function(ev){
        ok = 0;
        
        sent = secondSentence[0].value;
        sentWords = sent.split(" ");
        if (sentWords.length < 4) {
          secondSentence[0].value = "";
        }
        if (!sent.contains(hiddenFieldFactor2[0].value)) {
          secondSentence[0].value = "";
        }
        if (sentenceText[0].value.contains(sent)) {
          secondSentence[0].value = "";
        }   
        if (firstSentence[0].value != "") {
          sent2 = firstSentence[0].value;
          sent2Words = sent2.split(" ");
          var results = new Array();
          for (i = 0; i < sentWords.length; i++) {
            if (sent2Words.indexOf(sentWords[i]) !== -1) {
              results.push(sentWords[i]);
            }
          }
          if (results.length > (sentWords.length / 2) ) {
            secondSentence[0].value = "";
          }
        }
      }
    
    function updateHighlightedWords(arrayId, indexSent) {
      arrayId.sort(function(a, b) {
        if (isNaN(a) || isNaN(b)) {
          if (a > b) return 1;
          else return -1;
        }
        return a - b;
      });
      var selection2 = "";
      for (var i = 0; i < arrayId.length; i ++) {
        var num = parseInt(arrayId[i]);
        var n = num.toString();
        if (indexSent == "one") {
          selection2 += document.getElementById("one" + n).innerHTML + " ";
        }
        if (indexSent == "two") {
          selection2 += document.getElementById("two" + n).innerHTML + " ";
        }
      }
      return selection2;
      }
  
  function printArray(array) {
    retValue = "";
    for (i = 0; i < array.length; i ++) {
      retValue += array[i] + "-";
    } 
    if (array.length != 0) {
      retValue = retValue.slice(0, -1); 
    }
    return retValue;
  }
  
});


