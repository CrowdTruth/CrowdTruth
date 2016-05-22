var jobcontainer = function(require,module,exports){
var React = require('react');
var JobViz = require('./JobViz');
var JobList = require('./JobList');
var _ = require('underscore');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var State = require('react-router').State;
var util = require('../util/util.js');


var JobContainer = React.createClass({displayName: "JobContainer",

  mixins: [ State ],

  getInitialState: function(){
    return {
      job: {
        jobInfo: null,
        workerunits: [],
        units: [],
        workers: [],
        selected: {
          units: [],
          workers: [],
          workerunits: []
        },
        selectedUnit: null
      },
      jobsLeftToLoad: null
    }

    
  },

  select: function(type, ids, push, remove){

    var job = this.state.job;

    if (push){
      var selected = this.state.job.selected[type];
      ids.map(function(id){
        var index = selected.indexOf(id);
        if ((index>-1)&&remove){
          selected.splice(index,1);
        }else{
          selected.push(id);
        }
      })
      job.selected[type]= selected;
    }else{
      job.selected[type] = ids;
    }

    this.setState({job: job});
  },

  sortBy: function(type, field, asc){
    var job = this.state.job;
    var multiplier = 1;
    if (!asc){
      multiplier = -1; //descending
    }
    job[type] = _.sortBy(job[type],function(element){
      return multiplier * element[field];
    });
    this.setState({job: job});

  },

  getJob: function(){
    
    var getJobData = function(job){
      var jobData = {
        jobInfo: job,
        workerunits: [],
        units: [],
        workers: [],
        workersReady: false,
        workerunitsReady: false,
        unitsReady: false,
        selected: {
          units: [],
          workers: [],
          workerunits: []
        }
      }

      
      var checkIfReady = function(){
        if (jobData.workersReady && jobData.workerunitsReady && jobData.unitsReady){
          var job = this.state.job;
          if (!job.jobInfo){
            job.jobInfo = jobData.jobInfo;
          }else{
            addTojobInfo(jobData.jobInfo);
          }
          Array.prototype.push.apply(job.workerunits, jobData.workerunits);
          Array.prototype.push.apply(job.units, jobData.units);
          Array.prototype.push.apply(job.workers, jobData.workers);

          this.setState({
            job: job,
            jobsLeftToLoad:this.state.jobsLeftToLoad - 1 
          });
          if(this.state.jobsLeftToLoad == 0){
            getUniqueData();
          }
        }
      }.bind(this);

      var addTojobInfo = function(jobInfo){
        var job = this.state.job;
        job.jobInfo._id = job.jobInfo._id +" "+jobInfo._id;
        job.jobInfo.realCost += jobInfo.realCost;
        job.jobInfo.projectedCost += jobInfo.projectedCost;
        if (job.jobInfo.format != jobInfo.format){
          job.jobInfo.format = job.jobInfo.format + "/" +jobInfo.format;
        }
        if (job.jobInfo.status !=  jobInfo.status){
          job.jobInfo.status = job.jobInfo.status + "/" +jobInfo.status;
        }

        for (unit in jobInfo.results.withSpam){
          job.jobInfo.results.withSpam[unit] = jobInfo.results.withSpam[unit];
        }

        for (unit in jobInfo.results.withoutSpam){
          job.jobInfo.results.withoutSpam[unit] = jobInfo.results.withoutSpam[unit];
        }


        this.setState({job:job});   
      }.bind(this);

      var getUniqueData = function(){
        var job = this.state.job;
        job.units = _.uniq(job.units, function(item){return item._id});
        job.workerunits = _.uniq(job.workerunits, function(item){return item._id});
        job.workers = _.uniq(job.workers, function(item){return item._id});
        this.setState({
          job:job,
          jobsLeftToLoad: -1
        })
      }.bind(this);

      var workerunitsUrl = 'api/search?limit=5000&match[type]=workerunit&match[job_id]=' + job._id;
      var batchUrl = 'api/search?only[0]=parents&match[_id]=' + job.batch_id;
      var workersUrl = 'api/search?collection=crowdagents'

      var getWorkerunitsAndWorkers = function(){
        $.getJSON(workerunitsUrl, function(data){
          jobData.workerunits = data.documents;
          jobData.workerunitsReady = true;
          checkIfReady();

          getWorkers();

        });
      };


      var getUnits = function(){
        $.getJSON(batchUrl, function(data){
          unitsUrl = '/api/search?';
          var units = data.documents[0].parents;
          units.map(function(unit){
            unitsUrl = unitsUrl + '&match[_id][in][]=' + unit
          });
          $.getJSON(unitsUrl, function(data){
            jobData.units = data.documents;
            jobData.unitsReady = true;
            checkIfReady();
          });
        });

      }

      var getWorkers = function(){
        var workersRaw = _.pluck(jobData.workerunits, 'crowdAgent_id' );
        var workers = workersRaw.filter(function(element, i , workersRaw){
          return i == workersRaw.indexOf(element);
        })
        
        workers.map(function(worker){
          workersUrl += '&match[_id][in][]='+worker;
        })

        $.getJSON(workersUrl, function(data){
          jobData.workers = data.documents;
          jobData.workersReady = true;
          checkIfReady();
        })

      }

      getWorkerunitsAndWorkers();
      getUnits();
    };


    var ids = this.getParams().id.replace(/~/g, '/');
    var idsArray = ids.split('|');
    this.setState({jobsLeftToLoad: idsArray.length});

    idsArray.map(function(id){
      var url="/api/search?noCache&match[_id]=" + id;
      $.getJSON(url, function(data){
        if (data.documents[0]){        
          getJobData.bind(this,data.documents[0])();
        }else{
          console.log("No Job Data");
        }
      }.bind(this)); 

    }.bind(this));
       
  },

  componentDidMount: function(){

    this.getJob();

  },

  componentDidUpdate: function(prevProps, prevState){
    if(this.props !== prevProps){
      this.setState({
        job: {
          jobInfo: null,
          workerunits: [],
          units: [],
          workers: [],
          selected: {
            units: [],
            workers: [],
            workerunits: []
          },
          selectedUnit: null
        },
        jobsLeftToLoad: 0
      });
      this.getJob();
    }
  },

  render: function(){
    var job = this.state.job;
    var Job;

    if(this.state.jobsLeftToLoad != -1){
      Job = React.createElement("div", {className: "text-center"}, " Not Ready Yet ")
      

    }else{
      Job = (React.createElement(Row, {className: "bs-callout"}, 
            React.createElement("h3", {className: "text-center job-title"}, "Job id :", React.createElement("b", null, " ", job.jobInfo._id)), 
            React.createElement(Col, {xs: 6}, 
              React.createElement(JobViz, {job: job, select: this.select, sortBy: this.sortBy})
            ), 
            React.createElement(Col, {xs: 6}, 
              React.createElement(JobList, {job: job, select: this.select})
            )

          ))
    }

    return (
      React.createElement("div", null, 
      
        
          Job
        
     
      )
    )

  }

});


module.exports = JobContainer;

};



