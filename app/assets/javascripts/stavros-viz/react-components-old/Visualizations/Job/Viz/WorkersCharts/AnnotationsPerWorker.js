var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;

var AnnotationsPerWorker = React.createClass({
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
        text: 'Annotations per Worker'
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
                chart.props.select('workers', [this.worker], e.originalEvent.ctrlKey, false);
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
              return function(){
                chart.props.select('workerunits', this.workerunits, e.originalEvent.ctrlKey, false);
                chart.props.select('workers', [this.worker], e.originalEvent.ctrlKey, false);
              }
            }(this)
          }
        },
        name: 'non spam',
        data: [],
        color: 'green'
      }]
    }
    var workers = this.props.job.workers;
    for(var i = 0; i < (workers.length); i++){
      options.xAxis.categories.push(workers[i]._id.split('/').pop());
      options.series[0].data.push(
          {
            x: i,
            y: 0,
            workerunits:[],
            worker: workers[i]._id
          }
        );
      options.series[1].data.push({
        x: i,
        y: 0,
        workerunits: [], 
        worker: workers[i]._id
      });

    }

    this.props.job.workerunits.map(function(workerunit){
      var worker_id = workerunit.crowdAgent_id.split('/').pop();
      var workerIndex = options.xAxis.categories.indexOf(worker_id);
      var index = 1;
      if (workerunit.spam){
        index = 0;
      }
      options.series[index].data[workerIndex].y ++;
      options.series[index].data[workerIndex].workerunits.push(workerunit._id);

    })
    return options;
  },

  render: function(){
    return (
      <div className={'chart'}>
        <HighCharts config={this.options()} ref='chart' />
      </div>

      )
  }
});

module.exports = AnnotationsPerWorker;