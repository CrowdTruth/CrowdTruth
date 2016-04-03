var React = require('react');
var ReactDOM = require("react-dom");
var Router = require('react-router');
var VizContainer = require('./Visualizations/VizContainer.js');
var routes = require('./Visualizations/VizContainerNew.js');
//blocks = Blocks( $('.boxes'));

Router.run(routes,  function(Root){
  ReactDOM.render(<Root/>, document.getElementById('app'))
});
//React.render(<VizContainerNew/>, document.getElementById('app'));