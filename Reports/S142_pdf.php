<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';



$doc_title = "S142";
$doc_subject = "S142 Report";
$doc_keywords = "S142";

// necham si vygenerovat XML

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$reporttyp = $_GET['reporttyp'];
$a = AplDB::getInstance();

if ($reporttyp == 'infoVonBis') {
    $von = $a->make_DB_datum($_GET['von']);
    $bis = $a->make_DB_datum($_GET['bis']);
} else {
    $von = $jahr . "-" . $monat . "-01";
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;
}


$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S142",$password,$user,0);

if((!$fullAccess) && ($reporttyp=='lohn'))
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}


require_once('S142_xml.php');

//exit;
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");


foreach ($parameters as $param)
{
	$parametry=$param->childNodes;
	// v ramci parametru si prectu label a hodnotu
	foreach($parametry as $parametr)
	{
		$parametr=$parametr->childNodes;
		foreach($parametr as $par)
		{
			if($par->nodeName=="label")
				$label=$par->nodeValue;
			if($par->nodeName=="value")
				$value=$par->nodeValue;
		}
            if(strtolower($label)!="password")
            $params .= $label.": ".$value."  ";
	}
}

$headerCells = array(
    'persnr'=>array(
        'width'=>7.5,
        'text'=>'PersNr',
        'align'=>'',
    ),
    'name'=>array(
        'width'=>25,
        'text'=>'Name Vorname',
        'align'=>'',
     ),
    'stdlohn'=>array(
        'width'=>9,
        'text'=>'Std.Lohn\nL.Faktor\nOE',
        'align'=>'',
    ),
    'eintrittAustritt'=>array(
        'width'=>10,
        'text'=>'eintritt\naustritt',
        'align'=>'',
    ),
    'befristetProbezeit'=>array(
        'width'=>10,
        'text'=>'befristet\nProbezeit',
        'align'=>'',

    ),
    'a'=>array(
        'width'=>6,
        'text'=>'a',
        'align'=>'',

    ),
//    'aNAT'=>array(
//        'width'=>5,
//        'text'=>'a n AT',
//        'align'=>'',
//
//    ),
    'd'=>array(
        'width'=>5,
        'text'=>'d',
        'align'=>'',

    ),
    'n'=>array(
        'width'=>5,
        'text'=>'n',
        'align'=>'',

    ),
    'np'=>array(
        'width'=>4,
        'text'=>'np',
        'align'=>'',

    ),
    'nv'=>array(
        'width'=>4,
        'text'=>'nv',
        'align'=>'',

    ),
    'nw'=>array(
        'width'=>4,
        'text'=>'nw',
        'align'=>'',

    ),
    'nu'=>array(
        'width'=>4,
        'text'=>'nu',
        'align'=>'',

    ),
    'p'=>array(
        'width'=>4,
        'text'=>'p',
        'align'=>'',

    ),
    'u'=>array(
        'width'=>4,
        'text'=>'u',
        'align'=>'',

    ),
    'z'=>array(
        'width'=>4,
        'text'=>'z',
        'align'=>'',

    ),
    'frage'=>array(
        'width'=>4,
        'text'=>'?',
        'align'=>'',

    ),
    'mehrarbeit'=>array(
        'width'=>8,
        'text'=>'+-Std',
        'align'=>'',
     ),
    'nachtstd'=>array(
        'width'=>7,
        'text'=>'Nacht',
        'align'=>'',
    ),
    'sonestd'=>array(
        'width'=>7,
        'text'=>'Nacht',
        'align'=>'',
    ),
    'vjRst'=>array(
        'width'=>6,
        'text'=>'VJrst',
        'align'=>'',

    ),
    'jAnsp'=>array(
        'width'=>6.5,
        'text'=>'JAns',
        'align'=>'',

    ),
    'kor'=>array(
        'width'=>6,
        'text'=>'kor',
        'align'=>'',

    ),

    'genommen'=>array(
        'width'=>6,
        'text'=>'gen',
        'align'=>'',

    ),
    'offen'=>array(
        'width'=>6.5,
        'text'=>'offen',
        'align'=>'',

    ),
    'transport'=>array(
        'width'=>6,
        'text'=>'Trans\nCZK',
    ),
    'vorschuss'=>array(
        'width'=>7,
        'text'=>'Vorsch\nCZK',
        'align'=>'',

    ),
    'abmahnung'=>array(
        'width'=>7,
        'text'=>'Vorsch\nCZK',
        'align'=>'',

    ),
    'essen'=>array(
        'width'=>7,
        'text'=>'Essen\nCZK',
        'align'=>'',

    ),

    'exekution'=>array(
        'width'=>0,
        'text'=>'',
        'align'=>'',

    ),

    'dummy1'=>array(
        'width'=>8,
        'text'=>'',
        'align'=>'',

    ),
    'factoren'=>array(
        'width'=>7,
        'text'=>'',
        'align'=>'',

    ),
    'anwesenheit'=>array(
        'width'=>9,
        'text'=>'Anw\nStd',
        'align'=>'',

    ),
    'leistungmin'=>array(
        'width'=>10,
        'text'=>'Leistung\nmin',
        'align'=>'',

    ),
    'leistungkc'=>array(
        'width'=>10,
        'text'=>'Leistung\nCZK',
        'align'=>'',

    ),
    'qpraemie'=>array(
        'width'=>8,
        'text'=>'Qualitaet\nCZK',
        'align'=>'',

    ),
    'leistungpraemie'=>array(
        'width'=>8,
        'text'=>'Leistung\nCZK',
        'align'=>'',

    ),
    'quartalpraemie'=>array(
        'width'=>8,
        'text'=>'Quartal\nCZK',
        'align'=>'',

    ),
    'sonstpremie'=>array(
        'width'=>8,
        'text'=>'APrem.\nCZK',
        'align'=>'',

    ),
    'erschwerniss'=>array(
        'width'=>8,
        'text'=>'Erschw.\nCZK',
        'align'=>'',

    ),
    'lohn'=>array(
        'width'=>10,
        'text'=>'Lohn\nCZK',
        'align'=>'',

    ),
);

$aTageProMonat = 0;
$stundenDecimals = 1;

function showNoNull($hodnota){
    if($hodnota==0)
        return '';
    else
        return $hodnota;
}
/**
 * funkce pro vykresleni hlavicky na kazde strance
 * @param TCPDF $pdfobjekt
 * @param <type> $pole
 * @param <type> $headervyskaradku
 */
