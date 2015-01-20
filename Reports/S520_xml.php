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
$pcip=get_pc_ip();

// vytvorim si nekolik pohledu
$views=array("v_repararuren","v_leistung");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt = "create view $viewname as ";
$pt.= " select";
$pt.= "     dreparaturkopf.persnr_ma";
$pt.= "     ,dpers.name,dpers.vorname";
$pt.= "    ,dreparaturkopf.id";
$pt.= "    ,dreparaturkopf.invnummer";
$pt.= "     ,dreparatur_anlagen.anlage_beschreibung";
$pt.= "     ,dreparaturkopf.repzeit*5 as rep_kosten";
$pt.= "     ,dreparaturkopf.repzeit";
$pt.= "     ,DATE_FORMAT(dreparaturkopf.datum,'%d.%m.%Y') as datum";
$pt.= "     ,dreparaturkopf.persnr_reparatur";
$pt.= "     ,CONVERT(dreparaturpos.artnr,char) as artnr";
$pt.= "     ,CONCAT(`eink-artikel`.`art-name1`,' - ',`eink-artikel`.`art-name2`) as artname";
$pt.= "     ,if(`eink-artikel`.`art-vr-preis` is null,0,`eink-artikel`.`art-vr-preis`) as preis";
$pt.= "     ,if(dreparaturpos.anzahl is null,0,dreparaturpos.anzahl) as anzahl";
$pt.= "     ,if(dreparaturpos.et_alt is null,0,dreparaturpos.et_alt) as et_alt";
$pt.= "     ,if(dreparaturpos.bemerkung is null,'',dreparaturpos.bemerkung) as bemerkung";
$pt.= " from dreparaturkopf";
$pt.= " join dpers on dpers.persnr=dreparaturkopf.persnr_ma";
$pt.= " join dreparatur_geraete on dreparatur_geraete.invnummer=dreparaturkopf.invnummer";
$pt.= " join dreparatur_anlagen on dreparatur_anlagen.anlage_id=dreparatur_geraete.anlage_id";
$pt.= " left join dreparaturpos on dreparaturpos.reparatur_id=dreparaturkopf.id";
$pt.= " left join `eink-artikel` on CONVERT(`eink-artikel`.`art-nr`,char)=convert(dreparaturpos.artnr,char)";
$pt.= " where";
$pt.= "     dreparaturkopf.datum between '$von' and '$bis'";
$pt.= "     and dreparaturkopf.persnr_ma between '$persvon' and '$persbis'";
$pt.= "     and dreparaturkopf.invnummer between '$invnrvon' and '$invnrbis'";
if($reportTypPersNr===TRUE){
$pt.= " order by";
$pt.= "     dreparaturkopf.persnr_ma,";
$pt.= "     dreparaturkopf.invnummer,";
$pt.= "     dreparaturkopf.datum";
}
else{
 $pt.= " order by";
 $pt.= "     dreparaturkopf.invnummer,";
 $pt.= "     dreparaturkopf.datum";
}
if($bPraemien){
    $db->query($pt);
//    echo "<br>".$pt;
}


$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt = "create view $viewname as ";
$pt.=" select persnr,";
$pt.="     sum(if(`dtaetkz-abg`.Stat_Nr='S0011',if(auss_typ=4,`VZ-SOLL`*(`Stück`+`Auss-Stück`),`VZ-SOLL`*(`Stück`)),0)) as sumvzkd_11,";
$pt.="     sum(if(`dtaetkz-abg`.Stat_Nr='S0051',if(auss_typ=4,`VZ-SOLL`*(`Stück`+`Auss-Stück`),`VZ-SOLL`*(`Stück`)),0)) as sumvzkd_51";
$pt.=" from drueck";
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
$pt.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
$pt.=" where ";
$pt.="     daufkopf.kunde<>355 and";
$pt.="     datum between '$von' and '$bis'";
$pt.="     and persnr between $persvon and $persbis";
$pt.=" group by persnr";
$pt.=" having (sumvzkd_11<>0 or sumvzkd_51<>0)";
if($bPraemien){
    $db->query($pt);
//    echo "<br>".$pt;
}

