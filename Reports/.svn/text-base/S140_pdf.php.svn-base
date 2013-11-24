<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S140";
$doc_subject = "S140 Report";
$doc_keywords = "S140";

// necham si vygenerovat XML

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];


$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S140",$password,$user,0);

if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}


require_once('S140_xml.php');


// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");


foreach ($parameters as $param) {
    $parametry=$param->childNodes;
    // v ramci parametru si prectu label a hodnotu
    foreach($parametry as $parametr) {
        $parametr=$parametr->childNodes;
        foreach($parametr as $par) {
            if($par->nodeName=="label")
                $label=$par->nodeValue;
            if($par->nodeName=="value")
                $value=$par->nodeValue;
        }
        if(strtolower($label)!="password")
            $params .= $label.": ".$value."  ";
    //		$params .= $label.": ".$value."  ";
    }
}



$sum_zapati_persnr_array;
global $sum_zapati_persnr_array;

$sum_zapati_sestava_array;
global $sum_zapati_sestava_array;



//
/**
 * funkce pro vykresleni hlavicky na kazde strance
 * @param TCPDF $pdfobjekt
 * @param <type> $pole
 * @param <type> $headervyskaradku
 */
function pageheader($pdfobjekt,$rgb,$vyskaradku,$monat,$jahr) {

        $fill = 1;
        $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

        $von = sprintf("%02d.%02d.%4d", 1,$monat,$jahr);
        $dbVon = sprintf("%4d-%02d-%02d", $jahr,$monat,1);
        $pocetDnu = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $bis = sprintf("%02d.%02d.%4d", $pocetDnu,$monat,$jahr);
        $dbBis = sprintf("%4d-%02d-%02d", $jahr,$monat,$pocetDnu);
        $arbTage = getArbTageBetweenDatums($dbVon,$dbBis);
        $dayHeute = date('d');
        $monthHeute = date('m');
        $yearHeute = date('Y');

        $stampHeute = mktime(1, 1, 1, $monthHeute, $dayHeute, $yearHeute);
        $stampBis = strtotime($dbBis);

        if($stampHeute>$stampBis)
            $dbBisHeute = $dbBis;
        else
            $dbBisHeute = sprintf("%4d-%02d-%02d", $yearHeute,$monthHeute,$dayHeute);

        $fortSchritt = getArbTageBetweenDatums($dbVon,$dbBisHeute);

        // horni radek
        //persnr
        
        $obsah = "von: ".$von."  bis: ".$bis."   Arbeitstage: ".$arbTage."   Fort.: ".$fortSchritt;

        $sirkaDne = 5;
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10+25+7+7+4+7+$sirkaDne*12,$vyskaradku,$obsah,'TL',0,'L',$fill);

        $pismoDen = 5;
        $sirkaDovolene = 7;
        // jahranspruch
        $pdfobjekt->SetFont("FreeSans", "B", 4.5);
        $pdfobjekt->Cell(5*$sirkaDovolene,$vyskaradku,'','TL',0,'C',$fill);
        // transport
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'','TL',0,'R',$fill);
        // sumvorschuss
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'','T',0,'R',$fill);
        // sumessen
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'','T',0,'R',$fill);
        // leistung
        $pdfobjekt->SetFont("FreeSans", "B", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'','TL',0,'R',$fill);
        // leistpraem
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Leistungs','T',0,'R',$fill);
        // qualpraemie
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Qual.','T',0,'R',$fill);
        // erschwerniss
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'','T',0,'R',$fill);
        // sonstprem
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(15,$vyskaradku,'Sonst','T',0,'R',$fill);
        // leistung KC
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'','T',0,'R',$fill);
        // lohn
        $pdfobjekt->SetFont("FreeSans", "B", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'','T',0,'R',$fill);

        $pdfobjekt->Cell(0,$vyskaradku,"",'1',1,'R',$fill);


        // predposledni radek
        //persnr
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,"",'L',0,'R',$fill);

        //name
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(25,$vyskaradku,"",'',0,'L',$fill);
        //eintritt/austritt
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'eintritt','',0,'L',$fill);
        //dobaurcita / zkusebni_doba_dobaurcita
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'befristet','',0,'L',$fill);
        //schicht
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(4,$vyskaradku,'','',0,'R',$fill);
        //sumstunden
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'','',0,'R',$fill);
        $sirkaDne = 5;
        // a
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // d
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // n
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // np
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // nv
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // nw
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // nu
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // p
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // so
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // u
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // z
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        // frage
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'','',0,'R',$fill);
        $sirkaDovolene = 7;
        // jahranspruch
        $pdfobjekt->SetFont("FreeSans", "B", 4.5);
        $pdfobjekt->Cell(5*$sirkaDovolene,$vyskaradku,'Urlaub','LB',0,'C',$fill);
        // transport
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'','L',0,'R',$fill);
//        // sumvorschuss
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'','',0,'R',$fill);
//        // sumessen
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'','',0,'R',$fill);
//
//
//        // leistung
        $pdfobjekt->SetFont("FreeSans", "B", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Leistung','L',0,'R',$fill);
//
//        // leistpraem
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Praemie','',0,'R',$fill);
//
//        // qualpraemie
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Praemie','',0,'R',$fill);
//
//        // erschwerniss
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Ersch.','',0,'R',$fill);
//
//        // sonstprem
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(15,$vyskaradku,'Praemie','',0,'R',$fill);
//
//        // leistung KC
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Leist.','',0,'R',$fill);
//
//        // lohn
        $pdfobjekt->SetFont("FreeSans", "B", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Lohn','',0,'R',$fill);

          $pdfobjekt->Cell(0,$vyskaradku,"",'1',1,'R',$fill);

        // nejspodnejsi radek
        //persnr
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,"persnr",'LB',0,'R',$fill);

        //name
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(25,$vyskaradku,"Name Vorname",'B',0,'L',$fill);
//
//        //eintritt/austritt
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'austritt','B',0,'L',$fill);
//
//        //dobaurcita / zkusebni_doba_dobaurcita
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'Probezeit','B',0,'L',$fill);
//
//        //schicht
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(4,$vyskaradku,'','B',0,'R',$fill);
//
//        //sumstunden
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'Std.','B',0,'R',$fill);
//
//
        $sirkaDne = 5;