function pageheader($pdfobjekt,$rgb,$vyskaradku,$monat,$jahr) {

        global $headerCells;
        global $reporttyp;

        $fill = 1;
        $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
        $apl = new AplDB();

        if ($reporttyp == 'infoVonBis') {
        $von = $_GET['von'];
        $dbVon = $apl->make_DB_datum($von);
        $pocetDnu = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $bis = $_GET['bis'];
        $dbBis = $apl->make_DB_datum($bis);
    } else {
        $von = sprintf("%02d.%02d.%4d", 1, $monat, $jahr);
        $dbVon = sprintf("%4d-%02d-%02d", $jahr, $monat, 1);
        $pocetDnu = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $bis = sprintf("%02d.%02d.%4d", $pocetDnu, $monat, $jahr);
        $dbBis = sprintf("%4d-%02d-%02d", $jahr, $monat, $pocetDnu);
    }
    $arbTage = $apl->getArbTageBetweenDatums($dbVon, $dbBis);
        $dayHeute = date('d');
        $monthHeute = date('m');
        $yearHeute = date('Y');

        $stampHeute = mktime(1, 1, 1, $monthHeute, $dayHeute, $yearHeute);

        $vorjahr = $jahr;
        $vormonat = $monat -1;
        if($vormonat==0){
            $vormonat=12;
            $vorjahr--;
        }
        $pocetDnuVorMonat = cal_days_in_month(CAL_GREGORIAN, $vormonat, $vorjahr);

        $stampBis = strtotime($dbBis);

        if($stampHeute>$stampBis)
            $dbBisHeute = $dbBis;
        else
            $dbBisHeute = sprintf("%4d-%02d-%02d", $yearHeute,$monthHeute,$dayHeute);

        $fortSchritt = $apl->getArbTageBetweenDatums($dbVon,$dbBisHeute);

        $datumEndeMonat = sprintf("%02d%02d%02d",$jahr-2000,$monat,$pocetDnu);
        $datumEndeVorMonat = sprintf("%02d%02d%02d",$vorjahr-2000,$vormonat,$pocetDnuVorMonat);
	$datumEndeVorMonat = $monat.'/'.$jahr;//sprintf("%02d%02d%02d",$vorjahr-2000,$vormonat,$pocetDnuVorMonat);

        // horni radek
        //persnr

        $obsah = "von: ".$von."  bis: ".$bis."   Arbeitstage: ".$arbTage."   Fort.: ".$fortSchritt;

        $pdfobjekt->SetFont("FreeSans", "", 5);

        //prvni radek

        $pdfobjekt->Cell(
                            0
                            ,$vyskaradku
                            ,$obsah
                            ,'1'
                            ,1
                            ,'L'
                            ,$fill
                        );

        //druhy radek
        //persnr
        $pdfobjekt->Cell(
                            $headerCells['persnr']['width']
                            ,$vyskaradku
                            ,"PersNr"
                            ,'LTR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //name
        $pdfobjekt->Cell(
                            $headerCells['name']['width']
                            ,$vyskaradku
                            ,'Name Vorname'
                            ,'LTR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //stdlohn
        $pdfobjekt->Cell(
                            $headerCells['stdlohn']['width']
                            ,$vyskaradku
                            ,'Std.Lohn'
                            ,'LTR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //eintrittsdatum
        $pdfobjekt->Cell(
                            $headerCells['eintrittAustritt']['width']
                            ,$vyskaradku
                            ,'Eintritt'
                            ,'LTR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //befristetProbezeit
        $pdfobjekt->Cell(
                            $headerCells['befristetProbezeit']['width']
                            ,$vyskaradku
                            ,'VT bis'
                            ,'LTR'
                            ,0
                            ,'L'
                            ,$fill
                        );

        //a
        $pdfobjekt->Cell(
                            $headerCells['a']['width']
                            ,$vyskaradku
                            ,''
                            ,'TL'
                            ,0
                            ,'L'
                            ,$fill
                        );

        //    'aNAT'
//        $pdfobjekt->Cell(
//                            $headerCells['aNAT']['width']
//                            ,$vyskaradku
//                            ,''
//                            ,'T'
//                            ,0
//                            ,'L'
//                            ,$fill
//                        );
        //    'd'
        $pdfobjekt->Cell(
                            $headerCells['d']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //    'n'
        $pdfobjekt->Cell(
                            $headerCells['n']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //    'np'
        $pdfobjekt->Cell(
                            $headerCells['np']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //    'nv'
        $pdfobjekt->Cell(
                            $headerCells['nv']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //    'nw'
        $pdfobjekt->Cell(
                            $headerCells['nw']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //    'nu'
        $pdfobjekt->Cell(
                            $headerCells['nu']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //    'p'
        $pdfobjekt->Cell(
                            $headerCells['p']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //    'u'
        $pdfobjekt->Cell(
                            $headerCells['u']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //    'z'
        $pdfobjekt->Cell(
                            $headerCells['z']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //    'frage'
        $pdfobjekt->Cell(
                            $headerCells['frage']['width']
                            ,$vyskaradku
                            ,''
                            ,'TR'
                            ,0
                            ,'L'
                            ,$fill
                        );

        //    'nachtstd'
        $pdfobjekt->Cell(
                            $headerCells['nachtstd']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );

        //    'sonestd'
        $pdfobjekt->Cell(
                            $headerCells['sonestd']['width']
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'L'
                            ,$fill
                        );

        //    'mehrarbeit'
        $pdfobjekt->Cell(
                            $headerCells['mehrarbeit']['width']
                            ,$vyskaradku
                            ,$headerCells['mehrarbeit']['text']
                            ,'TLR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //dovolena
         $pdfobjekt->Cell(
                            $headerCells['vjRst']['width']-$headerCells['vjRst']['width']+0.1
                            ,$vyskaradku
                            ,''
                            ,'TL'
                            ,0
                            ,'C'
                            ,$fill
                        );
        $pdfobjekt->Cell(
                            $headerCells['jAnsp']['width']-$headerCells['jAnsp']['width']+0.1
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'C'
                            ,$fill
                        );
        $pdfobjekt->Cell(
                            +$headerCells['kor']['width']+$headerCells['vjRst']['width']+$headerCells['jAnsp']['width']+$headerCells['genommen']['width']+$headerCells['offen']['width']-4*0.1
                            ,$vyskaradku
                            ,'U r l a u b'
                            ,'T'
                            ,0
                            ,'C'
                            ,$fill
                        );
        $pdfobjekt->Cell(
                            $headerCells['genommen']['width']-$headerCells['genommen']['width']+0.1
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'C'
                            ,$fill
                        );

        $pdfobjekt->Cell(
                            $headerCells['offen']['width']-$headerCells['offen']['width']+0.1
                            ,$vyskaradku
                            ,''
                            ,'TR'
                            ,0
                            ,'C'
                            ,$fill
                        );

        //    'dummy1'
        $pdfobjekt->Cell(
                            $headerCells['dummy1']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        //    'leistungmin'
        $pdfobjekt->Cell(
                            $headerCells['leistungmin']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        //    'anwesenheit'
        $pdfobjekt->Cell(
                            $headerCells['anwesenheit']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );

        if($reporttyp=='lohn'){
        //    'factoren'
        $pdfobjekt->Cell(
                            $headerCells['factoren']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        }
        else{
        $pdfobjekt->Cell(
                            $headerCells['factoren']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,1
                            ,'C'
                            ,$fill
                        );
        }
        if($reporttyp=='lohn'){
        //    'leistungkc'
        $pdfobjekt->Cell(
                            $headerCells['leistungkc']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
//premie
        //    'qpraemie'
        $pdfobjekt->Cell(
                            $headerCells['qpraemie']['width']-$headerCells['qpraemie']['width']/2
                            ,$vyskaradku
                            ,''
                            ,'LT'
                            ,0
                            ,'C'
                            ,$fill
                        );

        //    'leistungpraemie'
        $pdfobjekt->Cell(
                            $headerCells['leistungpraemie']['width']+($headerCells['quartalpraemie']['width']/2+$headerCells['qpraemie']['width']/2)
                            ,$vyskaradku
                            ,'Prämien'
                            ,'T'
                            ,0
                            ,'C'
                            ,$fill
                        );

        //    'quartalpraemie'
        $pdfobjekt->Cell(
                            $headerCells['quartalpraemie']['width']-$headerCells['quartalpraemie']['width']/2
                            ,$vyskaradku
                            ,''
                            ,'T'
                            ,0
                            ,'C'
                            ,$fill
                        );
        //    'sonstpremie'
        $pdfobjekt->Cell(
                            $headerCells['sonstpremie']['width']
                            ,$vyskaradku
                            ,''
                            ,'TR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        //    'erschwerniss'
        $pdfobjekt->Cell(
                            $headerCells['erschwerniss']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        //    'lohn'
        $pdfobjekt->Cell(
                            $headerCells['lohn']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        // transport
        $pdfobjekt->Cell(
                            $headerCells['transport']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        // vorschuss
        $pdfobjekt->Cell(
                            $headerCells['vorschuss']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        // abmahnung
        $pdfobjekt->Cell(
                            $headerCells['abmahnung']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        // essen
        $pdfobjekt->Cell(
                            $headerCells['essen']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        // exekution
        $pdfobjekt->Cell(
                            $headerCells['exekution']['width']
                            ,$vyskaradku
                            ,''
                            ,'LTR'
                            ,1
                            ,'C'
                            ,$fill
                        );
        }
        //-------------------------------------------------------------------------------------------------------------------------
        //treti radek
        //persnr
        $pdfobjekt->Cell(
                            $headerCells['persnr']['width']
                            ,$vyskaradku
                            ,''
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //name
        $pdfobjekt->Cell(
                            $headerCells['name']['width']
                            ,$vyskaradku
                            ,''
                            ,'LR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //stdlohn
        $pdfobjekt->Cell(
                            $headerCells['stdlohn']['width']
                            ,$vyskaradku
                            ,'L.Faktor'
                            ,'LR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //eintrittsdatum
        $pdfobjekt->Cell(
                            $headerCells['eintrittAustritt']['width']
                            ,$vyskaradku
                            ,'Austritt'
                            ,'LR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //befristetProbezeit
        $pdfobjekt->Cell(
                            $headerCells['befristetProbezeit']['width']
                            ,$vyskaradku
                            ,'Probezeit'
                            ,'LR'
                            ,0
                            ,'L'
                            ,$fill
                        );

        //a
        $pdfobjekt->Cell(
                            $headerCells['a']['width']
                            ,$vyskaradku
                            ,'AT'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        //    'aNAT'
//        $pdfobjekt->Cell(
//                            $headerCells['aNAT']['width']
//                            ,$vyskaradku
//                            ,'anAT'
//                            ,'LR'
//                            ,0
//                            ,'R'
//                            ,$fill
//                        );
        //    'd'
        $pdfobjekt->Cell(
                            $headerCells['d']['width']
                            ,$vyskaradku
                            ,'d'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'n'
        $pdfobjekt->Cell(
                            $headerCells['n']['width']
                            ,$vyskaradku
                            ,'n'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'np'
        $pdfobjekt->Cell(
                            $headerCells['np']['width']
                            ,$vyskaradku
                            ,'np'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'nv'
        $pdfobjekt->Cell(
                            $headerCells['nv']['width']
                            ,$vyskaradku
                            ,'nv'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'nw'
        $pdfobjekt->Cell(
                            $headerCells['nw']['width']
                            ,$vyskaradku
                            ,'nw'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'nu'
        $pdfobjekt->Cell(
                            $headerCells['nu']['width']
                            ,$vyskaradku
                            ,'nu'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'p'
        $pdfobjekt->Cell(
                            $headerCells['p']['width']
                            ,$vyskaradku
                            ,'p'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'u'
        $pdfobjekt->Cell(
                            $headerCells['u']['width']
                            ,$vyskaradku
                            ,'u'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'z'
        $pdfobjekt->Cell(
                            $headerCells['z']['width']
                            ,$vyskaradku
                            ,'z'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'frage'
        $pdfobjekt->Cell(
                            $headerCells['frage']['width']
                            ,$vyskaradku
                            ,'?'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'nachtstd'
        $pdfobjekt->Cell(
                            $headerCells['nachtstd']['width']
                            ,$vyskaradku
                            ,'Nacht'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        //    'sonestd'
        $pdfobjekt->Cell(
                            $headerCells['sonestd']['width']
                            ,$vyskaradku
                            ,'Sa/So'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'mehrarbeit'
        $pdfobjekt->Cell(
                            $headerCells['mehrarbeit']['width']
                            ,$vyskaradku
                            ,$datumEndeVorMonat
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //dovolena
         $pdfobjekt->Cell(
                            $headerCells['vjRst']['width']
                            ,$vyskaradku
                            ,'VJrst'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        $pdfobjekt->Cell(
                            $headerCells['jAnsp']['width']
                            ,$vyskaradku
                            ,'JAns'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        $pdfobjekt->Cell(
                            $headerCells['kor']['width']
                            ,$vyskaradku
                            ,'kor'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        $pdfobjekt->Cell(
                            $headerCells['genommen']['width']
                            ,$vyskaradku
                            ,'gen'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        $pdfobjekt->Cell(
                            $headerCells['offen']['width']
                            ,$vyskaradku
                            ,'offen'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        //    'dummy1'
        $pdfobjekt->Cell(
                            $headerCells['dummy1']['width']
                            ,$vyskaradku
                            ,''
                            ,'LR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        //    'leistungmin'
        $pdfobjekt->Cell(
                            $headerCells['leistungmin']['width']
                            ,$vyskaradku
                            ,'VzAby'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'anwesenheit'
        $pdfobjekt->Cell(
                            $headerCells['anwesenheit']['width']
                            ,$vyskaradku
                            ,'Anw'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        if($reporttyp=='lohn'){
        //    'factoren'
        $pdfobjekt->Cell(
                            $headerCells['factoren']['width']
                            ,$vyskaradku
                            ,'VzAby'
                            ,'LR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        }
        else{
        $pdfobjekt->Cell(
                            $headerCells['factoren']['width']
                            ,$vyskaradku
                            ,'VzAby'
                            ,'LR'
                            ,1
                            ,'C'
                            ,$fill
                        );

        }
        if($reporttyp=='lohn'){
        //    'leistungkc'
        $pdfobjekt->Cell(
                            $headerCells['leistungkc']['width']
                            ,$vyskaradku
                            ,'VzAby'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
//premie
        //    'qpraemie'
        $pdfobjekt->Cell(
                            $headerCells['qpraemie']['width']
                            ,$vyskaradku
                            ,'Kvalif.'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        //    'leistungpraemie'
        $pdfobjekt->Cell(
                            $headerCells['leistungpraemie']['width']
                            ,$vyskaradku
                            ,'Leistung'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        //    'quartalpraemie'
        $pdfobjekt->Cell(
                            $headerCells['quartalpraemie']['width']
                            ,$vyskaradku
                            ,'Quartal'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    sonstpremie
        $pdfobjekt->Cell(
                            $headerCells['sonstpremie']['width']
                            ,$vyskaradku
                            ,'APrem.'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'erschwerniss'
        $pdfobjekt->Cell(
                            $headerCells['erschwerniss']['width']
                            ,$vyskaradku
                            ,'Adapt.'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'lohn'
        $pdfobjekt->Cell(
                            $headerCells['lohn']['width']
                            ,$vyskaradku
                            ,'Lohn'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // transport
        $pdfobjekt->Cell(
                            $headerCells['transport']['width']
                            ,$vyskaradku
                            ,'trans'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // vorschuss
        $pdfobjekt->Cell(
                            $headerCells['vorschuss']['width']
                            ,$vyskaradku
                            ,'vorsch'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // abmahnung
        $pdfobjekt->Cell(
                            $headerCells['abmahnung']['width']
                            ,$vyskaradku
                            ,'Abmah'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // essen
        $pdfobjekt->Cell(
                            $headerCells['essen']['width']
                            ,$vyskaradku
                            ,'essen'
                            ,'LR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // exekution
        $pdfobjekt->Cell(
                            $headerCells['exekution']['width']
                            ,$vyskaradku
                            ,'Exe'
                            ,'LR'
                            ,1
                            ,'R'
                            ,$fill
                        );
        }
        //-------------------------------------------------------------------------------------------------------------------------
        //ctvrty radek
        //persnr
        $pdfobjekt->Cell(
                            $headerCells['persnr']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //name
        $pdfobjekt->Cell(
                            $headerCells['name']['width']
                            ,$vyskaradku
                            ,'geb.Datum'
                            ,'BLR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //stdlohn
        $pdfobjekt->Cell(
                            $headerCells['stdlohn']['width']
                            ,$vyskaradku
                            ,'OE'
                            ,'LR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //eintrittsdatum
        $pdfobjekt->Cell(
                            $headerCells['eintrittAustritt']['width']
                            ,$vyskaradku
                            ,'DPP von'
                            ,'BLR'
                            ,0
                            ,'L'
                            ,$fill
                        );
        //befristetProbezeit
        $pdfobjekt->Cell(
                            $headerCells['befristetProbezeit']['width']
                            ,$vyskaradku
                            ,'DPP bis'
                            ,'BLR'
                            ,0
                            ,'L'
                            ,$fill
                        );

        //a
        $pdfobjekt->Cell(
                            $headerCells['a']['width']
                            ,$vyskaradku
                            ,'<>AT'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        //    'aNAT'
//        $pdfobjekt->Cell(
//                            $headerCells['aNAT']['width']
//                            ,$vyskaradku
//                            ,''
//                            ,'BLR'
//                            ,0
//                            ,'R'
//                            ,$fill
//                        );
        //    'd'
        $pdfobjekt->Cell(
                            $headerCells['d']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'n'
        $pdfobjekt->Cell(
                            $headerCells['n']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'np'
        $pdfobjekt->Cell(
                            $headerCells['np']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'nv'
        $pdfobjekt->Cell(
                            $headerCells['nv']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'nw'
        $pdfobjekt->Cell(
                            $headerCells['nw']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'nu'
        $pdfobjekt->Cell(
                            $headerCells['nu']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'p'
        $pdfobjekt->Cell(
                            $headerCells['p']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'u'
        $pdfobjekt->Cell(
                            $headerCells['u']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'z'
        $pdfobjekt->Cell(
                            $headerCells['z']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'frage'
        $pdfobjekt->Cell(
                            $headerCells['frage']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'nachtstd'
        $pdfobjekt->Cell(
                            $headerCells['nachtstd']['width']
                            ,$vyskaradku
                            ,'Std'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'sonestd'
        $pdfobjekt->Cell(
                            $headerCells['sonestd']['width']
                            ,$vyskaradku
                            ,'Std'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        //    'mehrarbeit'
        $pdfobjekt->Cell(
                            $headerCells['mehrarbeit']['width']
                            ,$vyskaradku
                            ,$datumEndeMonat
                            ,'LRB'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //dovolena
         $pdfobjekt->Cell(
                            $headerCells['vjRst']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        $pdfobjekt->Cell(
                            $headerCells['jAnsp']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        $pdfobjekt->Cell(
                            $headerCells['kor']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        $pdfobjekt->Cell(
                            $headerCells['genommen']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        $pdfobjekt->Cell(
                            $headerCells['offen']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'dummy1'
        $pdfobjekt->Cell(
                            $headerCells['dummy1']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,0
                            ,'C'
                            ,$fill
                        );
        //    'leistungmin'
        $pdfobjekt->Cell(
                            $headerCells['leistungmin']['width']
                            ,$vyskaradku
                            ,'min'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'anwesenheit'
        $pdfobjekt->Cell(
                            $headerCells['anwesenheit']['width']
                            ,$vyskaradku
                            ,'Std'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );


        //    'factoren'
        if($reporttyp=='lohn'){
        $pdfobjekt->Cell(
                            $headerCells['factoren']['width']
                            ,$vyskaradku
                            ,'Anw'
                            ,'LTRB'
                            ,0
                            ,'C'
                            ,$fill
                        );
        }
        else{
        $pdfobjekt->Cell(
                            $headerCells['factoren']['width']
                            ,$vyskaradku
                            ,'Anw'
                            ,'LTRB'
                            ,1
                            ,'C'
                            ,$fill
                        );

        }
        if($reporttyp=='lohn'){
        //    'leistungkc'
        $pdfobjekt->Cell(
                            $headerCells['leistungkc']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
//premie
        //    'qpraemie'
        $pdfobjekt->Cell(
                            $headerCells['qpraemie']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        //    'leistungpraemie'
        $pdfobjekt->Cell(
                            $headerCells['leistungpraemie']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );

        //    sonstpremie
        $pdfobjekt->Cell(
                            $headerCells['sonstpremie']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'quartalpraemie'
        $pdfobjekt->Cell(
                            $headerCells['quartalpraemie']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'erschwerniss'
        $pdfobjekt->Cell(
                            $headerCells['erschwerniss']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        //    'lohn'
        $pdfobjekt->Cell(
                            $headerCells['lohn']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // transport
        $pdfobjekt->Cell(
                            $headerCells['transport']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // vorschuss
        $pdfobjekt->Cell(
                            $headerCells['vorschuss']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // abmahnung
        $pdfobjekt->Cell(
                            $headerCells['abmahnung']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // essen
        $pdfobjekt->Cell(
                            $headerCells['essen']['width']
                            ,$vyskaradku
                            ,'CZK'
                            ,'BLR'
                            ,0
                            ,'R'
                            ,$fill
                        );
        // exekution
        $pdfobjekt->Cell(
                            $headerCells['exekution']['width']
                            ,$vyskaradku
                            ,''
                            ,'BLR'
                            ,1
                            ,'R'
                            ,$fill
                        );
        }
}



function test_pageoverflow($pdf,$vyskaradku,$rgb,$monat,$jahr)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdf->GetY()+$vyskaradku)>($pdf->getPageHeight()-$pdf->getBreakMargin()))
	{
		$pdf->AddPage();
                pageheader($pdf, $rgb, 4, $monat, $jahr);
	}
}

/**
 *
 * @param SimpleXMLElement $pole
 * @param string $og
 * @param string $datum
 * @param string $field 
 */
function getMinuten($pole,$og,$datum,$field){
    //najedu si na zvolene datum
    foreach($pole->person->tage->tag as $tag){
        $date = trim($tag->datum);
//        echo "<br>date=$date ";
        if(!strcmp($date, $datum)){
            // nasel jsem datum
//            echo "$date==$datum, pokacuju na hledani og ";
            foreach($tag->ogs->og as $ogcko){
                $ognr = trim($ogcko->ognr);
//                echo "ognr=$ognr ";
                if(!strcmp($ogcko->ognr,$og)){
//                    echo "$ognr==$og , vracim hodnotu";
                    return floatval($ogcko->{$field});
                }
            }
        }
    }
    return 0;
}

/**
 *
 * @param TCPDF $pdf
 * @param <type> $vyskaradku
 * @param <type> $beschreibung
 * @param <type> $datumyArray
 * @param <type> $summenArray
 * @param <type> $field
 * @param <type> $runden
 */
function echoGesamtSumme($pdf,$vyskaradku,$beschreibung,$datumyArray,$summenArray,$field,$decimals=0) {
    $sirkaBeschriftung = 40;
    $sirkaSumaRadku = 20;
    $pocetPriplatkovychDnu = 24;
    global $priplatekArray;
    global $priplatekBarva;
    
    $sirkaBunky = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$sirkaBeschriftung-$sirkaSumaRadku)/$pocetPriplatkovychDnu;
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    
    $pdf->Cell($sirkaBeschriftung,$vyskaradku, $beschreibung,'LRBT',0, 'L',0);
    $cislodne = 0;
    $sum = 0;
    foreach ($datumyArray as $datum) {
        $datum = trim($datum);
//        if($runden>0)
//            $value = round($summenArray[$datum][$field]);
//        else
//            $value = number_format($summenArray[$datum][$field],1);
        $value = number_format($summenArray[$datum][$field],$decimals,',',' ');

        $sum += $summenArray[$datum][$field];
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$cislodne]];
//        var_dump($rgbPriplatek);
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky, $vyskaradku, $value, 'LRBT', 0, 'R', 1);
        $cislodne++;
    }
    for($i=$cislodne;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }
//    for($i=0;$i<$pocetPriplatkovychDnu-$cislodne;$i++) $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    if($runden>0) $sum = round($sum);
    $sum = number_format($sum, $decimals,',',' ');
    // pro nektera pole nema suma pro radek smysl, proto je nezobrazim, napr. leistungsfaktor
    if(strcmp($field, 'leistungsfaktor'))
        $pdf->Cell(0, $vyskaradku, $sum, 'LRBT', 0, 'R', 0);
    
    $pdf->Ln();
}

/**
 *
 * @param TCPDF $pdf
 * @param <type> $beschreibung
 * @param <type> $hodnoty
 * @param <type> $datumyArray
 * @param <type> $og
 * @param <type> $field
 * @param int $decimals pocet desetinnych mist po zaokrouhleni
 */
function echoOGZeile($pdf,$vyskaradku,$beschreibung,$hodnoty,$datumyArray,$og,$field,$decimals=0) {
    $sirkaBeschriftung = 40;
    $sirkaSumaRadku = 20;
    $pocetPriplatkovychDnu = 24;
    global $priplatekBarva;
    global $priplatekArray;

    $sirkaBunky = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$sirkaBeschriftung-$sirkaSumaRadku)/$pocetPriplatkovychDnu;
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    $pdf->Cell($sirkaBeschriftung,$vyskaradku, $beschreibung,'LRBT',0, 'L',0);
    $cislodne = 0;
    $sum = 0;
    foreach($datumyArray as $datum) {
        $og = trim($og);
        $datum = trim($datum);
//        if($runden>0)
//            $value = round($hodnoty[$og][$datum][$field]);
//        else
            $value = number_format($hodnoty[$og][$datum][$field],$decimals,',',' ');

        $sum += $hodnoty[$og][$datum][$field];

//        var_dump($priplatekBarva);
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$cislodne]];
//        var_dump($rgbPriplatek);
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);

        $pdf->Cell($sirkaBunky, $vyskaradku, $value, 'LRBT', 0, 'R', 1);
        $cislodne++;
    }
    for($i=$cislodne;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }

//    for($i=0;$i<$pocetPriplatkovychDnu-$cislodne;$i++) $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
//    if($runden>0) $sum = round($sum);
    $sum = number_format($sum, $decimals,',',' ');

    $pdf->Cell(0, $vyskaradku, $sum, 'LRBT', 0, 'R', 0);
    $pdf->Ln();
}

/**
 *
 * @param TCPDF $pdf
 * @param array $datumArray
 * @param array $priplatekArray
 * @param array $rgb
 * @param int $vyskaradku 
 */
function pageheaderMesice($pdf,$datumArray,$priplatekArray,$rgb,$vyskaradku) {
    $sirkaBeschriftung = 40;
    $sirkaSumaRadku = 20;
    $pocetPriplatkovychDnu = 24;
    global $priplatekBarva;

    $sirkaBunky = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$sirkaBeschriftung-$sirkaSumaRadku)/$pocetPriplatkovychDnu;
    $pdf->SetFont("FreeSans", "B", 6);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    // bunka s popiskem, zde prazdna
    $pdf->Cell($sirkaBeschriftung, $vyskaradku,'Datum','LRT',0,'L',0);

    // datumy, datum je ve tvaru YYYYMMDD, zobrazim ve tvaru DD.MM.
    $den=0;
    foreach($datumArray as $datum) {
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$den]];
        $obsah = substr($datum, 6,2).".".substr($datum, 4,2).".";
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,$obsah,'LRBT',0,'R',1);
        //$pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        $den++;
    }
    //dorovnam na 24 dnu
    for($i=$den;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }
    $pdf->Ln();

    // cisla dnu + procentni sazba priplatku
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdf->Cell($sirkaBeschriftung, $vyskaradku,'priplatek [%]','LRB',0,'L',0);
    $den = 0;
    foreach($datumArray as $datum) {
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$den]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,$priplatekArray[$den]." %",'LRBT',0,'R',1);
        $den++;
    }
    //dorovnam na 24 dnu
    for($i=$den;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }


    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdf->Cell(0, $vyskaradku, 'Sum', 'LRBT', 0, 'R', 0);
    $pdf->Ln();
}

/**
 *
 * @param TCPDF $pdf
 * @param <type> $vyskaradku
 * @param <type> $hodnoty
 * @param <type> $datumyArray
 * @param <type> $og 
 */
function OGSummeCZK($pdf,$vyskaradku,$hodnoty,$datumyArray,$og){
    $sirkaBeschriftung = 40;
    $sirkaSumaRadku = 20;
    $pocetPriplatkovychDnu = 24;
    global $priplatekArray;
    global $priplatekBarva;
    $sirkaBunky = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$sirkaBeschriftung-$sirkaSumaRadku)/$pocetPriplatkovychDnu;
    $pdf->SetFont("FreeSans", "B", 7);
    $og = trim($og);
    $lohnfaktor = 0;
    // najdu si mezi vsemi hodnotami datumu lohnfaktor
    foreach($datumyArray as $datum){
        $datindex = trim($datum);
        if($lohnfaktor<$hodnoty[$og][$datindex]['lohnfaktor']) $lohnfaktor = $hodnoty[$og][$datindex]['lohnfaktor'];
    }
    $pdf->Cell($sirkaBeschriftung, $vyskaradku,$og." ( ".$lohnfaktor." ) CZK",'LRB',0,'L',0);

    //celkem vzaby kc
    $cislodne = 0;
    $sum = 0;
    foreach($datumyArray as $datum){
        $og = trim($og);
        $datum = trim($datum);
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$cislodne]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,round($hodnoty[$og][$datum]['celkemvzabykc']),'LRBT',0,'R',1);
        $sum += $hodnoty[$og][$datum]['celkemvzabykc'];
        $cislodne++;
    }
    for($i=$cislodne;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }
//    for($i=0;$i<$pocetPriplatkovychDnu-$cislodne;$i++) $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    $pdf->Cell(0, $vyskaradku, round($sum), 'LRBT', 0, 'R', 0);
    $pdf->Ln();
}

/**
 *
 * @param <type> $pdf
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $monat
 * @param <type> $jahr
 * @param <type> $summen 
 */
function radek_zapati($pdf, $vyskaradku,$rgb, $monat,$jahr,$summen){
        global $headerCells;
        global $summenZapati;
        global $stundenDecimals;
        global $reporttyp;
        $fontMaly = 4.5;
        $fontVetsi = 6;
        $fill=1;
        $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        //prvni radek
        $pdf->SetFont("FreeSans", "", $fontMaly);
        $pdf->Cell($headerCells['persnr']['width'],$vyskaradku, '','TL',0, 'R', $fill);
        $pdf->Cell($headerCells['name']['width'],$vyskaradku, '','T',0, 'R', $fill);
        $pdf->Cell($headerCells['stdlohn']['width'],$vyskaradku, '','T',0, 'R', $fill);
        $pdf->Cell($headerCells['eintrittAustritt']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['befristetProbezeit']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['a']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        //$pdf->Cell($headerCells['aNAT']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['d']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['n']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['np']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['nv']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['nw']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['nu']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['p']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['u']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['z']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['frage']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['nachtstd']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['sonestd']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['mehrarbeit']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['vjRst']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['jAnsp']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['kor']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['genommen']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['offen']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->SetFont("FreeSans", "", $fontVetsi);
        $pdf->Cell($headerCells['dummy1']['width'],$vyskaradku, 'Zeit','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['leistungmin']['width'],$vyskaradku, number_format($summenZapati['leistungmin_zeit'],0,',',' '),'TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['anwesenheit']['width'],$vyskaradku, number_format($summenZapati['anwesenheit_zeit'],$stundenDecimals,',',' '),'TLR',0, 'R', $fill);
        if($reporttyp=='lohn')
            $pdf->Cell($headerCells['factoren']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        else
            $pdf->Cell($headerCells['factoren']['width'],$vyskaradku, '','LR',1, 'R', $fill);
        if($reporttyp=='lohn'){
        $pdf->Cell($headerCells['leistungkc']['width'],$vyskaradku, number_format($summenZapati['leistungkc_zeit'],0,',',' '),'TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['qpraemie']['width'],$vyskaradku, number_format($summenZapati['qpraemie_zeit'],0,',',' '),'TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['leistungpraemie']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['quartalpraemie']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['sonstpremie']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['erschwerniss']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['lohn']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['transport']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['vorschuss']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['abmahnung']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['essen']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
        $pdf->Cell($headerCells['exekution']['width'],$vyskaradku, '','TLR',1, 'R', $fill);
        }
        //druhy radek
        $pdf->SetFont("FreeSans", "", $fontMaly);
        $pdf->Cell($headerCells['persnr']['width'],$vyskaradku, '','L',0, 'R', $fill);
        $pdf->Cell($headerCells['name']['width'],$vyskaradku, '','',0, 'L', $fill);
        $pdf->Cell($headerCells['stdlohn']['width'],$vyskaradku, '','',0, 'R', $fill);
        $pdf->Cell($headerCells['eintrittAustritt']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['befristetProbezeit']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['a']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        //$pdf->Cell($headerCells['aNAT']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['d']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['n']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['np']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['nv']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['nw']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['nu']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['p']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['u']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['z']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['frage']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['nachtstd']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['sonestd']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['mehrarbeit']['width'],$vyskaradku, number_format($summenZapati['mehrarbeit_vor'],1,',',' '),'LR',0, 'R', $fill);
        $pdf->Cell($headerCells['vjRst']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['jAnsp']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['kor']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['genommen']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['offen']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->SetFont("FreeSans", "", $fontVetsi);
        $pdf->Cell($headerCells['dummy1']['width'],$vyskaradku, 'Akkord','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['leistungmin']['width'],$vyskaradku, number_format($summenZapati['leistungmin_akkord'],0,',',' '),'LR',0, 'R', $fill);
        $pdf->Cell($headerCells['anwesenheit']['width'],$vyskaradku, number_format($summenZapati['anwesenheit_akkord'],$stundenDecimals,',',' '),'LR',0, 'R', $fill);
        if($reporttyp=='lohn')
            $pdf->Cell($headerCells['factoren']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        else
            $pdf->Cell($headerCells['factoren']['width'],$vyskaradku, '','LR',1, 'R', $fill);
        if($reporttyp=='lohn'){
        $pdf->Cell($headerCells['leistungkc']['width'],$vyskaradku, number_format($summenZapati['leistungkc_akkord'],0,',',' '),'LR',0, 'R', $fill);
        $pdf->Cell($headerCells['qpraemie']['width'],$vyskaradku, number_format($summenZapati['qpraemie_akkord'],0,',',' '),'LR',0, 'R', $fill);
        $pdf->Cell($headerCells['leistungpraemie']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['quartalpraemie']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['sonstpremie']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['erschwerniss']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['lohn']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['transport']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['vorschuss']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['abmahnung']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['essen']['width'],$vyskaradku, '','LR',0, 'R', $fill);
        $pdf->Cell($headerCells['exekution']['width'],$vyskaradku, '','LR',1, 'R', $fill);
        }


        //treti radek
        $pdf->SetFont("FreeSans", "", $fontMaly);
        $pdf->Cell($headerCells['persnr']['width'],$vyskaradku, '','LB',0, 'L', $fill);
        $pdf->Cell($headerCells['name']['width'],$vyskaradku, '','B',0, 'R', $fill);
        $pdf->Cell($headerCells['stdlohn']['width'],$vyskaradku, '','B',0, 'R', $fill);
        $pdf->Cell($headerCells['eintrittAustritt']['width'],$vyskaradku, '','LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['befristetProbezeit']['width'],$vyskaradku, '','LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['a']['width'],$vyskaradku, number_format($summenZapati['a'],0,',',' '),'LRB',0, 'R', $fill);
        //$pdf->Cell($headerCells['aNAT']['width'],$vyskaradku, number_format($summenZapati['aNAT'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['d']['width'],$vyskaradku, number_format($summenZapati['d'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['n']['width'],$vyskaradku, number_format($summenZapati['n'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['np']['width'],$vyskaradku, number_format($summenZapati['np'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['nv']['width'],$vyskaradku, number_format($summenZapati['nv'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['nw']['width'],$vyskaradku, number_format($summenZapati['nw'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['nu']['width'],$vyskaradku, number_format($summenZapati['nu'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['p']['width'],$vyskaradku, number_format($summenZapati['p'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['u']['width'],$vyskaradku, number_format($summenZapati['u'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['z']['width'],$vyskaradku, number_format($summenZapati['z'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['frage']['width'],$vyskaradku, number_format($summenZapati['frage'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['nachtstd']['width'],$vyskaradku, number_format($summenZapati['nachtstd'],1,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['sonestd']['width'],$vyskaradku, number_format($summenZapati['sonestd'],1,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['mehrarbeit']['width'],$vyskaradku, number_format($summenZapati['mehrarbeit_akt'],1,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['vjRst']['width'],$vyskaradku, number_format($summenZapati['vjRst'],1,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['jAnsp']['width'],$vyskaradku, number_format($summenZapati['jAnsp'],1,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['kor']['width'],$vyskaradku, number_format($summenZapati['kor'],1,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['genommen']['width'],$vyskaradku, number_format($summenZapati['genommen'],1,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['offen']['width'],$vyskaradku, number_format($summenZapati['offen'],1,',',' '),'LRB',0, 'R', $fill);
        $pdf->SetFont("FreeSans", "", $fontVetsi);
        $pdf->Cell($headerCells['dummy1']['width'],$vyskaradku, 'Sum','LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['leistungmin']['width'],$vyskaradku, number_format($summenZapati['leistungmin_gesamt'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['anwesenheit']['width'],$vyskaradku, number_format($summenZapati['anwesenheit_gesamt'],$stundenDecimals,',',' '),'LRB',0, 'R', $fill);
        if($reporttyp=='lohn')
            $pdf->Cell($headerCells['factoren']['width'],$vyskaradku,'','LRB',0, 'R', $fill);
        else
            $pdf->Cell($headerCells['factoren']['width'],$vyskaradku,'','LRB',1, 'R', $fill);
        if($reporttyp=='lohn'){
        $pdf->Cell($headerCells['leistungkc']['width'],$vyskaradku, number_format($summenZapati['leistungkc_gesamt'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['qpraemie']['width'],$vyskaradku, number_format($summenZapati['qpraemie_gesamt'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['leistungpraemie']['width'],$vyskaradku, number_format($summenZapati['leistungpraemie'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['quartalpraemie']['width'],$vyskaradku, number_format($summenZapati['quartalpraemie'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['sonstpremie']['width'],$vyskaradku, number_format($summenZapati['sonstpremie'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['erschwerniss']['width'],$vyskaradku, number_format($summenZapati['erschwerniss'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['lohn']['width'],$vyskaradku, number_format($summenZapati['lohn'],0,',',' '),'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['transport']['width'],$vyskaradku, $summenZapati['transport'],'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['vorschuss']['width'],$vyskaradku, $summenZapati['vorschuss'],'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['abmahnung']['width'],$vyskaradku, $summenZapati['abmahnung'],'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['essen']['width'],$vyskaradku, $summenZapati['essen'],'LRB',0, 'R', $fill);
        $pdf->Cell($headerCells['exekution']['width'],$vyskaradku, '','LRB',1, 'R', $fill);
        }
}

/**
 *
 * @param TCPDF $pdf
 * @param integer $vyskaradku
 * @param array $rgb
 * @param SimpleXMLElement $person
 * @param integer $monat
 * @param integer $jahr 
 */
function radek_person($pdf, $vyskaradku, $rgb, $person, $monat, $jahr,$persnr=0) {
    global $aTageProMonat;
    global $headerCells;
    global $summenZapati;
    global $stundenDecimals;
    global $reporttyp;
    global $lohnArray;
    
    $fill = 0;

    $a = AplDB::getInstance();

    if($reporttyp=='infoVonBis'){
        $von = $a->make_DB_datum($_GET['von']);
        $pocetDnu = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $bis = $a->make_DB_datum($_GET['bis']);
    }
    else{
        $von = $jahr . "-" . $monat . "-01";
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;
    }
    $vorjahr = $jahr;
    $vormonat = $monat - 1;
    if ($vormonat == 0) {
        $vormonat = 12;
        $vorjahr--;
    }
    $pocetDnuVorMonat = cal_days_in_month(CAL_GREGORIAN, $vormonat, $vorjahr);

    $datumEndeMonat = sprintf("%04d-%02d-%02d", $jahr, $monat, $pocetDnuVMesici);
    $datumEndeVorMonat = sprintf("%04d-%02d-%02d", $vorjahr, $vormonat, $pocetDnuVorMonat);

    $pdf->SetFont("FreeSans", "", 6);
    $eintritt = trim($person->eintritt);
    $persnr = trim($person->persnr);

    $aplDB = new AplDB();

    $persLohnFaktor = floatval(trim($person->perslohnfaktor));
    $leistFaktor = floatval(trim($person->leistfaktor));

    $faktorenInfo = sprintf("Std.Lohn: %.02f, Leistfaktor: %.02f", $persLohnFaktor * 60, $leistFaktor);

    $stundenA = floatval(trim($person->sumstundena));
    $stundenAkkord = floatval(trim($person->sumstundena_akkord));
    //$erschwerniss = intval(trim($person->risiko));

    $essen = intval(trim($person->essen));
    $exekution = intval(trim($person->exekution));
    $vorschuss = intval(trim($person->vorschuss));
    $transport = intval(trim($person->transport));
    //$sonstpremie = intval(trim($person->sonstpremie));
    $abmahnung = intval(trim($person->abmahnung));

    $dpp_von = trim($person->dpp_von);
    $dpp_bis = trim($person->dpp_bis);
    
    $hodCasove = $stundenA - $stundenAkkord;
    $hodUkolove = $stundenAkkord;

    $bErschwerniss = intval(trim($person->premie_za_prasnost)) != 0 ? TRUE : FALSE;
    $bleistungsPraemie = intval(trim($person->premie_za_vykon)) != 0 ? TRUE : FALSE;
    $bQPraemie_akkord = intval(trim($person->qpremie_akkord)) != 0 ? TRUE : FALSE;
    $bQPraemie_zeit = intval(trim($person->qpremie_zeit)) != 0 ? TRUE : FALSE;
    $bQTLPraemie = intval(trim($person->premie_za_3_mesice)) != 0 ? TRUE : FALSE;
    $bMAStunden = intval(trim($person->MAStunden)) != 0 ? TRUE : FALSE;

    //$erschwerniss = $bErschwerniss===TRUE?$erschwerniss:0;
    
    $d = intval(trim($person->tage_d));
    $p = intval(trim($person->tage_p));
    $z = intval(trim($person->tage_z));
    $nv = intval(trim($person->tage_nv));
    $nw = intval(trim($person->tage_nw));
    $nachtstd = round(floatval(trim($person->nachtstd)), 1);

    // pokud ma nejake z-tky, ovlivni to jeho narok na premii za kvalitu
    if ($z > 0) {
        $bQPraemie_akkord = FALSE;
        $bQPraemie_zeit = FALSE;
    }

    //$persnr = $person->persnr;

    
    $vonTimestamp = strtotime($von);
    $eintrittTimestamp = strtotime($eintritt);
    if ($eintrittTimestamp > $vonTimestamp)
        $arbTage = $aplDB->getArbTageBetweenDatums($eintritt, $bis);
    else
        $arbTage = $aplDB->getArbTageBetweenDatums($von, $bis);

    $anwTage = $aplDB->getATageProPersnrBetweenDatums($persnr, $von, $bis, 0);
    $anwTageArbeitsTage = $aplDB->getATageProPersnrBetweenDatums($persnr, $von, $bis, 1);

    $monatNormStunden = ($arbTage - $d - $nw) * 8;
    $monatNormMinuten = $monatNormStunden * 60;
    $ganzMonatNormMinuten = $aTageProMonat * 8 * 60;
    $ganzMonatNormStunden = $aTageProMonat * 8;

    $gesamtLohnZeitKc = 0;
    $gesamtLeistungZeit = 0;
    $gesamtLohnAkkordKc = 0;
    $gesamtVzAby = 0;
    $gesamtVzAbyAkkord = 0;
    $gesamtVzAbyZeit = 0;
    $gesamtQPraemie = 0;
    $gesamtQPraemie_akkord = 0;
    $gesamtQPraemie_zeit = 0;
    
    if(array_key_exists($persnr, $lohnArray['personen'])){
	$sonstpremie = floatval($lohnArray['personen'][$persnr]['aPremie']['apremie']);
    }
    else{
	$sonstpremie = 0;
    }
    
    $von = $jahr . "-" . $monat . "-01";
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;
    
    $bMzdaPodleAdaptace = FALSE;
    $bMzdaPodleAdaptace = $lohnArray['personen'][$persnr]['mzdaPodleAdaptace'];

    
    $gesamtLohnZeitKc = $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyZeitKc'];
    $gesamtLohnAkkordKc = $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyAkkordKc'];
    $gesamtVzAby = $lohnArray['personen'][$persnr]['monatlohn']['sumVzaby'];
    $gesamtVzAbyAkkord = $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyAkkord'];
    $gesamtVzAbyZeit = $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyZeit'];
    $gesamtQPraemie_akkord = $lohnArray['personen'][$persnr]['premieZaKvalifikaci']['akkord'];
    $gesamtQPraemie_zeit = $lohnArray['personen'][$persnr]['premieZaKvalifikaci']['zeit'];
    $gesamtQPraemie = $gesamtQPraemie_akkord + $gesamtQPraemie_zeit;
    
    // leistungspreamie

    $leistPraemieBerechnet = $lohnArray['personen'][$persnr]['leistungPremie']['leistungsPremieBetrag'];
    $leistPraemie = $bleistungsPraemie == true ? $leistPraemieBerechnet : 0;
    $anspruchLeistungspremie = $bleistungsPraemie == true ? 'ja' : 'nein';
    // QTL Praemie
    if(array_key_exists($persnr, $lohnArray['personen'])){
	$qtlPremieBetrag = $bQTLPraemie==TRUE?$lohnArray['personen'][$persnr]['qtlPremie']['qtlPremieBetrag']:0;
    }
    if ($reporttyp == 'lohn') {
	$qtlPraemie = $qtlPremieBetrag;
        $anspruchQTLPremie = $bQTLPraemie == true ? 'ja' : 'nein';
    }

    $gesamtQPraemie_zeit = $bQPraemie_zeit == true ? round($gesamtQPraemie_zeit) : 0;
    $gesamtQPraemie_akkord = $bQPraemie_akkord == true ? round($gesamtQPraemie_akkord) : 0;
    $anspruchQPremie_zeit = $bQPraemie_zeit == true ? 'ja' : 'nein';
    $anspruchQPremie_akkord = $bQPraemie_akkord == true ? 'ja' : 'nein';

    $hodCelkem = $hodCasove + $hodUkolove;
    $sumVzAby = $gesamtVzAbyZeit + $gesamtVzAbyAkkord;
    $sumLohn = $gesamtLohnZeitKc + $gesamtLohnAkkordKc;
    $sumQPraemie = $gesamtQPraemie_akkord + $gesamtQPraemie_zeit;
    if($z>0){
	$gesamtLohn = $sumLohn;// + $sumQPraemie + $erschwerniss + $leistPraemie + $qtlPraemie + $sonstpremie;
    }
    else{
	$gesamtLohn = $sumLohn + $sumQPraemie + $erschwerniss + $leistPraemie + $qtlPraemie + $sonstpremie;
    }
    

    $gesamtLohnAdapt = "";
    if($bMzdaPodleAdaptace){
	$erschwerniss = $lohnArray['personen'][$persnr]['adaptlohn']['summeLohn'];
	$adaptRest = $lohnArray['personen'][$persnr]['monatlohnRest']['sumVzabyAkkordKc']+$lohnArray['personen'][$persnr]['monatlohnRest']['sumVzabyZeitKc'];
	$gesamtLohn = $erschwerniss + $adaptRest;
	if($z>0){
	    $gesamtLohnAdapt = $sumLohn;// + $sumQPraemie + $leistPraemie + $qtlPraemie + $sonstpremie;
	}
	else{
	    $gesamtLohnAdapt = $sumLohn + $sumQPraemie + $leistPraemie + $qtlPraemie + $sonstpremie;
	}
	
    }
    
    $prozentPritomnostZFonduPracHodin = round($hodCelkem / $ganzMonatNormStunden, 2) * 100;

//    if ($prozentPritomnostZFonduPracHodin < 50)
//        $gesamtLohn -= $sumQPraemie;
    
    if ($hodUkolove != 0)
        $factorAkkord = $gesamtVzAbyAkkord / ($hodUkolove * 60);
    else
        $factorAkkord = 0;

    if ($hodCasove != 0)
        $factorZeit = $gesamtVzAbyZeit / ($hodCasove * 60);
    else
        $factorZeit = 0;

    if (($hodCelkem != 0))
        $factorGesamt = $sumVzAby / (($hodUkolove + $hodCasove) * 60);
    else
        $factorGesamt = 0;

    //$maStundenDatumAb = '000000';
    $prosinecDnu = cal_days_in_month(CAL_GREGORIAN, 12, $jahr-1);
    $maStundenDatumAb = substr($jahr-1, 2,2)."12".$prosinecDnu;
    
    if ($reporttyp == 'lohn') {
        if ($bMAStunden) {
	    $stddiff = $aplDB->getStdDiff($monat, $jahr, $persnr);
	    if($stddiff===NULL) {
		$startStd = 0;
	    }
	    else{
		$startStd = floatval ($stddiff['stunden']);
		$maStundenDatumAb = $stddiff['datum'];
	    }
		
            $mehrarb = $aplDB->getPlusMinusStunden($monat, $jahr, $persnr);
            $vorjahr = $jahr;
            $vormonat = $monat - 1;
            if ($vormonat == 0) {
                $vormonat = 12;
                $vorjahr--;
            }
            $mehrarbVor = $aplDB->getPlusMinusStunden($vormonat, $vorjahr, $persnr);
        } else {
            $mehrarb = 0;
            $mehrarbVor = 0;
        }
    }

    $aPremieFlag = $lohnArray['personen'][$persnr]['aPremie']['apremie_flag'];

//    if(array_key_exists(intval($person->persnr), $aPremienArray)){
//	    $aPremieFlag = $aPremienArray[intval($person->persnr)]['apremie_flag'];
//	}
//	else{
//	    $aPremieFlag = '';
//	}
    //popisek do hlavicky
    $headerCells['mehrarbeit']['text'] = $maStundenDatumAb;
    //prescas. hodiny pocitam vztazene ke startStd, napr. k 31.12.2013
    $endOfVorYearStd = $aplDB->getMAStundenDatum(date('Y-m-d',mktime(1, 1, 1, 12, 31, $jahr-1)),$persnr);
    //prescasove hodiny ve ve vybranem mesici
    $maStdMonat = $mehrarb-$mehrarbVor;
    
    //2016-02-05 spravne by melo byt
    //$mehrarb-=$endOfVorYearStd;
    
    
    $mehrarb-=$startStd;
    $mehrarbVor-=$startStd;
    //prvni radek
    $pdf->Cell($headerCells['persnr']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['name']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);

    // v pripade ze mi pro cloveka vyjde normovany pocet minut nula podbarvim datum nastupu
    // protoze datumem nastupu vetsim nez datum bis si zajistim jeho nulovou hodnotu.

    if ($monatNormMinuten <= 0) {
        $pdf->SetFillColor(255, 200, 200, 1);
        $fill = 1;
    } else {
        $fill = 0;
    }

    if ($reporttyp == 'lohn')
        $pdf->Cell($headerCells['stdlohn']['width'], $vyskaradku, number_format($persLohnFaktor * 60, 2), 'TLR', 0, 'R', $fill);
    else
        $pdf->Cell($headerCells['stdlohn']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);

    $pdf->Cell($headerCells['eintrittAustritt']['width'], $vyskaradku, $eintritt, 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['befristetProbezeit']['width'], $vyskaradku, $person->dobaurcita, 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['a']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    //$pdf->Cell($headerCells['aNAT']['width'],$vyskaradku, '','TLR',0, 'R', $fill);
    $pdf->Cell($headerCells['d']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['n']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['np']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['nv']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['nw']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['nu']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['p']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['u']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['z']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['frage']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['nachtstd']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['sonestd']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    
    // radek s pocatecni hodnotou prescasovych hodin
    if ($reporttyp == 'lohn')
        $pdf->Cell($headerCells['mehrarbeit']['width'], $vyskaradku, number_format($endOfVorYearStd, 1, ',', ' '), 'TLR', 0, 'R', $fill);
    else
        $pdf->Cell($headerCells['mehrarbeit']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);

//    $pdf->Cell($headerCells['mehrarbeit']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['vjRst']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['jAnsp']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['kor']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['genommen']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['offen']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['dummy1']['width'], $vyskaradku, 'Zeit', 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['leistungmin']['width'], $vyskaradku, number_format($gesamtVzAbyZeit, 0, ',', ' '), 'TLR', 0, 'R', $fill);
    $pdf->Cell($headerCells['anwesenheit']['width'], $vyskaradku, number_format($hodCasove, $stundenDecimals, ',', ' '), 'TLR', 0, 'R', $fill);
    if ($reporttyp == 'lohn')
        $pdf->Cell($headerCells['factoren']['width'], $vyskaradku, number_format($factorZeit, 2, ',', ' '), 'LR', 0, 'R', $fill);
    else
        $pdf->Cell($headerCells['factoren']['width'], $vyskaradku, number_format($factorZeit, 2, ',', ' '), 'LR', 1, 'R', $fill);
    if ($reporttyp == 'lohn') {
        $pdf->Cell($headerCells['leistungkc']['width'], $vyskaradku, number_format($gesamtLohnZeitKc, 0, ',', ' '), 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['qpraemie']['width'], $vyskaradku, number_format($gesamtQPraemie_zeit, 0, ',', ' '), 'TLR', 0, 'R', $fill);
        $fill = 0;
        $pdf->Cell($headerCells['leistungpraemie']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['quartalpraemie']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['sonstpremie']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['erschwerniss']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['lohn']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['transport']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['vorschuss']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['abmahnung']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['essen']['width'], $vyskaradku, '', 'TLR', 0, 'R', $fill);
        $pdf->Cell($headerCells['exekution']['width'], $vyskaradku, '', 'TLR', 1, 'R', $fill);
    }

    //druhy radek
    $pdf->Cell($headerCells['persnr']['width'], $vyskaradku, $persnr, 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['name']['width'], $vyskaradku, $person->vollname, 'LR', 0, 'L', $fill);
    if ($reporttyp == 'lohn')
        $pdf->Cell($headerCells['stdlohn']['width'], $vyskaradku, number_format($leistFaktor, 2), 'LR', 0, 'R', $fill);
    else
        $pdf->Cell($headerCells['stdlohn']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);

    $pdf->Cell($headerCells['eintrittAustritt']['width'], $vyskaradku, $person->austritt, 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['befristetProbezeit']['width'], $vyskaradku, $person->zkusebni_doba_dobaurcita, 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['a']['width'], $vyskaradku, $anwTageArbeitsTage, 'LR', 0, 'R', $fill);
    //$pdf->Cell($headerCells['aNAT']['width'],$vyskaradku, '','LR',0, 'R', $fill);
    $pdf->Cell($headerCells['d']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['n']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['np']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['nv']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['nw']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['nu']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['p']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['u']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['z']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['frage']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['nachtstd']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['sonestd']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    if ($reporttyp == 'lohn')
        $pdf->Cell($headerCells['mehrarbeit']['width'], $vyskaradku, number_format($maStdMonat, 1, ',', ' '), 'LR', 0, 'R', $fill);
    else
        $pdf->Cell($headerCells['mehrarbeit']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['vjRst']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['jAnsp']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['kor']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['genommen']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['offen']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['dummy1']['width'], $vyskaradku, 'Akkord', 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['leistungmin']['width'], $vyskaradku, number_format($gesamtVzAbyAkkord, 0, ',', ' '), 'LR', 0, 'R', $fill);
    $pdf->Cell($headerCells['anwesenheit']['width'], $vyskaradku, number_format($hodUkolove, $stundenDecimals, ',', ' '), 'LR', 0, 'R', $fill);
    if ($reporttyp == 'lohn')
        $pdf->Cell($headerCells['factoren']['width'], $vyskaradku, number_format($factorAkkord, 2, ',', ' '), 'LR', 0, 'R', $fill);
    else
        $pdf->Cell($headerCells['factoren']['width'], $vyskaradku, number_format($factorAkkord, 2, ',', ' '), 'LR', 1, 'R', $fill);
    if ($reporttyp == 'lohn') {
        $pdf->Cell($headerCells['leistungkc']['width'], $vyskaradku, number_format($gesamtLohnAkkordKc, 0, ',', ' '), 'LR', 0, 'R', $fill);
        $pdf->Cell($headerCells['qpraemie']['width'], $vyskaradku, number_format($gesamtQPraemie_akkord, 0, ',', ' '), 'LR', 0, 'R', $fill);
        $fill = 0;
        $pdf->Cell($headerCells['leistungpraemie']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
        $pdf->Cell($headerCells['quartalpraemie']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
	
//	if(array_key_exists(intval($person->persnr), $aPremienArray)){
//	    $sonstpremie = $aPremienArray[intval($person->persnr)]['apremie_flag'];
//	}
//	else{
//	    $sonstpremie = '';
//	}
	if($aPremieFlag=='!'){
	    $pdf->SetFillColor(255,255,230);
	    $fill = 1;
	}
	else{
	    $pdf->SetFillColor(255,255,255);
	    $fill = 0;
	}
        $pdf->Cell($headerCells['sonstpremie']['width'], $vyskaradku, $aPremieFlag, 'LR', 0, 'C', $fill);
	$fill = 0;
	
	$pdf->Cell($headerCells['erschwerniss']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
        
	if($bMzdaPodleAdaptace===TRUE){
	    $pdf->SetFillColor(240,255,230);
	    $fill = 1;
	    $pdf->Cell($headerCells['lohn']['width'], $vyskaradku, ''.  number_format($gesamtLohnAdapt,0,',',' ').'', 'LR', 0, 'R', $fill);
	    $pdf->SetFillColor(255,255,255);
	    $fill = 0;
	}
	else{
	    $pdf->SetFillColor(255,255,255);
	    $fill = 0;
	    $pdf->Cell($headerCells['lohn']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
	}
        
        $pdf->Cell($headerCells['transport']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
        $pdf->Cell($headerCells['vorschuss']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
        $pdf->Cell($headerCells['abmahnung']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
        $pdf->Cell($headerCells['essen']['width'], $vyskaradku, '', 'LR', 0, 'R', $fill);
        $pdf->Cell($headerCells['exekution']['width'], $vyskaradku, '', 'LR', 1, 'R', $fill);
    }


    //treti radek
    $pdf->Cell($headerCells['persnr']['width'], $vyskaradku, '', 'LB', 0, 'L', $fill);
    $pdf->Cell($headerCells['name']['width'], $vyskaradku, $person->geboren, 'LRB', 0, 'L', $fill);
    $pdf->Cell($headerCells['stdlohn']['width'], $vyskaradku, $person->regeloe, 'LR', 0, 'R', $fill);

    if(strlen(trim($dpp_von))>0){
	$pdf->SetFillColor(200, 255, 200, 1);
        $fill = 1;
    }
    else{
	$fill = 0;
    }

    $pdf->Cell($headerCells['eintrittAustritt']['width'], $vyskaradku, $dpp_von, 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['befristetProbezeit']['width'], $vyskaradku, $dpp_bis, 'LRB', 0, 'R', $fill);
    
    $fill=0;
    $pdf->Cell($headerCells['a']['width'], $vyskaradku, $anwTage - $anwTageArbeitsTage, 'LRB', 0, 'R', $fill);
//        $pdf->Cell($headerCells['aNAT']['width'],$vyskaradku, number_format($anwTage-$anwTageArbeitsTage,0,',',' '),'LRB',0, 'R', $fill);
    $pdf->Cell($headerCells['d']['width'], $vyskaradku, showNoNull($person->tage_d), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['n']['width'], $vyskaradku, showNoNull($person->tage_n), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['np']['width'], $vyskaradku, showNoNull($person->tage_np), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['nv']['width'], $vyskaradku, showNoNull($person->tage_nv), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['nw']['width'], $vyskaradku, showNoNull($person->tage_nw), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['nu']['width'], $vyskaradku, showNoNull($person->tage_nu), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['p']['width'], $vyskaradku, showNoNull($person->tage_p), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['u']['width'], $vyskaradku, showNoNull($person->tage_u), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['z']['width'], $vyskaradku, showNoNull($person->tage_z), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['frage']['width'], $vyskaradku, showNoNull($person->tage_frage), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['nachtstd']['width'], $vyskaradku, number_format(floatval($person->nachtstd), 1, ',', ' '), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['sonestd']['width'], $vyskaradku, number_format(floatval($person->sonestd), 1, ',', ' '), 'LRB', 0, 'R', $fill);
    if ($reporttyp == 'lohn')
        $pdf->Cell($headerCells['mehrarbeit']['width'], $vyskaradku, number_format($mehrarb, 1, ',', ' '), 'LRB', 0, 'R', $fill);
    else
        $pdf->Cell($headerCells['mehrarbeit']['width'], $vyskaradku, '', 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['vjRst']['width'], $vyskaradku, number_format(floatval(trim($person->rest)), 1, ',', ' '), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['jAnsp']['width'], $vyskaradku, number_format(floatval(trim($person->jahranspruch)), 1, ',', ' '), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['kor']['width'], $vyskaradku, number_format(floatval(trim($person->gekrzt)), 1, ',', ' '), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['genommen']['width'], $vyskaradku, number_format(floatval(trim($person->genom)), 1, ',', ' '), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['offen']['width'], $vyskaradku, number_format(floatval(trim($person->offen)), 1, ',', ' '), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['dummy1']['width'], $vyskaradku, 'Sum', 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['leistungmin']['width'], $vyskaradku, number_format($sumVzAby, 0, ',', ' '), 'LRB', 0, 'R', $fill);
    $pdf->Cell($headerCells['anwesenheit']['width'], $vyskaradku, number_format($hodCelkem, $stundenDecimals, ',', ' '), 'LRB', 0, 'R', $fill);
    if ($reporttyp == 'lohn')
        $pdf->Cell($headerCells['factoren']['width'], $vyskaradku, number_format($factorGesamt, 2, ',', ' '), 'LRB', 0, 'R', $fill);
    else
        $pdf->Cell($headerCells['factoren']['width'], $vyskaradku, number_format($factorGesamt, 2, ',', ' '), 'LRB', 1, 'R', $fill);
    if ($reporttyp == 'lohn') {
        $pdf->Cell($headerCells['leistungkc']['width'], $vyskaradku, number_format($sumLohn, 0, ',', ' '), 'LRB', 0, 'R', $fill);
        if ($prozentPritomnostZFonduPracHodin < 50) {
            $pdf->SetFillColor(255, 200, 200, 1);
            $fill = 1;
        } else {
            $fill = 0;
        }
        $pdf->Cell($headerCells['qpraemie']['width'], $vyskaradku, number_format($sumQPraemie, 0, ',', ' '), 'LRB', 0, 'R', $fill);
        $fill = 0;
        $pdf->Cell($headerCells['leistungpraemie']['width'], $vyskaradku, number_format($leistPraemie, 0, ',', ' '), 'LRB', 0, 'R', $fill);
        $pdf->Cell($headerCells['quartalpraemie']['width'], $vyskaradku, number_format($qtlPraemie, 0, ',', ' '), 'LRB', 0, 'R', $fill);
	
	//AplDB::varDump(intval($person->persnr));
	/*
	if(array_key_exists(intval($person->persnr), $aPremienArray)){
	    $sonstpremie = $aPremienArray[intval($person->persnr)]['apremie'];
	}
	else{
	    $sonstpremie = 0;
	}
	*/
	if($aPremieFlag=='!'){
	    $pdf->SetFillColor(255,255,230);
	    $fill = 1;
	}
	else{
	    $pdf->SetFillColor(255,255,255);
	    $fill = 0;
	}
        $pdf->Cell($headerCells['sonstpremie']['width'], $vyskaradku, number_format($sonstpremie, 0, ',', ' '), 'LRB', 0, 'R', $fill);
	$fill = 0;
	
	if($adaptRest!=0){
	    $pdf->SetFillColor(255,230,230);
	    $fill = 1;
	}
	else{
	    $pdf->SetFillColor(255,255,255);
	    $fill = 0;
	}
        $pdf->Cell($headerCells['erschwerniss']['width'], $vyskaradku, number_format($erschwerniss, 0, ',', ' '), 'LRB', 0, 'R', $fill);
        $pdf->Cell($headerCells['lohn']['width'], $vyskaradku, number_format($gesamtLohn, 0, ',', ' '), 'LRB', 0, 'R', $fill);
	$pdf->SetFillColor(255,255,255);
	$fill = 0;
        $pdf->Cell($headerCells['transport']['width'], $vyskaradku, $transport, 'LRB', 0, 'R', $fill);
        $pdf->Cell($headerCells['vorschuss']['width'], $vyskaradku, $vorschuss, 'LRB', 0, 'R', $fill);
        $pdf->Cell($headerCells['abmahnung']['width'], $vyskaradku, $abmahnung, 'LRB', 0, 'R', $fill);
        $pdf->Cell($headerCells['essen']['width'], $vyskaradku, $essen, 'LRB', 0, 'R', $fill);
        $pdf->Cell($headerCells['exekution']['width'], $vyskaradku, $exekution, 'LRB', 1, 'R', $fill);
    }
    //sumy pro zapati
    $summenZapati['a']+=$anwTage;
    $summenZapati['aNAT']+= ( $anwTage - $anwTageArbeitsTage);
    $summenZapati['d']+=intval(trim($person->tage_d));
    $summenZapati['n']+=intval(trim($person->tage_n));
    $summenZapati['np']+=intval(trim($person->tage_np));
    $summenZapati['nv']+=intval(trim($person->tage_nv));
    $summenZapati['nw']+=intval(trim($person->tage_nw));
    $summenZapati['nu']+=intval(trim($person->tage_nu));
    $summenZapati['p']+=intval(trim($person->tage_p));
    $summenZapati['u']+=intval(trim($person->tage_u));
    $summenZapati['z']+=intval(trim($person->tage_z));
    $summenZapati['frage']+=intval(trim($person->tage_frage));
    $summenZapati['nachtstd']+=floatval(trim($person->nachtstd));
    $summenZapati['sonestd']+=floatval(trim($person->sonestd));
    $summenZapati['mehrarbeit_vor']+=$maStdMonat;
    $summenZapati['mehrarbeit_akt']+=$mehrarb;
    $summenZapati['vjRst']+=floatval(trim($person->rest));
    $summenZapati['jAnsp']+=floatval(trim($person->jahranspruch));
    $summenZapati['kor']+=floatval(trim($person->gekrzt));
    $summenZapati['genommen']+=floatval(trim($person->genom));
    $summenZapati['offen']+=floatval(trim($person->offen));
    $summenZapati['transport']+=$transport;
    $summenZapati['vorschuss']+=$vorschuss;
    $summenZapati['abmahnung']+=$abmahnung;
    $summenZapati['essen']+=$essen;
    $summenZapati['anwesenheit_zeit']+=$hodCasove;
    $summenZapati['anwesenheit_akkord']+=$hodUkolove;
    $summenZapati['anwesenheit_gesamt']+=$hodCelkem;
    $summenZapati['leistungmin_zeit']+=$gesamtVzAbyZeit;
    $summenZapati['leistungmin_akkord']+=$gesamtVzAbyAkkord;
    $summenZapati['leistungmin_gesamt']+=$sumVzAby;
    $summenZapati['leistungkc_zeit']+=$gesamtLohnZeitKc;
    $summenZapati['leistungkc_akkord']+=$gesamtLohnAkkordKc;
    $summenZapati['leistungkc_gesamt']+=$sumLohn;

    $summenZapati['qpraemie_zeit']+=$gesamtQPraemie_zeit;
    $summenZapati['qpraemie_akkord']+=$gesamtQPraemie_akkord;
    $summenZapati['qpraemie_gesamt']+=$sumQPraemie;

    $summenZapati['leistungpraemie']+=$leistPraemie;
    $summenZapati['quartalpraemie']+=$qtlPraemie;
    $summenZapati['sonstpremie']+=$sonstpremie;
    $summenZapati['erschwerniss']+=$erschwerniss;
    $summenZapati['lohn']+=$gesamtLohn;
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

if($reporttyp=='lohn')
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S142 Abrechnung Mitarbeiter", $params);
else
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S142 Mitarbeiter Monatsleistungen/Anwesenheit", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-11, PDF_MARGIN_TOP-5, PDF_MARGIN_RIGHT-11);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setHeaderMargin(10);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

$pdf->AddPage();
pageheader($pdf, array(230,230,230), 4, $monat, $jahr);

$summenZapati = array();
$personen = simplexml_import_dom($domxml);

$apl = new AplDB();
$aTageProMonat = $apl->getArbTageBetweenDatums($von, $bis);
//$aPremienArray = $apl->getPersnrApremieArray($monat, $jahr, $persvon, $persbis, '*',FALSE);
$lohnArray = $apl->getLohnArray($persvon, $persbis, $jahr, $monat);
//AplDB::varDump($lohnArray);

foreach ($personen as $klic => $person) {
    if ($klic == 'person') {
	$persnr = $person->persnr;
        test_pageoverflow($pdf, 4 * 3, array(230, 230, 230), $monat, $jahr);
        radek_person($pdf, 4, array(255, 255, 255), $person, $monat, $jahr,$persnr);
    }
}

test_pageoverflow($pdf, 4 * 3, array(230, 230, 230), $monat, $jahr);
radek_zapati($pdf, 4, array(235, 235, 235), $monat, $jahr, $summenZapati);

//============================================================+
// END OF FILE                                                 
//============================================================+
$pdf->Output();