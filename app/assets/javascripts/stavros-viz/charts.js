var annotationschart = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Chart = require('./AnnotationsCharts/UnitsHeatmaps');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var Button = Bootstrap.Button;
var Input = Bootstrap.Input;
var _ = require('underscore');


var AnnotationsChart = React.createClass({displayName: "AnnotationsChart",
  getInitialState: function(){
    return {
      withSpam: true,
      unitIndex: 0,
      type: null
    }
  },

  getTypes: function(){
    var types = [];
    var unit = this.props.job.selected.units[this.state.unitIndex]
  
    var res = this.props.job.jobInfo.results.withSpam[unit];
    for (type in res){
      types.push(type);
    }


    return types;
  },

  componentWillReceiveProps: function(nextProps){
    if (nextProps !== this.props){
      this.setState({
        unitIndex:0,
        type: null

      })
    }
  },

  changeChart: function(next){
    if (next && this.state.index<(this.state.types.length-1)){
      this.setState({index: (this.state.index+1)})
    }else if(this.state.index>0){
      this.setState({index: this.state.index -1})
    }

  },

  changeSpam: function(){
    this.setState({withSpam: !this.state.withSpam})
  },


  handleSelection: function(){
    var type = this.refs.typeInput.getValue();
    var unitIndex = this.refs.unitInput.getValue();
    this.setState({
      type: type,
      unitIndex: unitIndex
    })
  },

  render: function(){

    var res = this.props.job.jobInfo.results;
    var selectedUnit = this.props.job.selected.units[this.state.unitIndex];

    var items = [];

    if (selectedUnit){

      var types = this.getTypes();
      res = this.state.withSpam? res.withSpam : res.withoutSpam;
      res = res[this.props.job.selected.units[this.state.unitIndex]];
      res = this.state.type? res[this.state.type]: res[types[0]];
      for (name in res){
        items.push({name: name , value: res[name]});
      }
      items = _.sortBy(items, 'name');
    }
   
    var activeChart;
    if (items.length > 0){
      activeChart = (
        React.createElement(Row, null, 
          React.createElement(Row, null, 

            React.createElement(Col, {xs: 4, style: {"paddingLeft": "40px"}}, 
              React.createElement(Input, {type: "select", ref: "unitInput", label: "Select unit ID", onClick: this.handleSelection}, 
              
                this.props.job.selected.units.map(function(unit, index){
                  return (
                      React.createElement("option", {value: index, key: index}, unit.split('/').pop())
                    )
                })
              
              )
            ), 

            React.createElement(Col, {xs: 4}, 
              React.createElement(Input, {type: "select", ref: "typeInput", label: "Select type", onClick: this.handleSelection}, 
              
                types.map(function(type, index){
                  return (
                      React.createElement("option", {value: type, key: index}, type)
                    )
                })
              
              )

            ), 

            React.createElement(Col, {xs: 4}, 
              React.createElement(Row, null, 
                React.createElement(Input, {type: "checkbox", label: "with Spam", defaultChecked: this.state.withSpam, onClick: this.changeSpam})
              )
            )
           
          ), 
          
          React.createElement(Chart, {items: items, _id: selectedUnit._id, height: this.props.height * 0.75})
        )
        )

    }else{
      var height = window.innerHeight* this.props.height*1.18+'px'
      activeChart = React.createElement(Row, null, " ", React.createElement("h3", {style: {height:height}, className: "text-center"}, " Select Units from Unit Charts or Table"))
    }
   

    

    return (
      React.createElement("div", {className: "bs-callout bs-callout-primary white"}, 
        React.createElement("h3", {className: 'text-center'}, "Annotations Heatmap"), 
      
        
          activeChart
        
       
          
      )
      )
  }


});


module.exports = AnnotationsChart;

};

