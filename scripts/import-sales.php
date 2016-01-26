<?php

chdir(dirname(dirname(__FILE__)));
$config = (require 'config.php');
require 'vendor/autoload.php';

$dbh = new PDO('mysql:host='. $config['db']['host'] .';port='. $config['db']['port'] .';dbname='. $config['db']['name'] .';charset=utf8', $config['db']['user'], $config['db']['password']);
DataProvider::getInstance()->setDbh($dbh);

$xlsxFile = $argv[1];
if (!preg_match('/(\d{4})-Q(\d)/', $xlsxFile, $matches)) {	

		echo "Filename must contain quarter info in format 'YYYY-Q[1,2,3,4]'\n";
		exit;
}

$year = $matches[1];
$quarter = $matches[2];

if ($salesReport = DataProvider::getInstance()->getSalesReportByQuarter($year .'/'. $quarter)) {
	
	$stmt = $dbh->prepare("DELETE FROM sales WHERE report_id = :id");
	$stmt->bindValue('id', $salesReport->id);
	if (!$stmt->execute()) {
	
		$errorInfo = $stmt->errorInfo();
		throw new \Exception($errorInfo[2], $errorInfo[1]);
	}

} else {

	$salesReport = new \Models\SalesReport;
	$salesReport->name = '';
	$salesReport->quarter = $year .'/'. $quarter;
	$salesReport->filename = $xlsxFile;
	DataProvider::getInstance()->insertSalesReport($salesReport);
}

chdir(dirname(__FILE__));
$csvFile = preg_replace('/\.xlsx$/', '.csv', $xlsxFile);
`ssconvert $xlsxFile $csvFile 2>&1 > /dev/null`;
$lines = file($csvFile);

$end = count($lines) - 1;
for ($n = 1; $n < $end; $n++) {

	$data = str_getcsv($lines[$n]);
	$sale = new \Models\Sale;
	$sale->report_id = $salesReport->id;
	
	if ($label = DataProvider::getInstance()->getLabelByName($data[0])) {
	
		$sale->label_id = $label->id;
	}
	
	if ($release = DataProvider::getInstance()->getReleaseByCatalog($data[1])) {
	
		$sale->release_id = $release->id;
	}
	
	if ($artist = DataProvider::getInstance()->getArtistByName($data[2])) {
	
		$sale->artist_id = $artist->id;
	}
	
	$sale->release_artist = $data[2];
	$sale->release_name = $data[3];
	$sale->track_artist = $data[4];
	$sale->track_title = $data[5];
	$sale->mix_name = $data[6];
	$sale->format = $data[7];
	$sale->sale_type = $data[8];
	$sale->quantity = $data[9];
	$sale->value = $data[10];
	$sale->deal = $data[11];
	$sale->royalty = $data[12];
	$sale->isrc = $data[13];
	$sale->ean = $data[14];
	$sale->store = $data[15];
	$sale->track_ref = $data[16];

	DataProvider::getInstance()->insertSale($sale);
}

