var React = require('react');
var Bootstrap = require('react-bootstrap');
var JobVizHeader = require('./Viz/JobVizHeader');
var UnitsChart = require('./Viz/UnitsChart');
var WorkersChart = require('./Viz/WorkersChart');
var AnnotationsChart = require('./Viz/AnnotationsChart');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var Button = Bootstrap.Button;


var JobViz = React.createClass({

  getInitialState: function(){
    return {
      top: UnitsChart,
      left: WorkersChart,
      right : AnnotationsChart
      }
  },

  componentDidMount: function(){
    //set up job
  },

  swapLeft: function(){
    this.setState({
      top: this.state.left,
      left: this.state.top
    })
  },

  swapRight: function(){
    this.setState({
      top: this.state.right,
      right: this.state.top
    })
  },

  render: function(){
    return (
      <div>
        <Row>
          <JobVizHeader job={this.props.job} />
        </Row>
        <Row className='top-viz'>
          <this.state.top job={this.props.job} select={this.props.select} sortBy={this.props.sortBy} height={0.35} />
        </Row>
        <Row>
          <Col xs={6}>
            <Button onClick={this.swapLeft}> <i className="fa fa-arrow-up"></i><i className="fa fa-arrow-down"></i> </Button>
          </Col>
          <Col xs={6}>
            <Button className={'pull-right'} onClick={this.swapRight}> <i className="fa fa-arrow-up"></i><i className="fa fa-arrow-down"></i>  </Button>
          </Col>
        </Row>
        <Row className='bottom-viz'>
          <Col xs={6}>
            <this.state.left job={this.props.job} select={this.props.select} sortBy={this.props.sortBy} height={0.25} />
          </Col>

          <Col xs={6}>
            <this.state.right job={this.props.job} select={this.props.select} sortBy={this.props.sortBy} height={0.25} />
          </Col>

        </Row>
      </div>
      )

  }

});


module.exports = JobViz