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
//$views=array("v_drueck","v_anwesenheit");


//$viewname=$pcip.$views[0];
//$db->query("drop view $viewname");
//$pt="create view $viewname";
//$pt.=" as SELECT DRUECK.PersNr,DRUECK.Datum,sum(if(DRUECK.auss_typ=4,(DRUECK.`Stück`+DRUECK.`Auss-Stück`)*DRUECK.`VZ-IST`,DRUECK.`Stück`*DRUECK.`VZ-IST`)) AS vzaby, sum(if(DRUECK.auss_typ=4,(DRUECK.`Stück`+DRUECK.`Auss-Stück`)*DRUECK.`VZ-soll`,DRUECK.`Stück`*DRUECK.`VZ-Soll`)) AS vzkd, Sum(DRUECK.`Verb-Zeit`) AS verb";
//$pt.=" FROM DRUECK";
//$pt.=" where ((drueck.datum='".$datum."'))";
//$pt.=" GROUP BY DRUECK.PersNr, DRUECK.Datum";
//
////echo $pt."<br>";
//$db->query($pt);
//
//$viewname=$pcip.$views[1];
//$db->query("drop view $viewname");
//$pt="create view $viewname";
//$pt.=" as SELECT persnr,datum,schicht as schichtanw,sum(stunden*60) as anwesenheit FROM `dzeit`";
//$pt.=" join dtattypen on dtattypen.tat=dzeit.tat";
//$pt.=" WHERE ((datum='".$datum."') and (schicht between '".$schicht_von."' and '".$schicht_bis."') and (dtattypen.oestatus='a'))";
//$pt.=" group by persnr,datum,schicht";
////echo $pt."<br>";
//$db->query($pt);
//
//// provedu dotaz nad vytvorenymi pohledy
//$v_drueck=$pcip.$views[0];
//$v_anwesenheit=$pcip.$views[1];
$sql = "";
$sql .=" select";
$sql .="     dauftr.`auftragsnr-exp` as export,";
$sql .="     DATE_FORMAT(daufkopf.ausliefer_datum,'%Y-%m-%d') as ausgeliefert_am,";
$sql .="     round(sum(if(`dtaetkz-abg`.Stat_Nr='S0011',(`stk-exp`+auss4_stk_exp)*VzKd,0))) as sum_vzkd_S0011,";
$sql .="     round(sum(if(`dtaetkz-abg`.Stat_Nr='S0041',(`stk-exp`+auss4_stk_exp)*VzKd,0))) as sum_vzkd_S0041,";
$sql .="     round(sum(if(`dtaetkz-abg`.Stat_Nr='S0051',(`stk-exp`+auss4_stk_exp)*VzKd,0))) as sum_vzkd_S0051,";
$sql .="     round(sum(if(`dtaetkz-abg`.Stat_Nr='S0061',(`stk-exp`+auss4_stk_exp)*VzKd,0))) as sum_vzkd_S0061,";
$sql .="     round(sum(if(`dtaetkz-abg`.Stat_Nr='S0081',(`stk-exp`+auss4_stk_exp)*VzKd,0))) as sum_vzkd_S0081,";
$sql .="     round(sum(if(`dtaetkz-abg`.Stat_Nr='S0091',(`stk-exp`+auss4_stk_exp)*VzKd,0))) as sum_vzkd_S0091,";
$sql .="     round(sum((`stk-exp`+auss4_stk_exp)*VzKd)) as sum_vzkd";
$sql .=" from dauftr";
$sql .=" join daufkopf on daufkopf.auftragsnr=dauftr.`auftragsnr-exp`";
$sql .=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
$sql .=" where";
$sql .="     daufkopf.kunde=$kunde";
$sql .="     and (daufkopf.ausliefer_datum between '$datumvon' and '$datumbis')";
$sql .=" group by";
$sql .="     dauftr.`auftragsnr-exp`,";
$sql .="     daufkopf.ausliefer_datum";
$sql .=" order by";
$sql .="     daufkopf.ausliefer_datum,";
$sql .="     dauftr.`auftragsnr-exp`";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'E010',
		'idColumn'=>'export',
		'rowTag'=>'ex',
		'elements'=>array(
                    'export',
                    'ausgeliefert_am',
                    'sum_vzkd_S0011',
                    'sum_vzkd_S0041',
                    'sum_vzkd_S0051',
                    'sum_vzkd_S0061',
                    'sum_vzkd_S0081',
                    'sum_vzkd_S0091',
                    'sum_vzkd',
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


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("E010.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
