//done
var React = require('react');
var _ = require('underscore');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var ListGroup = Bootstrap.ListGroup;
var ListGroupItem = Bootstrap.ListGroupItem;
var FilterInput = require('../util/FilterInput.js');
var Button = Bootstrap.Button;
var Input = Bootstrap.Input;
var util = require('../util/util.js');
var JobVizHeader = require('../Job/Viz/JobVizHeader');


var ProjectViz = React.createClass({displayName: "ProjectViz",

  getInitialState: function(){
    return {
      jobs: [],
      filteredJobs: [], 
      multipleSelection: false,
      selected: [],
      finished:true,
      unordered:true,
      running: true,
      onlyWithResults: true

    }
  }, 

  componentDidMount: function(){
    this.getJobs();
  },
  
  componentDidUpdate: function(prevProps, prevState){
    if(this.props !== prevProps){
      this.getJobs();
    }
  },


  getJobs: function(){
    var fields = ['_id', 'completion', 'created_at', 'realCost', 'user_id', 'workerunitsCount', 'status', 'format', 'projectedCost' ];
    var url= "api/search?noCache&match[type]=job&match[documentType]=" + this.props.project;
    var only='';
    fields.map(function(field, index){
      only+= ('&only['+index+']='+field);
    })

    url += only;
    $.getJSON(url, function(data){
      var jobs = data.documents;
      jobs = _.sortBy(jobs, function(job){
        var date = new Date(job.created_at);
        return -date.getTime();
      })
      this.setState({jobs: jobs, filteredJobs: jobs});
            
    }.bind(this));
  },

  selectJob: function(job, hasResults){
    if (!hasResults){
      return;
    }
    if (this.state.multipleSelection){

      var selected = this.state.selected;

      var index = selected.indexOf(job._id);
      if (index==-1){
        selected.push(job._id);
      }else{
        selected.splice(index, 1);
      }
      
      this.setState({selected: selected});
    }else{
      this.props.selectJob([job._id]);
    }
  },

  setFilteredJobs: function(filteredJobs){
    this.setState({filteredJobs: filteredJobs});
  },

  toggleMultipleSelection: function(){
    this.setState({
      multipleSelection: !this.state.multipleSelection,
      selected :[]
    });
  },

  selectMultiple: function(){
    var jobs = this.state.selected;
    if (jobs.length > 0){
      this.props.selectJob(jobs);
    }
  },

  setPreferences: function(e){
    var preference = e.target.defaultValue;
    var currentState = this.state;
    currentState[preference] = !currentState[preference];
    this.setState(currentState);
  },

  render: function(){

    return(
      React.createElement(Col, {xs: 6}, 
      React.createElement(Row, null, 
        React.createElement(Col, {xs: 8}, 
          React.createElement(FilterInput, {data: this.state.jobs, fields: ['_id', 'user_id'], onChange: this.setFilteredJobs, placeholder: "Search through Jobs"})
        ), 
        React.createElement(Col, {xs: 4}, 
          React.createElement(Button, {bsStyle: this.state.multipleSelection?'success':null, onClick: this.toggleMultipleSelection}, " Multiple "), 
          this.state.multipleSelection? React.createElement(Button, {bsStyle: "primary", onClick: this.selectMultiple}, " Ok "): null
        )
      ), 
      React.createElement(Row, null, 
        React.createElement(Col, {xs: 4}, 
          "Show only:"
        ), 
        React.createElement(Col, {xs: 8}, 
          React.createElement("label", {className: "checkbox-inline"}, React.createElement("input", {type: "checkbox", defaultChecked: true, value: "running", onClick: this.setPreferences}), " Running"), 
          React.createElement("label", {className: "checkbox-inline"}, React.createElement("input", {type: "checkbox", defaultChecked: true, value: "finished", onClick: this.setPreferences}), " Finished"), 
          React.createElement("label", {className: "checkbox-inline"}, React.createElement("input", {type: "checkbox", defaultChecked: true, value: "onlyWithResults", onClick: this.setPreferences}), "Only with results"), 
          React.createElement("label", {className: "checkbox-inline"}, React.createElement("input", {type: "checkbox", defaultChecked: true, value: "unordered", onClick: this.setPreferences}), "Unordered")
        )
      ), 
      React.createElement(Row, null, 
      React.createElement(ListGroup, {className: "fitscreen"}, 
      
        this.state.filteredJobs.map(function(job, index){
          var currentState = this.state;
          var hasResults = job.workerunitsCount && job.workerunitsCount>0;
          var classString = 'full-width list-group-item ';
          classString += hasResults? 'clickable': 'not-active';
          var visible =  currentState[job.status]
          visible = currentState.onlyWithResults? visible && hasResults: visible;
          classString += visible?' show': ' hidden';
           
          return(

            React.createElement("a", {className: classString + " " + (this.state.selected.indexOf(job._id)>=0?"list-group-item-info":""), key: index, onClick: this.selectJob.bind(this,job, hasResults)}, 
              React.createElement("h4", {className: "list-group-item-heading"}, 'Job: ' + job._id), 
              React.createElement(JobVizHeader, {job:  {jobInfo:job} })
              /*<ListGroupItem bsStyle={this.state.selected.indexOf(job._id)>=0?"info":null}  className={classString} disabled={hasResults?false:true} key={index} header={'Job: ' + job._id} onClick={this.selectJob.bind(this,job, hasResults)}>
                
                  {hasResults?  <div></div> : <div> This job has no results</div>}

                  <div>Completion: {(Math.floor(job.completion*100) + '%')}</div>
                  <div>Created at: {(new Date(job.created_at)).toDateString()}</div>
                  <div>User: {job.user_id}</div>
                
                  {job.realCost? <div>Cost: {job.realCost + '$'}</div> : <div></div>}
                  <div>Status: {job.status}</div>
              </ListGroupItem>*/
            ) 
           
            )
          
        }.bind(this))
      
      )
      )
      )
      )
  }
})

module.exports = ProjectViz;