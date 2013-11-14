<?php

include_once 'HTMLTable2JSON.php';
$helper = new HTMLTable2JSON();
$helper->tableToJSON('http://kdic.grinnell.edu/programs/schedule/');
//$helper->tableToJSON('http://kdic.grinnell.edu/programs/schedule/', TRUE, 'wp-table-reloaded-id-6-no-1', array(0 => 5));
//$helper->tableToJSON('http://lightswitch05.github.io/table-to-json/', FALSE);
?>