var joblist = function(require,module,exports){
var React = require('react');
var Input = require('react-bootstrap').Input;
var ReactTable = require('react-bootstrap-table');
var BootstrapTable = ReactTable.BootstrapTable;
var TableHeaderColumn = ReactTable.TableHeaderColumn;
var TableDataSet=ReactTable.TableDataSet;
var _ = require('underscore');
var Row = require('react-bootstrap').Row;
var Col = require('react-bootstrap').Col;

var JobList = React.createClass({displayName: "JobList",

  getInitialState: function(){
    return {
      width: window.innerWidth* 0.45,
      height: window.innerHeight * 0.75,
      type: 'workers',
      showSelected: false,
      columns: {
        units: [{
            field: '_id',
            name: 'id'
          },
          {
            field: 'avg_clarity',
            name: 'Average Clarity'
          },
          {
            field: 'content',
            name: 'Content',
            render: function(cell,row){
              if (cell.url){
                return cell.url;
              }else if(cell.chunk_text){
                return cell.chunk_text;
              }else if(cell.description){
                return cell.description;
              }else{
                return null;
              }

            }
          },
          {
            field: 'documentType',
            name: 'Document Type'
          }],
        workers: [{
            field: '_id',
            name: 'id',
          },
          {
            field: 'avg_agreement',
            name: 'Average Agreement'
          },
          {
            field: 'avg_cosine',
            name: 'Average Cosine'
          },
          {
            field: 'softwareAgent_id',
            name: 'Platform'
          },
          {
            field: 'country',
            name: 'Country'
          }],
        workerunits: [{
          field: '_id',
          name: 'id'
        },
        {
          field: 'unit_id',
          name: 'Unit ID'
        },
        {
          field: 'crowdAgent_id',
          name: 'Worker id',

        },
        {
          field: 'submitTime',
          name: 'Time to complete (sec)',
          render: function(cell,row){
            return (new Date(row['submitTime']) - new Date(row['acceptTime'])) / 1000;
          }
        }
        ],
        
      }
    };
  },

  componentDidMount: function(){
    window.addEventListener('resize', this.handleResize);
  },

  componentWillMount: function(){
    this.setTables(this.props);
  },


  tables: {
    workersTable: null,
    unitsTable: null,
    workerunitsTable: null,
    workersSelected: null,
    unitsSelected: null,
    workerunitsSelected: null
  },

  getSelectedRowProps: function(props,type){

    var selected = props.job.selected[type].slice();
    return {
      mode: "checkbox",
      clickToSelect: true,
      bgColor: "rgb(238, 193, 213)",
      onSelect: this.onRowSelect.bind(this,type),
      selected: selected,
      hideSelectColumn:true
    };
  },

  onRowSelect: function(type, row){
    this.props.select(type, [row._id], true, true);
  },

  setTables: function(props){
    this.tables.workersTable= React.createElement(BootstrapTable, {height: this.state.height+'px', data: props.job['workers'], key: 1, columnFilter: true, selectRow: this.getSelectedRowProps(props,'workers')}, 
          
            this.getColumns('workers')
          
        )
    
    this.tables.unitsTable = React.createElement(BootstrapTable, {height: this.state.height+'px', data: props.job['units'], key: 2, columnFilter: true, selectRow: this.getSelectedRowProps(props,'units')}, 
          
            this.getColumns('units')
          
        )

    this.tables.workerunitsTable = React.createElement(BootstrapTable, {height: this.state.height+'px', data: props.job['workerunits'], key: 3, columnFilter: true, selectRow: this.getSelectedRowProps(props,'workerunits')}, 
          
            this.getColumns('workerunits')
          
        )

    this.tables.workersSelected = React.createElement(BootstrapTable, {height: this.state.height+'px', data: this.getSelected(props, 'workers'), key: 4, columnFilter: true}, 
          
            this.getColumns('workers')
          
        )

    this.tables.unitsSelected = React.createElement(BootstrapTable, {height: this.state.height+'px', data: this.getSelected(props, 'units'), key: 5, columnFilter: true}, 
          
            this.getColumns('units')
          
        )

    this.tables.workerunitsSelected = React.createElement(BootstrapTable, {height: this.state.height+'px', data: this.getSelected(props, 'workerunits'), key: 6, columnFilter: true}, 
          
            this.getColumns('workerunits')
          
        )

  },
 
  handleResize: function(){
    this.setState({
      width: window.innerWidth* 0.45,
      height: window.innerHeight * 0.75
     });
    this.setTables(this.props);
  },


  getColumns: function(type){
    var cols = this.state.columns[type];
    var result = [];
    cols.map(function(col, index){
      result.push(React.createElement(TableHeaderColumn, {
                    dataField: col.field, 
                    dataKey: col.field, 
                    isKey: col.field=="_id"?true:false, 
                    dataSort: true, 
                    dataFormat: col.render, 
                    key: index}, 
                    col.name, " "))
    });

    return result;

  },

  getSelected: function(props, type){
    var selected =  _.filter(props.job[type], function(doc){
      return _.contains(props.job.selected[type], doc['_id']);
    }.bind(this));

    return selected;
  },


  toggleSelected: function(){
    this.setState({showSelected: !this.state.showSelected});
  },

  handleTypeChange:  function(){
    this.setState({type: this.refs.typeInput.getValue()});
    
  },

  componentWillReceiveProps: function(nextProps){
    if (nextProps!==this.props){
      this.setTables(nextProps);
    }
  },



  render: function(){
    
    this.setTables(this.props);


    return (
      React.createElement(Row, {className: "white"}, 
        React.createElement(Row, null, 
        React.createElement(Col, {xs: 6}, 
          React.createElement(Input, {type: "select", ref: "typeInput", onClick: this.handleTypeChange}, 
            React.createElement("option", {value: 'workers'}, " Workers "), 
            React.createElement("option", {value: 'units'}, " Units "), 
            React.createElement("option", {value: 'workerunits'}, " Annotations ")
          )
        ), 
        React.createElement(Col, {xs: 6}, 
          React.createElement(Input, {type: "checkbox", label: "Show only selected", onClick: this.toggleSelected})

        )
        ), 
        this.state.type=='workers'&& !this.state.showSelected? this.tables.workersTable: null, 
        this.state.type=='units' && !this.state.showSelected ? this.tables.unitsTable: null, 
        this.state.type=='workerunits' && !this.state.showSelected? this.tables.workerunitsTable: null, 

        this.state.type=='workers'&& this.state.showSelected? this.tables.workersSelected: null, 
        this.state.type=='units' && this.state.showSelected? this.tables.unitsSelected: null, 
        this.state.type=='workerunits' && this.state.showSelected? this.tables.workerunitsSelected: null
      )
      )

  }

});


module.exports = JobList;

};

