function heatMapGraph(categoriesX, categoriesY, heatMapData, heatMapTitle, heatMapSubtitle, colorAxis, divName, width, height, tooltip, show, legend) {

    var heatMapChart = "";
    var hideIcon = false;
    var addTooltipYLabel = function (divName, legend){
        var legendItems = $(divName +' .highcharts-yaxis-labels')[0].children;
        for (var legendItemIter in legendItems){
            var elemeHTML = $(legendItems[legendItemIter]);
            elemeHTML.attr('data-toggle','tooltip');
            var tooltipValue = legend[elemeHTML.text()];
            elemeHTML.attr('title',tooltipValue);
        }
    }
    var createImage = function (chart, url, title, x, y, w, h){
        var img = chart.renderer.image(url,  x, y, w, h);
        img.add();
        img.css({'cursor': 'pointer'});
        img.attr({'title': 'Pop out chart'});
        img.attr("data-toggle", "tooltip");
        img.attr("style", "opacity:0.5");
        img.attr("title", title);
        img.on('click', function () {


            /*for (var series in searchSet) {
                var series_id = searchSet[series];
                if (barChart.series[series_id].visible) {
                    hideIcon = true;
                    barChart.series[series_id].hide()
                    barChart.series[series_id].options.showInLegend = false;
                    barChart.series[series_id].legendItem = null;
                    barChart.legend.destroyItem(barChart.series[series_id]);
                    barChart.legend.render();
                } else {
                    hideIcon = false;
                    barChart.series[series_id].show();
                    barChart.series[series_id].options.showInLegend = true;
                    barChart.legend.renderItem(barChart.series[series_id]);
                    barChart.legend.render();
                }

            }*/
            if (hideIcon == true) {
                this.setAttribute("style", "opacity:0.5");
                $('.annotationHidden').addClass('hide');
                hideIcon = false;
            } else {
                this.setAttribute("style", "opacity:1");
                $('.annotationHidden').removeClass('hide');
                addTooltipYLabel('#annotationsBefore_div', legend);
                addTooltipYLabel('#annotationsDiff_div', legend);
                hideIcon = true;
            }
        });
    }

    var callback = function callback($this) {
        if((divName.indexOf('After') == -1) || (divName.indexOf('Metric') != -1))return;
        createImage(this, '/assets/judgements.png', "Low quality judgements", $this.chartWidth-60,15,19,14);
    }

    var compare = function (a, b) {
        a_array = a._id.split("/");
        b_array = b._id.split("/");
        a_id = a_array[a_array.length - 1];
        b_id = b_array[b_array.length - 1];
        return a_id - b_id;
    };

    var chartSettings =  {

        chart: {
            renderTo : divName,
            zoomType: 'xy',
            type: 'heatmap',
            //spacingBottom: 70,
            marginTop: 100,
            marginBottom: 190,
            width: width,
            height: height + 190 + 100,
            events: {
                load: function(event) {
                    if(legend != undefined) {
                        //addTooltipYLabel('#annotationsAfter_div', legend);
                    }

                }
            }

        },
        credits: {
            enabled: false
        },

        title: {
            style: {
                fontWeight: 'bold'
            },
            text: heatMapTitle
        },
        subtitle:{
            text:heatMapSubtitle
        },

        xAxis: {
            categories: categoriesX,
            labels: {
                rotation: -45,
                align: 'right',
                overflow: 'justify'
            }
        },

        yAxis: {
            opposite:true,
            scalable:false,
            categories: categoriesY,
            title: null,
            labels: {
                formatter: function() {
                    if (!(show)) return "";
                    return this.value;
                    /*console.dir('<a data-toggle="tooltip" title data-original-title="' + 'DA' + '">' + 'dada<a>');
                    return '<a data-toggle="tooltip" title data-original-title="' + 'DA' + '">' + 'dada</a>';*/
                },
                align: 'left'
            }
        },

        colorAxis: colorAxis,
        legend:{
            symbolWidth: width - 0.4*width,

            x: 0
        },

       /* legend: {

            layout: 'vertical',
            margin: 0,
            verticalAlign: 'top',
            y: 25,
            symbolHeight: 320
        },*/
        mapNavigation: {
            enabled: true,
            enableDoubleClickZoomTo: true
        },

        tooltip: {
            useHTML:true,
            hideDelay:10,
            formatter: tooltip
        },

        series: [{
            name: heatMapTitle,
            borderWidth: 1,
            data: heatMapData,
            dataLabels: {
                enabled: true,
                color: 'black',
                style: {
                    textShadow: 'none',
                    fontSize: 10,
                    HcTextStroke: null
                }

            }
        }]

    };


    heatMapChart =  new Highcharts.Chart(chartSettings, callback);


}