var React = require('react');
var Bootstrap = require('react-bootstrap');
var VizNavbar = require('./VizNavbar.js');
var JobContainer = require('./Job/JobContainer.js');
var Projects = require('./project/Projects.js');
var Row = Bootstrap.Row;

var Router = require('react-router');
var Route = Router.Route;
var RouteHandler = Router.RouteHandler;
var Redirect = Router.Redirect;


var StartPage = React.createClass({
  render: function() {
    return (
      
      <div className='container-fluid'> 
        <RouteHandler/>
      </div>
        
      
    )
  }
});

var routes = (
  <Route handler={StartPage}>
    <Redirect from="/" to="/projects" />
    <Route name="projects" path="/projects" handler={Projects}/>
    <Route name="job" path="/jobs/:id" handler={JobContainer}/>
  </Route>
  );



module.exports = routes;
