function unitsChartFacade(category) {

    this.unitsWorkerDetails = new unitsWorkerDetails(category);
    this.unitsDetails = new unitsDetails(category);
    this.unitsJobDetails = new unitsJobDetails(category);
    this.unitsAnnotationDetails = new unitsAnnotationDetails(category);
    this.pieChartIds = [{name:'domain'},{name:'format'},{field:'user_id',name:'created by',divName:"user"}];
    this.barChartGraph = ""

    if (category == '#twrex-structured-sentence_tab') {
        this.pieChartIds.push({field:'jobs',name:'jobs',divName:"optional1"});
        this.pieChartIds.push({field:'content.relation.noPrefix',name:'relation', divName:"optional2"});
        this.barChartGraph = new unitsBarChartGraph(category, this.unitsWorkerDetails, this.unitsJobDetails, this.unitsAnnotationDetails);
    } else if ((category == '#fullvideo_tab') || (category == '#fullvideo_tab')){
        this.pieChartIds.push({field:'inJobs',name:'jobs',divName:"optional1"});
        this.pieChartIds.push({field:'source',name:'source', divName:"optional2"});
        this.barChartGraph = new unitsBarChartGraph(category, this.unitsWorkerDetails, this.unitsJobDetails, this.unitsAnnotationDetails);
    } else if (category == '#job_tab') {
        this.pieChartIds.push({field:'status',name:'status',divName:"optional1"});
        this.pieChartIds.push({field:'type',name:'type', divName:"optional2"});
        this.pieChartIds.push({field:'softwareAgent_id',name:'platform', divName:"optional3"});
        this.barChartGraph = new jobsBarChartGraph(this.unitsDetails, this.unitsWorkerDetails, this.unitsAnnotationDetails);
    } else if (category == '#crowdagents_tab'){
        this.pieChartIds = []
        this.pieChartIds.push({field:'cache.flagged',name:'flagged',divName:"user"});
        this.pieChartIds.push({field:'softwareAgent_id',name:'platform',divName:"optional1"});
        this.pieChartIds.push({field:'country',name:'country', divName:"optional2"});
        this.barChartGraph = new workersBarChartGraph(this.unitsDetails, this.unitsJobDetails, this.unitsAnnotationDetails);
    }

    this.pieCharts = [];

    for (var pieChartIndex in this.pieChartIds){
        var field = this.pieChartIds[pieChartIndex]['name'];
        var divName = this.pieChartIds[pieChartIndex]['name'];
        if('field' in this.pieChartIds[pieChartIndex]){
            field = this.pieChartIds[pieChartIndex]['field'];
        }
        if('divName' in this.pieChartIds[pieChartIndex]){
            divName = this.pieChartIds[pieChartIndex]['divName'];
        }
        this.pieCharts.push(new pieChartGraph('',
            field, this.pieChartIds[pieChartIndex]['name'],divName, this.pieChartIds.length));
    }

    this.update = function(matchStr, sortStr){
        this.barChartGraph.updateBarChart(matchStr,sortStr);
        for (var pieChart in this.pieCharts){
            pieChart.updatePieChart(matchStr, sortStr);
        }
        //create the jobs pie chart
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

        }
        this.unitsWorkerDetails.createUnitsWorkerDetails();
        this.unitsAnnotationDetails.createUnitsAnnotationDetails();
        this.unitsJobDetails.createUnitsJobDetails();
    }
}