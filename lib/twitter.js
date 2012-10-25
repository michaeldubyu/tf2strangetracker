// UPDATE 10-17-2012: change in Twitter API!
$.getJSON("https://api.twitter.com/1/statuses/user_timeline.json?screen_name=tf2stranges&count=1&callback=?",
 function(data){
  $.each(data, function(i,item){
   ct = item.text;
  // include time tweeted - thanks to will
    mytime = item.created_at;
    var strtime = mytime.replace(/(\+\S+) (.*)/, '$2 $1')
    var mydate = new Date(Date.parse(strtime)).toLocaleDateString();
    var mytime = new Date(Date.parse(strtime)).toLocaleTimeString();
   ct = ct.replace(/http:\/\/\S+/g,  '<a href="$&" target="_blank">$&</a>');
   //ct = ct.replace(/\s(@)(\w+)/g,    ' @<a onclick="javascript:pageTracker._trackPageview('/outgoing/twitter.com/');" href="http://twitter.com/$2" target="_blank">$2</a>');
   //ct = ct.replace(/\s(#)(\w+)/g,    ' #<a onclick="javascript:pageTracker._trackPageview('/outgoing/search.twitter.com/search?q=%23');" href="http://search.twitter.com/search?q=%23$2" target="_blank">$2</a>');
  $("#jstweets").append('<div class="twitterpost">'+ct + " <small><i><BR \>(" + mydate + " @ " + mytime + ")</i></small></div>");
  });
 });

