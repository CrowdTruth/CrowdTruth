var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var _ = require('underscore');


var CountryPie = React.createClass({

  getInitialState: function(){
    return {
      height: window.innerHeight* this.props.height
    }
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


  options: function(){
    var options = {
      chart: {
        height: this.state.height,
        type: 'pie'
      },

      title:{
        text: 'Worker Judgements grouped By Country'
      },

      plotOptions: {
    
        pie: {
            shadow: false,
            center: ['50%', '50%']
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

      tooltip:{
          formatter: function(){
            var s = this.point.name + ': ' + Math.ceil(this.point.y*1000) / 10 + '% of total workers' ;
            if (this.point.partial_y){
              s += '-' + Math.ceil(this.point.partial_y*1000) / 10 + '%' + 'of '+this.point.country+' workers';
            }
            
            return s;
          }
      },

      series: [{
        cursor: 'pointer',
        point: {
          events: {
            click: function(chart){
              return function(e){
                chart.props.select('workers', this.workers, e.originalEvent.ctrlKey, false);
              }
            }(this)
          }
        },
        name: 'Country',
        data: [],
        size: '60%',
        dataLabels: {
            formatter: function () {
                return this.point.name;
            },
            color: 'white',
            distance: -30
        },
        
      },
      {
        cursor: 'pointer',
        point: {
          events: {
            click: function(chart){
              return function(e){
                chart.props.select('workerunits', this.workerunits, e.originalEvent.ctrlKey, false );
              }
            }(this)
          }
        },
        name: '',
        size: '80%',
        innerSize: '60%',
        data:[]
      }]
    }

    var countries = [];
    var workerUnits = [];
    var total = this.props.job.workerunits.length;


    this.props.job.workers.map(function(worker){
      var country = worker.country;
      var index = -1;
      countries.map(function(c, i){
        if (c.name == country){
          index = i;
          return;
        }
      })
      

      if(index == -1){
        index = countries.length;
        countries.push({name: country, workers: []});
        workerUnits.push({spam: 0, total: 0, spam_workerunits: [], nonspam_workerunits: []});
      }

      countries[index].workers.push(worker._id);

      var units = _.filter(this.props.job.workerunits, function(workerunit){
        return workerunit.crowdAgent_id == worker._id
      });



      workerUnits[index].total += units.length;
      var spam = _.filter(units, function(unit){
        return unit.spam == true;
      })

      units.map(function(unit){
        if (unit.spam){
          workerUnits[index].spam += 1;
          workerUnits[index].spam_workerunits.push(unit._id);

        }else{
          workerUnits[index].nonspam_workerunits.push(unit._id);
        }
      })





    }.bind(this))

    countries.map(function(country, i){
      options.series[0].data.push({
        name: country.name,
        y: workerUnits[i].total / total,
        workers: country.workers
      })

      options.series[1].data.push({
        name : 'spam',
        country: country.name,
        y: workerUnits[i].spam / total,
        partial_y: workerUnits[i].spam /workerUnits[i].total,
        color: 'red',
        workerunits: workerUnits[i].spam_workerunits
      })

      options.series[1].data.push({
        name : 'non spam',
        country: country.name,
        y: (workerUnits[i].total - workerUnits[i].spam ) / total,
        partial_y: (workerUnits[i].total - workerUnits[i].spam ) / workerUnits[i].total,
        color: 'green',
        workerunits: workerUnits[i].nonspam_workerunits
      })

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

module.exports = CountryPie;