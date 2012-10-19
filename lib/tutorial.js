$(window).load(function(){
    var split = location.search.replace('?', '').replace('&','=').split('=');

    if (split[1]=='tutorial'){
        $.extend($.gritter.options, {
            fade_in_speed: 'medium',
            fade_out_speed: 2000,
            position:'bottom-right'
        });

        $.gritter.add({
            title: 'Welcome to the tutorial!',
            text: 'This is a sample backpack. You can see items with their qualities and attributes after you&apos;ve entered your steamid on the main page.',
            sticky : true,
            after_close: function start(){
                 $.gritter.add({
                    title: 'Go ahead and hover over something!',
                    text: 'I&apos;ll explain how it works from over here!',
                    time:6000
                 });
            }
        });

        var backpack_exists=false;
        var bp_gritter_id;
        $(".backpack_partition").mouseenter(function() {
            $(".backpack_partition").css("border","2px solid grey");  
            if (backpack_exists==false){
                backpack_exists=true;
                var bp_id = $.gritter.add({
                    title: 'This is your backpack!',
                    text: 'Hovering over each item will show you detailed information about it, like name, item quality, strange ranking and tracking status. If something is being tracked, it has a <span style="color:#5DFC0A">green checkmark</span>, and it is actively checked every hour. Otherwise, it will show up with a <span style="color:#FF0000">red x</span>. You can set an item to be tracked, view its stats, or see its details by clicking on it.',
                    sticky:true,
                    after_close: function(){
                        backpack_exists=false;
                    }
                });
                bp_gritter_id = "gritter-item-" + bp_id;                 
                $("#"+bp_gritter_id).addClass("hover");
             }
            else{
                $("#"+bp_gritter_id).addClass("hover");
            }

            $("#"+bp_gritter_id).mouseenter(function(){
                $(".backpack_partition").css("border","2px solid grey");                
            });
            $("#"+bp_gritter_id).mouseleave(function(){
                $(".backpack_partition").css("border","0");                
            });

        });

        $(".backpack").mouseleave(function(){
            if (backpack_exists==true){
                $("#"+bp_gritter_id).removeClass("hover");              
            }
            $(".backpack_partition").css("border","0");  
        });

        var control_exists=false;
        var cp_gritter_id;
        $(".control_wrapper").mouseenter(function() {
           $(".control_wrapper").css("border","2px solid grey");  
           if (control_exists==false){
                control_exists=true;
                var cp_id = $.gritter.add({
                    title: 'These are filtering options!',
                    text: 'You can choose to see all the item types, or only a subset by only ticking off what types you want to see. You can also sort the items by select a sort criteria from the dropdown form.',
                    sticky:true,
                    after_close: function(){
                        control_exists=false;
                    }
                });
                cp_gritter_id = "gritter-item-" + cp_id;
                $("#"+cp_gritter_id).addClass("hover");
            }
            else{
                $("#"+cp_gritter_id).addClass("hover");
            }

            $("#"+cp_gritter_id).mouseenter(function(){
                $(".control_wrapper").css("border","2px solid grey");                
            });
            $("#"+cp_gritter_id).mouseleave(function(){
                $(".control_wrapper").css("border","0");                
            });

        });

        $(".control_wrapper").mouseleave(function(){
            if (control_exists==true){
                $("#"+cp_gritter_id).removeClass("hover");                
            }
            $(".control_wrapper").css("border","0");                
        });


        var nav_gritter_id;
        var header_exists =false;
        $(".header").mouseenter(function() {
           $(".header").css("border","2px solid grey");  
           if (header_exists==false){
                header_exists=true;
                var nav_id = $.gritter.add({
                    title: 'This is the navigation and title!',
                    sticky: true,
                    text: 'The owner of this backpack is linked here on the left, and you can visit your own profile and privacy settings for your stats (only if you&apos;re logged in), check out the <a class="contentLink" style="color:darkOrange; text-decoration:underline;" href="/?p=help">FAQ</a>, the <a class="contentLink" style="color:darkOrange; text-decoration:underline;" href="/?p=top10">top 50 items tracked</a>, or <a class="contentLink" style="color:darkOrange;text-decoration:underline;" href="/">search and return to the home form.</a>',
                    after_close: function(){
                        header_exists=false;
                    }
                });
                nav_gritter_id = "gritter-item-" + nav_id;
                $("#"+nav_gritter_id).addClass("hover");
            }
            else{
                $("#"+nav_gritter_id).addClass("hover");       
            }

            $("#"+nav_gritter_id).mouseenter(function(){
                $(".header").css("border","2px solid grey");                
            });
            $("#"+nav_gritter_id).mouseleave(function(){
                $(".header").css("border","0");                
            });
        });

        $(".header").mouseleave(function(){
            if (header_exists==true){
                $("#"+nav_gritter_id).removeClass("hover");                
            }
            $(".header").css("border","0");                
        });               
    }
});
