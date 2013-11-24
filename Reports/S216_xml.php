<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";
require_once "../db.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu
$a = AplDB::getInstance();
$kunde = $a->getKundeFromAuftransnr(substr($gepl_von, 1));

$pcip=get_pc_ip();
$views=array("pt_S216_geplannt_kdminsoll","pt_S216_ohneEx_minuten_aus_DRUECK","pt_S216_planvzkd");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     daufkopf.kunde,";
$pt.="     `dtaetkz-abg`.Stat_Nr,";
$pt.="     dauftr.abgnr,";
$pt.="     sum(dauftr.`stück`*dauftr.VzKd) as vzkd_gesamt,";
$pt.="     sum(dauftr.`stück`*dkopf.Gew) as gew_gesamtabgnr,";
$pt.="     sum(if(dauftr.KzGut='G',dauftr.`stück`*dkopf.Gew,0)) as gew_gesamt";
$pt.=" from ";
$pt.="     dauftr";
$pt.=" join daufkopf on dauftr.auftragsnr=daufkopf.auftragsnr";
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
$pt.=" join dkopf on dkopf.Teil=dauftr.teil";
$pt.=" where";
$pt.="     (dauftr.`auftragsnr-exp` is null)";
$pt.="     and (dauftr.`pal-nr-exp` is null)";
$pt.="     and (daufkopf.kunde between '$kundevon' and '$kundebis')";
$pt.="     and (`dtaetkz-abg`.Stat_Nr between 'S0011' and 'S0081')";
$pt.=" group by";
$pt.="     daufkopf.kunde,";
$pt.="     `dtaetkz-abg`.Stat_Nr,";
$pt.="     dauftr.abgnr";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     daufkopf.kunde,";
$pt.="     `dtaetkz-abg`.Stat_Nr as statnr,";
$pt.="     drueck.taetnr as abgnr,";
$pt.="     sum(if(auss_typ=4,(drueck.`Stück`+`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`)) as sumvzkd_drueck";
$pt.=" from ";
$pt.="     drueck";
$pt.=" join dauftr on drueck.`pos-pal-nr` = dauftr.`pos-pal-nr` and drueck.TaetNr = dauftr.abgnr and drueck.`pos-pal-nr` = dauftr.`pos-pal-nr` and drueck.Teil = dauftr.Teil and drueck.AuftragsNr = dauftr.AuftragsNr";
$pt.=" join daufkopf on dauftr.auftragsnr=daufkopf.auftragsnr";
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
$pt.=" join dkopf on dkopf.Teil=dauftr.teil";
$pt.=" where";
$pt.="     (dauftr.`auftragsnr-exp` is null)";
$pt.="     and (dauftr.`pal-nr-exp` is null)";
$pt.="     and (daufkopf.kunde between '$kundevon' and '$kundebis')";
$pt.=" group by";
$pt.="     daufkopf.kunde,";
$pt.="     `dtaetkz-abg`.Stat_Nr,";
$pt.="     drueck.taetnr";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" kunde,statnr,vzkd";
$pt.=" from dispostatnrkunde";
//$pt.=" where datum='".date('Y-m-d')."'";

//echo $pt."<br>";
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$pt_S216_geplannt_kdminsoll=$pcip.$views[0];
$pt_S216_ohneEx_minuten_aus_DRUECK=$pcip.$views[1];
$pt_S216_planvzkd=$pcip.$views[2];

$sql=" SELECT ";
$sql.=" $pt_S216_geplannt_kdminsoll.kunde,";
$sql.=" $pt_S216_geplannt_kdminsoll.Stat_Nr as statnr,";
$sql.=" if($pt_S216_planvzkd.vzkd is null,0,$pt_S216_planvzkd.vzkd) as vzkdplan,";
$sql.=" $pt_S216_geplannt_kdminsoll.abgnr,";
$sql.=" $pt_S216_geplannt_kdminsoll.vzkd_gesamt,";
$sql.=" if($pt_S216_ohneEx_minuten_aus_DRUECK.sumvzkd_drueck is null,0,$pt_S216_ohneEx_minuten_aus_DRUECK.sumvzkd_drueck) as vzkd_bearbeitet,";
$sql.=" $pt_S216_geplannt_kdminsoll.gew_gesamtabgnr,";
$sql.=" $pt_S216_geplannt_kdminsoll.gew_gesamt";
$sql.=" from $pt_S216_geplannt_kdminsoll";
$sql.=" left join $pt_S216_ohneEx_minuten_aus_DRUECK on $pt_S216_ohneEx_minuten_aus_DRUECK.kunde=$pt_S216_geplannt_kdminsoll.kunde and $pt_S216_ohneEx_minuten_aus_DRUECK.statnr=$pt_S216_geplannt_kdminsoll.Stat_Nr and $pt_S216_ohneEx_minuten_aus_DRUECK.abgnr=$pt_S216_geplannt_kdminsoll.abgnr";
$sql.=" left join $pt_S216_planvzkd on $pt_S216_planvzkd.kunde=$pt_S216_geplannt_kdminsoll.kunde and $pt_S216_planvzkd.statnr=$pt_S216_geplannt_kdminsoll.Stat_Nr";


//echo "sql=$sql"."<br>";

$query2xml = XML_Query2XML::factory($db);
	
$options = array(
    'rootTag' => 'S216',
    'idColumn' => 'kunde',
    'rowTag' => 'kunde',
    'elements' => array(
	'kd'=>'kunde',
	'statistiken' => array(
	    'rootTag' => 'statistiken',
	    'rowTag' => 'stat',
	    'idColumn' => 'statnr',
	    'elements' => array(
		'statnr',
		'vzkdplan',
		'taetigkeiten' => array(
		    'rootTag' => 'taetigkeiten',
		    'rowTag' => 'tat',
		    'idColumn' => 'abgnr',
		    'elements' => array(
			'abgnr',
			'vzkd_gesamt',
			'vzkd_bearbeitet',
			'gew_gesamtabgnr',
			'gew_gesamt',
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
$domxml->save("S216.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
