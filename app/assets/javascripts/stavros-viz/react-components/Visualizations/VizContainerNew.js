//done
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

var StartPage = React.createClass({displayName: "StartPage",
  render: function() {
    return (
      
      React.createElement("div", {className: "container-fluid"}, 
        React.createElement(RouteHandler, null)
      )
        
      
    )
  }
});

var routes = (
  React.createElement(Route, {handler: StartPage}, 
    React.createElement(Redirect, {from: "/", to: "/projects"}), 
    React.createElement(Route, {name: "projects", path: "/projects", handler: Projects}), 
    React.createElement(Route, {name: "job", path: "/jobs/:id", handler: JobContainer})
  )
  );



module.exports = routes;
