var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;

var WorkerunitsByHour = React.createClass({displayName: "WorkerunitsByHour",
  getInitialState: function(){
    return {
      height: window.innerHeight* this.props.height
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
    this.setState({
      height: window.innerHeight* this.props.height
     });
  },

  options: function(){
    var options = {
      chart: {
        height: this.state.height,
        type: 'column'
      },

      title:{
        text: 'Worker units hourly distribution Chart'
      },


      tooltip:{
          formatter: function(){
            var s = this.series.name + ': ' + this.y + '<br/>' +
                    Math.floor(this.y / this.point.stackTotal * 100) + '%';

            return s;
          }
      },

      xAxis: {
        categories: []
      }, 

      yAxis: {

        min: 0,
        title: {
            text: 'Total worker units'
        },
        stackLabels: {
            enabled: true,
            style: {
                fontWeight: 'bold',
                color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
            }
        }
      },

      plotOptions: {
        column: {
            stacking: 'normal'
        }
      },

      series: [
      {
        cursor: 'pointer',
        point: {
          events: {
            click: function(chart){
              return function(e){
                chart.props.select('workerunits', this.workerunits, e.originalEvent.ctrlKey, false);
                //this.update({color:'red'}, true, false);
              }
            }(this)
          }
        },
        name: 'spam',
        data: [],
        color: 'red'
      },

      {
        cursor: 'pointer',
        point: {
          events: {
            click: function(chart){
              return function(e){
                chart.props.select('workerunits', this.workerunits, e.originalEvent.ctrlKey, false);
                //this.update({color:'red'}, true, false);
              }
            }(this)
          }
        },
        name: 'non spam',
        data: [],
        color: 'green'
      }]
    }
    var hourLength = 2;
    for(var i = 0; i < (24/hourLength); i++){
      options.xAxis.categories.push(i*hourLength + ':00 to ' + (i+1)*hourLength +':00');
      options.series[0].data.push(
          {
            x: i,
            y: 0,
            workerunits:[]
          }
        );
      options.series[1].data.push({
        x: i,
        y: 0,
        workerunits: []
      });

    }

    this.props.job.workerunits.map(function(workerunit){
      var hour = (new Date(workerunit.acceptTime)).getHours();
      var index = 1;
      if (workerunit.spam){
        index = 0;
      }
      var hoursegment = Math.floor(hour / hourLength);
      options.series[index].data[hoursegment].y ++;
      options.series[index].data[hoursegment].workerunits.push(workerunit._id);

    })
    return options;
  },

  render: function(){
    return (
      React.createElement("div", {className: 'chart'}, 
        React.createElement(HighCharts, {config: this.options(), ref: "chart"})
      )

      )
  }
});

module.exports = WorkerunitsByHour;
