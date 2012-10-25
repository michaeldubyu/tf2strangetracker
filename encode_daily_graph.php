<?php
    include_once('scripts/dbconfig.php');
    $mysqli2 = mysqli_connect($host,$username,$password,$db);
    if(mysqli_connect_errno()) echo mysqli_connect_error();
    
    $current_time = time();
    $time = date('Ym');
    $table = "events_$time"; //select from current month's db
                     
    if (isset($_GET['itemid']) && $_GET['itemid']!=null) 
    {
        $itemid = $_GET['itemid'];
        $query2 = "SELECT * FROM $table WHERE `itemid`=$itemid AND `time` >= UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 1 DAY)) LIMIT 24";
        $query2 = mysqli_real_escape_string($mysqli2,$query2);                

        $data_daily = array();
        if ($result2=mysqli_query($mysqli2,$query2))
        {		
            while ($row2 = mysqli_fetch_object($result2))
            {
                $event_time = $row2->time; //key
                $event_value =  $row2->value; //value
                $data_daily[$event_time] = $event_value;
            }
        }
        
        $json_daily = array();

        foreach ($data_daily as $key => $value)
        {
            if ($value!='0') $json_daily[] = array($key * 1000, $value);
        } 
        
        $mergedData[] = array('label' => "24 Hour Overview", 'data' => $json_daily, 'color' => '#FFAA42');
        echo json_encode($mergedData);
        mysqli_free_result($result2);
    }
?>
