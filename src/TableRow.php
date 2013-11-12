<?php
/*****************************************************************
* TableRow.php
* Updated: Tuesday, Nov. 12, 2013
* 
* Colin Tremblay
* Grinnell College '14
*
* Stores attributes of a row and provides a JSON printing method
*  Used when the first column contains data, not headers
******************************************************************/

include_once "TableRow.php";

class TableRow {
	private $attribute_values; //array
	private $attribute_titles; //array

	function __construct(){
		$this->attribute_values = array();
		$this->attribute_titles = array();
	}

	public function addAttributePair($title, $value){
		array_unshift($this->attribute_titles, $title);
		array_unshift($this->attribute_values, $value);
	}
	
	public function writeJSON(){
		$ret = "\n\t{\n";
		while(null != ($temp = array_pop($this->attribute_values)))
			$ret = $ret."\t\t\"".array_pop($this->attribute_titles)."\":\"".$temp."\",\n";
		$ret = trim($ret, ",\n");
		$ret = $ret."\n\t}";
		return $ret;
	}
}
?>
