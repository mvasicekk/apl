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

if($teil=='%'){
$sql=" select dauftr.`auftragsnr-exp` as export,dauftr.auftragsnr,dauftr.teil,dauftr.`pos-pal-nr` as pal,dauftr.`stk-exp` as exstk,dkopf.`Gew` as gew,'$termin' as termin ";
$sql.=" from dauftr";
$sql.=" join dkopf using(teil)";
$sql.=" where ((dauftr.`auftragsnr-exp`='$export') and (dauftr.`KzGut`='G') and (dauftr.`pos-pal-nr`)>0)";
$sql.=" order by export,dauftr.auftragsnr,dauftr.`pos-pal-nr`";
}
else{
    $sql=" select dauftr.`auftragsnr-exp` as export,dauftr.auftragsnr,dauftr.teil,dauftr.`pos-pal-nr` as pal,dauftr.`stk-exp` as exstk,dkopf.`Gew` as gew,'$termin' as termin ";
$sql.=" from dauftr";
$sql.=" join dkopf using(teil)";
$sql.=" where ((dauftr.`auftragsnr-exp`='$export') and (dauftr.`KzGut`='G') and (dauftr.`pos-pal-nr`)>0 and dauftr.teil like '$teil')";
$sql.=" order by export,dauftr.auftragsnr,dauftr.`pos-pal-nr`";
}

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry



$options = array(
					'encoder'=>false,
					'rootTag'=>'D64X',
					'idColumn'=>'export',
					'rowTag'=>'export',
					'elements'=>array(
						'exportnr'=>'export',
                        'importe'=>array(
                            'rootTag'=>'importe',
                            'idColumn'=>'auftragsnr',
                            'rowTag'=>'import',
                            'elements'=>array(
                                'importnr'=>'auftragsnr',
                                'paletten'=>array(
                                    'rootTag'=>'paletten',
                                    'idColumn'=>'pal',
                                    'rowTag'=>'pal',
                                    'elements'=>array(
                                        'exportnr'=>'export',
                                        'auftragsnr',
                                        'teil',
                                        'palnr'=>'pal',
                                        'gew',
                                        'exstk',
                                        'termin'
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
$domxml->save("D64X.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
