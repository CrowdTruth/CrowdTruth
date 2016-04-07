var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var Button = Bootstrap.Button;
var Charts = [ 
    //require('./UnitsCharts/RelationScores'),
    {
      chart: require('./UnitsCharts/AvgClarityChart'),
      desc: 'Avg Clarity'
    },
    {
      chart: require('./UnitsCharts/UnitsGroupedByAvgClarity'),
      desc: 'Avg Clarity Histogram'
    },
    {
      chart: require('./UnitsCharts/UnitsTimeToComplete'),
      desc: 'Time to complete'
    }
  ]

var UnitsChart = React.createClass({displayName: "UnitsChart",
  getInitialState: function(){
    return {
      index: 0
    }
  },

  changeChart: function(next){
    var index = this.state.index;
    if (next){
      if (index < Charts.length - 1){
        this.setState({index: index+1});
      }
    }else{
      if (index > 0){
        this.setState({index : index - 1})
      }
    }
  },

  shouldComponentUpdate: function(nextProps, nextState){
    return (nextState!==this.state)||(this.props.job.units !== nextProps.job.units) ;
  },

  render: function(){
    var index = this.state.index;
    var Chart = Charts[index].chart;
    var rightActive = (index<Charts.length - 1);
    var leftActive = (index>0);
    var rightBtnLabel = rightActive?Charts[index+1].desc:'No more charts';
    var leftBtnLabel = leftActive?Charts[index-1].desc:'No more charts';

    return(
        React.createElement("div", {className: 'bs-callout bs-callout-success white'}, 
          React.createElement(Row, null, 
            React.createElement(Col, {xs: 4}, 
              React.createElement(Button, {bsStyle: "primary", onClick: this.changeChart.bind(null,false), disabled: !leftActive}, leftBtnLabel)
            ), 
            React.createElement(Col, {xs: 4}, 
              React.createElement("h3", {className: 'text-center'}, "Units")
            ), 
            React.createElement(Col, {xs: 4}, 
              React.createElement(Button, {className: "pull-right", onClick: this.changeChart.bind(null,true), bsStyle: "primary", disabled: !rightActive}, rightBtnLabel)
            )
          ), 
          React.createElement(Row, null, 
            React.createElement(Chart, {job: this.props.job, height: this.props.height, select: this.props.select, sortBy: this.props.sortBy})
          )
        )
        )
  }
});

module.exports = UnitsChart;