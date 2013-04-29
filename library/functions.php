<?php
/* Library functions */

/* Converts date from M/D/YYYY format into MySQL standard date-type format YYYY-MM-DD */
function mysqlDateConverter($date) {
    $date = explode('/',$date);
    if ($date[0] <= 9 AND strpos($date[0], '0') !== 0) $date[0] = '0'.$date[0]; // prepend a leading zero if necessary
    if ($date[1] <=9  AND strpos($date[1], '0') !== 0) $date[1] = '0'.$date[1];
    $date = array($date[2], $date[0], $date[1]);
    $formattedDate = implode('-', $date);
    return $formattedDate;
}

?>
