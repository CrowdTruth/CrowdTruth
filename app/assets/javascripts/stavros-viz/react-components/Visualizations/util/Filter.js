var _ = require('underscore');

function Filter(data, fields){

  this.data = data;
  

  if (fields){
    this.fields = fields;
  }else{
    this.fields = [];
    for (field in data[0]){
      this.fields.push(field);
    }
  }

  
  this.filterByField = function(field, value){
    return _.filter(this.data, function(document){
      return ((document[field]).toString()).toLowerCase().indexOf(value.toLowerCase()) > -1;
    })
  },

  this.filter =  function(value){
    return _.filter(this.data, function(document){
      var valid = false;
      this.fields.map(function(field){
        valid = valid||(((document[field]).toString()).toLowerCase().indexOf(value.toLowerCase()) > -1);
      })

      return valid;

    }.bind(this))
  }



}


module.exports = Filter;