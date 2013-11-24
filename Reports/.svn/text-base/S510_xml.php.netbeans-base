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
$views=array("v_repkosten","v_etkosten");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     dreparaturkopf.persnr_ma";
$pt.="     ,dreparaturkopf.invnummer";
$pt.="     ,dreparatur_anlagen.anlage_beschreibung";
$pt.="     ,sum(dreparaturkopf.repzeit*5) as rep_kosten";
$pt.=" from dreparaturkopf";
$pt.=" join dreparatur_geraete on dreparatur_geraete.invnummer=dreparaturkopf.invnummer";
$pt.=" join dreparatur_anlagen on dreparatur_anlagen.anlage_id=dreparatur_geraete.anlage_id";
$pt.=" where";
$pt.="     dreparaturkopf.datum between '$von' and '$bis'";
$pt.=" group by";
$pt.="     dreparaturkopf.persnr_ma,";
$pt.="     dreparaturkopf.invnummer";
//
//echo $pt."<br>";
$db->query($pt);
//
$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     dreparaturkopf.persnr_ma";
$pt.="     ,dreparaturkopf.invnummer";
$pt.="     ,sum(";
$pt.=" if(";
$pt.="             dreparatur_ersatzteiltypen.typ='ET',";
$pt.="             if(et_alt>0,`eink-artikel`.`art-vr-preis`*0.4,`eink-artikel`.`art-vr-preis`)*dreparaturpos.anzahl";
$pt.="             ,0";
$pt.="             )";
$pt.="         ) as et_kosten";
$pt.=" from dreparaturkopf";
$pt.=" join dreparatur_geraete on dreparatur_geraete.invnummer=dreparaturkopf.invnummer";
$pt.=" join dreparaturpos on dreparaturpos.reparatur_id=dreparaturkopf.id";
$pt.=" join dreparatur_et on convert(dreparaturpos.artnr,char)=convert(dreparatur_et.artnr,char) and dreparatur_et.anlage_id=dreparatur_geraete.anlage_id";
$pt.=" join `eink-artikel` on convert(`eink-artikel`.`art-nr`,char)=convert(dreparatur_et.artnr,char)";
$pt.=" join dreparatur_anlagen on dreparatur_anlagen.anlage_id=dreparatur_et.anlage_id";
$pt.=" join dreparatur_ersatzteiltypen on dreparatur_ersatzteiltypen.ersatzteiltyp_id=dreparatur_et.et_typ_id";
$pt.=" where";
$pt.=" dreparaturkopf.datum between '$von' and '$bis'";
$pt.=" group by";
$pt.="     dreparaturkopf.persnr_ma,";
$pt.="     dreparaturkopf.invnummer";

//echo $pt."<br>";
$db->query($pt);
//
//// provedu dotaz nad vytvorenymi pohledy
$v_repkosten=$pcip.$views[0];
$v_etkosten=$pcip.$views[1];

$sql = "select";
$sql.= " $v_repkosten.persnr_ma,";
$sql.= " dpers.name,dpers.vorname,";
$sql.= " $v_repkosten.invnummer,";
$sql.= " $v_repkosten.anlage_beschreibung,";
$sql.= " $v_repkosten.rep_kosten,";
$sql.= " if($v_etkosten.et_kosten is null,0,$v_etkosten.et_kosten) as et_kosten";
$sql.= " from";
$sql.= " $v_repkosten";
$sql.= " left join";
$sql.= " $v_etkosten";
$sql.= " on";
$sql.= " $v_repkosten.persnr_ma=$v_etkosten.persnr_ma and $v_repkosten.invnummer=$v_etkosten.invnummer";
$sql.= " join dpers on dpers.persnr=$v_repkosten.persnr_ma";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'S510',
		'idColumn'=>'persnr_ma',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr_ma',
                    'name',
                    'vorname',
                    'maschinen'=>array(
                        'rootTag'=>'maschinen',
                        'idColumn'=>'invnummer',
                        'rowTag'=>'machine',
                        'elements'=>array(
                            'invnummer',
                            'anlage_beschreibung',
                            'rep_kosten',
                            'et_kosten',
                        ),
                    )
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
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S510.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
