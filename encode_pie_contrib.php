<?php
if (isset ($_GET['item'])){

    include_once('scripts/dbconfig.php');
    $mysqli2 = mysqli_connect($host,$username,$password,$db);
    mysqli_query($mysqli2,"SET NAMES 'utf8'");

    if(mysqli_connect_errno()) echo mysqli_connect_error();
    
    $item = mysqli_real_escape_string($mysqli2, $_GET['item']);
    $defindex_q = "SELECT item_defindex FROM item_table WHERE item_name='$item' LIMIT 1";

    $def_re = mysqli_query($mysqli2,$defindex_q);
    $def = mysqli_fetch_assoc($def_re);
    $defindex = $def['item_defindex'];

    //now get the itemids of weapons with this defindex and check top_tracked table for the top 10 contributions
    $itemid_q = "SELECT `item_id` FROM `item_table` WHERE `item_defindex`='$defindex'";
    $ids = array();
    
    if ($itemid_re = mysqli_query($mysqli2,$itemid_q)){
        while ($itemid_row = mysqli_fetch_assoc($itemid_re)){
            $ids[] = $itemid_row['item_id']; 
        }
    }
    
    //now we have all the ids of the weapons that *could* be contributers,
    //so now we should check through the top 100 list again with these item_ids
    //and save any matches in $ids, otherwise unset them
    $contrib_data = array();
    
    foreach ($ids as $key => $itemid){
        $top_q = "SELECT items_top_tracked.*, item_table.owner_name FROM items_top_tracked, item_table WHERE items_top_tracked.itemid = item_table.item_id AND item_table.item_id ='$itemid'";
        $top_re = mysqli_query($mysqli2,$top_q);
        if (mysqli_num_rows($top_re)==0) unset($ids[$key]);
        else{
            //$itemid verified exists in top_tracked, so we have data for it
            $contrib_data[] = mysqli_fetch_assoc($top_re);
        }       
    }
    if (count($contrib_data)<15)echo json_encode($contrib_data);
    else echo json_encode(array_splice($contrib_data,0,count($contrib_data)-5));
}
?>
