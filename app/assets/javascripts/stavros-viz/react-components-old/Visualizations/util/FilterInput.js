var React = require('react');
var Bootstrap = require('react-bootstrap');
var Input = Bootstrap.Input;
var Filter = require('./Filter.js');
var FilterInput = React.createClass({

  getInitialState: function(){

    return {
      value: '',

    }


  },
  
  componentDidMount: function(){
    this.initializeFilter();
  },


  componentDidUpdate: function(prevProps, prevState){
    if(this.props !== prevProps){
      this.initializeFilter();
    }
  },

  initializeFilter: function(){
    this.filter = new Filter(this.props.data, this.props.fields);
  },


  handleChange: function(){
    var value = this.refs.input.getValue();

    this.setState({
      value: value
    })

    this.props.onChange(this.filter.filter(value));

  },


  render: function(){
    return(
        <Input 
          type='text'
          value= {this.state.value}
          placeholder= {this.props.placeholder}
          ref='input'
          onChange={this.handleChange}/>



      )
  }


})

module.exports = FilterInput