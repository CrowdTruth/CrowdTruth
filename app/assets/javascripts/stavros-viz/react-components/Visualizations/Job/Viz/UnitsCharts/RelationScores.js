var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;

var RelationScores = React.createClass({
  getInitialState: function(){

    

    return {
      height: window.innerHeight* this.props.height ,
      Relations: this.getRelations()
    }
  },

  getRelations: function(){
    var Relations = [];
    var results = this.props.job.jobInfo.results.withoutSpam;
    for (var unit in results){
      var scores = results[unit];//[this.props.job.jobInfo.type];
      var relationsFromUnit = Object.keys(results[unit][this.props.job.jobInfo.type]);
      Relations.push(relationsFromUnit);
    }

    Relations = Relations.filter(function(item, i, Relations){
      return i=Relations.indexOf(item);
    });

    return Relations;

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
        text: 'Average unit Clarity'
      },


      xAxis: {
        categories: []
      }, 

      yAxis: {
        min: 0,
        title: {
          text: 'RelationScore'
        }
      },
      series: [{
        cursor: 'pointer',
        point: {
          events: {
            click: this.getSelected
          }
        },

        name: 'Job Units',
        data: []
      }]
    }

    this.props.job.units.map(function(unit){
      options.xAxis.categories.push(unit._id.split('/').pop());
      options.series[0].data.push(unit.avg_clarity);
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

module.exports = RelationScores;