var unitsheatmap = function(require,module,exports){
var React = require('react');
var ReactDOM = require('react-dom');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var d3 = window.d3;

var UnitsHeatmaps = React.createClass({displayName: "UnitsHeatmaps",
  getInitialState: function(){
    return {
      height: window.innerHeight* this.props.height
    }
  },


  componentDidMount: function(){
    window.addEventListener('resize', this.handleResize);
    this.drawChart();
  },

  componentWillUnmount: function(){
    window.removeEventListener('resize', this.handleResize);
  },
  handleResize: function(){
    this.setState({height: window.innerHeight* this.props.height})
    this.drawChart();
  },
  componentDidUpdate: function(){
    this.drawChart();
  },

  drawChart: function(){
    el = ReactDOM.findDOMNode(this);

    d3.select(el)
      .select('svg')
      .remove();
    
    function cell_dim(total, cells) { return Math.floor(total/cells) }
    var infoWidth = 0;    
    var total_width = el.clientWidth*0.9;    
    var total_height = this.state.height;
    var data = this.props.items;
    var rows = data.length;
    var cell_width = 0.8* Math.floor(Math.sqrt((total_width*total_height)/rows));
    // for (var i=0; i< 5; i++){
    //    var cell_width = Math.floor(Math.sqrt((total_width - total_width%cell_width)*(total_height-total_height%cell_width)/rows));
    // }
  
    var row_height =  Math.min(cell_width, 60);
    var col_width = row_height;
    var col_cells = Math.floor(total_height/row_height);
    var row_cells = Math.floor(total_width/col_width);
    var cols = 1;
    

    var max = 0;
    data.map(function(item){
      if (item.value > max){
        max = item.value;
      }
    });
    var min = 100000;
    data.map(function(item){
      if (item.value < min){
        min = item.value;
      }
    });

    var color_chart = d3.select(el)
                      .append("svg")
                      .attr("class", "chart")
                      .attr("width", total_width)
                      .attr("height", total_height);

    var tip = d3.tip()
        .attr('class', 'd3-tip')
        .offset([-10, 0])
        .html(function(d) {
          return "<span>" + d.name + "</span>";
        })


    color_chart.call(tip);

    var color = d3.scale.linear()
            .domain([min, max])
            .range([ "#D8F0A0", "#385000"]);

    var nodes = color_chart.selectAll("g")
          .data(data)
          .enter()
          .append("g")
          .attr("transform", function(d, i) {
            
            var x = (i % row_cells)*col_width;
            var y = Math.floor(i/row_cells)*col_width;
            // var x = Math.floor(i/col_cells) * col_width;
            // var y = (i%col_cells)*row_height;
            return "translate(" + (infoWidth + x)+  ","  + y+ ")";
          })

    nodes.append("rect")
          // .attr("x", function(d,i) { return Math.floor(i / rows) * col_width; })
          // .attr("y", function(d,i) { return i % rows * row_height; })
          .attr("width", col_width)
          .attr("height", row_height)
          .attr("fill", function(d){return color(d.value)})
          .on('mouseover', tip.show)
          .on('mouseout', tip.hide);

  nodes.append("text")
    .attr("x", function(d) { return col_width/2; })
    .attr("y", row_height / 2)
    .attr("dy", ".35em")
    .attr("dx", "-.5em")
    .text(function(d) { return d.value; });

  },




  render: function(){

    return (
      React.createElement("div", {className: "Chart"}
      )
    );
   
   
  }
});

module.exports = UnitsHeatmaps;

};

var jobvizheader = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Label = Bootstrap.Label;
var Col=Bootstrap.Col;

var JobVizHeader = React.createClass({displayName: "JobVizHeader",

  

  render: function(){

    var toHHMMSS = function (sec) {
        var sec_num = parseInt(sec);

        var days = Math.floor(sec_num  / 86400);
        var hours   = Math.floor((sec_num -(days*86400)) / 3600);
        var minutes = Math.floor((sec_num -(days*86400) - (hours * 3600)) / 60);
        var seconds = sec_num -(days*86400) -(hours * 3600) - (minutes * 60);

        if (hours   < 10) {hours   = "0"+hours;}
        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}
        var time    =days+'days'+ ':' + hours+':'+minutes+':'+seconds;
        return time;
    }


    return (
      React.createElement(Row, {className: "bs-callout bs-callout-success"}, 
        React.createElement(HeaderItem, {size: 2, itemName: 'User', value: this.props.job.jobInfo.user_id}), 
        React.createElement(HeaderItem, {size: 2, itemName: 'Real cost', value: '$' +this.props.job.jobInfo.realCost}), 
        React.createElement(HeaderItem, {size: 2, itemName: 'Running time', value: this.props.job.jobInfo.runningTimeInSeconds?(toHHMMSS(this.props.job.jobInfo.runningTimeInSeconds)): Math.floor(this.props.job.jobInfo.completion*100) + '% completed'}), 
        React.createElement(HeaderItem, {size: 2, itemName: 'Status', value: this.props.job.jobInfo.status}), 
        React.createElement(HeaderItem, {size: 2, itemName: 'Created at', value: (new Date(this.props.job.jobInfo.created_at)).toDateString()}), 
        React.createElement(HeaderItem, {size: 2, itemName: 'Format', value: this.props.job.jobInfo.format})
      )

      )

  }

});

var HeaderItem = React.createClass({displayName: "HeaderItem",
  render: function(){
    var val = this.props.value;
    if (val.indexOf('undefined') > -1){
      val = 'Value not found';
    }
    return (
      React.createElement(Col, {xs: this.props.size}, 
        
        React.createElement(Row, null, 
          React.createElement("h3", {className: 'text-center'}, React.createElement(Label, {bsStyle: "danger"}, val))
        ), 
        React.createElement(Row, null, 
          React.createElement("div", {className: 'text-center'}, this.props.itemName)
        )
      )
      )
  }

})
module.exports = JobVizHeader

};;


