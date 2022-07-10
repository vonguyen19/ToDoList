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
                        DayPilot.Http.ajax({
                            url: "calendar_delete.php",
                            data: params,
                            success: function(ajax) {
                                dp.events.remove(params.id);
                                dp.message("Deleted");
                            }
                        });
                    }
                },
                {
                    text: "-"
                },
                {
                    text: "Blue",
                    icon: "icon icon-blue",
                    color: "#3d85c6",
                    onClick: function(args) {
                        updateColor(args.source, args.item.color);
                    }
                },
                {
                    text: "Green",
                    icon: "icon icon-green",
                    color: "#6aa84f",
                    onClick: function(args) {
                        updateColor(args.source, args.item.color);
                    }
                },
                {
                    text: "Orange",
                    icon: "icon icon-orange",
                    color: "#e69138",
                    onClick: function(args) {
                        updateColor(args.source, args.item.color);
                    }
                },
                {
                    text: "Red",
                    icon: "icon icon-red",
                    color: "#cc4125",
                    onClick: function(args) {
                        updateColor(args.source, args.item.color);
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
            var resources = [
                {name: "Planing", id: "Planing"},
                {name: "Doing", id: "Doing"},
                {name: "Complete", id: "Complete"},
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
                    type : "select",
                    options: resources,
                },
            ];

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
                    datatype : "application/json",
                    success: function (response) {
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
                // console.log(modal.result);
                // DayPilot.Http.ajax({
                //     url: "?controller=work&action=store",
                //     method : "POST",
                //     data: modal.result,
                //     headers : {
                //         'Content-Type' : "application/x-www-form-urlencoded"
                //     },
                //     success: function(ajax) {
                //         var dp = switcher.active.control;
                //         dp.events.add({
                //             start: data.start,
                //             end: data.end,
                //             id: ajax.data.id,
                //             text: data.text
                //         });
                //     },
                //     error : function(e){
                //         console.log(e);
                //     }
                // });

            });
        };

        dp.onEventClick = function(args) {
            DayPilot.Modal.alert(args.e.data.text);
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
            switcher.events.load("calendar_events.php");
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
</script>

<!-- <div class="container">
    <div>
        <a href="?controller=work&action=add">Add</a>
    </div>
</div> -->