$v_reparaturen = $pcip.$views[0];
$v_leistung = $pcip.$views[1];



if (!$bPraemien) {
    $sql.= " select";
    $sql.= "     dreparaturkopf.persnr_ma";
    $sql.= "     ,dpers.name,dpers.vorname";
    $sql.= "    ,dreparaturkopf.id";
    $sql.= "    ,dreparaturkopf.invnummer";
    $sql.= "     ,dreparatur_anlagen.anlage_beschreibung";
    $sql.= "     ,dreparaturkopf.repzeit*5 as rep_kosten";
    $sql.= "     ,dreparaturkopf.repzeit";
    $sql.= "     ,DATE_FORMAT(dreparaturkopf.datum,'%d.%m.%Y') as datum";
    $sql.= "     ,dreparaturkopf.persnr_reparatur";
    $sql.= "     ,CONVERT(dreparaturpos.artnr,char) as artnr";
    $sql.= "     ,CONCAT(`eink-artikel`.`art-name1`,' - ',`eink-artikel`.`art-name2`) as artname";
    $sql.= "     ,if(`eink-artikel`.`art-vr-preis` is null,0,`eink-artikel`.`art-vr-preis`) as preis";
    $sql.= "     ,if(dreparaturpos.anzahl is null,0,dreparaturpos.anzahl) as anzahl";
    $sql.= "     ,if(dreparaturpos.et_alt is null,0,dreparaturpos.et_alt) as et_alt";
    $sql.= "     ,if(dreparaturpos.bemerkung is null,'',dreparaturpos.bemerkung) as bemerkung";
    $sql.= " from dreparaturkopf";
    $sql.= " join dpers on dpers.persnr=dreparaturkopf.persnr_ma";
    $sql.= " join dreparatur_geraete on dreparatur_geraete.invnummer=dreparaturkopf.invnummer";
    $sql.= " join dreparatur_anlagen on dreparatur_anlagen.anlage_id=dreparatur_geraete.anlage_id";
    $sql.= " left join dreparaturpos on dreparaturpos.reparatur_id=dreparaturkopf.id";
    $sql.= " left join `eink-artikel` on CONVERT(`eink-artikel`.`art-nr`,char)=convert(dreparaturpos.artnr,char)";
    $sql.= " where";
    $sql.= "     dreparaturkopf.datum between '$von' and '$bis'";
    $sql.= "     and dreparaturkopf.persnr_ma between '$persvon' and '$persbis'";
    $sql.= "     and dreparaturkopf.invnummer between '$invnrvon' and '$invnrbis'";
    if($bET)
    $sql.= "     and CONVERT(dreparaturpos.artnr,char) like '$et'";

    if ($reportTypPersNr === TRUE) {
	$sql.= " order by";
	$sql.= "     dreparaturkopf.persnr_ma,";
	$sql.= "     dreparaturkopf.invnummer,";
	$sql.= "     dreparaturkopf.datum";
    } else {
	$sql.= " order by";
	$sql.= "     dreparaturkopf.invnummer,";
	$sql.= "     dreparaturkopf.datum";
    }
}
 else {
    // pro vypocet premii
    $sql = " select ";
    $sql.=" dpers.PersNr as persnr_ma,";
    $sql.=" dpers.name,";
    $sql.=" dpers.vorname,";
    $sql.=" $v_reparaturen.id,";
    $sql.=" $v_reparaturen.invnummer,";
    $sql.=" $v_reparaturen.anlage_beschreibung,";
    $sql.=" $v_reparaturen.rep_kosten,";
    $sql.=" $v_reparaturen.repzeit,";
    $sql.=" $v_reparaturen.datum,";
    $sql.=" $v_reparaturen.persnr_reparatur,";
    $sql.=" $v_reparaturen.artnr,";
    $sql.=" $v_reparaturen.artname,";
    $sql.=" $v_reparaturen.preis,";
    $sql.=" $v_reparaturen.anzahl,";
    $sql.=" $v_reparaturen.et_alt,";
    $sql.=" $v_reparaturen.bemerkung,";
    $sql.=" if($v_leistung.sumvzkd_11 is null,0,$v_leistung.sumvzkd_11) as vzkd_11,";
    $sql.=" if($v_leistung.sumvzkd_51 is null,0,$v_leistung.sumvzkd_51) as vzkd_51";
    $sql.=" from dpers";
    $sql.=" left join $v_leistung on $v_leistung.persnr=dpers.PersNr";
    $sql.=" left join $v_reparaturen on $v_reparaturen.persnr_ma=dpers.PersNr";
    $sql.=" where ";
//    $sql.=" (dpers.austritt is null or austritt<eintritt)";
//    $sql.=" and";
//    $sql.=" (dpers.dpersstatus='MA')";
//    $sql.=" and";
//    $sql.=" (dpers.kor=0)";
//    $sql.=" and";
    $sql.=" ($v_leistung.persnr is not null or $v_reparaturen.persnr_ma is not null)";
    $sql.=" order by dpers.PersNr";
}
//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
if ($reportTypPersNr === TRUE) {
    if ($bPraemien) {
	$options = array(
	    'encoder' => false,
	    'rootTag' => 'S520',
	    'idColumn' => 'persnr_ma',
	    'rowTag' => 'person',
	    'elements' => array(
		'persnr_ma',
		'name',
		'vorname',
		'vzkd_11',
		'vzkd_51',
		'maschinen' => array(
		    'rootTag' => 'maschinen',
		    'idColumn' => 'id',
		    'rowTag' => 'machine',
		    'elements' => array(
			'invnummer',
			'anlage_beschreibung',
			'rep_kosten',
			'repzeit',
			'datum',
			'persnr_reparatur',
			'positionen' => array(
			    'rootTag' => 'positionen',
			    'idColumn' => 'artnr',
			    'rowTag' => 'et',
			    'elements' => array(
				'artnr',
				'artname',
				'preis',
				'anzahl',
				'bemerkung',
				'et_alt'
			    ),
			),
		    ),
		)
	    ),
	);
    } else {
	$options = array(
	    'encoder' => false,
	    'rootTag' => 'S520',
	    'idColumn' => 'persnr_ma',
	    'rowTag' => 'person',
	    'elements' => array(
		'persnr_ma',
		'name',
		'vorname',
		'maschinen' => array(
		    'rootTag' => 'maschinen',
		    'idColumn' => 'id',
		    'rowTag' => 'machine',
		    'elements' => array(
			'invnummer',
			'anlage_beschreibung',
			'rep_kosten',
			'repzeit',
			'datum',
			'persnr_reparatur',
			'positionen' => array(
			    'rootTag' => 'positionen',
			    'idColumn' => 'artnr',
			    'rowTag' => 'et',
			    'elements' => array(
				'artnr',
				'artname',
				'preis',
				'anzahl',
				'bemerkung',
				'et_alt'
			    ),
			),
		    ),
		)
	    ),
	);
    }
} else {
    $options = array(
	'encoder' => false,
	'rootTag' => 'S520',
	'idColumn' => 'id',
	'rowTag' => 'machine',
	'elements' => array(
	    'invnummer',
	    'anlage_beschreibung',
	    'rep_kosten',
	    'repzeit',
	    'datum',
	    'persnr_reparatur',
	    'persnr_ma',
	    'name',
	    'vorname',
	    'positionen' => array(
		'rootTag' => 'positionen',
		'idColumn' => 'artnr',
		'rowTag' => 'et',
		'elements' => array(
		    'artnr',
		    'artname',
		    'preis',
		    'anzahl',
		    'bemerkung',
		    'et_alt'
		),
	    ),
	),
    );
}

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
//	$db->query($sql);
	//echo $sql."<br>";
}


//$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S520.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