//        // a
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'a','B',0,'R',$fill);
//
//        // d
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'d','B',0,'R',$fill);
//
//        // n
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'n','B',0,'R',$fill);
//
//        // np
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'np','B',0,'R',$fill);
//
//        // nv
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'nv','B',0,'R',$fill);
//
//        // nw
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'nw','B',0,'R',$fill);
//
//        // nu
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'nu','B',0,'R',$fill);
//        // p
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'p','B',0,'R',$fill);
//        // so
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'So','B',0,'R',$fill);
//
//        // u
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'u','B',0,'R',$fill);
//
//        // z
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'z','B',0,'R',$fill);
//
//        // frage
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,'?','B',0,'R',$fill);
//
        $sirkaDovolene = 7;

//        // rest
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,'VJrst','LB',0,'R',$fill);
//        // jahranspruch
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,'JAns','B',0,'R',$fill);
//        // gekrzt
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,'kor','B',0,'R',$fill);
//        // genom
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,'gen','B',0,'R',$fill);
//        // offen
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,'offen','B',0,'R',$fill);
//
//
//        // transport
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'Trans','LB',0,'R',$fill);
//        // sumvorschuss
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'Vorsch','B',0,'R',$fill);
//        // sumessen
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(7,$vyskaradku,'Essen','B',0,'R',$fill);
//
//
//        // leistung
        $pdfobjekt->SetFont("FreeSans", "B", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'min.','LB',0,'R',$fill);
//
//        // leistpraem
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Kc','B',0,'R',$fill);
//
//        // qualpraemie
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Kc','B',0,'R',$fill);
//
//        // erschwerniss
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Kc','B',0,'R',$fill);
//
//        // sonstprem
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(15,$vyskaradku,'Kc','B',0,'R',$fill);
//
//        // leistung KC
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Kc','B',0,'R',$fill);
//
//        // lohn
        $pdfobjekt->SetFont("FreeSans", "B", 4.5);
        $pdfobjekt->Cell(10,$vyskaradku,'Kc','B',0,'R',$fill);

          $pdfobjekt->Cell(0,$vyskaradku,"",'1',1,'R',$fill);

}


/**
 * funkce ktera vrati hodnotu podle nodename
 * predam ji nodelist a jmeno node ktereho hodnotu hledam
 * @param <type> $nodelist
 * @param <type> $nodename
 * @return <type>
 */
