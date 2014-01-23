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

// podrobny vyber
// 
// vytvorim si nekolik pohledu
$pcip=get_pc_ip();

$datebis = date('Y-m-d');

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
$sql_5 .=" order by einlager_datum desc,dteildokument.doku_nr asc";

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

$sql_4=" select drueck_id,drueck.auftragsnr,drueck.teil,taetnr as tat,";
//$S310_pal_view.fremdauftr,$S310_pal_view.fremdpos,";
$sql_4.=" drueck.schicht,drueck.oe,DATE_FORMAT(datum,'%d.%m.%Y') as datum,drueck.persnr,name, ";
$sql_4.=" `pos-pal-nr` as pal,`stück` as stk,`auss-stück` as auss_stk,auss_typ,`vz-soll` as vzkd_stk, ";
$sql_4.=" `vz-ist` as vzaby_stk,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`) as vzkd, ";
$sql_4.=" if(auss_typ=4,(`stück`+`auss-stück`)*`vz-ist`,`stück`*`vz-ist`) as vzaby, ";
$sql_4.=" `verb-zeit` as verb,gew,brgew,`muster-platz` as muster_platz,if(`muster-vom` is not null,DATE_FORMAT(`muster-vom`,'%y-%m-%d'),`muster-vom`) as muster_vom ";
$sql_4.=" from drueck  join dpers using (persnr) ";
$sql_4.=" join dkopf on (drueck.teil=dkopf.teil) ";
//$sql_4.= " join $S310_pal_view on $S310_pal_view.auftragsnr=drueck.auftragsnr and $S310_pal_view.teil=drueck.teil and $S310_pal_view.pal=drueck.`pos-pal-nr` ";
$sql_4.=" where (";
//$sql_4.=" (drueck.auftragsnr between '$auftragsnr_von' and '$auftragsnr_bis')";
$sql_4.=" (drueck.teil='$teil')";
$sql_4.=" AND (drueck.datum between '$rmab' and '$datebis')";
$sql_4.=" )";
$sql_4.=" order by drueck.auftragsnr,drueck.teil,drueck.taetnr,drueck.datum,drueck.persnr";
//echo "sql=$sql"."<br>";


$options_4 = array(
    'encoder' => false,
    'rootTag' => 'S550_4',
    'idColumn' => 'auftragsnr',
    'rowTag' => 'auftrag',
    'elements' => array(
	'auftragsnr',
	'teile' => array(
	    'rootTag' => 'teile',
	    'rowTag' => 'teil',
	    'idColumn' => 'teil',
	    'elements' => array(
		'teilnr' => 'teil',
		'taetigkeiten' => array(
		    'rootTag' => 'taetigkeiten',
		    'rowTag' => 'taetigkeit',
		    'idColumn' => 'tat',
		    'elements' => array(
			'tatnr' => 'tat',
			'positionen' => array(
			    'rootTag' => 'positionen',
			    'rowTag' => 'position',
			    'idColumn' => 'drueck_id',
			    'elements' => array(
				'tat',
				'datum',
				'pal',
				'schicht',
				'oe',
				'persnr',
				'name',
				'stk',
				'auss_stk',
				'auss_typ',
				'vzkd_stk',
				'vzkd',
				'vzaby_stk',
				'vzaby',
				'verb'
			    ),
			),
		    ),
		),
	    ),
	),
    ),
);

