<?php

include_once 'HTMLTable2JSON.php';
$helper = new HTMLTable2JSON();

$tests = 0;
$passed = 0;
$failed = 0;
$outfile = 'test_output.json';

// Tests:	tables with data in first column (setting firstColIsRowName to false)
$helper->tableToJSON('http://lightswitch05.github.io/table-to-json/', FALSE);
if (false == ($out_handle = fopen($outfile, 'w')))
	die('Failed to create output file.');
$output = "[\t{\n\t\t\"First Name\":\"Jill\", \n\t\t\"Last Name\":\"Smith\", \n\t\t\"Points\":\"50\"\n\t},\n\t{\n\t\t\"First Name\":\"Eve\", \n\t\t\"Last Name\":\"Jackson\", \n\t\t\"Points\":\"94\"\n\t},\n\t{\n\t\t\"First Name\":\"John\", \n\t\t\"Last Name\":\"Doe\", \n\t\t\"Points\":\"80\"\n\t},\n\t{\n\t\t\"First Name\":\"Adam\", \n\t\t\"Last Name\":\"Johnson\", \n\t\t\"Points\":\"67\"\n\t}\n]";
fwrite($out_handle, $output);
fclose($out_handle);

$code_output = md5_file('output.json');
$test_output = md5_file('test_output.json');
if($code_output == $test_output)
	$passed++;
else {
	echo 'test '.$tests.' failed.';
	$failed++;
}
$tests++;


// Tests: 	setting all options
//			tables with some cells with rowspan > 1 
$helper->tableToJSON('http://kdic.grinnell.edu/programs/schedule/', TRUE, 'wp-table-reloaded-id-6-no-1', array(0 => 0, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9));
if (false == ($out_handle = fopen($outfile, 'w')))
	die('Failed to create output file.');
$output = "{\"Monday\" : [\n\t{\"cell_text\" : \"<a href=\\\"http://kdic.grinnell.edu/programs/shows/the-jenn-n-juice-show/\\\">The Jenn N' Juice Show</a>\",\n\t\"column\" : \"Monday\",\n\t\"row\" : \"8:00PM\",\n\t\"spans\" : \"2\"},\n\t{\"cell_text\" : \"<a href=\\\"http://kdic.grinnell.edu/programs/shows/west-coast-dub/ \\\">West Coast Dub</a>\",\n\t\"column\" : \"Monday\",\n\t\"row\" : \"10:00PM\",\n\t\"spans\" : \"1\"},\n\t{\"cell_text\" : \"<a href=\\\"http://kdic.grinnell.edu/programs/shows/good-touches/\\\">Good Touches</a>\",\n\t\"column\" : \"Monday\",\n\t\"row\" : \"11:00PM\",\n\t\"spans\" : \"1\"},\n\t{\"cell_text\" : \"<a href=\\\"http://kdic.grinnell.edu/programs/shows/the-ray-kdic-variety-hour/\\\">The Ray KDIC Variety Hour</a>\",\n\t\"column\" : \"Monday\",\n\t\"row\" : \"12:00AM\",\n\t\"spans\" : \"1\"}]\n}";
fwrite($out_handle, $output);
fclose($out_handle);

$code_output = md5_file('output.json');
$test_output = md5_file('test_output.json');
if($code_output == $test_output)
	$passed++;
else {
	echo '<br />test '.$tests.' failed.';
	$failed++;
}
$tests++;


// Tests: 	testing mode
//			basic usage with thead and tbody
$table = "<table id=\"test-table\"><thead><tr><th>First Name</th><th>Last Name</th><th>Points</th></tr></thead><tbody><tr><td>Jill</td><td>Smith</td><td>50</td></tr><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr><td>John</td><td>Doe</td><td>80</td></tr></tbody></table>";
$helper = new HTMLTable2JSON();
$code_output = $helper->tableToJSON('', false, null, null, null, null, $table);
$test_output = "[{\"First Name\":\"Jill\", \"Last Name\":\"Smith\", \"Points\":\"50\"},{\"First Name\":\"Eve\", \"Last Name\":\"Jackson\", \"Points\":\"94\"},{\"First Name\":\"John\", \"Last Name\":\"Doe\", \"Points\":\"80\"}]";
if($code_output == $test_output)
	$passed++;
else {
	echo '<br />test '.$tests.' failed.';
	$failed++;
}
$tests++;



// Tests: 	basic usage without thead or tbody
$table = "<table id=\"test-table\"><tr><th>First Name</th><th>Last Name</th><th>Points</th></tr><tr><td>Jill</td><td>Smith</td><td>50</td></tr><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr><td>John</td><td>Doe</td><td>80</td></tr></table>";
$helper = new HTMLTable2JSON();
$code_output = $helper->tableToJSON('', false, null, null, null, null, $table);
$test_output = "[{\"First Name\":\"Jill\", \"Last Name\":\"Smith\", \"Points\":\"50\"},{\"First Name\":\"Eve\", \"Last Name\":\"Jackson\", \"Points\":\"94\"},{\"First Name\":\"John\", \"Last Name\":\"Doe\", \"Points\":\"80\"}]";

if($code_output == $test_output)
	$passed++;
else {
	echo '<br />test '.$tests.' failed.';
	$failed++;
}
$tests++;



// NOT IMPLEMENTED:
	//				override column names
	//				override cell values
	

