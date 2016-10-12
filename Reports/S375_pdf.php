<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S375";
$doc_subject = "S375 Report";
$doc_keywords = "S375";

// necham si vygenerovat XML
$parameters=$_GET;

// vytahnu paramety z _GET ( z getparameters.php )

$von=make_DB_datum($_GET['von']);
$bis=make_DB_datum($_GET['bis']);
$kdvon = $_GET['kdvon'];
$kdbis = $_GET['kdbis'];


//vytvorit pole mesicu podle zadanych datumu
$jahrMonatArray = array();
$start = strtotime($von);
$end = strtotime($bis);
$step = 24*60*60;
for($t=$start;$t<=$end;$t+=$step){
    $jm = date('Y-m',$t);
    $jahrMonatArray[$jm] += 1;
}

//exit();
$dnyvTydnu = array("Po","Ut","St","Ct","Pa","So","Ne");

$a = AplDB::getInstance();

// komentare k XML souboru do budoucna smazat
// nechci zobrazit parametry
// vynuluju promennou $params
$params="";

// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2


function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
    {
        //$pdfobjekt->AddPage();
        return TRUE;
    }
    return FALSE;
}

function test_pageoverflow($pdfobjekt, $vysradku, $cellhead)
{
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
        pageHeader($pdfobjekt, $cellhead, $vysradku);
        foreach($detailArray as $jmk=>$kundeRow){
            $pdf->SetFillColor(192, 192, 192);
            $pdf->Cell(4*$stkWidth,$rowHeight,$jmk,'LRBT',0,'L',1);

            $pdf->Ln();
            foreach ($kundeRow as $kd=>$detailRow){
                $pdf->SetFillColor(255, 255, 230);
                $pdf->Cell(4*$stkWidth,$rowHeight,$kd,'LRBT',0,'L',1);
                $pdf->Ln();
            }
            $pdf->Ln();
        }
        //$pdfobjekt->Ln();
        //$pdfobjekt->Ln();
    }
}

/**
 *
 * @param TCPDF $pdf
 * @param type $datumWidth
 * @param type $headerHeight
 * @param type $kundeNrArray
 */
function pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis) {
    $pdf->SetFillColor(255, 255, 230);
    $pdf->SetFont("FreeSans", "B", $s);
    $pdf->Cell($mntWidth, $rowHeight, '', 'LRT', 0, 'R', 1);
    $pdf->Cell($bewWidth, $rowHeight, '', 'LRT', 0, 'R', 1);
    $pdf->Cell(2 * $stkWidth, $rowHeight, "Sum $kdvon-$kdbis", 'LRT', 0, 'C', 1);

    foreach ($kundenNrArray as $kd => $v) {
        $pdf->Cell(2 * $stkWidth, $rowHeight, "$kd", 'LRT', 0, 'C', 1);
    }
    $pdf->Ln();

    $pdf->Cell($mntWidth, $rowHeight, 'Mnt.', 'LRB', 0, 'R', 1);
    $pdf->Cell($bewWidth, $rowHeight, 'Bew.', 'LRB', 0, 'R', 1);
    $pdf->Cell($stkWidth, $rowHeight, "E", 'LRTB', 0, 'C', 1);
    $pdf->Cell($stkWidth, $rowHeight, "I", 'LRTB', 0, 'C', 1);

    foreach ($kundenNrArray as $kd => $v) {
        $pdf->Cell($stkWidth, $rowHeight, "E", 'LRTB', 0, 'C', 1);
        $pdf->Cell($stkWidth, $rowHeight, "I", 'LRTB', 0, 'C', 1);
    }
    $pdf->Ln();
}





$kundeVon = $kdvon;
$kundeBis = $kdbis;
$datVon = $von;
$datBis = $bis;
$kundenNrArray = array();

$jahrMonatKwArray = array();
$bewertungArray = array();
$summeKunden = array();
$monatSummen = array();
$monatSummenKunden = array();
$gesamtSummen = array();
$gesamtSummenKunden = array();


// data po zakaznicich,rocich,mesicich
$sql="";
$sql.=" select ";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(Datum) as jahr,";
$sql.="     MONTH(Datum) as monat,";
$sql.="     drueck.oe,";
$sql.="     sum(if(auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*`VZ-SOLL`,(drueck.`Stück`)*`VZ-SOLL`)) as sum_vzkd,";
$sql.="     sum(if(TaetNr between 6400 and 6499,ABS(drueck.`Stück`)*`VZ-IST`,0)) as sum_vzaby_64XX,";
$sql.="     sum(if(TaetNr between 6500 and 6599,ABS(drueck.`Stück`)*`VZ-IST`,0)) as sum_vzaby_65XX";
$sql.=" from drueck";
$sql.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
$sql.=" where";
$sql.="     drueck.datum between '$von' and '$bis'";
$sql.="     and";
$sql.="     daufkopf.kunde between '$kdvon' and '$kdbis'";
$sql.=" group by";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(Datum),";
$sql.="     MONTH(Datum),";
$sql.="     oe;";


$repArray = $a->getQueryRows($sql);
//AplDB::varDump($repArray);

