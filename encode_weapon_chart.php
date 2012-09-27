<?php

    include_once('scripts/dbconfig.php');
    
    $mysqli2 = mysqli_connect($host,$username,$password,$db);
    if(mysqli_connect_errno()) echo mysqli_connect_error();
        
    $weapons = "SELECT * FROM `items_top_tracked` ORDER BY `value` DESC LIMIT 100";
    $weapons_q = mysqli_real_escape_string($mysqli2,$weapons);
    
    $top_data = array();

    if ($weapons_re=mysqli_query($mysqli2,$weapons_q))
    {		
        while ($weapons_row = mysqli_fetch_assoc($weapons_re))
        {
            $top_data[] = $weapons_row;
        }
    }
    mysqli_free_result($weapons_re);
    
    $names = array();
    $weps = array();
    foreach ($top_data as $weapon)
    {
        $itemid = $weapon['itemid'];
        $steamid = $weapon['steamid'];
        $kills = $weapon['value'];

        $def_q = "SELECT * FROM item_table WHERE item_id=$itemid";
        $def_q = mysqli_real_escape_string($mysqli2,$def_q);
        $def_re = mysqli_query($mysqli2,$def_q);
        $def = mysqli_fetch_assoc($def_re);
        
        $name=$def['item_name'];
        $defindex = $def['item_defindex'];
        if ($name!=NULL) 
        {
            $names[$itemid] = array("name"=>$name,"kills"=>$kills,"defindex"=>$defindex);
            $weps[$name] = $defindex;
        }
    }

    $sum = array();
    foreach ($names as $itemid => $arr)
    {
        foreach ($weps as $name => $defindex)
        {
            if ($arr['defindex']==$defindex && isset($arr['defindex'])) 
            {
                @$sum[$arr['name']] += $arr['kills'];
            }
        }
    }
        
    $json_data = array();
    foreach ($sum as $defindex => $total_kills){
        $json_data[] = array('label' => "$defindex KILLS", 'data' => $total_kills);
    }
    
    echo json_encode($json_data);
    
    mysqli_free_result($def_re);
    mysqli_close($mysqli2);
?>