function getValueForNode($nodelist,$nodename)
{
	$nodevalue="";
	foreach($nodelist as $node)
	{
		if($node->nodeName==$nodename)
		{
			$nodevalue=$node->nodeValue;
			return $nodevalue;
		}
	}
}


function getArbTageBetweenDatums($dbDatumVon,$dbDatumBis) {
    $sql = "select count(calendar.datum) as worktage from calendar where svatek=0 and datum between '$dbDatumVon' and '$dbDatumBis' and cislodne<>6 and cislodne<>7";
//    echo "$sql";
    dbConnect();
    $result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);
    return $row['worktage'];
}

function getATageCountBetweenDatums($dbDatumVon,$dbDatumBis,$persnr) {
    $sql = "select persnr,dtattypen.oestatus,datum";
    $sql.=" from dzeit ";
    $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
    $sql.=" where datum between '$dbDatumVon' and '$dbDatumBis' and persnr='$persnr' and dtattypen.oestatus='a'";
    $sql.=" group by persnr,dtattypen.oestatus,dzeit.datum";
    dbConnect();
    $res = mysql_query($sql);
    return mysql_num_rows($res);
}


function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$pole,$monat,$jahr){

        $fill = 0;
        $sirkaDne = 5;
        $sirkaDovolene = 7;
        $bis = sprintf("%02d.%02d.%04d",cal_days_in_month(CAL_GREGORIAN, $monat, $jahr),$monat,$jahr);
        //popisek sumy
        $pdfobjekt->SetFont("FreeSans", "", 6.5);
        $pdfobjekt->Cell(10+25+7+7+4,$vyskaradku,'Summe','LB',0,'L',$fill);

        // sumstunden
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $obsah = number_format($pole['sumstunden'], 0,',',' ');
        $pdfobjekt->Cell(7,$vyskaradku,$obsah,'B',0,'R',$fill);

        $pdfobjekt->Cell(12*$sirkaDne+5*$sirkaDovolene,$vyskaradku,'','LB',0,'L',$fill);

        // transport
        $pdfobjekt->SetFont("FreeSans", "", 4.5);

        $obsah = number_format($pole['transport'], 0,',',' ');
        $pdfobjekt->Cell(7,$vyskaradku,$obsah,'LB',0,'R',$fill);

        // sumvorschuss
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $obsah = number_format($pole['sumvorschuss'], 0,',',' ');
        $pdfobjekt->Cell(7,$vyskaradku,$obsah,'B',0,'R',$fill);
        // sumessen
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $obsah = number_format($pole['sumessen'], 0,',',' ');
        $pdfobjekt->Cell(7,$vyskaradku,$obsah,'B',0,'R',$fill);


        // leistung
        $pdfobjekt->SetFont("FreeSans", "B", 6);
        $obsah = number_format($pole['leistung'], 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'LB',0,'R',$fill);

        // leistpraem
        $pdfobjekt->SetFont("FreeSans", "", 6);
        $obsah = number_format($pole['leistpraem'], 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

        // qualpraemie
        $pdfobjekt->SetFont("FreeSans", "", 6);
        $obsah = number_format($pole['qualpraemie'], 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

        // erschwerniss
        $pdfobjekt->SetFont("FreeSans", "", 6);
        $obsah = number_format($pole['erschwerniss'], 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

        // sonstprem
        $pdfobjekt->SetFont("FreeSans", "", 6);
        $obsah = '';//number_format(0, 0);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

        // dummy
        $pdfobjekt->Cell(10+10,$vyskaradku,'','BR',0,'R',$fill);

        $pdfobjekt->Cell(0,$vyskaradku,"",'BR',1,'R',$fill);

        $pdfobjekt->Ln();

        $pdfobjekt->Cell(25,$vyskaradku,'Rest aus VJ (01.01.'.$jahr.')','TLB',0,'L',$fill);
        $obsah = number_format($pole['restold'], 1,',',' ');
        $pdfobjekt->Cell(25,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

//        $pdfobjekt->Cell(25,$vyskaradku,'Rest aktuell','TLB',0,'L',$fill);
//        $obsah = number_format($pole['rest'], 1,',',' ');
//        $pdfobjekt->Cell(25,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

        $pdfobjekt->Cell(25,$vyskaradku,'Anspruch '.$jahr,'TLB',0,'L',$fill);
        $obsah = number_format($pole['jahranspruch'], 1,',',' ');
        $pdfobjekt->Cell(25,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

        $pdfobjekt->Cell(25,$vyskaradku,'gekürzt','TLB',0,'L',$fill);
        $obsah = number_format($pole['gekrzt'], 1,',',' ');
        $pdfobjekt->Cell(25,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

        $pdfobjekt->Cell(25,$vyskaradku,'genommen (inkl. VJRest)','TLB',0,'L',$fill);
        $obsah = number_format($pole['genom'], 1,',',' ');
        $pdfobjekt->Cell(25,$vyskaradku,$obsah,'TLBR',0,'R',$fill);

        $obsah = number_format($pole['tage_d'], 1,',',' ');
        $pdfobjekt->Cell(30,$vyskaradku,'davon '.$obsah." in Monat ".$monat,'TLBR',1,'L',$fill);
        
//        $pdfobjekt->Cell(25,$vyskaradku,$obsah." in Monat ".$monat,'TBR',1,'R',$fill);

        $pdfobjekt->Cell(25,$vyskaradku,'offen ('.$bis.')','TLB',0,'L',$fill);
        $obsah = number_format($pole['offen'], 1,',',' ');
        $pdfobjekt->Cell(25,$vyskaradku,$obsah,'TLBR',1,'R',$fill);
}


/**
 *
 * @param TCPDF $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $childs
 */
function person_radek($pdfobjekt,$vyskaradku,$rgb,$childs,$monat,$jahr){

        $fill = 0;

        $pismoDen = 6;
        $pismoDovolena = 6;
        $pismoStunden = 6;

        $von = $jahr."-".$monat."-01";
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $bis = $jahr."-".$monat."-".$pocetDnuVMesici;
        $persnr = getValueForNode($childs, 'persnr');
        //persnr
        $pdfobjekt->SetFont("FreeSans", "", 6.5);
        $pdfobjekt->Cell(10,$vyskaradku,$persnr,'LB',0,'R',$fill);

        //name
        $pdfobjekt->SetFont("FreeSans", "", 6.5);
        $pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childs, 'name')." ".getValueForNode($childs, 'vorname'),'LB',0,'L',$fill);
        
        //eintritt/austritt
        $pdfobjekt->SetFont("FreeSans", "", 4);
        $pdfobjekt->MyMultiCell(7,$vyskaradku/2,getValueForNode($childs, 'eintritt')."\n".getValueForNode($childs, 'austritt'),'B','L',$fill);
        
        //dobaurcita / zkusebni_doba_dobaurcita
        $pdfobjekt->SetFont("FreeSans", "", 4);
        $pdfobjekt->MyMultiCell(7,$vyskaradku/2,getValueForNode($childs, 'dobaurcita')."\n".getValueForNode($childs, 'zkusebni_doba_dobaurcita'),'B','L',$fill);

        //schicht
        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell(4,$vyskaradku,getValueForNode($childs, 'schicht'),'B',0,'R',$fill);

        //sumstunden
        $pdfobjekt->SetFont("FreeSans", "", $pismoStunden);
        $obsah = number_format(getValueForNode($childs, 'sumstundena'), 1,',',' ');
        $pdfobjekt->Cell(7,$vyskaradku,$obsah,'LB',0,'R',$fill);

//<tage_frage>0</tage_frage>

        $sirkaDne = 5;
        // a
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $atageCount = getATageCountBetweenDatums($von, $bis, $persnr);

        $obsah = number_format($atageCount, 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'LB',0,'R',$fill);

        // d
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_d')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // n
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_n')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // np
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_np')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // nv
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_nv')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // nw
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_nw')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // nu
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_nu')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // p
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_p')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // So
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_so')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // u
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_u')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // z
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_z')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        // frage
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'tage_frage')), 0,',',' ');
        $pdfobjekt->Cell($sirkaDne,$vyskaradku,$obsah,'B',0,'R',$fill);

        $sirkaDovolene = 7;
        // rest
        $pdfobjekt->SetFont("FreeSans", "", $pismoDovolena);
        $obsah = number_format(floatval(getValueForNode($childs, 'restold')), 1,',',' ');
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,$obsah,'LB',0,'R',$fill);
        // jahranspruch
        $pdfobjekt->SetFont("FreeSans", "", $pismoDovolena);
        $obsah = number_format(floatval(getValueForNode($childs, 'jahranspruch')), 1,',',' ');
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,$obsah,'B',0,'R',$fill);
        // gekrzt
        $pdfobjekt->SetFont("FreeSans", "", $pismoDovolena);
        $obsah = number_format(floatval(getValueForNode($childs, 'gekrzt')), 1,',',' ');
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,$obsah,'B',0,'R',$fill);
        // genom
        $pdfobjekt->SetFont("FreeSans", "", $pismoDovolena);
        $obsah = number_format(floatval(getValueForNode($childs, 'genom')), 1,',',' ');
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,$obsah,'B',0,'R',$fill);
        // offen
        $pdfobjekt->SetFont("FreeSans", "", $pismoDovolena);
        $obsah = number_format(floatval(getValueForNode($childs, 'offen')), 1,',',' ');
        $pdfobjekt->Cell($sirkaDovolene,$vyskaradku,$obsah,'B',0,'R',$fill);


        // transport
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'transport')), 0,',',' ');
        $pdfobjekt->Cell(7,$vyskaradku,$obsah,'LB',0,'R',$fill);
        // sumvorschuss
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'sumvorschuss')), 0,',',' ');
        $pdfobjekt->Cell(7,$vyskaradku,$obsah,'B',0,'R',$fill);
        // sumessen
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'sumessen')), 0,',',' ');
        $pdfobjekt->Cell(7,$vyskaradku,$obsah,'B',0,'R',$fill);


        // leistung
        $pdfobjekt->SetFont("FreeSans", "B", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'leistung')), 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'LB',0,'R',$fill);

        // leistpraem
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'leistpraem')), 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

        // qualpraemie
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'qualpraemie')), 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

        // erschwerniss
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format(floatval(getValueForNode($childs, 'erschwerniss')), 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

        // sonstprem
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = '';//number_format(getValueForNode($childs, 'leistpraem'), 0);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

        $mzdaPodleSmen = getValueForNode($childs, 'mzda_podle_smen');
        $lohnKoef = floatval(getValueForNode($childs, 'lohnkoef'));
        $leistungKc = floatval(getValueForNode($childs, 'leistungkc'));
        $leistung = floatval(getValueForNode($childs, 'leistung'));
        $sumPraemien = floatval(getValueForNode($childs, 'leistpraem'))
                        + floatval(getValueForNode($childs, 'qualpraemie'))
                        + floatval(getValueForNode($childs, 'erschwerniss'));

        if($mzdaPodleSmen!=0){
            $leistKc = $leistungKc;
        }
        else
        {
            $leistKc = $leistung*$lohnKoef;
        }

        $lohn = $leistKc + $sumPraemien;

        // leistung KC
        $pdfobjekt->SetFont("FreeSans", "", $pismoDen);
        $obsah = number_format($leistKc, 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

        // lohn
        $pdfobjekt->SetFont("FreeSans", "B", $pismoDen);
        $obsah = number_format($lohn, 0,',',' ');
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

$pdfobjekt->Cell(0,$vyskaradku,"",'1',1,'R',$fill);
}

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vysradku
 * @param <type> $cellhead
 * @param <type> $jahr
 * @param <type> $monat
 */
function test_pageoverflow($pdfobjekt,$vysradku,$rgb,$monat,$jahr)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+3*$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$rgb,$vysradku,$monat,$jahr);
	}
}
				

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S140 PplanGesamt", $params);
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
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);


// prvni stranka
$pdf->AddPage();
pageheader($pdf,array(200,200,255),3,$monat,$jahr);

$personen = $domxml->getElementsByTagName('person');
foreach($personen as $person){
    $personChilds = $person->childNodes;
    foreach ($personChilds as $personnode){
        $sum_zapati_sestava_array[$personnode->nodeName] += $personnode->nodeValue;
    }
    test_pageoverflow($pdf, 3, array(200,200,255), $monat, $jahr);
    person_radek($pdf,4,array(255,255,255),$personChilds,$monat,$jahr);
}

zapati_sestava($pdf,4,array(255,255,255),$sum_zapati_sestava_array,$monat,$jahr);
//print_r($sum_zapati_sestava_array);

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>