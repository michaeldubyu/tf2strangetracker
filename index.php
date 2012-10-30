<?php 
session_start(); 
header("Cache-Control: no-store,must-revalidate");
header("Pragma: private");
header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

if (isset($_GET['logout'])){
        if (isset($_SESSION['steamID'])){
            session_start();
            session_unset();
            session_destroy();
            echo "<script>window.location.replace('http://tf2s.info');</script>";
        }
}
?>
<!DOCTYPE html>
<html>
	<head>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35707806-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
        <META http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="description" content="Team Fortress 2 Strange Weapon Tracking Website!" />
		<link rel="stylesheet" type="text/css" href="tf2_tracker.css" />
        <link rel="stylesheet" type="text/css" href="lib/gritter/css/jquery.gritter.css" />
        <script type="text/javascript" src="lib/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="lib/jquery.tinysort.min.js"></script>
        <script type="text/javascript" src="lib/flot/jquery.flot.js"></script>
        <script type="text/javascript" src="lib/flot/jquery.flot.selection.js"></script>
        <script type="text/javascript" src="lib/flot/jquery.flot.pie.js"></script>
        <script type="text/javascript" src="lib/backpack.js"></script>
        <script type="text/javascript" src="lib/graphs.js"></script>
        <script type="text/javascript" src="lib/tutorial.js"></script>
        <script type="text/javascript" src="lib/tutorial_item.js"></script>
        <script type="text/javascript" src="lib/gritter/js/jquery.gritter.js"></script>
        <script type="text/javascript" src="lib/twitter.js"></script>
        <!--[if IE]><script type="text/javascript" src="lib/flot/excanvas.js"></script><![endif]-->
	</head>
	
	<title>TF2 Strange Tracker</title>
	<body>
	
