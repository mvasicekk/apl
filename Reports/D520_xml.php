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


$sql="SELECT ";
$sql.=" d.`auftragsnr`,";
$sql.=" d.kzgut,";
$sql.=" d.`teil`,";
$sql.=" dk.teilbez,";
$sql.=" dk.teillang,";
$sql.=" dk.`muster-platz` as musterplatz,";
$sql.=" dk.gew,";
$sql.=" dk.brgew,";
$sql.=" dpos.`TaetBez-Aby-D` as tatbez_d,";
$sql.=" dpos.`TaetBez-Aby-T` as tatbez_t,";
$sql.=" dpos.lager_von,";
$sql.=" dpos.lager_nach,";
$sql.=" dpos.bedarf_typ,";
$sql.=" d.`mehrarb-kz` as tat,";
$sql.=" d.`abgnr`,";
$sql.=" d.`preis`, ";
$sql.=" d.`vzkd`, ";
$sql.=" d.`vzaby`, ";
$sql.=" sum(d.`stück`*d.preis) as sumpreis,";
$sql.=" sum(d.`stück`*d.vzkd) as sumvzkd,";
$sql.=" sum(d.`stück`*d.vzaby) as sumvzaby,";
$sql.=" sum(d.`stück`) as sumimportstk,";
$sql.=" sum(if(d.kzgut='G',d.`stück`*dk.gew,0)) as sumteilgew";
$sql.=" FROM dauftr d";
$sql.=" join dkopf dk on d.teil=dk.teil";
$sql.=" join dpos on d.teil=dpos.teil and d.abgnr=dpos.`taetnr-aby`";
//$sql.=" left join dpos on d.teil=dpos.teil";
$sql.=" WHERE d.`auftragsnr`='$import' AND dk.Teil=d.teil";
//$sql.=" WHERE d.`auftragsnr`='$import' AND dk.Teil=d.teil and (d.abgnr=dpos.`taetnr-aby` or dpos.`taetnr-aby`=3)";
$sql.=" GROUP BY d.`auftragsnr`, d.`teil`, d.`mehrarb-kz`,d.`abgnr`";
$sql.=" order by d.teil,d.abgnr";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry



$options = array(
					'encoder'=>false,
					'rootTag'=>'D520',
					'idColumn'=>'auftragsnr',
					'rowTag'=>'import',
					'elements'=>array(
						'importnr'=>'auftragsnr',
                        'teile'=>array(
                            'rootTag'=>'teile',
                            'rowTag'=>'teil',
                            'idColumn'=>'teil',
                            'elements'=>array(
                                'teilnr'=>'teil',
                                'teilbez',
                                'teillang',
                                'musterplatz',
                                'gew',
                                'brgew',
                                'positionen'=>array(
                                    'rootTag'=>'positionen',
                                    'rowTag'=>'position',
                                    'idColumn'=>'abgnr',
                                    'elements'=>array(
                                        'tat',
                                        'abgnr',
                                        'kzgut',
                                        'tatbez_d',
                                        'tatbez_t',
                                        'lager_von',
                                        'lager_nach',
                                        'bedarf_typ',
                                        'preis',
                                        'vzkd',
                                        'vzaby',
                                        'sumpreis',
                                        'sumvzkd',
                                        'sumvzaby',
                                        'sumimportstk',
                                        'sumteilgew',
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
$domxml->save("D520.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
