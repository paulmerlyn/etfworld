<?php
/*
 * This script (1) imports the JSON-encoded data, (2) creates the database tables, and (3) uploads the data into the
 * tables. Before running/testing this script, open /ssi/databaseconnector.php and edit the host, username, password,
 * and database constants to the values of your own database/DB user. (For additional visibility into the upload
 * process, you can also scroll down a little inside importandupload.php and uncomment the code block marked "FOR
 * TESTING ..." in order to see var dumps of the imported data before it's uploaded.)
 */

require_once('../ssi/databaseconnector.php');
require_once('../library/functions.php');

/*
Step 1: Import JSON-encoded data. (Note: In a production application scenario, I'd likely use a cron job on a daily
schedule to import the data.)
*/
$filestringmay30 = file_get_contents("../data/may30.json");
$filestringmay31 = file_get_contents("../data/may31.json");
$filestringjun1 = file_get_contents("../data/jun1.json");

$filestringsarray = array($filestringmay30, $filestringmay31, $filestringjun1);

/* I've designated the "descriptors" as static information pertaining to the ETF i.e. elements of Security such as
CIK, Cusip, Symbol, Name, and Market. I've designated the "price data" as dynamic information pertaining to the
ETF i.e. the other elements such as Date, Last, Open, High, Low, Volume, LastClose, etc.  */
$etf_descriptor_values_array = array(); // Will hold descriptor values such as 'spy', 'SPDR S&P 500', etc.
$price_data_values_array = array(); // Will hold price data values such as '6/1/2007', '154.08', etc.

/* Confession: To avoid additional complexity of renaming duplicate key names (i.e. 'Outcome', 'Message', 'Identity',
 and 'Delay'), I will omit them from the $etf_descriptors_keys_array and $price_data_keys_array arrays. However,
I will check that all of the Outcome fields have a value of "Success" before any data is uploaded.  */
$etf_descriptors_keys_array = array('CIK', 'Cusip', 'Symbol', 'ISIN', 'Valoren', 'Name', 'Market', 'CategoryOrIndustry');
$price_data_keys_array = array('Date', 'Last', 'Open', 'High', 'Low', 'Volume', 'LastClose', 'ChangeFromOpen',
    'PercentChangeFromOpen', 'ChangeFromLastClose', 'PercentChangeFromLastClose', 'SplitRatio',
    'CummulativeCashDividend', 'CummulativeStockDividendRatio', 'Currency', 'AdjustmentMethodUsed', 'DataConfidence');

foreach ($filestringsarray as $thestring) {
    // Organize the data into an array using recursive iterator
    $jsonIterator = new RecursiveIteratorIterator(
        new RecursiveArrayIterator(json_decode($thestring, TRUE)),
        RecursiveIteratorIterator::SELF_FIRST);

    foreach ($jsonIterator as $key => $val) {
        if(is_array($val)) {
            foreach ($val as $k => $item) {
                if ($k == 'Date') $item = mysqlDateConverter($item); // Convert date to MySQL-ready format
                if (in_array($k, $price_data_keys_array)) {
                    array_push($price_data_values_array, $item);
                }
                elseif (in_array($k, $etf_descriptors_keys_array)) {
                    if (is_null($item)) $item = ''; // Convert null items to an empty string for database upload
                    array_push($etf_descriptor_values_array, $item);
                    if ($k == 'Symbol') array_push($price_data_values_array, $item); /* Append the value of
 the ticker symbol into the array so it continues to be associated with the $price_data_values_array */
                }
            }
        }
        else {
            if ($key == 'Outcome' AND $val != 'Success') {
                // Report bad data. (Could alternatively report it to a log file or issue an email to an administrator.)
                echo "This file contains bad data. An 'Outcome' field is reported as a failure.";
                exit;
            }
        }
    }
}

/* FOR TESTING...
echo 'Here is the $etf_descriptor_values_array:<br />';
var_dump($etf_descriptor_values_array);
echo '<br />Here is the $price_data_values_array:<br />';
var_dump($price_data_values_array);
*/

