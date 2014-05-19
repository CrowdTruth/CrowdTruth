function unitsChartFacade(category, openModal, getSelection, updateSelection) {
    var categoryNameMap  = {'#twrex-structured-sentence_tab':'RelEx-structured sentence',
        '#fullvideo_tab':'Video',
        '#job_tab': 'job',
        '#crowdagents_tab':'worker',
        '#all_tab':'unit',
        '#drawing_tab': 'drawing',
        '#painting_tab': 'painting'
    };
    this.unitsWorkerDetails = new unitsWorkerDetails(category, categoryNameMap[category], openModal);
    this.unitsDetails = new unitsDetails(category, categoryNameMap[category], openModal);
    this.unitsJobDetails = new unitsJobDetails(category, categoryNameMap[category], openModal);
    this.unitsAnnotationDetails = new unitsAnnotationDetails(category, categoryNameMap[category], openModal);
    this.pieChartIds = [{name:'domain', tooltip:{'prefix' : 'in', 'suffix':'domain'} },
        {name:'format', tooltip:{'prefix' : 'in', 'suffix':'format' }},
        {field:'user_id', name:'created by', divName:"user", tooltip:{'prefix' : 'created by', 'suffix':'' }}];
    this.barChartGraph = ""
    this.labelCategory = "units"

    if (category == '#twrex-structured-sentence_tab') {
        this.pieChartIds.push({field:'jobs',name:'jobs',divName:"optional1", tooltip:{'prefix' : '', 'suffix':''}});
        this.pieChartIds.push({field:'content.relation.noPrefix',name:'relation', divName:"optional2", tooltip:{'prefix' : 'with', 'suffix':'relation'}});
        this.barChartGraph = new unitsBarChartGraph(category, categoryNameMap[category], this.unitsWorkerDetails, this.unitsJobDetails, this.unitsAnnotationDetails, getSelection, updateSelection);
    } else if ((category == '#fullvideo_tab')||(category == '#painting_tab') || (category == '#drawing_tab')){
        if (category == '#painting_tab') category = '#drawing_tab';
        this.pieChartIds.push({field:'jobs',name:'jobs',divName:"optional1", tooltip:{'prefix' : '', 'suffix':''}});
        this.pieChartIds.push({field:'source',name:'source', divName:"optional2", tooltip:{'prefix' : 'from', 'suffix':'source'}});
        this.barChartGraph = new unitsBarChartGraph(category, categoryNameMap[category], this.unitsWorkerDetails, this.unitsJobDetails, this.unitsAnnotationDetails, getSelection, updateSelection);
    } else if (category == '#job_tab') {
        this.labelCategory = "jobs"
        this.pieChartIds.push({field:'status',name:'status',divName:"optional1", tooltip:{'prefix' : '', 'suffix':''}});
        this.pieChartIds.push({field:'type',name:'type', divName:"optional2", tooltip:{'prefix' : 'with type', 'suffix':''}});
        this.pieChartIds.push({field:'softwareAgent_id',name:'platform', divName:"optional3", tooltip:{'prefix' : 'on', 'suffix':'platform'}});
        this.barChartGraph = new jobsBarChartGraph(this.unitsDetails, this.unitsWorkerDetails, this.unitsAnnotationDetails, getSelection, updateSelection);
    } else if (category == '#crowdagents_tab'){
        this.labelCategory = "workers"
        this.pieChartIds = []
        this.pieChartIds.push({field:'flagged',name:'flagged',divName:"user", tooltip:{'prefix' : '', 'suffix':''}});
        this.pieChartIds.push({field:'softwareAgent_id',name:'platform',divName:"optional1", tooltip:{'prefix' : 'on', 'suffix':'platform'}});
        this.pieChartIds.push({field:'country',name:'country', divName:"optional2", tooltip:{'prefix' : 'from', 'suffix':''}});
        this.barChartGraph = new workersBarChartGraph(this.unitsDetails, this.unitsJobDetails, this.unitsAnnotationDetails, getSelection, updateSelection);
    } else if (category == '#all_tab') {
        this.pieChartIds.push({field:'jobs',name:'jobs',divName:"optional1", tooltip:{'prefix' : '', 'suffix':''}});
        this.pieChartIds.push({field:'documentType',name:'document type',divName:"optional2", tooltip:{'prefix' : 'with', 'suffix':'document type'}});
        this.barChartGraph = new unitsBarChartGraph(category, categoryNameMap[category], this.unitsWorkerDetails, this.unitsJobDetails, this.unitsAnnotationDetails, getSelection, updateSelection);
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
        var tooltip = this.pieChartIds[pieChartIndex]['tooltip'];
        tooltip['label'] = this.labelCategory;
        this.pieCharts.push(new pieChartGraph(tooltip,'',
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

        for (var pieChartIndex in this.pieCharts){
            this.pieCharts[pieChartIndex].createPieChart(matchStr);

        }
        this.unitsWorkerDetails.createUnitsWorkerDetails();
        this.unitsAnnotationDetails.createUnitsAnnotationDetails();
        this.unitsJobDetails.createUnitsJobDetails();
    }
}