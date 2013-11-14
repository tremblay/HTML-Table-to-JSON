<?php

include_once 'HTMLTable2JSON.php';
$helper = new HTMLTable2JSON();

// Standard Usage
$helper->tableToJSON('http://kdic.grinnell.edu/programs/schedule/');

// Explicitly filling some fields: These two samples are identical
//$helper->tableToJSON('http://kdic.grinnell.edu/programs/schedule/', TRUE, 'wp-table-reloaded-id-6-no-1', array(0 => 5));
//$helper->tableToJSON('http://kdic.grinnell.edu/programs/schedule/', NULL, NULL, array(0 => 5));

// Treating first row as data
//$helper->tableToJSON('http://lightswitch05.github.io/table-to-json/', FALSE);

// Using $testing
//$table = "<table id=\"test-table\"><thead><tr><th>First Name</th><th>Last Name</th><th>Points</th></tr></thead><tbody><tr><td>Jill</td><td>Smith</td><td>50</td></tr><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr><td>John</td><td>Doe</td><td>80</td></tr></tbody></table>";
//$output = $helper->tableToJSON('', false, null, null, $table);
?>
