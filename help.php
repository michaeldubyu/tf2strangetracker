<?php

    render_plain_header();
    include_once("steamsignin.php");

    if (!isset($_SESSION['steamID'])) $genurl = SteamSignIn::genUrl();
    else $genurl = "?userid=$_SESSION[steamID]";

    echo "<div class='help_wrapper'>";
        echo "<div class='help_content'>";
            echo "<h2>in which some mail is read</h2>";
            echo "<div class='question'>Q : What is this?</div>";
            echo "<div class='answer'>A : This site aims to help users view strange kills over time on their strange weapons.</div><BR \>";
            echo "<div class='question'>Q : How do I use this?</div>";
            echo "<div class='answer'>You can click the <a class = 'contentLink' href=''>search</a>
            button and enter in a steamid, or community ID. If you don't know either of those, you may login
            with steam at the bottom of any page.</div><BR \>";
            echo "<div class='question'>Q : How do I track weapons?</div>";
            echo "<div class='answer'>You can track anyone's weapons that hasn't been added my database yet
            for their backpack, then by clicking the weapon - which then shows you the detailed view and 
            data to be shown, or the tracking option.</div><BR \>";
            echo "<div class='question'>Q : How can I set my privacy settings? I don't want anyone seeing my stats other than me.</div>";
            echo "<div class='answer'>A : You can <a class='contentLink' href='?p=$genurl'>log in</a> to manage your privacy.</div><BR \>";
            echo "<div class='question'>Q : Is logging in through steam secure?</div>";
            echo "<div class='answer'>A : Yes - like all sites, you do not submit any sensitive information to me,
            logging in only returns me your steamid - which anyone could obtain easily. The steamid just makes my life easier in finding your
            backpack.</div><BR \>";
            echo "<div class='question'>Q : How do you do this?</div>";
            echo "<div class='answer'>A : The <a class='contentLink' href='http://steamcommunity.com/dev/'>Steam Web API</a> holds
            a lot of information. I mean like a lot. I simply make requests to their servers, save items I'm
            interested in tracking into a database. Every hour, I run a cronjob to go through this list, and
            save the corresponding information. I use <a class='contentLink' href='http://code.google.com/p/flot'>flot</a> to plot data.</div><BR \>";
            echo "<div class='question'>Q : How can I report bugs?</div>";
            echo "<div class='answer'>A : Let me know at michaeldubyu@gmail.com, or <a href='http://steamcommunity.com/id/geogaddithecat/'>my profile</a>.</div><BR \>";
            echo "<div class='question'>Q : Why is there data missing??</div>";
            echo "<div class='answer'>A : Check out <a href='/lib/lock'>this log file</a>. If there's a hourly data point missing, most likely it's because the steam web api did not respond to my request. Otherwise, please let me know!</div><BR \>";
            echo "<div class='question'>Q : What browsers do you support?</div>";
            echo "<div class='answer'>A : Updated versions of Chrome, Firefox, and Safari.</div><BR \>";

        echo "</div>";
        //render_ads();
    //render_ads();
    echo "</div>";        

    render_footer();
?>
