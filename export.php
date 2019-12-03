function addRowToCsv(& $csvString, $cols) {
    $csvString .= implode(',', $cols) . PHP_EOL; //Edits must be 6 characters - all I added was a "." before the =. :-)
}

$csvString = '';
$first = true;

while ($row = mysqli_fetch_assoc($query)) {
    if ($first === true) {
        $first = false;
        addRowToCsv($csvString, array_keys($row));
    }
    addRowToCsv($csvString, $row);
}

header('Content-type: text/csv');
header('Content-disposition: attachment;filename=AdventurePlanner.csv');

echo $csvString;
