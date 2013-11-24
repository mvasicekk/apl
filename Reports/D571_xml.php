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
    $sql.="bemerk,";
    $sql.="name1,";
    $sql.="teillang,";
    $sql.="stk_pro_gehaenge as spg,";
    $sql.="dteildokument.id as dok_id,";
    $sql.="dteildokument.musterplatz,";
    $sql.="DATE_FORMAT(dteildokument.einlag_datum,'%d.%m.%y') as einlag_datum,";
    $sql.="dteildokument.doku_nr,";
    $sql.="dteildokument.freigabe_vom,";
    $sql.="DATE_FORMAT(dteildokument.freigabe_am,'%d.%m.%y') as freigabe_am";
    $sql.=" from dkopf";
    $sql.=" join dksd on dkopf.kunde=dksd.kunde";
    $sql.=" left join dteildokument on dteildokument.teil=dkopf.teil";
    $sql.=" where ((dkopf.kunde='$kunde'))";
    if ($teillangsort != 0)
        $sql.=" order by dkopf.teillang,dteildokument.einlag_datum desc,dteildokument.doku_nr asc";
    else
        $sql.=" order by dkopf.teil,dteildokument.einlag_datum desc,dteildokument.doku_nr asc";
}
else {
    $sql = "select";
    $sql.=" dkopf.kunde,";
    $sql.="dkopf.teil,";
    $sql.="dkopf.teilbez,";
    $sql.="gew,";
    $sql.="brgew,";
    $sql.="bemerk,";
    $sql.="name1,";
    $sql.="teillang,";
    $sql.="stk_pro_gehaenge as spg,";
    $sql.="dteildokument.id as dok_id,";
    $sql.="dteildokument.musterplatz,";
    $sql.="DATE_FORMAT(dteildokument.einlag_datum,'%d.%m.%y') as einlag_datum,";
    $sql.="dteildokument.doku_nr,";
    $sql.="dteildokument.freigabe_vom,";
    $sql.="DATE_FORMAT(dteildokument.freigabe_am,'%d.%m.%y') as freigabe_am";
//    $sql.="max(aufdat) as letztdatum ";
    $sql.=" from dauftr join daufkopf using(auftragsnr)";
    $sql.=" join dkopf on dauftr.teil=dkopf.teil";
    $sql.=" left join dteildokument on dteildokument.teil=dkopf.teil";
    $sql.=" join dksd on daufkopf.kunde=dksd.kunde";
    $sql.=" where ((daufkopf.aufdat between '$datumvom' and '$datumbis') and (dkopf.kunde='$kunde'))";
    if ($teillangsort != 0)
        $sql.=" order by dkopf.teillang,dteildokument.einlag_datum desc,dteildokument.doku_nr asc";
    else
        $sql.=" order by dkopf.teil,dteildokument.einlag_datum desc,dteildokument.doku_nr asc";
}


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
    'encoder' => false,
    'rootTag' => 'D571',
    'idColumn' => 'kunde',
    'rowTag' => 'kunde',
    'elements' => array(
	'kundenr' => 'kunde',
	'name1',
	'teile' => array(
	    'rootTag' => 'teile',
	    'idColumn' => 'teil',
	    'rowTag' => 'teil',
	    'elements' => array(
		'teilnr' => 'teil',
		'teilbez',
		'gew',
		'brgew',
		'bemerk',
		// pridano  2010-11-01 pozadavek rk
		// rb predpokladal, ze uz je to hotovo
		'spg',
		'teillang',
		'dokumente' => array(
		    'rootTag' => 'dokumente',
		    'idColumn' => 'dok_id',
		    'rowTag' => 'dok',
		    'elements' => array(
			'teilnr' => 'teil',
			'teilbez',
			'gew',
			'brgew',
			'bemerk',
			// pridano  2010-11-01 pozadavek rk
			// rb predpokladal, ze uz je to hotovo
			'spg',
			'teillang',
			'musterplatz',
			'einlag_datum',
			'doku_nr',
			'freigabe_vom',
			'freigabe_am',
		    ),
		),
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
$domxml->save("D571.xml");
?>
