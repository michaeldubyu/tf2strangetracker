history.navigationMode = 'compatible';
$(document).ready(function(){                
    var split = location.search.replace('?', '').replace('&','=').split('=');
    console.log(split);    
    if ($(".graph_daily").length){
        $.ajax({
            url: "encode_daily_graph.php?itemid="+split[3],
            method: 'GET',
            dataType: 'json',
            success: onOutboundReceived
        });
        $('#loading').show();//show loading img
        function onOutboundReceived(series) {
            var length = series.length;
            var finalData = series;
            var options = {
                lines: { show: true },
                legend: { show : false },
                points: { show: true, hoverable:true },
                grid: { hoverable: true, clickable: true },
                xaxis: { mode : "time", timeformat : "%H:%M", minTickSize:[1,"hour"] },
                yaxis: { minTickSize: "1", tickDecimals : "0" }
            };
            $.plot($(".graph_daily"), finalData, options);
            $('#loading').hide();//hide loading img
        }
        
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
        $.ajax({
            url: "encode_weekly_graph.php?itemid="+split[3],
            method: 'GET',
            dataType: 'json',
            success: onOutboundReceivedWeekly
        });
        $('#loading').show();//show loading img

        function onOutboundReceivedWeekly(series) {
            var length = series.length;
            var finalData = series;
            var options = {
                lines: { show: true },
                legend: { show : false },
                points: { show: true, hoverable:true },
                grid: { hoverable: true, clickable: true },
                xaxis: { mode : "time", minTickSize:[1,"day"], timeformat : "%m/%0d"},
                yaxis: { minTickSize: "1", tickDecimals : "0" }
            };
            $.plot($(".graph_weekly"), finalData, options);
            $('#loading').hide();//hide loading img
        }
        
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
        $(".graph_wep_performance").bind("plotclick",function(event,pos,item){
            if (item){
                if (previousHover != item.seriesIndex) {
                    previousHover = item.seriesIndex;
                    
                    $(".top10_contrib > h3").remove();
                    $("table.contrib_table").empty();

                    var name = item.series.label.split("KILLS")[0];
                   //gives item name, pass that into ajax script
                   
                    function pieContrib(data){
                        //create table rows with data and display it
                        $('#loading').hide(); //remove loading img
                        var table_obj = $('.contrib_table');

                        table_obj.append($('<tr><td id="steamid">OWNER STEAMID</td><td id="itemid">WEAPON ID</td><td id="kills">KILL COUNT</td></tr>'));
                        table_obj.parent().prepend($('<h3>'+name+'</h3>'));
                        
                        data.sort(sort_by('value',true,parseInt));
                        $.each(data, function(index, item){
                             table_obj.append($('<tr><td id="steamid"><a href=?userid='+item.steamid+'>'+item.steamid+'</a></td><td id="itemid"><a href=?userid='+item.steamid+'&item='+item.itemid+'>'+item.itemid+'</a></td><td id="kills">'+item.value+'</td></tr>'));
                        })
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
    
