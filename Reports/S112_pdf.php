<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "S112";
$doc_subject = "S112 Report";
$doc_keywords = "S112";

// necham si vygenerovat XML

$parameters=$_GET;
//$monat = $_GET['monat'];
//$jahr = $_GET['jahr'];
$von = make_DB_datum(validateDatum($_GET['von']));
$bis = make_DB_datum(validateDatum($_GET['bis']));

$user = $_SESSION['user'];
$reporttyp = $_GET['reporttyp'];

$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S112",$password,$user,0);

if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}


require_once('S112_xml.php');


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

//echo "von = $von, bis = $bis";
//exit;



global $ogFarbenArray;

$sum_zapati_sestava_array;
global $sum_zapati_sestava_array;

$sum_zapati_schichtgruppe_array;
global $sum_zapati_schichtgruppe_array;

$text_zapati_schichtgruppe_array;
global $text_zapati_schichtgruppe_array;

function getSchichtGruppeBeschreibung($id){
    dbConnect();
    $sql = "select dschichtgruppen.schichtgruppe_beschreibung from dschichtgruppen where dschichtgruppen.id_schichtgruppe='$id'";
//    echo "sql=$sql";
    $result = mysql_query($sql);
    $output = '';
    if(mysql_num_rows($result)>0){
        $row = mysql_fetch_assoc($result);
//        print_r($row);
        $output = $row['schichtgruppe_beschreibung'];
    }
    return $output;
}

//
/**
 * funkce pro vykresleni hlavicky na kazde strance
 * @param TCPDF $pdfobjekt
 * @param <type> $pole
 * @param <type> $headervyskaradku
 */
function pageheader($pdfobjekt,$rgb,$vyskaradku,$last) {

        $fill = 1;
        $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

        if($last==1) {
            $obsah = "OG";
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(13,$vyskaradku,$obsah,'TLB',0,'L',$fill);
            //schichtfuehrer
            $obsah = "Text";
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(10+35,$vyskaradku,$obsah,'TLB',0,'L',$fill);
        }
        else {
        //schicht
            $obsah = "OG";
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //schicht
            $obsah = "OE";
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(13,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //schichtfuehrer
            $obsah = "Text";
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(35,$vyskaradku,$obsah,'TLB',0,'L',$fill);
        }
        //vzkd
        $obsah = "VzKd";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = "VzAby";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //verb
        $obsah = "Verb";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //soll1
        $obsah = "Soll1";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //soll2
        $obsah = "Soll2";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //a
        $obsah = "A";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //d
        $obsah = "D";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //n
        $obsah = "N";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //np
        $obsah = "NP";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nu
        $obsah = "NU";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nv
        $obsah = "NV";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nw
        $obsah = "NW";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);


        //p
        $obsah = "P";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //u
        $obsah = "U";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //z
        $obsah = "Z";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //frage
        $obsah = "?";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //sonst
        $obsah = "Sonst";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //gesamt
        $obsah = "Sum abw.";
        $pdfobjekt->SetFont("FreeSans", "B", 5.5);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = "VzAby/Verb";
        $pdfobjekt->SetFont("FreeSans", "B", 5);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = "VzKd/Verb";
        $pdfobjekt->SetFont("FreeSans", "B", 5);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = "VzKd/VzAby";
        $pdfobjekt->SetFont("FreeSans", "B", 5);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

//        $pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'R',$fill);

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
	return $nodevalue;
}



function testValues($childs){
    $vzkd = floatval(getValueForNode($childs, 'sumvzkd'));
    $vzaby = floatval(getValueForNode($childs, 'sumvzaby'));
    $verb = floatval(getValueForNode($childs, 'sumverb'));
    $ursprung = floatval(getValueForNode($childs, 'minuten_a_ursprung'));
    $minuten_gesamt = floatval(getValueForNode($childs, 'minuten_gesamt'));

    if($vzkd>0 || $vzaby>0 || $verb>0 || $minuten_gesamt>0||$ursprung>0)
        return true;
    else
        return false;
}

/**
 *
 * @param TCPDF $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $childs
 */