var jobviz=function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var JobVizHeader = require('./Viz/JobVizHeader');
var UnitsChart = require('./Viz/UnitsChart');
var WorkersChart = require('./Viz/WorkersChart');
var AnnotationsChart = require('./Viz/AnnotationsChart');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var Button = Bootstrap.Button;


var JobViz = React.createClass({displayName: "JobViz",

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
      React.createElement("div", null, 
        React.createElement(Row, null, 
          React.createElement(JobVizHeader, {job: this.props.job})
        ), 
        React.createElement(Row, {className: "top-viz"}, 
          React.createElement(this.state.top, {job: this.props.job, select: this.props.select, sortBy: this.props.sortBy, height: 0.35})
        ), 
        React.createElement(Row, null, 
          React.createElement(Col, {xs: 6}, 
            React.createElement(Button, {onClick: this.swapLeft}, " ", React.createElement("i", {className: "fa fa-arrow-up"}), React.createElement("i", {className: "fa fa-arrow-down"}), " ")
          ), 
          React.createElement(Col, {xs: 6}, 
            React.createElement(Button, {className: 'pull-right', onClick: this.swapRight}, " ", React.createElement("i", {className: "fa fa-arrow-up"}), React.createElement("i", {className: "fa fa-arrow-down"}), "  ")
          )
        ), 
        React.createElement(Row, {className: "bottom-viz"}, 
          React.createElement(Col, {xs: 6}, 
            React.createElement(this.state.left, {job: this.props.job, select: this.props.select, sortBy: this.props.sortBy, height: 0.25})
          ), 

          React.createElement(Col, {xs: 6}, 
            React.createElement(this.state.right, {job: this.props.job, select: this.props.select, sortBy: this.props.sortBy, height: 0.25})
          )

        )
      )
      )

  }

});


