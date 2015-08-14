// Based on jQuery.ganttView v.0.8.8 Copyright (c) 2010 JC Grubbs - jc.grubbs@devmynd.com - MIT License
function Gantt(app) {
    this.app = app;
    this.data = [];

    this.options = {
        container: "#gantt-chart",
        showWeekends: true,
        cellWidth: 21,
        cellHeight: 31,
        slideWidth: 1000,
        vHeaderWidth: 200
    };
}

// Save task after a resize or move
Gantt.prototype.saveTask = function(task) {
    this.app.showLoadingIcon();

    $.ajax({
        cache: false,
        url: $(this.options.container).data("save-url"),
        contentType: "application/json",
        type: "POST",
        processData: false,
        data: JSON.stringify(task),
        complete: this.app.hideLoadingIcon.bind(this)
    });
};

// Build the Gantt chart
Gantt.prototype.execute = function() {
    this.data = this.prepareData($(this.options.container).data('tasks'));

    var minDays = Math.floor((this.options.slideWidth / this.options.cellWidth) + 5);
    var range = this.getDateRange(minDays);
    var startDate = range[0];
    var endDate = range[1];
    var container = $(this.options.container);
    var chart = jQuery("<div>", { "class": "ganttview" });

    chart.append(this.renderVerticalHeader());
    chart.append(this.renderSlider(startDate, endDate));
    container.append(chart);

    jQuery("div.ganttview-grid-row div.ganttview-grid-row-cell:last-child", container).addClass("last");
    jQuery("div.ganttview-hzheader-days div.ganttview-hzheader-day:last-child", container).addClass("last");
    jQuery("div.ganttview-hzheader-months div.ganttview-hzheader-month:last-child", container).addClass("last");

    this.listenForBlockResize(startDate);
    this.listenForBlockDrag(startDate);
};

// Render task list on the left
Gantt.prototype.renderVerticalHeader = function() {
    var headerDiv = jQuery("<div>", { "class": "ganttview-vtheader" });
    var itemDiv = jQuery("<div>", { "class": "ganttview-vtheader-item" });
    var seriesDiv = jQuery("<div>", { "class": "ganttview-vtheader-series" });

    for (var i = 0; i < this.data.length; i++) {
        seriesDiv.append(jQuery("<div>", {
            "class": "ganttview-vtheader-series-name tooltip",
            "title": "<strong>" + this.data[i].column_title + "</strong> (" + this.data[i].progress + ")<br/>" + this.data[i].title
        }).append(jQuery("<a>", {"href": this.data[i].link, "target": "_blank"}).append(this.data[i].title)));
    }

    itemDiv.append(seriesDiv);
    headerDiv.append(itemDiv);

    return headerDiv;
};

// Render right part of the chart (top header + grid + bars)
Gantt.prototype.renderSlider = function(startDate, endDate) {
    var slideDiv = jQuery("<div>", {"class": "ganttview-slide-container"});
    var dates = this.getDates(startDate, endDate);

    slideDiv.append(this.renderHorizontalHeader(dates));
    slideDiv.append(this.renderGrid(dates));
    slideDiv.append(this.addBlockContainers());
    this.addBlocks(slideDiv, startDate);

    return slideDiv;
};

// Render top header (days)
Gantt.prototype.renderHorizontalHeader = function(dates) {
    var headerDiv = jQuery("<div>", { "class": "ganttview-hzheader" });
    var monthsDiv = jQuery("<div>", { "class": "ganttview-hzheader-months" });
    var daysDiv = jQuery("<div>", { "class": "ganttview-hzheader-days" });
    var totalW = 0;

    for (var y in dates) {
        for (var m in dates[y]) {
            var w = dates[y][m].length * this.options.cellWidth;
            totalW = totalW + w;

            monthsDiv.append(jQuery("<div>", {
                "class": "ganttview-hzheader-month",
                "css": { "width": (w - 1) + "px" }
            }).append($.datepicker.regional[$("body").data('js-lang')].monthNames[m] + " " + y));

            for (var d in dates[y][m]) {
                daysDiv.append(jQuery("<div>", { "class": "ganttview-hzheader-day" }).append(dates[y][m][d].getDate()));
            }
        }
    }

    monthsDiv.css("width", totalW + "px");
    daysDiv.css("width", totalW + "px");
    headerDiv.append(monthsDiv).append(daysDiv);

    return headerDiv;
};

