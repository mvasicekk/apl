<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

$pcip=get_pc_ip();
$views=array("pt_stamm","pt_drueck");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     dkopf.`Kunde`,";
$pt.="     dksd.name1,";
$pt.="     dksd.preismin,";
$pt.="     dkopf.`Teil`,";
$pt.="     dkopf.`Teilbez`,";
$pt.="     dkopf.`Gew`,";
$pt.="     dkopf.`BrGew`,";
$pt.="     dpos.`TaetNr-Aby`,";
$pt.="     dpos.`TaetBez-Aby-D`,";
$pt.="     `dtaetkz-abg`.`Name` as abgnrbezeichnung,";
$pt.="     dpos.`VZ-min-kunde`,";
$pt.="     dpos.`vz-min-aby`,";
$pt.="     if(dkopf.`Gew`<0,0,";
$pt.="         if(dkopf.`Gew`<=30,3/29*dkopf.`Gew`+0.2-3/29,";
$pt.="             if(dkopf.`Gew`<=50,0.0625*dkopf.`Gew`+1.125,";
$pt.="                 if(dkopf.`Gew`<=150,0.0325*dkopf.`Gew`+2.625,0.025*dkopf.`Gew`+3.75))))*if(dpos.`TaetNr-Aby`>=1200,1.5,1) as vzaby_formel";
$pt.=" from dkopf";
$pt.=" join dksd on dksd.kunde=dkopf.kunde";
$pt.=" join dpos on dpos.`Teil`=dkopf.`Teil`";
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby`";
$pt.=" where dkopf.kunde between '$kundevon' and '$kundebis' and `dtaetkz-abg`.`Stat_Nr`='S0061' and dpos.`TaetNr-Aby` between 1000 and 2000";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     drueck.teil,";
$pt.="     drueck.taetnr,";
$pt.="     sum(drueck.`Stück`) as sumstk,";
$pt.="     sum(drueck.`Verb-Zeit`) as sumverb";
$pt.=" from drueck";
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.`TaetNr`";
$pt.=" where `dtaetkz-abg`.`Stat_Nr`='S0061' and drueck.`TaetNr` between 1000 and 2000 and drueck.`Datum` between '$datevon' and '$datebis'";
$pt.=" group by drueck.`Teil`,drueck.taetnr";
//echo $pt."<br>";
$db->query($pt);


// provedu dotaz nad vytvorenymi pohledy
$pt_stamm=$pcip.$views[0];
$pt_drueck=$pcip.$views[1];

$sql = "select ";
$sql.="     $pt_stamm.`Kunde` as kunde,";
$sql.="     $pt_stamm.name1,";
$sql.="     $pt_stamm.preismin,";
$sql.="     $pt_stamm.`Teil` as teil,";
$sql.="     $pt_stamm.`Teilbez` as teilbez,";
$sql.="     $pt_stamm.`Gew` as gew,";
$sql.="     $pt_stamm.`BrGew` as brgew,";
$sql.="     $pt_stamm.`TaetNr-Aby` as abgnr,";
$sql.="     $pt_stamm.abgnrbezeichnung ,";
$sql.="     $pt_stamm.`VZ-min-kunde` as vzkd,";
$sql.="     $pt_stamm.`vz-min-aby` as vzaby,";
$sql.="     $pt_stamm.vzaby_formel,";
$sql.="     $pt_drueck.sumstk,";
$sql.="     $pt_drueck.sumverb,";
$sql.="     if($pt_drueck.sumstk<>0,$pt_drueck.sumverb/$pt_drueck.sumstk,0) as avgverb";
$sql.=" from $pt_stamm";
$sql.=" join $pt_drueck on $pt_stamm.Teil=$pt_drueck.teil and $pt_stamm.`TaetNr-Aby`=$pt_drueck.taetnr";
$sql.=" where $pt_drueck.sumverb>0";
$sql.=" order by kunde,sumverb desc,teil,abgnr";

//echo "sql=$sql"."<br>";

function deltaProzent_VzAbyFormel_zu_VzAby($record){
    $rundenStellen = 2;
    $deltaAbsolut = $record['vzaby_formel']-$record['vzaby'];
    return $record['vzaby']==0?0:round($deltaAbsolut/$record['vzaby']*100,$rundenStellen);
}

function deltaProzent_VzKd_zu_VzAby($record){
    $rundenStellen = 2;
    $deltaAbsolut = $record['vzkd']-$record['vzaby'];
    return $record['vzaby']==0?0:round($deltaAbsolut/$record['vzaby']*100,$rundenStellen);
}

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$options = array(
					'encoder'=>false,
					'rootTag'=>'S821',
					'idColumn'=>'kunde',
					'rowTag'=>'kunde',
					'elements'=>array(
						'kundenr'=>'kunde',
						'name1',
						'preismin',
						'teile'=>array(
							'rootTag'=>'teile',
							'rowTag'=>'teil',
							'idColumn'=>'teil',
							'elements'=>array(
								'teilnr'=>'teil',
								'teilbez',
								'gew',
                                                                'brgew',
                                                                'taetigkeiten'=>array(
                                                                    'rootTag'=>'taetigkeiten',
                                                                    'rowTag'=>'taetigkeit',
                                                                    'idColumn'=>'abgnr',
                                                                    'elements'=>array(
                                                                        'teilnr'=>'teil',
                                                                        'teilbez',
                                                                        'gew',
                                                                        'brgew',
                                                                        'abgnr',
                                                                        'abgnrbezeichnung',
                                                                        'vzkd',
                                                                        'vzaby',
                                                                        'vzaby_formel',
                                                                        'vzabyformel_zu_vzaby'=>'#deltaProzent_VzAbyFormel_zu_VzAby();',
                                                                        'vzkd_zu_vzaby'=>'#deltaProzent_VzKd_zu_VzAby();',
                                                                        'sumstk',
                                                                        'sumverb',
                                                                        'avgverb',
                                                                     ),
                                                                 ),
							),
						),
					)
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
$domxml->save("S821.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
