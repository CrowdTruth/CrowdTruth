var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var _ = require('underscore');
var Input = Bootstrap.Input;
var Button = Bootstrap.Button;


var UnitsGroupedByAvgClarity = React.createClass({

  getInitialState: function(){
    return {
      height: window.innerHeight* this.props.height *0.8,
      clarityRange: 5,
      invalidInput: false
    }
  },

  getSelected: function(){
  },

  componentDidMount: function(){
    window.addEventListener('resize', this.handleResize);
  },

  componentWillUnmount: function(){
    window.removeEventListener('resize', this.handleResize);
  },

  handleResize: function(){
    this.setState({height: window.innerHeight* this.props.height });
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

      title:{
        text: 'Units Grouped by Clarity'
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
          text: 'Number Of units'
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

        name: 'Units',
        data: []
      }]
    }

    for(var i=0; i<Math.ceil(100/this.state.clarityRange) ; i+=1){
      options.xAxis.categories.push( i*this.state.clarityRange + '%-' + ((i+1)*this.state.clarityRange)+'%');
      options.series[0].data.push({
        x: i,
        y: 0,
        units: []
      })
    }

    this.props.job.units.map(function(unit){
      var index = Math.floor(unit.avg_clarity / (this.state.clarityRange/100));
      index = unit.avg_clarity == 1 ? index - 1: index;
      options.series[0].data[index].y ++;
      options.series[0].data[index].units.push(unit._id);
    }.bind(this))

    return options;
  },

  setClarityRange: function(){
    var newRange = this.refs.timeRangeInput.getValue();
    if (!isNaN(newRange) && parseInt(newRange)<100){
      this.setState({clarityRange:parseInt(newRange)});
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
          <Col xs={4}> <div className="pull-right">Set Clarity Range:</div> </Col>
          <Col xs={4}><Input type='text' ref="timeRangeInput" bsStyle={this.state.invalidInput?"error":null} /> </Col> 
          <Col xs={4}><Button onClick={this.setClarityRange}>OK</Button></Col>
        </Row>
      </div>
      )
  }
});

module.exports = UnitsGroupedByAvgClarity;