// Render grid
Gantt.prototype.renderGrid = function(dates) {
    var gridDiv = jQuery("<div>", { "class": "ganttview-grid" });
    var rowDiv = jQuery("<div>", { "class": "ganttview-grid-row" });

    for (var y in dates) {
        for (var m in dates[y]) {
            for (var d in dates[y][m]) {
                var cellDiv = jQuery("<div>", { "class": "ganttview-grid-row-cell" });
                if (this.options.showWeekends && this.isWeekend(dates[y][m][d])) {
                    cellDiv.addClass("ganttview-weekend");
                }
                rowDiv.append(cellDiv);
            }
        }
    }
    var w = jQuery("div.ganttview-grid-row-cell", rowDiv).length * this.options.cellWidth;
    rowDiv.css("width", w + "px");
    gridDiv.css("width", w + "px");

    for (var i = 0; i < this.data.length; i++) {
        gridDiv.append(rowDiv.clone());
    }

    return gridDiv;
};

// Render bar containers
Gantt.prototype.addBlockContainers = function() {
    var blocksDiv = jQuery("<div>", { "class": "ganttview-blocks" });

    for (var i = 0; i < this.data.length; i++) {
        blocksDiv.append(jQuery("<div>", { "class": "ganttview-block-container" }));
    }

    return blocksDiv;
};

// Render bars
Gantt.prototype.addBlocks = function(slider, start) {
    var rows = jQuery("div.ganttview-blocks div.ganttview-block-container", slider);
    var rowIdx = 0;

    for (var i = 0; i < this.data.length; i++) {
        var series = this.data[i];
        var size = this.daysBetween(series.start, series.end) + 1;
        var offset = this.daysBetween(start, series.start);
        var text = jQuery("<div>", {"class": "ganttview-block-text"});

        var block = jQuery("<div>", {
            "class": "ganttview-block tooltip",
            "title": this.getBarTooltip(this.data[i]),
            "css": {
                "width": ((size * this.options.cellWidth) - 9) + "px",
                "margin-left": (offset * this.options.cellWidth) + "px"
            }
        }).append(text);

        if (size >= 2) {
            text.append(this.data[i].progress);
        }

        block.data("task", this.data[i]);
        this.setTaskColor(block, this.data[i]);
        jQuery(rows[rowIdx]).append(block);
        rowIdx = rowIdx + 1;
    }
};

// Get tooltip for task bars
Gantt.prototype.getBarTooltip = function(task) {

    if (task.not_defined) {
        return $(this.options.container).data("label-not-defined");
    }

    return "<strong>" + task.progress + "</strong><br/>" +
        $(this.options.container).data("label-assignee") + " " + (task.assignee ? task.assignee : '') + "<br/>" +
        $(this.options.container).data("label-start-date") + " " + $.datepicker.formatDate('yy-mm-dd', task.start) + "<br/>" +
        $(this.options.container).data("label-end-date") + " " + $.datepicker.formatDate('yy-mm-dd', task.end);
};

// Set task color
Gantt.prototype.setTaskColor = function(block, task) {
    if (task.not_defined) {
        block.addClass("ganttview-block-not-defined");
    }
    else {
        block.css("background-color", task.color.background);
        block.css("border-color", task.color.border);
    }
};

// Setup jquery-ui resizable
Gantt.prototype.listenForBlockResize = function(startDate) {
    var self = this;

    jQuery("div.ganttview-block", this.options.container).resizable({
        grid: this.options.cellWidth,
        handles: "e,w",
        stop: function() {
            var block = jQuery(this);
            self.updateDataAndPosition(block, startDate);
            self.saveTask(block.data("task"));
        }
    });
};

// Setup jquery-ui drag and drop
Gantt.prototype.listenForBlockDrag = function(startDate) {
    var self = this;

    jQuery("div.ganttview-block", this.options.container).draggable({
        axis: "x",
        grid: [this.options.cellWidth, this.options.cellWidth],
        stop: function() {
            var block = jQuery(this);
            self.updateDataAndPosition(block, startDate);
            self.saveTask(block.data("task"));
        }
    });
};

