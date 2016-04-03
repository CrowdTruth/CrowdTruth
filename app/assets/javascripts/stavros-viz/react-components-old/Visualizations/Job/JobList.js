var React = require('react');
var Input = require('react-bootstrap').Input;
var ReactTable = require('react-bootstrap-table');
var BootstrapTable = ReactTable.BootstrapTable;
var TableHeaderColumn = ReactTable.TableHeaderColumn;
var TableDataSet=ReactTable.TableDataSet;
var _ = require('underscore');
var Row = require('react-bootstrap').Row;
var Col = require('react-bootstrap').Col;

var JobList = React.createClass({

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
    this.tables.workersTable= <BootstrapTable height={this.state.height+'px'} data={props.job['workers']} key={1} columnFilter={true} selectRow={this.getSelectedRowProps(props,'workers')}>
          {
            this.getColumns('workers')
          }
        </BootstrapTable>
    
    this.tables.unitsTable = <BootstrapTable height={this.state.height+'px'} data={props.job['units']} key={2} columnFilter={true} selectRow={this.getSelectedRowProps(props,'units')}>
          {
            this.getColumns('units')
          }
        </BootstrapTable>

    this.tables.workerunitsTable = <BootstrapTable height={this.state.height+'px'} data={props.job['workerunits']} key={3} columnFilter={true} selectRow={this.getSelectedRowProps(props,'workerunits')}>
          {
            this.getColumns('workerunits')
          }
        </BootstrapTable>

    this.tables.workersSelected = <BootstrapTable height={this.state.height+'px'} data={this.getSelected(props, 'workers')} key={4} columnFilter={true}>
          {
            this.getColumns('workers')
          }
        </BootstrapTable>

    this.tables.unitsSelected = <BootstrapTable height={this.state.height+'px'} data={this.getSelected(props, 'units')} key={5} columnFilter={true}>
          {
            this.getColumns('units')
          }
        </BootstrapTable>

    this.tables.workerunitsSelected = <BootstrapTable height={this.state.height+'px'} data={this.getSelected(props, 'workerunits')} key={6} columnFilter={true}>
          {
            this.getColumns('workerunits')
          }
        </BootstrapTable>

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
      result.push(<TableHeaderColumn
                    dataField={col.field}                   
                    dataKey={col.field}
                    isKey={col.field=="_id"?true:false}
                    dataSort={true}
                    dataFormat={col.render}
                    key={index}> 
                    {col.name} </TableHeaderColumn>)
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
      <Row className='white'>
        <Row>
        <Col xs={6}>
          <Input type='select' ref="typeInput" onClick={this.handleTypeChange}>
            <option value={'workers'}> Workers </option>
            <option value={'units'}> Units </option>
            <option value={'workerunits'}> Annotations </option>
          </Input>
        </Col>
        <Col xs={6}>
          <Input type='checkbox' label='Show only selected' onClick={this.toggleSelected} />

        </Col>
        </Row>
        {this.state.type=='workers'&& !this.state.showSelected? this.tables.workersTable: null}
        {this.state.type=='units' && !this.state.showSelected ? this.tables.unitsTable: null}
        {this.state.type=='workerunits' && !this.state.showSelected? this.tables.workerunitsTable: null}

        {this.state.type=='workers'&& this.state.showSelected? this.tables.workersSelected: null}
        {this.state.type=='units' && this.state.showSelected? this.tables.unitsSelected: null}
        {this.state.type=='workerunits' && this.state.showSelected? this.tables.workerunitsSelected: null}
      </Row>
      )

  }

});


module.exports = JobList;