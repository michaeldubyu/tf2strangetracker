<?php
    include ("functions.php");

    $steamid = "1337";
    $tutorial = "true";

    $profile = simplexml_load_file("{$_SERVER['DOCUMENT_ROOT']}/profiles/1337_profile.xml");
    $backpack = simplexml_load_file("{$_SERVER['DOCUMENT_ROOT']}/backpacks/1337_backpack.xml");
    $schema = simplexml_load_file("{$_SERVER['DOCUMENT_ROOT']}/lib/schema.xml");

    $status= $backpack->status;
    $name = simplexml_load_string($profile->steamID->asXML(),null,LIBXML_NOCDATA);
    $display_name = strtoupper($name);
    $user_status = strtolower(simplexml_load_string($profile->onlineState->asXML(),null,LIBXML_NOCDATA));
    $avatar_full = simplexml_load_string($profile->avatarMedium->asXML(), null, LIBXML_NOCDATA);

    render_profile_header($steamid,$avatar_full,$user_status,$display_name);        
    render_backpack($backpack,$schema,$steamid,true,$tutorial);    
    render_footer();        
?>
