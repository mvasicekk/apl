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
$views=array("pt_S790_dauftr","pt_S790_aufgew","pt_S790_drueck");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select daufkopf.kunde,dauftr.auftragsnr,daufkopf.aufdat,daufkopf.fertig,";
$pt.=" daufkopf.ausliefer_datum,sum((`stk-exp`+auss4_stk_exp)*vzkd) as sumvzkddauftr,";
$pt.=" sum((`stk-exp`+auss4_stk_exp)*vzkd*preismin) as wert";
$pt.=" from dauftr join daufkopf using(auftragsnr)";
$pt.=" join dksd using(kunde) ";
$pt.=" where ((`mehrarb-kz`<>'F') and (daufkopf.kunde between '$kunde_von' and '$kunde_bis') ";
$pt.=" and ((daufkopf.ausliefer_datum is null) or  ";
$pt.=" (daufkopf.ausliefer_datum between '$ausliefer_von' and '$ausliefer_bis')))";
$pt.=" group by daufkopf.kunde,dauftr.auftragsnr,daufkopf.aufdat,daufkopf.fertig, daufkopf.ausliefer_datum";
$db->query($pt);
	
$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select daufkopf.kunde,dauftr.auftragsnr,dksd.preismin,`waehr-kz`, sum(gew*`stück`) as aufgew";
$pt.=" from dauftr join daufkopf using(auftragsnr)";
$pt.=" join dksd using(kunde) join dkopf on (dauftr.teil=dkopf.teil)";
$pt.=" where ((`kzgut`='G') and  (daufkopf.kunde between '$kunde_von' and '$kunde_bis')";
$pt.=" and ((daufkopf.ausliefer_datum is null)";
$pt.=" or (daufkopf.ausliefer_datum between '$ausliefer_von' and '$ausliefer_bis')))";
$pt.=" group by daufkopf.kunde,dauftr.auftragsnr,dksd.preismin,`waehr-kz`";
$db->query($pt);
//echo "<br>PT<br>$pt";


$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select drueck.auftragsnr, sum(if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-soll`,drueck.`stück`*`vz-soll`)) as sumvzkd,";
$pt.=" sum(if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-ist`,drueck.`stück`*`vz-ist`)) as sumvzaby,";
$pt.=" sum(`verb-zeit`) as sumverb,";
$pt.=" sum(if(taetnr>1999 and taetnr<4000,if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-soll`,drueck.`stück`*`vz-soll`),0)) as vzkd1999,";
$pt.=" sum(if(taetnr>1999 and taetnr<4000,if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-ist`,drueck.`stück`*`vz-ist`),0)) as vzaby1999,";
$pt.=" sum(if(taetnr>3999,if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-ist`,drueck.`stück`*`vz-ist`),0)) as vzaby3999";
$pt.=" from drueck join daufkopf using(auftragsnr)";
$pt.=" where ((daufkopf.kunde between '$kunde_von' and '$kunde_bis') and ((daufkopf.ausliefer_datum is null)";
$pt.=" or (daufkopf.ausliefer_datum between '$ausliefer_von' and '$ausliefer_bis'))) group by drueck.auftragsnr";
$db->query($pt);
//echo "<br>PT:<br>$pt";

// provedu dotaz nad vytvorenymi pohledy
$pt_S790_dauftr=$pcip.$views[0];
$pt_S790_aufgew=$pcip.$views[1];
$pt_S790_drueck=$pcip.$views[2];

$sql=" SELECT $pt_S790_dauftr.kunde,";
$sql.=" DATE_FORMAT($pt_S790_dauftr.ausliefer_datum,'%y-%m-%d') as ausliefer,";
$sql.=" $pt_S790_dauftr.ausliefer_datum,";
$sql.=" if($pt_S790_dauftr.ausliefer_datum is null,'ohne',DATE_FORMAT($pt_S790_dauftr.ausliefer_datum,'%m')) as mesic,";
$sql.=" $pt_S790_dauftr.auftragsnr,";
$sql.=" DATE_FORMAT($pt_S790_dauftr.aufdat,'%y-%m-%d') as aufdat,";
$sql.=" DATE_FORMAT($pt_S790_dauftr.fertig,'%y-%m-%d') as fertig,";
$sql.=" $pt_S790_dauftr.wert,";
$sql.=" $pt_S790_aufgew.aufgew,";
$sql.=" $pt_S790_aufgew.aufgew/1000 as ton,";
$sql.=" $pt_S790_dauftr.sumvzkddauftr,";
$sql.=" $pt_S790_drueck.sumvzkd,";
$sql.=" $pt_S790_drueck.sumvzaby,";
$sql.=" $pt_S790_drueck.sumverb,";
$sql.=" $pt_S790_drueck.vzkd1999,";
$sql.=" $pt_S790_drueck.vzaby1999,";
$sql.=" $pt_S790_drueck.vzaby3999,";
$sql.=" $pt_S790_aufgew.preismin,";
$sql.=" $pt_S790_aufgew.`waehr-kz` as waehr,";
$sql.="  `aufgew`/1000 AS aufgewichttonnet,";
$sql.=" if(`aufgew`<>0,`sumvzkd`*`preismin`/`aufgew`*1000,0) AS dmton";
$sql.=" FROM ($pt_S790_dauftr INNER JOIN $pt_S790_aufgew";
$sql.=" ON $pt_S790_dauftr.auftragsnr = $pt_S790_aufgew.auftragsnr)";
$sql.=" LEFT JOIN $pt_S790_drueck ON $pt_S790_dauftr.auftragsnr = $pt_S790_drueck.auftragsnr";
$sql.=" ORDER BY $pt_S790_dauftr.ausliefer_datum, $pt_S790_dauftr.auftragsnr";

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
    $kurs = get_kurs($record['waehr'],$record['ausliefer_datum']);
    if($kurs!=0)
        $preismin=$record['preismin']/$kurs;
    else
        $preismin=0;
	
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
		'rootTag'=>'S790',
		'idColumn'=>'kunde',
		'rowTag'=>'kunden',
		'elements'=>array(
			'kunde',
			'mesice'=>array(
				'rootTag'=>'mesice',
				'rowTag'=>'mesic',
				'idColumn'=>'mesic',
				'elements'=>array(
					'mesicpopis'=>"#popis_mesice();",
					'importe'=>array(
						'rootTag'=>'importe',
						'rowTag'=>'import',
						'idColumn'=>'auftragsnr',
						'elements'=>array(
							'auftragsnr',
                            'aufdat',
                            'ausliefer_datum',
                            'ausliefer',
                            'fertig',
                            'wert',
                            'aufgew',
                            'ton',
                            'kurs'=>"#get_kurs1();",
                            'eur_pro_tonne'=>"#eur_pro_tonne();",
                            'vzkd_dauftr'=>'sumvzkddauftr',
                            'vzkd'=>'sumvzkd',
                            'vzaby'=>'sumvzaby',
                            'verb'=>'sumverb',
                            'vzkd1999',
                            'vzaby1999',
                            'vzaby3999',
                            'preismin',
                            'waehr',
                            'aufgewichttonnet',
                            'dmton',
							'fac1'=>"#fac1();",
    						'fac2'=>"#fac2();"
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
$domxml->save("S790.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
