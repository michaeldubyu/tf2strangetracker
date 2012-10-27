<?php 

    include_once('scripts/dbconfig.php');
    $mysqli2 = mysqli_connect($host,$username,$password,$db);
    
    if(mysqli_connect_errno()) echo mysqli_connect_error();
    
    $time = date('Ym');
    $prevmonth = date('Ym', strtotime("-1 month"));
    $prevmonth = "events_$prevmonth";
    $table = "events_$time"; //select from current month's db
    
    if (isset($_GET['itemid']) && $_GET['itemid']!=null) 
    {
        $itemid = $_GET['itemid'];
        $weeklyQuery = "SELECT value,time FROM $table WHERE `time` >= UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 30 DAY))  AND `itemid` = $itemid";
        $weeklyQuery = mysqli_real_escape_string($mysqli2,$weeklyQuery);                
        $data_weekly = array();
        
        if ($result_weekly=mysqli_query($mysqli2,$weeklyQuery))
        {		
            while ($row_weekly = mysqli_fetch_object($result_weekly))
            {
                $event_time_weekly = $row_weekly->time; //key
                $event_value_weekly =  $row_weekly->value; //value
                $data_weekly[$event_time_weekly]= $event_value_weekly;
            }
        }	
        
        $json_weekly = array();

        foreach ($data_weekly as $key => $value)
        {
            if ($value!='0') $json_weekly[] = array($key * 1000, $value);
        } 
        
        $mergedWeeklyData[] = array('label' => "7 DAY OUTLOOK", 'data' => $json_weekly, 'color' => '#FFAA42');
        echo json_encode($mergedWeeklyData);
        mysqli_free_result($result_weekly);
    }
?>
