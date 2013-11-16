<?php
/*****************************************************************
* TableColumn.php
* Updated: Tuesday, Nov. 12, 2013
* 
* Colin Tremblay
* Grinnell College '14
*
* Stores attributes of a column and provides JSON printing methods
*  Used when the first column headers, not data
******************************************************************/

include_once "TableCell.php";

class TableColumn {
	private $column_header; //string
	private $cells; //array

	function __construct($header){
		$this->column_header = $header;
		$this->cells = array();
	}

	public function getName(){
		return $this->column_header;
	}

	public function setName($new_header){
		$this->column_header = $new_header;
	}

	public function hasCells(){
		return (count($this->cells) >= 1);
	}

	public function addCell($cell_text, $row_header, $span, $url){
		$new_cell = new TableCell($cell_text, $this->column_header, $row_header, $span, $url);
		array_unshift($this->cells, $new_cell);
	}
	
	public function writeJSON(){
		$ret = "\"".$this->column_header."\" : [";
		while(null != ($temp = array_pop($this->cells)))
			$ret = $ret.$temp->returnJSON().",";
		$ret = trim($ret, ",");
		$ret = $ret."]";
		return $ret;
	}
}
?>
