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

// vytvorim si nekolik pohledu


$pcip=get_pc_ip();
$views=array("v_dauftr","v_drueck");

if($teil!='%')
{
	$teilwhere="(teil='$teil') and ";
	$teildrueckwhere="(drueck.teil='$teil') and ";
}
else
{
	$teilwhere="";
	$teildrueckwhere="";
}
	
$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT dauftr.AuftragsNr, Teil, daufkopf.minpreis,sum(`Stück`) as sumursprung  FROM dauftr";
$pt.=" join daufkopf on dauftr.auftragsnr=daufkopf.auftragsnr";
$pt.=" WHERE ($teilwhere ((dauftr.`auftragsnr-exp`) Is Null) AND ((dauftr.`pal-nr-exp`) Is Null) AND";
$pt.=" ((dauftr.AuftragsNr) Between '".$auftragsnr_von."' And '".$auftragsnr_bis."') and (kzgut='G')) GROUP BY dauftr.AuftragsNr, Teil,daufkopf.minpreis";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT drueck.AuftragsNr, drueck.Teil, drueck.TaetNr, sum(drueck.`Stück`) as stk,";
$pt.=" sum(if(auss_typ=4,(drueck.`Stück`+`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`)) as sumvzkd,";
$pt.=" sum(if(auss_typ=4,(drueck.`Stück`+`auss-Stück`)*`vz-ist`,drueck.`Stück`*`vz-ist`)) as sumvzaby,";
$pt.=" Sum(drueck.`Verb-Zeit`) AS sumverb,";
$pt.=" max(drueck.`vz-soll`) AS vzkdmin,";
$pt.=" sum(if(auss_typ=4,(drueck.`auss-Stück`),0)) as sum_auss4,";
$pt.=" sum(if(auss_typ=2,(drueck.`auss-Stück`),0)) as sum_auss2,";
$pt.=" sum(if(auss_typ=6,(drueck.`auss-Stück`),0)) as sum_auss6";
$pt.=" FROM drueck INNER JOIN dauftr ";
$pt.=" ON (drueck.`pos-pal-nr` = dauftr.`pos-pal-nr`) and (drueck.TaetNr = dauftr.abgnr) AND ";
$pt.=" (drueck.Teil = dauftr.Teil) AND  (drueck.AuftragsNr = dauftr.AuftragsNr) ";
$pt.=" WHERE ($teildrueckwhere ((dauftr.`auftragsnr-exp`) Is Null) AND ((dauftr.`pal-nr-exp`) Is Null) AND  ";
$pt.=" ((dauftr.AuftragsNr) Between '".$auftragsnr_von."'  And '".$auftragsnr_bis."')) ";
$pt.=" GROUP BY drueck.AuftragsNr, drueck.Teil, drueck.TaetNr";
 
//echo $pt."<br>";
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$v_dauftr=$pcip.$views[0];
$v_drueck=$pcip.$views[1];


$sql=" SELECT $v_dauftr.auftragsnr, $v_dauftr.teil, ";
$sql.=" Sum($v_dauftr.sumursprung) AS stkimport, ";
$sql.=" $v_drueck.TaetNr, Sum($v_drueck.stk) AS gutstk, ";
$sql.=" Sum($v_drueck.sumvzkd) AS vzkd, ";
$sql.=" Sum($v_drueck.sumvzaby) AS vzaby, ";
$sql.=" Sum($v_drueck.sumverb) AS verb, ";
$sql.=" minpreis*vzkdmin as preis, ";
$sql.=" $v_drueck.sum_auss2 as auss2, $v_drueck.sum_auss4 as auss4, ";
$sql.=" $v_drueck.sum_auss6 as auss6";
$sql.=" FROM $v_dauftr left JOIN $v_drueck ";
$sql.=" ON ($v_drueck.Teil = $v_dauftr.Teil) ";
$sql.=" AND ($v_drueck.AuftragsNr = $v_dauftr.AuftragsNr)";
$sql.=" GROUP BY $v_dauftr.AuftragsNr, $v_dauftr.Teil, ";
$sql.=" $v_drueck.TaetNr, $v_drueck.sum_auss2, ";
$sql.=" $v_drueck.sum_auss4, $v_drueck.sum_auss6";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
function spocti_factor($record)
{
	if($record['verb']!=0)
		return $record['vzkd']/$record['verb'];
	else
		return 0;
}

function vypocti_fac1($record) {
    global $vzkdZeigen;
    if ($record['verb'] != 0) {
	if ($vzkdZeigen === TRUE) {
	    return $record['vzkd'] / $record['verb'];
	} else {
	    return $record['vzaby'] / $record['verb'];
	}
    } else {
	return 0;
    }
}

$options = array(
		'rootTag'=>'S210noex',
		'idColumn'=>'auftragsnr',
		'rowTag'=>'auftraege',
		'elements'=>array(
			'auftragsnr',
			'teile'=>array(
				'rootTag'=>'teile',
				'rowTag'=>'teil',
				'idColumn'=>'teil',
				'elements'=>array(
					'teilnr'=>'teil',
					'stkimport',
					'taetigkeiten'=>array(
						'rootTag'=>'taetigkeiten',
						'rowTag'=>'taetigkeit',
						'idColumn'=>'TaetNr',
						'elements'=>array(
							'tat'=>'TaetNr',
							'gutstk',
							'auss2',
							'auss4',
							'auss6',
							'vzkd',
							'vzaby',
							'verb',
							'preis',
							'factor'=>'#vypocti_fac1();'
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
//$domxml->save("S210noex.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
