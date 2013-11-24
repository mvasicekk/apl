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

$views=array("personen","personen2roky");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     dv1.persnr,";
$pt.="     dv1.eintritt as eintritt_a,";
$pt.="     dv1.befristet as befristet_a,";
$pt.="     dv1.probezeit as probezeit_a,";
$pt.="     dv1.vertrag_anfang as va_a,";
$pt.="     dv1.verlang as verlang_a";
$pt.=" from dpersvertrag dv1";
$pt.=" where eintritt=(select max(dv2.eintritt) from dpersvertrag dv2 where dv1.persnr=dv2.persnr)";

$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     dv1.persnr,";
$pt.="     dv1.eintritt as eintritt_a,";
$pt.="     dv1.befristet as befristet_a,";
$pt.="     dv1.probezeit as probezeit_a,";
$pt.="     dv1.vertrag_anfang as va_a,";
$pt.="     dv1.verlang as verlang_a,";
$pt.="     DATEDIFF(now(),dv1.eintritt) as tage_arb_from_1st_eintritt";
$pt.=" from dpersvertrag dv1";
$pt.=" where eintritt=(select min(dv2.eintritt) from dpersvertrag dv2 where dv1.persnr=dv2.persnr)";

// zjisteni prvniho eintrittu pro vypcet 2 let  bez 3 mesicu , tj. 630 dnu pro prodluzovani smluv
// pokud dojde k preruseni smluv na dobu delsi nez 6 msicu, musim brat

//$s1ql = "select eintritt";


// spusteni dotazu pro vytvoreni vieweru

$db->query($pt);

$personen = $pcip.$views[0];
$personen2roky = $pcip.$views[1];

$persNrWhere= ' and ( 1 )';
if(($persvon!=$persbis)||(($persvon!=0 && $persbis!=0))) $persNrWhere = " and (dpers.persnr between $persvon and $persbis)";

$befristetDatumWhere = " and (1)";
if(strlen($befrvon)>2) $befristetDatumWhere = " and ($personen.befristet_a between '$befrvon' and '$befrbis')";

$probezeitWhere = " and (1)";
if($zkusdoba===TRUE) {
    $probezeitWhere = " and ($personen.probezeit_a>now())";
    if((strlen($zkusdobaod)>2) && (strlen($zkusdobado)>2)){
        $probezeitWhere = "and ($personen.probezeit_a between '$zkusdobaod' and '$zkusdobado')";
    }
}

//$roky2Where = " and (1)";
//if($roky2===TRUE){
//    $roky2Where = " and ($personen2roky.tage_arb_from_1st_eintritt>630) and ($personen.befristet_a>=now())";
//}

$unbefristetWhere = " and (1)";
if($dobaneurcita===TRUE){
    $unbefristetWhere = " and $personen.befristet_a is null";
    $befristetDatumWhere = " and (1)";
    //$probezeitWhere = " and (1)";
}

$sql = " select";
$sql.= "     dpers.persnr,";
$sql.= "     CONCAT(dpers.`Name`,' ',dpers.vorname) as name,";
//$sql.= "     dpers.Vorname as vorname,";
$sql.= "     $personen.eintritt_a,";
$sql.= "     $personen.befristet_a,";
$sql.= "     $personen.probezeit_a,";
$sql.= "     $personen.va_a,";
$sql.= "     $personen.verlang_a,";
$sql.= "     if($personen2roky.tage_arb_from_1st_eintritt is null,0,$personen2roky.tage_arb_from_1st_eintritt) as arbtage2roky,";
$sql.= "     DATE_FORMAT(dpersvertrag.eintritt,'%y-%m-%d') as eintritt,";
$sql.= "     if(dpersvertrag.verlang<>0,'|||',DATE_FORMAT(dpersvertrag.eintritt,'%y-%m-%d')) as eintrittF,";
$sql.= "     DATE_FORMAT(dpersvertrag.austritt,'%y-%m-%d') as austritt,";
$sql.= "     DATE_FORMAT(dpersvertrag.befristet,'%y-%m-%d') as befristet,";
$sql.= "     DATE_FORMAT(dpersvertrag.probezeit,'%y-%m-%d') as probezeit,";
$sql.= "     dpersvertrag.vertrag_anfang,";
$sql.= "     dpersvertrag.verlang";
$sql.= " from dpersvertrag";
$sql.= " join $personen on $personen.persnr=dpersvertrag.persnr";
$sql.= " left join $personen2roky on $personen2roky.persnr=dpersvertrag.persnr";
$sql.= " join dpers on dpers.PersNr=dpersvertrag.persnr";
$sql.= " where";
$sql.= "     dpers.austritt is null";
$sql.= "     and dpers.dpersstatus='MA'";
$sql.= $persNrWhere;
$sql.= $unbefristetWhere;
$sql.= $befristetDatumWhere;
$sql.= $probezeitWhere;
$sql.= $roky2Where;
$sql.= " order by dpers.PersNr,dpersvertrag.eintritt;";


//DEBUG zobrazim pripravenu SQL dotaz
//echo $sql;

$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'S145',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'name',
//                    'vorname',
                    'eintritt_a',
                    'befristet_a',
                    'probezeit_a',
                    'va_a',
                    'verlang_a',
                    'arbtage2roky',
                    'vertraege'=>array(
                        'rootTag'=>'vertraege',
                        'idColumn'=>'eintritt',
                        'rowTag'=>'vertrag',
                        'elements'=>array(
                            'austritt',
                            'eintritt',
                            'eintrittF',
                            'befristet',
                            'probezeit',
                            'vertrag_anfang',
                            'verlang',
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
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S145.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