$sql = " select";
$sql.= "     dkopf.Kunde as kunde,";
$sql.= "     dksd.preismin,";
$sql.= "     dkopf.Teil as teil,";
$sql.= "     dkopf.teillang as teillang,";
$sql.= "     dkopf.Teilbez as teilbez,";
$sql.= "     dkopf.Gew as gew,";
$sql.= "     dkopf.BrGew as brgew,";
$sql.= "     dkopf.jb_lfd_2,";
$sql.= "     dkopf.jb_lfd_1,";
$sql.= "     dkopf.jb_lfd_j,";
$sql.= "     dkopf.gut_lfd_1,";
$sql.= "     dkopf.preis_stk_gut,";
$sql.= "     dkopf.preis_stk_auss,";
$sql.= "     dkopf.kosten_stk_auss,";
$sql.= "     dkopf.`Muster-Platz` as musterplatz,";
$sql.= "     dkopf.fremdauftr_dkopf,";
$sql.= "     dkopf.`Muster-Freigabe-1` as freigabe1,";
$sql.= "     dkopf.`Muster-Freigabe-2` as freigabe2,";
$sql.= "     DATE_FORMAT(dkopf.`Muster-Freigabe-1-vom`,'%Y-%m-%d') as freigabe1vom,";
$sql.= "     DATE_FORMAT(dkopf.`Muster-Freigabe-2-vom`,'%Y-%m-%d') as freigabe2vom,";
$sql.= "     dpos.`TaetNr-Aby` as abgnr,";
$sql.= "     dpos.`VZ-min-kunde` as vzkd,";
$sql.= "     dpos.`VZ-min-kunde`*dkopf.jb_lfd_1 as bed_lfd_1_vzkd,";
$sql.= "     dpos.`VZ-min-kunde`*dkopf.jb_lfd_j as bed_lfd_j_vzkd,";
$sql.= "     dpos.`VZ-min-kunde`*dkopf.gut_lfd_1 as gut_lfd_1_vzkd,";
$sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin,4) as preis,";
$sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin*dkopf.jb_lfd_1,4) as bed_lfd_1_preis,";
$sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin*dkopf.jb_lfd_j,4) as bed_lfd_j_preis,";
$sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin*dkopf.gut_lfd_1,4) as gut_lfd_1_preis,";
$sql.= " `dtaetkz-abg`.`Name` as abgnr_name";
$sql.= " from dkopf";
$sql.= " join dksd on dksd.Kunde=dkopf.Kunde";
$sql.= " left join dpos on dpos.Teil=dkopf.Teil";
$sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby`";
$sql.= " where";
$sql.= "     dkopf.Teil='$teil'";
$sql.= "     and dpos.`TaetNr-Aby`>3";
$sql.= "     and dpos.`kz-druck`<>0";
$sql.= " order by";
$sql.= "     dkopf.Teil,";
$sql.= "     dpos.`TaetNr-Aby`";

$sql2.=" select";
$sql2.="     dkopf.teil,";
$sql2.="     dkopf.Teilbez as teilbez,";
$sql2.="     dkopf.Wst as wst,";
$sql2.="     dkopf.Gew as gew,";
$sql2.="     dkopf.BrGew as brgew,";
$sql2.="     dkopf.FA as fa,";
$sql2.="     dkopf.JB as jb,";
$sql2.="     dkopf.Reklamation as reklamation,";
$sql2.="     dkopf.`Letzte-Reklamation` as letzte_reklamation,";
$sql2.="     dkopf.`Muster-vom` as mustervom,";
$sql2.="     dkopf.`Muster-Platz` as musterplatz,";
$sql2.="     dkopf.`Muster-Freigabe-1` as mfreigabe1,";
$sql2.="     dkopf.`Muster-Freigabe-1-vom` as mfreigabe1vom,";
$sql2.="     dkopf.`Muster-Freigabe-2` as mfreigabe2,";
$sql2.="     dkopf.`Muster-Freigabe-2-vom` as mfreigabe2vom,";
$sql2.="     dkopf.bemerk,";
$sql2.="     dkopf.komplex,";
$sql2.="     dkopf.teillang,";
$sql2.="     dkopf.`Art Guseisen` as artguseisen,";
$sql2.="     dkopf.status,";
$sql2.="     dkopf.preis_stk_gut,";
$sql2.="     dkopf.preis_stk_auss,";
$sql2.="     dkopf.kosten_stk_auss,";
$sql2.="     dkopf.jb_lfd_2,";
$sql2.="     dkopf.jb_lfd_1,";
$sql2.="     dkopf.jb_lfd_j,";
$sql2.="     dkopf.gut_lfd_1,";
$sql2.="     dkopf.fremdauftr_dkopf,";
$sql2.="     dkopf.schwierigkeitsgrad_S11,";
$sql2.="     dkopf.schwierigkeitsgrad_S51,";
$sql2.="     dkopf.schwierigkeitsgrad_SO,";
$sql2.="     dkopf.verpackungmenge,";
$sql2.="     dkopf.restmengen_verw,";
$sql2.="     dkopf.stk_pro_gehaenge,";
$sql2.="     dpos.dpos_id,";			// pro zobrazeni vicenasobnych operaci.
$sql2.="     dpos.`TaetNr-Aby` as abgnr,";
$sql2.="     `dtaetkz-abg`.dtaetkz,";
$sql2.="     `dtaetkz-abg`.Stat_Nr as statnr,";
$sql2.="     dpos.KzGut as kzgut,";
$sql2.="     dpos.`kz-druck` as kzdruck,";
$sql2.="     dpos.`TaetBez-Aby-D` as tatbez_d,";
$sql2.="     dpos.`TaetBez-Aby-T` as tatbez_t,";
$sql2.="     dpos.`VZ-min-kunde` as vzkd,";
$sql2.="     dpos.`vz-min-aby` as vzaby,";
$sql2.="     dpos.lager_von,";
$sql2.="     dpos.lager_nach,";
$sql2.="     dpos.bedarf_typ";
$sql2.=" from dkopf";
$sql2.=" join dpos using(teil)";
$sql2.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby`";
$sql2.=" where";
$sql2.="     dkopf.teil='$teil'";
$sql2.=" order by";
$sql2.="     dpos.`TaetNr-Aby`,";
$sql2.="     dpos.stamp desc";

