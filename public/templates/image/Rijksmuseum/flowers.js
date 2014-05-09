require(['jquery-noconflict'], function(jQuery) {    
  //Ensure MooTools is where it must be
  Window.implement('$', function(el, nc){
    return document.id(el, nc, this.document);
  });
  var $ = window.jQuery;
  // Use `$` in here.
  
  //initialize variables
  var timers  = new Object();  
  var timings = new Object();
  var startTime = new Date().getTime();
  
  //logic to store when the image is clicked
  $("#imageLink").click(function(){
    $(this).parents(".content").find(".imageclicked").val("true");
  });  
  
  //start timer logic for onfocus for the text field
  $("input[type='text'],textarea").focus(function(event){
    //full unique name: job[field]
    var name = $(event.currentTarget).attr('name');   
    timers[name] = new Date().getTime();
  });
  
  //stop timer logic for blur for the text field
  $("input[type='text'],textarea").blur(function(event){
    //the unique name of the fiels job[field]
    var name = $(event.currentTarget).attr('name');
    //current value of the field
    var value =  $(event.currentTarget).val();    
    //calculate the ms 
    var ms = new Date().getTime() - timers[name];
    //split the name into job and field
    var job = name.split("[")[0];
    var field = name.split("[")[1].replace("]","");
    //check if the timings info contains the job object
    if(!timings.hasOwnProperty(job)){
      //create the property
      timings[job] = new Object();
    }
    //the job object exists, check if the timing array also exists
    if(timings[job][field] ===undefined){
      //create the array 
      timings[job][field] = new Array();
    }
    //the data to add to the timing array
    var data = {"ms":ms,"tag":value,"startTime":timers[name]};
    timings[job][field].push(data);
    //Stringify and set the timing info
    var t = JSON.stringify(timings);
    $(this).parents(".content").find(".tagtiming").val(t);
   });
  
  //logic for submitting a result
  $("input[type='submit']").click(function(e){
    //if custom  validation fails, do not submit
    if(!validate()){
      e.preventDefault(); 
    }
    
    var endTime = new Date().getTime();
    var totalTime = endTime - startTime;
    $(".starttime").val(startTime);
    $(".endtime").val(endTime);
    $(".totaltime").val(totalTime);
    
    return validate();
  });
  
  //logic for selecting an other checkbox to deselect the other
  $("input[type='checkbox'].other").click(function(){
    //get the two checkboxed and their checked status
    var unable = $(this).parents(".checkboxes").find("input[type='checkbox'][value='unable']");
    var unable_checked = unable.prop("checked");
    var fantasy= $(this).parents(".checkboxes").find("input[type='checkbox'][value='fantasy']");
    var fantasy_checked = fantasy.prop("checked");
    //get the current checked value of the clicked (label or checkbox) 
    var value = $(this).val();
   
    // if both values are checked
    if(unable_checked && fantasy_checked){
      console.log("unable_checked:"+unable_checked+", fantasy_checked:"+fantasy_checked+ ", selected:"+value);
      if(value === "fantasy"){
        //deselect both checkboxes
        $(this).parents(".checkboxes").find("input[type='checkbox']").prop("checked",false);
        unable.click();
        unable.click();
        fantasy.click();
      }
      if(value === "unable"){
        //deselect both checkboxes
        $(this).parents(".checkboxes").find("input[type='checkbox']").prop("checked",false);
        fantasy.click();
        fantasy.click();
        unable.click();
      }
    }
    //if one of the boxes is selected, scroll to top to view the fields again
    if(unable.prop("checked") || fantasy.prop("checked")){
      $(this).parents(".content")[0].scrollIntoView();
    } 
  });
  
  
  /*
  * Show the validation message for the given field. Optional parameter to remove
  * current validation messages (default = true)
  */
  function showValidationMessage(field, message, removeOtherValidation){
    //removeOtherValidation defaults to true;
    removeOtherValidation= (typeof removeOtherValidation=== "undefined") ? true : removeOtherValidation;

     //find the cml_row the field belongs to
    var row = field.parents(".cml_row").first();
    
    //remove previous validation
    if(removeOtherValidation){
      removeValidationMessage(field);
    } 

    //create the outer div
    var outerDiv = $(document.createElement("div"));
    outerDiv.css("margin","0px");
    outerDiv.css("position","relative");
    outerDiv.css("overflow","hidden");
    outerDiv.css("height","41px");
    //create the inner div
    var innerDiv = $(document.createElement("div"));
    innerDiv.addClass("errorMessage");
    innerDiv.css("margin","0px");
    innerDiv.css("overflow","hidden");
    //create the text of the message
    var text = $(document.createElement("p")).append(message);
    //add the text to the inner div
    innerDiv.html(text);
    //add the innerdiv to the outer div
    outerDiv.append(innerDiv);
    //hide the div
    outerDiv.hide();
    //add the outer div to the DOM before the cml_row of the field
    outerDiv.insertBefore(row);
    //show the outer div with a smoots animation
    outerDiv.slideDown();
  }
  
  /*
  * Find the cml_row this field belongs to and removes all validation messages present
  */
  function removeValidationMessage(field){
    //find the cml_row the field belongs to
    var row = field.parents(".cml_row").first();
    //validation is the previous DOM element before the cml_row
    var potentialValidation = row.prev("div");
    //it should contain an element with class "errorMessage"
    var hasError = potentialValidation.find(".errorMessage");
    if(hasError.length>0){
      //hide the validation with a nice animation
      potentialValidation.slideUp();
      //remove the validation from the DOM
      potentialValidation.remove();
      //recurse this method to delete other existing validation for the field
      removeValidationMessage(field);
    } 
  }
  
  /**
  * Validates all the tasks in the job
  */
  function validate(){
    var validates = true;
    var duplicateMessage = "Please enter different names in the fields";
    
    //loop over the tasks
    var tasks =  $(".name1").parents(".customTask");
    tasks.each(function(index){
      //get the values of the name fields
      var name1 = $(this).find(".name1");
      var name2 = $(this).find(".name2");
      var name3 = $(this).find(".name3");
      var n1 = name1.hasClass("has_default") ? "" : name1.val();
      var n2 = name2.hasClass("has_default") ? "" : name2.val();
      var n3 = name3.hasClass("has_default") ? "" : name3.val();
      
      //if name2 is not empty and equals name1
      if(n1.length >0 && n2.length >0 && n1===n2){    
        //validation fails
        validates = false;
        //display a validation message
        showValidationMessage(name2, duplicateMessage );
      }
      else{
        removeValidationMessage(name2);
      }
      //if name3 is not empty and equals name1
      if(n1.length >0 && n3.length >0 && n1===n3){    
        //validation fails
        validates = false;
        //display a validation message
        showValidationMessage(name3, duplicateMessage );
      }
      else{
        removeValidationMessage(name3);
      }
      
      //if name3 is not empty and equals name2
      if(n2.length >0 && n3.length >0 && n2===n3){    
        //validation fails
        validates = false;
        //display a validation message
        showValidationMessage(name3, duplicateMessage );
      }
      else{
        removeValidationMessage(name3); 
      }
    });     
    
    //if validation fails, scroll upwards to the first failed field
    if(!validates){
      $(".errorMessage:visible").first().parents(".border")[0].scrollIntoView();
    }
    
    return validates;
  }
  
  //adding low and high to the ratings
  $(".confidence tr td:first-child").text("Uncertain");
  $(".confidence tr td:last-child").text("Certain");
  
  //only show the comments of the last unit
  $(".jobComments").hide();
  $(".jobComments").last().show();
});
