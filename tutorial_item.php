<?php
    include ("functions.php");

    $steamid = "1337";
    $tutorial = "true";

    $profile = simplexml_load_file("{$_SERVER['DOCUMENT_ROOT']}/profiles/1337_profile.xml");
    $backpack = simplexml_load_file("{$_SERVER['DOCUMENT_ROOT']}/backpacks/1337_backpack.xml");
    $schema = simplexml_load_file("{$_SERVER['DOCUMENT_ROOT']}/lib/schema.xml");

    $status= $backpack->status;
    $name = simplexml_load_string($profile->steamID->asXML(),null,LIBXML_NOCDATA);
    $display_name = strtoupper($name);
    $user_status = strtolower(simplexml_load_string($profile->onlineState->asXML(),null,LIBXML_NOCDATA));
    $avatar_full = simplexml_load_string($profile->avatarMedium->asXML(), null, LIBXML_NOCDATA);

    render_profile_header($steamid,$avatar_full,$user_status,$display_name);        

    if (isset($_GET['item']) && $_GET['item'] != '' && $_GET['item'] != null){
        $itemid = $_GET['item'];

        $item = array();
        $items[] = $itemid;
        $item_defindex = itemmap_filter_defindex_and_node($backpack,"id",$items,"defindex");
        $single_defindex = $item_defindex[$itemid];

        $defindex = array();
        $defindex[] = $item_defindex[$itemid];
        $item_image_url = itemmap_filter_defindex_and_node($schema,"defindex",$defindex,"image_url_large");

        $item_name = itemmap_filter_defindex_and_node($schema,"defindex",$defindex,"name");
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

        render_item_desc($steamid,$itemid, $single_quality,$item_image_url,$single_defindex, $single_item_strange_kills,$single_item_name, $single_item_custom_name,$single_item_custom_desc,$single_item_previous_id,$single_item_strange_kills,true);   

         echo '<div class="graph_sidebar_wrapper clear">';
             echo '<div class="stat_all">';
             if ($single_quality=='strange')
             {
                 echo '<div class="title">PERFORMANCE<BR \>';
                 //if (isset($_SESSION['steamID']) && $_GET['userid']==$_SESSION['steamID']) echo "<span id='not_public'>Note : This data is visible to only you.</span>";
                 echo '</div>';
                 echo '<div class="graph_all clear">';

                 echo "<span id='no_data'>No data available for this item. <a class='contentLink' style='text-decoration:underline;' href='#'>Start tracking?</a></span>";
                 //show no data link first, then use jquery to display the item added message
                 echo "<span id='no_data' style='display:none;'>Item added. I'll be checking this item every hour and you'll soon able to see more in depth data.</span>";
            
                 //show graphs
                 echo "<div style='display:none;' class='graph_daily_wrapper'>";
                     echo "<span id='graph_24hrs'><h1 style='text-align:center; color:#86b5d9;'>in the last 24 hours</h1></span>";
                     echo "<div class='graph_daily'><img id='loading' src='lib/spin.gif' />";
                     echo "</div>";
                 echo "</div>";
                 echo "<div style='display:none;' class='graph_weekly_wrapper'>";
                    echo "<span id='graph_weekly'><h1 style='text-align:center; color:#d986b5;'>in the last while</h1></span>";
                    echo "<div class='graph_weekly'><img id='loading' src='lib/spin.gif' />";
                    echo "</div>";
                 echo "</div>";

                    echo '</div>';
                echo '</div>';

                echo '<div class="sidebar">';
                    echo "<div id='admin_title'>OPTIONS</div>"; 
                        echo "<ul id='admin_list'>";
                            echo "<li style='display:none';>STOP TRACKING ITEM</li>"; 
                            echo "<li class='contentLink' style='text-decoration:underline;'>EXPORT AS CSV</li>";     
                      echo "</ul>";
             }
            echo '</div>';
        echo '</div>';
    }
    render_footer();  

?>
