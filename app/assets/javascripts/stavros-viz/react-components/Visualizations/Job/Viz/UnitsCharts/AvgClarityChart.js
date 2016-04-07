var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var Button = Bootstrap.Button;
var Col = Bootstrap.Col;

var AvgClarityChart = React.createClass({
  getInitialState: function(){
    return {
      height: window.innerHeight* this.props.height*0.85
    }
  },


  getSelected: function(){
  },

  componentDidMount: function(){
    window.addEventListener('resize', this.handleResize);
    this.props.sortBy('units','avg_clarity', true);
    this.forceUpdate();
  },

  componentWillUnmount: function(){
    window.removeEventListener('resize', this.handleResize);
  },

  handleResize: function(){
    this.setState({
      height: window.innerHeight* this.props.height *0.85
     });
  },

  select: function(ids, ctrlKey){
    this.props.select('units', ids, ctrlKey);
  },



  options: function(){

    var options = {
      chart: {
        height: this.state.height,
        type: 'column'
      },

      plotOptions: {
        series: {
          allowPointSelect: true
        }
      },

      title:{
        text: 'Average unit Clarity'
      },


      xAxis: {
        categories: []
      }, 

      yAxis: {
        min: 0,
        max: 1,
        title: {
          text: 'Average Clarity'
        }
      },
      series: [{
        cursor: 'pointer',
        point: {
          events: {
            click: function(chart){
              return function(e){

                chart.select(this.units, e.originalEvent.ctrlKey);
                //this.update({color:'red'}, true, false);
              }
            }(this)
          }
        },

        name: 'Avg Clarity',
        data: []
      }]
    }

    this.props.job.units.map(function(unit, index){
      options.xAxis.categories.push(unit._id.split('/').pop());
      options.series[0].data.push({
        x: index,
        y: unit.avg_clarity,
        units:[unit._id]
      });
    })
    return options;
  },

  setSort: function(asc){
    this.props.sortBy('units','avg_clarity', asc);
    this.forceUpdate();
  },

  render: function(){
    return (
      React.createElement("div", {className: 'chart'}, 
        React.createElement(HighCharts, {config: this.options(), ref: "chart"}), 
        React.createElement(Row, null, 
          React.createElement(Col, {xs: 4}, " ", React.createElement("div", {className: "pull-right"}, "Sort:"), " "), 
          React.createElement(Col, {xs: 4}, React.createElement(Button, {onClick: this.setSort.bind(this,true)}, " Asc "), " "), 
          React.createElement(Col, {xs: 4}, React.createElement(Button, {onClick: this.setSort.bind(this,false)}, "Desc"))
        )
      )
    )
  }
});


module.exports = AvgClarityChart;