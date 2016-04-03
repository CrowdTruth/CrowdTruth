var React = require('react');
var Bootstrap = require('react-bootstrap');
var Navbar = Bootstrap.Navbar;
var Nav = Bootstrap.Nav;
var NavItem = Bootstrap.NavItem;
var VizNavbar = React.createClass({


  gotoProjects: function(){
    this.transitionTo('projects');
  },

  render: function(){
    return (
      <Navbar brand='CrowdTruth'>
        <Nav key={1}>
          <NavItem eventKey={1} >To be Added</NavItem>
          <NavItem eventKey={2} href="#/projects" > Projects</NavItem>
        </Nav>
      
      </Navbar>
    )
  }
})

module.exports = VizNavbar;