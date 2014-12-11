<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


$connectString = "mysql://".LOCAL_USER.":".LOCAL_PASS."@".LOCAL_HOST."/".LOCAL_DB;
$db = &DB::connect($connectString);

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu


$pcip=get_pc_ip();

if ($alleTeile === TRUE) {
    $sql = "select";
    $sql.=" dkopf.kunde,";
    $sql.="dkopf.teil,";
    $sql.="dkopf.teilbez,";
    $sql.="gew,";
    $sql.="brgew,";
    $sql.="DATE_FORMAT(`muster-vom`,'%d.%m.%y') as mustervom,";
    $sql.="`muster-platz` as musterplatz,";
    $sql.="`muster-freigabe-1` as musterfreigabe1,";
    $sql.="DATE_FORMAT(`muster-freigabe-1-vom`,'%d.%m.%y') as musterfreigabe1vom,";
    $sql.="`muster-freigabe-2` as musterfreigabe2,";
    $sql.="DATE_FORMAT(`muster-freigabe-2-vom`,'%d.%m.%y') as musterfreigabe2vom,";
    $sql.="bemerk,";
    $sql.="name1,";
    $sql.="teillang,";
    $sql.="stk_pro_gehaenge as spg";
    $sql.=" from dkopf";
    $sql.=" join dksd on dkopf.kunde=dksd.kunde";
    $sql.=" where ((dkopf.kunde='$kunde')) group by dkopf.kunde,dkopf.teil";
    if ($teillangsort != 0)
        $sql.=" order by dkopf.teillang";
    else
        $sql.=" order by dkopf.teil";
}
else {
    $sql = "select";
    $sql.=" dkopf.kunde,";
    $sql.="dkopf.teil,";
    $sql.="dkopf.teilbez,";
    $sql.="gew,";
    $sql.="brgew,";
    $sql.="DATE_FORMAT(`muster-vom`,'%d.%m.%y') as mustervom,";
    $sql.="`muster-platz` as musterplatz,";
    $sql.="`muster-freigabe-1` as musterfreigabe1,";
    $sql.="DATE_FORMAT(`muster-freigabe-1-vom`,'%d.%m.%y') as musterfreigabe1vom,";
    $sql.="`muster-freigabe-2` as musterfreigabe2,";
    $sql.="DATE_FORMAT(`muster-freigabe-2-vom`,'%d.%m.%y') as musterfreigabe2vom,";
    $sql.="bemerk,";
    $sql.="name1,";
    $sql.="teillang,";
    $sql.="stk_pro_gehaenge as spg,";
    $sql.="max(aufdat) as letztdatum ";
    $sql.=" from dauftr join daufkopf using(auftragsnr)";
    $sql.=" join dkopf using(teil)";
    $sql.=" join dksd on daufkopf.kunde=dksd.kunde";
    $sql.=" where ((daufkopf.aufdat between '$datumvom 00:00:00' and '$datumbis 23:59:59') and (dkopf.kunde='$kunde')) group by dkopf.kunde,dkopf.teil";
    if ($teillangsort != 0)
        $sql.=" order by dkopf.teillang";
    else
        $sql.=" order by dkopf.teil";
}


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
                    'encoder'=>false,
					'rootTag'=>'D570',
                    'idColumn'=>'kunde',
                    'rowTag'=>'kunde',
                    'elements'=>array(
                        'kundenr'=>'kunde',
                        'name1',
                        'teile'=>array(
                            'rootTag'=>'teile',
                            'idColumn'=>'teil',
                            'rowTag'=>'teil',
                            'elements'=>array(
                                'teilnr'=>'teil',
                                'teilbez',
                                'gew',
                                'brgew',
                                'mustervom',
                                'musterplatz',
                                'musterfreigabe1',
                                'musterfreigabe1vom',
                                'musterfreigabe2',
                                'musterfreigabe2vom',
                                'bemerk',
                                // pridano  2010-11-01 pozadavek rk
                                // rb predpokladal, ze uz je to hotovo
                                'spg',
                                'teillang'
                            ),
                          ),
                      )
                    );
//


								
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
$domxml->save("D570.xml");
?>
