/**
 * Highcharts plugin for adding contextmenu event to points
 *
 * Author: Joe Kuan, Torstein Hønsi
 * Last revision: 2013-05-27
 */
(function (Highcharts) {

    Highcharts.wrap(Highcharts.Chart.prototype, 'firstRender', function (proceed) {

        proceed.call(this);

        var chart = this,
            container = this.container,
            plotLeft = this.plotLeft,
            plotTop = this.plotTop,
            plotWidth = this.plotWidth,
            plotHeight = this.plotHeight,
            inverted = this.inverted,
            pointer = this.pointer;

        // Note:
        // - Safari 5, IE8: mousedown, contextmenu, click
        // - Firefox 5: mousedown contextmenu
        container.oncontextmenu = function(e) {

            var hoverPoint = chart.hoverPoint,
                chartPosition = pointer.chartPosition;

            this.rightClick = true;

            e = pointer.normalize(e);

            e.cancelBubble = true; // IE specific
            e.returnValue = false; // IE 8 specific
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            if (e.preventDefault) {
                e.preventDefault();
            }

            if (!pointer.hasDragged) {
                if (hoverPoint && pointer.inClass(e.target, 'highcharts-tracker')) {
                    var plotX = hoverPoint.plotX,
                        plotY = hoverPoint.plotY;

                    // add page position info
                    Highcharts.extend(hoverPoint, {
                        pageX: chartPosition.left + plotLeft +
                            (inverted ? plotWidth - plotY : plotX),
                        pageY: chartPosition.top + plotTop +
                            (inverted ? plotHeight - plotX : plotY)
                    });

                    // the series click event
                    HighchartsAdapter.fireEvent(
                        hoverPoint.series, 'contextmenu',
                        Highcharts.extend(e, {
                            point: hoverPoint
                        })
                    );

                    // the point click event
                    hoverPoint.firePointEvent('contextmenu', e);
                }
            }
        }
    });

}(Highcharts));