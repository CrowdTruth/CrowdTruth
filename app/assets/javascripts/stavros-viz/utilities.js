router = function(require,module,exports){
var React = require('react');
var ReactDOM = require("react-dom");
var Router = require('react-router');
var VizContainer = require('./Visualizations/VizContainer.js');
var routes = require('./Visualizations/VizContainerNew.js');
//blocks = Blocks( $('.boxes'));

Router.run(routes,  function(Root){
  ReactDOM.render(React.createElement(Root, null), document.getElementById('app'))
});
//React.render(<VizContainerNew/>, document.getElementById('app'));

};

var routes = function(require,module,exports){
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


};

var viznavbar = function(require,module,exports){
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

};

var viztabs = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Nav = Bootstrap.Nav;
var NavItem = Bootstrap.NavItem;
var Button = Bootstrap.Button;


var VizTabs = React.createClass({displayName: "VizTabs",

  getInitialState: function(){
    return {active: 0}
  },

  onSelect: function(selectedKey){

    if (this.state.active != selectedKey){
      if(this.props.items.length == selectedKey){
        this.props.onAdd();
      }else{
        this.props.onSelect(selectedKey);
      }

      this.setState({active: selectedKey});

    }

  },


  goToPrevious: function(index){
    var items = this.state.items;
    if(items[index].job){
      items[index].job = null;
    }else{
      items[index].project = null;
    }
    this.setState({items: items});
  },

  deleteItem: function(index){
    var items = this.state.items;
    items.splice(index, 1);
    this.setState({items: items});
  },

  render: function(){
    

    return (
           
        React.createElement(Nav, {bsStyle: "tabs", activeKey: this.state.active, onSelect: this.onSelect}, 
        
          this.props.items.map(function(item, index){
            var Tab = [];
            var title = '';
            var backButton= false;
            
            if (item.project){
              title = item.project;
              if (item.job){
                title += (" " +item.job); 
              }
              backButton = true;

            }else{
              title = "All Projects";
            }           
            Tab.push(React.createElement("span", null, " ", title));
            return(
              React.createElement(NavItem, {eventKey: index, key: index}, 
                
                  Tab
                
              )
              )
          }.bind(this)), 
        
        
        React.createElement(NavItem, {eventKey: this.props.items.length, key: this.props.items.length}, "Add New")
      )
    

    )
  }
})

module.exports = VizTabs;

};

var filter = function(require,module,exports){
var _ = require('underscore');

function Filter(data, fields){

  this.data = data;
  

  if (fields){
    this.fields = fields;
  }else{
    this.fields = [];
    for (field in data[0]){
      this.fields.push(field);
    }
  }

  
  this.filterByField = function(field, value){
    return _.filter(this.data, function(document){
      return ((document[field]).toString()).toLowerCase().indexOf(value.toLowerCase()) > -1;
    })
  },

  this.filter =  function(value){
    return _.filter(this.data, function(document){
      var valid = false;
      this.fields.map(function(field){
        valid = valid||(((document[field]).toString()).toLowerCase().indexOf(value.toLowerCase()) > -1);
      })

      return valid;

    }.bind(this))
  }



}


module.exports = Filter;

};

var filterinput = function(require,module,exports){
var React = require('react');
var Bootstrap = require('react-bootstrap');
var Input = Bootstrap.Input;
var Filter = require('./Filter.js');
var FilterInput = React.createClass({displayName: "FilterInput",

  getInitialState: function(){

    return {
      value: '',

    }


  },
  
  componentDidMount: function(){
    this.initializeFilter();
  },


  componentDidUpdate: function(prevProps, prevState){
    if(this.props !== prevProps){
      this.initializeFilter();
    }
  },

  initializeFilter: function(){
    this.filter = new Filter(this.props.data, this.props.fields);
  },


  handleChange: function(){
    var value = this.refs.input.getValue();

    this.setState({
      value: value
    })

    this.props.onChange(this.filter.filter(value));

  },


  render: function(){
    return(
        React.createElement(Input, {
          type: "text", 
          value: this.state.value, 
          placeholder: this.props.placeholder, 
          ref: "input", 
          onChange: this.handleChange})



      )
  }


})

module.exports = FilterInput

};

var util = function(require,module,exports){
module.exports = {
  host : "http://localhost/"
};

};