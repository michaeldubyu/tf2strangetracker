 
<?php
function render_profile_header($steamid,$avatar_full,$user_status,$display_name)
{
    include_once ("steamsignin.php");
    echo '<div class="header">';
		echo "<a href='http://steamcommunity.com/profiles/$steamid/'>";
		echo "<img src='$avatar_full' title='Go to this user&#x27;s steam profile page.' height='50' width='50' class='profile_pic' id='$user_status' \></a>";
		echo '<div class="nav">';
			echo "<h1><a class='contentLink' href='http://steamcommunity.com/profiles/$steamid/'>{$display_name}</a><span id='$user_status'>'S TF2 BACKPACK</span></h1>";
		echo '</div>';
  		        echo "<div class='nav_control' style='margin-top:20px;'>";
            echo '<a id="nav_home" href="/" ></a>';
            echo '<a id="nav_search" href="/" ></a>';
            echo '<a id="nav_top10" href="/?p=top10" ></a>';
            echo '<a id="nav_faq" href="/?p=help" ></a>';
  	       
    	if (isset($_SESSION['steamID']) && ($_SESSION['steamID']!=null)) {
            echo '<a id="nav_usercp_loggedin" href="/?p=usercp"></a>';
            echo "<a id='nav_profile_loggedin' href='/?userid=$_SESSION[steamID]'></a>";
        }
      	else{
           $genurl = SteamSignIn::genUrl();
           echo "<a id='nav_usercp' href='$genurl'></a>";
           echo "<a id='nav_profile' href='$genurl'></a>";
	}
    echo '</div>';
    echo '</div>';
}