function schicht_radek($ogFarbenArray,$pdfobjekt,$vyskaradku,$rgb,$childs){

        static $ogOld="0";
        static $pageOx4 = 0;
        $fill = 1;

        $ogRGBArray = split(",",$ogFarbenArray);

//        print_r($ogFarbenArray);
//        print_r($ogRGBArray);
        
        $pdfobjekt->SetFillColor($ogRGBArray[0],$ogRGBArray[1],$ogRGBArray[2],1);

        $schichtnr = getValueForNode($childs, 'oekz');
        $oeVerantwortlich = getValueForNode($childs, 'oe_verantwortlich');

        //og
        $obsah = substr(getValueForNode($childs, 'og'),0,30);
        $ogVerantwortlich = getValueForNode($childs, 'og_verantwortlich');
        $pdfobjekt->SetFont("FreeSans", "", 7);

        if(strcmp($obsah,$ogOld)){
            if(strcmp($ogOld,"0")){
                $pdfobjekt->Ln(2);
            }
            $showOGVerantwortlich = TRUE;
            $ogOld = $obsah;
        }
        else
            $showOGVerantwortlich = FALSE;

        //$f = sin($obsah);

        // odstrankuju pred Ox4
        if(!strcmp($obsah, 'Ox4') && ($pageOx4==0)){
            $pdfobjekt->AddPage();
            pageheader($pdfobjekt,array(255,255,200),5,0);
            $pageOx4 = 1;
            $pdfobjekt->SetFont("FreeSans", "", 7);
        }


        if($showOGVerantwortlich===TRUE){
        $pdfobjekt->Cell(8,$vyskaradku,$obsah,'TLB',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell(4,$vyskaradku,$ogVerantwortlich,'TB',0,'R',$fill);
        }
        else
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'L',$fill);

        //schicht
        $obsah = $schichtnr;
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(9,$vyskaradku,$obsah,'TLB',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell(4,$vyskaradku,$oeVerantwortlich,'TB',0,'R',$fill);

        //schichtfuehrer
        $obsah = substr(getValueForNode($childs, 'oetext'),0,30);

        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(35,$vyskaradku,$obsah,'TLB',0,'L',$fill);

        //vzkd
        $obsah = number_format(floatval(getValueForNode($childs, 'sumvzkd')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = number_format(floatval(getValueForNode($childs, 'sumvzaby')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //verb
        $obsah = number_format(floatval(getValueForNode($childs, 'sumverb')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //soll1
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_a_soll1')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //soll2
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_a_ursprung')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //a
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_a')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //d
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_d')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //n
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_n')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //np
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_np')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nu
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_nu')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nv
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_nv')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nw
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_nw')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //p
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_p')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //u
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_u')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //z
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_z')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //frage
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_frage')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //sonst
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_sonst')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //gesamt
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_gesamt_noa')),0,',',' ');
        if(intval($obsah)==0) $obsah = '';
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        $verb = floatval(getValueForNode($childs, 'sumverb'));
        $vzkd = floatval(getValueForNode($childs, 'sumvzkd'));
        $vzaby = floatval(getValueForNode($childs, 'sumvzaby'));

        $fac1 = $verb==0?0:$vzaby/$verb;
        $fac2 = $verb==0?0:$vzkd/$verb;
        $fac3 = $vzaby==0?0:$vzkd/$vzaby;

        //vzaby
        $obsah = number_format($fac1,2,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = number_format($fac2,2,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = number_format($fac3,2,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

//        $pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'R',$fill);


}

function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$pole,$ende=TRUE){

        $fill = 1;
        $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

        //schicht
        $obsah = "Summe";//$schichtnr;
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        if($ende===TRUE)
            $pdfobjekt->Cell(13+10+35,$vyskaradku,$obsah,'TLB',0,'L',$fill);
        else
            $pdfobjekt->Cell(13+2+10+35,$vyskaradku,$obsah,'TLB',0,'L',$fill);

        //vzkd
        $obsah = number_format($pole['sumvzkd'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = number_format($pole['sumvzaby'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //verb
        $obsah = number_format($pole['sumverb'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //soll1
        $obsah = number_format($pole['minuten_a_soll1'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //soll2
        $obsah = number_format($pole['minuten_a_ursprung'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //a
        $obsah = number_format($pole['minuten_a'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //d
        $obsah = number_format($pole['minuten_d'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //n
        $obsah = number_format($pole['minuten_n'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //np
        $obsah = number_format($pole['minuten_np'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);
        //nu
        $obsah = number_format($pole['minuten_nu'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);


        //nv
        $obsah = number_format($pole['minuten_nv'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nw
        $obsah = number_format($pole['minuten_nw'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //p
        $obsah = number_format($pole['minuten_p'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //u
        $obsah = number_format($pole['minuten_u'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //z
        $obsah = number_format($pole['minuten_z'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //frage
        $obsah = number_format($pole['minuten_frage'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //sonst
        $obsah = number_format($pole['minuten_sonst'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //gesamt
        $obsah = number_format($pole['minuten_gesamt_noa'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        $verb = floatval($pole['sumverb']);
        $vzkd = floatval($pole['sumvzkd']);
        $vzaby = floatval($pole['sumvzaby']);

        $fac1 = $verb==0?0:$vzaby/$verb;
        $fac2 = $verb==0?0:$vzkd/$verb;
        $fac3 = $vzaby==0?0:$vzkd/$vzaby;

        //vzaby
        $obsah = number_format($fac1,2,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = number_format($fac2,2,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = number_format($fac3,2,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

//        $pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'R',$fill);


}

function zapati_sestava_index($pdfobjekt,$vyskaradku,$rgb,$pole,$anwesend=TRUE){

        $fill = 1;
        $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

        $anwMinuten = $pole['minuten_a'];


        if($anwesend===TRUE){

                //schicht
        $obsah = "Index (Anw=100)";//$schichtnr;
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(13+10+35,$vyskaradku,$obsah,'TLBR',0,'L',$fill);

        //vzkd
        $obsah = number_format($pole['sumvzkd']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = number_format($pole['sumvzaby']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //verb
        $obsah = number_format($pole['sumverb']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //soll1
        $obsah = number_format($pole['minuten_a_soll1']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //soll2
        $obsah = number_format($pole['minuten_a_ursprung']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //a
        $obsah = number_format($pole['minuten_a']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLBR',0,'R',$fill);

        $pdfobjekt->Ln();
        }
        else{
                    //schicht
        $obsah = "Index Abwesenheit (Anw=100)";//$schichtnr;
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(13+10+35,$vyskaradku,$obsah,'TLBR',0,'L',$fill);

                $pdfobjekt->Cell(5*12,$vyskaradku,'','0',0,'L',0);
        //a
        $obsah = number_format($pole['minuten_a']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);
        //d
        $obsah = number_format($pole['minuten_d']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //n
        $obsah = number_format($pole['minuten_n']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //np
        $obsah = number_format($pole['minuten_np']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nu
        $obsah = number_format($pole['minuten_nu']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nv
        $obsah = number_format($pole['minuten_nv']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nw
        $obsah = number_format($pole['minuten_nw']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);


        //p
        $obsah = number_format($pole['minuten_p']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //u
        $obsah = number_format($pole['minuten_u']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //z
        $obsah = number_format($pole['minuten_z']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //frage
        $obsah = number_format($pole['minuten_frage']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //sonst
        $obsah = number_format($pole['minuten_sonst']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //gesamt
        $obsah = number_format($pole['minuten_gesamt_noa']/$anwMinuten*100,1,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLBR',0,'R',$fill);
        $pdfobjekt->Ln();
        }
}

function zapati_schichtgruppen($ogFarbenArray,$pdfobjekt,$vyskaradku,$rgb,$pole,$polegesamt,$poletext){

        $fill = 1;
        $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
        
        $gruppen = array_keys($pole);
        sort($gruppen);

        // spocitam si celkovou sumu skupin, ktere vypisuju samostatne = $gruppen
        foreach($gruppen as $gruppe) {
            $ogRGBArray = split(',', $ogFarbenArray[$gruppe]);
            $pdfobjekt->SetFillColor($ogRGBArray[0], $ogRGBArray[1], $ogRGBArray[2]);
        //schicht
            $obsah = $gruppe;//$schichtnr;
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(9,$vyskaradku,$obsah,'TLB',0,'L',$fill);
            $pdfobjekt->SetFont("FreeSans", "", 6);
            $pdfobjekt->Cell(4,$vyskaradku,$poletext[$gruppe]['ogverantwortlich'],'TB',0,'R',$fill);

            //schichtfuehrer
            $obsah = $poletext[$gruppe]['ogtext'];//getSchichtGruppeBeschreibung($gruppe);//getValueForNode($childs, 'Schichtfuehrer');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10+35,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //vzkd
            $obsah = number_format($pole[$gruppe]['sumvzkd'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //vzaby
            $obsah = number_format($pole[$gruppe]['sumvzaby'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //verb
            $obsah = number_format($pole[$gruppe]['sumverb'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //soll1
            $obsah = number_format($pole[$gruppe]['minuten_a_soll1'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //soll2
            $obsah = number_format($pole[$gruppe]['minuten_a_ursprung'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //a
            $obsah = number_format($pole[$gruppe]['minuten_a'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //d
            $obsah = number_format($pole[$gruppe]['minuten_d'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //n
            $obsah = number_format($pole[$gruppe]['minuten_n'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //np
            $obsah = number_format($pole[$gruppe]['minuten_np'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nu
            $obsah = number_format($pole[$gruppe]['minuten_nu'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nv
            $obsah = number_format($pole[$gruppe]['minuten_nv'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nw
            $obsah = number_format($pole[$gruppe]['minuten_nw'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            
            //p
            $obsah = number_format($pole[$gruppe]['minuten_p'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //u
            $obsah = number_format($pole[$gruppe]['minuten_u'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //z
            $obsah = number_format($pole[$gruppe]['minuten_z'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //frage
            $obsah = number_format($pole[$gruppe]['minuten_frage'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //sonst
            $obsah = number_format($pole[$gruppe]['minuten_sonst'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //gesamt
            $obsah = number_format($pole[$gruppe]['minuten_gesamt_noa'],0,',',' ');
            if(intval($obsah)==0) $obsah = '';
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            $verb = floatval($pole[$gruppe]['sumverb']);
            $vzkd = floatval($pole[$gruppe]['sumvzkd']);
            $vzaby = floatval($pole[$gruppe]['sumvzaby']);

            $fac1 = $verb==0?0:$vzaby/$verb;
            $fac2 = $verb==0?0:$vzkd/$verb;
            $fac3 = $vzaby==0?0:$vzkd/$vzaby;

            //vzaby
            $obsah = number_format($fac1,2,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //vzaby
            $obsah = number_format($fac2,2,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);


//vzaby
            $obsah = number_format($fac3,2,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

//            $pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'R',$fill);
        }

        zapati_sestava($pdfobjekt,4,array(230,230,255),$polegesamt);
        zapati_sestava_index($pdfobjekt,4,array(230,230,255),$polegesamt);
        zapati_sestava_index($pdfobjekt,4,array(230,230,255),$polegesamt,FALSE);
        
        // a jeste jednou s procentnim podilem z gesamt
        $decimals = 1;
        $pdfobjekt->Ln();
        pageheader($pdfobjekt,array(255,255,200),5,1);
        $summeProzent = array();
        foreach($gruppen as $gruppe) {
            $ogRGBArray = split(',', $ogFarbenArray[$gruppe]);
            $pdfobjekt->SetFillColor($ogRGBArray[0], $ogRGBArray[1], $ogRGBArray[2]);
        //schicht
            $obsah = $gruppe." in %";//$schichtnr;
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(13,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //schichtfuehrer
            $obsah = $poletext[$gruppe]['ogtext'];//getSchichtGruppeBeschreibung($gruppe);//getValueForNode($childs, 'Schichtfuehrer');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10+35,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //vzkd
            $obsah = number_format($polegesamt['sumvzkd']!=0?$pole[$gruppe]['sumvzkd']/$polegesamt['sumvzkd']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'sumvzkd';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;

            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //vzaby
            $obsah = number_format($polegesamt['sumvzaby']!=0?$pole[$gruppe]['sumvzaby']/$polegesamt['sumvzaby']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'sumvzaby';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //verb
            $vypocet =
            $obsah = number_format($polegesamt['sumverb']!=0?$pole[$gruppe]['sumverb']/$polegesamt['sumverb']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'sumverb';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //soll1
            $vypocet =
            $obsah = number_format($polegesamt['minuten_a_soll1']!=0?$pole[$gruppe]['minuten_a_soll1']/$polegesamt['minuten_a_soll1']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_a_soll1';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //soll2
            $obsah = number_format($polegesamt['minuten_a_ursprung']!=0?$pole[$gruppe]['minuten_a_ursprung']/$polegesamt['minuten_a_ursprung']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_a_ursprung';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //a
            $obsah = number_format($polegesamt['minuten_a']!=0?$pole[$gruppe]['minuten_a']/$polegesamt['minuten_a']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_a';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //d
            $obsah = number_format($polegesamt['minuten_d']!=0?$pole[$gruppe]['minuten_d']/$polegesamt['minuten_d']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_d';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //n
            $obsah = number_format($polegesamt['minuten_n']!=0?$pole[$gruppe]['minuten_n']/$polegesamt['minuten_n']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_n';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //np
            $obsah = number_format($polegesamt['minuten_np']!=0?$pole[$gruppe]['minuten_np']/$polegesamt['minuten_np']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_np';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nu
            $obsah = number_format($polegesamt['minuten_nu']!=0?$pole[$gruppe]['minuten_nu']/$polegesamt['minuten_nu']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_nu';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nv
            $obsah = number_format($polegesamt['minuten_nv']!=0?$pole[$gruppe]['minuten_nv']/$polegesamt['minuten_nv']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_nv';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nw
            $obsah = number_format($polegesamt['minuten_nw']!=0?$pole[$gruppe]['minuten_nw']/$polegesamt['minuten_nw']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_nw';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);


            //p
            $obsah = number_format($polegesamt['minuten_p']!=0?$pole[$gruppe]['minuten_p']/$polegesamt['minuten_p']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_p';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //u
            $obsah = number_format($polegesamt['minuten_u']!=0?$pole[$gruppe]['minuten_u']/$polegesamt['minuten_u']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_u';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //z
            $obsah = number_format($polegesamt['minuten_z']!=0?$pole[$gruppe]['minuten_z']/$polegesamt['minuten_z']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_z';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //frage
            $obsah = number_format($polegesamt['minuten_frage']!=0?$pole[$gruppe]['minuten_frage']/$polegesamt['minuten_frage']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_frage';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //sonst
            //echo "<br>$gruppe: ".$pole[$gruppe]['minuten_sonst']." / ".$polegesamt['minuten_sonst'];
            $obsah = number_format(round($polegesamt['minuten_sonst'])!=0?round($pole[$gruppe]['minuten_sonst'])/round($polegesamt['minuten_sonst'])*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_sonst';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //gesamt
            $obsah = number_format($polegesamt['minuten_gesamt_noa']!=0?$pole[$gruppe]['minuten_gesamt_noa']/$polegesamt['minuten_gesamt_noa']*100:0,$decimals,',',' ');
            if(floatval(strtr($obsah, ',','.'))==0) $obsah = '';
            $feld = 'minuten_gesamt_noa';
            $summeProzent[$feld] += $polegesamt[$feld]!=0?$pole[$gruppe][$feld]/$polegesamt[$feld]*100:0;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

        }

            $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
            //schicht
            $obsah = "Summe %";//$schichtnr;
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(13,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //schichtfuehrer
            $obsah = "";//getSchichtGruppeBeschreibung($gruppe);//getValueForNode($childs, 'Schichtfuehrer');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10+35,$vyskaradku,$obsah,'TB',0,'L',$fill);

            //vzkd
            $feld = 'sumvzkd';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //vzaby
            $feld = 'sumvzaby';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //verb
            $feld = 'sumverb';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //soll1
            $feld = 'minuten_a_soll1';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //a ursprung
            $feld = 'minuten_a_ursprung';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //a
            $feld = 'minuten_a';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //d
            $feld = 'minuten_d';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //n
            $feld = 'minuten_n';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //np
            $feld = 'minuten_np';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nu
            $feld = 'minuten_nu';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nv
            $feld = 'minuten_nv';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nw
            $feld = 'minuten_nw';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);


            //p
            $feld = 'minuten_p';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //u
            $feld = 'minuten_u';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //z
            $feld = 'minuten_z';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //frage
            $feld = 'minuten_frage';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //sonst
            $feld = 'minuten_sonst';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //gesamt
            $feld = 'minuten_gesamt_noa';
            $obsah = number_format($summeProzent[$feld],$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLBR',1,'R',$fill);

}

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vysradku
 * @param <type> $cellhead
 * @param <type> $jahr
 * @param <type> $monat
 */
function test_pageoverflow($pdfobjekt,$vysradku,$rgb,$last)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$rgb,$vysradku,$last);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S112 ".$reporttyp, $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-10, PDF_MARGIN_TOP-13, PDF_MARGIN_RIGHT-10);
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

$ogFarbenArray = array();
// vytahnu si oefarben
$farben = $domxml->getElementsByTagName('farbe');
foreach($farben as $farbe){
    $farbeChilds = $farbe->childNodes;
    $key = getValueForNode($farbeChilds, 'og');
    $value = getValueForNode($farbeChilds, 'rgb');
//    echo "<br>"."key = ".$key." value = ".$value;
    $ogFarbenArray[$key] = $value;
}


// prvni stranka
$pdf->AddPage();
pageheader($pdf,array(255,255,200),5,0);

//exit;
//
$schichten = $domxml->getElementsByTagName('oe');
foreach($schichten as $schicht){
    $schichtChilds = $schicht->childNodes;
    $schichtgruppe = getValueForNode($schichtChilds, 'og');
    $ogtext = getValueForNode($schichtChilds, 'ogbeschreibung');
    $ogVerantwortlich = getValueForNode($schichtChilds, 'og_verantwortlich');
    foreach ($schichtChilds as $schichtnode){
        $sum_zapati_sestava_array[$schichtnode->nodeName] += $schichtnode->nodeValue;
        $sum_zapati_schichtgruppe_array[$schichtgruppe][$schichtnode->nodeName] += $schichtnode->nodeValue;
        $text_zapati_schichtgruppe_array[$schichtgruppe]['ogtext'] = $ogtext;
        $text_zapati_schichtgruppe_array[$schichtgruppe]['ogverantwortlich'] = $ogVerantwortlich;
    }
    // vypisu radek se smenu jen kdyz budu mit nejake hodnoty z drucku nebo z dzeit
    if(testValues($schichtChilds)){
        test_pageoverflow($pdf, 5, array(255,255,200),0);
        schicht_radek($ogFarbenArray[$schichtgruppe],$pdf,4,array(255,255,255),$schichtChilds);
    }
}

//print_r($sum_zapati_schichtgruppe_array);
//
test_pageoverflow($pdf, 5, array(255,255,200),0);
zapati_sestava($pdf,4,array(230,230,255),$sum_zapati_sestava_array,FALSE);

$pdf->AddPage();
pageheader($pdf,array(255,255,200),5,1);
//$pdf->Ln();
//zapati_sestava($pdf,4,array(230,230,255),$sum_zapati_sestava_array);
zapati_schichtgruppen($ogFarbenArray,$pdf,4,array(230,255,255),$sum_zapati_schichtgruppe_array,$sum_zapati_sestava_array,$text_zapati_schichtgruppe_array);

//zapati_sestava($pdf,4,array(230,230,255),$sum_zapati_sestava_array);
////print_r($sum_zapati_sestava_array);

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>