//echo "sql=$sql"."<br>";


$sql3="select dkopf.teil,dkopf.teilbez,dkopf.gew,";
$sql3.="DATE_FORMAT(dkopf.`muster-vom`,'%d.%m.%Y') as mustervom, ";
$sql3.=" dksd.preismin,dksd.name1, ";
$sql3.="sum(if(stat_nr='S0011' and `taetkz-nr`='P',`Stück`,0)) as pt_stk, ";
$sql3.="sum(if(stat_nr='S0011',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as pt_kdmin, ";
$sql3.="sum(if(stat_nr='S0011',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as pt_abymin, ";
$sql3.="sum(if(stat_nr='S0011',`verb-zeit`,0)) as pt_verb, ";
$sql3.="sum(if(stat_nr='S0041',Stück+`auss-Stück`,0)) as st_stk, ";
$sql3.="sum(if(stat_nr='S0041',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as st_kdmin, ";
$sql3.="sum(if(stat_nr='S0041',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as st_abymin, ";
$sql3.="sum(if(stat_nr='S0041',`verb-zeit`,0)) as st_verb, ";
$sql3.="sum(if(stat_nr='S0061',Stück+`auss-Stück`,0)) as g_stk, ";
$sql3.="sum(if(stat_nr='S0061',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as g_kdmin, ";
$sql3.="sum(if(stat_nr='S0061',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as g_abymin, ";
$sql3.="sum(if(stat_nr='S0061',`verb-zeit`,0)) as g_verb, ";
$sql3.="sum(if(stat_nr<>'S0061' and stat_nr<>'S0041' and stat_nr<>'S0011',Stück+`auss-Stück`,0)) as sonst_stk, ";
$sql3.="sum(if(stat_nr<>'S0061' and stat_nr<>'S0041' and stat_nr<>'S0011',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as sonst_kdmin, ";
$sql3.="sum(if(stat_nr<>'S0061' and stat_nr<>'S0041' and stat_nr<>'S0011',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as sonst_abymin, ";
$sql3.="sum(if(stat_nr<>'S0061' and stat_nr<>'S0041' and stat_nr<>'S0011',`verb-zeit`,0)) as sonst_verb, ";
$sql3.="sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`)) as sum_kdmin, ";
$sql3.="sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`)) as sum_abymin, ";
$sql3.="sum(`verb-zeit`) as sum_verb ";
$sql3.="from drueck join dkopf on (drueck.teil=dkopf.teil) ";
$sql3.="join dksd on (dksd.kunde=dkopf.kunde) ";
$sql3.="join `dtaetkz-abg` on (`dtaetkz-abg`.`abg-nr`=drueck.taetnr) ";
$sql3.="where ((drueck.datum between '$hitvon'  and '$hitbis') and (dkopf.teil='$teil') ) ";
$sql3.="group by dkopf.teil,dkopf.teilbez,dkopf.gew,dkopf.`muster-vom`,dksd.preismin,dksd.Name1";

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

