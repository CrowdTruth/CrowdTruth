var React = require('react');
var JobViz = require('./JobViz');
var JobList = require('./JobList');
var _ = require('underscore');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Col = Bootstrap.Col;
var State = require('react-router').State;
var util = require('../util/util.js');


var JobContainer = React.createClass({

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

      var workerunitsUrl = util.host + 'api/search?limit=5000&match[documentType]=workerunit&match[job_id]=' + job._id;
      var batchUrl = util.host + 'api/search?only[0]=parents&match[_id]=' + job.batch_id;
      var workersUrl = util.host + 'api/search?collection=crowdagents'

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
          unitsUrl = util.host + '/api/search?';
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
      var url=util.host + "/api/search?noCache&match[_id]=" + id;
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
      Job = <div className="text-center"> Not Ready Yet </div>
      

    }else{
      Job = (<Row className='bs-callout'>
            <h3 className='text-center job-title'>Job id :<b> {job.jobInfo._id}</b></h3>
            <Col xs={6}>
              <JobViz job={job} select={this.select} sortBy={this.sortBy}/>
            </Col>
            <Col xs={6}>
              <JobList job={job} select={this.select} />
            </Col>

          </Row>)
    }

    return (
      <div>
      
        {
          Job
        }
     
      </div>
    )

  }

});


module.exports = JobContainer;