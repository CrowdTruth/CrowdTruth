var React = require('react');
var _ = require('underscore');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var ListGroup = Bootstrap.ListGroup;
var ListGroupItem = Bootstrap.ListGroupItem;
var FilterInput = require('../util/FilterInput.js');
var util = require('../util/util.js');

var AllViz = React.createClass({

  getInitialState: function(){
    return {
      projects: [],
      filteredProjects: [],
      active:null
    }
  },

  componentDidMount: function(){
    this.getProjects();

  },

  componentDidUpdate: function(prevProps, prevState){
    if(this.props !== prevProps){
      this.getProjects();
    }
  },



  getProjects: function(){

    var fields = ['_id', 'type', 'realCost', 'user_id'];
    var url= util.host + "api/search?noCache&match[documentType]=job";
    fields.map(function(field, index){
      url += '&only[' + index + ']=' + field;
    })


    $.getJSON(url, function(data){
      var groups = _.groupBy(data.documents, 'type');
      var projects = [];
      for (type in groups){
        projects.push({
          type: type,
          jobs: groups[type],
          users: this.getUsers(groups[type])
        })
      }

      this.setState({projects: projects, filteredProjects: projects});
            
    }.bind(this));
  },

  selectProject: function(type, index){
    this.setState({active:index})
    this.props.selectProject(type);
  },

  setFilteredProjects: function(filteredProjects){
    this.setState({filteredProjects: filteredProjects});
  },

  getTotalCost: function(jobs){
    var cost = _.reduce(jobs, function(memo, job){
        cost = job.realCost? job.realCost: 0;
        return memo + cost;
      }, 0)

    cost = Math.floor(cost*100) / 100;
    return cost;
  },

  getUsers: function(jobs){
    var users = _.uniq(jobs, function(job) { return job.user_id; });
    usersString = users[0].user_id;
    for (var i=1; i<users.length; i++){
      usersString+= ', ' + users[i].user_id;
    }

    return usersString;

  },

  render: function(){
    


    return(
      <Col xs={6}>
      <Row>
      
      <FilterInput data={this.state.projects} fields={['type', 'users']} onChange={this.setFilteredProjects} placeholder="Search through projects" />
      <ListGroup className='fitscreen'>
      {
        this.state.filteredProjects.map(function(project, index){
          return(
              <a className={'clickable full-width list-group-item ' + (this.state.active==index?"list-group-item-info":"")} key={index} onClick={this.selectProject.bind(this,project.type, index)}>
                <h4 className='list-group-item-heading'>{'Project ' + project.type}</h4> 
                <div>Job Count: {project.jobs.length} </div>
                <div>Total Cost: {this.getTotalCost(project.jobs) + '$'}</div>
                <div>Users: {project.users}</div>
              </a> 
            )
        }.bind(this))
      }
      
      </ListGroup>

      </Row>
      </Col>
      )
  }

})


module.exports = AllViz;