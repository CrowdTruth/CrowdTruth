var React = require('react');
var Bootstrap = require('react-bootstrap');
var Chart = require('./AnnotationsCharts/UnitsHeatmaps');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var Button = Bootstrap.Button;
var Input = Bootstrap.Input;
var _ = require('underscore');


var AnnotationsChart = React.createClass({
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
        <Row>
          <Row>

            <Col xs={4} style={{"paddingLeft": "40px"}}>
              <Input type='select' ref="unitInput" label='Select unit ID' onClick={this.handleSelection} >
              {
                this.props.job.selected.units.map(function(unit, index){
                  return (
                      <option value={index} key={index}>{unit.split('/').pop()}</option>
                    )
                })
              }
              </Input>
            </Col>

            <Col xs={4}>
              <Input type='select' ref="typeInput" label='Select type' onClick={this.handleSelection} >
              {
                types.map(function(type, index){
                  return (
                      <option value={type} key={index}>{type}</option>
                    )
                })
              }
              </Input>

            </Col>

            <Col xs={4}>
              <Row>
                <Input type='checkbox' label='with Spam' defaultChecked={this.state.withSpam} onClick={this.changeSpam}/>
              </Row>
            </Col>
           
          </Row>
          
          <Chart items={items} _id={selectedUnit._id} height={this.props.height * 0.75} />
        </Row>
        )

    }else{
      var height = window.innerHeight* this.props.height*1.18+'px'
      activeChart = <Row> <h3 style={{height:height}} className={"text-center"}> Select Units from Unit Charts or Table</h3></Row>
    }
   

    

    return (
      <div className='bs-callout bs-callout-primary white'>
        <h3 className={'text-center'}>Annotations Heatmap</h3>
      
        {
          activeChart
        }
       
          
      </div>
      )
  }


});


module.exports = AnnotationsChart;