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

$views=array("D741_sumteilstk","");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");

$pt=" create view $viewname as ";
$pt.=" select kunde,auftragsnr,";
$pt.="datum as rechdatumraw,";
$pt.="DATE_FORMAT(datum,'%Y-%m-%d') as rechdatum,";
$pt.="DATE_FORMAT(`datum-auslief`,'%Y-%m-%d') as lieferdatum,";
$pt.=" vom,";
$pt.=" an,";
$pt.=" origauftrag,";
//$pt.=" if(fremdauftr is null,' ',fremdauftr) as fremdauftr,";
//$pt.=" if(fremdpos is null,'',fremdpos) as fremdpos,";
$pt.=" teil,";
$pt.=" teilbez,";
$pt.=" `taet-kz` as tat,";
$pt.=" text1,";
$pt.=" dm,";
$pt.=" abgnr,";
$pt.=" sum(`stück`) as stk,";
$pt.=" sum(ausschuss) as auss";
$pt.=" from drechneu";
$pt.=" where (((drechneu.AuftragsNr)='$export') and (`Taet-kz`<>'I'))";
//$pt.=" GROUP BY drechneu.kunde, drechneu.AuftragsNr, drechneu.vom, drechneu.an, drechneu.fremdauftr, drechneu.fremdpos, drechneu.Teil, drechneu.teilbez, drechneu.`Taet-kz`, drechneu.DM, drechneu.abgnr";
$pt.=" GROUP BY drechneu.kunde, drechneu.AuftragsNr, drechneu.vom, drechneu.an, drechneu.origauftrag,drechneu.Teil, drechneu.teilbez, drechneu.`Taet-kz`, drechneu.DM, drechneu.abgnr";

$db->query($pt);

//echo "pt=$pt<br>";
// provedu dotaz nad vytvorenymi pohledy

$D743_sumteilstk=$pcip.$views[0];

//$sql = "select * from $D743_sumteilstk";

$sql=" select $D743_sumteilstk.kunde";
$sql.=", AuftragsNr";
$sql.=",rechdatum";
$sql.=",lieferdatum";
$sql.=",DATE_FORMAT(DATE_ADD($D743_sumteilstk.rechdatumraw,INTERVAL dkndumrech.zahlungsziel DAY),'%d.%m.%Y') as zahldatum";
$sql.=",fusszeile1";
$sql.=",fusszeile2";
$sql.=",fusszeile3";
$sql.=",dkndumrech.wahr";
$sql.=",dkndumrech.mwst";
$sql.=",dkndumrech.zahlungsziel";
$sql.=",dkndumrech.rechtext";
$sql.=",dkndumrech.kontotext";
$sql.=",dkndumrech.verzweck";
$sql.=",dksd.name1 as vomname";
$sql.=",dksd.Straße as vomstrasse";
$sql.=",dksd.plz as vomplz";
$sql.=",dksd.ort as vomort";
$sql.=",dksd.land as vomland";
$sql.=",dksd1.name1 as anname1";
$sql.=",dksd1.name2 as anname2";
$sql.=",dksd1.Straße as anstrasse";
$sql.=",dksd1.plz as anplz";
$sql.=",dksd1.ort as anort";
$sql.=",dksd1.land as anland";
$sql.=",dksd1.dic as andic";
$sql.=", $D743_sumteilstk.origauftrag";
$sql.=", $D743_sumteilstk.vom";
$sql.=", $D743_sumteilstk.an";
//$sql.=", fremdauftr";
//$sql.=", fremdpos";
$sql.=", Teil";
$sql.=", teilbez";
$sql.=", text1";
$sql.=", tat";
$sql.=", stk";
$sql.=", auss";
$sql.=", sum(DM) as preis";
$sql.=", max(abgnr) as reihe";
$sql.=" FROM $D743_sumteilstk";
$sql.=" join dksd on dksd.kunde=$D743_sumteilstk.vom";
$sql.=" join dksd as dksd1 on dksd1.kunde=$D743_sumteilstk.an";
$sql.=" join dkndumrech on dkndumrech.vom=$D743_sumteilstk.vom and dkndumrech.an=$D743_sumteilstk.an";
//$sql.=" GROUP BY $D743_sumteilstk.kunde, auftragsnr, $D743_sumteilstk.vom, $D743_sumteilstk.an, fremdauftr, fremdpos, Teil, teilbez, tat";
$sql.=" GROUP BY $D743_sumteilstk.kunde, auftragsnr, $D743_sumteilstk.origauftrag,$D743_sumteilstk.vom, $D743_sumteilstk.an, Teil, teilbez, tat,stk";
//$sql.=" order by fremdauftr,fremdpos,teil,reihe";
$sql.=" order by teil,reihe";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


function getBetrag($record){
    $value = round(
                    floatval(($record['stk']+$record['auss'])*$record['preis'])
                    ,3);
    return round($value,2);
}

$options = array(
					'encoder'=>false,
					'rootTag'=>'D741',
					'idColumn'=>'AuftragsNr',
					'rowTag'=>'rechnung',
					'elements'=>array(
						'AuftragsNr',
						'rechdatum',
						'lieferdatum',
                        'zahldatum',
						'kunde',
						'vom',
						'an',
                        'origauftrag',
						'fusszeile1',
						'fusszeile2',
						'fusszeile3',
                        'wahr',
                        'mwst',
                        'zahlungsziel',
                        'rechtext',
                        'kontotext',
                        'verzweck',
                        'vomname',
                        'vomstrasse',
                        'vomplz',
                        'vomort',
                        'vomland',
                        'anname1',
                        'anname2',
                        'anstrasse',
                        'anplz',
                        'anort',
                        'anland',
                        'andic',
//						'fremdauftr'=>array(
//							'rootTag'=>'fremdauftraege',
//							'idColumn'=>'fremdauftr',
//							'rowTag'=>'fremdauftr',
//							'elements'=>array(
//								'fremdauftrnr'=>'fremdauftr',
//								'fremdpos'=>array(
//									'rootTag'=>'fremdpositionen',
//									'rowTag'=>'fremdpos',
//									'idColumn'=>'fremdpos',
//									'elements'=>array(
//										'fremdauftrnr'=>'fremdauftr',
//										'fremdposnr'=>'fremdpos',
										'teile'=>array(
											'rootTag'=>'teile',
											'rowTag'=>'teil',
											'idColumn'=>'Teil',
											'elements'=>array(
												'teilnr'=>'Teil',
												'teilbez',
												'taetigkeiten'=>array(
													'rootTag'=>'taetigkeiten',
													'rowTag'=>'taetigkeit',
													'idColumn'=>'tat',
                    								'elements'=>array(
                                                        'tat',
                                                        'kusy'=>array(
                                                            'rootTag'=>'kusy',
                                                            'rowTag'=>'kus',
                                                            'idColumn'=>'stk',
                                                            'elements'=>array(
                                                                'teilnr'=>'Teil',
                                                                'teilbez',
                                                                'tat',
                                                                'text1',
                                                                'preis',
                                                                'stk',
                                                                'auss',
                                                                'betrag'=>'#getBetrag();',
                                                                'waehrung'=>'wahr'
                                                            ),
                                                          ),
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
$domxml->save("D741.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
