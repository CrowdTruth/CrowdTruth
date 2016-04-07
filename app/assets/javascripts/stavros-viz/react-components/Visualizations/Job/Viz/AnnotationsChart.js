var React = require('react');
var Bootstrap = require('react-bootstrap');
var Chart = require('./AnnotationsCharts/UnitsHeatmaps');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var Button = Bootstrap.Button;
var Input = Bootstrap.Input;
var _ = require('underscore');


var AnnotationsChart = React.createClass({displayName: "AnnotationsChart",
  getInitialState: function(){
    return {
      withSpam: true,
      unitIndex: 0,
      type: null
    }
  },

  getTypes: function(){
    var types = [];
    var unit = this.props.job.selected.units[this.state.unitIndex]
  
    var res = this.props.job.jobInfo.results.withSpam[unit];
    for (type in res){
      types.push(type);
    }


    return types;
  },

  componentWillReceiveProps: function(nextProps){
    if (nextProps !== this.props){
      this.setState({
        unitIndex:0,
        type: null

      })
    }
  },

  changeChart: function(next){
    if (next && this.state.index<(this.state.types.length-1)){
      this.setState({index: (this.state.index+1)})
    }else if(this.state.index>0){
      this.setState({index: this.state.index -1})
    }

  },

  changeSpam: function(){
    this.setState({withSpam: !this.state.withSpam})
  },


  handleSelection: function(){
    var type = this.refs.typeInput.getValue();
    var unitIndex = this.refs.unitInput.getValue();
    this.setState({
      type: type,
      unitIndex: unitIndex
    })
  },

  render: function(){

    var res = this.props.job.jobInfo.results;
    var selectedUnit = this.props.job.selected.units[this.state.unitIndex];

    var items = [];

    if (selectedUnit){

      var types = this.getTypes();
      res = this.state.withSpam? res.withSpam : res.withoutSpam;
      res = res[this.props.job.selected.units[this.state.unitIndex]];
      res = this.state.type? res[this.state.type]: res[types[0]];
      for (name in res){
        items.push({name: name , value: res[name]});
      }
      items = _.sortBy(items, 'name');
    }
   
    var activeChart;
    if (items.length > 0){
      activeChart = (
        React.createElement(Row, null, 
          React.createElement(Row, null, 

            React.createElement(Col, {xs: 4, style: {"paddingLeft": "40px"}}, 
              React.createElement(Input, {type: "select", ref: "unitInput", label: "Select unit ID", onClick: this.handleSelection}, 
              
                this.props.job.selected.units.map(function(unit, index){
                  return (
                      React.createElement("option", {value: index, key: index}, unit.split('/').pop())
                    )
                })
              
              )
            ), 

            React.createElement(Col, {xs: 4}, 
              React.createElement(Input, {type: "select", ref: "typeInput", label: "Select type", onClick: this.handleSelection}, 
              
                types.map(function(type, index){
                  return (
                      React.createElement("option", {value: type, key: index}, type)
                    )
                })
              
              )

            ), 

            React.createElement(Col, {xs: 4}, 
              React.createElement(Row, null, 
                React.createElement(Input, {type: "checkbox", label: "with Spam", defaultChecked: this.state.withSpam, onClick: this.changeSpam})
              )
            )
           
          ), 
          
          React.createElement(Chart, {items: items, _id: selectedUnit._id, height: this.props.height * 0.75})
        )
        )

    }else{
      var height = window.innerHeight* this.props.height*1.18+'px'
      activeChart = React.createElement(Row, null, " ", React.createElement("h3", {style: {height:height}, className: "text-center"}, " Select Units from Unit Charts or Table"))
    }
   

    

    return (
      React.createElement("div", {className: "bs-callout bs-callout-primary white"}, 
        React.createElement("h3", {className: 'text-center'}, "Annotations Heatmap"), 
      
        
          activeChart
        
       
          
      )
      )
  }


});


module.exports = AnnotationsChart;