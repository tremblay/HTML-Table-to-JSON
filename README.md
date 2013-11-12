HTML-Table-to-JSON
==================

Author: Colin Tremblay

Date:   Tuesday, 12th November, 2013

### About
Easily scrape and parse a table stored on a web page.

This project is still in ALPHA, meaning it is not fully functional!

### Usage
To get the parser, simply download the 4 php files in 'src.' To use, include HTMLTable2JSON.php in your php file, create a new HTMLTable2JSON object, and call tableToJSON($url, $headerOption)
The header option indicates whether the first column should be used as a header for the entire row. Choosing TRUE results in cells arranged in an array, where each cell has properties of name, column title, row title, and span number. Choosing FALSE treats each cell as a value for the attribute indicated in the column header. With this option, rows are arranged in an array, with column_title : cell_title pairs as attributes.
test.php has an example of this usage.

For support, feedback, suggestions etc. please email tremblay@grinnell.edu

### License

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ 