function render_backpack($backpack,$schema,$steamid,$online=true,$tutorial)
{
    $ids = get_tf2_allitem_node($backpack,"id"); //ids of all items
    $defindexes = get_tf2_allitem_node($backpack,"defindex"); //defindexes of all items

    $item_map_image_url = itemmap_filter_defindex_and_node($schema,"defindex",$defindexes,"image_url");
    $item_map_name = itemmap_filter_defindex_and_node($schema,"defindex",$defindexes,"name");
    $item_map_quality = itemmap_filter_defindex_and_node($backpack,"id",$ids,"quality");		
	$item_map_defindex = itemmap_filter_defindex_and_node($backpack,"id",$ids,"defindex");	
    $item_map_custom_name = itemmap_filter_defindex_and_node($backpack,"id",$ids,"custom_name");
    $item_map_custom_desc = itemmap_filter_defindex_and_node($backpack,"id",$ids,"custom_desc");
    $item_map_inventory_pos = itemmap_filter_defindex_and_node($backpack,"id",$ids,"inventory");
    $item_previous_id = itemmap_filter_defindex_and_node($backpack,"id",$ids,"original_id");
    $attr_map_id_strange_kills = attrmap_filter_defindex_and_node($backpack,$ids,"214","value");
    $attr_map_id_painted = attrmap_filter_defindex_and_node($backpack,$ids,"142","float_value");
    $attr_map_id_particle_effects = attrmap_filter_defindex_and_node($backpack,$ids,"134","float_value");
    
    @$item_map_cannot_trade = itemmap_filter_defindex_and_node($backpack,"id",$ids,"flag_cannot_trade");
    @$item_map_cannot_craft = itemmap_filter_defindex_and_node($backpack,"id",$ids,"flag_cannot_craft");   
    $id_map_defindex = map_tf2_allitem_node($backpack,"id",$ids,"defindex"); 
    //$id_map_defindex[$key=id] => value (defindex)
        echo '<div class="control_wrapper clear">';
            echo '<div class="control">';    //filter
                echo '<div class="text">';
                    echo "filter:";
                    echo '<form>
                        <div class="form-checkbox"><input type="checkbox" class="filter" name="normal" value="normal" checked /><span style="padding-right:2px;" id="normal">normal</span></div>
                        <div class="form-checkbox"><input type="checkbox" class="filter" name="unique" value="unique" checked /><span style="padding-right:2px;" id="unique">unique </span></div>
                        <div class="form-checkbox"><input type="checkbox" class="filter" name="unusual" value="unusual"checked /><span style="padding-right:2px;" id="unusual">unusual</span></div>
                        <div class="form-checkbox"><input type="checkbox" class="filter" name="valve" value="valve" checked /><span style="padding-right:2px;" id="valve">valve </span></div>
                        <div class="form-checkbox"><input type="checkbox" class="filter" name="vintage" value="vintage" checked  /><span style="padding-right:2px;" id="vintage">vintage</span></div>
                        <div class="form-checkbox"><input type="checkbox" class="filter" name="strange" value="strange" checked /><span style="padding-right:2px;" id="strange">strange</span></div>
                        <div class="form-checkbox"><input type="checkbox" class="filter" name="self-made" value="self-made" checked  /><span style="padding-right:2px;" id="self-made">self-made</span></div>
                        <div class="form-checkbox"><input type="checkbox" class="filter" name="community" value="community" checked  /><span style="padding-right:2px;" id="community">community</span></div>
                        <div class="form-checkbox"><input type="checkbox" class="filter" name="genuine" value="genuine" checked  /><span style="padding-right:2px;" id="genuine">genuine</span></div>
                    </form>';
                echo '</div>';
                echo '<div class="sortdrop">';
                    echo '<center>Sort by:<BR \><select class="dropform" name="sort_options">';
                        echo '<option id="normal" value=""></option>';                    
                        echo '<option id="normal" value="sort_by_backpack">in-game</option>';                    
                        echo '<option id="normal" value="sort_by_name">item name</option>';
                        echo '<option id="normal" value="sort_by_quality">item quality</option>';
                    echo '</select></center>';                
                echo '</div>';
            echo '</div>';
        echo '</div>';
    
    echo '<div class="tf2_backpack_all">';
        if ($online==false) echo '<span id="error">WARNING : Steam Community may be down right now. I\'m currently using an older snapshot of your backpack.</span>';
        echo '<div class="backpack clear">';
               echo '<div class="backpack_partition">';        

    include_once('scripts/dbconfig.php');    
    $mysqli2 = new mysqli($host,$username,$password,$db) or die($mysqli2->error);
	$mysqli2->set_charset('utf8');
    $itemcount=0;
    
    $tracked_stranges = "SELECT * FROM `items` WHERE `steamid`='$steamid'";
    $iid_tracked = array();
    
    if ($result=mysqli_query($mysqli2,$tracked_stranges))
    {		
        while ($row = mysqli_fetch_assoc($result))
        {
            $iid_tracked[$row['itemid']] = 1;
        }
    }
    
    $track_all = "SELECT * from user WHERE `steamid`='$steamid'";
    $re = mysqli_query($mysqli2,$track_all);
    $track_all = mysqli_fetch_assoc($re);
    
    $track_all_option = $track_all['track_all'];
    $track_privacy = $track_all['track_privacy'];
    
    //if set to track all, add all the weapons now
    //if set to remove all, remove all the weapons from the list now
    $ids = get_tf2_allitem_node($backpack,"id"); //ids of all items
    
    if ($track_all_option==1){ //if we want to track them all, add them all

        foreach ($item_map_quality as $id => $quality){
            if ($quality=='11'){
                $query = "SELECT * FROM `items` WHERE `itemid`=$id";
                $result = $mysqli2->query($query);
                if ($result->num_rows=='0'){
                    $i = "INSERT INTO `items` (`steamid`,`itemid`) VALUES ('$steamid','$id')";
                    $mysqli2->query($i) or die ($mysqli2->error);
                    $update = "UPDATE item_table SET `tracked`='1' WHERE item_id='$itemid' AND steam_id='$steamid'";
                    $mysqli2->query($update) or die ($mysqli2->error);
                }
            }
        }
    }
    
    foreach ($id_map_defindex as $key => $value) //key are ids, $values are defindexes
    {
        //$itemmap[$itemindex]
        $itemquality = $item_map_quality[$key];
        $quality = tf2_get_quality($itemquality);
        if (isset($attr_map_id_painted[$key]) && $attr_map_id_painted[$key]!='') 
		{
			$painted = sprintf('%06X',$attr_map_id_painted[$key]);
			$painted_name = tf2_get_hex_to_paint_name("#$painted");
		}
		else $painted = null;
		if (isset($attr_map_id_particle_effects[$key]) && $attr_map_id_particle_effects[$key]!='')
		{
			$particle_effect = intval($attr_map_id_particle_effects[$key]);
			$particle_effect = attrmap_get_particle_attribute($schema, $particle_effect);
		}
        else $particle_effect = null;
		if (isset($item_map_custom_name[$key]) && $item_map_custom_name[$key]!=null) $custom_name = $item_map_custom_name[$key];
		else $custom_name=null;
		if (isset($item_map_custom_desc[$key]) && $item_map_custom_desc[$key]!=null) $custom_desc = $item_map_custom_desc[$key];
		else $custom_desc=null;
		
		$previous_id = $item_previous_id[$key];
		$defindex = $item_map_defindex[$key];
		$name = $item_map_name[$value];
		if ($itemquality=='11') $strange_item_rank = tf2_get_strange_kill_rank($attr_map_id_strange_kills[$key]);
        
        $desc = strtoupper($item_map_name[$value]);
        $desc = str_replace('THE ','',$desc);
		$desc = str_replace('UPGRADEABLE TF_WEAPON_','',$desc);
        $desc = str_replace('PRIMARY','',$desc);
		$desc = str_replace('_','',$desc);
		
        $pos = $item_map_inventory_pos[$key] & 0x0000FFFF;
		
		$check = "SELECT count(*) FROM item_table WHERE item_id=$key";
		$result = $mysqli2->query($check);
		$count = $result->fetch_assoc();
		if ($count['count(*)'] == 0)
		{
			//insert into db
			if ($painted!=null || $particle_effect!=null) $has_attribute = 1;
			else $has_attribute=0;
			$insert = "INSERT INTO item_table (item_id,previous_id,quality,has_attributes,attribute1,attribute2,attribute3,item_defindex,item_name,item_custom_name,item_custom_desc,steam_id) VALUES ('$key','$previous_id','$quality','$has_attribute','$painted','$particle_effect','','$defindex','$desc','$custom_name','$custom_desc','$steamid')";
			$mysqli2->query($insert);
		}
		
        echo "<div class='item' inventory_position=\"{$pos}\" item=\"{$desc}\" id='$quality'>";
        if ($tutorial!="true") echo "<a href='?userid={$steamid}&item={$key}'>";
        else echo "<a href='?p=tutorial_item&item={$key}'>";
        echo "<img width='75' height'75' src='$item_map_image_url[$value]' \>";
        echo "</a>";
		if ($pos == 0) echo '<div id="new_item">NEW ITEM!</div>';
		if ($painted != null) echo "<div id='new_item' style='color:#$painted;'>PAINTED</div>";
        if (isset($iid_tracked[$key])) echo "<img id='tracked' width='12' height='12' src='lib/check.png'>";
        else if ($quality=="strange") echo "<img id='tracked' width='12' height='12' src='lib/cross.png'>";
            echo "<div class='tooltip'>";
                echo "<div class='tooltext' id='$quality'>";
                    if ($itemquality!='11') echo "<span id='item_quality'>{$quality}</span><BR \>";	
                    else echo "{$strange_item_rank}<BR \>";				
                    if ($custom_name != null) echo "<span id='custom_name'>\"$custom_name\"</span><BR \>";
                    else echo "<span id='item_desc'>$desc</span><BR \>";	                  
                    if ($custom_desc != null) echo "<span id='custom_desc'>\"$custom_desc\"</span><BR \>";
                    if (isset($strange_kills) && $strange_kills != null) echo "<span id='strange_kills'>Kills : $strange_kills</span><BR \>";
					echo "<span style='font-size : 12px'>id : $key</span><BR \>";
					if ($painted!=null) echo "<span style='font-size : 11px;'>painted : $painted_name</span><BR \>";
					if ($particle_effect!=null) echo "<span style='font-size : 11px;'>effect: $particle_effect</span><BR \>";
					if ($item_map_cannot_trade[$key]!=null) echo "<span style='font-size:10px;color:#ff4d4d;'>(untradeable)</span><BR \>";
					if ($item_map_cannot_craft[$key]!=null) echo "<span style='font-size:10px;color:#ff4d4d;'>(uncraftable)</span><BR \>";
                    if (isset($iid_tracked[$key])) echo "<span style='font-size:10px;color:#7CFC00'>Tracking : Yes</span>";
                    else if ($quality=="strange") echo "<span style='font-size:10px;color:#ff4d4d;'>Tracking : No</span>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
        
        $itemcount++;
        if ($itemcount == 50)
        {
            echo "</div>";
            echo "<hr class='floatclear' \>";
            echo "<div class='backpack_partition'>";        
            $itemcount=0;
        }
    }
	$mysqli2->close();
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

function render_item_desc($steamid,$itemid, $single_quality,$item_image_url,$single_defindex, $single_item_strange_kills, $single_item_name, $single_item_custom_name,$single_item_custom_desc,$single_item_previous_id,$single_item_strange_kills,$tutorial)        
{
        echo '<div class="item_wrapper clear">';
        echo '<div class="item_desc_all clear">';
        if ($tutorial!=true)echo "<a class='back_button' style='text-decoration:underline;' href='?userid={$steamid}'>&lt;&lt;BACK TO BACKPACK</a><br \>";
        else echo "<a class='back_button' style='text-decoration:underline;' href='?p=tutorial'>&lt;&lt;BACK TO BACKPACK</a><br \>";
        
            echo '<div class="item_page_img">';
                echo "<img style='margin-left:10px;' height='175' width='175' class='item' id='{$single_quality}' src='{$item_image_url[$single_defindex]}' \>";
            echo '</div>';
            echo '<div class="item_page_desc">';
                if ($single_quality=='strange')
                {
                    $rank = tf2_get_strange_kill_rank($single_item_strange_kills);
                     echo  "<span id='$single_quality'>$rank </span><BR \>";
                }
                else echo  "<span id='$single_quality'>$single_quality </span><BR \>";
                if ($single_item_custom_name!=null) echo "<span style='font-size:25px;' id='{$single_quality}'>\"$single_item_custom_name\"</span><BR \>";
                else echo "<span  id='{$single_quality}'>$single_item_name </span><BR \>";
                if ($single_item_custom_desc!=null) echo "<span style='font-size:20px; color : #FFD700;'>\"$single_item_custom_desc\"</span><BR \>";
                echo "<span style='font-size:20px;' id='{$single_quality}'>ID : $itemid</span><BR \>";
                if ($single_item_previous_id!=null) echo "<span style='font-size:15px;' id='{$single_quality}'>Previous ID : $single_item_previous_id</span><BR \>";
                if ($single_item_strange_kills!=null) echo "<span id='item_desc_strange_kills'>Kills : $single_item_strange_kills</span><BR \>";
            echo '</div>';
            echo '</div>';
        echo '</div>';
        echo '<HR class="item_page" \>';   
}

function render_plain_header()
{
	include_once ("steamsignin.php");
  	echo "<div class='no_login_header'>";
		echo '<div class="no_login_nav" onclick="location.href=\'/\';">';
			echo "<span>TF2 STRANGE TRACKER</span>";
		echo '</div>';
        echo "<div class='nav_control'>";
            echo '<a id="nav_home" href="/" ></a>';
            echo '<a id="nav_search" href="/" ></a>';
            echo '<a id="nav_top10" href="/?p=top10" ></a>';
            echo '<a id="nav_faq" href="/?p=help" ></a>';
  	       
    	if (isset($_SESSION['steamID']) && ($_SESSION['steamID']!=null)) {
            echo '<a id="nav_usercp_loggedin" href="/?p=usercp"></a>';
            echo "<a id='nav_profile_loggedin' href='/?userid=$_SESSION[steamID]'></a>";
        }
      	else{
           $genurl = SteamSignIn::genUrl();
           echo "<a id='nav_usercp' href='$genurl'></a>";
           echo "<a id='nav_profile' href='$genurl'></a>";
	}
        echo "</div>";
    echo '</div>';
}

function render_footer()
{
    include_once ("steamsignin.php");
    include_once ("functions.php");
    echo "<div class='footer'>";

    if (isset($_SESSION['display_name'])) echo "<div id='custom_desc'>POWERED BY STEAM - ALL MARKS ARE PROPERTY OF THEIR RESPECTIVE OWNERS - Thanks for Logging in, $_SESSION[display_name]!</div>";
    else if (!isset($_GET['openid_assoc_handle']))
    {
        $genurl = SteamSignIn::genUrl();
        echo "<div id='custom_desc'><a href='http://www.steampowered.com' >POWERED BY STEAM - </a><a href='$genurl'><img src='/lib/sits_small.png' \></a></div>";
    }
    else
    {
        $loginStatus = SteamSignIn::validate();
        $s_profile = get_steam_profile_xml($loginStatus);
        if ($s_profile->steamID64 !=null)
        {
            $sd_name = simplexml_load_string($s_profile->steamID->asXML(),null,LIBXML_NOCDATA);
            $_SESSION['display_name'] = (string) $sd_name;
            $_SESSION['steamID'] = (string) $s_profile->steamID64;
            $_SESSION['last_activity'] = time();
           echo "<script>window.location = '?id=$_SESSION[steamID]';</script>";
        }
        else echo "<div id='custom_desc' style = 'font-size:15px; height:25px; margin : 5px 5px 5px 5px;'>That response didn't seem quite right. Please verify your login details!</div>";
    }
    echo "</div>";
}

function render_info_panel($customURL,$steamid,$user_status,$mostplayedgame,$mostplayedhours)
{
    echo '<div class="summary">';
        echo '<div class="text">';
            echo "profile summary";
            echo "<HR \>";
			echo "name : $customURL<BR \> ";
			echo "steamid : $steamid<BR \>";
			if ($user_status == 'in-game') echo "currently : {$state}<BR \>";
			echo "most played game : $mostplayedgame, $mostplayedhours hours";
        echo '</div>';
    echo '</div>';
}

function render_ads()
{
    	echo '<div class="ads"><script type="text/javascript"><!--
            google_ad_client = "ca-pub-9354358608748913";
            /* Home Blog */
            google_ad_slot = "3921198496";
            google_ad_width = 160;
            google_ad_height = 600;
            //-->
            </script>
            <script type="text/javascript"
            src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script></div>';
}

?>