// Tests: 	ignore column 0
$table = "<table id=\"test-table\"><thead><tr><th>First Name</th><th>Last Name</th><th>Points</th></tr></thead><tbody><tr><td>Jill</td><td>Smith</td><td>50</td></tr><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr><td>John</td><td>Doe</td><td>80</td></tr></tbody></table>";
$helper = new HTMLTable2JSON();
$code_output = $helper->tableToJSON('', false, null, array(0 => 0), null, null, $table);
$test_output = "[{\"Last Name\":\"Smith\", \"Points\":\"50\"},{\"Last Name\":\"Jackson\", \"Points\":\"94\"},{\"Last Name\":\"Doe\", \"Points\":\"80\"}]";
if($code_output == $test_output)
	$passed++;
else {
	echo '<br />test '.$tests.' failed.';
	$failed++;
}
$tests++;


// NOT IMPLEMENTED:
	//				ignore hidden row

// Tests:	include hidden row
$table = "<table id=\"test-table\"><thead><tr><th>First Name</th><th>Last Name</th><th>Points</th></tr></thead><tbody><tr><td>Jill</td><td>Smith</td><td>50</td></tr><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr style=\"display: none;\"><td>John</td><td>Doe</td><td>80</td></tr></tbody></table>";
$helper = new HTMLTable2JSON();
$code_output = $helper->tableToJSON(' ', false, null, array(0 => 0), null, null, $table);
$test_output = "[{\"Last Name\":\"Smith\", \"Points\":\"50\"},{\"Last Name\":\"Jackson\", \"Points\":\"94\"},{\"Last Name\":\"Doe\", \"Points\":\"80\"}]";
if($code_output == $test_output)
	$passed++;
else {
	echo '<br />test '.$tests.' failed.';
	$failed++;
}
$tests++;


// NOT IMPLEMENTED:
	//				treat first row as column headers regardless of tags
	//				onlyColumns option
	//				interaction btwn onlyColumns and ignoresColumns -- onlyColumns shoudl trump



// Tests:	Take column headers as input: headers in first row are overriden by supplied headers
// Tests:	include hidden row
$table = "<table id=\"test-table\"><tr><th>First Name</th><th>Last Name</th><th>Points</th></tr><tr><td>Jill</td><td>Smith</td><td>50</td></tr><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr><td>John</td><td>Doe</td><td>80</td></tr></table>";
$helper = new HTMLTable2JSON();
$code_output = $helper->tableToJSON(' ', false, null, null, array(2 => 'Score'), null, $table);
$test_output = "[{\"First Name\":\"Jill\", \"Last Name\":\"Smith\", \"Score\":\"50\"},{\"First Name\":\"Eve\", \"Last Name\":\"Jackson\", \"Score\":\"94\"},{\"First Name\":\"John\", \"Last Name\":\"Doe\", \"Score\":\"80\"}]";
if($code_output == $test_output)
	$passed++;
else {
	echo '<br />test '.$tests.' failed.';
	$failed++;
}
$tests++;

// Tests:	test when headers are not provided by the table, but ARE provided as an argument
//			Note: You MUST set firstRowIsData to TRUE if there is not a header row in the table.
$table = "<table id=\"test-table\"><tr><td>Jill</td><td>Smith</td><td>50</td></tr><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr><td>John</td><td>Doe</td><td>80</td></tr></table>";
$helper = new HTMLTable2JSON();
$code_output = $helper->tableToJSON(' ', false, null, null, array(0 => 'First Name', 1 => 'Last Name', 2 => 'Score'), TRUE, $table);
$test_output = "[{\"First Name\":\"Jill\", \"Last Name\":\"Smith\", \"Score\":\"50\"},{\"First Name\":\"Eve\", \"Last Name\":\"Jackson\", \"Score\":\"94\"},{\"First Name\":\"John\", \"Last Name\":\"Doe\", \"Score\":\"80\"}]";
if($code_output == $test_output)
	$passed++;
else {
	echo '<br />test '.$tests.' failed.';
	$failed++;
}
$tests++;

// Tests:	test when headers are not provided by the table, and are not provided as an argument
$table = "<table id=\"test-table\"><tr><td>Jill</td><td>Smith</td><td>50</td></tr><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr><td>John</td><td>Doe</td><td>80</td></tr></table>";
$code_output = $helper->tableToJSON(' ', false, null, null, null, TRUE, $table);
$table = "<table id=\"test-table\"><tr><th>Jill</th><th>Smith</th><th>50</th></tr><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr><td>John</td><td>Doe</td><td>80</td></tr></table>";
$another_output = $helper->tableToJSON(' ', false, null, null, null, TRUE, $table);
$table = "<table id=\"test-table\"><thead><tr><th>Jill</th><th>Smith</th><th>50</th></tr></thead><tbody><tr><td>Eve</td><td>Jackson</td><td>94</td></tr><tr><td>John</td><td>Doe</td><td>80</td></tr></tbody></table>";
$third_output = $helper->tableToJSON(' ', false, null, null, null, TRUE, $table);
$test_output = "[{\"Column 0\":\"Jill\", \"Column 1\":\"Smith\", \"Column 2\":\"50\"},{\"Column 0\":\"Eve\", \"Column 1\":\"Jackson\", \"Column 2\":\"94\"},{\"Column 0\":\"John\", \"Column 1\":\"Doe\", \"Column 2\":\"80\"}]";
if($code_output == $test_output && $test_output == $another_output && $test_output == $third_output)
	$passed++;
else {
	echo '<br />test '.$tests.' failed.';
	$failed++;
}
$tests++;
		//											test interaction with ignoresColumns
		//											test interaction with onlyColumns
	// 				nested tables



if ($passed == $tests)
	echo '<br />Ran '.$tests.' tests. All passed!';
else
	echo '<br />Ran '.$tests.' tests. '.$passed.' passed! '.$failed.' failed!';
?>
