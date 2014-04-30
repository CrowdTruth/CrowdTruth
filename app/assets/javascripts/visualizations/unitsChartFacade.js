function unitsChartFacade() {
    this.unitsWorkerDetails = new unitsWorkerDetails();
    this.unitsJobDetails = new unitsJobDetails();
    this.unitsAnnotationDetails = new unitsAnnotationDetails();
    this.barChartGraph = new barChartGraph(this.unitsWorkerDetails, this.unitsJobDetails, this.unitsAnnotationDetails);
    this.pieChartIds = [{name:'domain'},{name:'format'},{field:'user_id',name:'created by',divName:"user"},{field:'content.relation.noPrefix',name:'relation'}];
    this.pieCharts = [];
    jobsPieChart = new pieChartGraph('','','in jobs','jobs');


    for (var pieChartIndex in this.pieChartIds){
        var field = this.pieChartIds[pieChartIndex]['name'];
        var divName = this.pieChartIds[pieChartIndex]['name'];
        if('field' in this.pieChartIds[pieChartIndex]){
            field = this.pieChartIds[pieChartIndex]['field'];
        }
        if('divName' in this.pieChartIds[pieChartIndex]){
            divName = this.pieChartIds[pieChartIndex]['divName'];
        }
        this.pieCharts.push(new pieChartGraph('match[documentType][]=twrex-structured-sentence',
            field, this.pieChartIds[pieChartIndex]['name'],divName));
    }
    this.createJobsPieChart = function(matchStr){
        //get the count for units in jobs
        $.getJSON('/api/analytics/aggregate/?' + matchStr + '&sort[created_at]=1' + '&match[cache.jobs.count][>]=0',
                  function(data) {
            var chartData = [];
            chartData.push(['in jobs', data['count']]);
            $.getJSON('/api/analytics/aggregate/?' + matchStr + '&sort[created_at]=1' + '&match[cache.jobs.count]=0',
                function(data) {
                chartData.push(['not in jobs', data['count']]);
                    console.dir(jobsPieChart);
                jobsPieChart.drawPieChart(chartData);
            });
           console.dir(data);
        });
    }

    this.update = function(matchStr, sortStr){
        this.unitChartContr.updateBarChart(matchStr,sortStr);
        for (var pieChart in this.pieCharts){
            pieChart.updatePieChart(matchStr, sortStr);
        }
        //create the jobs pie chart
        this.createJobsPieChart(matchStr);
        this.unitsWorkerDetails.createUnitsWorkerDetails();
        this.unitsAnnotationDetails.createUnitsAnnotationDetails();
        this.unitsJobDetails.createUnitsJobDetails();

    }


    this.init = function(matchStr, sortStr){
        this.barChartGraph.createBarChart(matchStr, sortStr);
        //console.dir(this.pieCharts);

        for (var pieChartIndex in this.pieCharts){
           // console.dir(this.pieCharts[pieChartIndex]);
            //console.dir(matchStr);
            this.pieCharts[pieChartIndex].createPieChart(matchStr);

            console.dir( this.pieCharts[pieChartIndex]);
        }
        this.createJobsPieChart(matchStr);
        this.unitsWorkerDetails.createUnitsWorkerDetails();
        this.unitsAnnotationDetails.createUnitsAnnotationDetails();
        this.unitsJobDetails.createUnitsJobDetails();
    }
}