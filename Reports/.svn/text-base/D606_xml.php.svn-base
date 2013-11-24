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


$views=array("pt_D605_dauftr","pt_D605_drueck","pt_D605_drueckG_gesamt");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");

$pt="create view $viewname";
$pt.=" as SELECT dauftr.AuftragsNr, dauftr.`pos-pal-nr`as import_pal,";
$pt.=" DATE_FORMAT(aufdat,'%d.%m.%Y') as aufdat,max(if(kzgut='G',trim(`pal-nr-exp`),0)) as export_pal,dauftr.Teil, sum(if(kzgut='G',`Stück`,0)) as import_stk,";
$pt.=" sum(if(kzgut='G',`stk-exp`,0)) as export_stk, sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)<>'T',vzkd,0)) as S0011P";
$pt.=" ,sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)<>'T',vzkd,0)) as sumS0011P, ";
$pt.=" sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)<>'T',1,0)) as cnt_S0011P,";
$pt.=" sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)='T',vzkd,0)) as S0011T, ";
$pt.=" sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)='T',vzkd,0)) as sumS0011T, ";
$pt.=" sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)='T',1,0)) as cnt_S0011T,sum(if(Stat_Nr='S0041',vzkd,0)) as S0041, ";
$pt.=" sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0041',vzkd,0)) as sumS0041, sum(if(Stat_Nr='S0041',1,0)) as cnt_S0041, ";
$pt.=" sum(if(Stat_Nr='S0051',vzkd,0)) as S0051, sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0051',vzkd,0)) as sumS0051, ";
$pt.=" sum(if(Stat_Nr='S0051',1,0)) as cnt_S0051, sum(if(Stat_Nr='S0061',vzkd,0)) as S0061, ";
$pt.=" sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0061',vzkd,0)) as sumS0061, sum(if(Stat_Nr='S0061',1,0)) as cnt_S0061, ";
$pt.=" sum(if(kzgut='G',`Stück`,0))*sum(vzkd) as sumvzkd, sum(if(kzgut='G',`Stück`,0))*dkopf.gew/1000 as imp_gew ";
$pt.=" FROM dauftr JOIN dkopf using (teil) JOIN `dtaetkz-abg` ON dauftr.abgnr = `dtaetkz-abg`.`abg-nr` ";
$pt.=" JOIN daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr ";
$pt.=" where (((dauftr.`auftragsnr-exp`) = '$export')) group BY dauftr.AuftragsNr, import_pal,dauftr.Teil order by dauftr.AuftragsNr, import_pal,dauftr.Teil;";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");

$pt="create view $viewname";
$pt.=" as SELECT drueck.AuftragsNr, drueck.`pos-pal-nr` as import_pal,drueck.Teil, sum(if(`taetkz-nr`='T',drueck.`Stück`,0))  as sum_stk_T,";
$pt.=" sum(if(`taetkz-nr`='P',drueck.`Stück`,0))  as sum_stk_P, sum(if(`taetkz-nr`='St',drueck.`Stück`,0))  as sum_stk_St, ";
$pt.=" sum(if(`taetkz-nr`='G',drueck.`Stück`,0))  as sum_stk_G, sum(if(`taetkz-nr`='E',drueck.`Stück`,0))  as sum_stk_E, ";
$pt.=" sum(if(auss_typ=2,`auss-Stück`,0)) as auss2, sum(if(auss_typ=4,`auss-Stück`,0)) as auss4, sum(if(auss_typ=6,`auss-Stück`,0)) as auss6";
$pt.=" FROM drueck JOIN `dtaetkz-abg`  ON drueck.TaetNr=`dtaetkz-abg`.`abg-nr` JOIN dauftr on drueck.auftragsnr=dauftr.auftragsnr and ";
$pt.=" drueck.teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.taetnr=dauftr.abgnr ";
$pt.=" where (((dauftr.`auftragsnr-exp`) = '$export')) group BY drueck.AuftragsNr, import_pal,drueck.Teil";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT drueck.AuftragsNr, drueck.teil, drueck.`pos-pal-nr` as import_pal,  sum(drueck.`Stück`)  as sum_stk_Gtat ";
$pt.=" FROM drueck JOIN dauftr on  (drueck.auftragsnr=dauftr.auftragsnr) and (drueck.teil=dauftr.teil) and (drueck.`pos-pal-nr`=dauftr.`pos-pal-nr`) and (drueck.taetnr=dauftr.abgnr) where (((dauftr.`auftragsnr-exp`) = '$export') and (dauftr.kzgut='G')) group BY drueck.AuftragsNr,drueck.teil,import_pal";
//echo $pt."<br>";
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy

