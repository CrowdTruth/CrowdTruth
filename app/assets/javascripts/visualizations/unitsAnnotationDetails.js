function unitsAnnotationDetails(category, categoryName, openModal) {

    var urlBase = "/api/analytics/piegraph/?match[documentType][]=workerunit&";
    var annotationDivs = [];
    var queryField = 'unit_id';
    var categoryPrefix = 'in';
    var querySettings = {'metricCateg':'units', metricFilter:['withSpam', 'withoutSpam'], aggName:"aggUnits", metricFields:['max_relation_Cos'],
        metricName:['clarity'],
        metricTooltip : [{key:'CrowdTruth Average Unit Clarity', value:"the value is defined as the maximum unit annotation score achieved on any annotation for that unit. " +
            "High agreement over the annotations is represented by high cosine scores, indicating a clear unit. " +
            "Click to select/deselect."}],
        metricSuffix: ".avg"}

    if (category == '#job_tab'){
        queryField = 'job_id'
        querySettings = {
            annotationMetricFields:['top_ann_cond_prob', 'cond_prob', 'annot_ambiguity', 'cond_prob_minus_rel_prob', 'mutual_info', 'annot_prob', 'annot_top_prob',  'annot_freq', 'annot_clarity'],
            annotationMetricNames:["max P(Rc|Rr-Top)", "max P(Rc|Rr)", "max Rr->Rc", "max P(Rc|Rr)-P(Rc)",  "max I(Rc,Rd)", "P(R)","P(R-Top)", "|S:R|", "RClar"],
            pivotTableFields :['top_ann_cond_prob', 'cond_prob', 'rel_similarity', 'cond_prob_minus_rel_prob', 'mutual_info_dict'],
            pivotTableNames :["P(Rc|Rr-Top)", 'P(Rc|Rr)', 'Rr->Rc', 'P(Rc|Rr)-P(Rc)', 'I(Rc,Rd)']
            }
    } else if (category == '#crowdagents_tab'){
        queryField = 'crowdAgent_id'
        querySettings = {'metricCateg':'workers',metricFilter:['withoutFilter', 'withFilter'], aggName:'aggWorkers', metricFields:['avg_worker_agreement','worker_cosine'],
            metricName:['agreement','cosine'], metricSuffix: "",
            metricTooltip : [{key:'CrowdTruth Average Worker Agreement score', value:'Higher scores indicate better quality workers. Click to select/deselect.'},
                {key:'CrowdTruth Average Cosine Similarity', value:'Higher Scores indicate better quality workers. Click to select/deselect.'}]}
        categoryPrefix = 'from';
    }

    var currentSelection = [];
    var currentSelectionInfo = {};
    var unitsAnnotationInfo = {};
    var activeSelectedPlatform = "";
    var activeSelectedType = "";
    var pieChart = "";

    var callback = function callback($this){
        var img = $this.renderer.image('/assets/check_mark.png',$this.chartWidth-60,15,19,14);
        img.add();
        img.css({'cursor':'pointer'});
        img.attr({'title':'Pop out chart'});
        img.attr("data-toggle","tooltip");
        img.attr("title", "Click to see results without low quality annotations");
        img.on('click',function(){
            alert("under construction");
            // prcessing after image is clicked
        });
        var img = $this.renderer.image('/assets/cross.png',$this.chartWidth-90,16,19,12);
        img.add();
        img.css({'cursor':'pointer'});
        img.attr({'title':'Pop out chart'});
        img.attr("data-toggle","tooltip");
        img.attr("title", "Click to see results with low quality annotations");
        img.on('click',function(){
            alert("under construction");
            // prcessing after image is clicked
        });
    }


    var  getJobHeatMapData = function(platform, type) {

        var series = {};
        activeSelectedPlatform = platform;
        activeSelectedType = type;

        var annotationsURL = '/api/v1/?field[documentType][]=job&'
        for (var indexUnits in currentSelection) {
            annotationsURL += '&field[_id][]=' + currentSelection[indexUnits];
        }

        annotationsURL += '&field[softwareAgent_id][]=' + platform ;
        annotationsURL += '&field[type][]=' + type;

        annotationsURL += '&only[]=results&only[]=metrics.units&only[]=metrics.annotations' +
            '&only[]=metrics.pivotTables.annotations';

        //get the list of workers for this units
        $.getJSON(annotationsURL, function (data) {
            var heatMapData = {};
            heatMapData['annotations'] = {};
            heatMapData['avg_clarity'] = {};
            heatMapData['annotationMetric'] = {};
            heatMapData['pivotTable'] = {};
            for (var heatMapCateg in heatMapData){
                heatMapData[heatMapCateg]['min'] = Number.MAX_VALUE;
                heatMapData[heatMapCateg]['max'] = Number.MIN_VALUE;
                heatMapData[heatMapCateg]['categoryY'] = [];
                heatMapData[heatMapCateg]['indexY'] = 0;
                heatMapData[heatMapCateg]['spam'] = [];
                heatMapData[heatMapCateg]['nonSpam'] = [];
                heatMapData[heatMapCateg]['diff'] = [];
            }

            var indexX = 0;

            var allUnits = data[0]['results']['withoutSpam'];
            var mTaksKeys = Object.keys(allUnits[Object.keys(allUnits)[0]]);
            var categories = Object.keys(allUnits[Object.keys(allUnits)[0]][mTaksKeys[0]]);

            for (var annotationIter in categories) {
                var annotation = categories[annotationIter];
                for (var heatMapCateg in heatMapData){
                    heatMapData[heatMapCateg]['indexY'] = 0;
                }
                for (var iterPivotTable in querySettings.pivotTableFields){
                    var pivotTable = querySettings.pivotTableFields[iterPivotTable];
                    if (!(pivotTable in heatMapData['pivotTable'] )) {
                        heatMapData['pivotTable'][pivotTable] = {};
                        heatMapData['pivotTable'][pivotTable]['min'] = Number.MAX_VALUE;
                        heatMapData['pivotTable'][pivotTable]['max'] = Number.MIN_VALUE;
                        heatMapData['pivotTable'][pivotTable]['categoryY'] = [];
                        heatMapData['pivotTable'][pivotTable]['indexY'] = 0;
                        heatMapData['pivotTable'][pivotTable]['spam'] = [];
                        heatMapData['pivotTable'][pivotTable]['nonSpam'] = [];
                        heatMapData['pivotTable'][pivotTable]['diff'] = [];
                    }
                    heatMapData['pivotTable'][pivotTable]['indexY'] = 0;
                }
                for(var iterData in data) {
                    var jobID =  data[iterData]['_id'];
                    var arrayID = jobID.split("/");
                    var jobNb = arrayID[arrayID.length - 1];
                    //get annotation data
                    for(var unitID in data[iterData]['results']['withSpam']){
                        for (var taskIter in mTaksKeys) {
                            var task = mTaksKeys[taskIter];
                            if (task == 'avg') continue;

                            var arrayUnit = unitID.split("/");
                            var unitNb = arrayUnit[arrayUnit.length - 1];

                            if(indexX == 0) {

                                if ( mTaksKeys.length > 1) {
                                    heatMapData['annotations']['categoryY'].push( 'unit ' + unitNb + '(' + task + ')' + " in job " + jobNb);
                                    heatMapData['avg_clarity']['categoryY'].push( 'unit ' + unitNb + '(' + task + ')' + " in job " + jobNb);
                                } else {
                                    heatMapData['annotations']['categoryY'].push( 'unit ' + unitNb + " in job " + jobNb);
                                    heatMapData['avg_clarity']['categoryY'].push( 'unit ' + unitNb + " in job " + jobNb);
                                }

                                var spamValue = data[iterData]['metrics']['units']['withSpam'][unitID][task]['max_relation_Cos'];
                                var nonSpamValue = data[iterData]['metrics']['units']['withoutSpam'][unitID][task]['max_relation_Cos'];
                                heatMapData['avg_clarity']['spam'].push([indexX, heatMapData['avg_clarity']['indexY'],spamValue]);
                                heatMapData['avg_clarity']['nonSpam'].push([indexX, heatMapData['avg_clarity']['indexY'],nonSpamValue]);
                                heatMapData['avg_clarity']['diff'].push([indexX, heatMapData['avg_clarity']['indexY'],spamValue-nonSpamValue]);
                                if(  heatMapData['avg_clarity']['max'] < spamValue) heatMapData['avg_clarity']['max'] = spamValue;
                                if(  heatMapData['avg_clarity']['min'] > nonSpamValue) heatMapData['avg_clarity']['min'] = nonSpamValue;
                                if(  heatMapData['avg_clarity']['min'] > spamValue-nonSpamValue) heatMapData['avg_clarity']['min'] = spamValue-nonSpamValue;
                                heatMapData['avg_clarity']['indexY'] += 1;
                            }

                            var spamValue = data[iterData]['results']['withSpam'][unitID][task][annotation];
                            var nonSpamValue = data[iterData]['results']['withoutSpam'][unitID][task][annotation];
                            heatMapData['annotations']['spam'].push([indexX, heatMapData['annotations']['indexY'],spamValue]);
                            heatMapData['annotations']['nonSpam'].push([indexX, heatMapData['annotations']['indexY'],nonSpamValue]);
                            heatMapData['annotations']['diff'].push([indexX, heatMapData['annotations']['indexY'],spamValue-nonSpamValue]);
                            if(  heatMapData['annotations']['max'] < spamValue) heatMapData['annotations']['max'] = spamValue;
                            if(  heatMapData['annotations']['min'] > nonSpamValue) heatMapData['annotations']['min'] = nonSpamValue;
                            if(  heatMapData['annotations']['min'] > spamValue-nonSpamValue) heatMapData['annotations']['min'] = spamValue-nonSpamValue;
                            heatMapData['annotations']['indexY'] += 1;
                        }
                    }

                    for(var iterPivotTable in querySettings.pivotTableFields){
                        var pivotTable = querySettings.pivotTableFields[iterPivotTable];
                        console.dir(pivotTable)
                        for (var annotationIterY in categories) {
                            var annotY = categories[annotationIterY];
                            if(indexX == 0) {
                                heatMapData['pivotTable'][pivotTable]['categoryY'].push( annotY + " in job " + jobNb);
                            }
                            console.dir(annotY)

                            var spamValue = 0;
                            var nonSpamValue = 0;
                            if (annotation in data[iterData]['metrics']['pivotTables']['annotations']['withSpam'][pivotTable]) {
                                var spamValue = data[iterData]['metrics']['pivotTables']['annotations']['withSpam'][pivotTable][annotY][annotation];
                                var nonSpamValue = data[iterData]['metrics']['pivotTables']['annotations']['withoutSpam'][pivotTable][annotY][annotation];
                            }
                            console.dir(spamValue)
                            console.dir(nonSpamValue)



                            heatMapData['pivotTable'][pivotTable]['spam'].push([indexX, heatMapData['pivotTable'][pivotTable]['indexY'],spamValue.toFixed(3)]);
                            heatMapData['pivotTable'][pivotTable]['nonSpam'].push([indexX, heatMapData['pivotTable'][pivotTable]['indexY'],nonSpamValue.toFixed(3)]);
                            heatMapData['pivotTable'][pivotTable]['diff'].push([indexX, heatMapData['pivotTable'][pivotTable]['indexY'],(spamValue-nonSpamValue).toFixed(3)]);
                            if(  heatMapData['pivotTable'][pivotTable]['max'] < spamValue) heatMapData['pivotTable'][pivotTable]['max'] = spamValue;
                            if(  heatMapData['pivotTable'][pivotTable]['min'] > nonSpamValue) heatMapData['pivotTable'][pivotTable]['min'] = nonSpamValue;
                            if(  heatMapData['pivotTable'][pivotTable]['min'] > spamValue-nonSpamValue) heatMapData['pivotTable'][pivotTable]['min'] = spamValue-nonSpamValue;
                            heatMapData['pivotTable'][pivotTable]['indexY'] += 1;
                        }
                    }
                    if(indexX == 0) {
                        for(var iterMetrics in querySettings.annotationMetricFields){
                            var metricField = querySettings.annotationMetricFields[iterMetrics];

                            for (var annotationIterY in categories) {
                                var annotY = categories[annotationIterY];
                                if (!(metricField in heatMapData['annotationMetric'] )) {
                                    heatMapData['annotationMetric'][metricField] = {};
                                    heatMapData['annotationMetric'][metricField]['min'] = Number.MAX_VALUE;
                                    heatMapData['annotationMetric'][metricField]['max'] = Number.MIN_VALUE;
                                    heatMapData['annotationMetric'][metricField]['categoryY'] = [];
                                    heatMapData['annotationMetric'][metricField]['indexY'] = 0;
                                    heatMapData['annotationMetric'][metricField]['spam'] = [];
                                    heatMapData['annotationMetric'][metricField]['nonSpam'] = [];
                                    heatMapData['annotationMetric'][metricField]['diff'] = [];
                                }
                                heatMapData['annotationMetric'][metricField]['categoryY'].push( annotY + " in job " + jobNb);

                                var spamValue = data[iterData]['metrics']['annotations']['withSpam'][metricField][annotY];
                                var nonSpamValue = data[iterData]['metrics']['annotations']['withoutSpam'][metricField][annotY];
                                heatMapData['annotationMetric'][metricField]['spam'].push([indexX, heatMapData['annotationMetric'][metricField]['indexY'],spamValue.toFixed(3)]);
                                heatMapData['annotationMetric'][metricField]['nonSpam'].push([indexX, heatMapData['annotationMetric'][metricField]['indexY'],nonSpamValue.toFixed(3)]);
                                heatMapData['annotationMetric'][metricField]['diff'].push([indexX, heatMapData['annotationMetric'][metricField]['indexY'],(spamValue-nonSpamValue).toFixed(3)]);
                                if(  heatMapData['annotationMetric'][metricField]['max'] < spamValue) heatMapData['annotationMetric'][metricField]['max'] = spamValue;
                                if(  heatMapData['annotationMetric'][metricField]['min'] > nonSpamValue) heatMapData['annotationMetric'][metricField]['min'] = nonSpamValue;
                                if(  heatMapData['annotationMetric'][metricField]['min'] > spamValue-nonSpamValue) heatMapData['annotationMetric'][metricField]['min'] = spamValue-nonSpamValue;
                                heatMapData['annotationMetric'][metricField]['indexY'] += 1;
                            }
                        }

                    }

                }
                indexX += 1;
            }

            var title = 'Aggregated view of ' + ' annotations of ' +  currentSelection.length   +  ' Selected ' + categoryName + '(s)'
            var width = (3*(($('.maincolumn').width() - 50)/5))
            var tooltip = function () {
                return '<b>' + this.series.xAxis.categories[this.point.x] + '</b> got <b>' +
                    this.point.value + '</b> annotations ' + categoryPrefix + ' <b>' + this.series.yAxis.categories[this.point.y] + '</b>';
            }
            var min = heatMapData['annotations']['min'];
            var max = heatMapData['annotations']['max'];
            var height = 16 *  heatMapData['annotations']['categoryY'].length;
            heatMapGraph(categories,  heatMapData['annotations']['categoryY'],  heatMapData['annotations']['nonSpam'], title ,
                'High quality annotations.', min, max, 'annotationsAfter_div', width, height, tooltip, true);
            annotationDivs.push('annotationsAfter_div');
            heatMapGraph(categories,  heatMapData['annotations']['categoryY'],  heatMapData['annotations']['spam'], title,
                'All annotations.', min, max, 'annotationsBefore_div', width, height, tooltip, true);
            annotationDivs.push('annotationsBefore_div');
            heatMapGraph(categories,  heatMapData['annotations']['categoryY'],  heatMapData['annotations']['diff'], title,
                'Low quality annotations.', min, max, 'annotationsDiff_div',width, height, tooltip, true);
            annotationDivs.push('annotationsDiff_div');


            var title = "Unit clarity";
            var width = ((($('.maincolumn').width() - 50)/5))

            var tooltip = function () {
                return '<p class = "pieDivGraphs">' + this.series.yAxis.categories[this.point.y] + ', ' + this.series.xAxis.categories[this.point.x] + ' score:<b>' +
                    this.point.value + '</p>';
            }
            var height = 16 * heatMapData['avg_clarity']['categoryY'].length;
            var min = heatMapData['avg_clarity']['min'];
            var max = heatMapData['avg_clarity']['max'];
            heatMapGraph(['clarity'], heatMapData['avg_clarity']['categoryY'], heatMapData['avg_clarity']['nonSpam'], title ,
                'After filtering low quality', min, max, 'unitsMetricAfter_'+0+'_div',width, height, tooltip, false);
            annotationDivs.push('unitsMetricAfter_'+0+'_div');
            heatMapGraph(['clarity'], heatMapData['avg_clarity']['categoryY'], heatMapData['avg_clarity']['spam'], title ,
                'Before filtering low quality', min, max, 'unitsMetricBefore_'+0+'_div', width, height, tooltip, false);
            annotationDivs.push('unitsMetricBefore_'+0+'_div');
            heatMapGraph(['clarity'], heatMapData['avg_clarity']['categoryY'], heatMapData['avg_clarity']['diff'], title ,
                'low - high quality metrics', min, max, 'unitsMetricDiff_'+0+'_div', width, height, tooltip,false);
            annotationDivs.push('unitsMetricDiff_'+0+'_div');

            var iterMetrics = 0;
            for (iterMetrics; iterMetrics < querySettings.pivotTableFields.length; iterMetrics++ ){
                var metricField = querySettings.annotationMetricFields[iterMetrics];
                var pivotTable = querySettings.pivotTableFields[iterMetrics];
                var title = querySettings.pivotTableNames[iterMetrics] + ' of annotations for ' +  currentSelection.length   +  ' Selected ' + categoryName + '(s)'
                var width = (3*(($('.maincolumn').width() - 50)/5))
                var tooltip = function () {
                    return '<p class = "pieDivGraphs">' + this.series.yAxis.categories[this.point.y] + ' - ' + this.series.xAxis.categories[this.point.x] + ' score:<b>' +
                        this.point.value + '</p>';
                }
                var min = heatMapData['pivotTable'][pivotTable]['min'];
                var max =heatMapData['pivotTable'][pivotTable]['max'];
                var height = 16 *  heatMapData['pivotTable'][pivotTable]['categoryY'].length;
                heatMapGraph(categories,  heatMapData['pivotTable'][pivotTable]['categoryY'],  heatMapData['pivotTable'][pivotTable]['nonSpam'], title ,
                    'After filtering low quality', min, max, 'pivotTableAfter_'+iterMetrics+'_div', width, height, tooltip, true);
                annotationDivs.push('pivotTableAfter_'+iterMetrics+'_div');
                heatMapGraph(categories,  heatMapData['pivotTable'][pivotTable]['categoryY'],  heatMapData['pivotTable'][pivotTable]['spam'], title,
                    'Before filtering low quality', min, max, 'pivotTableBefore_'+ iterMetrics +'_div', width, height, tooltip, true);
                annotationDivs.push('pivotTableBefore_'+iterMetrics+'_div');
                heatMapGraph(categories, heatMapData['pivotTable'][pivotTable]['categoryY'],  heatMapData['pivotTable'][pivotTable]['diff'], title,
                    'low - high quality metrics', min, max, 'pivotTableDiff_'+ iterMetrics +'_div',width, height, tooltip, true);
                annotationDivs.push('pivotTableDiff_'+ iterMetrics +'_div');


                var title = querySettings.annotationMetricNames[iterMetrics];
                var width = ((($('.maincolumn').width() - 50)/5))


                var height = 16 * heatMapData['annotationMetric'][metricField]['categoryY'].length;
                var min = heatMapData['annotationMetric'][metricField]['min'];
                var max = heatMapData['annotationMetric'][metricField]['max'];
                heatMapGraph(title, heatMapData['annotationMetric'][metricField]['categoryY'], heatMapData['annotationMetric'][metricField]['nonSpam'], title ,
                    'After filtering low quality', min, max, 'annotationsMetricAfter_'+ iterMetrics +'_div',width, height, tooltip, false);
                annotationDivs.push('annotationsMetricAfter_'+iterMetrics+'_div');
                heatMapGraph(title, heatMapData['annotationMetric'][metricField]['categoryY'], heatMapData['annotationMetric'][metricField]['spam'], title ,
                    'Before filtering low quality', min, max, 'annotationsMetricBefore_'+ iterMetrics+'_div', width, height, tooltip, false);
                annotationDivs.push('annotationsMetricBefore_'+iterMetrics+'_div');
                heatMapGraph(title, heatMapData['annotationMetric'][metricField]['categoryY'],heatMapData['annotationMetric'][metricField]['diff'], title ,
                    'low - high quality metrics', min, max, 'annotationsMetricDiff_'+ iterMetrics +'_div', width, height, tooltip, false);
                annotationDivs.push('annotationsMetricDiff_'+iterMetrics+'_div');
            }
            var noMetrics = querySettings.annotationMetricFields.length - iterMetrics;
            var middleIter = (noMetrics)/2 + iterMetrics - 1;
            for (iterMetrics; iterMetrics < querySettings.annotationMetricFields.length;iterMetrics++ ){
                var metricField = querySettings.annotationMetricFields[iterMetrics];
                var title = querySettings.annotationMetricNames[iterMetrics];
                var width = ((($('.maincolumn').width() - 50)/(noMetrics + 1)))
                console.dir(width);
                var height = 16 * heatMapData['annotationMetric'][metricField]['categoryY'].length;
                var min = heatMapData['annotationMetric'][metricField]['min'];
                var max = heatMapData['annotationMetric'][metricField]['max'];
                var show = false;
                if (iterMetrics == middleIter ) {
                    show = true;
                    var width = (2*(($('.maincolumn').width() - 50)/(noMetrics + 1)))
                }
                heatMapGraph(title, heatMapData['annotationMetric'][metricField]['categoryY'], heatMapData['annotationMetric'][metricField]['nonSpam'], title ,
                    'After filtering low quality', min, max, 'annotationsMetricAfter_'+ iterMetrics +'_div',width, height, tooltip, show);
                annotationDivs.push('annotationsMetricAfter_'+iterMetrics+'_div');
                heatMapGraph(title, heatMapData['annotationMetric'][metricField]['categoryY'], heatMapData['annotationMetric'][metricField]['spam'], title ,
                    'Before filtering low quality', min, max, 'annotationsMetricBefore_'+ iterMetrics+'_div', width, height, tooltip, show);
                annotationDivs.push('annotationsMetricBefore_'+iterMetrics+'_div');
                heatMapGraph(title, heatMapData['annotationMetric'][metricField]['categoryY'],heatMapData['annotationMetric'][metricField]['diff'], title ,
                    'low - high quality metrics', min, max, 'annotationsMetricDiff_'+ iterMetrics +'_div', width, height, tooltip, show);
                annotationDivs.push('annotationsMetricDiff_'+iterMetrics+'_div');
            }



         });


    }
    var getHeatMapData = function(platform, type) {
        //url to get the annotation

        if (category == '#job_tab'){
            getJobHeatMapData(platform, type);
            return;
        }
        var categories = [];
        var series = {};
        activeSelectedPlatform = platform;
        activeSelectedType = type;

        var annotationsURL = urlBase;
        annotationsURL += 'match[softwareAgent_id][]=' + platform ;
        if (type != ""){
            annotationsURL += '&match[type][]=' + type;
        }
        annotationsURL += '&project[annotationVector]=annotationVector&project[spam]=spam&project[job_id]=job_id&group=' + queryField +
            '&push[annotationVector]=annotationVector&push[spam]=spam&push[job_id]=job_id';

        //get the list of workers for this units
        $.getJSON(annotationsURL, function (data) {
            var urlJobsInfo =  '/api/v1/?field[documentType]=job&'

            for (var dataIter in data){
                var fieldID = data[dataIter]['_id'];
                series[fieldID] = {};
                for (var iterMetric in querySettings['metricFields']){
                    var metricName = querySettings['metricFields'][iterMetric]

                    var metricFilters = querySettings['metricFilter']
                    for (var iterFilter in metricFilters) {
                        urlJobsInfo += '&only[]=metrics.' + querySettings['metricCateg'] + '.' +
                            metricFilters[iterFilter] + '.' + fieldID + querySettings['metricSuffix'] + '.' + metricName ;
                    }
                }
                for (var iterJob in data[dataIter]['job_id']){
                    var jobID = data[dataIter]['job_id'][iterJob]
                   /* urlJobsInfo += 'field[_id][]=' + jobID + '&';
                    for (var iterMetric in querySettings['metricFields']){
                        var metricName = querySettings['metricFields'][iterMetric]

                        var metricFilters = querySettings['metricFilter']
                        for (var iterFilter in metricFilters) {
                            urlJobsInfo += '&only[]=metrics.' + querySettings['metricCateg'] + '.' +
                                metricFilters[iterFilter] + '.' + fieldID + '.' + metricName + querySettings['metricSuffix'];
                        }
                    }*/

                    var dataCategory = 'spam';
                    if (!(jobID in series[fieldID])) {
                        series[fieldID][jobID] = {}
                    }
                    if (data[dataIter]['spam'][iterJob] == false) dataCategory = 'nonSpam';

                    var annVectors = data[dataIter]['annotationVector'][iterJob]
                    for (var annTaskKey in annVectors) {
                        if (categories.length == 0) {
                            categories = Object.keys(annVectors[annTaskKey]);
                        }
                        var missingKeys =  false;
                        if (!(dataCategory in series[fieldID][jobID])) {
                            missingKeys = true;
                            series[fieldID][jobID]['spam'] = {}
                            series[fieldID][jobID]['nonSpam'] = {}
                        }

                        for (var annKey in annVectors[annTaskKey]) {
                            if (missingKeys){
                                series[fieldID][jobID]['spam'][annKey] = 0
                                series[fieldID][jobID]['nonSpam'][annKey] = 0
                                series[fieldID][jobID][dataCategory][annKey] = annVectors[annTaskKey][annKey];
                            } else {
                                series[fieldID][jobID][dataCategory][annKey] += annVectors[annTaskKey][annKey];
                            }
                        }
                    }
                }
            }
            var categoriesY = [];
            var heatMapDataAfter = []
            var heatMapDataBefore = []
            var heatMapDataDiff = []
            var indexX = 0;
            var min = Number.MAX_VALUE;
            var max = Number.MIN_VALUE;
            var indexY = 0;
            for (var annotation in categories) {
                indexY = 0;
                for (var worker in series) {
                    for (var job in series[worker]){
                        var arrayUnit = worker.split("/");
                        var workerID = arrayUnit[arrayUnit.length - 1];
                        var arrayUnit = job.split("/");
                        var jobID = arrayUnit[arrayUnit.length - 1];
                        if(indexX == 0) {
                            if ( workerID == jobID) {
                                categoriesY.push(categoryName + ' ' + workerID  );
                            } else {
                                categoriesY.push(categoryName + ' ' + workerID + ' in job ' + jobID );
                            }

                        }
                        var afterData =  series[worker][job]['nonSpam'][categories[annotation]];
                        var beforeData =  series[worker][job]['spam'][categories[annotation]];
                        heatMapDataAfter.push([indexX, indexY, afterData]);
                        heatMapDataBefore.push([indexX, indexY, afterData + beforeData]);
                        heatMapDataDiff.push([indexX, indexY, beforeData]);
                        if( max < (afterData + beforeData)) max = afterData + beforeData;
                        if( min > afterData) min = afterData;
                        if( min > beforeData) min = beforeData;
                        indexY += 1;
                    }
                }
                indexX += 1;
            }
            var title = 'Aggregated view of ' + ' annotations of ' +  currentSelection.length   +  ' Selected ' + categoryName + '(s)'
            var width = (3*(($('.maincolumn').width() - 50)/5))

            var tooltip = function () {
                return '<b>' + this.series.xAxis.categories[this.point.x] + '</b> got <b>' +
                    this.point.value + '</b> annotations ' + categoryPrefix + ' <b>' + this.series.yAxis.categories[this.point.y] + '</b>';
            }
            var height = 16 * categoriesY.length;
            heatMapGraph(categories, categoriesY, heatMapDataAfter, title , 'High quality annotations.', min, max,
                'annotationsAfter_div', width, height, tooltip, true);
            annotationDivs.push('annotationsAfter_div');
            heatMapGraph(categories, categoriesY, heatMapDataBefore, title, 'All annotations.',
                min, max, 'annotationsBefore_div', width, height, tooltip, true);
            annotationDivs.push('annotationsBefore_div');
            heatMapGraph(categories, categoriesY, heatMapDataDiff, title, 'Low quality annotations.',
                min, max, 'annotationsDiff_div',width, height, tooltip, true);
            annotationDivs.push('annotationsDiff_div');
            //display spam data
            //display spam data


            var metricsData = {};
            metricsData['spam'] = {};
            metricsData['nonSpam'] = {};
            $.getJSON(urlJobsInfo, function (data) {
                for(var iterData in data){
                    var jobID = data[iterData]['_id'];
                    var arrayUnit = jobID.split("/");
                    var jobNo = arrayUnit[arrayUnit.length - 1];

                    if (!('metrics' in data[iterData])) continue;

                    var jobInfo = data[iterData]['metrics'][querySettings['metricCateg']];
                    var metricFilters = querySettings['metricFilter']

                    for (var worker in jobInfo[metricFilters[0]]) {
                        for (var iterMetric in querySettings['metricFields']){
                            var metricName = querySettings['metricFields'][iterMetric];
                            if (!(metricName in metricsData['spam'])) {
                                metricsData['spam'][metricName] = {}
                                metricsData['nonSpam'][metricName] = {}
                            }
                            var arrayUnit = worker.split("/");
                            var workerID = arrayUnit[arrayUnit.length - 1];
                            var key = categoryName + ' ' + workerID + ' in job ' + jobNo
                            metricsData['spam'][metricName][key] = jobInfo[metricFilters[0]][worker][metricName];
                            if (querySettings['metricSuffix'] != '') metricsData['spam'][metricName][key] = jobInfo[metricFilters[0]][worker]['avg'][metricName];
                            metricsData['nonSpam'][metricName][key] = jobInfo[metricFilters[1]][worker][metricName];
                            if (querySettings['metricSuffix'] != '') metricsData['nonSpam'][metricName][key] = jobInfo[metricFilters[1]][worker]['avg'][metricName];
                        }
                    }
                }
                //create the basic heatMap - should exist for each view



                //display non spam data
                for (var iterMetric in querySettings['metricFields']) {
                    var minMetric = Number.MAX_VALUE;
                    var maxMetric = Number.MIN_VALUE;
                    var metricName = querySettings['metricFields'][iterMetric];
                    var spamData = [];
                    var nonSpamData = [];
                    var diffData = [];

                    for (var iterCateg in categoriesY) {
                        var y = parseInt(iterCateg);
                        var afterData = metricsData['nonSpam'][metricName][categoriesY[iterCateg]]
                        var beforeData = metricsData['spam'][metricName][categoriesY[iterCateg]]
                        spamData.push([0, y, beforeData.toFixed(3)]);
                        nonSpamData.push([0, y, afterData.toFixed(3)]);
                        diffData.push([0, y, (beforeData.toFixed(3) - afterData.toFixed(3)).toFixed(3)]);
                        if( maxMetric < afterData) maxMetric = afterData;
                        if( maxMetric < beforeData) maxMetric = beforeData;
                        if( minMetric > afterData) minMetric = afterData;
                        if( minMetric > beforeData - afterData) minMetric = beforeData - afterData;
                    }

                    var title = categoryName + " " + querySettings['metricName'][iterMetric]
                    var width = ((1/querySettings.metricName.length)*(($('.maincolumn').width() - 50)/5))

                    var tooltip = function () {
                        return '<p class = "pieDivGraphs">' + this.series.yAxis.categories[this.point.y] + ', ' + this.series.xAxis.categories[this.point.x] + ' score:<b>' +
                            this.point.value + '</p>';
                    }
                    var height = 16 * categoriesY.length;
                    heatMapGraph([querySettings['metricName'][iterMetric]], categoriesY, nonSpamData, title ,
                        'After filtering low quality', minMetric, maxMetric, 'annotationsMetricAfter_'+iterMetric+'_div',width, height, tooltip, false);
                    annotationDivs.push('annotationsMetricAfter_'+iterMetric+'_div');
                    heatMapGraph([querySettings['metricName'][iterMetric]], categoriesY, spamData, title ,
                        'Before filtering low quality', minMetric, maxMetric, 'annotationsMetricBefore_'+iterMetric+'_div', width, height, tooltip, false);
                    annotationDivs.push('annotationsMetricBefore_'+iterMetric+'_div');
                    heatMapGraph([querySettings['metricName'][iterMetric]], categoriesY, diffData, title ,
                        'low - high quality metrics', minMetric, maxMetric, 'annotationsMetricDiff_'+iterMetric+'_div', width, height, tooltip, false);
                    annotationDivs.push('annotationsMetricDiff_'+iterMetric+'_div');
                }


            });

        });
        //group them
    }

    var drawPieChart = function (platform, spam) {
        console.dir(platform)
        console.dir(spam)
        pieChart = new Highcharts.Chart({
            chart: {
                renderTo: 'annotationsPie_div',
                type: 'pie',
                marginTop: 0,
                width: (1*(($('.maincolumn').width() - 50)/5)),
                height:400
            },
            title: {
                text: 'Type of Annotations of the ' + currentSelection.length + ' selected '+ ' ' + categoryName + '(s)'
            },
            credits: {
                enabled: false
            },
            subtitle: {
                text: 'Click a category to see the distribution of annotations'
            },
            yAxis: {
                title: {
                    text: 'Number of workers per unit'
                }
            },
            dataLabels: {
                enabled: true
            },
            plotOptions: {
                pie: {

                    shadow: false,

                    allowPointSelect: true,
                    center: ['50%', '50%'],
                    point: {
                        events: {
                            click: function () {
                                var platform = this.options.platform;
                                var type = "";
                                if ('type' in this.options) {
                                    type = this.options.type;
                                    getHeatMapData(platform, type);
                                    for (var iterDiv in annotationDivs){
                                        var divName = annotationDivs[iterDiv];
                                        $('#'+divName).show();
                                    }
                                }


                            }
                        }
                    }
                }
            },
            tooltip: {
                useHTML : true,
                formatter: function() {
                    var seriesValue = this.key;
                    return '<p><b>' + seriesValue + ' </b></br>' + this.series.name + ' : ' +
                        this.percentage.toFixed(2) + ' % ('  + this.y + '/' + this.total + ')' +
                        '</p>';
                },
                followPointer : false,
                hideDelay:10
            },

            series: [
                {
                    name: '# of annotations',
                    data: platform,
                    size: '40%',
                    dataLabels: {
                        formatter: function () {
                            // display only if larger than 1
                            return this.point.name;
                        },
                        color: 'white',
                        distance: -30
                    }


                },
                {
                    name: '# of annotations',
                    data: spam,
                    size: '60%',
                    innerSize: '40%',
                    dataLabels: {
                        formatter: function () {
                            // display only if larger than 1
                            return this.point.name;
                        },
                        color: 'black'

                    }

                }
            ]
        });
    }


    this.update = function (selectedUnits, selectedInfo) {
        for (var iterDiv in annotationDivs){
            var divName = annotationDivs[iterDiv];
            $('#'+divName).hide();
        }
        if(selectedUnits.length == 0){
            $('#annotationsPie_div').hide();
        } else {
            $('#annotationsPie_div').show();

        }
        activeSelectedPlatform = "";
        activeSelectedType = "";
        currentSelection = selectedUnits;
        currentSelectionInfo = selectedInfo
        urlBase = "/api/analytics/piegraph/?match[documentType][]=workerunit&";
        //create the series data
        for (var indexUnits in selectedUnits) {
            urlBase += 'match[' + queryField + '][]=' + selectedUnits[indexUnits] + '&';
        }


        platformURL = urlBase + '&project[_id]=_id&group=softwareAgent_id&push[id]=_id';
        $.getJSON(platformURL, function (data) {
            var platformData = [];
            var categoriesData = [];
            var requests = [];
            var iterColors = 0;
            var colors = ['#FFC640', '#A69C00'];


            for (var platformIter in data) {
                var platformID = data[platformIter]['_id'];
                platformData.push({name: platformID, y: data[platformIter]['id'].length,
                    color: Highcharts.Color(colors[platformIter]).brighten(0.07).get(),
                    platform: platformID});
                unitsAnnotationInfo[platformID] = {};
                unitsAnnotationInfo[platformID]['all'] = data[platformIter]['id'];
                //get the jobs by category
                requests.push($.get(urlBase + 'match[softwareAgent_id][]=' + data[platformIter]['_id'] + '&project[_id]=_id&group=type&addToSet=_id'));


            }
            var defer = $.when.apply($, requests);
            defer.done(function () {
                var platform = "";
                var type = "";

                $.each(arguments, function (index, responseData) {
                    // "responseData" will contain an array of response information for each specific request
                    if ($.isArray(responseData)) {
                        if (responseData[1] == 'success') {
                            responseData = responseData[0];
                        }
                        for (var iterObj in responseData) {

                            categoriesData.push({name: responseData[iterObj]['_id'],
                                type: responseData[iterObj]['_id'],
                                y: responseData[iterObj].content.length,
                                color: Highcharts.Color(colors[index]).brighten(-0.01*iterObj).get(),
                                platform: data[index]['_id']});
                            platform = data[index]['_id'];
                            type = responseData[iterObj]['_id']
                            unitsAnnotationInfo[platform][type] = responseData[iterObj].content;
                        }
                    }
                });
                drawPieChart(platformData, categoriesData);

            });

        });

    }


    this.createUnitsAnnotationDetails = function () {

    }

}