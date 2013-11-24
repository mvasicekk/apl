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

$views=array("anwesenheit","drueck");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT dzeit.persnr,dzeit.datum,sum(stunden) as stunden,sum(if(dtattypen.erschwerniss<>0,dzeit.stunden*6,0)) as prasne ";
$pt.=" from dzeit join dpers on dpers.persnr=dzeit.persnr join dtattypen on dzeit.tat=dtattypen.tat";
$pt.=" where ";
$pt.=" dtattypen.oestatus='a' ";
if($bNoPersnr===FALSE)
    $pt.=" and dzeit.persnr='$persnr' ";
$pt.=" and dzeit.datum>=dpers.eintritt and dzeit.datum<=DATE_ADD(dpers.eintritt,INTERVAL 62 DAY)";
if($bEintrittVom===TRUE)
    $pt.=" and dpers.eintritt>='$eintrittVom' and (austritt is null or eintritt>austritt)";
$pt.=" group by dzeit.persnr,dzeit.datum";

$db->query($pt);
$anwesenheit=$pcip.$views[0];
//echo $pt."<br>";
//exit;
//drueck
$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     drueck.persnr,";
$pt.="     drueck.`Datum` as datum,";
$pt.= "    `dtaetkz-abg`.`Stat_Nr` as statnr,";
$pt.="     sum(if(auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,drueck.`Stück`*drueck.`VZ-IST`)) as vzaby_min,";
$pt.="     sum(if(auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,drueck.`Stück`*drueck.`VZ-IST`*dtattypen.lohnfaktor)) as vzaby_kc,";
$pt.="     sum(if(auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor*dtattypen.qualitatspraemie/100,drueck.`Stück`*drueck.`VZ-IST`*dtattypen.lohnfaktor*dtattypen.qualitatspraemie/100)) as qualpraemie_kc,";
$pt.="     sum(if((drueck.`TaetNr` between 5000 and 7999),if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as bezpriplatku_min,";
$pt.="     sum(if((drueck.`TaetNr` between 5000 and 7999),if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor),0)) as bezpriplatku_kc";
$pt.=" from";
$pt.="     drueck";
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.taetnr";
$pt.=" join dtattypen on dtattypen.tat=drueck.oe";
$pt.=" join dpers on dpers.`PersNr`=drueck.`PersNr`";
$pt.=" where";
$pt.="     drueck.datum>=dpers.eintritt and drueck.datum<=DATE_ADD(dpers.eintritt,INTERVAL 62 DAY)";
if($bEintrittVom===TRUE)
$pt.=" and dpers.eintritt>='$eintrittVom' and (austritt is null or eintritt>austritt)";
if($bNoPersnr===FALSE)
$pt.="     and drueck.persnr='$persnr'";
$pt.=" group by drueck.persnr,drueck.datum,`dtaetkz-abg`.`Stat_Nr`";
$db->query($pt);
$drueck=$pcip.$views[1];
//echo $pt;
//exit;
// 2011-03-17
// nova verze dotazu pro korekci procent priplatku v zqavislosti na Stat_Nr
$sql = "select";
$sql.= "    $drueck.persnr,";
$sql.= "    CONCAT(dpers.name,' ',dpers.vorname) as name,";
$sql.= "    dpers.eintritt,";
$sql.= "    DATE_FORMAT($drueck.datum,'%Y-%m-%d') as datum,";
$sql.="     if($anwesenheit.stunden is null,0,$anwesenheit.stunden) as stunden,";
$sql.="     if($anwesenheit.prasne is null,0,$anwesenheit.prasne) as prasne,";
$sql.= "    $drueck.statnr,";
$sql.= "    $drueck.vzaby_min,";
$sql.= "    $drueck.vzaby_kc,";
$sql.= "    $drueck.bezpriplatku_min,";
$sql.= "    $drueck.bezpriplatku_kc,";
$sql.= "    $drueck.qualpraemie_kc";
$sql.= " from";
$sql.= "    $drueck";
$sql.= " join dpers on dpers.`PersNr`=$drueck.`PersNr`";
$sql.= " left join $anwesenheit on $anwesenheit.persnr=$drueck.persnr and $anwesenheit.datum=$drueck.datum";
$sql.= " where 1";
if($bNoPersnr===FALSE)
    $sql.=" and dpers.persnr='$persnr'";
//$sql.= "    $drueck.datum>=dpers.eintritt and $drueck.datum<DATE_ADD(dpers.eintritt,INTERVAL 62 DAY)";
$sql.= " group by";
$sql.= "    $drueck.persnr,";
$sql.= "    $drueck.datum,";
$sql.= "    $drueck.statnr";
//echo $sql;
//exit;

$query2xml = XML_Query2XML::factory($db);
	

$options = array(
    'encoder' => false,
    'rootTag' => 'personen',
        'idColumn' => 'persnr',
        'rowTag' => 'person',
        'elements' => array(
            'persnr',
            'name',
            'eintritt',
            'datumy' => array(
                'rootTag' => 'tage',
                'idColumn' => 'datum',
                'rowTag' => 'tag',
                'elements' => array(
                    'datum',
                    'stunden',
                    'prasne',
                    'statnr' => array(
                        'rootTag' => 'statnrs',
                        'idColumn' => 'statnr',
                        'rowTag' => 'statnr',
                        'elements' => array(
                            'statnrnr' => 'statnr',
                            'vzaby_min',
                            'vzaby_kc',
                            'bezpriplatku_min',
                            'bezpriplatku_kc',
                            'qualpraemie_kc',
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
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


//$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S192.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
