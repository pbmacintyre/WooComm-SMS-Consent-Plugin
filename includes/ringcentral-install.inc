<?php 
/**
 * Copyright (C) 2025 RingCentral
 *
 */
// check WordPress version requirements
if (version_compare(get_bloginfo('version'),'6.8','<')) {
//     deactivate the  plugin if current WordPress version is less than that noted above
    deactivate_plugins(basename(__FILE__));
    exit("Your WordPress Version is too old for this plugin...") ;
}
?>