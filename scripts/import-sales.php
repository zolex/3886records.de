<?php

chdir(dirname(dirname(__FILE__)));
$config = (require 'config.php');
require 'vendor/autoload.php';

$doReimport = true;

$dbh = new PDO('mysql:host='. $config['db']['host'] .';port='. $config['db']['port'] .';dbname='. $config['db']['name'] .';charset=utf8', $config['db']['user'], $config['db']['password']);
DataProvider::getInstance()->setDbh($dbh);

$salesReports = array();
$files = glob('/srv/apache2/3886records.de/www/production/shared/sales-reports/*');

foreach ($files as $file) {

	if (preg_match('/Label_Worx_-_#(\d+)_(\d{4})-Q([1234])_-_Sales_Report\.xlsx$/', $file, $matches)) {	

		$report = $matches[1];
		$year = $matches[2];
		$quarter = $matches[3];

		$xlsFile = basename($file);

		if ($salesReport = DataProvider::getInstance()->getSalesReportByQuarter($year .'/'. $quarter)) {
			
			if (!$doReimport) {

				echo "Skippping already imported #$report ($year/$quarter)" . PHP_EOL;
				continue;

			} else {

				echo "Re-Import already imported #$report ($year/$quarter)" . PHP_EOL;

				$stmt = $dbh->prepare("DELETE FROM sales WHERE report_id = :id");
				$stmt->bindValue('id', $salesReport->id);
				if (!$stmt->execute()) {
				
					$errorInfo = $stmt->errorInfo();
					throw new \Exception($errorInfo[2], $errorInfo[1]);
				}
			}

		} else {

			$salesReport = new \Models\SalesReport;
			$salesReport->name = $report;
			$salesReport->quarter = $year .'/'. $quarter;
			$salesReport->filename = $xlsFile;
			DataProvider::getInstance()->insertSalesReport($salesReport);
		}

		chdir(dirname($file));
		$csvFile = preg_replace('/\.xlsx$/', '.csv', $xlsFile);
		echo "converting $xlsFile to $csvFile..." . PHP_EOL;
		`ssconvert $xlsFile csv/$csvFile 2>&1 > /dev/null`;

		$lines = file('csv/'.$csvFile);

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
	}
}


