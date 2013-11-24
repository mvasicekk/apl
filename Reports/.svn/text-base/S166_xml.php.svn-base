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

// TODO udelat vyber pro minimalni a maximalni hodnotu casu v dany den - to bude prichod a odchod

$sql.=" select ";
$sql.="     DATE_FORMAT(dt,'%Y-%m-%d') as datum,";
$sql.="     edata_access_events.persnr,";
$sql.="     concat(dpers.name,' ',dpers.vorname) as persname,";
$sql.="     dt,";
$sql.="     badgenumber";
$sql.=" from edata_access_events";
$sql.=" left join dpers on dpers.persnr=edata_access_events.persnr";
$sql.=" where ";
$sql.="     class='access'";
$sql.=" and (type='Access granted' or type='Access denied Card unknown')";
$sql.=" and DATE_FORMAT(dt,'%Y-%m-%d') between '$von' and '$bis'";
$sql.=" order by";
$sql.="     DATE_FORMAT(dt,'%Y-%m-%d'),";
$sql.="     persnr,";
$sql.="     dt;";

$query2xml = XML_Query2XML::factory($db);
	

$options = array(
    'encoder' => false,
    'rootTag' => 'S166',
    'idColumn' => 'datum',
    'rowTag' => 'tag',
    'elements' => array(
        'datum',
        'personen' => array(
            'rootTag' => 'personen',
            'rowTag' => 'person',
            'idColumn' => 'persnr',
            'elements' => array(
                'persnr',
                'persname',
                'events' => array(
                    'rootTag' => 'events',
                    'rowTag' => 'event',
                    'idColumn' => 'dt',
                    'elements' => array(
                        'dt',
                        'badgenumber',
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
$domxml->save("S166.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
