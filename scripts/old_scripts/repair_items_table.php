<?php
//look through items, find items with steamid 0, grep backpacks, assign proper steamid
//check through events and see if there are items that exist there but not items table
//SELECT steamid,itemid FROM events_201208 WHERE itemid NOT IN ( SELECT itemid FROM items ) 
//after itemids have been corrected

function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}

	/*
	if(mysqli_connect_errno()) echo mysqli_connect_error();
	$time = date('Ym');
	$table = "events_$time"; //select from current month's db

	$query_t = "SELECT steamid,itemid FROM $table WHERE steamid=0";
	$query_t = mysqli_real_escape_string($mysqli_t,$query_t);
	
	$itemids = array();
	
	if ($result_steamid_itemid=mysqli_query($mysqli_t,$query_t))
	{		
		while ($row_steamid_itemid = mysqli_fetch_assoc($result_steamid_itemid))
		{
            //var_dump($row_steamid_itemid);
			$itemids[] = $row_steamid_itemid['itemid'];
		}
	}
    mysqli_free_result($result_steamid_itemid);
    
    $items = array();
    foreach ($itemids as $key => $itemid)
    {
        $command = "grep \"$itemid\" ../backpacks/*";
        exec($command,$output);
        $steamid = get_string_between($output[0],"../backpacks/","_");
        //construct new array of $items with "steamid"=>value, 
        $items[] = array("steamid"=>$steamid,"itemid"=>$itemid);
    }
    //var_dump($items);
    
    foreach ($items as $item_steamid)
    {
        $steamid = $item_steamid['steamid'];
        $itemid = $item_steamid['itemid'];
        $query_i = "UPDATE $table SET steamid='$steamid' WHERE itemid='$itemid'";
        mysqli_query($mysqli_t,$query_i);
    }
    
    //now check for items that have entries but not in items
    $query_f = "SELECT steamid,itemid FROM events_201208 WHERE itemid NOT IN ( SELECT itemid FROM items )";
	$ids = array();
    if ($result=mysqli_query($mysqli_t,$query_f))
	{		
		while ($row_result = mysqli_fetch_assoc($result))
		{
            //$ids[] = array("steamid"=>$row_result['steamid'], "itemid"=>$row_result['itemid']);
            $steamid = $row_result['steamid'];
            $itemid = $row_result['itemid'];
            mysqli_query($mysqli_t,"INSERT INTO `items` (steamid,itemid) VALUES ('$steamid','$itemid')"); 
		}
	}
    
	mysqli_close($mysqli_t);*/

?>