/*
Step 2: Create the database tables.
Schema: I'm using two tables.
etf_descriptors_table contains the "static" information pertaining to the ETFs i.e. elements of Security such as each ETF's CIK, Cusip, Symbol, Name, and Market.
etf_prices_table contains the "dynamic" price data pertaining to the ETFs i.e. elements such as Date, Last, Open, High, Low, Volume, LastClose, etc.

Create a MySQL table named: etf_descriptors_table with columns:
* Symbol (unique, NOT NULL, primary key, varchar(8)),
* CIK (char(10)),
* CUSIP (char(9)),
* ISIN (char(12),
* Valoren (varchar(10)),
* Name (varchar(40)),
* Market (varchar(20)),
* CategoryOrIndustry (varchar(30)),

*/

$query = <<<HEREDOC
CREATE  TABLE etf_descriptors_table (
    Symbol varchar(8) NOT NULL primary key,
    CIK char(10),
    CUSIP char(9),
    ISIN char(12),
    Valoren varchar(10),
    Name varchar(40),
    Market varchar(20),
    CategoryOrIndustry varchar(30),
    UNIQUE(CUSIP)
) ENGINE=INNODB;

HEREDOC;

if (!$result = $db -> query($query)) {
    die('etf_descriptors_table was not created successfully. The query was: '.$query.' and the error is: '.$db ->
        error);
}
else {
    echo '<br />etf_descriptors_table was successfully created.';
}

/*
Create a MySQL table named: etf_prices_table with columns:
* ID (bigint, NOT NULL, auto increment, primary key),
* Symbol (unique, NOT NULL, varchar(8), foreign key [NEED TO SET ENGINE = InnoDB),
* Date (date),
* Last (float(9,3)),
* Open (float(9,3)),
* High (float(9,3)),
* Low (float(9,3)),
* Volume (int), unsigned),
* LastClose (float(9,3)),
* ChangeFromOpen (float(9,3)),
* PercentChangeFromOpen (float(7,3)),
* ChangeFromLastClose (float(9,3)),
* PercentChangeFromLastClose (float(7,3)),
* SplitRatio (tinyint)),
* CummulativeCashDividend (float(9,3)),
* CummulativeStockDividendRatio (tinyint),
* Currency (varchar(5)),
* AdjustmentMethodUsed (varchar(20)),
* DataConfidence (varchar(10))
*/

$query = <<<HEREDOC
CREATE  TABLE etf_prices_table (
    ID bigint NOT NULL auto_increment primary key,
    Symbol varchar(8) NOT NULL,
    Date date,
    Last float(9,3),
    Open float(9,3),
    High float(9,3),
    Low float(9,3),
    Volume int unsigned,
    LastClose float(9,3),
    ChangeFromOpen float(9,3),
    PercentChangeFromOpen float(7,3),
    ChangeFromLastClose float(9,3),
    PercentChangeFromLastClose float(7,3),
    SplitRatio tinyint,
    CummulativeCashDividend float(9,3),
    CummulativeStockDividendRatio tinyint,
    Currency varchar(5),
    AdjustmentMethodUsed varchar(20),
    DataConfidence varchar(10),
    FOREIGN KEY (Symbol)
        REFERENCES etf_descriptors_table(Symbol)
        ON DELETE CASCADE
) ENGINE=INNODB;

HEREDOC;

if (!$result = $db -> query($query)) {
    die('etf_prices_table was not created successfully. The query was: '.$query.' and the error is: '.$db ->
        error);
}
else {
    echo '<br />etf_prices_table was successfully created.';
}

/*
Step 3: Upload the data into the tables
*/

/* Step 3A: Upload data into the etf_descriptors_table. Construct the INSERT query iteratively by cycling through the
 $etf_descriptor_values_array. Use IGNORE to ignore duplicates upon insertion. */