function get_kdmin_zu_verb($record)
{
	if($record['sum_verb']!=0)
		return $record['sum_kdmin']/$record['sum_verb'];
	else
		return 0;
}

function get_abymin_zu_verb($record)
{
	if($record['sum_verb']!=0)
		return $record['sum_abymin']/$record['sum_verb'];
	else
		return 0;
}

function get_waehr_pro_tonne($record)
{
	if(($record['pt_stk']*$record['gew'])!=0)
	{
		$hodnota=($record['pt_kdmin']*$record['preismin'])/($record['pt_stk']*$record['gew'])*1000;
		return $hodnota;
	}	
	else
		return 0;
}

$options3 = array(
    'encoder' => false,
    'rootTag' => 'S550_3',
    'idColumn' => 'teil',
    'rowTag' => 't',
    'elements' => array(
	'teilnr'=>'teil',
	'teilbez',
	'gew',
	'mustervom',
	'preismin',
	'name1',
	'pt_stk',
	'pt_kdmin',
	'pt_abymin',
	'pt_verb',
	'st_stk',
	'st_kdmin',
	'st_abymin',
	'st_verb',
	'g_stk',
	'g_kdmin',
	'g_abymin',
	'g_verb',
	'sonst_stk',
	'sonst_kdmin',
	'sonst_abymin',
	'sonst_verb',
	'sum_kdmin',
	'sum_abymin',
	'sum_verb',
	'kdmin_zu_verb'=>'#get_kdmin_zu_verb();',
	'abymin_zu_verb'=>'#get_abymin_zu_verb();',
	'waehr_pro_tonne'=>'#get_waehr_pro_tonne();',
	),
);

$options2 = array(
    'encoder' => false,
    'rootTag' => 'S550_2',
    'idColumn' => 'teil',
    'rowTag' => 't',
    'elements' => array(
	'teil',
	'teilbez',
	'wst',
	'gew',
	'brgew',
	'fa',
	'jb',
	'reklamation',
	'letzte_reklamation',
	'mustervom',
	'musterplatz',
	'mfreigabe1',
	'mfreigabe1vom',
	'mfreigabe2',
	'mfreigabe2vom',
	'bemerk',
	'komplex',
	'teillang',
	'artguseisen',
	'status',
	'preis_stk_gut',
	'preis_stk_auss',
	'kosten_stk_auss',
	'jb_lfd_2',
	'jb_lfd_1',
	'jb_lfd_j',
	'gut_lfd_1',
	'fremdauftr_dkopf',
	'schwierigkeitsgrad_S11',
	'schwierigkeitsgrad_S51',
	'schwierigkeitsgrad_SO',
	'verpackungmenge',
	'restmengen_verw',
	'stk_pro_gehaenge',
	'tats' => array(
	    'rootTag' => 'tats',
	    'idColumn' => 'dpos_id',
	    'rowTag' => 'tat',
	    'elements' => array(
		'abgnr',
		'dtaetkz',
		'statnr',
		'kzgut',
		'kzdruck',
		'tatbez_d',
		'tatbez_t',
		'vzkd',
		'vzaby',
		'lager_von',
		'lager_nach',
		'bedarf_typ',
	    ),
	),
    ),
);

