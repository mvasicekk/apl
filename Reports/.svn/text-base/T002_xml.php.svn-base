<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect("mysql://".LOCAL_USER.":".LOCAL_PASS."@".LOCAL_HOST."/".LOCAL_DB);

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
//$db->query("set character set cp1250");
$db->query("set names utf8");

// vytvorim si nekolik pohledu


$pcip=get_pc_ip();

/*
$views=array("D710_exportstk1","D710_exportstk","export_uebersicht_geliefert_teil","export_uebersicht_gew_gut");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dauftr.`auftragsnr-exp`, dauftr.teil, dauftr.auftragsnr, dauftr.abgnr, dauftr.`mehrarb-kz`,";
$pt.=" Sum(dauftr.`stk-exp`) AS `Summevonstk-exp`,";
$pt.=" Sum(dauftr.auss2_stk_exp) AS Summevonauss2_stk_exp,";
$pt.=" Sum(dauftr.auss4_stk_exp) AS Summevonauss4_stk_exp,";
$pt.=" Sum(dauftr.auss6_stk_exp) AS Summevonauss6_stk_exp,";
$pt.=" dauftr.KzGut";
$pt.=" FROM dauftr";
$pt.=" where (((dauftr.`auftragsnr-exp`)='$export'))";
$pt.=" GROUP BY dauftr.`auftragsnr-exp`, dauftr.teil, dauftr.auftragsnr, dauftr.abgnr, dauftr.`mehrarb-kz`, dauftr.KzGut";
$pt.=" ORDER BY dauftr.teil, dauftr.auftragsnr, dauftr.abgnr, dauftr.`mehrarb-kz`;";


//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");

$D710_exportstk1=$pcip.$views[0];

$pt="create view $viewname";
$pt.=" as SELECT $D710_exportstk1.`auftragsnr-exp`, $D710_exportstk1.Teil, $D710_exportstk1.AuftragsNr, Min($D710_exportstk1.abgnr) AS Minvonabgnr,";
$pt.=" $D710_exportstk1.`MehrArb-KZ`, $D710_exportstk1.`Summevonstk-exp`,";
$pt.=" Sum($D710_exportstk1.Summevonauss2_stk_exp) AS SummevonSummevonauss2_stk_exp, ";
$pt.=" Sum($D710_exportstk1.Summevonauss4_stk_exp) AS SummevonSummevonauss4_stk_exp, ";
$pt.=" Sum($D710_exportstk1.Summevonauss6_stk_exp) AS SummevonSummevonauss6_stk_exp, $D710_exportstk1.KzGut";
$pt.=" FROM $D710_exportstk1";
$pt.=" where ((($D710_exportstk1.`auftragsnr-exp`)='$export') AND (($D710_exportstk1.`MehrArb-KZ`)<>'I'))";
$pt.=" GROUP BY $D710_exportstk1.`auftragsnr-exp`, $D710_exportstk1.Teil, $D710_exportstk1.AuftragsNr, $D710_exportstk1.`MehrArb-KZ`,";
$pt.=" $D710_exportstk1.`Summevonstk-exp`, $D710_exportstk1.`KzGut`";
$pt.=" ORDER BY $D710_exportstk1.Teil, $D710_exportstk1.AuftragsNr, Minvonabgnr;";


//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT DAUFTR.auftragsnr, DKOPF.Gew, DAUFTR.teil, Sum(DAUFTR.`stk-exp`) AS geliefert,";
$pt.=" Sum(DAUFTR.`st�ck`) AS angeliefert, `gew`* Sum(DAUFTR.`stk-exp`) AS gew_gut, DAUFTR.`auftragsnr-exp`,";
$pt.=" max(fremdauftr) AS fremdauftr, max(fremdpos) as fremdpos";
$pt.=" FROM DAUFTR INNER JOIN DKOPF ON DAUFTR.teil = DKOPF.Teil";
$pt.=" WHERE (((DAUFTR.KzGut)='G') and (DAUFTR.`auftragsnr-exp`='$export'))";
$pt.=" GROUP BY DAUFTR.auftragsnr, DKOPF.Gew, DAUFTR.teil, DAUFTR.`auftragsnr-exp`";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[3];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT DAUFTR.`auftragsnr-exp`, Sum(`gew`*`stk-exp`) AS gew_gut, Count(DAUFTR.`pal-nr-exp`) AS positionen";
$pt.=" FROM DAUFTR INNER JOIN DKOPF ON DAUFTR.teil = DKOPF.Teil";
$pt.=" WHERE ((DAUFTR.KzGut='G') and (dauftr.`auftragsnr-exp`='$export'))";
$pt.=" GROUP BY DAUFTR.`auftragsnr-exp`";

//echo $pt."<br>";
$db->query($pt);


// provedu dotaz nad vytvorenymi pohledy

$D710_exportstk1=$pcip.$views[0];
$D710_exportstk=$pcip.$views[1];
$export_uebersicht_geliefert_teil=$pcip.$views[2];
$export_uebersicht_gew_gut=$pcip.$views[3];
 */

