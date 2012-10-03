$(document).ready(function(){
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
        $(".backpack_partition").mouseenter(function() {
           if (backpack_exists==false){
                backpack_exists=true;
                $.gritter.add({
                    title: 'This is your backpack!',
                    text: 'Hovering over each item will show you detailed information about it, like name, item quality, strange ranking and tracking status. If something is being tracked, it has a green checkmark, and it is actively checked every hour. Otherwise, it will show up with a red cross. You can set an item to be tracked, view its stats, or see its details by clicking on it.',
                    time:6000,
                    after_close: function(){
                        backpack_exists=false;
                    }
                });
            }
        });

        var control_exists=false;
        $(".control_wrapper").mouseenter(function() {
           if (control_exists==false){
                control_exists=true;
                $.gritter.add({
                    title: 'These are filtering options!',
                    text: 'You can choose to see all the item types, or only a subset by only ticking off what types you want to see. You can also sort the items by select a sort criteria from the dropdown form.',
                    time:6000,
                    after_close: function(){
                        control_exists=false;
                    }
                });
            }
        });

        var header_exists =false;
        $(".header").mouseenter(function() {
           if (header_exists==false){
                header_exists=true;
                $.gritter.add({
                    title: 'This is the navigation and title!',
                    time:6000,
                    text: 'The owner of this backpack is linked here on the left, and you can visit your own profile and privacy settings for your stats (only if you&apos;re logged in), check out the FAQ, the top 50 items tracked, or search and return to the home form.',
                    after_close: function(){
                        header_exists=false;
                    }
                });
            }
        });

               
    }
});