var avgclaritychart = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var Button = Bootstrap.Button;
var Col = Bootstrap.Col;

var AvgClarityChart = React.createClass({displayName: "AvgClarityChart",
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

};

var unitsgroupedbyclarity = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var _ = require('underscore');
var Input = Bootstrap.Input;
var Button = Bootstrap.Button;


var UnitsGroupedByAvgClarity = React.createClass({displayName: "UnitsGroupedByAvgClarity",

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
      React.createElement("div", {className: 'chart'}, 
        React.createElement(HighCharts, {config: this.options(), ref: "chart"}), 
        React.createElement(Row, null, 
          React.createElement(Col, {xs: 4}, " ", React.createElement("div", {className: "pull-right"}, "Set Clarity Range:"), " "), 
          React.createElement(Col, {xs: 4}, React.createElement(Input, {type: "text", ref: "timeRangeInput", bsStyle: this.state.invalidInput?"error":null}), " "), 
          React.createElement(Col, {xs: 4}, React.createElement(Button, {onClick: this.setClarityRange}, "OK"))
        )
      )
      )
  }
});

module.exports = UnitsGroupedByAvgClarity;

};

var unitsbytimetocomplete = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var _ = require('underscore');
var Input = Bootstrap.Input;
var Button = Bootstrap.Button;

var UnitsTimeToComplete = React.createClass({displayName: "UnitsTimeToComplete",
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
      React.createElement("div", {className: 'chart'}, 
        React.createElement(HighCharts, {config: this.options(), ref: "chart"}), 
        React.createElement(Row, null, 
          React.createElement(Col, {xs: 4}, " ", React.createElement("div", {className: "pull-right"}, "Set Time Range:"), " "), 
          React.createElement(Col, {xs: 4}, React.createElement(Input, {type: "text", ref: "timeRangeInput", bsStyle: this.state.invalidInput?"error":null}), " "), 
          React.createElement(Col, {xs: 4}, React.createElement(Button, {onClick: this.setTimeRange}, "OK"))
        )
      )
      )
  }
});

module.exports = UnitsTimeToComplete;

};

var workerschart = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var Button = Bootstrap.Button;


var Charts = [ 
    {
      chart:require('./WorkersCharts/WorkerAgreementChart'),
      desc: 'Worker Agreement'
    },
    {
      chart:require('./WorkersCharts/CountryPie'),
      desc: 'Country Pie'
    },
    {
      chart:require('./WorkersCharts/WorkerunitsByHour'),
      desc: 'Hourly workerunits'
    },
    {
      chart:require('./WorkersCharts/AnnotationsPerWorker'),
      desc: 'Annotations per worker'
    } 
  ]


var WorkersChart = React.createClass({displayName: "WorkersChart",
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
    return (nextState!==this.state)||(this.props.job.workers !== nextProps.job.workers) ;
  },

  render: function(){
    var index = this.state.index;
    var Chart = Charts[index].chart;
    var rightActive = (index<Charts.length - 1);
    var leftActive = (index>0);
    var rightBtnLabel = rightActive?Charts[index+1].desc:'No more charts';
    var leftBtnLabel = leftActive?Charts[index-1].desc:'No more charts';

    return(
        React.createElement("div", {className: 'bs-callout bs-callout-danger white'}, 
          React.createElement(Row, null, 
            React.createElement(Col, {xs: 4}, 
              React.createElement(Button, {bsStyle: "primary", onClick: this.changeChart.bind(null,false), disabled: !leftActive}, leftBtnLabel)
            ), 
            React.createElement(Col, {xs: 4}, 
              React.createElement("h3", {className: 'text-center'}, "Workers")
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

module.exports = WorkersChart;

};

var annotationsperworker = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;

var AnnotationsPerWorker = React.createClass({displayName: "AnnotationsPerWorker",
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
      React.createElement("div", {className: 'chart'}, 
        React.createElement(HighCharts, {config: this.options(), ref: "chart"})
      )

      )
  }
});

module.exports = AnnotationsPerWorker;

};

var countriepie = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var _ = require('underscore');


var CountryPie = React.createClass({displayName: "CountryPie",

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
      React.createElement("div", {className: 'chart'}, 
        React.createElement(HighCharts, {config: this.options(), ref: "chart"})
      )
      )
  }
});

module.exports = CountryPie;

};

var workeragreement = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var _ = require('underscore');
var Button = Bootstrap.Button;
var Col = Bootstrap.Col;


var WorkerAgreementChart = React.createClass({displayName: "WorkerAgreementChart",
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

module.exports = WorkerAgreementChart;

};

var workerunitsbyhour = function(require,module,exports){
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

};

var unitschart = function(require,module,exports){
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

}