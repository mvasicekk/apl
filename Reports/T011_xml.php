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

$sql = " select";
$sql.= "     dkopf.teil,";
$sql.= "     dkopf.Kunde as kunde,";
$sql.= "     dkopf.`Muster-Platz` as platz,";
$sql.= "     dkopf.Teilbez as teilbez,";
$sql.= "     dkopf.gew as gew,";
$sql.= "     dkopf.teillang";
$sql.= "     ,DATE_FORMAT(dkopf.`Muster-vom`,'%y-%m-%d') as muster_vom";
$sql.= "     ,dkopf.`Muster-Freigabe-1` as freigabe1";
$sql.= "     ,dkopf.`Muster-Freigabe-2` as freigabe2";
$sql.= "     ,DATE_FORMAT(dkopf.`Muster-Freigabe-1-vom`,'%y-%m-%d') as freigabe1_vom";
$sql.= "     ,DATE_FORMAT(dkopf.`Muster-Freigabe-2-vom`,'%y-%m-%d') as freigabe2_vom";
$sql.=" from dauftr join daufkopf using(auftragsnr)";
$sql.=" join dkopf using(teil)";
$sql.=" join dksd on daufkopf.kunde=dksd.kunde";
$sql.=" where ((daufkopf.aufdat between '$datumvon 00:00:00' and '$datumbis 23:59:59')";
if(($kunde>0) && ($teil=='*')){
    $sql.= "     and (dkopf.kunde='$kunde')";
}
if((strlen(trim($teil))>0) && ($teil!='*')){
    $sql.= "     and (dkopf.teil='$teil')";
}
$sql.=" )";
$sql.=" group by dkopf.kunde,dkopf.teil";

// 
// pdepsani SQL query a vzber asociativniho pole poelde ubenci puhde naru


//echo $sql;

$query2xml = XML_Query2XML::factory($db);
	

$options = array(
    'encoder' => false,
    'rootTag' => 'T011',
    'idColumn' => 'teil',
    'rowTag' => 'teil',
    'elements' => array(
        'teilnr' => 'teil',
        'kunde',
        'platz',
        'teilbez',
        'gew',
        'teillang',
        'muster_vom',
        'freigabe1',
        'freigabe2',
        'freigabe1_vom',
        'freigabe2_vom',
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

//$element=$domxml->createElement("parameters");
//$parametry=$domxml->firstChild;
//$parametry->appendChild($element);
//$i=1;
//foreach($p as $var=>$value)
//{
//	$poradinode=$domxml->createElement("N".$i);
//	$labelnode=$domxml->createElement("label",$var);
//	$valuenode=$domxml->createElement("value",$value);
//	$element->appendChild($poradinode);
//	$poradinode->appendChild($labelnode);
//	$poradinode->appendChild($valuenode);
//	$i++;
//}


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
$domxml->save("T011.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
