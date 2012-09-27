<?php
include ("{$_SERVER['DOCUMENT_ROOT']}/whats/functions.php");

//for each user in the db : 
////get friend's list
////for each friend :
//////get their backpack
//////hash their current backpack, if different vs existing or doesn't exist,
////////add to db

$mysqli = mysqli_connect('localhost','root','h1myn4meISroot','tf2db');
if(mysqli_connect_errno()) echo mysqli_connect_error();

$query = "SELECT steamid FROM `items` GROUP BY steamid";
$query = mysqli_real_escape_string($mysqli,$query);

$db_list = array();

if ($result = mysqli_query($mysqli, $query))
{
    while ($row = mysqli_fetch_object($result))
    {
        $db_list[] = $row->steamid;
    }
}

$all_friends = array();
foreach ($db_list as $user)
{
    $f_list_xml = get_steam_friends_xml($user);
    if ($f_list_xml !=null)
    {
        foreach ($f_list_xml->friends->friend as $friend)
        {
            //get in list
            $all_friends[] = (string)$friend->steamid;
        }
    }
}

//only unique in the friends list sense, they be tracked in the db
$unique = array_unique($all_friends);

save_new_user_xml($unique,$mysqli);
$mysqli->close();

function save_new_user_xml($unique,$mysqli)
{
    foreach ($unique as $f)
    {
        $profile = save_xml(get_steam_profile_xml($f),"/whats/profiles/{$f}_profile.xml");
        $backpack = save_xml(get_tf2_backpack_xml($f),"/whats/backpacks/{$f}_backpack.xml");
        //get profile, save
        add_new_stranges($profile,$backpack,$mysqli);
    }
}

function add_new_stranges($profile,$backpack,$mysqli)
{
    $steamid = $profile->steamID64;
    $ids = get_tf2_allitem_node($backpack,"id"); //ids of all items
    $item_map_quality = itemmap_filter_defindex_and_node($backpack,"id",$ids,"quality");		

    
    foreach ($item_map_quality as $key => $value)
    {
        if ($value!='11') unset($item_map_quality[$key]);
    }
    
    foreach ($item_map_quality as $id => $quality)
    {
        $query = "SELECT * FROM `items` WHERE `itemid`=$id";
        $result = $mysqli->query($query);
        
        if ($result->num_rows=='0') 
        {
            $i = "INSERT INTO `items` (`steamid`,`itemid`) VALUES ('$steamid','$id')";
            $mysqli->query($i) or die ($mysqli->error);
        }
    }
}


?>
