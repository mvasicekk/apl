<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";
require_once '../db.php';


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu
$views=array("pt_auss");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";

$pt.=" as select dauftr.auftragsnr,";
$pt.=" dauftr.`pos-pal-nr` as pal,";
$pt.=" sum(dauftr.auss2_stk_exp+dauftr.auss4_stk_exp+dauftr.auss6_stk_exp) as auss";
$pt.=" from dauftr";
$pt.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$pt.=" where";
//$pt.="  dauftr.auftragsnr between $auftragsnr_von and $auftragsnr_bis";
$pt.="  daufkopf.aufdat between '$auftragsnr_von' and '$auftragsnr_bis 23:59:59'";
$pt.="     and teil='$teil'";
$pt.=" group by";
$pt.="     dauftr.auftragsnr,";
$pt.="     dauftr.`pos-pal-nr`";
//echo $pt."<br>";
$db->query($pt);

// vsechny views spojit v dotazu

$pt_auss=$pcip.$views[0];
$sql="SELECT DATE_FORMAT(dim.aufdat,'%d.%m.%Y') as aufdat,if(dex.ausliefer_datum is not null,DATE_FORMAT(dex.ausliefer_datum,'%d.%m.%y'),'') as auslief,d.`auftragsnr-exp` as auftragsnrex,d.`pos-pal-nr` as palimp,d.`stück` as stkimp,";
$sql.=" d.`stk-exp` as impkorr,sum(if(drueck.`stück` is not null,drueck.`stück`,0)) as stkrueG,";
$sql.=" d.`stk-exp` as stkexp,drech.`stück` as stkre,";
$sql.=" d.`auss2_stk_exp`+d.`auss4_stk_exp`+d.`auss6_stk_exp` as aussstk,";
$sql.=" $pt_auss.auss as aussstk_all,";
$sql.=" d.`auftragsnr`,   d.`abgnr`,d.`fremdpos`,d.`giesstag`, d.`pal-nr-exp` as pal_exp";
$sql.=" FROM dauftr d";
$sql.=" join daufkopf dim on d.auftragsnr=dim.auftragsnr";
$sql.=" left join daufkopf dex on d.`auftragsnr-exp`=dex.auftragsnr";
$sql.=" join $pt_auss on $pt_auss.auftragsnr=d.auftragsnr and $pt_auss.pal=d.`pos-pal-nr`";
$sql.=" left join drueck on d.auftragsnr=drueck.auftragsnr and d.`pos-pal-nr`=drueck.`pos-pal-nr` and d.teil=drueck.teil and d.abgnr=drueck.taetnr";
$sql.=" left join drech on d.`auftragsnr-exp`=drech.auftragsnr and d.`auftragsnr`=drech.origauftrag and d.teil=drech.teil and d.`pos-pal-nr`=drech.`pos-pal-nr` and d.`mehrarb-kz`=drech.`taet-kz`"; 
//$sql.=" where ((d.auftragsnr between '$auftragsnr_von' and '$auftragsnr_bis') and (d.teil='$teil') and (d.kzgut='G')) ";
$sql.=" where ((dim.aufdat between '$auftragsnr_von' and '$auftragsnr_bis 23:59:59') and (d.teil='$teil') and (d.kzgut='G')) ";
$sql.=" group by"; 
$sql.=" d.`auftragsnr`,";
$sql.=" d.`pos-pal-nr`,";
$sql.=" d.`pal-nr-exp`";
$sql.=" order by d.auftragsnr,d.`pos-pal-nr`";
//echo "sql=$sql"."<br>";
//posilani nahoru, jde na 2
$query2xml = XML_Query2XML::factory($db);
	
function get_muster_vom($record)
{
	if(strlen($record['muster_vom'])>0)
		return $record['muster_vom'];
	else
		return "??-??-??";
}

function get_bemerkung($record){
    $a = AplDB::getInstance();
    return $a->getPalBemerkungIMPal($record['auftragsnr'],$record['palimp']);
}


$options = array(
					'encoder'=>false,
					'rootTag'=>'S810',
					'idColumn'=>'auftragsnr',
					'rowTag'=>'auftrag',
					'elements'=>array(
						'auftragsnr',
						'aufdat',
						'paletten'=>array(
							'rootTag'=>'paletten',
							'rowTag'=>'palette',
							'idColumn'=>'palimp',
							'elements'=>array(
								'auftragsnr',
								'palimp',
								'stkimp',
								'impkorr',
								'bemerkung'=>'#get_bemerkung();',
								'stkrueG',
								'stkexp',
								'stkre',
								'aussstk',
                                                                'aussstk_all',
								'abgnr',
								'fremdpos',
								'giesstag',
								'auftragsnrex',
								'auslief',
								'pal_exp'
							),
						),
					),
				);
// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);
$domxml->encoding="UTF-8";

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
$domxml->save("S810.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
