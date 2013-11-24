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

$sql="select daufkopf.kunde,daufkopf.minpreis,dksd.`waehr-kz` as waehrung,";
$sql.=" max(drueck.`vz-soll`)*daufkopf.minpreis as preis,max(drueck.`vz-soll`) as vzkdmin,dauftr.`auftragsnr-exp`, dauftr.teil,dauftr.auftragsnr,";
$sql.=" drueck.taetnr, sum(drueck.`stück`) as stk,";
$sql.=" sum(if(auss_typ=2,`auss-stück`,0)) as auss2,";
$sql.=" sum(if(auss_typ=4,`auss-stück`,0)) as auss4,";
$sql.=" sum(if(auss_typ=6,`auss-stück`,0)) as auss6 ,";
$sql.=" sum(if(auss_typ=4,(drueck.`Stück`+`auss-Stück`)*`vz-soll`, drueck.`Stück`*`vz-soll`))  as vzkd,";
$sql.=" sum(if(auss_typ=4,(drueck.`Stück`+`auss-Stück`)*`vz-ist`, drueck.`Stück`*`vz-ist`)) as vzaby,";
$sql.=" Sum(drueck.`Verb-Zeit`) AS verb,";
$sql.=" if(dauftr.abgnr=drueck.taetnr,'G','') as kzgut";
//$sql.="dpos.`kzgut`";
$sql.=" from dauftr";
$sql.=" join daufkopf";
$sql.=" on  (dauftr.`auftragsnr-exp`=daufkopf.auftragsnr)";
$sql.=" join dksd";
$sql.=" on (daufkopf.kunde=dksd.kunde)";
$sql.=" left join  drueck";
$sql.=" on  ((dauftr.auftragsnr=drueck.auftragsnr) and (dauftr.teil=drueck.teil)  and  (dauftr.`pos-pal-nr`=drueck.`pos-pal-nr`))";
$sql.=" left join dpos on (dpos.teil=drueck.teil) and (dpos.`taetnr-aby`=drueck.taetnr)";
$sql.=" where ((`auftragsnr-exp`='".$export."' )  and  (dauftr.kzgut='G') and (drueck.taetnr is not null) and (drueck.taetnr between $tatvon and $tatbis))";
$sql.=" group by daufkopf.kunde,dauftr.`auftragsnr-exp`, dauftr.teil,dauftr.auftragsnr, drueck.taetnr,dpos.kzgut";

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

function vypocti_fac1($record)
{
	if($record['verb']!=0)
		return $record['vzkd']/$record['verb'];
	else
		return 0;
}

$options = array(
		'encoder'=>false,
		'rootTag'=>'S211',
		'idColumn'=>'auftragsnr-exp',
		'rowTag'=>'exporte',
		'elements'=>array(
			'auftragsnr-exp',
			'kunde',
			'minpreis',
			'waehrung',
			'teile'=>array(
				'rootTag'=>'teile',
				'rowTag'=>'teil',
				'idColumn'=>'teil',
				'elements'=>array(
					'teilnr'=>'teil',
					'importe'=>array(
						'rootTag'=>'importe',
						'rowTag'=>'import',
						'idColumn'=>'auftragsnr',
						'elements'=>array(
							'auftragsnr',
							'taetigkeiten'=>array(
								'rootTag'=>'taetigkeiten',
								'rowTag'=>'taetigkeit',
								'idColumn'=>'taetnr',
								'elements'=>array(
									'tat'=>'taetnr',
									'preis',
									'vzkdmin',
									'stk',
									'auss2',
									'auss4',
									'auss6',
									'vzkd',
									'vzaby',
									'verb',
									'kzgut',
									'fac1'=>'#vypocti_fac1();'
								),
							),
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
$domxml->save("S211.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
