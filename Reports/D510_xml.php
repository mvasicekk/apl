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

// status pridat
$sql_5 .= " select";
$sql_5 .= "     dteildokument.id,";
$sql_5 .= "     dteildokument.doku_nr,";
$sql_5 .= "     dokumenttyp.doku_beschreibung,";
$sql_5 .= "     DATE_FORMAT(dteildokument.einlag_datum,'%d.%m.%Y') as einlager_datum,";
$sql_5 .= "     musterplatz,";
$sql_5 .= "     DATE_FORMAT(dteildokument.freigabe_am,'%d.%m.%Y') as freigabe_am,";
$sql_5 .= "     freigabe_vom";
$sql_5 .= " from dteildokument";
$sql_5 .= " join dokumenttyp on dokumenttyp.doku_nr=dteildokument.doku_nr";
$sql_5 .= " where";
$sql_5 .= "     teil='$teil'";
$sql_5 .= " order by einlager_datum desc,dteildokument.doku_nr asc";

$options_5 = array(
    'encoder' => false,
    'rootTag' => 'S550_5',
    'idColumn' => 'id',
    'rowTag' => 'dokument',
    'elements' => array(
	'doku_nr',
	'doku_beschreibung',
	'einlager_datum',
	'musterplatz',
	'freigabe_am',
	'freigabe_vom',
    ),
);

// vybrat i pole status, v pripade, ze bude == 'ALT' podbarvit hlavicku/cislo dilu cervene
$views=array("pt_D510_teil_letzter_auftrag");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select teil,daufkopf.auftragsnr,daufkopf.aufdat from daufkopf";
$pt.=" join dauftr using (auftragsnr) where ((teil='$teil')) order by aufdat desc limit 1";

//echo $pt."<br>";
$db->query($pt);

$pt_D510_teil_letzter_auftrag=$pcip.$views[0];

$sql=" SELECT dkopf.Teilbez, dkopf.Teil,dkopf.kunde,dkopf.`Art Guseisen` as age,";
$sql.=" dkopf.restmengen_verw,dkopf.fremdauftr_dkopf,";
$sql.=" dksd.name1,dksd.name2,dkopf.status, dkopf.verpackungmenge,dkopf.stk_pro_gehaenge,dpos.`kz-druck` as kzdruck,dpos.KzGut, dpos.`TaetNr-Aby` as tatnr,dpos.dpos_id,";
$sql.=" `dtaetkz-abg`.dtaetkz as tatkz, dpos.`TaetBez-Aby-D` as tatbez_d, dpos.`TaetBez-Aby-T` as tatbez_t,dpos.mittel,";
$sql.=" dpos.`VZ-min-kunde` as vzkd, dpos.`vz-min-aby` as vzaby, dkopf.Gew, dkopf.teillang, dkopf.`Muster-Platz` as musterplatz,";
$sql.=" DATE_FORMAT(dkopf.`muster-vom`,'%d.%m.%Y') as mustervom,dpos.lager_von, dpos.lager_nach, dpos.bedarf_typ,dpos.`kz-druck` as kzdruck,";
$sql.=" $pt_D510_teil_letzter_auftrag.auftragsnr, DATE_FORMAT($pt_D510_teil_letzter_auftrag.aufdat,'%d.%m.%Y') as aufdat, `dtaetkz-abg`.Stat_Nr, dkopf.BrGew";
//$sql.=" sum(if(`kz-druck`<>0,`vz-min-kunde`,0)) as sumvzkd_aktiv";
$sql.=" FROM (`dtaetkz-abg` INNER JOIN (dkopf INNER JOIN dpos ON dkopf.Teil = dpos.Teil)";
$sql.=" ON `dtaetkz-abg`.`abg-nr` = dpos.`TaetNr-Aby`)";
$sql.=" join dksd on dkopf.kunde=dksd.kunde";
$sql.=" LEFT JOIN $pt_D510_teil_letzter_auftrag ON dkopf.Teil = $pt_D510_teil_letzter_auftrag.teil";
$sql.=" WHERE (((dkopf.Teil)='$teil')) order by tatnr,dpos.stamp DESC";
//$sql.=" group by dkopf.kunde,dkopf.teil,dpos.`taetnr-aby`";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$domxml_5 = $query2xml->getXML($sql_5,$options_5);
$domxml_5->save("S847_5.xml");

$options = array(
    'encoder' => false,
    'rootTag' => 'D510',
    'idColumn' => 'Teil',
    'rowTag' => 'teil',
    'elements' => array(
        'teilnr' => 'Teil',
        'kunde',
        'name1',
        'name2',
        'Teilbez',
        'teillang',
        'status',   // nova promenna pro nastaveni flagu
        'musterplatz',
        'verpackungmenge',
        'stk_pro_gehaenge',
        'restmengen_verw',
        'fremdauftr_dkopf',
        'mustervom',
        'Gew',
        'auftragsnr',
        'aufdat',
        'BrGew',
        'age',
        'positionen' => array(
            'rootTag' => 'positionen',
            'idColumn' => 'dpos_id',
            'rowTag' => 'tat',
            'elements' => array(
                'kzdruck',
                'KzGut',
                'tatnr',
                'tatkz',
                'tatbez_d',
                'tatbez_t',
                'vzkd',
                'vzaby',
                'lager_von',
                'lager_nach',
                'bedarf_typ',
                'kzdruck',
		'mittel',
                'Stat_Nr'
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
//$domxml->save("D510.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
