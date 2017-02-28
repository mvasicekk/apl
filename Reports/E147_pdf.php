<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "E147";
$doc_subject = "E147 MA Anw Liste";
$doc_keywords = "E147";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];

$datvon = sprintf("%04d-%02d-%02d",$jahr,$monat,1);
$calDays = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$datbis = sprintf("%04d-%02d-%02d",$jahr,$monat,$calDays);

$apl = AplDB::getInstance();


$sql.=" select";
$sql.="     dpers.regeloe,";
$sql.="     dzeit.PersNr as persnr,";
$sql.="     CONCAT(dpers.`Name`,' ',dpers.Vorname) as `name`,";
$sql.="     dzeit.Datum,";
$sql.="     sum(dzeit.Stunden) as sum_stunden";
$sql.=" from";
$sql.="     dzeit";
$sql.=" join dtattypen on dtattypen.tat=dzeit.tat";
$sql.=" join dpers on dpers.PersNr=dzeit.PersNr";
$sql.=" where";
$sql.="     dzeit.PersNr between '$persvon' and '$persbis'";
$sql.="     and";
$sql.="     dtattypen.oestatus='a'";
$sql.="     and";
$sql.="     dzeit.Datum between '$datvon' and '$datbis'";
$sql.=" group by";
$sql.="     dzeit.PersNr,";
$sql.="     dzeit.Datum";
$sql.=" order by `name`";
    
$rows = $apl->getQueryRows($sql);

date_default_timezone_set('Europe/Prague');

/** PHPExcel */
require_once '../Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$user = get_user_pc();
// Set properties
$objPHPExcel->getProperties()->setCreator($user)
							 ->setLastModifiedBy($user)
							 ->setTitle("E147")
							 ->setSubject("E147")
							 ->setDescription("E147")
							 ->setKeywords("office openxml php")
							 ->setCategory("phpexcel");

//echo "$sql<hr>";
//AplDB::varDump($rows);

//exit();
//
// popisky sloupcu
$radek = 1;
$sloupec = 0;

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, "regeloe");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, "persnr");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, "jmeno");
$sloupec++;

for($i=1;$i<=$calDays;$i++){
    $popis = "$i";
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
}
$radek++;
$sloupec = 0;

$lidi = array();
// vytvorit pole s lidma
foreach ($rows as $r){
    $persnr = $r['persnr'];
    $lidi[$persnr]['name'] = $r['name'];
    $lidi[$persnr]['regeloe'] = $r['regeloe'];
    $den = intval(substr($r['Datum'],8));
    $lidi[$persnr][$den] = $r['sum_stunden'];
}

foreach ($lidi as $persnr=>$l){
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $l['regeloe']);
    $sloupec++;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $persnr);
    $sloupec++;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $l['name']);
    $sloupec++;
    //projit hodiny ve dnech
    for($i=1;$i<=$calDays;$i++){
	if(array_key_exists($i, $lidi[$persnr])){
	    $obsah = $lidi[$persnr][$i];
	}
	else{
	    $obsah = '';
	}
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $obsah);
	$sloupec++;
    }
    $radek++;
    $sloupec=0;
}
// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('E147');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="E147.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;


//============================================================+
// END OF FILE                                                 
//============================================================+

?>
