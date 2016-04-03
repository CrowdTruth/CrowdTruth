var React = require('react');
var Bootstrap = require('react-bootstrap');
var VizNavbar = require('./VizNavbar.js');
var JobContainer = require('./Job/JobContainer.js');
var VizTabs = require('./VizTabs.js');
var AllProjects = require('./project/AllViz.js');
var ProjectViz = require('./project/ProjectViz.js');


var VizContainer = React.createClass({
  getInitialState: function(){
    return {
      items : [{
        project: null,
        job: null
      }],

      active: 0

    };
  },

  onSelect: function(index){
    this.setState({active: index});
  },

  onAdd: function(){
    var newitems = this.state.items;
    newitems.push({project: null, job:null});
    this.setState({items: newitems,active: (this.state.items.length - 1) })
  },

  selectProject: function(project){
    var items = this.state.items;
    items[this.state.active].project = project;
    this.setState({items: items});
  },

  selectJob: function(job){
    var items = this.state.items;
    items[this.state.active].job = job._id;
    this.setState({items: items});
  },

  render: function(){
    var Tab;
    var activeItem = this.state.items[this.state.active];
  
    if (activeItem.job == null){
      Tab = [ 
        <AllProjects selectProject={this.selectProject} key={0}/>,
        <ProjectViz project={activeItem.project} selectJob={this.selectJob} key={1}/>
      ]
    }else{
      Tab = <JobContainer job_id={activeItem.job}/>
    }


    return (

      <div className='container-fluid'>
        {/*<VizTabs items={this.state.items} onSelect={this.onSelect} onAdd={this.onAdd}>*/}
        {Tab}

      </div>



    )
  }
})

module.exports = VizContainer;