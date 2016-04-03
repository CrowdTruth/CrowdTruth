var React = require('react');
var Bootstrap = require('react-bootstrap');
var Row = Bootstrap.Row;
var Label = Bootstrap.Label;
var Col=Bootstrap.Col;

var JobVizHeader = React.createClass({

  

  render: function(){

    var toHHMMSS = function (sec) {
        var sec_num = parseInt(sec);

        var days = Math.floor(sec_num  / 86400);
        var hours   = Math.floor((sec_num -(days*86400)) / 3600);
        var minutes = Math.floor((sec_num -(days*86400) - (hours * 3600)) / 60);
        var seconds = sec_num -(days*86400) -(hours * 3600) - (minutes * 60);

        if (hours   < 10) {hours   = "0"+hours;}
        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}
        var time    =days+'days'+ ':' + hours+':'+minutes+':'+seconds;
        return time;
    }


    return (
      <Row className='bs-callout bs-callout-success'>
        <HeaderItem size={2} itemName={'User'} value={this.props.job.jobInfo.user_id} />
        <HeaderItem size={2} itemName={'Real cost'} value={'$' +this.props.job.jobInfo.realCost} />
        <HeaderItem size={2} itemName={'Running time'} value={this.props.job.jobInfo.runningTimeInSeconds?(toHHMMSS(this.props.job.jobInfo.runningTimeInSeconds)): Math.floor(this.props.job.jobInfo.completion*100) + '% completed' } />
        <HeaderItem size={2} itemName={'Status'} value={this.props.job.jobInfo.status} />
        <HeaderItem size={2} itemName={'Created at'} value={(new Date(this.props.job.jobInfo.created_at)).toDateString()} />
        <HeaderItem size={2} itemName={'Format'} value={this.props.job.jobInfo.format} />
      </Row>

      )

  }

});

var HeaderItem = React.createClass({
  render: function(){
    var val = this.props.value;
    if (val.indexOf('undefined') > -1){
      val = 'Value not found';
    }
    return (
      <Col xs={this.props.size}>
        
        <Row>
          <h3 className={'text-center'}><Label bsStyle='danger'>{val}</Label></h3>
        </Row>
        <Row>
          <div className={'text-center'}>{this.props.itemName}</div>
        </Row>
      </Col>
      )
  }

})
module.exports = JobVizHeader