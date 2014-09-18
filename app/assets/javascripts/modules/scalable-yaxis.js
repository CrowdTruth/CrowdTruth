/**
 * Highcharts plugin for manually scaling Y-Axis range.
 *
 * Author: Roland Banguiran
 * Email: banguiran@gmail.com
 *
 * Usage: Set scalable:false in the yAxis options to disable.
 * Default: true
 */

// JSLint options:
/*global Highcharts, document */

(function (H) {
    'use strict';
    var addEvent = H.addEvent,
        each = H.each,
        doc = document,
        body = doc.body;

    H.wrap(H.Chart.prototype, 'init', function (proceed) {

        // Run the original proceed method
        proceed.apply(this, Array.prototype.slice.call(arguments, 1));

        var chart = this,
            renderer = chart.renderer,
            yAxis = chart.yAxis;
        var yAxisRightObj = 0,
            yAxisLeftObj = 0;

        each(yAxis, function (yAxis) {
            if(yAxis.opposite == true) {
                yAxisRightObj++;
            } else {
                yAxisLeftObj++;
            }
        });

        var position = 0;
        each(yAxis, function (yAxis) {
            var options = yAxis.options,
                scalable = options.scalable === undefined ? true : options.scalable,
                labels = options.labels,
                pointer = chart.pointer,
                labelGroupBBox,
                bBoxX,
                bBoxY,
                bBoxWidth,
                bBoxHeight,
                isDragging = false,
                downYValue;

            if (scalable) {

                bBoxWidth = yAxis.opposite ? yAxis.right/yAxisRightObj - 0.15*yAxis.right/yAxisRightObj : yAxis.left/yAxisLeftObj - 0.15*yAxis.left/yAxisLeftObj
                bBoxHeight = chart.chartHeight - yAxis.top - yAxis.bottom;
                bBoxX = yAxis.opposite ? (yAxis.labelAlign === 'left' ? (chart.chartWidth - yAxis.right + yAxis.offset) :  (chart.chartWidth - yAxis.right + yAxis.offset)) : (yAxis.labelAlign === 'left' ? yAxis.left - yAxis.len + yAxis.offset : yAxis.left  + yAxis.offset - bBoxWidth);
                bBoxY = yAxis.top;

                // Render an invisible bounding box around the y-axis label group
                // This is where we add mousedown event to start dragging

                labelGroupBBox = renderer.rect(bBoxX, bBoxY, bBoxWidth, bBoxHeight)
                    .attr({
                        fill: '#FFF',
                        opacity: 0,
                        zIndex: 8
                    })
                    .css({
                        cursor: 'ns-resize'
                    })
                    .add();

                labels.style.cursor = 'ns-resize';

                addEvent(labelGroupBBox.element, 'mousedown', function (e) {
                    var downYPixels = pointer.normalize(e).chartY;

                    downYValue = yAxis.toValue(downYPixels);
                    isDragging = true;
                });

                addEvent(chart.container, 'mousemove', function (e) {
                    if (isDragging) {
                        body.style.cursor = 'ns-resize';

                        var dragYPixels = chart.pointer.normalize(e).chartY,
                            dragYValue = yAxis.toValue(dragYPixels),

                            extremes = yAxis.getExtremes(),
                            userMin = extremes.userMin,
                            userMax = extremes.userMax,
                            dataMin = extremes.dataMin,
                            dataMax = extremes.dataMax,

                            min = userMin !== undefined ? userMin : dataMin,
                            max = userMax !== undefined ? userMax : dataMax,

                            newMin,
                            newMax;

                        // update max extreme only if dragged from upper portion
                        // update min extreme only if dragged from lower portion
                        if (downYValue > (dataMin + dataMax) / 2) {
                            newMin = min;
                            newMax = max - (dragYValue - downYValue);
                            newMax = newMax > dataMax ? newMax : dataMax; //limit
                        } else {
                            newMin = min - (dragYValue - downYValue);
                            newMin = newMin < dataMin ? newMin : dataMin; //limit
                            newMax = max;
                        }

                        yAxis.setExtremes(newMin, newMax, true, false);
                    }
                });

                addEvent(document, 'mouseup', function () {
                    if(isDragging) {
                        body.style.cursor = 'default';
                        isDragging = false;
                    }

                });

                // double-click to go back to default range
                addEvent(labelGroupBBox.element, 'dblclick', function () {

                    var extremes = yAxis.getExtremes(),
                        dataMin = extremes.dataMin,
                        dataMax = extremes.dataMax;

                    yAxis.setExtremes(dataMin, dataMax, true, false);
                });
            }
            position = position + 1;
        });
    });
}(Highcharts));