$pt_D605_dauftr=$pcip.$views[0];
$pt_D605_drueck=$pcip.$views[1];
$pt_D605_drueckG_gesamt=$pcip.$views[2];

$sql="SELECT '$export' as ex,$pt_D605_dauftr.AuftragsNr, $pt_D605_dauftr.import_pal, $pt_D605_dauftr.export_pal, $pt_D605_dauftr.Teil, $pt_D605_dauftr.import_stk, $pt_D605_drueck.sum_stk_T, $pt_D605_drueck.sum_stk_P, $pt_D605_drueck.sum_stk_St, $pt_D605_drueck.sum_stk_G, $pt_D605_drueck.sum_stk_E, $pt_D605_drueck.auss2, $pt_D605_drueck.auss4, $pt_D605_drueck.auss6, $pt_D605_dauftr.S0011P, $pt_D605_dauftr.sumS0011P, $pt_D605_dauftr.cnt_S0011P, $pt_D605_dauftr.S0011T, $pt_D605_dauftr.sumS0011T, $pt_D605_dauftr.cnt_S0011T, $pt_D605_dauftr.S0041, $pt_D605_dauftr.sumS0041, $pt_D605_dauftr.cnt_S0041, $pt_D605_dauftr.S0051, $pt_D605_dauftr.sumS0051, $pt_D605_dauftr.cnt_S0051, $pt_D605_dauftr.S0061, $pt_D605_dauftr.sumS0061, $pt_D605_dauftr.cnt_S0061, $pt_D605_dauftr.imp_gew, $pt_D605_dauftr.sumvzkd, $pt_D605_dauftr.export_stk, $pt_D605_dauftr.aufdat, `sum_stk_Gtat`-`import_stk` AS GDiff FROM ($pt_D605_dauftr LEFT JOIN $pt_D605_drueck ON ($pt_D605_dauftr.Teil = $pt_D605_drueck.Teil) AND ($pt_D605_dauftr.import_pal = $pt_D605_drueck.import_pal) AND ($pt_D605_dauftr.AuftragsNr = $pt_D605_drueck.AuftragsNr)) LEFT JOIN $pt_D605_drueckG_gesamt ON ($pt_D605_dauftr.Teil = $pt_D605_drueckG_gesamt.Teil) AND ($pt_D605_dauftr.import_pal = $pt_D605_drueckG_gesamt.import_pal) AND ($pt_D605_dauftr.AuftragsNr = $pt_D605_drueckG_gesamt.AuftragsNr) order by $pt_D605_dauftr.Teil,$pt_D605_dauftr.import_pal";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry



$options = array(
					'rootTag'=>'D606',
					'idColumn'=>'ex',
					'rowTag'=>'export',
					'elements'=>array(
						'exportnr'=>'ex',
						'teile'=>array(
							'rootTag'=>'teile',
							'idColumn'=>'Teil',
							'rowTag'=>'teil',
							'elements'=>array(
								'teilnr'=>'Teil',
								'paletten'=>array(
									'rootTag'=>'paletten',
									'idColumn'=>'import_pal',
									'rowTag'=>'pal',
									'elements'=>array(
										'teilnr'=>'Teil',
										'import'=>'AuftragsNr',
										'import_pal',
										'export_pal',
										'import_stk',
										'sum_stk_T',
										'sum_stk_P',
										'sum_stk_St',
										'sum_stk_G',
										'sum_stk_E',
										'auss2',
										'auss4',
										'auss6',
										'S0011P',
										'sumS0011P',
										'cnt_S0011P',
										'S0011T',
										'sumS0011T',
										'cnt_S0011T',
										'S0041',
										'sumS0041',
										'cnt_S0041',
										'S0051',
										'sumS0051',
										'cnt_S0051',
										'S0061',
										'sumS0061',
										'cnt_S0061',
										'imp_gew',
										'sumvzkd',
										'export_stk',
										'GDiff'
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
//$domxml->save("D606.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
