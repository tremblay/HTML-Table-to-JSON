<?php

class TableCell {
	private $cell_text;
	private $column_info;
	private $row_info;
	private $row_span;

	function __construct($text, $column, $row, $span){
		$this->cell_text = $text;
		$this->column_info = $column;
		$this->row_info = $row;
		$this->row_span = $span;
	}
	
	public function returnJSON(){
		$ret = "\n\t{\"cell_text\" : \"$this->cell_text\",\n";
		$ret = $ret."\t\"column\" : \"$this->column_info\",\n";
		$ret = $ret."\t\"row\" : \"$this->row_info\",\n";
		$ret = $ret."\t\"spans\" : \"$this->row_span\"}";
		return $ret;
	}
}
?>
