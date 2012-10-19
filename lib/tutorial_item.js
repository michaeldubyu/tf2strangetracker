$(window).load(function(){
    var split = location.search.replace('?', '').replace('&','=').split('=');

    if (split[1]=='tutorial_item'){
       var itemid = split[3];

        $.extend($.gritter.options, {
            fade_in_speed: 'medium',
            fade_out_speed: 1000,
            position:'bottom-right'
        });

        var item_exists = false;
        var item_gritter_id;
        $(".item_desc_all").mouseover(function(){
            $(".item_desc_all").css("border","2px solid grey");
            if (item_exists==false){ 
                item_exists=true;
                var item_id = $.gritter.add({
                    title: 'This is a detailed description of an item.',
                    text: 'You can see interesting tidbits like the current and previous ID of the item, the strange kill rank and kill count. Initially, there is no data for strange weapons when they are not being actively tracked.',
                    sticky : true,
                    after_close: 
                        function start(){
                            item_exists=false;
                        }
                });

                item_gritter_id = "gritter-item-" + item_id;
                $("#"+item_gritter_id).addClass("hover");
            }else{
                $("#"+item_gritter_id).addClass("hover");            
            }

            $("#"+item_gritter_id).mouseenter(function(){
                $(".item_desc_all").css("border","2px solid grey");
            });
            $("#"+item_gritter_id).mouseleave(function(){
                $(".item_desc_all").css("border","0");
            });
        });

        $(".item_desc_all").mouseleave(function(){
            if (item_exists==true){
                $("#"+item_gritter_id).removeClass("hover");
            }
            $(".item_desc_all").css("border","0");  
        });

        var sidebar_exists = false;
        var sidebar_gritter_id;
        $(".sidebar").mouseover(function(){
            $(".sidebar").css("border","2px solid grey");
            if (sidebar_exists==false){ 
                sidebar_exists=true;
                var sidebar_id = $.gritter.add({
                    title: 'These are administrative options.',
                    text: 'If you are the owner of the item, you can <span style="color:#5DFC0A">start</span> or <span style="color:red">stop</span> tracking the item.',
                    sticky : true,
                    after_close: 
                        function start(){
                            sidebar_exists=false;
                        }
                });

                sidebar_gritter_id = "gritter-item-" + sidebar_id;
                $("#"+sidebar_gritter_id).addClass("hover");
            }else{
                $("#"+sidebar_gritter_id).addClass("hover");            
            }

            $("#"+sidebar_gritter_id).mouseenter(function(){
                $(".sidebar").css("border","2px solid grey");
            });
            $("#"+sidebar_gritter_id).mouseleave(function(){
                $(".sidebar").css("border","0");
            });
        });

        $(".sidebar").mouseleave(function(){
            if (sidebar_exists==true){
                $("#"+sidebar_gritter_id).removeClass("hover");
            }
            $(".sidebar").css("border","0");  
        });

        var tracklink_gritter_id;
        var tracklink_exists=false;
        $("#no_data").mouseover(function() {

           $("#"+tracklink_gritter_id).css("display","block");
            
           if (tracklink_exists==false){
                tracklink_exists=true;
                var tracklink_id = $.gritter.add({
                    title: 'Graph data is shown here.',
                    text: 'If there is data to be shown.',
                    sticky:true,
                    after_close: function(){
                        tracklink_exists=false;
                    }
                });
                tracklink_gritter_id = "gritter-item-" + tracklink_id;
            }
            $("#"+tracklink_gritter_id).addClass("hover");

            $("#no_data").mouseleave(function(){
               $("#"+tracklink_gritter_id).removeClass("hover");
            });

            $("#track_link").click(function(){
                $("#"+tracklink_gritter_id).css("display","none");
            });

        });

        var stop_gritter_id;
        var start_gritter_id;
        var tracked_exists=false;
        $("#track_link").click(function(){

            plotRandDaily();          
            plotRandWeekly(); 

            $("#stop_track_link").css("display","list-item");

            if (tracked_exists==false){
                tracked_exists=true;
                var start_id = $.gritter.add({
                    title: '<span style="color:#5DFC0A">This item is now being tracked!</span>',
                    text: 'I would then check the stats on it by the hour and show you the results here. Obviously this is just random data I&apos;ve generated to show you what happens for now. You can hover over each point to see the numerical time and kill count values.',
                    sticky: true,
                    after_close: function(){
                        tracked_exists=false;
                    }
                });
                start_gritter_id = "gritter-item-"+start_id;
            }

            $("#no_data").css("display","none");
            $(".graph_daily_wrapper").css("display","block")        
            $(".graph_weekly_wrapper").css("display","block")        
            $("#"+stop_gritter_id).css("display","none");
            $("#"+start_gritter_id).css("display","block");
            $("#"+tracklink_gritter_id).css("display","none");
        }); 
           
        var stoptracking_exists=false;
        $("#stop_track_link").click(function(){

            if (stoptracking_exists==false){
                stoptracking_exists=true;
                    var stop_id = $.gritter.add({
                        title: '<span style="color:red">This item is no longer being tracked!</span>',
                        text: 'I&apos;ll still hold onto the data in case you change your mind, but I will not be checking the kill count hourly until then!',
                        sticky: true,
                        after_close: function(){
                            stoptracking_exists=false;
                        }
                    });
                stop_gritter_id = "gritter-item-"+stop_id;
            }
            $("#no_data").css("display","block");
            $(".graph_daily_wrapper").css("display","none");      
            $(".graph_weekly_wrapper").css("display","none");
            $("#stop_track_link").css("display","none");                       
            $("#"+start_gritter_id).css("display","none");
            $("#"+stop_gritter_id).css("display","block");
        });

        function plotRandDaily(){
            var d = new Date();
            var twentyfourago = d.getTime() - (24*3600000); //already time in ms 24 hrs ago
            
            var rdata = new Array();
            
            for (var i=0;i<24;i++){
                var data = new Array();
                data[0] = twentyfourago + (i*3600000);
                data[1] = String(getRandomInt(0,100));
                rdata[i] = data;
            }
            
            var dataobj = new Array();
            dataobj = {"data":rdata, "color":"#FFAA42", "label":"24 Hour Sample Overview"};
            var wrap = new Array();
            wrap[0] = dataobj;
            
            var length = wrap.length;
            var options = {
               lines: { show: true },
               legend: { show : false },
               points: { show: true, hoverable:true },
               grid: { hoverable: true, clickable: true },
               xaxis: { mode : "time", timeformat : "%H:%M", minTickSize:[1,"hour"] },
               yaxis: { minTickSize: "1", tickDecimals : "0" }
            };
            console.log(wrap);
            $.plot($(".graph_ex_daily"), wrap, options);

            var previousPoint = null;
            $(".graph_ex_daily").bind("plothover", function (event,pos,item){
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

        function plotRandWeekly(){
            var d = new Date();
            var sevendaysago = d.getTime() - (7*24*3600000); //already time in ms 7 days ago
            
            var rdata = new Array();
            
            for (var i=0;i<168;i++){
                var data = new Array();
                data[0] = sevendaysago + (i*3600000);
                data[1] = String(getRandomInt(0,100));
                rdata[i] = data;
            }
            
            var dataobj = new Array();
            dataobj = {"data":rdata, "color":"#FFAA42", "label":"24 Hour Sample Overview"};
            var wrap = new Array();
            wrap[0] = dataobj;
            
            var length = wrap.length;
            var options = {
                lines: { show: true },
                legend: { show : false },
                points: { show: true, hoverable:true },
                grid: { hoverable: true, clickable: true },
                xaxis: { mode : "time", minTickSize:[1,"day"], timeformat : "%m/%0d"},
                yaxis: { minTickSize: "1", tickDecimals : "0" }
            };
            console.log(wrap);
            $.plot($(".graph_ex_weekly"), wrap, options);

            var previousPointWeekly = null;
            $(".graph_ex_weekly").bind("plothover", function (event,pos,item2){
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
                    }else{
                        $("#graph_info_weekly").remove();
                        previousPointWeekly = null;
                    }
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

        function getRandomInt (min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
         }

        
    }
});
