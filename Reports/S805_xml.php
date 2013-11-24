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

$sql.=" select ";
$sql.="     daufkopf.kunde,";
$sql.="     dksd.Name1 as name1,";
$sql.="     dksd.Name2 as name2,";
$sql.="     `dtaetkz-abg`.Stat_Nr as statnr,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=1,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_01,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=2,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_02,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=3,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_03,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=4,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_04,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=5,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_05,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=6,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_06,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=7,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_07,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=8,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_08,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=9,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_09,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=10,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_10,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=11,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_11,";
$sql.="     sum(if(MONTH(daufkopf.ausliefer_datum)=12,(drech.`Stück`+drech.Ausschuss)*drech.DM,0)) as m_12,";
$sql.="     sum((drech.`Stück`+drech.Ausschuss)*drech.DM) as betrag";
$sql.=" from drech";
$sql.=" join daufkopf on daufkopf.auftragsnr=drech.AuftragsNr";
$sql.=" join dksd on dksd.Kunde=daufkopf.kunde";
$sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drech.abgnr";
$sql.=" where";
$sql.="     daufkopf.kunde between $kundevon and $kundebis";
$sql.="     and daufkopf.ausliefer_datum between '$jahr-01-01' and '$jahr-12-31'";
$sql.=" group by";
$sql.="     daufkopf.kunde,";
$sql.="     `dtaetkz-abg`.Stat_Nr;";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

$options = array(
                'encoder'=>FALSE,
		'rootTag'=>'S805',
		'idColumn'=>'kunde',
		'rowTag'=>'kunde',
		'elements'=>array(
			'kundenr'=>'kunde',
			'name1',
			'name2',
			'stat'=>array(
				'rootTag'=>'stat',
				'rowTag'=>'statnr_row',
				'idColumn'=>'statnr',
				'elements'=>array(
					'statnr',
					'm_01',
					'm_02',
					'm_03',
					'm_04',
					'm_05',
					'm_06',
					'm_07',
					'm_08',
					'm_09',
					'm_10',
					'm_11',
					'm_12',
					'betrag',
				),
			),
		),
);
					

// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);

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
$domxml->save("S805.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
