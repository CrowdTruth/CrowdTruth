var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var _ = require('underscore');
var Button = Bootstrap.Button;
var Col = Bootstrap.Col;


var WorkerAgreementChart = React.createClass({
  getInitialState: function(){
    return {
      height: window.innerHeight* this.props.height *0.85
    }
  },

  getSelected: function(){
  },

  componentDidMount: function(){
    window.addEventListener('resize', this.handleResize);
    this.props.sortBy('workers','avg_agreement', true);
    this.forceUpdate();
  },

  componentWillUnmount: function(){
    window.removeEventListener('resize', this.handleResize);
  },

  handleResize: function(){
    this.setState({
      height: window.innerHeight* this.props.height*0.85
     });
  },

  select: function(ids, push){
    var annot = [];

    ids.map(function(worker){
      this.props.job.workerunits.map(function(workerunit){
        if (workerunit.crowdAgent_id === worker){
          annot.push(workerunit._id);
        }
      })
    }.bind(this))

    this.props.select('workers', ids, push, false);
    this.props.select('workerunits', annot, push, false);
  },

   setSort: function(asc){
    this.props.sortBy('workers','avg_agreement', asc);
    this.forceUpdate();
  },

  options: function(){
    var options = {
      chart: {
        height: this.state.height,
        type: 'column'
      },

      title:{
        text: 'Worker Agreement Chart'
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
        max: 1,
        title: {
          text: 'Average Agreement'
        }
      },
      series: [{
        cursor: 'pointer',
        point: {
          events: {
            click: function(chart){
              return function(e){ 
                chart.select(this.workers, e.originalEvent.ctrlKey);
                //this.update({color:'red'}, true, false);
              }
            }(this)
          }
        },

        name: 'Workers',
        data: []
      }]
    }

    this.props.job.workers.map(function(worker, index){
      options.xAxis.categories.push(worker._id.split('/').pop());
      options.series[0].data.push({
        x: index,
        y:worker.avg_agreement,
        workers: [worker._id]
      });
    })
    return options;
  },

  render: function(){
    return (
      <div className={'chart'}>
        <HighCharts config={this.options()} ref='chart' />
        <Row>
          <Col xs={4}> <div className="pull-right">Sort:</div> </Col>
          <Col xs={4}><Button onClick={this.setSort.bind(this,true)}> Asc </Button> </Col> 
          <Col xs={4}><Button onClick={this.setSort.bind(this,false)}>Desc</Button></Col>
        </Row>
      </div>

      )
  }
});

module.exports = WorkerAgreementChart;