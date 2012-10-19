<?php


$xml = simplexml_load_file("http://api.steampowered.com/IEconItems_440/GetSchema/v0001/?key=E952EF69C4972394EF63800AC3F40C07&steamid=76561197985023948&format=xml&language=en");

if ($xml!=null)
{
        file_put_contents('/home/geogaddi/webapps/htdocs/lib/schema.xml',$xml->asXML());
        $time = date('d/m/y H:i:s');
        file_put_contents ('/home/geogaddi/webapps/htdocs/lib/lock',"TF2 schema successfully updated at $time. \n", FILE_APPEND);
}
else
{
    	$time = date('d/m/y H:i:s');
        file_put_contents ('/home/geogaddi/webapps/htdocs/lib/lock',"TF2 schema failed to update at $time. \n", FILE_APPEND);
}
?>


