//done
var React = require('react');
var _ = require('underscore');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var AllProjects = require('./AllViz.js');
var ProjectViz = require('./ProjectViz.js');
var Navigation = require('react-router').Navigation;

var Projects = React.createClass({displayName: "Projects",

  mixins: [Navigation],

  getInitialState: function(){
    return {
      project: null 
    }
  },


  selectProject: function(project){
    
    this.setState({project: project});
  },

  selectJob: function(ids){
    var idsString = '';

    ids.map(function(id){
      idsString += id.replace(/\//g, '~') + '|';
    })
    idsString = idsString.substring(0, idsString.length - 1);
    this.transitionTo('job', {id: idsString});
  },


  render: function(){
    

    return(
      React.createElement(Row, null, 
        React.createElement(AllProjects, {selectProject: this.selectProject, key: 0}), 
        React.createElement(ProjectViz, {project: this.state.project, selectJob: this.selectJob, key: 1})
      )
      )
  }

})


module.exports = Projects;