<?php
/*****************************************************************
* TableCell.php
* Updated: Tuesday, Nov. 12, 2013
* 
* Colin Tremblay
* Grinnell College '14
*
* Stores attributes of a cell and provides a JSON printing method
*  Used when the first column headers, not data
******************************************************************/

class TableCell {
	private $cell_text;
	private $column_info;
	private $row_info;
	private $row_span;
	private $url;

	function __construct($text, $column, $row, $span, $url){
		$this->cell_text = $text;
		$this->column_info = $column;
		$this->row_info = $row;
		$this->row_span = $span;
		$this->url = $url;
	}
	
	public function returnJSON(){
		$ret = "\n\t{\"cell_text\" : \"$this->cell_text\",\n";
		if (null != $this->url)
			$ret = $ret."\t\"url\" : \"$this->url\",\n";
		$ret = $ret."\t\"column\" : \"$this->column_info\",\n";
		$ret = $ret."\t\"row\" : \"$this->row_info\",\n";
		$ret = $ret."\t\"spans\" : \"$this->row_span\"}";
		return $ret;
	}
}
?>
