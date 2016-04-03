var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var _ = require('underscore');
var Input = Bootstrap.Input;
var Button = Bootstrap.Button;

var UnitsTimeToComplete = React.createClass({
  getInitialState: function(){
    return {
      height: window.innerHeight* this.props.height*0.8,
      timeRange: 5,
      invalidInput: false
    }
  },

  getSelected: function(){
  },

  componentDidMount: function(){
    window.addEventListener('resize', this.handleResize);
  },

  handleResize: function(){
    this.setState({height: window.innerHeight* this.props.height });
  },

  componentWillUnmount: function(){
    window.removeEventListener('resize', this.handleResize);
  },

  select: function(ids, ctrlKey){
    this.props.select('units', ids, ctrlKey, false);
  },

  options: function(){
    var options = {
      chart: {
        height: this.state.height,
        type: 'column'
      },

      title:{
        text: 'Units Grouped by Time to Complete'
      },

      plotOptions: {
        series: {
          allowPointSelect: true
        }
      },

      xAxis: {
        categories: []
      }, 

      yAxis: {
        min: 0,
        title: {
          text: 'Number of units'
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

        name: 'Job Units',
        data: []
      }]
    }

    var avgTimes = [];
    var maxTime = 0;
    this.props.job.units.map(function(unit){
      var unitAnnot = _.where(this.props.job.workerunits, {unit_id: unit._id});
      var sumtime = 0;
      var count = 0;
      unitAnnot.map(function(annot){
        sumtime += (new Date(annot['submitTime']) - new Date(annot['acceptTime'])) / 1000;
        count ++;
      })
      avgTime = sumtime/count;
      avgTimes.push({
        id: unit._id,
        value: avgTime
      });
      if (avgTime > maxTime){
        maxTime = avgTime;
      }

    }.bind(this))

    maxTime = Math.ceil(maxTime/10) * 10;

    for(var i = 0; i< maxTime; i+=this.state.timeRange){
      options.xAxis.categories.push( i + 's-' + (i+this.state.timeRange)+'s');
      options.series[0].data.push({
        x: i/this.state.timeRange,
        y: 0,
        units: []
      });
    }

    avgTimes.map(function(time){
      options.series[0].data[Math.floor(time.value /this.state.timeRange)].y ++;
      options.series[0].data[Math.floor(time.value /this.state.timeRange)].units.push(time.id)
    }.bind(this))

    return options;
  },

  setTimeRange: function(){
    var newRange = this.refs.timeRangeInput.getValue();
    if (!isNaN(newRange)){
      this.setState({timeRange:parseInt(newRange)});
      this.setState({invalidInput: false});
    }else{
      this.setState({invalidInput: true});
    }
  },

  render: function(){
    return (
      <div className={'chart'}>
        <HighCharts config={this.options()} ref='chart' />
        <Row>
          <Col xs={4}> <div className="pull-right">Set Time Range:</div> </Col>
          <Col xs={4}><Input type='text' ref="timeRangeInput" bsStyle={this.state.invalidInput?"error":null} /> </Col> 
          <Col xs={4}><Button onClick={this.setTimeRange}>OK</Button></Col>
        </Row>
      </div>
      )
  }
});

module.exports = UnitsTimeToComplete;