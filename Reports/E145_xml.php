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

$views=array("edata");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     edata_access_events.persnr,";
$pt.="     DATE_FORMAT(edata_access_events.dt,'%Y-%m-%d') as datum,";
$pt.="     DATE_FORMAT(min(edata_access_events.dt),'%H:%i') as von,";
$pt.="     DATE_FORMAT(max(edata_access_events.dt),'%H:%i') as bis,";
$pt.="     count(edata_access_events.persnr) as pocet";
$pt.=" from";
$pt.="     edata_access_events";
$pt.=" where ";
$pt.="     edata_access_events.persnr<>0";
$pt.="     and edata_access_events.persnr between '$persvon' and '$persbis'";
$pt.="     and edata_access_events.dt between '$datevon' and '$datebis'";
$pt.=" group by";
$pt.="     edata_access_events.persnr,";
$pt.="     DATE_FORMAT(edata_access_events.dt,'%Y-%m-%d')";
$db->query($pt);

$e=$pcip.$views[0];

$sql.=" select ";
$sql.="     dzeit.persnr,";
$sql.="     CONCAT(dpers.name,' ',dpers.vorname) as name,";
$sql.="     DATE_FORMAT(dzeit.datum,'%Y-%m-%d') as datum,";
$sql.="     DATE_FORMAT(min(dzeit.anw_von),'%H:%i') as von,";
$sql.="     DATE_FORMAT(max(dzeit.anw_bis),'%H:%i') as bis,";
$sql.="     sum(dzeit.Stunden) as stunden,";
$sql.="     sum(dzeit.pause1) as pause,";
$sql.="	    $e.von as e_von,";
$sql.="	    $e.bis as e_bis,";
$sql.="	    $e.pocet as e_pocet";
$sql.=" from";
$sql.="     dzeit";
$sql.=" join dpers on dpers.persnr=dzeit.persnr";
$sql.=" left join $e on $e.persnr=dzeit.persnr and $e.datum=dzeit.datum";
$sql.=" where ";
$sql.="     dzeit.Datum between '$datevon' and '$datebis'";
$sql.="     and dzeit.PersNr between  '$persvon' and '$persbis'";
$sql.=" group by";
$sql.="     dzeit.persnr,";
$sql.="     dzeit.datum";

   
//echo "sql=$sql"."<br>";
//exit;

$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'E145',
		'idColumn'=>'persnr',
		'rowTag'=>'ma',
		'elements'=>array(
                    'persnr',
		    'datumy'=>array(
			'rootTag'=>'datumy',
			'idColumn'=>'datum',
			'rowTag'=>'den',
			'elements'=>array(
			    'persnr',
			    'name',
			    'datum',
			    'von',
			    'bis',
			    'stunden',
			    'pause',
			    'e_von',
			    'e_bis',
			    'e_pocet'
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


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("E145.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
