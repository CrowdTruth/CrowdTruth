var React = require('react');
var Bootstrap = require('react-bootstrap');
var Navbar = Bootstrap.Navbar;
var Nav = Bootstrap.Nav;
var NavItem = Bootstrap.NavItem;
var VizNavbar = React.createClass({displayName: "VizNavbar",


  gotoProjects: function(){
    this.transitionTo('projects');
  },

  render: function(){
    return (
      React.createElement(Navbar, {brand: "CrowdTruth"}, 
        React.createElement(Nav, {key: 1}, 
          React.createElement(NavItem, {eventKey: 1}, "To be Added"), 
          React.createElement(NavItem, {eventKey: 2, href: "#/projects"}, " Projects")
        )
      
      )
    )
  }
})

module.exports = VizNavbar;