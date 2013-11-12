<?php
/**********************************************************
* Colin Tremblay
* Grinnell College '14
*
* This file creates a json file from an HTML table
***********************************************************/


ini_set('display_errors', 'On');
ini_set('memory_limit', '-1');
include_once "TableColumn.php";
include_once "TableRow.php";

class HTML_Table_to_JSON {

	public function tableToJSON($url, $useFirstColumnAsRowName) {
		// Get html using curl
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($c);
		if (curl_error($c))
			die(curl_error($c));

		// Check return status
		$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		if (200 <= $status && 300 > $status)
			echo 'Got the html.<br />';
		else
			echo 'Failed to get html.<br />';
		curl_close($c);

		// Pull table out of HTML
		$start_pos = stripos($html, '<table id');
		$end_pos = stripos($html, '</table>');
		$length = $end_pos - $start_pos;
		$table = substr($html, $start_pos, $length);
		$permanent_table = $table;

		// Get number of columns and create that many TableColumn objects
		$start_pos = strrpos($table, '<td class="column-');
		$end_pos = strpos($table, '">', $start_pos);
		$start_pos += strlen('<td class="column-');
		$temp_length = $end_pos - $start_pos;
		$number_of_columns = intval(substr($table, $start_pos, $temp_length));

		$column_array = array();
		$row_array = array();
		$header_start = stripos($table, '<thead>');
		if (false !== $header_start) {
			$header_end = stripos($table, '</thead>');
			$header_end += strlen('</thead>');
			$header_length = $header_end - $header_start;
			$header = substr($table, $header_start, $header_length);
			$column_tag = '<th class="column-';

			$header_start = stripos($header, $column_tag);
			while (false !== $header_start) {
				$header_start = stripos($header, '">', $header_start) + strlen('">');
				$header_end = stripos($header, '</th>', $header_start);
				if ($header_end != $header_start) {
					$header_length = $header_end - $header_start;
					$cell_name = substr($header, $header_start, $header_length);
					$cell_name = str_replace('"', '\"', $cell_name);
					$cell_name = str_replace('<br />', '', $cell_name);
					$cell_name = trim($cell_name);
					array_push($column_array, new TableColumn($cell_name));
				}
				else {
					$header_start = stripos($header, $column_tag) + strlen($column_tag);
					$header_end = stripos($header, '">', $header_start);
					$header_length = $header_end - $header_start;
					$column_number  = substr($header, $header_start, $header_length);
					array_push($column_array, new TableColumn('Column'.$column_number));
				}
				// Cut out 
				$header_end = stripos($header, '</tr>');
				$header_end += strlen('</tr>');
				$header_start = stripos($header, '</th>');
				$header_start += strlen('</th>');
				$header_length = $header_end - $header_start;
				$header = substr($header, $header_start, $header_length);

				// set up next pass through loop
				$header_start = stripos($header, $column_tag);
			}
		
			// Trim out the header row 
			$start_pos = stripos($table, '<tr class="row-2');
		}
		else 
			$start_pos = stripos($table, '<tr class="row-1');
		
		$table = substr($table, $start_pos, $length - $start_pos);

		// Loop through the rows
		$start_pos = stripos($table, '<tr class="row-');
		while (false !== $start_pos) {
			// Create temp string with JUST the row we're currently looking at
			$end_pos = stripos($table, '</tr>', 1);
			$end_pos += strlen('</tr>');
			$length = $end_pos - $start_pos;
			$temp = substr($table, $start_pos, $length);
			$table_row_object = new TableRow();
			if ($useFirstColumnAsRowName){
				// Get Header from column 1 and trim out column 1
				$inner_pos_start = stripos($temp, 'class="column-1');
				$inner_pos_start = stripos($temp, '">', $inner_pos_start) + strlen('">');
				$inner_pos_end = stripos($temp, '</td>');
				$inner_len = $inner_pos_end - $inner_pos_start;
				$row_header = substr($temp, $inner_pos_start, $inner_len);
				$row_header = str_replace('<br />', '', $row_header);
				$row_header = trim($row_header);
				
				$inner_pos_start = $inner_pos_end + strlen('</td>');
				$inner_pos_end = stripos($temp, '</tr>') + strlen('</tr>');
				$inner_len = $inner_pos_end - $inner_pos_start;
				$temp = substr($temp, $inner_pos_start, $inner_len);
			}
			else {
				$inner_pos_start = strlen('<tr class="row-');
				$inner_pos_end = stripos($temp, ' odd');
				if (false === $inner_pos_end)
					$inner_pos_end = stripos($temp, ' even');
				$inner_len = $inner_pos_end - $inner_pos_start;
				$row_header = 'Row '.substr($temp, $inner_pos_start, $inner_len);		
			}

			// Loop through the columns
			$column_tag = 'class="column-';
			$inner_pos_start = stripos($temp, '<td ');
			while (false !== $inner_pos_start) {
				$inner_pos_end = stripos($temp, '</td>', $inner_pos_start) + strlen('</td>');
				$inner_len = $inner_pos_end - $inner_pos_start;
				$cell = substr($temp, $inner_pos_start, $inner_len);

				$inner_pos_end = stripos($cell, '</td>');
				$inner_pos_start = stripos($cell, '">') + strlen('">');
				if ($inner_pos_end != $inner_pos_start) {
					$inner_len = $inner_pos_end - $inner_pos_start;
					$cell_name = substr($cell, $inner_pos_start, $inner_len);
					$cell_name = str_replace('"', '\"', $cell_name);
					$cell_name = str_replace('<br />', '', $cell_name);
					$cell_name = trim($cell_name);

					$other_pos = stripos($cell, ' rowspan-');
					if (false === $other_pos)
						$spans_one = true;
					else $spans_one = false;

					$inner_pos_start = stripos($cell, $column_tag) + strlen($column_tag);
					if($spans_one)
						$inner_pos_end = stripos($cell, '">', $inner_pos_start);
					else $inner_pos_end = $other_pos;
					$inner_len = $inner_pos_end - $inner_pos_start;
					$column_number = substr($cell, $inner_pos_start, $inner_len);
					// Subtract 1 to zero index
					$array_index = intval($column_number) - 1;


					if(!$spans_one) {
						$inner_pos_start = stripos($cell, ' rowspan-') + strlen(' rowspan-');
						$inner_pos_end = stripos($cell, '">');
						$inner_len = $inner_pos_end - $inner_pos_start;
						$span = substr($cell, $inner_pos_start, $inner_len);
						$span_number = intval($span);
					}
					else $span_number = 1;
					$column_array[$array_index]->addCell($cell_name, $row_header, $span_number);
				
					if(!$useFirstColumnAsRowName){
						$column_header = $column_array[$array_index]->getName();
						$table_row_object->addAttributePair($column_header, $cell_name);
					}
				}

				// Cut out 
				$inner_pos_end = stripos($temp, '</td>') + strlen('</td>');
				$inner_pos_start = stripos($temp, '</tr>') + strlen('</tr>');
				$inner_len = $inner_pos_start - $inner_pos_end;
				$temp = substr($temp, $inner_pos_end, $inner_len);

				// set up next pass through loop
				$inner_pos_start = stripos($temp, '<td ');
			}
			if (!$useFirstColumnAsRowName) {
				array_push($row_array, $table_row_object);
			}
			$start_pos = stripos($table, '</tbody');
			$start_pos += strlen('</tbody');
			$length = $start_pos - $end_pos;
			$table = substr($table, $end_pos, $length);
			$start_pos = stripos($table, '<tr class="row-');
		}

		$outfile = 'output.json';
		if (false == ($out_handle = fopen($outfile, 'w')))
			echo 'Failed to create output file.';

		if($useFirstColumnAsRowName) {
			$output = "{";
			foreach($column_array as &$col) {
				if ($col->hasCells())
					$output = $output.$col->writeJSON().",\n";
			}
			$output = trim($output, ",\n");
			$output = $output."\n}";
		}
		else {
			$output = "[";
			foreach($row_array as &$row) {
				$output = $output.$row->writeJSON().",\n";
			}
			$output = trim($output, ",\n");
			$output = $output."\n]";
		}
		fwrite($out_handle, $output);
		fclose($out_handle);

		echo 'JSON Created';
	}
}
?>
