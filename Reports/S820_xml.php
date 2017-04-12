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


$sql="select ";
$sql.="    daufkopf.kunde,";
$sql.="     dkopf.teil,";
$sql.="     dkopf.teilbez,dkopf.gew,";
$sql.="     DATE_FORMAT(dkopf.`muster-vom`,'%d.%m.%Y') as mustervom, ";
$sql.="     dksd.preismin,dksd.name1, ";
$sql.="     sum(if(stat_nr='S0011' and `taetkz-nr`='P',drueck.`Stück`,0)) as pt_stk, ";
$sql.="     sum(if(dauftr.KzGut='G',dauftr.`stück`,0)) as g_imp_stk, ";
$sql.="     sum(if(dauftr.KzGut='G',dauftr.`stück`*dkopf.Gew,0)) as g_imp_gew, ";
$sql.="     sum(if(stat_nr='S0011',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`),0)) as pt_kdmin, ";
$sql.="     sum(if(stat_nr='S0011',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-ist`,drueck.`Stück`*`vz-ist`),0)) as pt_abymin, ";
$sql.="     sum(if(stat_nr='S0011',`verb-zeit`,0)) as pt_verb, ";
$sql.="     sum(if(stat_nr='S0041',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`),0)) as st_kdmin, ";
$sql.="     sum(if(stat_nr='S0041',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-ist`,drueck.`Stück`*`vz-ist`),0)) as st_abymin, ";
$sql.="     sum(if(stat_nr='S0041',`verb-zeit`,0)) as st_verb, ";
$sql.="     sum(if(stat_nr='S0043',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`),0)) as st1_kdmin, ";
$sql.="     sum(if(stat_nr='S0043',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-ist`,drueck.`Stück`*`vz-ist`),0)) as st1_abymin, ";
$sql.="     sum(if(stat_nr='S0043',`verb-zeit`,0)) as st1_verb, ";
$sql.="     sum(if(stat_nr='S0061',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`),0)) as g_kdmin, ";
$sql.="     sum(if(stat_nr='S0061',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-ist`,drueck.`Stück`*`vz-ist`),0)) as g_abymin, ";
$sql.="     sum(if(stat_nr='S0061',`verb-zeit`,0)) as g_verb, ";
$sql.="     sum(if(stat_nr='S0062',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`),0)) as g1_kdmin, ";
$sql.="     sum(if(stat_nr='S0062',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-ist`,drueck.`Stück`*`vz-ist`),0)) as g1_abymin, ";
$sql.="     sum(if(stat_nr='S0062',`verb-zeit`,0)) as g1_verb, ";
$sql.="     sum(if(stat_nr<>'S0043' and stat_nr<>'S0062' and stat_nr<>'S0061' and stat_nr<>'S0041' and stat_nr<>'S0011',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`),0)) as sonst_kdmin, ";
$sql.="     sum(if(stat_nr<>'S0043' and stat_nr<>'S0062' and stat_nr<>'S0061' and stat_nr<>'S0041' and stat_nr<>'S0011',if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-ist`,drueck.`Stück`*`vz-ist`),0)) as sonst_abymin, ";
$sql.="     sum(if(stat_nr<>'S0043' and stat_nr<>'S0062' and stat_nr<>'S0061' and stat_nr<>'S0041' and stat_nr<>'S0011',`verb-zeit`,0)) as sonst_verb, ";
$sql.="     sum(if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`)) as sum_kdmin, ";
$sql.="     sum(if(auss_typ=4,(drueck.Stück+drueck.`auss-Stück`)*`vz-ist`,drueck.`Stück`*`vz-ist`)) as sum_abymin, ";
$sql.="     sum(`verb-zeit`) as sum_verb ";
$sql.=" from drueck join daufkopf using (auftragsnr) ";
$sql.=" join dauftr on dauftr.auftragsnr=drueck.AuftragsNr and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.teil=drueck.Teil and dauftr.abgnr=drueck.TaetNr";
$sql.=" join dkopf on (drueck.teil=dkopf.teil) ";
$sql.=" join dksd on (dksd.kunde=daufkopf.kunde) ";
$sql.=" join `dtaetkz-abg` on (`dtaetkz-abg`.`abg-nr`=drueck.taetnr) ";
$sql.=" where ";
$sql.="     ((drueck.datum between '$datevon'  and '$datebis') and (daufkopf.kunde between '$kundevon' and '$kundebis') ) ";
$sql.=" group by ";
$sql.="     daufkopf.kunde,";
$sql.="     dkopf.teil";
$sql.=" order by kunde,sum_verb desc,teil";
	    
//echo "sql=$sql"."<br>";

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
	if(($record['g_imp_stk']*$record['gew'])!=0)
	{
		$hodnota=($record['sum_kdmin']*$record['preismin'])/($record['g_imp_stk']*$record['gew'])*1000;
		return $hodnota;
	}	
	else
		return 0;
}

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$options = array(
					'encoder'=>false,
					'rootTag'=>'S820',
					'idColumn'=>'kunde',
					'rowTag'=>'kunde',
					'elements'=>array(
						'kundenr'=>'kunde',
						'name1',
						'preismin',
						'teile'=>array(
							'rootTag'=>'teile',
							'rowTag'=>'teil',
							'idColumn'=>'teil',
							'elements'=>array(
								'teilnr'=>'teil',
								'teilbez',
								'gew',
								'mustervom',
								'g_imp_stk',
								'g_imp_gew',
								'pt_kdmin',
								'pt_abymin',
								'pt_verb',
								'st_kdmin',
								'st_abymin',
								'st_verb',
								'st1_kdmin',	//S0043
								'st1_abymin',
								'st1_verb',
								'g_kdmin',
								'g_abymin',
								'g_verb',
								'g1_kdmin',	//S0062
								'g1_abymin',
								'g1_verb',
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
						),
					)
				);			
// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML
					
$domxml = $query2xml->getXML($sql,$options);

$domxml->encoding="UTF-8";

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

//$db->disconnect();
//============================================================+
// END OF FILE                                                 
//============================================================+
//$domxml->save("S820.xml");
//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
