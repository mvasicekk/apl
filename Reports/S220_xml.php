<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";

// cast pro vytvoreni XML by mela byt v jinaem souboru jmenosestavy_xml.php
$connectString = "mysql://".LOCAL_USER.":".LOCAL_PASS."@".LOCAL_HOST."/".LOCAL_DB;

//$db = &DB::connect('mysql://root:nuredv@localhost/apl');
$db = &DB::connect($connectString);

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

$pcip=get_pc_ip();

$query2xml = XML_Query2XML::factory($db);


// dotaz
// podle

 if(strlen($teil)>0)
    $where = "((dauftr.auftragsnr='$auftragsnr') and (dauftr.teil = '$teil'))";
 else
    $where = "((dauftr.auftragsnr='$auftragsnr'))";

if(strlen($teil)>0)
    $whereRU = "((drueck.auftragsnr='$auftragsnr') and (drueck.teil = '$teil'))";
 else
    $whereRU = "((drueck.auftragsnr='$auftragsnr'))";

$views=array("pt_dauftr","pt_drueck");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dauftr.auftragsnr,teil,`pos-pal-nr` as pal,max(if(dauftr.`KzGut`='G',dauftr.`stück`,0)) as stkimport,daufkopf.minpreis";
$pt.=" from dauftr";
$pt.=" join daufkopf using(auftragsnr)";
$pt.=" where $where";
$pt.=" group by dauftr.auftragsnr,teil,`pos-pal-nr`";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select auftragsnr,teil,`pos-pal-nr` as pal,taetnr as abgnr,sum(drueck.`Stück`) as gutstk,";
$pt.="sum(if(drueck.auss_typ=2,drueck.`Auss-Stück`,0)) as auss2,";
$pt.="sum(if(drueck.auss_typ=4,drueck.`Auss-Stück`,0)) as auss4,";
$pt.="sum(if(drueck.auss_typ=6,drueck.`Auss-Stück`,0)) as auss6,";
$pt.="sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as sumvzkd,";
$pt.="sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as sumvzaby,";
$pt.="sum(drueck.`Verb-Zeit`) as sumverb,";
$pt.="if(sum(drueck.`Verb-Zeit`)<>0,sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`))/sum(drueck.`Verb-Zeit`),0) as fac1";
$pt.=" from drueck";
$pt.=" where $whereRU";
$pt.=" group by auftragsnr,teil,`pos-pal-nr`";
$db->query($pt);


$dauftr = $pcip.$views[0];
$drueck = $pcip.$views[1];

$sql="select $dauftr.auftragsnr,$dauftr.teil,$dauftr.pal,$drueck.abgnr,";
//$sql.="dauftr.preis,";
$sql.="$drueck.gutstk,";
$sql.="$drueck.auss2,";
$sql.="$drueck.auss4,";
$sql.="$drueck.auss6,";
$sql.="sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as sumvzkd,";
$sql.="sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as sumvzaby,";
$sql.="sum(drueck.`Verb-Zeit`) as sumverb,";
$sql.="if(sum(drueck.`Verb-Zeit`)<>0,sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`))/sum(drueck.`Verb-Zeit`),0) as fac1,";
$sql.="max(if(dauftr.`KzGut`='G',dauftr.`stück`,0)) as stkimport";
$sql.=" from dauftr";
$sql.=" left join drueck";
$sql.=" on dauftr.auftragsnr=drueck.`AuftragsNr` and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr`";
 $sql.=" where $where";
 $sql.=" group by dauftr.auftragsnr,dauftr.`pos-pal-nr`,dauftr.teil,drueck.taetnr";


// tady se budou tisknout parametry


$options = array(
        'encoder'=>false,
		'rootTag'=>'S220',
		'rowTag'=>'auftrag',
		'idColumn'=>'auftragsnr',
		'elements'=>array(
            'auftragsnr',
            'paletten'=>array(
				'rootTag'=>'paletten',
				'rowTag'=>'palette',
				'idColumn'=>'pal',
				'elements'=>array(
						'pal',
						'teil',
						'taetigkeiten'=>array(
										'rootTag'=>'taetigkeiten',
										'rowTag'=>'taetigkeit',
										'idColumn'=>'abgnr',
										'elements'=>array(
														'abgnr',
														'preis',
                                                        'gutstk',
														'auss2',
														'auss4',
														'auss6',
														'sumvzkd',
														'sumvzaby',
														'sumverb',
														'fac1',
                                                        'stkimport'
														),
										),
									),
							),
						)
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
$domxml->save("S220.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