<?php 
include("render_functions.php");
if (isset($_SESSION['last_activity']) && time() - 3600 > $_SESSION['last_activity'])
{
    //destroy dession if it was older than half an hour
    session_unset();
    session_destroy();
}
if (isset($_GET['userid']) && $_GET['userid'] != '' && $_GET['userid'] != null) 
{
    //display backpack for this user
    include ("functions.php");
    
    $web_api_status = true;

    $id = strip_tags($_GET['userid']);
    $id = trim($id);
    $valid = array('-','_');

    if (!ctype_alnum(str_replace($valid,'',$id))) $profile = null;
    else $profile = get_steam_profile_xml($id);

    if (isset($profile->steamID64) && $profile->steamID64 != null) 
    {
		$steamid = $profile->steamID64;
        $backpack = get_tf2_backpack_xml($steamid);

		if (isset($backpack->status)) $status=$backpack->status;
		else $web_api_status=false;

		if ($web_api_status==true && $status!='15')
		{
            $name = simplexml_load_string($profile->steamID->asXML(),null,LIBXML_NOCDATA);
			$display_name = strtoupper($name);
			$user_status = strtolower(simplexml_load_string($profile->onlineState->asXML(),null,LIBXML_NOCDATA));
			$avatar_full = simplexml_load_string($profile->avatarMedium->asXML(), null, LIBXML_NOCDATA);
			
            render_profile_header($steamid,$avatar_full,$user_status,$display_name);	

            $schema = simplexml_load_file("{$_SERVER['DOCUMENT_ROOT']}/lib/schema.xml");		

            if (isset($_GET['item']) && $_GET['item'] != '' && $_GET['item'] != null)
            {
                $itemid = $_GET['item'];
                
                $item = array();
                $items[] = $itemid;
                $item_defindex = itemmap_filter_defindex_and_node($backpack,"id",$items,"defindex");
                $single_defindex = $item_defindex[$itemid];
                                
                $defindex = array();
                $defindex[] = $item_defindex[$itemid];
                $item_image_url = itemmap_filter_defindex_and_node($schema,"defindex",$defindex,"image_url_large");
                
                $item_name = itemmap_filter_defindex_and_node($schema,"defindex",$defindex,"item_name");
                $item_quality = itemmap_filter_defindex_and_node($backpack,"id",$items,"quality");
                $item_custom_name = itemmap_filter_defindex_and_node($backpack,"id",$items,"custom_name");
                $item_custom_desc = itemmap_filter_defindex_and_node($backpack,"id",$items,"custom_desc");
                $item_strange_kills = attrmap_filter_defindex_and_node($backpack,$items,"214","value");
                $item_previous_id = itemmap_filter_defindex_and_node($backpack,"id",$items,"original_id");
                
                $single_quality = tf2_get_quality($item_quality[$itemid]);
				$single_item_name = $item_name[$single_defindex];
                $single_item_custom_desc = $item_custom_desc[$itemid];
                $single_item_custom_name = $item_custom_name[$itemid];
				@$single_item_strange_kills = $item_strange_kills[$itemid];
                @$single_item_previous_id = $item_previous_id[$itemid];
                  
                $single_item_name = str_replace('The ','',$single_item_name);
                $single_item_name = str_replace('Upgradeable TF_WEAPON_','',$single_item_name);

                echo '<div class="item_page_all clear">';

                render_item_desc($steamid,$itemid, $single_quality,$item_image_url,$single_defindex, $single_item_strange_kills,$single_item_name, $single_item_custom_name,$single_item_custom_desc,$single_item_previous_id,$single_item_strange_kills, false);         
                render_item_graphs($single_quality,$itemid, $steamid);

                echo '</div>';
                echo '</div>';
            }
            else if ($backpackxml->result!='15')
            {//normal render
                render_backpack($backpack,$schema,$steamid,$profile,true,false);
                save_xml($backpack,"/backpacks/{$steamid}_backpack.xml");	        
                save_xml($profile,"/profiles/{$steamid}_profile.xml"); 
            }
        }
        render_ads();
        echo '</div>';
        echo '</div>';
        render_footer();       
	}
	else
    {
        render_plain_header();
        if (is_numeric($id)) echo "<p id='error'>Invalid Community ID or response from Steam Servers for your profile - <a class='contentLink' href='http://steamcommunity.com/profiles/$id/?xml=1'>Is this also inaccessible?</a></p>";
        else echo "<p id='error'>Invalid Community ID or response from Steam Servers for your profile - <a class='contentLink' href='http://steamcommunity.com/id/$id/?xml=1'>Is this also inaccessible?</a></p>";
        render_footer();
    }
}
else if (isset($_GET['p']) && $_GET['p'] != '' && $_GET['p'] != null)
{

    //render_ads();
    $page = $_GET['p'];
    if ($page == "top10")
    {
        render_plain_header();

        //get top 10 max values for distinct items
        include_once('scripts/dbconfig.php');
        
        $mysqli_t = mysqli_connect($host,$username,$password,$db);
        mysqli_query($mysqli_t,"SET NAMES 'utf8'");

        if(mysqli_connect_errno()) echo mysqli_connect_error();
        $time = date('Ym');
        $table = "events_$time"; //select from current month's db

        $query_t = "SELECT items_top_tracked.itemid, items_top_tracked.steamid, items_top_tracked.value, item_table.item_name, item_table.owner_name FROM items_top_tracked LEFT JOIN item_table ON items_top_tracked.itemid = item_table.item_id ORDER BY value DESC LIMIT 0,25";
        $query_t = mysqli_real_escape_string($mysqli_t,$query_t);
        
        $top25_data = array();
        
        if ($result_top10=mysqli_query($mysqli_t,$query_t))
        {		
            while ($row_top10 = mysqli_fetch_assoc($result_top10))
            {
                $top25_data[] = $row_top10;
            }
        }	
        $query_right = "SELECT items_top_tracked.itemid, items_top_tracked.steamid, items_top_tracked.value, item_table.item_name, item_table.owner_name FROM items_top_tracked LEFT JOIN item_table ON items_top_tracked.itemid = item_table.item_id ORDER BY value DESC LIMIT 25,25";
        $query_right = mysqli_real_escape_string($mysqli_t,$query_right);
        
        $top25_right_data = array();

        if ($result_top25_right=mysqli_query($mysqli_t,$query_right))
        {		
            while ($row_top25_right = mysqli_fetch_assoc($result_top25_right))
            {
                $top25_right_data[] = $row_top25_right;
            }
		}
        
        mysqli_close($mysqli_t);
        mysqli_free_result($result_top10);
        mysqli_free_result($result_top25_right);
        
        echo "<div class='top10_wrapper clear'>";
            echo "<div id='top10_title'>TOP 50 WEAPONS CURRENTLY BEING TRACKED</div><BR \>";
            echo "<div class='top10_table_wrapper clear'>";
            echo "<div class='top10_left'>";
                echo "<table class='top10'><tbody>";
                echo "<tr><td id='rank'>rank</td><td id='itemid'>itemid</td><td id='kills'>kills</td><td id='weapontype'>weapon type</td><td id='steamid'>name</td></tr>";
                $rank = 1;
                foreach ($top25_data as $wep)
                {
                    echo "<tr>";
                    echo "<td id='rank'>$rank</td>";
                    foreach ($wep as $key => $value)
                    {
                        if ($key=='steamid') $steamid = $value;
                        if ($key=='itemid') echo "<td><a href='?userid=$wep[steamid]&item=$value'>$value</a></td>";
                        if ($key=='item_name') echo "<td id='weapontype'>$value</td>";
                        if ($key=='value') echo "<td id='kills'>$value</td>";
                        if ($key=='owner_name') echo "<td><a href='?userid=$steamid'>$value</a></td>";
                    }
                    echo "</tr>";
                    $rank++;
                }
                echo "</tbody></table>";
            echo "</div>";
            echo "<div class='top10_right'>";
                echo "<table class='top10'><tbody>";
                echo "<tr><td id='rank'>rank</td><td id='itemid'>itemid</td><td id='kills'>kills</td><td id='weapontype'>weapon type</td><td id='steamid'>name</td></tr>";
                foreach ($top25_right_data as $wep)
                {
                    echo "<tr>";
                    echo "<td id='rank'>$rank</td>";
                    foreach ($wep as $key => $value)
                    {
                        if ($key=='steamid') $steamid = $value;
                        if ($key=='itemid') echo "<td><a href='?userid=$wep[steamid]&item=$value'>$value</a></td>";
                        if ($key=='item_name') echo "<td id='weapontype'>$value</td>";
                        if ($key=='value') echo "<td id='kills'>$value</td>";
                        if ($key=='owner_name') echo "<td><a href='?userid=$steamid'>$value</a></td>";
                    }
                    echo "</tr>";
                    $rank++;
                }
                echo "</tbody></table>";
           echo "</div>";
        echo "</div>";            
        render_ads();
        echo "</div>";
        render_footer();
    }
    else if ($page=="tutorial"){
        include("tutorial.php");
    }
    else if ($page=="tutorial_item"){
        include("tutorial_item.php");
    }
    else if ($page=="help")
    {
        include("help.php");
    }
    else if ($page=='usercp' && isset($_SESSION['steamID']) && ($_SESSION['steamID']!=null))
    {   
        //usercp screen
         //privacy settings
        //0 for public, 1 for private

        render_plain_header();

        include_once('scripts/dbconfig.php');
        $mysql = mysqli_connect($host,$username,$password,$db);
        $steamid = $_SESSION['steamID'];
        $check = "SELECT * FROM user where steamid = '$steamid'";

        $db_stat = 0;
        $db_privacy = 0;
        $track_all = 0;

        if ($result = mysqli_query($mysql,$check))
        {
            if (mysqli_num_rows($result)!=0)
            {
                $re = mysqli_fetch_assoc($result);
                $db_stat = $re['stat_privacy'];
                $db_privacy = $re['track_privacy'];
                $db_track_all = $re['track_all'];
            }
        }
        echo '<div class="privacy_form">';
            echo '<h2>PRIVACY SETTINGS FOR YOUR ITEMS</h2>';
            echo '<form name="privacy" action="#" method="POST">';
            if (isset($db_stat) && $db_stat==0){
                echo '<input type="radio" checked="checked" name="stat_privacy" value="0" />I want to show all my stats to everyone. -OR- ';
                echo '<input type="radio" name="stat_privacy" value="1" >I want to show my stats to no one except me.<BR \><BR \>';
            }
            else if (isset($db_stat) && $db_stat==1){
                echo '<input type="radio" name="stat_privacy" value="0">I want to show all my stats to everyone. -OR- ';
                echo '<input type="radio" checked="checked" name="stat_privacy" value="1" >I want to show my stats to no one except me.<BR \><BR \>';
            }
            if (isset($db_privacy) && $db_privacy==1){
                echo '<input type="radio" name="track_privacy" value="0" />I want to let anyone start tracking my weapons for me. -OR- ';
                echo '<input type="radio" name="track_privacy" checked="checked" value="1" />I want to have exclusive control over what is tracked and what isn\'t.<BR \><BR \>';
            }
            else if (isset($db_privacy) && $db_privacy==0){
                echo '<input type="radio" name="track_privacy" checked="checked" value="0" />I want to let anyone start tracking my weapons for me. -OR- ';
                echo '<input type="radio" name="track_privacy" value="1" />I want to have exclusive control over what is tracked and what isn\'t.<BR \><BR \>';
            }
            if (!isset($db_track_all)){//if no option has ever been selected
                echo '<input type="radio" name="track_all" value="1" />I want to have all my strange weapons, current and future, to be tracked automatically. (OPTIONAL) <BR \><BR \>';
            }		
            else if (isset($db_track_all) && $db_track_all==1){
                echo '<input type="radio" name="track_all" checked="checked" value="1" />I want to have all my strange weapons, current and future, to be tracked automatically. (OPTIONAL) <BR \><BR \>';
            }
    //1 for track_all means yes all weapons will be tracked
            echo '<input id="sub" type="submit" value="Save Settings" />';
            echo '</form>';
        echo '</div>';
       render_ads(); 
       render_footer();

        if ((isset($_POST['stat_privacy']) || isset($_POST['track_privacy']) || isset($_POST['track_all'])))
        {
            if (!isset($_POST['track_all'])) $track_all = 0;
            else $track_all = $_POST['track_all'];
            $query = "REPLACE INTO user (steamid,stat_privacy,track_privacy,track_all) VALUES ('$_SESSION[steamID]','$_POST[stat_privacy]','$_POST[track_privacy]','$track_all')";
            mysqli_query($mysql,$query);	
            echo '<script>location.reload();</script>'; //force page reload otherwise chrome h8s you
        }
    }
}
else
{
    include ("functions.php");
    include_once("steamsignin.php");
    if (!isset($_SESSION['steamID'])) $genurl = SteamSignIn::genUrl();
    else $genurl = "?userid=$_SESSION[steamID]";
	render_plain_header();
	echo '<div class="form clear">';
		echo "<div id = 'form'>
				 <form action='' id='formstyle' method='GET'>
					 WELCOME! FIRST TIME?<BR \><BR \>
					 <input size='50' type='text' style='text-align:center;background:#2D2828;color:#808080; border:1px solid;' name='userid' id='userid' />
					 <BR \><BR \>
					 Enter a Steam ID, Community ID, or <a href='$genurl' class='contentLink'>Log In!</a>
                </form>";
        echo '</div>'; 
            include_once('scripts/dbconfig.php');
            $mysqli_f = mysqli_connect($host,$username,$password,$db);
            if(mysqli_connect_errno()) echo mysqli_connect_error();
            
            $query_f = "SELECT COUNT(DISTINCT(`steamid`)) FROM `items`";
            $query_f = mysqli_real_escape_string($mysqli_f,$query_f);
            
            $result_f = mysqli_query($mysqli_f,$query_f);
            $row_users = mysqli_fetch_row($result_f);
            
            $query_i = "SELECT COUNT(DISTINCT(`itemid`)) FROM `items`";
            $query_i = mysqli_real_escape_string($mysqli_f, $query_i);
            
            $result_i = mysqli_query($mysqli_f,$query_i);
            $row_items = mysqli_fetch_row($result_i);
    
            $backpack_num = num_files("{$_SERVER['DOCUMENT_ROOT']}/backpacks");
            
            mysqli_free_result($result_i);
            mysqli_free_result($result_f);    
            
    echo "<div class='front_stats'>Currently tracking $row_users[0] unique users with $row_items[0] items.";
    //echo "To date have checked over unique $backpack_num backpacks from the Steam Community.";
    echo "</div>";

    //render_ads();
    echo '<div class="front_stats_all clear">';
		echo '<h3>TOP STRANGE WEAPON KILLS LOGGED<BR \>BREAKDOWN AND CONTRIBUTORS</h3>';
        //echo '<div class="all_graphs">';
            echo '<div class="contrib_wrapper clear"><div class="graph_wep_performance"></div><BR \>';
            echo '<div class="top10_contrib"><img id="loading" src="lib/spin.gif" /><h3 style="margin-top:150px;">Hover over each slice to see details!</h3><table class="contrib_table"></table></div></div>';
	echo '</div>';
    echo "<div class='twitter_container'><h2><div id='jstweets'><img id='twitter_img' src='lib/twitter.png' \><a style='color : #7eb3cd;' href='http://twitter.com/tf2stranges'>FROM TWITTER</a> - <a href='https://twitter.com/intent/user?screen_name=tf2stranges'>FOLLOW @TF2STRANGES</a><BR \><BR \></h2></div></div>";
    render_ads();
    echo '</div>';

    render_footer();
}

?>
	</body>
</html>