if($repArray!==NULL){
    foreach ($repArray as $rep){
	$kunde = $rep['kunde'];
	$oeSchicht = $rep['oe'];
	
	// az budou naplnene oe v dtattypen, vezmu oe podle teto tabulky
	// na zkousku
	$gr = substr($oeSchicht,  strlen($oeSchicht)-2); // posledni 2 znaky
	$cislo = intval(($gr)); // udelam z toho cislo
	if($cislo==0){
	    $oe = "S";	// S jako Sonstiges
	}
	else{
	    $oe = "*".$cislo;
	}
	
	//$oe = $oeSchicht;	    
	
	$vzaby64xx = $rep['sum_vzaby_64XX'];
	$vzaby65xx = $rep['sum_vzaby_65XX'];
	$vzkd = $rep['sum_vzkd'];
	$jm = sprintf("%04d-%02d",$rep['jahr'],$rep['monat']);	
	
	$kundenArray[$kunde] += 1;
	$oeArray[$oe] += 1;
	$kdOEMonatArray[$kunde][$oe][$jm]['vzaby_64xx'] += floatval($vzaby64xx);
	$kdOEMonatArray[$kunde][$oe][$jm]['vzaby_65xx'] += floatval($vzaby65xx);
	$kdOEMonatArray[$kunde][$oe][$jm]['vzkd'] += floatval($vzkd);
	$kdOEMonatArray[$kunde][$oe][$jm]['podil_64xx'] = $kdOEMonatArray[$kunde][$oe][$jm]['vzkd']!=0?$kdOEMonatArray[$kunde][$oe][$jm]['vzaby_64xx']/$kdOEMonatArray[$kunde][$oe][$jm]['vzkd']*100:0;
	$kdOEMonatArray[$kunde][$oe][$jm]['podil_65xx'] = $kdOEMonatArray[$kunde][$oe][$jm]['vzkd']!=0?$kdOEMonatArray[$kunde][$oe][$jm]['vzaby_65xx']/$kdOEMonatArray[$kunde][$oe][$jm]['vzkd']*100:0;

	$kdOESumMonatArray[$kunde][$oe]['vzaby_64xx'] += floatval($vzaby64xx);
	$kdOESumMonatArray[$kunde][$oe]['vzaby_65xx'] += floatval($vzaby65xx);
	$kdOESumMonatArray[$kunde][$oe]['vzkd'] += floatval($vzkd);
	$kdOESumMonatArray[$kunde][$oe]['podil_64xx'] = $kdOESumMonatArray[$kunde][$oe]['vzkd']!=0?$kdOESumMonatArray[$kunde][$oe]['vzaby_64xx']/$kdOESumMonatArray[$kunde][$oe]['vzkd']*100:0;
	$kdOESumMonatArray[$kunde][$oe]['podil_65xx'] = $kdOESumMonatArray[$kunde][$oe]['vzkd']!=0?$kdOESumMonatArray[$kunde][$oe]['vzaby_65xx']/$kdOESumMonatArray[$kunde][$oe]['vzkd']*100:0;

	$kdSumOESumMonatArray[$kunde]['vzaby_64xx'] += floatval($vzaby64xx);
	$kdSumOESumMonatArray[$kunde]['vzaby_65xx'] += floatval($vzaby65xx);
	$kdSumOESumMonatArray[$kunde]['vzkd'] += floatval($vzkd);
	$kdSumOESumMonatArray[$kunde]['podil_64xx'] = $kdSumOESumMonatArray[$kunde]['vzkd']!=0?$kdSumOESumMonatArray[$kunde]['vzaby_64xx']/$kdSumOESumMonatArray[$kunde]['vzkd']*100:0;
	$kdSumOESumMonatArray[$kunde]['podil_65xx'] = $kdSumOESumMonatArray[$kunde]['vzkd']!=0?$kdSumOESumMonatArray[$kunde]['vzaby_65xx']/$kdSumOESumMonatArray[$kunde]['vzkd']*100:0;
	
	
	//sumy pro OE pro vsechny zakazniky ------------------------------------
	$OESumMonatArray[$oe][$jm]['vzaby_64xx'] += floatval($vzaby64xx);
	$OESumMonatArray[$oe][$jm]['vzaby_65xx'] += floatval($vzaby65xx);
	$OESumMonatArray[$oe][$jm]['vzkd'] += floatval($vzkd);
	$OESumMonatArray[$oe][$jm]['podil_64xx'] = $OESumMonatArray[$oe][$jm]['vzkd']!=0?$OESumMonatArray[$oe][$jm]['vzaby_64xx']/$OESumMonatArray[$oe][$jm]['vzkd']*100:0;
	$OESumMonatArray[$oe][$jm]['podil_65xx'] = $OESumMonatArray[$oe][$jm]['vzkd']!=0?$OESumMonatArray[$oe][$jm]['vzaby_65xx']/$OESumMonatArray[$oe][$jm]['vzkd']*100:0;
	
	$OESumMonatSumArray[$oe]['vzaby_64xx'] += floatval($vzaby64xx);
	$OESumMonatSumArray[$oe]['vzaby_65xx'] += floatval($vzaby65xx);
	$OESumMonatSumArray[$oe]['vzkd'] += floatval($vzkd);
	$OESumMonatSumArray[$oe]['podil_64xx'] = $OESumMonatSumArray[$oe]['vzkd']!=0?$OESumMonatSumArray[$oe]['vzaby_64xx']/$OESumMonatSumArray[$oe]['vzkd']*100:0;
	$OESumMonatSumArray[$oe]['podil_65xx'] = $OESumMonatSumArray[$oe]['vzkd']!=0?$OESumMonatSumArray[$oe]['vzaby_65xx']/$OESumMonatSumArray[$oe]['vzkd']*100:0;
	
	$sumOESumMonatSumArray[$jm]['vzaby_64xx'] += floatval($vzaby64xx);
	$sumOESumMonatSumArray[$jm]['vzaby_65xx'] += floatval($vzaby65xx);
	$sumOESumMonatSumArray[$jm]['vzkd'] += floatval($vzkd);
	$sumOESumMonatSumArray[$jm]['podil_64xx'] = $sumOESumMonatSumArray[$jm]['vzkd']!=0?$sumOESumMonatSumArray[$jm]['vzaby_64xx']/$sumOESumMonatSumArray[$jm]['vzkd']*100:0;
	$sumOESumMonatSumArray[$jm]['podil_65xx'] = $sumOESumMonatSumArray[$jm]['vzkd']!=0?$sumOESumMonatSumArray[$jm]['vzaby_65xx']/$sumOESumMonatSumArray[$jm]['vzkd']*100:0;

	$sumOE['vzaby_64xx'] += floatval($vzaby64xx);
	$sumOE['vzaby_65xx'] += floatval($vzaby65xx);
	$sumOE['vzkd'] += floatval($vzkd);
	$sumOE['podil_64xx'] = $sumOE['vzkd']!=0?$sumOE['vzaby_64xx']/$sumOE['vzkd']*100:0;
	$sumOE['podil_65xx'] = $sumOE['vzkd']!=0?$sumOE['vzaby_65xx']/$sumOE['vzkd']*100:0;
	//----------------------------------------------------------------------
	
	
	$kdSumOEMonatArray[$kunde][$jm]['vzaby_64xx'] += floatval($vzaby64xx);
	$kdSumOEMonatArray[$kunde][$jm]['vzaby_65xx'] += floatval($vzaby65xx);
	$kdSumOEMonatArray[$kunde][$jm]['vzkd'] += floatval($vzkd);
	$kdSumOEMonatArray[$kunde][$jm]['podil_64xx'] = $kdSumOEMonatArray[$kunde][$jm]['vzkd']!=0?$kdSumOEMonatArray[$kunde][$jm]['vzaby_64xx']/$kdSumOEMonatArray[$kunde][$jm]['vzkd']*100:0;
	$kdSumOEMonatArray[$kunde][$jm]['podil_65xx'] = $kdSumOEMonatArray[$kunde][$jm]['vzkd']!=0?$kdSumOEMonatArray[$kunde][$jm]['vzaby_65xx']/$kdSumOEMonatArray[$kunde][$jm]['vzkd']*100:0;
    }
}

$jmArray = array_keys($jahrMonatArray);
sort($jmArray);
$kundenNrArray = array_keys($kundenArray);
sort($kundenNrArray);
$oeArray = array_keys($oeArray);
sort($oeArray);

