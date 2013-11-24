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

$views=array("v_dzeit","v_drueck","v_schichten");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dzeit.schicht,";
$pt.=" sum(if(dtattypen.oestatus='a',dzeit.`Stunden`*60,0)) as minuten_a,";
$pt.=" sum(if(dzeit.tat='d',dzeit.`Stunden`*60,0)) as minuten_d,";
$pt.=" sum(if(dzeit.tat='n',dzeit.`Stunden`*60,0)) as minuten_n,";
$pt.=" sum(if(dzeit.tat='np',dzeit.`Stunden`*60,0)) as minuten_np,";
$pt.=" sum(if(dzeit.tat='nv',dzeit.`Stunden`*60,0)) as minuten_nv,";
$pt.=" sum(if(dzeit.tat='nw',dzeit.`Stunden`*60,0)) as minuten_nw,";
$pt.=" sum(if(dzeit.tat='nu',dzeit.`Stunden`*60,0)) as minuten_nu,";
$pt.=" sum(if(dzeit.tat='p',dzeit.`Stunden`*60,0)) as minuten_p,";
$pt.=" sum(if(dzeit.tat='u',dzeit.`Stunden`*60,0)) as minuten_u,";
$pt.=" sum(if(dzeit.tat='z',dzeit.`Stunden`*60,0)) as minuten_z,";
$pt.=" sum(if(dzeit.tat='?',dzeit.`Stunden`*60,0)) as minuten_frage,";
$pt.=" sum(dzeit.`Stunden`*60) as minuten_gesamt,";
$pt.=" sum(dzeit.`Stunden`*60)-(sum(if(dtattypen.oestatus='a',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='d',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='n',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='np',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='nv',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='nw',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='nu',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='p',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='u',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='?',dzeit.`Stunden`*60,0))+sum(if(dzeit.tat='z',dzeit.`Stunden`*60,0))) as minuten_sonst";
$pt.=" from dzeit";
$pt.=" join dtattypen on dtattypen.tat=dzeit.tat";
$pt.=" where dzeit.`Datum` between '$von' and '$bis' and dzeit.`Schicht` between '$schichtvon' and '$schichtbis'";
$pt.=" group by dzeit.`Schicht`";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select drueck.schicht,";
$pt.=" sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as sumvzkd,";
$pt.=" sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as sumvzaby,";
$pt.=" sum(drueck.`Verb-Zeit`) as sumverb";
$pt.=" from drueck";
$pt.=" where drueck.`Datum` between '$von' and '$bis' and drueck.schicht between '$schichtvon' and '$schichtbis'";
$pt.=" group by drueck.schicht";
////echo $pt."<br>";
$db->query($pt);


$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dschicht.`Schichtnr` as schicht,dschicht.`Schichtfuehrer`,dschichtgruppen.schichtgruppe_beschreibung,dschicht.id_schichtgruppe from dschicht";
$pt.=" join dschichtgruppen on dschicht.id_schichtgruppe=dschichtgruppen.id_schichtgruppe";
$pt.=" where dschicht.`Schichtnr` between '$schichtvon' and '$schichtbis'";

$db->query($pt);


// provedu dotaz nad vytvorenymi pohledy
$v_dzeit=$pcip.$views[0];
$v_drueck = $pcip.$views[1];
$v_schichten = $pcip.$views[2];

$sql = "select $v_schichten.*,";
$sql.=" $v_dzeit.";
$sql.=" $v_dzeit.minuten_a,";
$sql.=" $v_dzeit.minuten_d,";
$sql.=" $v_dzeit.minuten_n,";
$sql.=" $v_dzeit.minuten_np,";
$sql.=" $v_dzeit.minuten_nv,";
$sql.=" $v_dzeit.minuten_nw,";
$sql.=" $v_dzeit.minuten_nu,";
$sql.=" $v_dzeit.minuten_p,";
$sql.=" $v_dzeit.minuten_u,";
$sql.=" $v_dzeit.minuten_z,";
$sql.=" $v_dzeit.minuten_frage,";
$sql.=" $v_dzeit.minuten_gesamt,";
$sql.=" $v_dzeit.minuten_sonst,";
$sql.=" $v_drueck.sumvzkd,";
$sql.=" $v_drueck.sumvzaby,";
$sql.=" $v_drueck.sumverb";
$sql.=" from $v_schichten";
$sql.=" left join $v_dzeit on $v_dzeit.schicht=$v_schichten.schicht";
$sql.=" left join $v_drueck on $v_drueck.schicht=$v_schichten.schicht";
$sql.=" order by $v_schichten.schicht";

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
//exit;

// tady se budou tisknout parametry


$options = array(
		'encoder'=>false,
		'rootTag'=>'S110',
		'idColumn'=>'schicht',
		'rowTag'=>'schicht',
		'elements'=>array(
                                    'schichtnr'=>'schicht',
                                    'id_schichtgruppe',
                                    'schichtgruppe_beschreibung',
                                    'Schichtfuehrer',
                                    'minuten_a',
                                    'minuten_d',
                                    'minuten_n',
                                    'minuten_np',
                                    'minuten_nv',
                                    'minuten_nw',
                                    'minuten_nu',
                                    'minuten_p',
                                    'minuten_u',
                                    'minuten_z',
                                    'minuten_frage',
                                    'minuten_gesamt',
                                    'minuten_sonst',
                                    'sumvzkd',
                                    'sumvzaby',
                                    'sumverb'
                                 ),
                 );


// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML

/**
 *
 *
 * @var DOMDocument
 */

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


// smazu pouzite pohledy
for($i=0;$i<sizeof($views);$i++)
{
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S110.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
