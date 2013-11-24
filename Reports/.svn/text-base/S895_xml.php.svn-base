<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";

// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$connectString = "mysql://".LOCAL_USER.":".LOCAL_PASS."@".LOCAL_HOST."/".LOCAL_DB;

//$db = &DB::connect('mysql://root:nuredv@localhost/apl');
$db = &DB::connect($connectString);

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu


$pcip=get_pc_ip();
$views=array("priprava");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select daufkopf.kunde,daufkopf.auftragsnr,dkopf.teil,dkopf.gew,DATE_FORMAT(daufkopf.ausliefer_datum,'%d.%m.%Y') as aufdat,sum(if(dauftr.kzgut='G',dauftr.`stk-exp`,0)) as stk,sum(dauftr.auss2_stk_exp) as auss2,sum(dauftr.auss4_stk_exp) as auss4,sum(dauftr.auss6_stk_exp) as auss6 from dauftr";
$pt.=" join daufkopf on dauftr.`auftragsnr-exp`=daufkopf.auftragsnr";
$pt.=" join dkopf on dauftr.teil=dkopf.teil";
$pt.=" where ((daufkopf.kunde between '$kundevon' and '$kundebis') and (daufkopf.ausliefer_datum between '$aufdatvon' and '$aufdatbis'))";
$pt.=" group by daufkopf.kunde,daufkopf.auftragsnr,dkopf.teil";

//echo "pt=$pt";
$db->query($pt);



// provedu dotaz nad vytvorenymi pohledy
$pt_priprava=$pcip.$views[0];

$sql="select $pt_priprava.kunde";
$sql.=",$pt_priprava.auftragsnr";
$sql.=",$pt_priprava.aufdat";
$sql.=",sum($pt_priprava.stk) as stk";
$sql.=",sum($pt_priprava.auss2) as auss2";
$sql.=",sum($pt_priprava.auss4) as auss4";
$sql.=",sum($pt_priprava.auss6) as auss6";
$sql.=",sum(($pt_priprava.auss2+$pt_priprava.auss4+$pt_priprava.auss6+$pt_priprava.stk)*$pt_priprava.gew) as vaha";
$sql.=" from $pt_priprava";
$sql.=" group by kunde,auftragsnr";

//$sql=" SELECT drech.AuftragsNr, drech.`Taet-kz`, drech.Text1, drech.DM, Sum(drech.`Stück`) AS sumstk, Sum(drech.Ausschuss) AS sumauss, sum((drech.`Stück`+Ausschuss)*DM) as betrag";
//$sql.=" FROM drech join daufkopf on daufkopf.auftragsnr=drech.auftragsnr";
//$sql.=" where ((ausliefer_datum between '".$ausliefer_von."' and '".$ausliefer_bis."') and (teil='".$teil."') and (daufkopf.kunde='".$kunde."'))";
//$sql.=" GROUP BY drech.AuftragsNr, drech.`Taet-kz`, drech.Text1, drech.DM";
//$sql.=" ORDER BY drech.auftragsnr,drech.`Taet-kz`";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

//function get_kurs($wahr,$ausliefer)
//{
//	//echo "wahr=$wahr,ausliefer=$ausliefer<br>";
//	if($wahr!="EUR")
//	{
//		// podle auslieferdatumu a meny zjistim kurs
//		$res=mysql_query("select kurs from dkurs where ((gilt_von<='".$ausliefer."') and (gilt_bis>='".$ausliefer."'))");
//		$row=mysql_fetch_array($res);
//		//echo "kurs=".$row['kurs']."<br>";
//		return $row['kurs'];
//	}
//	else
//	{
//		//echo "kurs=1<br>";
//		return 1;
//	}
//}

$options = array(
        'encoder'=>false,
		'rootTag'=>'S890',
		'idColumn'=>'kunde',
		'rowTag'=>'knd',
		'elements'=>array(
			'kunde',
			'auftraege'=>array(
				'rootTag'=>'auftraege',
				'rowTag'=>'auftrag',
				'idColumn'=>'auftragsnr',
				'elements'=>array(
					'auftragsnr',
					'aufdat',
					'stk',
                    'auss2',
                    'auss4',
                    'auss6',
					'vaha'
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
$domxml->save("S890.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