//AplDB::varDump($jmArray);
//AplDB::varDump($kundenNrArray);
//AplDB::varDump($oeArray);
//AplDB::varDump($kdOEMonatArray);

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params = "Kunde $kdvon - $kdbis, Datum ".$_GET['von']."-".$_GET['bis'];
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S375 - opravy", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
//$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 7);
$pdf->SetLineWidth(0.1);

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$pocetZakazniku = count($kundenNrArray);
$pocetMesicu = count($jmArray);
//AplDB::varDump($pocetZakazniku);
//***************************************************************************************************************************\\
//ocelova modr
$pdf->SetFillColor(176,196,222);
$rowHeight = 6;
$oeWidth = 10;
$popisWidth = 20;
$sumaWidth = 15;
$prostorProMesice = $pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$oeWidth-$popisWidth-$sumaWidth;
$monatWidth = $prostorProMesice / $pocetMesicu;
$popisArray = array(
    "vzaby_64xx"=>array("popis"=>"VzAby 64xx","decimals"=>0,"suffix"=>''),
    "vzaby_65xx"=>array("popis"=>"VzAby 65xx","decimals"=>0,"suffix"=>''),
    "vzkd"=>array("popis"=>"VzKd","decimals"=>0,"suffix"=>''),
    "podil_64xx"=>array("popis"=>"Podil VzAby (64xx) oprav na VzKd v %","decimals"=>2,"suffix"=>"%"),
    "podil_65xx"=>array("popis"=>"Podil VzAby (65xx) oprav na VzKd v %","decimals"=>2,"suffix"=>'%'),
    );
//***************************************************************************************************************************\\


foreach ($kundenNrArray as $kunde){
    $pdf->AddPage();
    $pdf->SetFillColor(176,196,222);
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->Cell(0,$rowHeight,"Kunde: ".$kunde,'LRBT',1,'L',1);
    //hlavicku
    $pdf->Cell($oeWidth, $rowHeight, "cinnost", 'LRBT', 0, 'C', 1,'',0,TRUE);
    $pdf->Cell($popisWidth, $rowHeight, "popis", 'LRBT', 0, 'C', 1,'',0,TRUE);
    foreach ($jmArray as $jm){
	$pdf->Cell($monatWidth, $rowHeight, "$jm", 'LRBT', 0, 'C', 1,'',0,TRUE);
    }
    $pdf->Cell(0, $rowHeight, "celkem", 'LRBT', 0, 'C', 1,'',0,TRUE);
    $pdf->Ln();
    
    $kdOEArray = $kdOEMonatArray[$kunde];
    foreach ($oeArray as $oe){
	$pdf->SetFont("FreeSans", "", 7);
	if(is_array($kdOEArray[$oe])){
	    //u zakaznika jsem nasel oe
	    //$pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign)
	    $pdf->MultiCell($oeWidth, $rowHeight*5, $oe, 'LRBT', 'C', FALSE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight*5, 'M');
	    $xStart = $pdf->GetX();
	    foreach ($popisArray as $value=>$popisA){
		$pdf->SetFont("FreeSans", "", 7);
		$pdf->MultiCell($popisWidth, $rowHeight, $popisA['popis'], 'LRBT', 'C', FALSE, FALSE, $xStart, '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M',TRUE);
		//pole s mesici
		foreach ($jmArray as $jm){
		    $hodnota = number_format($kdOEArray[$oe][$jm][$value],$popisA['decimals'],',',' ');		    
		    $pdf->Cell($monatWidth, $rowHeight, "$hodnota".$popisA['suffix'], 'LRBT', 0, 'R', 0,'',0,TRUE);
		}
		// TODO jeste pridat sloupec se sumou
		$pdf->SetFillColor(255,255,230);
		$pdf->SetFont("FreeSans", "B", 7);
		$hodnota = number_format($kdOESumMonatArray[$kunde][$oe][$value],$popisA['decimals'],',',' ');		    
		$pdf->Cell($sumaWidth, $rowHeight, "$hodnota".$popisA['suffix'], 'LRBT', 0, 'R', 1,'',0,TRUE);
		//dalsi radek
		$pdf->Ln();
	    }
	    $pdf->SetFillColor(129,147,170);
	    $pdf->Cell(0,0.3,"",'1',1,'C',TRUE,'',0,TRUE);
	}
    }
    //suma pres vsechny oe
    $pdf->SetFillColor(176,196,222);
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->MultiCell($oeWidth, $rowHeight*5, "Sum", 'LRBT', 'C', TRUE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight*5, 'M');
    $xStart = $pdf->GetX();
    foreach ($popisArray as $value=>$popisA){
	$pdf->SetFillColor(176,196,222);
	$pdf->MultiCell($popisWidth, $rowHeight, $popisA['popis'], 'LRBT', 'C', TRUE, FALSE, $xStart, '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M',TRUE);
	//pole s mesici
	foreach ($jmArray as $jm){
	    $hodnota = number_format($kdSumOEMonatArray[$kunde][$jm][$value],$popisA['decimals'],',',' ');		    
	    $pdf->Cell($monatWidth, $rowHeight, "$hodnota".$popisA['suffix'], 'LRBT', 0, 'R', 1,'',0,TRUE);
	}
	// TODO jeste pridat sloupec se sumou
	$pdf->SetFillColor(255,255,230);
	$hodnota = number_format($kdSumOESumMonatArray[$kunde][$value],$popisA['decimals'],',',' ');		    
	$pdf->Cell($sumaWidth, $rowHeight, "$hodnota".$popisA['suffix'], 'LRBT', 0, 'R', 1,'',0,TRUE);
	//dalsi radek
	$pdf->Ln();
    }
}


