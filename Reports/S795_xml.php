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
$views=array(
                "pt_S795_drueck_import_zeitpunkt",
                "pt_S795_kdminuten_import_in_exporten",
                "pt_S795_kdminuten_export_final",
                "pt_S795_drueck_import_vor_in_nach"
            );

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" daufkopf.kunde,";
$pt.=" drueck.`auftragsnr`,";
$pt.=" daufkopf.`aufdat`,";
$pt.=" sum(if(auss_typ=4,(drueck.`Stück`+drueck.`auss-stück`)*drueck.`vz-soll`,drueck.`stück`*drueck.`vz-soll`)) as sumvzkd";
$pt.=" from drueck";
$pt.=" join daufkopf on daufkopf.auftragsnr=drueck.`auftragsnr`";
$pt.=" where ((daufkopf.kunde between '$kunde_von' and '$kunde_bis') and (daufkopf.aufdat>='$auftr_von') and (drueck.datum<='$zeitpunkt'))";
$pt.=" group by daufkopf.kunde,drueck.auftragsnr,DATE_FORMAT(daufkopf.aufdat,'%Y%m%d')";
$db->query($pt);
//echo "<br>PT<br>$pt";

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";

$pt.=" as select dauftr.auftragsnr,";
$pt.=" dauftr.`auftragsnr-exp`,";
$pt.=" da2.ausliefer_datum,";
$pt.=" sum((`stk-exp`+auss4_stk_exp)*vzkd) as sumvzkdex";
$pt.=" from dauftr";
$pt.=" join daufkopf as da1";
$pt.=" on (da1.auftragsnr=dauftr.auftragsnr)";
$pt.=" join daufkopf as da2 on (da2.auftragsnr=dauftr.`auftragsnr-exp`)";
$pt.=" where ((da1.kunde between '$kunde_von' and '$kunde_bis') and (da1.aufdat>='$auftr_von') and (`mehrarb-kz`<>'F') and (`mehrarb-kz`<>'Lg') and (`mehrarb-kz`<>'Z') and (da2.ausliefer_datum<='$zeitpunkt'))";
$pt.=" group by dauftr.auftragsnr,dauftr.`auftragsnr-exp`,da2.ausliefer_datum";
$db->query($pt);
//echo "<br>PT<br>$pt";



$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" dauftr.auftragsnr,";
$pt.=" sum(if(da2.ausliefer_datum<'$rm_von',(`stk-exp`+auss4_stk_exp)*vzkd,0)) as sumvzkdex_vor,";
$pt.=" sum(if(da2.ausliefer_datum between '$rm_von' and '$rm_bis',(`stk-exp`+auss4_stk_exp)*vzkd,0)) as sumvzkdex_in,";
$pt.=" sum(if(da2.ausliefer_datum>'$rm_bis',(`stk-exp`+auss4_stk_exp)*vzkd,0)) as sumvzkdex_nach,";
$pt.=" sum(if(da2.ausliefer_datum is not null and da2.ausliefer_datum<='$zeitpunkt',(`stk-exp`+auss4_stk_exp)*vzkd,0)) as sumvzkdex_alle";
$pt.=" from dauftr";
$pt.=" join daufkopf as da1 on (da1.auftragsnr=dauftr.auftragsnr)";
$pt.=" join daufkopf as da2 on (da2.auftragsnr=dauftr.`auftragsnr-exp`)";
$pt.=" where ((da1.kunde between '$kunde_von' and '$kunde_bis') and (da1.aufdat>='$auftr_von') and (`mehrarb-kz`<>'F') and (`mehrarb-kz`<>'Z') and (`mehrarb-kz`<>'Lg'))";
$pt.=" group by dauftr.auftragsnr";
$db->query($pt);
//echo "<br>PT<br>$pt";

