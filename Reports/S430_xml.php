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

$pcip=get_pc_ip();

$views=array("teile");

// povolit naslouchani na portu

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");

$pt=" create view $viewname as ";
$pt.=" select distinct dpos.teil";
$pt.=" from dpos";
$pt.=" join dkopf on dkopf.teil=dpos.teil";
$pt.=" where dkopf.kunde='$kunde1'";
if((strlen($abgnr)>0) && ($abgnr!='*'))
$pt.=" and dpos.`TaetNr-Aby`=$abgnr";

$db->query($pt);
$teile=$pcip.$views[0];

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
$sql.= "     dkopf.jb_lfd_plus_1,";
$sql.= "     dkopf.stk_g_ist_2012,";
$sql.= "     dkopf.stk_g_ist_2013,";
$sql.= "     dkopf.stk_g_ist_2014,";
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
if ($preise == "neu") {
    $sql.= "     dpos.`vzkd_neu` as vzkd,";
    $sql.= "     dpos.`vzkd_neu`*dkopf.jb_lfd_1 as bed_lfd_1_vzkd,";
    $sql.= "     dpos.`vzkd_neu`*dkopf.jb_lfd_j as bed_lfd_j_vzkd,";
    $sql.= "     dpos.`vzkd_neu`*dkopf.jb_lfd_plus_1 as bed_lfd_plus_1_vzkd,";
    $sql.= "     dpos.`vzkd_neu`*dkopf.jb_lfd_1 as bed_2011_vzkd,";
    $sql.= "     dpos.`vzkd_neu`*dkopf.jb_lfd_j as bed_2012_vzkd,";
    $sql.= "     round(dpos.`vzkd_neu`*dksd.preismin,4) as preis,";
    $sql.= "     round(dpos.`vzkd_neu`*dksd.preismin*dkopf.jb_lfd_1,4) as bed_lfd_1_preis,";
    $sql.= "     round(dpos.`vzkd_neu`*dksd.preismin*dkopf.jb_lfd_j,4) as bed_lfd_j_preis,";
    $sql.= "     round(dpos.`vzkd_neu`*dksd.preismin*dkopf.jb_lfd_plus_1,4) as bed_lfd_plus_1_preis,";
    $sql.= "     round(dpos.`vzkd_neu`*dksd.preismin*dkopf.jb_lfd_1,4) as bed_2011_preis,";
    $sql.= "     round(dpos.`vzkd_neu`*dksd.preismin*dkopf.jb_lfd_j,4) as bed_2012_preis,";
} else {
    $sql.= "     dpos.`VZ-min-kunde` as vzkd,";
    $sql.= "     dpos.`VZ-min-kunde`*dkopf.jb_lfd_1 as bed_lfd_1_vzkd,";
    $sql.= "     dpos.`VZ-min-kunde`*dkopf.jb_lfd_j as bed_lfd_j_vzkd,";
    $sql.= "     dpos.`VZ-min-kunde`*dkopf.jb_lfd_plus_1 as bed_lfd_plus_1_vzkd,";
    $sql.= "     dpos.`VZ-min-kunde`*dkopf.jb_lfd_1 as bed_2011_vzkd,";
    $sql.= "     dpos.`VZ-min-kunde`*dkopf.jb_lfd_j as bed_2012_vzkd,";
    $sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin,4) as preis,";
    $sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin*dkopf.jb_lfd_1,4) as bed_lfd_1_preis,";
    $sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin*dkopf.jb_lfd_j,4) as bed_lfd_j_preis,";
    $sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin*dkopf.jb_lfd_plus_1,4) as bed_lfd_plus_1_preis,";
    $sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin*dkopf.jb_lfd_1,4) as bed_2011_preis,";
    $sql.= "     round(dpos.`VZ-min-kunde`*dksd.preismin*dkopf.jb_lfd_j,4) as bed_2012_preis,";
}
$sql.= " `dtaetkz-abg`.`Name` as abgnr_name";
$sql.= " from dkopf";
//jen pro dily podle hitliste
//$sql.= " join tmp_hitliste on tmp_hitliste.teil=dkopf.teil";
$sql.= " join dksd on dksd.Kunde=dkopf.Kunde";
$sql.= " left join dpos on dpos.Teil=dkopf.Teil";
$sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby`";
//$sql.= " join $teile on $teile.teil=dkopf.teil";
$sql.= " where";
$sql.= "     dkopf.kunde='$kunde1'";
$sql.= "     and dpos.`TaetNr-Aby`>3";
$sql.= "     and dpos.`kz-druck`<>0";
if((strlen($abgnr)>0) && ($abgnr!='*'))
$sql.= "     and dpos.`TaetNr-Aby`=$abgnr";

//if($jb===TRUE){
//    $sql.= " and (dkopf.jb_lfd_j<>0 or dkopf.jb_lfd_1<>0 or dkopf.jb_lfd_2<>0 or dkopf.jb_lfd_plus_1<>0)";
//}
    
if($alt==FALSE){
    $sql.= " and (dkopf.status not like '%ALT%' or dkopf.status is null)";
}
    
// vynechat frachtkosten
$sql.= "     and dpos.`TaetNr-Aby`<>1701";
if(strlen($teil)>1)
$sql.= "     and dkopf.teil like '$teil'";
$sql.= " order by";
$sql.= "     dkopf.Teil,";
$sql.= "     `dtaetkz-abg`.`stat_nr`,";
$sql.= "     dpos.`TaetNr-Aby`";



//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

$options = array(
        'encoder' => false,
        'rootTag' => 'S430',
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
		    'jb_lfd_plus_1',
		    'jb_lfd_j',
		    'stk_g_ist_2012',
		    'stk_g_ist_2013',
		    'stk_g_ist_2014',
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
			    'bed_lfd_plus_1_vzkd',
                            'bed_2011_vzkd',
                            'bed_2012_vzkd',
			    'bed_lfd_1_preis',
			    'bed_lfd_j_preis',
			    'bed_lfd_plus_1_preis',
                            'bed_2011_preis',
                            'bed_2012_preis',
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
$domxml->save("S430.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>