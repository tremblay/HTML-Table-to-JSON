HTML-Table-to-JSON
==================

Author: Colin Tremblay

Date:   Saturday, 16th November, 2013

## About
Easily scrape and parse a table stored on a web page.

This project is still in ALPHA, meaning it is not fully functional!

The current version is .7

The project currently works on most HTML tables. Nested tables and tables containing malicious inputs have not been dealt with.

Features similar to those in the javascript version (https://github.com/lightswitch05/table-to-json) by @lightswitch05 are being added incrementally. 

## Usage
To get the parser, simply download the 4 php files in 'src.' 

To use, include HTMLTable2JSON.php in your php file, create a new HTMLTable2JSON object, and call `tableToJSON($url);`

### Optional Arguments
- `firstColIsRowName`
  - Boolean indicating whether the first column in the table should be parsed as the title for all values in the row.
  - Default: `TRUE`
- `tableID`
  - String to contain the ID of the table. Allows user to specify a particular table. Default behavior simply grabs the first table encountered on the page.
  - Default: `''`
- `ignoreColumns`
  - Array of column indexes to ignore.
  - Format: `array(0 => firstColToIgnore, 1 => secondColToIgnore)` OR `array(firstIndex, secondIndex)`.
  - Default: `NULL`
- `headers`
  - Array of header names.
  - Format: `array(colNum1 => header1, colNum2 => header2)`.
  - Default: `NULL`
- `firstRowIsData`
  - Boolean indicating whether the first row contains data (not headers).
  - Choosing `TRUE` treats the first row as data regardless of `<th>` tags. DO NOT choose this if there are headers in the first row that you want to override.
  - Default: `FALSE`
- `onlyColumns`
  - Array of column indexes to include; all others are ignored.
  - Format: `array(0 => firstColToInclude, 1 => secondColToInclude)` OR `array(firstIndex, secondIndex)`.
  - Default `NULL`
- `arrangeByRows`
  - Choosing `FALSE` treats cells as discrete objects. Cells are arranged in arrays by column, where each cell has properties of name, column title, row title, span number, and URL (if applicable). 
  - Choosing `TRUE` treats each cell as a value for the attribute indicated in the column header. With this option, rows are arranged in an array, with `column_title : cell_title` pairs as attributes.
  - Default: `FALSE`
- `ignoreHidden`
  - Boolean indicating whether rows tagged with `style=\"display: none;` should appear in output.
  - Setting `TRUE` will suppress hidden rows.
  - Default: `FALSE`
- `printJSON`
  - Boolean indicating whether the program should create the JSON or simply return the output to the caller.
  - Setting `FALSE` leaves the output in the hands of the caller. `TRUE` creates a JSON file. 
  - Default: `TRUE` 
- `testingTable`
  - String representing an HTML table. Allows user to manually input a table for conversion, instead of scraping from a webpage.
  - Ignores whatever value is in `url`.
  - Default: `NULL`

Note about php and optional arguments: If you wish to use an argument lower on the list, but not one higher, you must still fill in the higher values. To avoid changing the program, use `NULL` as the argument for any options you do not wish to change. 

sample.php has examples of the correct usage.

### TODO
 - Override cell names/data
 - Handle more html tags within a cell (links have been handled).
 - Do something with the style and/or class of a given cell, row, or column. This information could be useful in some cases and shouldn't be hard to access.
 - Code cleanup/refactor

For support, feedback, suggestions etc. please email tremblay@grinnell.edu

## License

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