$viewname=$pcip.$views[3];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" drueck.auftragsnr,";
$pt.=" sum(if(datum<'$rm_von',if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-soll`,drueck.`stück`*`vz-soll`),0)) as sumvzkd_vor,";
$pt.=" sum(if(datum between '$rm_von' and '$rm_bis',if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-soll`,drueck.`stück`*`vz-soll`),0)) as sumvzkd_in,";
$pt.=" sum(if(datum>'$rm_bis',if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-soll`,drueck.`stück`*`vz-soll`),0)) as sumvzkd_nach";
$pt.=" from drueck";
$pt.=" join daufkopf on (daufkopf.auftragsnr=drueck.auftragsnr)";
$pt.=" where ((daufkopf.kunde between '$kunde_von' and '$kunde_bis') and (daufkopf.aufdat>='$auftr_von'))";
$pt.=" group by drueck.auftragsnr";
$db->query($pt);
//echo "<br>PT<br>$pt";

// provedu dotaz nad vytvorenymi pohledy
$pt_S795_drueck_import_zeitpunkt=$pcip.$views[0];
$pt_S795_kdminuten_import_in_exporten=$pcip.$views[1];
$pt_S795_kdminuten_export_final=$pcip.$views[2];
$pt_S795_drueck_import_vor_in_nach=$pcip.$views[3];


//exit;

$sql=" select";
$sql.=" $pt_S795_drueck_import_zeitpunkt.kunde,";
$sql.=" $pt_S795_drueck_import_zeitpunkt.auftragsnr,";
$sql.=" DATE_FORMAT($pt_S795_drueck_import_zeitpunkt.aufdat,'%d.%m.%Y') as aufdat,";
$sql.=" $pt_S795_drueck_import_zeitpunkt.sumvzkd,";
$sql.=" $pt_S795_drueck_import_vor_in_nach.sumvzkd_vor,";
$sql.=" $pt_S795_drueck_import_vor_in_nach.sumvzkd_in,";
$sql.=" $pt_S795_drueck_import_vor_in_nach.sumvzkd_nach,";
$sql.=" $pt_S795_kdminuten_import_in_exporten.`auftragsnr-exp`,";
$sql.=" DATE_FORMAT($pt_S795_kdminuten_import_in_exporten.ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,";
$sql.=" $pt_S795_kdminuten_import_in_exporten.sumvzkdex,";
$sql.=" if($pt_S795_kdminuten_export_final.sumvzkdex_vor is null,0,$pt_S795_kdminuten_export_final.sumvzkdex_vor) as sumvzkdex_vor,";
$sql.=" $pt_S795_kdminuten_export_final.sumvzkdex_in,";
$sql.=" $pt_S795_kdminuten_export_final.sumvzkdex_nach,";
$sql.=" sumvzkd_vor-if($pt_S795_kdminuten_export_final.sumvzkdex_vor is null,0,$pt_S795_kdminuten_export_final.sumvzkdex_vor) AS delta_vor,";
$sql.=" sumvzkd_in-if($pt_S795_kdminuten_export_final.sumvzkdex_in is null,0,$pt_S795_kdminuten_export_final.sumvzkdex_in) as delta_in,";
$sql.=" sumvzkd_nach-if($pt_S795_kdminuten_export_final.sumvzkdex_nach is null,0,$pt_S795_kdminuten_export_final.sumvzkdex_nach) AS delta_nach,";
$sql.=" $pt_S795_kdminuten_export_final.sumvzkdex_alle,";
$sql.=" sumvzkd_vor+sumvzkd_in AS im_vor_plus_in,";
$sql.=" sumvzkdex_vor+sumvzkdex_in AS ex_vor_plus_in";
$sql.=" FROM";
$sql.=" (($pt_S795_drueck_import_zeitpunkt LEFT JOIN $pt_S795_drueck_import_vor_in_nach ON $pt_S795_drueck_import_zeitpunkt.auftragsnr = $pt_S795_drueck_import_vor_in_nach.auftragsnr)";
$sql.=" LEFT JOIN $pt_S795_kdminuten_import_in_exporten ON $pt_S795_drueck_import_zeitpunkt.auftragsnr = $pt_S795_kdminuten_import_in_exporten.auftragsnr)";
$sql.=" LEFT JOIN $pt_S795_kdminuten_export_final ON $pt_S795_drueck_import_zeitpunkt.auftragsnr = $pt_S795_kdminuten_export_final.auftragsnr";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
	//echo $sql."<br>";
// tady se budou tisknout parametry