//sumy pro oe pres vsechny zakazniky -------------------------------------------
$pdf->AddPage();
$pdf->SetFillColor(176,196,222);
$pdf->SetFont("FreeSans", "B", 7);
$pdf->Cell(0,$rowHeight,"OE Summen alle Kunden",'LRBT',1,'L',1);
//hlavicku
$pdf->Cell($oeWidth, $rowHeight, "cinnost", 'LRBT', 0, 'C', 1,'',0,TRUE);
$pdf->Cell($popisWidth, $rowHeight, "popis", 'LRBT', 0, 'C', 1,'',0,TRUE);
foreach ($jmArray as $jm){
    $pdf->Cell($monatWidth, $rowHeight, "$jm", 'LRBT', 0, 'C', 1,'',0,TRUE);
}
$pdf->Cell(0, $rowHeight, "celkem", 'LRBT', 0, 'C', 1,'',0,TRUE);
$pdf->Ln();
foreach ($oeArray as $oe){
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->MultiCell($oeWidth, $rowHeight*5, $oe, 'LRBT', 'C', FALSE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight*5, 'M');
    $xStart = $pdf->GetX();
    foreach ($popisArray as $value=>$popisA){
	$pdf->SetFont("FreeSans", "", 7);
	$pdf->MultiCell($popisWidth, $rowHeight, $popisA['popis'], 'LRBT', 'C', FALSE, FALSE, $xStart, '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M',TRUE);
	//pole s mesici
	foreach ($jmArray as $jm){
	    $hodnota = number_format($OESumMonatArray[$oe][$jm][$value],$popisA['decimals'],',',' ');		    
	    $pdf->Cell($monatWidth, $rowHeight, "$hodnota".$popisA['suffix'], 'LRBT', 0, 'R', 0,'',0,TRUE);
	}
	// TODO jeste pridat sloupec se sumou
	$pdf->SetFillColor(255,255,230);
	$pdf->SetFont("FreeSans", "B", 7);
	$hodnota = number_format($OESumMonatSumArray[$oe][$value],$popisA['decimals'],',',' ');		    
	$pdf->Cell($sumaWidth, $rowHeight, "$hodnota".$popisA['suffix'], 'LRBT', 0, 'R', 1,'',0,TRUE);
	//dalsi radek
	$pdf->Ln();
    }
    $pdf->SetFillColor(129,147,170);
    $pdf->Cell(0,0.3,"",'1',1,'C',TRUE,'',0,TRUE);
}
//suma pres vsechny oe
$pdf->SetFillColor(176, 196, 222);
$pdf->SetFont("FreeSans", "B", 6.2);
$pdf->MultiCell($oeWidth, $rowHeight * 5, "Sum", 'LRBT', 'C', TRUE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight * 5, 'M');
$xStart = $pdf->GetX();
foreach ($popisArray as $value => $popisA) {
    $pdf->SetFillColor(176, 196, 222);
    $pdf->MultiCell($popisWidth, $rowHeight, $popisA['popis'], 'LRBT', 'C', TRUE, FALSE, $xStart, '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M', TRUE);
    //pole s mesici
    foreach ($jmArray as $jm) {
	$hodnota = number_format($sumOESumMonatSumArray[$jm][$value], $popisA['decimals'], ',', ' ');
	$pdf->Cell($monatWidth, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
    }
    // celkova suma pres vsechno
    // TODO jeste pridat sloupec se sumou
    $pdf->SetFillColor(255, 255, 230);
    $hodnota = number_format($sumOE[$value], $popisA['decimals'], ',', ' ');
    $pdf->Cell($sumaWidth, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
    //dalsi radek
    $pdf->Ln();
}

// sumy pre zakazniky, jinak formatovane nez zbytek
//hlavicka


$pdf->AddPage('L','',TRUE);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S3750 - opravy", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$headerWidth = 15;
$celkemWidth = 25;
$prostorProMesice = $pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$headerWidth-$celkemWidth;
$monatWidth = $prostorProMesice / $pocetMesicu;

$pdf->SetFillColor(176,196,222);
$pdf->SetFont("FreeSans", "B", 7);
$pdf->Cell(0,$rowHeight,"Summen Kunden",'LRBT',1,'L',1);
//hlavicku
$pdf->Cell($headerWidth, $rowHeight, "mesic", 'LRBT', 0, 'C', 1,'',0,TRUE);
foreach ($jmArray as $jm){
    $pdf->Cell($monatWidth, $rowHeight, "$jm", 'LRBT', 0, 'C', 1,'',0,TRUE);
}

//------------------------------------------------------------------------------
// nadpis celkove sumy
//------------------------------------------------------------------------------

$pdf->SetFillColor(255, 255, 230);
$pdf->Cell(0, $rowHeight, "celkem", 'LRBT', 0, 'C', 1,'',0,TRUE);
$pdf->Ln();
$pdf->SetFillColor(176,196,222);
$pdf->Cell($headerWidth, $rowHeight, "zákazník", 'LRBT', 0, 'C', 1,'',0,TRUE);
foreach ($jmArray as $jm){
    $pdf->MultiCell($monatWidth/2, $rowHeight, "VzAby 64XX", 'LRBT', 'C', TRUE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M',TRUE);
    $pdf->MultiCell($monatWidth/2, $rowHeight, "VzAby 65XX", 'LRBT', 'C', TRUE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M',TRUE);
}
$pdf->SetFillColor(255,255,230);
$pdf->MultiCell($celkemWidth/2, $rowHeight, "VzAby 64XX", 'LRBT', 'C', TRUE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M',TRUE);
$pdf->MultiCell(0, $rowHeight, "VzAby 65XX", 'LRBT', 'C', TRUE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M',TRUE);
$pdf->Ln();
//zakaznici
foreach ($kundenNrArray as $kd){
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->Cell($headerWidth, $rowHeight, "$kd", 'LRBT', 0, 'R', 0,'',0,TRUE);
    foreach ($jmArray as $jm){
	$value = "vzaby_64xx";
	$popisA = $popisArray[$value];
	$hodnota = number_format($kdSumOEMonatArray[$kd][$jm][$value], $popisA['decimals'], ',', ' ');
	$pdf->Cell($monatWidth/2, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 0, '', 0, TRUE);
	$value = "vzaby_65xx";
	$popisA = $popisArray[$value];
	$hodnota = number_format($kdSumOEMonatArray[$kd][$jm][$value], $popisA['decimals'], ',', ' ');
	$pdf->Cell($monatWidth/2, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 0, '', 0, TRUE);
    }
    $value = "vzaby_64xx";
    $popisA = $popisArray[$value];
    $hodnota = number_format($kdSumOESumMonatArray[$kd][$value], $popisA['decimals'], ',', ' ');
    $pdf->Cell($celkemWidth/2, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
    $value = "vzaby_65xx";
    $popisA = $popisArray[$value];
    $hodnota = number_format($kdSumOESumMonatArray[$kd][$value], $popisA['decimals'], ',', ' ');
    $pdf->Cell(0, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
    $pdf->Ln();
}

//celkem
$pdf->SetFont("FreeSans", "B", 6.5);
$pdf->SetFillColor(176,196,222);
$pdf->MultiCell($headerWidth, $rowHeight, "Celkem VzAby", 'LRBT', 'L', TRUE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M', TRUE);
//$pdf->Cell($headerWidth, $rowHeight, "Celkem", 'LRBT', 0, 'L', 1, '', 0, TRUE);
foreach ($jmArray as $jm) {
    $value = "vzaby_64xx";
    $popisA = $popisArray[$value];
    $hodnota = number_format($sumOESumMonatSumArray[$jm][$value], $popisA['decimals'], ',', ' ');
    $pdf->Cell($monatWidth / 2, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
    $value = "vzaby_65xx";
    $popisA = $popisArray[$value];
    $hodnota = number_format($sumOESumMonatSumArray[$jm][$value], $popisA['decimals'], ',', ' ');
    $pdf->Cell($monatWidth / 2, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
}
$pdf->SetFillColor(255,255,230);
$value = "vzaby_64xx";
$popisA = $popisArray[$value];
$hodnota = number_format($sumOE[$value], $popisA['decimals'], ',', ' ');
$pdf->Cell($celkemWidth/2, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
$value = "vzaby_65xx";
$popisA = $popisArray[$value];
$hodnota = number_format($sumOE[$value], $popisA['decimals'], ',', ' ');
$pdf->Cell(0, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
$pdf->Ln();

//vzkd
$pdf->SetFont("FreeSans", "B", 6.5);
$pdf->SetFillColor(176,196,222);
$pdf->Cell($headerWidth, $rowHeight, "VzKd", 'LRBT', 0, 'L', 1, '', 0, TRUE);
foreach ($jmArray as $jm) {
    $value = "vzkd";
    $popisA = $popisArray[$value];
    $hodnota = number_format($sumOESumMonatSumArray[$jm][$value], $popisA['decimals'], ',', ' ');
    $pdf->Cell($monatWidth, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
}
$pdf->SetFillColor(255,255,230);
$value = "vzkd";
$popisA = $popisArray[$value];
$hodnota = number_format($sumOE[$value], $popisA['decimals'], ',', ' ');
$pdf->Cell(0, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
$pdf->Ln();

//procenta
$pdf->SetFont("FreeSans", "B", 6.5);
$pdf->SetFillColor(176,196,222);
$pdf->MultiCell($headerWidth, $rowHeight, "VzAby\n / VzKd [%]", 'LRBT', 'L', TRUE, FALSE, '', '', TRUE, TRUE, FALSE, FALSE, $rowHeight, 'M', TRUE);
//$pdf->Cell($headerWidth, $rowHeight, "%", 'LRBT', 0, 'L', 1, '', 0, TRUE);
foreach ($jmArray as $jm) {
    $value = "podil_64xx";
    $popisA = $popisArray[$value];
    $hodnota = number_format($sumOESumMonatSumArray[$jm][$value], $popisA['decimals'], ',', ' ');
    $pdf->Cell($monatWidth / 2, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
    $value = "podil_65xx";
    $popisA = $popisArray[$value];
    $hodnota = number_format($sumOESumMonatSumArray[$jm][$value], $popisA['decimals'], ',', ' ');
    $pdf->Cell($monatWidth / 2, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
}
$pdf->SetFillColor(255,255,230);
$value = "podil_64xx";
$popisA = $popisArray[$value];
$hodnota = number_format($sumOE[$value], $popisA['decimals'], ',', ' ');
$pdf->Cell($celkemWidth/2, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
$value = "podil_65xx";
$popisA = $popisArray[$value];
$hodnota = number_format($sumOE[$value], $popisA['decimals'], ',', ' ');
$pdf->Cell(0, $rowHeight, "$hodnota" . $popisA['suffix'], 'LRBT', 0, 'R', 1, '', 0, TRUE);
$pdf->Ln();

// grafy
$pdf->AddPage('L');

$graphVzAbyKd = array();
foreach ($kundenNrArray as $kd){
    //$graphVzAbyKd[$kd] = array('64XX'=>$kdSumOESumMonatArray[$kd]['vzaby_64xx'],'65XX'=>$kdSumOESumMonatArray[$kd]['vzaby_65xx']);
    $graphVzAbyKd['vzaby_64xx'][$kd] = round($kdSumOESumMonatArray[$kd]['vzaby_64xx']);
    $graphVzAbyKd['vzaby_65xx'][$kd] = round($kdSumOESumMonatArray[$kd]['vzaby_65xx']);
}

$graphVzAbyOeJm = array();
$graphVzAbyPodilOeJm = array();

foreach ($oeArray as $oe){
    foreach ($jmArray as $jm){
	$graphVzAbyOeJm[$oe][$jm] = round($OESumMonatArray[$oe][$jm]['vzaby_64xx']);
	$graphVzAbyPodilOeJm[$oe][$jm] = $OESumMonatArray[$oe][$jm]['podil_64xx'];
    }
}
//AplDB::varDump($graphVzAbyPodilOeJm);

//$graphVzAbyKd['celkem'] = array('64XX'=>$sumOE['vzaby_64xx'],'65XX'=>$sumOE['vzaby_65xx']);

//$graphVzAbyKd['vzaby_64xx']['celkem'] = round($sumOE['vzaby_64xx']);
//$graphVzAbyKd['vzaby_65xx']['celkem'] = round($sumOE['vzaby_65xx']);


include("../Classes/pChart/class/pData.class.php");
include("../Classes/pChart/class/pDraw.class.php");
include("../Classes/pChart/class/pImage.class.php");

$myData = new pData();
$myData->addPoints($graphVzAbyKd['vzaby_64xx'], "64xx");
$myData->addPoints($graphVzAbyKd['vzaby_65xx'], "65xx");

$Palette = array(
    "0"=>array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>100),
    "1"=>array("R"=>224,"G"=>100,"B"=>46,"Alpha"=>100),
    "2"=>array("R"=>224,"G"=>214,"B"=>46,"Alpha"=>100),
    "3"=>array("R"=>46,"G"=>151,"B"=>224,"Alpha"=>100),
    "4"=>array("R"=>176,"G"=>46,"B"=>224,"Alpha"=>100),
    "5"=>array("R"=>224,"G"=>46,"B"=>117,"Alpha"=>100),
    "6"=>array("R"=>92,"G"=>224,"B"=>46,"Alpha"=>100),
    "7"=>array("R"=>224,"G"=>176,"B"=>46,"Alpha"=>100)
    );

$Palette1 = array(
    "0"=>array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>10),
    "1"=>array("R"=>224,"G"=>100,"B"=>46,"Alpha"=>10),
    "2"=>array("R"=>224,"G"=>214,"B"=>46,"Alpha"=>10),
    "3"=>array("R"=>46,"G"=>151,"B"=>224,"Alpha"=>10),
    "4"=>array("R"=>176,"G"=>46,"B"=>224,"Alpha"=>10),
    "5"=>array("R"=>224,"G"=>46,"B"=>117,"Alpha"=>10),
    "6"=>array("R"=>92,"G"=>224,"B"=>46,"Alpha"=>10),
    "7"=>array("R"=>224,"G"=>176,"B"=>46,"Alpha"=>10)
    );

//$myData->setPalette("65xx",$Palette);
//$myData->setPalette("64xx",$Palette1);

$abscissaArray = $kundenNrArray;
//array_push($abscissaArray,"celkem");

$myData->addPoints($abscissaArray, "zakaznici");
$myData->setSerieDescription("zakaznici","Kunden");
$myData->setAbscissa("zakaznici");

$myData->setSerieDescription("64xx", "VzAby 64XX [min]");
$myData->setSerieDescription("65xx", "VzAby 65XX [min]");

$myData->setAxisName(0,"VzAby [min]");

$imgWidth = 1800;
$imgHeight = 1000;
$myPicture = new pImage($imgWidth, $imgHeight, $myData);
$Settings = array("StartR" => 231, "StartG" => 231, "StartB" => 97, "EndR" => 1, "EndG" => 138, "EndB" => 68, "Alpha" => 100);
//oramovat a vyplnit prostor pro obrazek
$myPicture->drawGradientArea(0,0,$imgWidth,$imgHeight,DIRECTION_VERTICAL,array("StartR"=>220,"StartG"=>220,"StartB"=>220,"EndR"=>255,"EndG"=>255,"EndB"=>255,"Alpha"=>100));
//$myPicture->setShadow(TRUE,array("X"=>4,"Y"=>4,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>40));
$myPicture->drawRectangle(0,0,$imgWidth-1,$imgHeight-1,array("R"=>0,"G"=>0,"B"=>0));
$myPicture->setShadow(FALSE);

$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Medium.ttf", "FontSize" => 20,"Alpha"=>100));
$TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
, "R" => 0, "G" => 0, "B" => 0,"Alpha"=>100); //ocelova modr
$myPicture->drawText($imgWidth/2, 25, "VzAby na interní reklamace (opravy 64xx,65xx) - zakaznik $kdvon - $kdbis, obdobi $von - $bis", $TextSettings);

$myPicture->setGraphArea(100, 50, $imgWidth-20, $imgHeight-200);
//zvyraznit prostor pro graf
$myPicture->drawFilledRectangle(100,50,$imgWidth-20,$imgHeight-50,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>50));
$myPicture->setFontProperties(array("R" => 0, "G" => 0, "B" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14));

$Settings = array(
      "Pos" => SCALE_POS_LEFTRIGHT
    , "Mode" => SCALE_MODE_START0
    , "LabelingMethod" => LABELING_ALL
    ,"CycleBackground"=>TRUE
    , "GridR" => 5
    , "GridG" => 5
    , "GridB" => 5
    , "GridAlpha" => 50
    , "TickR" => 0
    , "TickG" => 0
    , "TickB" => 0
    , "TickAlpha" => 50
    , "LabelRotation" => 0
    , "CycleBackground" => 1
    , "DrawXLines" => TRUE
    , "DrawYLines" => TRUE
    , "DrawSubTicks" => 0
    , "SubTickR" => 255
    , "SubTickG" => 0
    , "SubTickB" => 0
    , "SubTickAlpha" => 50
);
$myPicture->drawScale($Settings);
//$myPicture->drawScale();

$Config = array(
    "AroundZero" => 0,
    "DisplayPos"=>LABEL_POS_OUTSIDE,
    "DisplayValues"=>TRUE,
    "Gradient"=>TRUE,
    "Interleave"=>0.5,
    //"DisplayOrientation"=>ORIENTATION_HORIZONTAL,
    //"Threshold"=>$Threshold
    //"OverrideColors"=>$Palette,
);
// vykreslit chart
$myPicture->drawBarChart($Config);
$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20)); 
$myPicture->drawRoundedFilledRectangle(1300-10, 840, $imgWidth-100, $imgHeight-90, 10,array("R"=>240,"G"=>240,"B"=>240,"Alpha"=>100,"Surrounding"=>-200));
$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Bold.ttf","FontSize"=>15,"R"=>0,"G"=>0,"B"=>0));

$myPicture->drawText(1300,840+25,"Celkem:",array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
$myPicture->drawText(1300+100,840+30,"VzAby 64XX [min]",array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
$vzAby64xxCelkem = number_format(round($sumOE['vzaby_64xx']),0,',',' ');
$myPicture->drawText($imgWidth-110,840+25,$vzAby64xxCelkem,array("Align"=>TEXT_ALIGN_BOTTOMRIGHT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));

$myPicture->drawText(1300+100,840+60,"VzAby 65XX [min]",array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
$vzAby65xxCelkem = number_format(round($sumOE['vzaby_65xx']),0,',',' ');
$myPicture->drawText($imgWidth-110,840+55,$vzAby65xxCelkem,array("Align"=>TEXT_ALIGN_BOTTOMRIGHT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));


$Config = array("FontR" => 0, "FontG" => 0, "FontB" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14, "Margin" => 6, "Alpha" => 100, "BoxWidth" => 20,"BoxHeight" => 20, "Style" => LEGEND_NOBORDER
, "Mode" => LEGEND_VERTICAL
);

$myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>40));
$myPicture->drawLegend(110, 850, $Config);

//$myPicture->stroke();
//toto taky funguje

$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Bold.ttf","FontSize"=>15,"R"=>0,"G"=>0,"B"=>0));

//foreach ($krRows as $kr){
//    $myPicture->drawThreshold($kr['grenze'], array(
//            "R"=>255,
//            "G"=>0,
//            "B"=>0,
//            "DrawBox"=>TRUE,
//            "BoxR"=>80,
//            "BoxG"=>80,
//            "BoxB"=>80,
//            "BoxAlpha"=>200,
//            "Alpha"=>255,
//            "NoMargin"=>TRUE,
//            "CaptionAlign"=>CAPTION_RIGHT_BOTTOM,
//            //"CaptionOffset"=>TRUE,
//            "OffsetX"=>50,
//            "Ticks"=>1,
//            "WriteCaption"=>TRUE,
//            "Caption"=>"do ".$kr['grenze']." bodů = hodnocení ".$kr['bewertung'],
//            "CaptionAlpha"=>255,
//        )
//    );
//}

$myPicture->Render("S375_graf.png");
//$pdf->AddPage();
$y = $pdf->GetY();
$pdf->Image("S375_graf.png", PDF_MARGIN_LEFT, $y + 10, 260, 160, 'PNG');



$pdf->AddPage('L');

$myData = new pData();
foreach ($oeArray as $oe){
    if($oe=='S') continue;
    $myData->addPoints($graphVzAbyOeJm[$oe], "$oe");
    $myData->setSerieDescription($oe, "$oe");
}


$Palette = array(
    "0"=>array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>100),
    "1"=>array("R"=>224,"G"=>100,"B"=>46,"Alpha"=>100),
    "2"=>array("R"=>224,"G"=>214,"B"=>46,"Alpha"=>100),
    "3"=>array("R"=>46,"G"=>151,"B"=>224,"Alpha"=>100),
    "4"=>array("R"=>176,"G"=>46,"B"=>224,"Alpha"=>100),
    "5"=>array("R"=>224,"G"=>46,"B"=>117,"Alpha"=>100),
    "6"=>array("R"=>92,"G"=>224,"B"=>46,"Alpha"=>100),
    "7"=>array("R"=>224,"G"=>176,"B"=>46,"Alpha"=>100)
    );

$Palette1 = array(
    "0"=>array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>10),
    "1"=>array("R"=>224,"G"=>100,"B"=>46,"Alpha"=>10),
    "2"=>array("R"=>224,"G"=>214,"B"=>46,"Alpha"=>10),
    "3"=>array("R"=>46,"G"=>151,"B"=>224,"Alpha"=>10),
    "4"=>array("R"=>176,"G"=>46,"B"=>224,"Alpha"=>10),
    "5"=>array("R"=>224,"G"=>46,"B"=>117,"Alpha"=>10),
    "6"=>array("R"=>92,"G"=>224,"B"=>46,"Alpha"=>10),
    "7"=>array("R"=>224,"G"=>176,"B"=>46,"Alpha"=>10)
    );

//$myData->setPalette("65xx",$Palette);
//$myData->setPalette("64xx",$Palette1);

$abscissaArray = $jmArray;
//AplDB::varDump($jmArray);

//array_push($abscissaArray,"celkem");

$myData->addPoints($abscissaArray, "jm");
$myData->setSerieDescription("jm","Monate");
$myData->setAbscissa("jm");

$myData->setAxisName(0,"VzAby [min]");

$imgWidth = 1800;
$imgHeight = 1000;
$myPicture = new pImage($imgWidth, $imgHeight, $myData);
$Settings = array("StartR" => 231, "StartG" => 231, "StartB" => 97, "EndR" => 1, "EndG" => 138, "EndB" => 68, "Alpha" => 100);
//oramovat a vyplnit prostor pro obrazek
$myPicture->drawGradientArea(0,0,$imgWidth,$imgHeight,DIRECTION_VERTICAL,array("StartR"=>220,"StartG"=>220,"StartB"=>220,"EndR"=>255,"EndG"=>255,"EndB"=>255,"Alpha"=>100));
//$myPicture->setShadow(TRUE,array("X"=>4,"Y"=>4,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>40));
$myPicture->drawRectangle(0,0,$imgWidth-1,$imgHeight-1,array("R"=>0,"G"=>0,"B"=>0));
$myPicture->setShadow(FALSE);

$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Medium.ttf", "FontSize" => 20,"Alpha"=>100));
$TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
, "R" => 0, "G" => 0, "B" => 0,"Alpha"=>100); //ocelova modr
$myPicture->drawText($imgWidth/2, 25, "VzAby na interní reklamace (opravy 64xx) - zakaznik $kdvon - $kdbis, obdobi $von - $bis", $TextSettings);

$myPicture->setGraphArea(100, 50, $imgWidth-20, $imgHeight-200);
//zvyraznit prostor pro graf
$myPicture->drawFilledRectangle(100,50,$imgWidth-20,$imgHeight-50,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>50));
$myPicture->setFontProperties(array("R" => 0, "G" => 0, "B" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14));

$Settings = array(
      "Pos" => SCALE_POS_LEFTRIGHT
    , "Mode" => SCALE_MODE_START0
    , "LabelingMethod" => LABELING_ALL
    ,"CycleBackground"=>TRUE
    , "GridR" => 5
    , "GridG" => 5
    , "GridB" => 5
    , "GridAlpha" => 50
    , "TickR" => 0
    , "TickG" => 0
    , "TickB" => 0
    , "TickAlpha" => 50
    , "LabelRotation" => 0
    , "CycleBackground" => 1
    , "DrawXLines" => TRUE
    , "DrawYLines" => TRUE
    , "DrawSubTicks" => 0
    , "SubTickR" => 255
    , "SubTickG" => 0
    , "SubTickB" => 0
    , "SubTickAlpha" => 50
);
$myPicture->drawScale($Settings);
//$myPicture->drawScale();

$Config = array(
    "AroundZero" => 0,
    "DisplayPos"=>LABEL_POS_OUTSIDE,
    "DisplayValues"=>FALSE,
    "Gradient"=>TRUE,
    "Interleave"=>1,
    //"DisplayOrientation"=>ORIENTATION_HORIZONTAL,
    //"Threshold"=>$Threshold
    //"OverrideColors"=>$Palette,
);


//$myData->setPalette("64xx",$Palette);
//
// vykreslit chart
$myPicture->drawBarChart($Config);
//$myPicture->drawStackedBarChart($Config);
//$myPicture->drawFilledStepChart();

/*
$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20)); 
$myPicture->drawRoundedFilledRectangle(1300-10, 840, $imgWidth-100, $imgHeight-90, 10,array("R"=>240,"G"=>240,"B"=>240,"Alpha"=>100,"Surrounding"=>-200));
$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Bold.ttf","FontSize"=>15,"R"=>0,"G"=>0,"B"=>0));

$myPicture->drawText(1300,840+25,"Celkem:",array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
$myPicture->drawText(1300+100,840+30,"VzAby 64XX [min]",array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
$vzAby64xxCelkem = number_format(round($sumOE['vzaby_64xx']),0,',',' ');
$myPicture->drawText($imgWidth-110,840+25,$vzAby64xxCelkem,array("Align"=>TEXT_ALIGN_BOTTOMRIGHT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));

$myPicture->drawText(1300+100,840+60,"VzAby 65XX [min]",array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
$vzAby65xxCelkem = number_format(round($sumOE['vzaby_65xx']),0,',',' ');
$myPicture->drawText($imgWidth-110,840+55,$vzAby65xxCelkem,array("Align"=>TEXT_ALIGN_BOTTOMRIGHT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));

*/

$Config = array("FontR" => 0, "FontG" => 0, "FontB" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14, "Margin" => 6, "Alpha" => 100, "BoxWidth" => 20,"BoxHeight" => 20, "Style" => LEGEND_NOBORDER
, "Mode" => LEGEND_HORIZONTAL
);

$myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>40));
$myPicture->drawLegend(110, 850, $Config);


$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Bold.ttf","FontSize"=>15,"R"=>0,"G"=>0,"B"=>0));

$myPicture->Render("S375_graf1.png");
//$pdf->AddPage();
$y = $pdf->GetY();
$pdf->Image("S375_graf1.png", PDF_MARGIN_LEFT, $y + 10, 260, 160, 'PNG');


$pdf->AddPage('L');

$myData = new pData();
foreach ($oeArray as $oe){
    if($oe=='S') continue;
    $myData->addPoints($graphVzAbyPodilOeJm[$oe], "$oe");
    $myData->setSerieDescription($oe, "$oe");
}


$Palette = array(
    "0"=>array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>100),
    "1"=>array("R"=>224,"G"=>100,"B"=>46,"Alpha"=>100),
    "2"=>array("R"=>224,"G"=>214,"B"=>46,"Alpha"=>100),
    "3"=>array("R"=>46,"G"=>151,"B"=>224,"Alpha"=>100),
    "4"=>array("R"=>176,"G"=>46,"B"=>224,"Alpha"=>100),
    "5"=>array("R"=>224,"G"=>46,"B"=>117,"Alpha"=>100),
    "6"=>array("R"=>92,"G"=>224,"B"=>46,"Alpha"=>100),
    "7"=>array("R"=>224,"G"=>176,"B"=>46,"Alpha"=>100)
    );

$Palette1 = array(
    "0"=>array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>10),
    "1"=>array("R"=>224,"G"=>100,"B"=>46,"Alpha"=>10),
    "2"=>array("R"=>224,"G"=>214,"B"=>46,"Alpha"=>10),
    "3"=>array("R"=>46,"G"=>151,"B"=>224,"Alpha"=>10),
    "4"=>array("R"=>176,"G"=>46,"B"=>224,"Alpha"=>10),
    "5"=>array("R"=>224,"G"=>46,"B"=>117,"Alpha"=>10),
    "6"=>array("R"=>92,"G"=>224,"B"=>46,"Alpha"=>10),
    "7"=>array("R"=>224,"G"=>176,"B"=>46,"Alpha"=>10)
    );

//$myData->setPalette("65xx",$Palette);
//$myData->setPalette("64xx",$Palette1);

$abscissaArray = $jmArray;
//AplDB::varDump($jmArray);

//array_push($abscissaArray,"celkem");

$myData->addPoints($abscissaArray, "jm");
$myData->setSerieDescription("jm","Monate");
$myData->setAbscissa("jm");

$myData->setAxisName(0,"[%]");
$myData->setAxisDisplay(0,AXIS_FORMAT_CUSTOM,"procFormat");

function procFormat($v){
    return number_format($v, 1, ',', ' ');
}

$imgWidth = 1800;
$imgHeight = 1000;
$myPicture = new pImage($imgWidth, $imgHeight, $myData);
$Settings = array("StartR" => 231, "StartG" => 231, "StartB" => 97, "EndR" => 1, "EndG" => 138, "EndB" => 68, "Alpha" => 100);
//oramovat a vyplnit prostor pro obrazek
$myPicture->drawGradientArea(0,0,$imgWidth,$imgHeight,DIRECTION_VERTICAL,array("StartR"=>220,"StartG"=>220,"StartB"=>220,"EndR"=>255,"EndG"=>255,"EndB"=>255,"Alpha"=>100));
//$myPicture->setShadow(TRUE,array("X"=>4,"Y"=>4,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>40));
$myPicture->drawRectangle(0,0,$imgWidth-1,$imgHeight-1,array("R"=>0,"G"=>0,"B"=>0));
$myPicture->setShadow(FALSE);

$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Medium.ttf", "FontSize" => 20,"Alpha"=>100));
$TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
, "R" => 0, "G" => 0, "B" => 0,"Alpha"=>100); //ocelova modr
$myPicture->drawText($imgWidth/2, 25, "Podíl interní reklamací (opravy 64xx) na celk. výrobě (VzKd) - zakaznik $kdvon - $kdbis, obdobi $von - $bis", $TextSettings);

$myPicture->setGraphArea(100, 50, $imgWidth-20, $imgHeight-200);
//zvyraznit prostor pro graf
$myPicture->drawFilledRectangle(100,50,$imgWidth-20,$imgHeight-50,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>50));
$myPicture->setFontProperties(array("R" => 0, "G" => 0, "B" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14));

$Settings = array(
      "Pos" => SCALE_POS_LEFTRIGHT
    , "Mode" => SCALE_MODE_START0
    , "LabelingMethod" => LABELING_ALL
    ,"CycleBackground"=>TRUE
    , "GridR" => 5
    , "GridG" => 5
    , "GridB" => 5
    , "GridAlpha" => 50
    , "TickR" => 0
    , "TickG" => 0
    , "TickB" => 0
    , "TickAlpha" => 50
    , "LabelRotation" => 0
    , "CycleBackground" => 1
    , "DrawXLines" => TRUE
    , "DrawYLines" => TRUE
    , "DrawSubTicks" => 0
    , "SubTickR" => 255
    , "SubTickG" => 0
    , "SubTickB" => 0
    , "SubTickAlpha" => 50
);
$myPicture->drawScale($Settings);
//$myPicture->drawScale();

$Config = array(
    "AroundZero" => 0,
    "DisplayPos"=>LABEL_POS_OUTSIDE,
    "DisplayValues"=>FALSE,
    "Gradient"=>TRUE,
    "Interleave"=>1,
    //"DisplayOrientation"=>ORIENTATION_HORIZONTAL,
    //"Threshold"=>$Threshold
    //"OverrideColors"=>$Palette,
);

$myPicture->drawThresholdArea(0, 0.0, array("R"=>0,"G"=>255,"B"=>0,"Alpha"=>30));
$myPicture->drawThresholdArea(0.0, 0.6, array("R"=>180,"G"=>255,"B"=>180,"Alpha"=>30));
$myPicture->drawThresholdArea(0.6, 0.8, array("R"=>255,"G"=>255,"B"=>150,"Alpha"=>30));
$myPicture->drawThresholdArea(0.8, 100, array("R"=>255,"G"=>100,"B"=>100,"Alpha"=>30));
//$myData->setPalette("64xx",$Palette);
//
// vykreslit chart
$myPicture->drawBarChart($Config);
//$myPicture->drawStackedBarChart($Config);
//$myPicture->drawFilledStepChart();

/*
$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20)); 
$myPicture->drawRoundedFilledRectangle(1300-10, 840, $imgWidth-100, $imgHeight-90, 10,array("R"=>240,"G"=>240,"B"=>240,"Alpha"=>100,"Surrounding"=>-200));
$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Bold.ttf","FontSize"=>15,"R"=>0,"G"=>0,"B"=>0));

$myPicture->drawText(1300,840+25,"Celkem:",array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
$myPicture->drawText(1300+100,840+30,"VzAby 64XX [min]",array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
$vzAby64xxCelkem = number_format(round($sumOE['vzaby_64xx']),0,',',' ');
$myPicture->drawText($imgWidth-110,840+25,$vzAby64xxCelkem,array("Align"=>TEXT_ALIGN_BOTTOMRIGHT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));

$myPicture->drawText(1300+100,840+60,"VzAby 65XX [min]",array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
$vzAby65xxCelkem = number_format(round($sumOE['vzaby_65xx']),0,',',' ');
$myPicture->drawText($imgWidth-110,840+55,$vzAby65xxCelkem,array("Align"=>TEXT_ALIGN_BOTTOMRIGHT,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));

*/

$Config = array("FontR" => 0, "FontG" => 0, "FontB" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14, "Margin" => 6, "Alpha" => 100, "BoxWidth" => 20,"BoxHeight" => 20, "Style" => LEGEND_NOBORDER
, "Mode" => LEGEND_HORIZONTAL
);

$myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>40));
$myPicture->drawLegend(110, 850, $Config);


$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Bold.ttf","FontSize"=>15,"R"=>0,"G"=>0,"B"=>0));

$myPicture->Render("S375_graf2.png");
//$pdf->AddPage();
$y = $pdf->GetY();
$pdf->Image("S375_graf2.png", PDF_MARGIN_LEFT, $y + 10, 260, 160, 'PNG');

//AplDB::varDump($myData);


$pdf->Output();

