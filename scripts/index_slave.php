<?php
/* Form processor slave script for index.php. It receives user submissions (posted via AJAX in index.php) of symbol (e.g. SPY) and date (e.g. 2007-05-31) and retrieves the associated price data from the etf_prices_table. */

require_once('../ssi/databaseconnector.php');

$symbol = $_POST['symbol'];
$date = $_POST['date']; // Formatted by index.php to YYY-MM-DD

// Sanitize foreign data submissions to combat injection attacks
$symbol = htmlspecialchars($symbol);
$date = htmlspecialchars($date);

if (!get_magic_quotes_gpc())
{
    $symbol = addslashes($symbol);
    $date = addslashes($date);
}

// Formulate database query
$query = "SELECT p.Open, p.High, p.Low, p.Last, p.Volume, p.ChangeFromLastClose, p.PercentChangeFromLastClose,
p.Currency, d.Name FROM etf_prices_table AS p, etf_descriptors_table AS d WHERE p.Symbol = '".$symbol."' AND Date =
'".$date."' AND p.Symbol = d.Symbol";

if (!$result = $db -> query($query)) {
    die('Unable to select from etf_prices_table. The query was: '.$query.' and the error is: '.$db -> error);
}

$row = $result -> fetch_row();

echo json_encode($row); // Retrieved by index.php via AJAX
?>