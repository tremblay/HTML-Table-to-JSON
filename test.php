<?php

include_once 'HTML_Table_to_JSON.php';
$helper = new HTML_Table_to_JSON();
$helper->tableToJSON('http://kdic.grinnell.edu/programs/schedule/', TRUE);
//http://lightswitch05.github.io/table-to-json/
?>