function fac1($record)
{
	if($record['sumvzaby']!=0)
		return $record['sumvzkd']/$record['sumvzaby'];
	else
		return 0;
}

function fac2($record)
{
	if($record['sumverb']!=0)
		return $record['sumvzkd']/$record['sumverb'];
	else
		return 0;
}

function eur_pro_tonne($record)
{
	$preismin=$record['preismin']/get_kurs($record['waehr'],$record['ausliefer_datum']);
	
	if($record['aufgew']!=0)
		return $record['sumvzkd']*$preismin/$record['ton'];
	else
		return 0;
}

function popis_mesice($record)
{
	$mesice=array("Jan","Feb","Mrz","Apr","Mai","Jun","Jul","Aug","Sep","Oct","Nov","Dez");
    if($record['mesic']=='ohne')
        return($record['mesic']);
    else
        return($mesice[$record['mesic']-1]);
}

function get_kurs($wahr,$ausliefer)
{
	//echo "wahr=$wahr,ausliefer=$ausliefer<br>";
	if($wahr!="EUR")
	{
		// podle auslieferdatumu a meny zjistim kurs
		$res=mysql_query("select kurs from dkurs where ((gilt_von<='".$ausliefer."') and (gilt_bis>='".$ausliefer."'))");
		$row=mysql_fetch_array($res);
		//echo "kurs=".$row['kurs']."<br>";
		return $row['kurs'];
	}
	else
	{
		//echo "kurs=1<br>";
		return 1;
	}
}

function get_kurs1($record)
{
    //return $record['waehr'];
	//echo "wahr=$wahr,ausliefer=$ausliefer<br>";
    $wahr = $record['waehr'];
    $ausliefer = $record['ausliefer_datum'];
	if($wahr!="EUR")
	{
		// podle auslieferdatumu a meny zjistim kurs
		$res=mysql_query("select kurs from dkurs where ((gilt_von<='".$ausliefer."') and (gilt_bis>='".$ausliefer."'))");
		$row=mysql_fetch_array($res);
		//echo "kurs=".$row['kurs']."<br>";
		return $row['kurs'];
	}
	else
	{
		//echo "kurs=1<br>";
		return 1;
	}
}

function preismin_in_EUR($record)
{
	$wahr=$record['waehr'];
	$ausliefer_datum=$record['ausliefer_datum'];
	return $record['preismin']/get_kurs($wahr,$ausliefer_datum);
}

function sumpreis_leistung_EUR($record)
{
	$wahr=$record['waehr'];
	$ausliefer_datum=$record['ausliefer_datum'];
	return $record['sumpreis_leistung']/get_kurs($wahr,$ausliefer_datum);
}

function sumpreis_sonst_EUR($record)
{
	$wahr=$record['waehr'];
	$ausliefer_datum=$record['ausliefer_datum'];
	return $record['sumpreis_sonst']/get_kurs($wahr,$ausliefer_datum);
}

$options = array(
        'encoder'=>false,
		'rootTag'=>'S795',
		'idColumn'=>'kunde',
		'rowTag'=>'kunde',
		'elements'=>array(
			'kundenr'=>'kunde',
            'importe'=>array(
                'rootTag'=>'importe',
                'rowTag'=>'import',
                'idColumn'=>'auftragsnr',
                'elements'=>array(
                    'importnr'=>'auftragsnr',
                    'aufdat',
                    'sumvzkd',
                    'sumvzkd_vor',
                    'sumvzkd_in',
                    'sumvzkd_nach',
                    'sumvzkdex_vor',
                    'sumvzkdex_in',
                    'sumvzkdex_nach',
                    'delta_vor',
                    'delta_in',
                    'delta_nach',
                    'sumvzkdex_alle',
                    'im_vor_plus_in',
                    'ex_vor_plus_in',
                    'exporte'=>array(
                        'rootTag'=>'exporte',
                        'rowTag'=>'export',
                        'idColumn'=>'auftragsnr-exp',
                        'elements'=>array(
                            'exportnr'=>'auftragsnr-exp',
                            'ausliefer_datum',
                            'sumvzkdex'
                        ),
                    ),
                ),
             ),
            )
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
$domxml->save("S795.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
