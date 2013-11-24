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

$sql = "select";
$sql.= "    dkopf.Kunde as kunde,";
$sql.= "    dkopf.Teil as teil,";
$sql.= "    dkopf.Teilbez as teilbez,";
$sql.= "    dkopf.Gew as gew,";
$sql.= "    dkopf.BrGew as brgew,";
$sql.= "    DATE_FORMAT(dkopf.stamp,'%d.%m.%Y') as apl_stamp,";
$sql.= "    DATE_FORMAT(max(drueck.stamp),'%d.%m.%Y') as drueck_stamp,";
$sql.= "    dpos.`TaetNr-Aby` as abgnr,";
$sql.= "    `dtaetkz-abg`.dtaetkz,";
$sql.= "    `dtaetkz-abg`.Stat_Nr as statnr,";
$sql.= "    dpos.`VZ-min-kunde` as vzkd_alt,";
$sql.= "    round(round(dkopf.Gew*0.0533,4)/$minpreis_alt,4) as vzkd_alt_ber,";
$sql.= "    if((`dtaetkz-abg`.Stat_Nr='S0041') or ((`dtaetkz-abg`.Stat_Nr='S0061') and (dpos.`TaetNr-Aby`<2000) and (dpos.`TaetNr-Aby`<>59)),round((dpos.`VZ-min-kunde`*$minpreis_alt)/$minpreis_neu,4),dpos.`VZ-min-kunde`) as vzkd_neu,";
$sql.= "    if((`dtaetkz-abg`.Stat_Nr='S0041') or ((`dtaetkz-abg`.Stat_Nr='S0061') and (dpos.`TaetNr-Aby`<2000) and (dpos.`TaetNr-Aby`<>59)),round((round(round(dkopf.Gew*0.0533,4)/$minpreis_alt,4)*$minpreis_alt)/$minpreis_neu,4),round(round(dkopf.Gew*0.0533,4)/$minpreis_alt,4)) as vzkd_neu_ber,";
$sql.= "    round(dpos.`VZ-min-kunde`*$minpreis_alt,4) as preis_alt,";
$sql.= "    round(round(round(dkopf.Gew*0.0533,4)/$minpreis_alt,4)*$minpreis_alt,4) as preis_alt_ber,";
$sql.= "    round(if((`dtaetkz-abg`.Stat_Nr='S0041') or ((`dtaetkz-abg`.Stat_Nr='S0061') and (dpos.`TaetNr-Aby`<2000) and (dpos.`TaetNr-Aby`<>59)),round((dpos.`VZ-min-kunde`*$minpreis_alt)/$minpreis_neu,4)*$minpreis_neu,dpos.`VZ-min-kunde`*$minpreis_neu),4) as preis_neu,";
$sql.= "    round(if((`dtaetkz-abg`.Stat_Nr='S0041') or ((`dtaetkz-abg`.Stat_Nr='S0061') and (dpos.`TaetNr-Aby`<2000) and (dpos.`TaetNr-Aby`<>59)),round((round(round(dkopf.Gew*0.0533,4)/$minpreis_alt,4)*$minpreis_alt)/$minpreis_neu,4)*$minpreis_neu,round(round(dkopf.Gew*0.0533,4)/$minpreis_alt,4)*$minpreis_neu),4) as preis_neu_ber,";
$sql.= "    round(if(dkopf.Gew<>0,round(dpos.`VZ-min-kunde`*$minpreis_alt,4)/dkopf.Gew,0),5) as koef_gew_alt,";
$sql.= "    round(if(dkopf.Gew<>0,round(round(round(dkopf.Gew*0.0533,4)/$minpreis_alt,4)*$minpreis_alt,4)/dkopf.Gew,0),5) as koef_gew_alt_ber,";
$sql.= "    round(if(dkopf.Gew<>0,round(if((`dtaetkz-abg`.Stat_Nr='S0041') or ((`dtaetkz-abg`.Stat_Nr='S0061') and (dpos.`TaetNr-Aby`<2000) and (dpos.`TaetNr-Aby`<>59)),round((dpos.`VZ-min-kunde`*$minpreis_alt)/$minpreis_neu,4)*$minpreis_neu,dpos.`VZ-min-kunde`*$minpreis_neu),4)/dkopf.Gew,0),5) as koef_gew_neu,";
$sql.= "    round(if(dkopf.Gew<>0,round(if((`dtaetkz-abg`.Stat_Nr='S0041') or ((`dtaetkz-abg`.Stat_Nr='S0061') and (dpos.`TaetNr-Aby`<2000) and (dpos.`TaetNr-Aby`<>59)),round((round(round(dkopf.Gew*0.0533,4)/$minpreis_alt,4)*$minpreis_alt)/$minpreis_neu,4)*$minpreis_neu,round(round(dkopf.Gew*0.0533,4)/$minpreis_alt,4)*$minpreis_neu),4)/dkopf.Gew,0),5) as koef_gew_neu_ber,";
$sql.= "    round(if(dkopf.BrGew<>0,round(dpos.`VZ-min-kunde`*$minpreis_alt,4)/dkopf.BrGew,0),5) as koef_brgew_alt,";
$sql.= "    round(if(dkopf.BrGew<>0,round(if((`dtaetkz-abg`.Stat_Nr='S0041') or ((`dtaetkz-abg`.Stat_Nr='S0061') and (dpos.`TaetNr-Aby`<2000) and (dpos.`TaetNr-Aby`<>59)),round((dpos.`VZ-min-kunde`*$minpreis_alt)/$minpreis_neu,4)*$minpreis_neu,dpos.`VZ-min-kunde`*$minpreis_neu),4)/dkopf.BrGew,0),5) as koef_brgew_neu";
$sql.= " from";
$sql.= "    dkopf";
$sql.= " join ";
$sql.= "    dpos on dpos.Teil=dkopf.Teil";
$sql.= " join";
$sql.= "    `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby`";
$sql.= " left join";
$sql.= "    drueck on drueck.Teil=dkopf.Teil";
$sql.= " where";
$sql.= "    dkopf.kunde = 130";
$sql.= "    and dpos.`VZ-min-kunde`<>0";
$sql.= "    and `dtaetkz-abg`.dtaetkz<>'F'";
// jen aktualni dily
$sql.= "    and dkopf.teilbez not like 'ALT_%'";
// jen ALT_ dily
//$sql.= "    and dkopf.teilbez like 'ALT_%'";
$sql.= " group by";
$sql.= "    dkopf.Teil,";
$sql.= "    dpos.`TaetNr-Aby`";
$sql.= " order by";
$sql.= "    dkopf.Teil,";
$sql.= "    dpos.`TaetNr-Aby`";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'T003',
		'idColumn'=>'teil',
		'rowTag'=>'teil1',
		'elements'=>array(
                    'kunde',
                    'teil',
                    'teilbez',
                    'gew',
                    'brgew',
                    'apl_stamp',
                    'drueck_stamp',
                    'teatigkeiten'=>array(
                        'rootTag'=>'taetigkeiten',
                        'idColumn'=>'abgnr',
                        'rowTag'=>'taetigkeit',
                        'elements'=>array(
                            'abgnr',
                            'dtaetkz',
                            'statnr',
                            'vzkd_alt',
                            'vzkd_alt_ber',
                            'vzkd_neu',
                            'vzkd_neu_ber',
                            'preis_alt',
                            'preis_alt_ber',
                            'preis_neu',
                            'preis_neu_ber',
                            'koef_gew_alt',
                            'koef_gew_alt_ber',
                            'koef_brgew_alt',
                            'koef_gew_neu',
                            'koef_gew_neu_ber',
                            'koef_brgew_neu',
                        )
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
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("T003.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
