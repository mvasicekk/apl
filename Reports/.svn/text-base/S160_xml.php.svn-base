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
$views=array("v_drueck","v_anwesenheit");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT drueck.PersNr,drueck.Datum,sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,drueck.`Stück`*drueck.`VZ-IST`)) AS vzaby, sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-soll`,drueck.`Stück`*drueck.`VZ-Soll`)) AS vzkd, Sum(drueck.`Verb-Zeit`) AS verb";
$pt.=" FROM drueck";
$pt.=" where ((drueck.datum='".$datum."'))";
$pt.=" GROUP BY drueck.PersNr, drueck.Datum";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT persnr,datum,schicht as schichtanw,sum(stunden*60) as anwesenheit FROM `dzeit`";
$pt.=" join dtattypen on dtattypen.tat=dzeit.tat";
$pt.=" WHERE ((datum='".$datum."') and (schicht between '".$schicht_von."' and '".$schicht_bis."') and (dtattypen.oestatus='a'))";
$pt.=" group by persnr,datum,schicht";
//echo $pt."<br>";
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$v_drueck=$pcip.$views[0];
$v_anwesenheit=$pcip.$views[1];


$sql=" SELECT dpers.schicht,dschicht.Schichtfuehrer,dpers.persnr,dpers.name,dpers.vorname,$v_drueck.vzkd,$v_drueck.vzaby,$v_drueck.verb,$v_anwesenheit.anwesenheit,";
$sql.=" if($v_drueck.verb<>0,$v_drueck.vzaby/$v_drueck.verb*100,0) as fac1,";
$sql.=" if($v_anwesenheit.anwesenheit<>0,$v_drueck.vzaby/$v_anwesenheit.anwesenheit*100,0) as fac2,";
$sql.=" if($v_anwesenheit.anwesenheit<>0,$v_drueck.verb/$v_anwesenheit.anwesenheit*100,0) as fac3";
$sql.=" from $v_anwesenheit join dpers on dpers.persnr=$v_anwesenheit.persnr";
$sql.=" left join $v_drueck";
$sql.=" on $v_drueck.persnr=$v_anwesenheit.persnr and $v_drueck.datum=$v_anwesenheit.datum";
$sql.=" join dschicht on dschicht.schichtnr=dpers.schicht";
$sql.=" where ((dpers.schicht between '".$schicht_von."' and '".$schicht_bis."') and ($v_anwesenheit.anwesenheit>0))";
$sql.=" ORDER BY dpers.schicht,dpers.persnr";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

function get_kurs($wahr,$ausliefer)
{
	//echo "wahr=$wahr,ausliefer=$ausliefer<br>";
	if($wahr!="EUR")
	{
		// podle auslieferdatumu a meny zjistim kurs
		$res=mysql_query("select kurs from dkurs where ((gilt_von<='".$ausliefer."') and (gilt_bis>='".$ausliefer."'))");
		$row=mysql_fetch_array($res);
		//echo "kurs=".$row['kurs']."<br>";
		return $row['kurs'];
	}
	else
	{
		//echo "kurs=1<br>";
		return 1;
	}
}

$options = array(
		'encoder'=>false,
		'rootTag'=>'S160',
		'idColumn'=>'schicht',
		'rowTag'=>'schichten',
		'elements'=>array(
			'schicht',
			'Schichtfuehrer',
			'mitarbeiter'=>array(
				'rootTag'=>'arbeiter',
				'rowTag'=>'pers',
				'idColumn'=>'persnr',
				'elements'=>array(
					'persnr',
					'name',
					'vorname',
					'vzkd',
					'vzaby',
					'verb',
					'anwesenheit',
					'fac1',
					'fac2',
					'fac3'
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
//$domxml->save("S160.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