/* Determine the number of cycles we need to iterate through a loop when building the INSERT query for the
etf_descriptor_table by dividing the array size by the number of columns in the table (i.e. 8). */
$NofDescriptors = count($etf_descriptors_keys_array);
$cycles = count($etf_descriptor_values_array)/$NofDescriptors;

$queryValuesString = ''; // Initialize

for ($i=0; $i < $cycles; $i++) {
    for ($j=0; $j < $NofDescriptors; $j++) {
        $idx = $i*$NofDescriptors + $j; // Calculate the value of the array index
        if ($j == 0) $queryValuesString = $queryValuesString.'(';

        /* Preserve data formats by identifying strings and enclosing them in quotation marks. */
        if (is_string($etf_descriptor_values_array[$idx])) {
            $queryValuesString .= "'".$etf_descriptor_values_array[$idx]."',";
        }
        else {
            $queryValuesString .= $etf_descriptor_values_array[$idx].",";
        }
        if ($j == ($NofDescriptors - 1)) {
            // Final item, which corresponds to last column for a record in the database table.
            $queryValuesString = rtrim($queryValuesString, ','); // Remove the excess comma
            $queryValuesString = $queryValuesString.'),'; // Append a '),' to close this group of values
        }
    }
}
$queryValuesString = rtrim($queryValuesString, ','); // Remove the excess comma

$etf_descriptor_keys_string = implode(',', $etf_descriptors_keys_array);

$query = 'INSERT IGNORE INTO etf_descriptors_table ('.$etf_descriptor_keys_string.') VALUES '.$queryValuesString;

if (!$result = $db -> query($query)) {
    die('Data was not successfully inserted into etf_descriptors_table. The query was: '.$query.' and
    the error is: '.$db ->
        error);
}
else {
    echo "<br />Data was successfully inserted into etf_descriptors_table.";
}


/* Step 3B: Upload data into the etf_prices_table. Construct the INSERT query iteratively by cycling through the
 $price_data_values_array. Use IGNORE to ignore duplicates upon insertion. */

array_push($price_data_keys_array, 'Symbol'); // Append 'Symbol'.

/* Determine the number of cycles we need to iterate through a loop when building the INSERT query for the
etf_prices_table by dividing the array size by the number of columns in the table (i.e. 8). */
$NofPriceDataKeys = count($price_data_keys_array);
$cycles = count($price_data_values_array)/$NofPriceDataKeys;
$queryValuesString = ''; // Initialize

/* Comment: In the spirit of DRY, I'd make the following code block a function or even a class method if I had more
time. */
for ($i=0; $i < $cycles; $i++) {
    for ($j=0; $j < $NofPriceDataKeys; $j++) {
        $idx = $i*$NofPriceDataKeys + $j; // Calculate the value of the array index
        if ($j == 0) $queryValuesString = $queryValuesString.'(';

        /* Preserve data formats by identifying strings and enclosing them in quotation marks. */
        if (is_string($price_data_values_array[$idx])) {
            $queryValuesString .= "'".$price_data_values_array[$idx]."',";
        }
        else {
            $queryValuesString .= $price_data_values_array[$idx].",";
        }
        if ($j == ($NofPriceDataKeys - 1)) {
            // Final item, which corresponds to last column for a record in the database table.
            $queryValuesString = rtrim($queryValuesString, ','); // Remove the excess comma
            $queryValuesString = $queryValuesString.'),'; // Append a '),' to close this group of values
        }
    }
}
$queryValuesString = rtrim($queryValuesString, ','); // Remove the excess comma

$price_data_keys_string = implode(',', $price_data_keys_array);

$query = 'INSERT IGNORE INTO etf_prices_table ('.$price_data_keys_string.') VALUES '.$queryValuesString;

if (!$result = $db -> query($query)) {
    die('Data was not successfully inserted into etf_prices_table. The query was: '.$query.' and
    the error is: '.$db ->
        error);
}
else {
    echo "<br />Data was successfully inserted into etf_prices_table.";
}
?>