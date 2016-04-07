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