var React = require('react');
var ReactDOM = require('react-dom');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var HighCharts = require('react-highcharts/bundle/highcharts');;
var d3 = window.d3;

var UnitsHeatmaps = React.createClass({
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