$options = array(
        'encoder' => false,
        'rootTag' => 'S550_1',
        'idColumn' => 'kunde',
        'rowTag' => 'kunde',
        'elements' => array(
            'kundenr'=>'kunde',
            'preismin',
            'teile' => array(
                'rootTag' => 'teile',
                'idColumn' => 'teil',
                'rowTag' => 'teil',
                'elements' => array(
                    'teilnr'=>'teil',
                    'teilbez',
                    'teillang',
                    'gew',
                    'brgew',
		    'jb_lfd_2',
		    'jb_lfd_1',
		    'jb_lfd_j',
		    'gut_lfd_1',
                    'preis_stk_gut',
                    'preis_stk_auss',
                    'kosten_stk_auss',
                    'musterplatz',
                    'fremdauftr_dkopf',
                    'freigabe1',
                    'freigabe2',
                    'freigabe1vom',
                    'freigabe2vom',
                    'taetigkeiten' => array(
                        'rootTag' => 'taetigkeiten',
                        'idColumn' => 'abgnr',
                        'rowTag' => 'tat',
                        'elements' => array(
                            'abgnr',
                            'abgnr_name',
                            'vzkd',
                            'preis',
                            'bed_lfd_1_vzkd',
                            'bed_lfd_j_vzkd',
			    'gut_lfd_1_vzkd',
                            'bed_lfd_1_preis',
                            'bed_lfd_j_preis',
			    'gut_lfd_1_preis',
                        ),
                    ),
                ),
            )
        ),
    );
// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml_1 = $query2xml->getXML($sql,$options);
$domxml_2 = $query2xml->getXML($sql2,$options2);
$domxml_3 = $query2xml->getXML($sql3,$options3);
$domxml_4 = $query2xml->getXML($sql_4,$options_4);
$domxml_5 = $query2xml->getXML($sql_5,$options_5);
//$domxml->encoding="windows-1250";

// pokusy s polem parameters
// rozkouskovat si ho na jednotlive polozky
// a vytvorim pole parametru
// TODO doplnit nejakou moznost zformatovani, napr. datum zadane ve formulari by melo nejak vypadat
// mozna by bylo lepsi resit to AJAXem uz pri zadavani do formulare
// ale uz pri generovani formulare budu muset pridat handlery podle planovaneho obsahu formulare

//echo "<pre>";var_dump($parameters);echo "</pre>";

foreach($parameters as $var=>$value)
{
//    echo "<br>var=$var, value=$value";
	// pokud nazev parametru obsahuje _label, budu hledat hodnotu parametru
	if(strpos($var,"_label"))
	{
		$p[$value]=$last_value;
	}
	$last_value=$value;
	//$promenne.=$var."=".$value."&";
}
//echo "<pre>";var_dump($p);echo "</pre>";
// v promenne p bych mel mit seznam parametru, pridam ho do XML jako node do domxml
//

$element=$domxml_1->createElement("parameters");
$parametry=$domxml_1->firstChild;
$parametry->appendChild($element);
$i=1;
foreach($p as $var=>$value)
{
	$poradinode=$domxml_1->createElement("N".$i);
	$labelnode=$domxml_1->createElement("label",$var);
	$valuenode=$domxml_1->createElement("value",$value);
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


//$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml_1->save("S847_1.xml");
$domxml_2->save("S847_2.xml");
$domxml_3->save("S847_3.xml");
$domxml_4->save("S847_4.xml");
$domxml_5->save("S847_5.xml");


//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
