<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";
require_once "../db.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu
$a = AplDB::getInstance();


$pcip=get_pc_ip();

$sql.=" select ";
$sql.="     dauftr.teil,";
$sql.="     dkopf.Teilbez as teilbezeichnung,";
$sql.="     dkopf.verpackungmenge as vpe,";
$sql.="     dkopf.Gew as gew,";
$sql.="     sum(if(dauftr.KzGut='G',dauftr.`stück`,0)) as stk,";
$sql.="     sum(if(dauftr.kzgut='G',(dauftr.`stück`*dkopf.Gew)/1000,0)) as sum_gew";
$sql.=" from dauftr";
$sql.=" join dkopf on dkopf.Teil=dauftr.teil";
$sql.=" where";
$sql.="     dauftr.termin='$termin'";
$sql.=" group by dauftr.teil";

//echo "$sql";
$query2xml = XML_Query2XML::factory($db);
	
$options = array(
    'encoder'=>false,
    'rootTag' => 'D610',
    'idColumn' => 'teil',
    'rowTag' => 'teil',
    'elements' => array(
	'teilnr'=>'teil',
	'teilbezeichnung',
	'vpe',
	'gew',
	'stk',
	'sum_gew'
	),
);





// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);
$domxml->encoding="windows-1250";

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
$domxml->save("D610.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
