<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S110";
$doc_subject = "S110 Report";
$doc_keywords = "S110";

// necham si vygenerovat XML

$parameters=$_GET;
//$monat = $_GET['monat'];
//$jahr = $_GET['jahr'];
$schichtvon = $_GET['schichtvon'];
$schichtbis = $_GET['schichtbis'];
$von = make_DB_datum(validateDatum($_GET['von']));
$bis = make_DB_datum(validateDatum($_GET['bis']));

$user = $_SESSION['user'];

$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S110",$password,$user,0);

if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}


require_once('S110_xml.php');


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




$sum_zapati_sestava_array;
global $sum_zapati_sestava_array;

$sum_zapati_schichtgruppe_array;
global $sum_zapati_schichtgruppe_array;

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
function pageheader($pdfobjekt,$rgb,$vyskaradku) {

        $fill = 1;
        $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

        //schicht
        $obsah = "Schichtnr";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(13,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //schichtfuehrer
        $obsah = "Schichführer";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(40,$vyskaradku,$obsah,'TLB',0,'L',$fill);

        //vzkd
        $obsah = "VzKd";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = "VzAby";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //verb
        $obsah = "Verb";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

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

        //nv
        $obsah = "NV";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nw
        $obsah = "NW";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nu
        $obsah = "NU";
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
        $obsah = "Ges";
        $pdfobjekt->SetFont("FreeSans", "B", 7);
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
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        $pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'R',$fill);

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
    $minuten_gesamt = floatval(getValueForNode($childs, 'minuten_gesamt'));

    if($vzkd>0 || $vzaby>0 || $verb>0 || $minuten_gesamt>0)
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
function schicht_radek($pdfobjekt,$vyskaradku,$rgb,$childs){

        $fill = 0;

        $schichtnr = getValueForNode($childs, 'schichtnr');

        //schicht
        $obsah = $schichtnr;
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(13,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //schichtfuehrer
        $obsah = getValueForNode($childs, 'Schichtfuehrer');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(40,$vyskaradku,$obsah,'TLB',0,'L',$fill);

        //vzkd
        $obsah = number_format(floatval(getValueForNode($childs, 'sumvzkd')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = number_format(floatval(getValueForNode($childs, 'sumvzaby')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //verb
        $obsah = number_format(floatval(getValueForNode($childs, 'sumverb')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //a
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_a')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //d
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_d')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //n
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_n')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //np
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_np')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nv
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_nv')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nw
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_nw')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nu
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_nu')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //p
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_p')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //u
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_u')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //z
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_z')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //frage
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_frage')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //sonst
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_sonst')),0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //gesamt
        $obsah = number_format(floatval(getValueForNode($childs, 'minuten_gesamt')),0,',',' ');
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
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        $pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'R',$fill);


}

function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$pole){

        $fill = 1;
        $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

        //schicht
        $obsah = "Summe";//$schichtnr;
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(13,$vyskaradku,$obsah,'TLB',0,'L',$fill);

        //schichtfuehrer
        $obsah = "";//getValueForNode($childs, 'Schichtfuehrer');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(40,$vyskaradku,$obsah,'TLB',0,'L',$fill);

        //vzkd
        $obsah = number_format($pole['sumvzkd'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //vzaby
        $obsah = number_format($pole['sumvzaby'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //verb
        $obsah = number_format($pole['sumverb'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

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

        //nv
        $obsah = number_format($pole['minuten_nv'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nw
        $obsah = number_format($pole['minuten_nw'],0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        //nu
        $obsah = number_format($pole['minuten_nu'],0,',',' ');
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
        $obsah = number_format($pole['minuten_gesamt'],0,',',' ');
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
        $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

        $pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'R',$fill);


}

function zapati_schichtgruppen($pdfobjekt,$vyskaradku,$rgb,$pole,$polegesamt){

        $fill = 1;
        $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
        
        $gruppen = array(1,2,3);

        // spocitam si celkovou sumu skupin, ktere vypisuju samostatne = $gruppen
        foreach($gruppen as $gruppe) {
            foreach ($pole[$gruppe] as $klic=>$hodnota){
                $poleSkoroGesamt[$klic] += $hodnota;
            }
        }

        foreach ($polegesamt as $klic=>$hodnota){
            $pole[99][$klic] = $polegesamt[$klic]-$poleSkoroGesamt[$klic];
        }

        array_push($gruppen, 99);
        
        foreach($gruppen as $gruppe) {

        //schicht
            $obsah = "SG".$gruppe."";//$schichtnr;
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(13,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //schichtfuehrer
            $obsah = getSchichtGruppeBeschreibung($gruppe);//getValueForNode($childs, 'Schichtfuehrer');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(40,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //vzkd
            $obsah = number_format($pole[$gruppe]['sumvzkd'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //vzaby
            $obsah = number_format($pole[$gruppe]['sumvzaby'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //verb
            $obsah = number_format($pole[$gruppe]['sumverb'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //a
            $obsah = number_format($pole[$gruppe]['minuten_a'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //d
            $obsah = number_format($pole[$gruppe]['minuten_d'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //n
            $obsah = number_format($pole[$gruppe]['minuten_n'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //np
            $obsah = number_format($pole[$gruppe]['minuten_np'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nv
            $obsah = number_format($pole[$gruppe]['minuten_nv'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nw
            $obsah = number_format($pole[$gruppe]['minuten_nw'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nu
            $obsah = number_format($pole[$gruppe]['minuten_nu'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);
            
            //p
            $obsah = number_format($pole[$gruppe]['minuten_p'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //u
            $obsah = number_format($pole[$gruppe]['minuten_u'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //z
            $obsah = number_format($pole[$gruppe]['minuten_z'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //frage
            $obsah = number_format($pole[$gruppe]['minuten_frage'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //sonst
            $obsah = number_format($pole[$gruppe]['minuten_sonst'],0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //gesamt
            $obsah = number_format($pole[$gruppe]['minuten_gesamt'],0,',',' ');
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
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            $pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'R',$fill);
        }

        // a jeste jednou s procentnim podilem z gesamt
        $decimals = 1;
        $pdfobjekt->Ln();
        foreach($gruppen as $gruppe) {

        //schicht
            $obsah = "SG".$gruppe." in %";//$schichtnr;
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(13,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //schichtfuehrer
            $obsah = getSchichtGruppeBeschreibung($gruppe);//getValueForNode($childs, 'Schichtfuehrer');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(40,$vyskaradku,$obsah,'TLB',0,'L',$fill);

            //vzkd
            $obsah = number_format($polegesamt['sumvzkd']!=0?$pole[$gruppe]['sumvzkd']/$polegesamt['sumvzkd']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //vzaby
            $obsah = number_format($polegesamt['sumvzaby']!=0?$pole[$gruppe]['sumvzaby']/$polegesamt['sumvzaby']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //verb
            $vypocet =
            $obsah = number_format($polegesamt['sumverb']!=0?$pole[$gruppe]['sumverb']/$polegesamt['sumverb']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //a
            $obsah = number_format($polegesamt['minuten_a']!=0?$pole[$gruppe]['minuten_a']/$polegesamt['minuten_a']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //d
            $obsah = number_format($polegesamt['minuten_d']!=0?$pole[$gruppe]['minuten_d']/$polegesamt['minuten_d']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //n
            $obsah = number_format($polegesamt['minuten_n']!=0?$pole[$gruppe]['minuten_n']/$polegesamt['minuten_n']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //np
            $obsah = number_format($polegesamt['minuten_np']!=0?$pole[$gruppe]['minuten_np']/$polegesamt['minuten_np']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nv
            $obsah = number_format($polegesamt['minuten_nv']!=0?$pole[$gruppe]['minuten_nv']/$polegesamt['minuten_nv']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nw
            $obsah = number_format($polegesamt['minuten_nw']!=0?$pole[$gruppe]['minuten_nw']/$polegesamt['minuten_nw']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //nu
            $obsah = number_format($polegesamt['minuten_nu']!=0?$pole[$gruppe]['minuten_nu']/$polegesamt['minuten_nu']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //p
            $obsah = number_format($polegesamt['minuten_p']!=0?$pole[$gruppe]['minuten_p']/$polegesamt['minuten_p']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //u
            $obsah = number_format($polegesamt['minuten_u']!=0?$pole[$gruppe]['minuten_u']/$polegesamt['minuten_u']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //z
            $obsah = number_format($polegesamt['minuten_z']!=0?$pole[$gruppe]['minuten_z']/$polegesamt['minuten_z']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //frage
            $obsah = number_format($polegesamt['minuten_frage']!=0?$pole[$gruppe]['minuten_frage']/$polegesamt['minuten_frage']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //sonst
            $obsah = number_format($polegesamt['minuten_sonst']!=0?$pole[$gruppe]['minuten_sonst']/$polegesamt['minuten_sonst']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TLB',0,'R',$fill);

            //gesamt
            $obsah = number_format($polegesamt['minuten_gesamt']!=0?$pole[$gruppe]['minuten_gesamt']/$polegesamt['minuten_gesamt']*100:0,$decimals,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLBR',1,'R',$fill);


            //vzaby
//            $obsah = '';//number_format($fac1,2,',',' ');
//            $pdfobjekt->SetFont("FreeSans", "", 7);
//            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);
//
//            //vzaby
//            $obsah = '';//number_format($fac2,2,',',' ');
//            $pdfobjekt->SetFont("FreeSans", "", 7);
//            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);
//
//            //vzaby
//            $obsah = '';//number_format($fac3,2,',',' ');
//            $pdfobjekt->SetFont("FreeSans", "", 7);
//            $pdfobjekt->Cell(12,$vyskaradku,$obsah,'TLB',0,'R',$fill);
//
//            $pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'R',$fill);
        }

}

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vysradku
 * @param <type> $cellhead
 * @param <type> $jahr
 * @param <type> $monat
 */
function test_pageoverflow($pdfobjekt,$vysradku,$rgb)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$rgb,$vysradku);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S110 Pplanschichtstatistik", $params);
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
pageheader($pdf,array(255,255,200),5);

//exit;
//
$schichten = $domxml->getElementsByTagName('schicht');
foreach($schichten as $schicht){
    $schichtChilds = $schicht->childNodes;
    $schichtgruppe = getValueForNode($schichtChilds, 'id_schichtgruppe');
    foreach ($schichtChilds as $schichtnode){
        $sum_zapati_sestava_array[$schichtnode->nodeName] += $schichtnode->nodeValue;
        $sum_zapati_schichtgruppe_array[$schichtgruppe][$schichtnode->nodeName] += $schichtnode->nodeValue;
    }
    // vypisu radek se smenu jen kdyz budu mit nejake hodnoty z drucku nebo z dzeit
    if(testValues($schichtChilds)){
        test_pageoverflow($pdf, 5, array(255,255,200));
        schicht_radek($pdf,4,array(255,255,255),$schichtChilds);
    }
}

//print_r($sum_zapati_schichtgruppe_array);
//
zapati_sestava($pdf,4,array(230,230,255),$sum_zapati_sestava_array);

//$pdf->AddPage();
//pageheader($pdf,array(255,255,200),5);
$pdf->Ln();
zapati_schichtgruppen($pdf,4,array(230,255,255),$sum_zapati_schichtgruppe_array,$sum_zapati_sestava_array);
//zapati_sestava($pdf,4,array(230,230,255),$sum_zapati_sestava_array);
////print_r($sum_zapati_sestava_array);

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>