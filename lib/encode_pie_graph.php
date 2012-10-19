<?php
    include_once('scripts/dbconfig.php');
    
    $mysqli2 = mysqli_connect($host,$username,$password,$db);
    if(mysqli_connect_errno()) echo mysqli_connect_error();
    
    $last_time = time()-86400;
    $current_time = time();
    $time = date('Ym');
    $table = "item_table"; //select from current month's db
                     
    //pick random weapon from items, item_defindex, then look at ownership data
    $rand = "SELECT * from item_table order by RAND() limit 1";
    $re = mysqli_query($mysqli2, $rand);
    $defindex = mysqli_fetch_assoc($re);
    $rand = $defindex['item_defindex'];
    $name = $defindex['item_name'];
    
    $ownership_count = "SELECT * from item_table where item_defindex='$rand'";
    $oc_res = mysqli_query($mysqli2,$ownership_count);
    $appeared = mysqli_num_rows($oc_res);
    
    $totalq = "select * from item_table";
    if ($to_re = mysqli_query($mysqli2,$totalq)) $total = mysqli_num_rows($to_re);
    $total -= $appeared;
    
    $json_data = array();
    
    $json_data[] = array('label' => "Times $name Seen", 'data' => $appeared, 'color'=> "#D9868C");
    $json_data[] = array('label' =>" All Backpack Items Seen", 'data' => $total, 'color' => "#86b5d9");
    //$mergedData[] = array('data'=>$json_data);
    
    echo json_encode($json_data);
    
    mysqli_free_result($re);
    mysqli_close($mysqli2);
?>
