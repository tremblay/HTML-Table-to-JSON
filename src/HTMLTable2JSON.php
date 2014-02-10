<?php
/*****************************************************************
* HTMLTable2JSON.php
* Updated: Tuesday, Nov. 12, 2013
* 
* Colin Tremblay
* Grinnell College '14
*
* Creates a JSON file from the first HTML table at the given URL
******************************************************************/

ini_set('display_errors', 'On');
ini_set('memory_limit', '-1');
include_once "TableColumn.php";
include_once "TableRow.php";

class HTMLTable2JSON {

	public function tableToJSON($url, $firstColIsRowName = TRUE, $tableID = '', $ignoreCols = NULL, $headers = NULL, $firstRowIsData = FALSE, $onlyColumns = FALSE, $arrangeByRow = FALSE, $ignoreHidden = FALSE, $printJSON = TRUE, $testingTable = NULL) {
		$ignoring = FALSE;
		$excluding = FALSE;
		if (NULL != $onlyColumns){
			if (!is_array($onlyColumns)) {
				echo('onlyColumns must be an array. Did not ignore any columns.<br />');
				$onlyColumns = NULL;
			}
			else  
				for ($i = 0; $i < count($onlyColumns); $i++) {
					if(is_int($onlyColumns[$i])) {
						$excluding = TRUE;
						$ignoreCols = NULL;
						break;
					}
				}
		}
		else if (NULL != $ignoreCols) {
			if (!is_array($ignoreCols)) {
				echo('ignoreCols must be an array. Did not ignore any columns.<br />');
				$ignoreCols = NULL;
			}
			else  
				for ($i = 0; $i < count($ignoreCols); $i++) {
					if(is_int($ignoreCols[$i])) {
						$ignoring = TRUE;
						break;
					}
				}
		}

		if (NULL != $headers)
			if (!is_array($headers)) {
				echo('headers must be an array. Will not change any headers.<br />');
				$headers = NULL;
			}

		if (NULL == $testingTable) {
			// Get html using curl
			$c = curl_init($url);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			$html = curl_exec($c);
			if (curl_error($c))
				die(curl_error($c));

			// Check return status
			$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
			if (200 <= $status && 300 > $status)
				echo 'Got the html from '.$url.'<br />';
			else
				die('Failed to get html from '.$url.'<br />');
			curl_close($c);
		}
		else $html = $testingTable;

		// Remove newlines, returns, and breaks
		$html = str_replace(array("\n", "\r", "\t"), '', $html);
		$html = str_ireplace("\0D", '', $html);
		$html = str_ireplace("<br>", '', $html);
		$html = str_ireplace("<br />", '', $html);
		$html = str_ireplace("<br/>", '', $html);
		
		// Pull table out of HTML
		if (strcmp('', $tableID))
			$table_str = '<table';
		else $table_str = '<table id="'.$tableID;
		$start_pos = stripos($html, $table_str);
		$end_pos = stripos($html, '</table>', $start_pos) + strlen('</table>');
		$length = $end_pos - $start_pos;
		$table = substr($html, $start_pos, $length);
		$permanent_table = $table;

		// Set up arrays
		$column_array = array();
		$row_array = array();
		if (!$firstRowIsData) {
			if (FALSE !== stripos($table, '</th>')){
				$cell_tag = '<th';
				$end_tag = '</th>';
			}
			else{
				$cell_tag = '<td';
				$end_tag = '</td>';
			}

			$header_start = stripos($table, $cell_tag);
			if (false !== $header_start) {
				$header_end = stripos($table, '<thead>');
				if (false !== $header_end)
					$header_start = stripos($table, $cell_tag, $header_end + 1);

				$header_end = stripos($table, '</tr>');
				$header_end += strlen('</tr>');
				$header_length = $header_end - $header_start;
				$header = substr($table, $header_start, $header_length);

				for ($i = 0; false !== $header_start; $i++) {
					$header_start = stripos($header, '>') + strlen('>');
					$header_end = stripos($header, $end_tag, $header_start);
					if ($header_end != $header_start) {
						$header_length = $header_end - $header_start;
						$cell_name = substr($header, $header_start, $header_length);
						$cell_name = str_replace('"', '\"', $cell_name);
						$cell_name = trim($cell_name);
						array_push($column_array, new TableColumn($cell_name));
					}
					else 
						array_push($column_array, new TableColumn('Column'.$i));
		
					// Cut out 
					$header_end = stripos($header, '</tr>');
					$header_end += strlen('</tr>');
					$header_start = stripos($header, $end_tag);
					$header_start += strlen($end_tag);
					$header_length = $header_end - $header_start;
					$header = substr($header, $header_start, $header_length);

					// set up next pass through loop
					$header_start = stripos($header, $cell_tag);
				}
			
				// Trim out the header row 
				$start_pos = stripos($table, $end_tag) + strlen($end_tag);
			}
			else 
				$start_pos = stripos($table, '<tr');
		}
		else $start_pos = stripos($table, '<tr');

		$table = substr($table, $start_pos, $length - $start_pos);

		if (NULL != $headers)
			foreach ($headers as $key => $value) {
				for ($m = 0; $key > count($column_array); $m++)
					array_push($column_array, new TableColumn('Column'.$m));
				if (count($column_array) == $key)
					array_push($column_array, new TableColumn($value));
				else
					$column_array[$key]->setName($value);
			}
		// Set up array for skipping columns that don't show up in HTML
			// due to rowspan > 1 in the previous row
		$skipped_columns = array();

		// Loop through the rows
		$start_pos = stripos($table, '<tr');
		for ($j = 0; false !== $start_pos; $j++) {
			// Create temp string with JUST the row we're currently looking at
			$end_pos = stripos($table, '</tr>', $start_pos);
			$end_pos += strlen('</tr>');
			$length = $end_pos - $start_pos;
			$temp = substr($table, $start_pos, $length);
			if (!$ignoreHidden || (FALSE === stripos($temp, "style=\"display: none;\""))){
				// If this row doens't have a skipped array, add one
				if (count($skipped_columns) <= $j + 1) {
					$row_with_skipped_columns = array();
					array_push($skipped_columns, $row_with_skipped_columns);
				}
				if ($firstRowIsData && FALSE !== stripos($temp, '<th')){
					$cell_tag = '<th';
					$end_tag = '</th>';
				}
				else {
					$cell_tag = '<td';
					$end_tag = '</td>';
				}
				if ($firstColIsRowName){
					// Get Header from column 1 and trim out column 1
					$inner_pos_start = stripos($temp, $cell_tag);
					$inner_pos_start = stripos($temp, '>', $inner_pos_start) + strlen('>');
					$inner_pos_end = stripos($temp, $end_tag);
					$inner_len = $inner_pos_end - $inner_pos_start;
					$row_header = substr($temp, $inner_pos_start, $inner_len);
					$row_header = trim($row_header);
					
					$inner_pos_start = $inner_pos_end + strlen($end_tag);
					$inner_pos_end = stripos($temp, '</tr>') + strlen('</tr>');
					$inner_len = $inner_pos_end - $inner_pos_start;
					$temp = substr($temp, $inner_pos_start, $inner_len);
					$table_row_object = new TableRow($row_header);
					$i = 1;
				}
				else {
					$row_header = 'Row '.$j;
					$table_row_object = new TableRow();
					$i = 0;
				}

				// Loop through the columns
				$inner_pos_start = stripos($temp, $cell_tag);
				for ($i; false !== $inner_pos_start; $i++) {
					// Skip over columns in the array created by rowspans
					while (in_array($i, $skipped_columns[$j]))
						$i++;

					// Skip over user specified columns
					if ((NULL == $ignoreCols || !in_array($i, $ignoreCols)) && (NULL == $onlyColumns || in_array($i, $onlyColumns))){
						$inner_pos_start += strlen($cell_tag);
						$inner_pos_end = stripos($temp, $end_tag, $inner_pos_start) + strlen($end_tag);
						$inner_len = $inner_pos_end - $inner_pos_start;
						$cell = substr($temp, $inner_pos_start, $inner_len);

						$inner_pos_end = stripos($cell, $end_tag);
						$inner_pos_start = stripos($cell, '>') + strlen('>');
						if ($inner_pos_end != $inner_pos_start) {
							$inner_len = $inner_pos_end - $inner_pos_start;
							$cell_name = substr($cell, $inner_pos_start, $inner_len);
							$cell_name = str_replace('"', '\"', $cell_name);
							$cell_name = str_replace('&nbsp;', '', $cell_name);
							$cell_name = trim($cell_name);
							$link_start = stripos($cell_name, '<a href=\"');
							if (false === $link_start)
								$link = null;
							else {
								$link_start +=  + strlen('<a href=\"');
								$link_end = stripos($cell_name, '\">', $link_start);
								$link_len = $link_end - $link_start;
								$link = substr($cell_name, $link_start, $link_len);
								$link = trim($link);
								$link_start = stripos($cell_name, '</a>');
								$link_end += strlen('\">');
								$link_len = $link_start - $link_end;
								$cell_name = substr($cell_name, $link_end, $link_len);
							}
							// Remove Font tag
							$link_start = stripos($cell_name, '<font');
							if (false !== $link_start) {
								$link_start = stripos($cell_name, '\">', $link_start) + strlen('\">');
								$link_end = stripos($cell_name, '</font>', $link_start);
								$link_len = $link_end - $link_start;
								$cell_name = substr($cell_name, $link_start, $link_len);
							}
							$other_pos = stripos($cell, ' rowspan-');
							if (false === $other_pos)
								$spans_one = true;
							else $spans_one = false;

							if(!$spans_one) {
								$inner_pos_start = stripos($cell, ' rowspan-') + strlen(' rowspan-');
								$inner_pos_end = stripos($cell, '">');
								$inner_len = $inner_pos_end - $inner_pos_start;
								$span = substr($cell, $inner_pos_start, $inner_len);
								$span_number = intval($span);
								$span_no = $span_number;
								// Set up skipped columns to skip over columns missed in HTML in future rows due to rowspan > 1
								for ($k = $j + 1; $span_no > 1; $span_no--, $k++) {
									if (count($skipped_columns) <= $k) {
										$another_row_with_skipped_columns = array();
										array_push($skipped_columns, $another_row_with_skipped_columns);
									}
									array_push($skipped_columns[$k], $i);
								}
							}
							else $span_number = 1;
							for ($m = 0; $i > count($column_array); $m++)
								array_push($column_array, new TableColumn('Column'.count($column_array)));
							if (count($column_array) == $i)
								array_push($column_array, new TableColumn('Column '.$i));
							$column_array[$i]->addCell($cell_name, $row_header, $span_number, $link);
						
							if($arrangeByRow){
								$column_header = $column_array[$i]->getName();
								$table_row_object->addAttributePair($column_header, $cell_name);
								if (NULL != $link) {
									$link_header = $column_header.' URL';
									$table_row_object->addAttributePair($link_header, $link);	
								}						
							}
						}
					}
					// Cut out 
					$inner_pos_end = stripos($temp, $end_tag) + strlen($end_tag);
					$inner_pos_start = stripos($temp, '</tr>') + strlen('</tr>');
					$inner_len = $inner_pos_start - $inner_pos_end;
					$temp = substr($temp, $inner_pos_end, $inner_len);

					// set up next pass through loop
					if (FALSE === stripos($temp, '<th')){
						$cell_tag = '<td';
						$end_tag = '</td>';
					}
					$inner_pos_start = stripos($temp, $cell_tag);
				}
				if ($arrangeByRow) {
					array_push($row_array, $table_row_object);
				}
			}
			$start_pos = stripos($table, '</table');
			$start_pos += strlen('</table');
			$length = $start_pos - $end_pos;
			$table = substr($table, $end_pos, $length);
			$start_pos = stripos($table, '<tr');
		}

		if ($printJSON) {
			$outfile = 'output.json';
			if (false == ($out_handle = fopen($outfile, 'w')))
				die('Failed to create output file.');
		}

		// Create the JSON formatted output
		if($arrangeByRow) {
			if ($firstColIsRowName)
				$output = "{";
			else $output = "[";
			foreach($row_array as &$row) {
				if ($row->hasCells())
					$output = $output.$row->writeJSON().",\n";
			}
			$output = trim($output, ",\n");
			if ($firstColIsRowName)
				$output = $output."\n}";
			else $output = $output."\n]";
		}
		else {
			$output = "{";
			foreach($column_array as &$col) {
				if ($col->hasCells())
					$output = $output.$col->writeJSON().",\n";
			}
			$output = trim($output, ",\n");
			$output = $output."\n}";
		}

		if ($printJSON) {
			fwrite($out_handle, $output);
			fclose($out_handle);
			echo 'JSON Created<br />';
		}
		else {
			$output = str_replace("\n", "", $output);
			$output = str_replace("\t", "", $output);
			return $output;
		}
	}
}
?>
