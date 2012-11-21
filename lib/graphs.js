history.navigationMode = 'compatible';
$(window).load(function(){                

    function DownloadJSON2CSV(objArray)
    {
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = '';

        for (var i = 0; i < array.length; i++) {
            var line = '';

            // Here is an example where you would wrap the values in double quotes
            for (var index in array[i]) {
               line += '"' + array[i][index] + '",';
               line += '\r\n';
            }

            line.slice(0,line.Length-1); 
            str += line + '\r\n';
        }
        var w = window.open ('', 'wnd');
        var explain = 'The first value is the title of the data. The second value is the color used. The third is an array in the format of UNIXTIME, KILL COUNT values - where the UNIXTIME is expressed in milliseconds (so divide by 1000 to get seconds).';
        w.document.body.innerHTML = '<div>'+(str)+'</div>'+'<BR \>'+'<div>'+explain+'</div>';
    }

    var split = location.search.replace('?', '').replace('&','=').split('=');
    if (split[1]!='tutorial'){
        $.extend($.gritter.options, {
            fade_in_speed: 'medium',
            fade_out_speed: 2000,
            position:'bottom-right'
        });

        $.gritter.add({
           title: 'First time using this?',
           text: 'Try the <a style="text-decoration:underline;" class="contentLink" href="/?p=tutorial">tutorial!</a>',
           sticky : true
        });
    }

    var daily_plot, daily_x_from, daily_x_to, daily_y_from, daily_y_to;
    var weekly_plot, week_x_from, week_x_to, week_y_from, week_y_to;

    function setNearestPoint(ranges, plot, data){
        time_from = ranges.xaxis.from;
        time_to = ranges.xaxis.to;
    
        //take selection, look through array for closest time, setselection as that
        var from_lo, to_hi;
        
        $.each(data, function(){
          if (from_lo == null || Math.abs(this[0] - time_from) < Math.abs(from_lo - time_from)) {
            from_lo = this[0];
          }
        });    

        for (var i =0; i < data.length; i++){
            if (data[i][0] >= time_to && (to_hi === undefined || to_hi > data[i][0])) to_hi = data[i][0];
        };

        plot.setSelection({xaxis:{from:from_lo,to:to_hi}}, true);
        var nRange = new Array();
        nRange[0] = from_lo;
        nRange[1] = to_hi;
        return nRange;
    };

    function showSelectedStats(ranges,plot,data){
        //ranges has the new, binded ranges
        var y1,y2;
        var c1,c2;

        for (var i=0;y1==null || y2==null;i++){
            if (ranges[0] == data[i][0]) { y1=data[i][1]; c1=i; }
            if (ranges[1] == data[i][0]) { y2=data[i][1]; c2=i; }
        }        
   
        var d = (y2-y1) / (ranges[1]-ranges[0]);
        d /= 3600000;
        d = new String(d);
        $("#stat_list").append('<BR id="select" \>');
        $("#stat_list").append('<li class=\'selected_stat\'>data points selected : <span id=\'stat_value\'>'+(data.slice(c1,c2).length+1)+'</li>');    
        $("#stat_list").append('<ul><li class=\'selected_stat\'>&#916;kills/hr in selection : <span id=\'stat_value\'>'+d.substr(0,5)+'</li></ul>');
    }

    if ($("#no_data").length){
        $(".sidebar_stats").css("display","none");
        $("#zoom_in").parent().css("display","none");
    }

    if ($(".graph_daily").length){
        $('#loading').show();//show loading img
    
        var finalData = null;

        function onOutboundReceived(series) {
            finalData = series;
            var options = {
                selection : { mode : "x" },
                series:{
                    lines: { show: true },
                    points: { show: true, hoverable:true },
                },
                legend: { show : false },
                grid: { hoverable: true, clickable: true },
                xaxis: { mode : "time", timeformat : "%H:%M", minTickSize:[1,"hour"] },
                yaxis: { minTickSize: "1", tickDecimals : "" },
            };
            if (daily_plot==null){
                daily_plot = $.plot($(".graph_daily"), finalData, options);
            
                var daily_axes = daily_plot.getAxes();
                daily_x_from = daily_axes.xaxis.min;
                daily_x_to = daily_axes.xaxis.max;
                daily_y_from = daily_axes.yaxis.min;
                daily_y_to = daily_axes.yaxis.max;
            }

            $('#loading').hide();
            
            $("#download_daily_data").click(function(){
                DownloadJSON2CSV(finalData);                
            });

            var recv_data = finalData[0];
            var data_arr = recv_data["data"];

            if (data_arr.length>0) hourStat(finalData);
            else $(".sidebar_stats").css("display","none");

            var initial_range=null;
            var time_from;
            var time_to;

            $(".graph_daily").bind("plotselected", function(event, ranges){
                $(".selected_stat").remove();
                $("#select").remove();

                var nRange = setNearestPoint(ranges, daily_plot, data_arr);
                showSelectedStats(nRange,daily_plot, data_arr);

                $("#zoom_in_daily").click(function() {
                    if (initial_range!=ranges) {
                        options.yaxis.max = data_arr[daily_plot.getSelection().yaxis.max];
                        options.yaxis.min = data_arr[daily_plot.getSelection().yaxis.min];
                        daily_plot = $.plot($(".graph_daily"), finalData, $.extend(true, {}, options, { xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to } }));
                    }
                    initial_range = ranges;
                });
                $("#reset_daily").click(function() {
                    daily_plot.clearSelection();
                    daily_plot = $.plot($(".graph_daily"), finalData, $.extend(true, {}, options, {
                        xaxis: { min: daily_x_from, max: daily_x_to },
                        yaxis: { min: daily_y_from, max: daily_y_to }
                    }));
                    $(".selected_stat").remove();
                    $("#select").remove();
                });
            });

            $(".graph_daily").bind("plotunselected", function(event){
                $(".selected_stat").remove();
                $("#select").remove();
            });
        }

        function hourStat(finalData){
             //delta kills
            var arr = finalData[0];
            var data = arr["data"];
            var kill_delta;
            var elapsed_hours;
            var kph;

            //need delta of the first value and last value
            kill_delta = data[data.length-1][1]-data[0][1];
            var c_time = new Date();
            elapsed_hours = (data[data.length-1][0] - data[0][0]) / 3600000;
            kph = kill_delta / elapsed_hours;
            kph = new String(kph);
            $("#stat_list").append('<li>points for daily data : <span id=\'stat_value\'>'+data.length+'</li>');
            $("#stat_list").append('<ul><li>&#916;kills/hr in last 24hrs: <span id=\'stat_value\'> '+kph.substr(0,5)+'</li></ul><br id="not-select"\>');
        }

        $.ajax({
            url: "encode_daily_graph.php?itemid="+split[3],
            method: 'GET',
            dataType: 'json',
            success: onOutboundReceived
        });
        
        var previousPoint = null;
        $(".graph_daily").bind("plothover", function (event,pos,item){
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    
                    $("#graph_info").remove();
                    var x = item.datapoint[0].toFixed(2), y = item.datapoint[1].toFixed(2);
                    var date = new Date(item.datapoint[0]);
                    var hour = date.getUTCHours();
                    var min = date.getMinutes();
                    var msg = hour + ":" + min + ", " + Math.round(y);
                    if (min < 10) msg = hour + ":" + "0" +  min + ", " + Math.round(y);
                    showToolTip(item.pageX, item.pageY, msg);
                }
            }
            else{
                $("#graph_info").remove();
                previousPoint = null;
            }
        });

        function showToolTip(x,y,contents){
            $('<div id="graph_info">' + contents + '</div>').css( {
                position : 'absolute',
                display : 'none',
                top: y+ 5,
                left : x+5,
                border : '1px solid #fdd',
                padding : '2px',
                'background-color' : '#fee',
                opacity:0.80
            }).appendTo("body").fadeIn(200);
        }
    }

    if ($(".graph_weekly").length){

        $('#loading').show();//show loading img

        function onOutboundReceivedWeekly(series) {
            var length = series.length;
            finalData = series;
            var options = {
                lines: { show: true },
                legend: { show : false },
                points: { show: true, hoverable:true },
                grid: { hoverable: true, clickable: true },
                xaxis: { mode : "time", minTickSize:[1,"day"], timeformat : "%m/%0d"},
                yaxis: { minTickSize: "1", tickDecimals : "0" },
                selection : { mode: "x" } 
            };
            if (weekly_plot==null){
                weekly_plot = $.plot($(".graph_weekly"), finalData, options);

                var weekly_axes = weekly_plot.getAxes();
                weekly_x_from = weekly_axes.xaxis.min;
                weekly_x_to = weekly_axes.xaxis.max;
                weekly_y_from = weekly_axes.yaxis.min;
                weekly_y_to = weekly_axes.yaxis.max;            
            }

            $('#loading').hide();//hide loading img
                

            $("#download_weekly_data").click(function(){
                DownloadJSON2CSV(finalData);                
            });

            var recv_data = finalData[0];
            var data_arr = recv_data["data"];

            if (data_arr.length>0) hourStat(finalData);
            else $(".sidebar_stats").css("display","none");

            function hourStat(finalData){
                 //delta kills
                var arr = finalData[0];
                var data = arr["data"];
                var kill_delta;
                var elapsed_hours;
                var kph;

                $("#stat_list").append('<li>data points for the month :  <span id=\'stat_value\'>'+data.length+'</li>');
    
                //need delta of the first value and last value
                kill_delta = data[data.length-1][1]-data[0][1];
                var c_time = new Date();
                elapsed_hours = (data[data.length-1][0] - data[0][0]) / 3600000;
                kph = kill_delta / elapsed_hours;
                kph = new String(kph);
                $("#stat_list").append('<ul><li>&#916;kills/hr in this month: <span id=\'stat_value\'>'+kph.substr(0,5)+'</li></ul>');
            
                //by the day

                var kpd;
                var elapsed_days;
                
                elapsed_days = (data[data.length-1][0] - data[0][0]) / (3600000*24);
                kpd = kill_delta / elapsed_days;
                kpd = new String(kpd);
                $("#stat_list").append('<ul><li>&#916;kills/day in this month: <span id=\'stat_value\'>'+kpd.substr(0,5)+'</li></ul>');
            }
            
            var initial_range=null;
            $(".graph_weekly").bind("plotselected", function(event, ranges){
                $(".selected_stat").remove();
                $("#select").remove();
        
                var nRange = setNearestPoint(ranges, weekly_plot, data_arr);
                showSelectedStats(nRange,daily_plot, data_arr);
 
               $("#zoom_in_weekly").click(function() {
                    if (initial_range!=ranges){
                         options.yaxis.max = data_arr[weekly_plot.getSelection().yaxis.max];
                         options.yaxis.min = data_arr[weekly_plot.getSelection().yaxis.min];
                         weekly_plot = $.plot($(".graph_weekly"), finalData, $.extend(true, {}, options, { xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to } }));
                    }
                    initial_range = ranges;
                });
        
                $("#reset_weekly").click(function() {
                    weekly_plot = $.plot($(".graph_weekly"), finalData, $.extend(true, {}, options, {
                        xaxis: { min: weekly_x_from, max: weekly_x_to },
                        yaxis: { min: weekly_y_from, max: weekly_y_to }
                    }));
                    $(".selected_stat").remove();
                    $("#select").remove();
                });
            });

            $(".graph_weekly").bind("plotunselected", function(event){
                $(".selected_stat").remove();
                $("#select").remove();
            });

        }

        $.ajax({
            url: "encode_weekly_graph.php?itemid="+split[3],
            method: 'GET',
            dataType: 'json',
            success: onOutboundReceivedWeekly
        });
        
        var previousPointWeekly = null;
        $(".graph_weekly").bind("plothover", function (event,pos,item2){
            if (item2) {
                if (previousPointWeekly != item2.dataIndex) {
                    previousPointWeekly = item2.dataIndex;
                    
                    $("#graph_info_weekly").remove();
                    var x = item2.datapoint[0], y = item2.datapoint[1].toFixed(2);
                    var date = new Date(x + 7 * 3600 * 1000);
                    var day = date.getDate();
                    var month = date.getMonth()+1;
                    var hour = date.getHours();
                    var minutes = date.getMinutes();
                    var msg = month + "/" + day + " - " + hour + ":" + minutes;
                    if (minutes < 10) msg = month + "/" + day + " - " + hour + ":" + "0" + minutes;
                    showToolTipWeekly(item2.pageX, item2.pageY, msg + ", " + Math.round(y));
                }
            }
            else{
                $("#graph_info_weekly").remove();
                previousPointWeekly = null;
            }
        });
        
        function showToolTipWeekly(x,y,contents){
            $('<div id="graph_info_weekly">' + contents + '</div>').css( {
                position : 'absolute',
                display : 'none',
                top: y+ 5,
                left : x+5,
                border : '1px solid #fdd',
                padding : '2px',
                'background-color' : '#fee',
                opacity:0.80
            }).appendTo("body").fadeIn(200);
        }
    }
    
    if ($(".graph_wep_performance").length){
        function weaponKillPie(data) {
            $.plot($(".graph_wep_performance"), data, 
            {
                series: {
                    pie: { 
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            threshold : .015,
                            radius: 1,
                            formatter: function(label, series){
                                return '<div style="font-size:9pt;text-align:center;padding:1px;color:white;">'+label+'<br/>'+Math.round(series.percent*100)/100+'% ('+series.data[0][1]+')</div>';
                            },
                            background: { opacity: 0.8 }
                        }
                    }
                },
                grid:{
                    hoverable : true,
                    clickable : true
                },
                legend : {
                    show : false
                }
            });
        }
        $.ajax({
            url: "encode_weapon_chart.php",
            method: 'GET',
            dataType: 'json',
            success: weaponKillPie
        });
        //if someone mouses over a particular section of the pie
        var previousHover = null;
        $(".graph_wep_performance").bind("plothover",function(event,pos,item){
            if (item){
                if (previousHover != item.seriesIndex) {
                    previousHover = item.seriesIndex;
                    
                    var name = item.series.label.split("KILLS")[0];
                    var dt;
                    //gives item name, pass that into ajax script
                   
                    $(".top10_contrib > h3").remove();
                    $("table.contrib_table").empty();
                    dt = null;

                    function pieContrib(data){
                        //create table rows with data and display it
                        $('#loading').hide(); //remove loading img
                        
                        $(".top10_contrib > h3").remove();
                        $("table.contrib_table").empty();
                        dt = null;           
                        var table_obj = $('.contrib_table');

                        table_obj.append($('<thead><tr><td id="steamid">OWNER</td><td id="itemid">WEAPON ID</td><td id="kills">KILL COUNT</td></tr></thead>'));
                        table_obj.parent().prepend($('<h3>'+name+'</h3>')); 
                        
                        data.sort(sort_by('value',true,parseInt));
                        $.each(data, function(index, item){
                             table_obj.append($('<tr><td id="steamid"><a href=?userid='+item.steamid+'>'+item.owner_name+'</a></td><td id="itemid"><a href=?userid='+item.steamid+'&item='+item.itemid+'>'+item.itemid+'</a></td><td id="kills">'+item.value+'</td></tr>'));
                        })

                        /*var dt = $("table.contrib_table").dataTable({
                            "bPaginate": false,
                            "bLengthChange": false,
                            "bFilter": false,
                            "bSort": false,
                            "bInfo": false,
                            "bAutoWidth": false,
                            "bRetrieve" : true
                        });*/
                    }
                    $('#loading').show();//show loading img
                    
                    $.ajax({
                        url: "encode_pie_contrib.php?item=" + name,
                        method: 'GET',
                        dataType: 'json',
                        success: pieContrib
                    });
                }
            }
            else{
                previousHover = null;
            }
        });
        
       //by triptych @ http://stackoverflow.com/questions/979256/how-to-sort-an-array-of-javascript-objects
       var sort_by = function(field, reverse, primer){
           var key = function (x) {return primer ? primer(x[field]) : x[field]};
           return function (a,b) {
               var A = key(a), B = key(b);
               return (A < B ? -1 : (A > B ? 1 : 0)) * [1,-1][+!!reverse];                
           }
        }
    }
});  
    
