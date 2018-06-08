<?php
//update item_table with all stranges that are being tracked

$query = "SELECT * FROM `items`";

$tracked = array();

if ($result=mysqli_query($mysqli,$query))
{		
    while ($row = mysqli_fetch_assoc($result))
    {
        $tracked[$row['steamid']] = $row['itemid'];
    }
}	
var_dump($tracked);
foreach ($tracked as $steam => $item)
{
    $query2="UPDATE item_table SET `tracked`='1' WHERE item_id='$item' AND steam_id='$steam'";
    mysqli_query($mysqli,$query2) or die (mysqli_error());
}

mysqli_close($mysqli);
?>
