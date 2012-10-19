<?php
    include_once('scripts/dbconfig.php');
    $mysqli2 = mysqli_connect($host,$username,$password,$db);
    
    if(mysqli_connect_errno()) echo mysqli_connect_error();
    
    $table = "item_table"; 
    $cont = 1;
    //pick random weapon from items, item_defindex, then look at ownership data
    while ($cont==1)
    {
        $rand = "SELECT * from item_table order by RAND() limit 1";
        $re = mysqli_query($mysqli2, $rand);
        $defindex = mysqli_fetch_assoc($re);
        $rand = $defindex['item_defindex'];
        $name = $defindex['item_name'];

        $ownership_count = "SELECT * from item_table where item_defindex='$rand'";
        $oc_res = mysqli_query($mysqli2,$ownership_count);
        $appeared = mysqli_num_rows($oc_res);
        
        $name_mod_count = "SELECT * from item_table where item_defindex='$rand' AND item_custom_name!=''";
        $name_res = mysqli_query($mysqli2,$name_mod_count);
        $mod_count = mysqli_num_rows($name_res);
        
        $desc_mod_count = "SELECT * from item_table where item_defindex='$rand' AND item_custom_desc!=''";
        $desc_res = mysqli_query($mysqli2,$desc_mod_count);
        $desc_count = mysqli_num_rows($desc_res);
        
        if ($mod_count!=0 || $desc_count!=0) $cont=0;
    }
    
    $total = $appeared - $mod_count - $desc_count;
    
    $json_data = array();
    
    $json_data[] = array('label' => "Times $name Seen", 'data' => $total, 'color'=> "#D9868C");
    $json_data[] = array('label' =>" With A Custom Name", 'data' => $mod_count, 'color' => "#86b5d9");
    $json_data[] = array('label' =>" With A Custom Description", 'data' => $desc_count, 'color' => "#d9d486");
    
    echo json_encode($json_data);
    
    mysqli_free_result($re);
    mysqli_close($mysqli2);
?>
