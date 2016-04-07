var React = require('react');
var Bootstrap = require('react-bootstrap');
var JobVizHeader = require('./Viz/JobVizHeader');
var UnitsChart = require('./Viz/UnitsChart');
var WorkersChart = require('./Viz/WorkersChart');
var AnnotationsChart = require('./Viz/AnnotationsChart');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var Button = Bootstrap.Button;

var JobViz = React.createClass({displayName: "JobViz",

  getInitialState: function(){
    return {
      top: UnitsChart,
      left: WorkersChart,
      right : AnnotationsChart
      }
  },

  componentDidMount: function(){
    //set up job
  },

  swapLeft: function(){
    this.setState({
      top: this.state.left,
      left: this.state.top
    })
  },

  swapRight: function(){
    this.setState({
      top: this.state.right,
      right: this.state.top
    })
  },

  render: function(){
    return (
      React.createElement("div", null, 
        React.createElement(Row, null, 
          React.createElement(JobVizHeader, {job: this.props.job})
        ), 
        React.createElement(Row, {className: "top-viz"}, 
          React.createElement(this.state.top, {job: this.props.job, select: this.props.select, sortBy: this.props.sortBy, height: 0.35})
        ), 
        React.createElement(Row, null, 
          React.createElement(Col, {xs: 6}, 
            React.createElement(Button, {onClick: this.swapLeft}, " ", React.createElement("i", {className: "fa fa-arrow-up"}), React.createElement("i", {className: "fa fa-arrow-down"}), " ")
          ), 
          React.createElement(Col, {xs: 6}, 
            React.createElement(Button, {className: 'pull-right', onClick: this.swapRight}, " ", React.createElement("i", {className: "fa fa-arrow-up"}), React.createElement("i", {className: "fa fa-arrow-down"}), "  ")
          )
        ), 
        React.createElement(Row, {className: "bottom-viz"}, 
          React.createElement(Col, {xs: 6}, 
            React.createElement(this.state.left, {job: this.props.job, select: this.props.select, sortBy: this.props.sortBy, height: 0.25})
          ), 

          React.createElement(Col, {xs: 6}, 
            React.createElement(this.state.right, {job: this.props.job, select: this.props.select, sortBy: this.props.sortBy, height: 0.25})
          )

        )
      )
      )

  }

});


module.exports = JobViz