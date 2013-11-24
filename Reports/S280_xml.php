<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");
$pcip=get_pc_ip();

// vytvorim si nekolik pohledu
$views=array("vertrag_l_eintritt","vertrag_l_austritt");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     dpers.persnr,";
$pt.="     dpers.eintritt as eintritt_letzt,";
$pt.="     dpers.austritt as austritt_letzt,";
$pt.="     count(dpersvertrag.eintritt) as eintritt_count";
$pt.=" from";
$pt.="     dpersvertrag";
$pt.=" join dpers on dpers.PersNr=dpersvertrag.persnr";
$pt.=" where";
$pt.="      dpersvertrag.verlang=0";
$pt.=" group by";
$pt.="     dpers.persnr";

//echo $pt;
$db->query($pt);


$vertrag = $pcip.$views[0];

$oeWhere = '';
if($oeArray!=FALSE) {
    if(count($oeArray)==1 && $oeArray[0]=='*') {
        // nedelam nic
    }
    else {
        foreach ($oeArray as $oecko) {
            $oeWhere .= " drueck.oe like '".$oecko."' or";
        }
        if(strlen($oeWhere)>0) {
            // odeberu or na konci retezce
            $oeWhere = substr($oeWhere, 0, strlen($oeWhere)-2);
        }
        $oeWhere = strtr($oeWhere, '*', '%');
    }
}

$oeStammWhere = '';
if($oeStammArray!=FALSE) {
    if(count($oeStammArray)==1 && $oeStammArray[0]=='*') {
        // nedelam nic
    }
    else {
        foreach ($oeStammArray as $oecko) {
            $oeStammWhere .= " dtt1.og like '".$oecko."' or";
            $oeStammWhere .= " dtt2.og like '".$oecko."' or";
        }
        if(strlen($oeStammWhere)>0) {
            // odeberu or na konci retezce
            $oeStammWhere = substr($oeStammWhere, 0, strlen($oeStammWhere)-2);
        }
        $oeStammWhere = strtr($oeStammWhere, '*', '%');
    }
}

$oe1StammWhere = '';
if($oe1StammArray!=FALSE) {
    if(count($oe1StammArray)==1 && $oe1StammArray[0]=='*') {
        // nedelam nic
    }
    else {
        foreach ($oe1StammArray as $oecko) {
            $oe1StammWhere .= " dpers.regeloe like '".$oecko."' or";
            $oe1StammWhere .= " drueck.oe like '".$oecko."' or";
        }
        if(strlen($oe1StammWhere)>0) {
            // odeberu or na konci retezce
            $oe1StammWhere = substr($oe1StammWhere, 0, strlen($oe1StammWhere)-2);
        }
        $oe1StammWhere = strtr($oe1StammWhere, '*', '%');
    }
}

$teil = strtr($teil, '*', '%');

$sql=" SELECT drueck_id,dpers.Name,dpers.Vorname,drueck.AuftragsNr,drueck.Teil, dkopf.Teilbez,dpers.regeloe,dpers.einarb_zuschlag";
// austritt
$sql.=" ,DATE_FORMAT(dpers.austritt,'%Y-%m-%d') as A";
//eintritt
$sql.=" ,DATE_FORMAT(dpers.eintritt,'%Y-%m-%d') as E";
//probezeit
$sql.=" ,if(dpersdetail1.zkusebni_doba_dobaurcita is not null,DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%Y-%m-%d'),null) as P";
//befristet
$sql.=" ,if(dpersdetail1.dobaurcita is not null,DATE_FORMAT(dpersdetail1.dobaurcita,'%Y-%m-%d'),null) as B";
// vertrag werte
$sql.=" ,$vertrag.eintritt_letzt";
$sql.=" ,$vertrag.austritt_letzt";
//$sql.=" ,$vertrag.befristet_letzt";
$sql.=" ,$vertrag.eintritt_count";
//vzkd
$sql.=" ,drueck.TaetNr,drueck.`Stück` as stk, if(`auss_typ`=4,(`Stück`+`Auss-Stück`)*`VZ-SOLL`,`Stück`*`VZ-SOLL`) AS vzkd, ";
//vzaby
$sql.=" if(`auss_typ`=4,(`Stück`+`Auss-Stück`)*`VZ-IST`,`Stück`*`VZ-IST`) AS vzaby, ";
//cislo palety
$sql.=" drueck.`Verb-Zeit` as verb,drueck.PersNr,drueck.Datum as datum,drueck.`pos-pal-nr` as pal, ";
//casy od do , smena,oe
$sql.=" DATE_FORMAT(drueck.`verb-von`,'%H:%i') as von,DATE_FORMAT(drueck.`verb-bis`,'%H:%i') as bis,drueck.`marke-aufteilung` as ma, drueck.Schicht as drueck_schicht,";
$sql.=" drueck.`auss_typ`,drueck.`Auss-Stück` as auss_stk, dpers.Schicht as dpers_schicht,";
$sql.= " drueck.oe,";
$sql.=" If(`auss_typ`=4,(`Stück`+`Auss-Stück`),`Stück`) AS bezstueck, dkopf.kunde ";
$sql.=" FROM dkopf";
$sql.= " JOIN (dpers JOIN drueck ON dpers.PersNr = drueck.PersNr) ON dkopf.Teil = drueck.Teil ";
$sql.= " join dtattypen dtt2 on dtt2.tat=dpers.regeloe";
$sql.= " join dtattypen dtt1 on dtt1.tat=drueck.oe";
$sql.=" left join $vertrag on $vertrag.persnr=drueck.persnr";
$sql.=" left join dpersdetail1 on dpersdetail1.persnr=dpers.persnr";
$sql.=" WHERE ((drueck.persnr between '".$pers_von."' and '".$pers_bis."') ";
$sql.=" AND (drueck.Datum Between '".$datum_von."' And '".$datum_bis."') ";
$sql.=" AND (drueck.taetnr Between '".$tatvon."' And '".$tatbis."') ";
if(strlen($oeWhere)>0){
    $sql.= " and (".$oeWhere.") ";
}
if(strlen($oeStammWhere)>0){
    $sql.= " and (".$oeStammWhere.") ";
}
if(strlen($oe1StammWhere)>0){
    $sql.= " and (".$oe1StammWhere.") ";
}
if($kunde!='*')
    $sql .=" and (dkopf.kunde='$kunde')";
