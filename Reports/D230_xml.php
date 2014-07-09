<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
//$db->query("set character set cp1250");
$db->query("set names utf8");

// vytvorim si nekolik pohledu


$pcip=get_pc_ip();


$sql="select auftragsnr,dauftr.teil,`pos-pal-nr` as pal, `stück` as stk,daufkopf.kunde,name1,name2,";
$sql.=" dkopf.verpackungmenge,teilbez,teillang,gew,`art guseisen` as artguseisen,";
$sql.=" dkopf.brgew,dkopf.restmengen_verw,";
$sql.=" dverp.id,dverp.verp_id,dverp.verp_stk,`eink-artikel`.`art-name1` as verp_name,";  
$sql.="`muster-platz` as musterplatz,DATE_FORMAT(`muster-vom`,'%d.%m.%Y') as mustervom,";
$sql.=" dpos_id,`abgnr` as taetnr,`TaetBez-Aby-D` as tatbez_d,`TaetBez-Aby-T` as tatbez_t,vzaby,dpos.mittel,fremdpos,";
$sql.=" if(`vz-min-aby`<>0,round(60/`vz-min-aby`),0) as ks_hod";
$sql.=" from dauftr";
$sql.=" join daufkopf using(auftragsnr)";
$sql.=" join dksd on daufkopf.kunde=dksd.kunde";
$sql.=" join dkopf on dauftr.teil=dkopf.teil";
$sql.=" join dpos on dauftr.teil=dpos.teil and dauftr.abgnr=dpos.`taetnr-aby`";
$sql.=" join `dtaetkz-abg` on dpos.`taetnr-aby`=`dtaetkz-abg`.`abg-nr`";
$sql.=" left join dverp on dauftr.teil=dverp.teil_id";
$sql.=" left join `eink-artikel` on `eink-artikel`.`art-nr`=dverp.verp_id";
$sql.=" where ((auftragsnr='$auftragsnr') and (`pos-pal-nr` between '$palvon' and '$palbis') and (`auftragsnr-exp` is null)";
$sql.=" and (1) and (`dtaetkz-abg`.druck_arbpapier<>0))";
$sql.=" order by auftragsnr,pal,taetnr,id;";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry



$options = array(
					'encoder'=>false,
					'rootTag'=>'D230',
					'idColumn'=>'auftragsnr',
					'rowTag'=>'auftrag',
					'elements'=>array(
						'auftragsnr',
						'kunde',
						'name1',
						'name2',
						'paletten'=>array(
							'rootTag'=>'paletten',
							'idColumn'=>'pal',
							'rowTag'=>'palette',
							'elements'=>array(
                                                                'auftragsnr',
								'pal',
								'teil',
								'stk',
								'teilbez',
								'teillang',
								'gew',
								'brgew',
								'restmengen_verw',
								'verpackungmenge',
								'artguseisen',
								'musterplatz',
								'mustervom',
								'fremdpos',
        							'taetigkeiten'=>array(
									'rootTag'=>'taetigkeiten',
									'rowTag'=>'taetigkeit',
									'idColumn'=>'dpos_id',
									'elements'=>array(
										'taetnr',
										'tatbez_d',
										'tatbez_t',
										'vzaby',
										'mittel',
										'ks_hod',
                                                                                'verpackungen'=>array(
                                                                                        'rootTag'=>'verpackungen',
                                                                                        'rowTag'=>'verpackung',
                                                                                        'idColumn'=>'id',
                                                                                        'elements'=>array(
                                                                                                'verp_id',
                                                                                                'verp_stk',
                                                                                                'verp_name'
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
//$domxml->encoding="UFT-8";

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
$domxml->save("D230.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
