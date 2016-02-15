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

//var_dump($oeArray);
if($oeArray!=FALSE) {
    if(count($oeArray)==1 && $oeArray[0]=='*') {
        // nedelam nic
    }
    else {
        foreach ($oeArray as $oecko) {
            $oeWhere .= " dambew.oe like '".$oecko."' or";
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
    }
}



//echo "<br>oeWhere $oeWhere";
// vytvorim si nekolik pohledu
$pcip=get_pc_ip();

if(strstr($reporttyp,"sort lt.og-oe")!=FALSE){
    // verze se sumama pro OG , OE
$sql= "select dtattypen.og,dambew.oe,dambew.`AMNr` as amnr,";
$sql.=" `eink-artikel`.`art-name1` as artikelname,";
$sql.=" sum(dambew.`AusgabeStk`) as ausgabestk,sum(dambew.`RueckgabeStk`) as rueckgabestk,";
$sql.=" sum(dambew.`AusgabeStk`-dambew.`RueckgabeStk`) as differenz,";
//$sql.=" (dambew.`AusgabeStk`*`eink-artikel`.`art-vr-preis`-dambew.`RueckgabeStk`*`eink-artikel`.`art-vr-preis`) as preisausgabe";
$sql.=" sum((dambew.`AusgabeStk`*`eink-artikel`.`art-vr-preis`)) as preisausgabe";
$sql.=" from dambew ";
$sql.=" join dpers on dambew.`PersNr`=dpers.`PersNr`";
$sql.=" join `eink-artikel` on dambew.`AMNr`=`eink-artikel`.`art-nr`";
$sql.=" join dtattypen on dambew.oe=dtattypen.tat";
$sql.=" where (dambew.`PersNr` between '$persnrvon' and '$persnrbis') and (dambew.`Datum` between '$datumvon' and '$datumbis') and (amnr_typ=1)  and (dambew.amnr>0)";
//$sql.=" where (dambew.`PersNr` between '$persnrvon' and '$persnrbis') and (dambew.`Datum` between '$datumvon' and '$datumbis') and (amnr_typ=1) and (dambew.amnr>100000)";
if(strlen($amnr)>0)
    $sql.=" and (dambew.amnr like '$amnr')";
if(strlen($oeWhere)>0)
    $sql.=" and ($oeWhere)";
$sql.=" group by dtattypen.og,dambew.oe,dambew.`AMNr`";
//$sql.="order by dambew.persnr,dambew.amnr,dambew.datum";
}
else{
$sql= "select dambew.id,DATE_FORMAT(dambew.`Datum`,'%y-%m-%d') as datum,dambew.`PersNr` as persnr,dtattypen.og,dambew.oe,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dambew.`AMNr` as amnr,";
$sql.=" `eink-artikel`.`art-name1` as artikelname,";
$sql.=" dambew.`AusgabeStk` as ausgabestk,dambew.`RueckgabeStk` as rueckgabestk,";
$sql.=" dambew.bemerkung,";
$sql.=" dambew.`AusgabeStk`-dambew.`RueckgabeStk` as differenz,";
$sql.=" (dambew.`AusgabeStk`-dambew.`RueckgabeStk`)*(`eink-artikel`.`art-vr-preis`) as preisdiff,";
//$sql.=" (dambew.`AusgabeStk`*`eink-artikel`.`art-vr-preis`-dambew.`RueckgabeStk`*`eink-artikel`.`art-vr-preis`) as preisausgabe";
$sql.=" (dambew.`AusgabeStk`*`eink-artikel`.`art-vr-preis`) as preisausgabe";
$sql.=" from dambew ";
$sql.=" join dpers on dambew.`PersNr`=dpers.`PersNr`";
$sql.=" join `eink-artikel` on dambew.`AMNr`=`eink-artikel`.`art-nr`";
$sql.=" join dtattypen on dambew.oe=dtattypen.tat";
$sql.=" where (dambew.`PersNr` between '$persnrvon' and '$persnrbis') and (dambew.`Datum` between '$datumvon' and '$datumbis') and (amnr_typ=1)  and (dambew.amnr>0)";
//$sql.=" where (dambew.`PersNr` between '$persnrvon' and '$persnrbis') and (dambew.`Datum` between '$datumvon' and '$datumbis') and (amnr_typ=1) and (dambew.amnr>100000)";
if(strlen($amnr)>0)
    $sql.=" and (dambew.amnr like '$amnr')";
if(strlen($bemerkung)>0)
    $sql.=" and (dambew.bemerkung like '$bemerkung')";
if(strlen($benutzer)>0)
    $sql.=" and (dambew.comp_user_accessuser like '$benutzer')";
if(strlen($oeWhere)>0)
    $sql.=" and ($oeWhere)";
$sql.="order by dambew.persnr,dambew.amnr,dambew.datum";
}
//echo $sql;
$query2xml = XML_Query2XML::factory($db);
	
if (strstr($reporttyp, "sort lt.og-oe") != FALSE) {
    $options = array(
        'encoder' => false,
        'rootTag' => 'S190',
        'idColumn' => 'og',
        'rowTag' => 'og',
        'elements' => array(
            'ognr' => 'og',
            'oes' => array(
                'rootTag' => 'oes',
                'rowTag' => 'oe',
                'idColumn' => 'oe',
                'elements' => array(
                    'oenr' => 'oe',
                    'arbeitsmittel' => array(
                        'rootTag' => 'arbeitsmittel',
                        'rowTag' => 'am',
                        'idColumn' => 'amnr',
                        'elements' => array(
                            'amnr',
                            'artikelname',
                            'details' => array(
                                'rootTag' => 'details',
                                'rowTag' => 'detail',
                                'elements' => array(
                                    'ognr'=>'og',
                                    'oenr'=>'oe',
                                    'amnummer'=>'amnr',
                                    'artikelname',
                                    'preisausgabe',
                                    'ausgabestk',
                                    'rueckgabestk',
                                    'differenz',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    );
} else {
    $options = array(
        'encoder' => false,
        'rootTag' => 'S190',
        'idColumn' => 'persnr',
        'rowTag' => 'person',
        'elements' => array(
            'persnr',
            'name',
            'arbeitsmittel' => array(
                'rootTag' => 'arbeitsmittel',
                'rowTag' => 'am',
                'idColumn' => 'amnr',
                'elements' => array(
                    'amnr',
                    'artikelname',
                    'details' => array(
                        'rootTag' => 'details',
                        'rowTag' => 'detail',
                        'idColumn' => 'id',
                        'elements' => array(
                            'datum',
                            'og',
                            'oe',
			    'bemerkung',
                            'preisausgabe',
                            'ausgabestk',
                            'rueckgabestk',
                            'differenz',
                            'preisdiff',
                        ),
                    ),
                ),
            ),
        ),
    );
}
//echo $sql;
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
$domxml->save("S190.xml");
?>