/*
 * tiskne podle nastaveni v pracovnim planu
$sql="select auftragsnr,dauftr.teil,`pos-pal-nr` as pal, `stück` as stk,daufkopf.kunde,name1,name2,";
$sql.=" teilbez,teillang,gew,`art guseisen` as artguseisen,`muster-platz` as musterplatz,DATE_FORMAT(`muster-vom`,'%d.%m.%Y') as mustervom,";
$sql.=" dpos_id,`taetnr-aby` as taetnr,`TaetBez-Aby-D` as tatbez_d,`TaetBez-Aby-T` as tatbez_t,`vz-min-aby` as vzaby,";
$sql.=" if(`vz-min-aby`<>0,round(60/`vz-min-aby`),0) as ks_hod";
$sql.=" from dauftr";
$sql.=" join daufkopf using(auftragsnr)";
$sql.=" join dksd on daufkopf.kunde=dksd.kunde";
$sql.=" join dkopf on dauftr.teil=dkopf.teil";
$sql.=" join dpos on dauftr.teil=dpos.teil";
$sql.=" where ((auftragsnr='$auftragsnr') and (`pos-pal-nr` between '$palvon' and '$palbis') and (`auftragsnr-exp` is null)";
$sql.=" and (dpos.`kz-druck`<>0) and (dauftr.kzgut='G'))";
$sql.=" order by auftragsnr,pal,taetnr;";
*/

$where = "";

if(strlen(trim($kunde))>0){
    if(strlen($where)>0)
        $where = $where . " and (kunde='$kunde')";
    else
        $where = $where . " (kunde='$kunde')";
}

if(strlen(trim($regal))>0){
    if(strlen($where)>0)
        $where = $where . " and (`muster-platz`='$regal')";
    else
        $where = $where . " (`muster-platz`='$regal')";
}

if(strlen(trim($teil))>0){
    if(strlen($where)>0)
        $where = $where . " and (`teil`='$teil')";
    else
        $where = $where . " (`teil`='$teil')";
}

if(strlen($where)>0)
    $where = "( ".$where." )";
else
    $where = "( 1=2 )";
    
$sql = "select teil,teilbez,kunde,teillang,gew,`muster-platz` as regal,`muster-freigabe-1-vom` freigabe1vom from dkopf where $where order by teil";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry



$options = array(
					'encoder'=>false,
					'rootTag'=>'T002',
					'idColumn'=>'teil',
					'rowTag'=>'teil',
					'elements'=>array(
						'teilnr'=>'teil',
						'kunde',
						'teilbez',
                        'teillang',
                        'gew',
                        'regal',
                        'freigabe1vom'
                    )
                 );
//						'name2',
//						'paletten'=>array(
//							'rootTag'=>'paletten',
//							'idColumn'=>'pal',
//							'rowTag'=>'palette',
//							'elements'=>array(
//								'pal',
//								'teil',
//								'stk',
//								'teilbez',
//								'teillang',
//								'gew',
//								'artguseisen',
//								'musterplatz',
//								'mustervom',
//								'taetigkeiten'=>array(
//									'rootTag'=>'taetigkeiten',
//									'rowTag'=>'taetigkeit',
//									'idColumn'=>'dpos_id',
//									'elements'=>array(
//										'taetnr',
//										'tatbez_d',
//										'tatbez_t',
//										'vzaby',
//										'ks_hod'
//									),
//								),
//							),
//						),
//					)
//				);

// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);
//$domxml->encoding="UFT-8";

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
$domxml->save("T002.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
