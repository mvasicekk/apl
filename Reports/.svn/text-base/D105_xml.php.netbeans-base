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

$oeWhere = '';
$regeloeWhere = '';

$datumVonStamp = strtotime($datum);
$weekOfYear = date('W',$datumVonStamp);

if($oeArray!=FALSE) {
    if(count($oeArray)==1 && $oeArray[0]=='*') {
        // nedelam nic
    }
    else {
        foreach ($oeArray as $oecko) {
            $oeWhere .= " dzeitsoll.oe like '".$oecko."' or";
            // podle rozlisim podle licheho a sudeho tydne
            if($weekOfYear%2 != 0){
                //lichytyden
                $regeloeWhere .= " dpers.regeloe like '".$oecko."' or";
            }
            else{
                $regeloeWhere .= " dpers.alteroe like '".$oecko."' or";
            }
        }
        if(strlen($oeWhere)>0) {
            // odeberu or na konci retezce
            $oeWhere = substr($oeWhere, 0, strlen($oeWhere)-2);
        }
        if(strlen($regeloeWhere)>0) {
            // odeberu or na konci retezce
            $regeloeWhere = substr($regeloeWhere, 0, strlen($regeloeWhere)-2);
        }

        $oeWhere = strtr($oeWhere, '*', '%');
        $regeloeWhere = strtr($regeloeWhere, '*', '%');
    }
}

// vytvorim si nekolik pohledu

$datvon = $datum;
$datbis = $datumbis;
$timebis = strtotime($datvon);
$timebisinput = strtotime($datumbis);
$timebisPlus6Days = $timebis + 5 * (24*60*60);

if($timebisinput>$timebisPlus6Days) $timebisinput = $timebisPlus6Days;

$datbis = date('Y-m-d', $timebisinput);

//$oe = str_replace('*', '%', $oe);
if(!strcmp($schicht, '*')) $schicht = '';
//if(!strcmp($oe,'%')) $oe = '';

//echo "datvon=$datvon,datbis=$datbis";

//exit;

$pcip=get_pc_ip();

// hlidat pritomnost austrittu, nebo podminka na eintritt > austritt

//if( (strlen($schicht)>0) && (strlen($oeWhere)>0) )
//    $sql = "select dpers.`PersNr` as persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr join dzeitsoll on dzeitsoll.persnr=dpers.`PersNr` where ((dpers.austritt is null or dpers.eintritt>dpers.austritt) and (dpers.`Schicht`='$schicht') and (dzeitsoll.datum between '$datvon' and '$datbis') and ($oeWhere)) order by dpers.`PersNr`";
//else if(strlen($schicht)>0){
//    $sql = "select dpers.`PersNr` as persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where ((dpers.austritt is null or dpers.eintritt>dpers.austritt) and (dpers.`Schicht`='$schicht')) order by dpers.`PersNr`";
//}
//else if(strlen($oeWhere)>0){
//    $sql = "select dpers.`PersNr` as persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr join dzeitsoll on dzeitsoll.persnr=dpers.`PersNr` where ((dpers.austritt is null or dpers.eintritt>dpers.austritt) and (dzeitsoll.datum between '$datvon' and '$datbis') and ($oeWhere)) order by dpers.`PersNr`";
//}
//else{
//    $sql = "select dpers.`PersNr` as persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where ((dpers.austritt is null or dpers.eintritt>dpers.austritt)) order by dpers.`PersNr`";
//}

if ($reporttyp == 'stamm OE') {
    if ((strlen($schicht) > 0) && (strlen($oeWhere) > 0))
        $sql = "select dpers.`PersNr` as persnr,dpers.regeloe,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where (($regeloeWhere) and (dpers.austritt is null) and (dpers.dpersstatus='MA') and (dpers.persnr between '$persvon' and '$persbis') and (dpers.regeloe<>'-')) order by dpers.regeloe,dpers.`PersNr`";
    else if (strlen($schicht) > 0) {
        $sql = "select dpers.`PersNr` as persnr,dpers.regeloe,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where ((dpers.austritt is null) and (dpers.dpersstatus='MA') and (dpers.persnr between '$persvon' and '$persbis')  and (dpers.regeloe<>'-')) order by dpers.regeloe,dpers.`PersNr`";
    } else if (strlen($oeWhere) > 0) {
        $sql = "select dpers.`PersNr` as persnr,dpers.regeloe,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where (($regeloeWhere) and (dpers.persnr between '$persvon' and '$persbis') and (dpers.austritt is null) and (dpers.dpersstatus='MA')  and (dpers.regeloe<>'-')) order by dpers.regeloe,dpers.`PersNr`";
    } else {
        $sql = "select dpers.`PersNr` as persnr,dpers.regeloe,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where ((dpers.persnr between '$persvon' and '$persbis') and (dpers.austritt is null) and (dpers.dpersstatus='MA') and (dpers.regeloe<>'-')) order by dpers.regeloe,dpers.`PersNr`";
    }
} else {
// 2010-07-01 budu vybirat podle regeloe
    if ((strlen($schicht) > 0) && (strlen($oeWhere) > 0))
        $sql = "select dpers.`PersNr` as persnr,dpers.regeloe,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr join dzeitsoll on dzeitsoll.persnr=dpers.`PersNr` join dtattypen on dzeitsoll.oe=dtattypen.tat where ((dpers.austritt is null) and (dpers.dpersstatus='MA') and (dpers.persnr between '$persvon' and '$persbis') and (dzeitsoll.datum between '$datvon' and '$datbis') and ($oeWhere or ($regeloeWhere and dtattypen.oestatus<>'a' and dtattypen.oestatus<>'-'))) order by dpers.`PersNr`";
    else if (strlen($schicht) > 0) {
        $sql = "select dpers.`PersNr` as persnr,dpers.regeloe,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where ((dpers.austritt is null) and (dpers.dpersstatus='MA') and (dpers.persnr between '$persvon' and '$persbis')) order by dpers.`PersNr`";
    } else if (strlen($oeWhere) > 0) {
        $sql = "select dpers.`PersNr` as persnr,dpers.regeloe,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr join dzeitsoll on dzeitsoll.persnr=dpers.`PersNr`  join dtattypen on dzeitsoll.oe=dtattypen.tat where ((dpers.persnr between '$persvon' and '$persbis') and (dpers.austritt is null) and (dpers.dpersstatus='MA') and (dzeitsoll.datum between '$datvon' and '$datbis') and ($oeWhere or ($regeloeWhere and dtattypen.oestatus<>'a' and dtattypen.oestatus<>'-'))) order by dpers.`PersNr`";
    } else {
        $sql = "select dpers.`PersNr` as persnr,dpers.regeloe,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where ((dpers.persnr between '$persvon' and '$persbis') and (dpers.austritt is null) and (dpers.dpersstatus='MA') ) order by dpers.`PersNr`";
    }
}
//echo "datvon=$datvon,datbis=$datbis,oe=$oe,schicht=$schicht<br>";
//echo $sql;
//exit;


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'D105',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'name',
                    'eintritt',
                    'austritt',
                    'ort',
                    'tel',
                    'marke',
                    'regeloe'
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
$domxml->save("D105.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