module.exports = JobViz

};

var vizcontainer = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var VizNavbar = require('./VizNavbar.js');
var JobContainer = require('./Job/JobContainer.js');
var VizTabs = require('./VizTabs.js');
var AllProjects = require('./project/AllViz.js');
var ProjectViz = require('./project/ProjectViz.js');


var VizContainer = React.createClass({displayName: "VizContainer",
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
        React.createElement(AllProjects, {selectProject: this.selectProject, key: 0}),
        React.createElement(ProjectViz, {project: activeItem.project, selectJob: this.selectJob, key: 1})
      ]
    }else{
      Tab = React.createElement(JobContainer, {job_id: activeItem.job})
    }


    return (

      React.createElement("div", {className: "container-fluid"}, 
        /*<VizTabs items={this.state.items} onSelect={this.onSelect} onAdd={this.onAdd}>*/
        Tab

      )



    )
  }
})

module.exports = VizContainer;

};


var allviz = function(require,module,exports){
var React = require('react');
var _ = require('underscore');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var ListGroup = Bootstrap.ListGroup;
var ListGroupItem = Bootstrap.ListGroupItem;
var FilterInput = require('../util/FilterInput.js');
var util = require('../util/util.js');

var AllViz = React.createClass({displayName: "AllViz",

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

    var fields = ['_id', 'documentType', 'realCost', 'user_id'];
    var url="api/search?noCache&match[type]=job";
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
      React.createElement(Col, {xs: 6}, 
      React.createElement(Row, null, 
      
      React.createElement(FilterInput, {data: this.state.projects, fields: ['type', 'users'], onChange: this.setFilteredProjects, placeholder: "Search through projects"}), 
      React.createElement(ListGroup, {className: "fitscreen"}, 
      
        this.state.filteredProjects.map(function(project, index){
          return(
              React.createElement("a", {className: 'clickable full-width list-group-item ' + (this.state.active==index?"list-group-item-info":""), key: index, onClick: this.selectProject.bind(this,project.type, index)}, 
                React.createElement("h4", {className: "list-group-item-heading"}, 'Project ' + project.type), 
                React.createElement("div", null, "Job Count: ", project.jobs.length, " "), 
                React.createElement("div", null, "Total Cost: ", this.getTotalCost(project.jobs) + '$'), 
                React.createElement("div", null, "Users: ", project.users)
              ) 
            )
        }.bind(this))
      
      
      )

      )
      )
      )
  }

})


module.exports = AllViz;

};


var projectviz = function(require,module,exports){
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

};

var projects = function(require,module,exports){
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

};