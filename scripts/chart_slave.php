<?php
/*
This script, which is executed (via an AJAX post) when the user clicks the "Chart" button in index.php,
retrieves the data from the etf_prices_table, sending back an associative array of ETF price values for each
available date (i.e. May 30, May 31, and Jun 1, 2007) to be used by the JavaScript High Charts/Stock Chart for
candlestick chart creation.
 */

require_once('../ssi/databaseconnector.php');

$chartsymbol = $_POST['chartsymbol']; // posted as a hidden field

// Sanitize foreign data submissions to combat injection attacks
$chartsymbol = htmlspecialchars($chartsymbol);

if (!get_magic_quotes_gpc())
{
    $chartsymbol = addslashes($chartsymbol);
}

// Formulate database query
$query = "SELECT Date, Open, High, Low, Last as Close, Symbol, Currency FROM etf_prices_table WHERE Symbol = '"
    .$chartsymbol."'";

if (!$result = $db -> query($query)) {
    die('Unable to select OHLC price data from etf_prices_table. The query was: '.$query.' and the error is: '.$db ->
        error);
}

/* Store all retrieved dates and prices in arrays */
$datesArray = array(); // Initialize
$pricesArray = array();
$myDataArray = array();

while ($row = $result -> fetch_assoc()) {
    $myDataArray[] = $row;
}

echo json_encode($myDataArray);





