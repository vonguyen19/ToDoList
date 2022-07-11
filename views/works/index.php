<div class="header">
    <h2>To Do List</h2>
</div>

<div class="main">
    <div style="display:flex">
        <div>
            <div id="nav"></div>
        </div>
        <div style="flex-grow: 1; margin-left: 10px;">
            <div class="toolbar buttons">
                <span class="toolbar-item"><a id="buttonDay" href="#">Day</a></span>
                <span class="toolbar-item"><a id="buttonWeek" href="#">Week</a></span>
                <span class="toolbar-item"><a id="buttonMonth" href="#">Month</a></span>
            </div>
            <div id="dpDay"></div>
            <div id="dpWeek"></div>
            <div id="dpMonth"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var resources = [{
            name: "Planing",
            id: "Planing"
        },
        {
            name: "Doing",
            id: "Doing"
        },
        {
            name: "Complete",
            id: "Complete"
        },
    ];

    var form = [{
            name: "Name",
            id: "workName"
        },
        {
            name: "Starting Date",
            id: "startDate",
            dateFormat: "MMMM d, yyyy h:mm tt",
            disabled: true
        },
        {
            name: "Ending Date",
            id: "endDate",
            dateFormat: "MMMM d, yyyy h:mm tt",
            disabled: true
        },
        {
            name: "Status",
            id: "status",
            type: "select",
            options: resources,
        },
    ];

    var nav = new DayPilot.Navigator("nav");
    nav.showMonths = 3;
    nav.skipMonths = 3;
    nav.init();

    var day = new DayPilot.Calendar("dpDay");
    day.viewType = "Day";
    configureCalendar(day);
    day.init();

    var week = new DayPilot.Calendar("dpWeek");
    week.viewType = "Week";
    configureCalendar(week);
    week.init();

    var month = new DayPilot.Month("dpMonth");
    configureCalendar(month);
    month.init();

    function configureCalendar(dp) {
        dp.contextMenu = new DayPilot.Menu({
            items: [{
                    text: "Delete",
                    onClick: function(args) {
                        var params = {
                            id: args.source.id(),
                        };

                        $.ajax({
                            type: "POST",
                            url: "?controller=work&action=delete",
                            data: modal.result,
                            datatype: "application/json",
                            success: function(response) {
                                try {
                                    response = JSON.parse(response);
                                } catch (error) {}

                                if (response['status'] === 'success') {
                                    dp.events.remove(params.id);
                                }

                                dp.message(response.message);
                            }
                        });
                    }
                },
                {
                    text: "Update",
                    onClick: function(args) {
                        console.log(args);
                        var data = {
                            id : args.source.data.id,
                            workName : args.source.data.text,
                            startDate: args.source.data.start,
                            endDate: args.source.data.end,
                            status : args.source.data.status,
                        };


                        DayPilot.Modal.form(form, data).then(function(modal) {
                            dp.clearSelection();

                            if (modal.canceled) {
                                return;
                            }
                            
                            $.ajax({
                                type: "POST",
                                url: "?controller=work&action=update",
                                data: modal.result,
                                datatype: "application/json",
                                success: function(response) {
                                    try {
                                        response = JSON.parse(response);
                                    } catch (error) {}
                                    if(response.status === 'success'){
                                        console.log(modal.result);
                                        var e = dp.events.find(modal.result.id);
                                        e.text(modal.result.workName);
                                        dp.events.update(e).notify();
                                    }
                                    dp.message(response.message);
                                }
                            });

                        });
                    }
                }
            ]
        });


        dp.onBeforeEventRender = function(args) {
            if (!args.data.backColor) {
                args.data.backColor = "#6aa84f";
            }
            args.data.borderColor = "darker";
            args.data.fontColor = "#fff";
            args.data.barHidden = true;

            args.data.areas = [{
                right: 2,
                top: 2,
                width: 20,
                height: 20,
                html: "&equiv;",
                action: "ContextMenu",
                cssClass: "area-menu-icon",
                visibility: "Hover"
            }];
        };

        dp.onEventMoved = function(args) {
            DayPilot.Http.ajax({
                url: "calendar_move.php",
                data: {
                    id: args.e.id(),
                    newStart: args.newStart,
                    newEnd: args.newEnd
                },
                success: function() {
                    console.log("Moved.");
                }
            });
        };

        dp.onEventResized = function(args) {
            DayPilot.Http.ajax({
                url: "calendar_move.php",
                data: {
                    id: args.e.id(),
                    newStart: args.newStart,
                    newEnd: args.newEnd
                },
                success: function() {
                    console.log("Resized.");
                }
            });

        };

        // event creating
        dp.onTimeRangeSelected = function(args) {
            var data = {
                startDate: args.start,
                endDate: args.end,
            };


            DayPilot.Modal.form(form, data).then(function(modal) {
                dp.clearSelection();

                if (modal.canceled) {
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "?controller=work&action=store",
                    data: modal.result,
                    datatype: "application/json",
                    success: function(response) {
                        try {
                            response = JSON.parse(response);
                        } catch (error) {}

                        var dp = switcher.active.control;
                        dp.events.add({
                            start: data.startDate,
                            end: data.endDate,
                            id: response['lastInsertId'],
                            text: modal.result.workName
                        });
                    }
                });

            });
        };

        dp.onEventClick = function(args) {
            var startDate = formatDate(args.e.data.start);
            var endDate = formatDate(args.e.data.end);
            var dataHtml = "<div class='contentWork'>" +
                "<div class='form-control'>" +
                "<label>Name: </label>" +
                "<span>" + args.e.data.text + "</span>" +
                "</div>" +
                "<div class='form-control'>" +
                "<label>Start Date: </label>" +
                "<span>" + startDate + "</span>" +
                "</div>" +
                "<div class='form-control'>" +
                "<label>End Date: </label>" +
                "<span>" + endDate + "</span>" +
                "</div>" +
                "<div class='form-control'>" +
                "<label>Status: </label>" +
                "<span>" + args.e.data.status + "</span>" +
                "</div>" +
                "</div>";
            DayPilot.Modal.alert(dataHtml);
        };
    }

    var switcher = new DayPilot.Switcher({
        triggers: [{
                id: "buttonDay",
                view: day
            },
            {
                id: "buttonWeek",
                view: week
            },
            {
                id: "buttonMonth",
                view: month
            }
        ],
        navigator: nav,
        selectedClass: "selected-button",
        onChanged: function(args) {
            console.log("onChanged fired");
            switcher.events.load("?controller=work&action=show");
        }
    });

    switcher.select("buttonWeek");

    function updateColor(e, color) {
        var params = {
            id: e.data.id,
            color: color
        };
        DayPilot.Http.ajax({
            url: "calendar_color.php",
            data: params,
            success: function(ajax) {
                var dp = switcher.active.control;
                e.data.backColor = color;
                dp.events.update(e);
                dp.message("Color updated");
            }
        });
    }

    function formatDate(date) {
        var date = new Date(date);
        dformat = [
            date.getDate(),
            date.getMonth() + 1,
            date.getFullYear()
        ].join('/') + ' ' + [
            date.getHours(),
            date.getMinutes(),
            date.getSeconds()
        ].join(':');
        return dformat;
    }
</script>

<!-- <div class="container">
    <div>
        <a href="?controller=work&action=add">Add</a>
    </div>
</div> -->