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


//$views=array("pt_D605_dauftr","pt_D605_drueck","pt_D605_drueckG_gesamt");

//$viewname=$pcip.$views[0];
//$db->query("drop view $viewname");
//
//$pt="create view $viewname";
//$pt.=" as SELECT max(if(kzgut='G',trim(`auftragsnr-exp`),0)) as export_lief, dauftr.AuftragsNr, dauftr.`pos-pal-nr`as import_pal,";
//$pt.=" DATE_FORMAT(aufdat,'%d.%m.%Y') as aufdat,max(if(kzgut='G',trim(`pal-nr-exp`),0)) as export_pal,dauftr.Teil, sum(if(kzgut='G',`Stück`,0)) as import_stk,";
//$pt.=" sum(if(kzgut='G',`stk-exp`,0)) as export_stk, sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)<>'T',vzkd,0)) as S0011P";
//$pt.=" ,sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)<>'T',vzkd,0)) as sumS0011P, ";
//$pt.=" sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)<>'T',1,0)) as cnt_S0011P,";
//$pt.=" sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)='T',vzkd,0)) as S0011T, ";
//$pt.=" sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)='T',vzkd,0)) as sumS0011T, ";
//$pt.=" sum(if(`Stat_Nr`='S0011' and left(`mehrarb-kz`,1)='T',1,0)) as cnt_S0011T,sum(if(Stat_Nr='S0041',vzkd,0)) as S0041, ";
//$pt.=" sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0041',vzkd,0)) as sumS0041, sum(if(Stat_Nr='S0041',1,0)) as cnt_S0041, ";
//$pt.=" sum(if(Stat_Nr='S0051',vzkd,0)) as S0051, sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0051',vzkd,0)) as sumS0051, ";
//$pt.=" sum(if(Stat_Nr='S0051',1,0)) as cnt_S0051, sum(if(Stat_Nr='S0061',vzkd,0)) as S0061, ";
//$pt.=" sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0061',vzkd,0)) as sumS0061, sum(if(Stat_Nr='S0061',1,0)) as cnt_S0061, ";
//$pt.=" sum(if(kzgut='G',`Stück`,0))*sum(vzkd) as sumvzkd, sum(if(kzgut='G',`Stück`,0))*dkopf.gew/1000 as imp_gew ";
//$pt.=" FROM dauftr JOIN dkopf using (teil) JOIN `dtaetkz-abg` ON dauftr.abgnr = `dtaetkz-abg`.`abg-nr` ";
//$pt.=" JOIN daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr ";
//$pt.=" where (((dauftr.AuftragsNr) = '$auftragsnr')) group BY dauftr.AuftragsNr, import_pal,dauftr.Teil order by dauftr.AuftragsNr, import_pal,dauftr.Teil;";
//
////echo $pt."<br>";
//$db->query($pt);
//
//$viewname=$pcip.$views[1];
//$db->query("drop view $viewname");
//
//$pt="create view $viewname";
//$pt.= " as SELECT drueck.AuftragsNr, drueck.`pos-pal-nr`as import_pal,drueck.Teil, sum(if(`taetkz-nr`='T',`Stück`,0))  as sum_stk_T, ";
//$pt.= " sum(if(`taetkz-nr`='P',`Stück`,0))  as sum_stk_P, sum(if(`taetkz-nr`='St',`Stück`,0))  as sum_stk_St, ";
//$pt.= " sum(if(`taetkz-nr`='G',`Stück`,0))  as sum_stk_G, sum(if(`taetkz-nr`='E',`Stück`,0))  as sum_stk_E, ";
//$pt.= " sum(if(auss_typ=2,`auss-Stück`,0)) as auss2, sum(if(auss_typ=4,`auss-Stück`,0)) as auss4, sum(if(auss_typ=6,`auss-Stück`,0)) as auss6";
//$pt.= "  FROM drueck JOIN `dtaetkz-abg`  ON drueck.TaetNr=`dtaetkz-abg`.`abg-nr` where (((drueck.AuftragsNr) = '$auftragsnr')) ";
//$pt.= " group BY drueck.AuftragsNr, import_pal,drueck.Teil order by drueck.AuftragsNr, import_pal,drueck.Teil;";
//
////echo $pt."<br>";
//$db->query($pt);
//
//$viewname=$pcip.$views[2];
//$db->query("drop view $viewname");
//$pt="create view $viewname";
//$pt.= " as SELECT drueck.AuftragsNr, drueck.`pos-pal-nr`as import_pal, drueck.Teil, sum(if(dpos.`kzgut`='G',`Stück`,0))  as sum_stk_Gtat";
//$pt.= " FROM drueck JOIN `dpos`  on (drueck.teil=dpos.teil) and (drueck.taetnr=dpos.`taetnr-aby`) ";
//$pt.= " where (((drueck.AuftragsNr) = '$auftragsnr')) group BY drueck.AuftragsNr, import_pal,drueck.Teil;";
//
////echo $pt."<br>";
//$db->query($pt);
//
//// provedu dotaz nad vytvorenymi pohledy
//
//$pt_D605_dauftr=$pcip.$views[0];
//$pt_D605_drueck=$pcip.$views[1];
//$pt_D605_drueckG_gesamt=$pcip.$views[2];

$sql = " select";
$sql.= "     dauftr.auftragsnr as import,";
$sql.= "     dauftr.`auftragsnr-exp` as export,";
$sql.= "     daufkopf.aufdat,";
$sql.= "     dauftr.teil,";
$sql.= "     dauftr.`pos-pal-nr` as pal,";
$sql.= "     dauftr.`stück` as stk,";
$sql.= "     dauftr.`stk-exp` as stk_exp,";
$sql.= "     dauftr.`stück`*dkopf.Gew as gew_netto,";
$sql.= "     dauftr.`stück`*dkopf.BrGew as gew_brutto";
$sql.= " from";
$sql.= "     dauftr";
$sql.= " join dkopf on dkopf.Teil=dauftr.teil";
$sql.= " join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.= " where";
$sql.= "     dauftr.auftragsnr='$import'";
$sql.= "     and dauftr.KzGut='G'";
$sql.= " order by";
$sql.= "     dauftr.auftragsnr,";
$sql.= "     dauftr.teil,";
$sql.= "     dauftr.`pos-pal-nr`";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

$options = array(
        'encoder' => false,
        'rootTag' => 'S420',
        'idColumn' => 'import',
        'rowTag' => 'import',
        'elements' => array(
            'importnr'=>'import',
            'aufdat',
            'teile' => array(
                'rootTag' => 'teile',
                'idColumn' => 'teil',
                'rowTag' => 'teil',
                'elements' => array(
                    'teilnr'=>'teil',
                    'paletten' => array(
                        'rootTag' => 'paletten',
                        'idColumn' => 'pal',
                        'rowTag' => 'palette',
                        'elements' => array(
                            'importnr'=>'import',
                            'teilnr'=>'teil',
                            'pal',
                            'export',
                            'stk',
                            'stk_exp',
                            'gew_netto',
                            'gew_brutto',
                        ),
                    ),
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
$domxml->save("S420.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
