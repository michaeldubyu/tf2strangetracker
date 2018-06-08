<?php

require_once("../../webapps/htdocs/thread.php");
include ("../../webapps/htdocs/functions.php");

function update_kills($lower){
    if(mysqli_connect_errno()) echo mysqli_connect_error();

    $time = date('Ym');
    $table = "events_$time"; //select from current month's db

    $query = "SELECT * FROM `items` ORDER BY `id` ASC LIMIT $lower,1000";
    $query = mysqli_real_escape_string($mysqli,$query);

    $exec_time = time();
    $start = date('d/m/y H:i:s');

    $incomplete = 0;
    if ($result = mysqli_query($mysqli, $query))
    {
	file_put_contents("../../webapps/htdocs/lib/lock","Update began at $start, starting at $lower. \n",FILE_APPEND);

        while ($row = mysqli_fetch_object($result))
        {
            if (time()-$exec_time < 3500) //if script has been running older than 7000 seconds, give up
            {
                $steamid = $row->steamid;
                $itemid = $row->itemid;
            
                $backpack = get_tf2_backpack_xml($steamid);
                if ($backpack!=null)
                {
                    $kills = get_single_attr($backpack,$itemid,"214","value");
                    $time = time();
                    $insert = "INSERT into `$table` (`steamid`,`value`,`itemid`,`time`) VALUES ('$steamid','$kills','$itemid','$time')";
                    $mysqli->query($insert) or die ($mysqli->error);
                }
            }
            else 
            {
                $incomplete = 1;
                break(2);
            }
        }
    }
    mysqli_close($mysqli);
    mysqli_free_result($result);
    $time = date('d/m/y H:i:s');
    file_put_contents("../../webapps/htdocs/lib/lock","Update incomplete = $incomplete at $time, starting at $lower. \n",FILE_APPEND);
}

if(!Thread::available()){
    die('Threads not supported');
}

//update schema
//$steamid = get_steam_id_64("geogaddithecat");
//$schema = get_tf2_schema_xml($steamid);
//save_xml($schema,"schema.xml");

$mysqli_c = mysqli_connect('localhost','geogaddi_tf2db','h1myn4meISroot','geogaddi_tf2db');
if(mysqli_connect_errno()) echo mysqli_connect_error();

$query = "SELECT * from items";
$count_r = mysqli_query($mysqli_c,$query);
$num_files = mysqli_num_rows($count_r);

$k=0;
$thread_count=0;
$threads = array();

while ($k<$num_files)
{
    $t = new Thread('update_kills');
    $t->start($k);
    $threads[] = $t;

    if ($k+800 > $num_files) $k += $num_files%800;
    else $k+=800;    
    
    $thread_count++;
}

while( !empty( $threads ) ) {
    foreach( $threads as $index => $thread ) {
        if( ! $thread->isAlive() ) {
            unset( $threads[$index] );
        }
    }
    // let the CPU do its work
    sleep( 1 );
}

file_put_contents("../../webapps/htdocs/lib/lock","\n",FILE_APPEND);

?>