if($teil!='%')
    $sql .=" and (dkopf.teil like '$teil')";
$sql.=")";
$sql.=" group by drueck_id order by datum,persnr,auftragsnr,von,teil,taetnr";
//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

function vzkd_stk($record)
{
	if($record['bezstueck']!=0)
		return $record['vzkd']/$record['bezstueck'];
	else
		return 0;
}

function vzaby_stk($record)
{
	if($record['bezstueck']!=0)
		return $record['vzaby']/$record['bezstueck'];
	else
		return 0;
}

function verb_stk($record)
{
	if($record['bezstueck']!=0)
		return $record['verb']/$record['bezstueck'];
	else
		return 0;
}

$options = array(
					'encoder'=>false,
					'rootTag'=>'S280',
					'idColumn'=>'datum',
					'rowTag'=>'datumy',
					'elements'=>array(
						'datum',
						'personal'=>array(
							'rootTag'=>'personal',
							'rowTag'=>'pers',
							'idColumn'=>'PersNr',
							'elements'=>array(
								'PersNr',
								'Name',
								'Vorname',
								//'schicht'=>'dpers_schicht',
                                                                'regeloe',
                                                            'einarb_zuschlag',
                                                            'A',
                                                            'E',
                                                            'P',
                                                            'B',
                                                            'eintritt_letzt',
                                                            'austritt_letzt',
//                                                          'befristet_letzt',
                                                            'eintritt_count',
//								'anwvon',
//								'anwbis',
//                                                                'anwesenheit'=>array(
//                                                                    'rootTag'=>'anwesenheit',
//                                                                    'rowTag'=>'anwteil',
//                                                                    'idColumn'=>'dzeitid',
//                                                                    'elements'=>array(
//                                                                        'anw_von',
//                                                                        'anw_bis',
//                                                                        'stunden',
//                                                                        'tat',
//                                                                    ),
//                                                                ),
								'auftraege'=>array(
									'rootTag'=>'auftraege',
									'rowTag'=>'auftrag',
									'idColumn'=>'AuftragsNr',
									'elements'=>array(
										'AuftragsNr',
										'positionen'=>array(
											'rootTag'=>'positionen',
											'rowTag'=>'position',
											'idColumn'=>'drueck_id',
											'elements'=>array(
												'Teil',
												'pal',
												'Teilbez',
												'drueck_schicht',
                                                                                                'oe',
												'TaetNr',
												'stk',
												'auss_stk',
												'auss_typ',
												'vzkd_stk'=>'#vzkd_stk();',
												'vzaby_stk'=>'#vzaby_stk();',
												'verb_stk'=>'#verb_stk();',
												'vzkd',
												'vzaby',
												'verb',
												'ma',
												'von',
												'bis'
											),
										),
									),
								),
							),
						),
					),
				);

// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);
//$domxml->encoding="windows-1250";

// pokusy s polem parameters
// rozkouskovat si ho na jednotlive polozky
// a vytvorim pole parametru
// TODO doplnit nejakou moznost zformatovani, napr. datum zadane ve formulari by melo nejak vypadat
// mozna by bylo lepsi resit to AJAXem uz pri zadavani do formulare
// ale uz pri generovani formulare budu muset pridat handlery podle planovaneho obsahu formulare

foreach($parameters as $var=>$value)
{
	
	// pokud nazev parametru obsahuje _label, budu hledat hodnotu parametru
	if(strpos($var,"_label"))
	{
		$p[$value]=$last_value;
	}
	$last_value=$value;
	//$promenne.=$var."=".$value."&";
}

// v promenne p bych mel mit seznam parametru, pridam ho do XML jako node do domxml
//

$element=$domxml->createElement("parameters");
$parametry=$domxml->firstChild;
$parametry->appendChild($element);
$i=1;
foreach($p as $var=>$value)
{
	$poradinode=$domxml->createElement("N".$i);
	$labelnode=$domxml->createElement("label",$var);
	$valuenode=$domxml->createElement("value",$value);
	$element->appendChild($poradinode);
	$poradinode->appendChild($labelnode);
	$poradinode->appendChild($valuenode);
	$i++;
}


//header('Content-Type: application/xml');
//echo $proc->transformToXML($domxml);



// smazu pouzite pohledy
for($i=0;$i<sizeof($views);$i++)
{

	// pohledy se smazou podle jejich poctu definovaneho polem views
	
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S280.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