// Update the task data and the position on the chart
Gantt.prototype.updateDataAndPosition = function(block, startDate) {
    var container = jQuery("div.ganttview-slide-container", this.options.container);
    var scroll = container.scrollLeft();
    var offset = block.offset().left - container.offset().left - 1 + scroll;
    var task = block.data("task");

    // Restore color for defined block
    task.not_defined = false;
    this.setTaskColor(block, task);

    // Set new start date
    var daysFromStart = Math.round(offset / this.options.cellWidth);
    var newStart = this.addDays(this.cloneDate(startDate), daysFromStart);
    task.start = newStart;

    // Set new end date
    var width = block.outerWidth();
    var numberOfDays = Math.round(width / this.options.cellWidth) - 1;
    task.end = this.addDays(this.cloneDate(newStart), numberOfDays);

    if (numberOfDays > 0) {
        jQuery("div.ganttview-block-text", block).text(task.progress);
    }

    // Update tooltip
    block.attr("title", this.getBarTooltip(task));

    block.data("task", task);

    // Remove top and left properties to avoid incorrect block positioning,
    // set position to relative to keep blocks relative to scrollbar when scrolling
    block
        .css("top", "")
        .css("left", "")
        .css("position", "relative")
        .css("margin-left", offset + "px");
};

// Creates a 3 dimensional array [year][month][day] of every day
// between the given start and end dates
Gantt.prototype.getDates = function(start, end) {
    var dates = [];
    dates[start.getFullYear()] = [];
    dates[start.getFullYear()][start.getMonth()] = [start];
    var last = start;

    while (this.compareDate(last, end) == -1) {
        var next = this.addDays(this.cloneDate(last), 1);

        if (! dates[next.getFullYear()]) {
            dates[next.getFullYear()] = [];
        }

        if (! dates[next.getFullYear()][next.getMonth()]) {
            dates[next.getFullYear()][next.getMonth()] = [];
        }

        dates[next.getFullYear()][next.getMonth()].push(next);
        last = next;
    }

    return dates;
};

// Convert data to Date object
Gantt.prototype.prepareData = function(data) {
    for (var i = 0; i < data.length; i++) {
        var start = new Date(data[i].start[0], data[i].start[1] - 1, data[i].start[2], 0, 0, 0, 0);
        data[i].start = start;

        var end = new Date(data[i].end[0], data[i].end[1] - 1, data[i].end[2], 0, 0, 0, 0);
        data[i].end = end;
    }

    return data;
};

// Get the start and end date from the data provided
Gantt.prototype.getDateRange = function(minDays) {
    var minStart = new Date();
    var maxEnd = new Date();

    for (var i = 0; i < this.data.length; i++) {
        var start = new Date();
        start.setTime(Date.parse(this.data[i].start));

        var end = new Date();
        end.setTime(Date.parse(this.data[i].end));

        if (i == 0) {
            minStart = start;
            maxEnd = end;
        }

        if (this.compareDate(minStart, start) == 1) {
            minStart = start;
        }

        if (this.compareDate(maxEnd, end) == -1) {
            maxEnd = end;
        }
    }

    // Insure that the width of the chart is at least the slide width to avoid empty
    // whitespace to the right of the grid
    if (this.daysBetween(minStart, maxEnd) < minDays) {
        maxEnd = this.addDays(this.cloneDate(minStart), minDays);
    }

    // Always start one day before the minStart
    minStart.setDate(minStart.getDate() - 1);

    return [minStart, maxEnd];
};

// Returns the number of day between 2 dates
Gantt.prototype.daysBetween = function(start, end) {
    if (! start || ! end) {
        return 0;
    }

    var count = 0, date = this.cloneDate(start);

    while (this.compareDate(date, end) == -1) {
        count = count + 1;
        this.addDays(date, 1);
    }

    return count;
};

// Return true if it's the weekend
Gantt.prototype.isWeekend = function(date) {
    return date.getDay() % 6 == 0;
};

// Clone Date object
Gantt.prototype.cloneDate = function(date) {
    return new Date(date.getTime());
};

// Add days to a Date object
Gantt.prototype.addDays = function(date, value) {
    date.setDate(date.getDate() + value * 1);
    return date;
};

/**
 * Compares the first date to the second date and returns an number indication of their relative values.
 *
 * -1 = date1 is lessthan date2
 * 0 = values are equal
 * 1 = date1 is greaterthan date2.
 */
Gantt.prototype.compareDate = function(date1, date2) {
    if (isNaN(date1) || isNaN(date2)) {
        throw new Error(date1 + " - " + date2);
    } else if (date1 instanceof Date && date2 instanceof Date) {
        return (date1 < date2) ? -1 : (date1 > date2) ? 1 : 0;
    } else {
        throw new TypeError(date1 + " - " + date2);
    }
};
