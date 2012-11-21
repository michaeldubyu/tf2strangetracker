<?php
	$mysqli_t = mysqli_connect('localhost','geogaddi_tf2db','h1myn4meISroot','geogaddi_tf2db');
	if(mysqli_connect_errno()) echo mysqli_connect_error();
	$time = date('Ym');
	$table = "events_$time"; //select from current month's db

    $query_t = "SELECT * FROM (SELECT itemid,value,steamid FROM $table ORDER BY value DESC) AS tbl1 GROUP BY itemid ORDER BY value DESC LIMIT 0,100";
	$query_t = mysqli_real_escape_string($mysqli_t,$query_t);
	
	$top100_data = array();
	
	if ($result_top100=mysqli_query($mysqli_t,$query_t))
	{		
		while ($row_top100 = mysqli_fetch_assoc($result_top100))
		{
			$top100_data[] = $row_top100;
		}
	}

	foreach ($top100_data as $data) 
	{
		$itemid = $data['itemid'];
		$kills = $data['value'];
		$steamid = $data['steamid'];
        $time = time();
		$insert_t = "INSERT INTO `items_top_tracked` (itemid,value,steamid,last_modified) VALUES ('$itemid','$kills','$steamid','$time') ON DUPLICATE KEY UPDATE `value`='$kills'";
		//inserts only if updated, using itemid as a primary key
		mysqli_query($mysqli_t,$insert_t);
	}
	
?>
