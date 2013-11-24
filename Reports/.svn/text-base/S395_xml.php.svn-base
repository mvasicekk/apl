<?php
session_start();
require_once('XML/Query2XML.php');
require_once('../db.php');
require_once "../fns_dotazy.php";




// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
//$db = &DB::connect('mysql://root:nuredv@localhost/apl');

$aplDB = AplDB::getInstance();

$kunde = $kd;
//global $db;
$pcip=get_pc_ip();

//$db->setFetchMode(DB_FETCHMODE_ASSOC);
//$db->query("set names utf8");
// vytahnu si parametry z XML souboru

//seznam vsech dilu od daneho zakaznika


$domxml = new DOMDocument("1.0");
$domxml->encoding="UTF-8";


$root=$domxml->createElement("S395");
$domxml->appendChild($root);

//$element=$domxml->createElement("parameters");
//$parametry=$domxml->firstChild;
//$parametry->appendChild($element);
//$i=1;

$node = $domxml->createElement('kunde');
$data = $domxml->createTextNode($kunde);
$node->appendChild($data);
$root->appendChild($node);

// seznam skladu pro legendu

$lagerArray = $aplDB->getLagerArray();
if(is_array($lagerArray)){
    foreach($lagerArray as $klic=>$popis){

        $node = $domxml->createElement('lager');

        $node1 = $domxml->createElement('kz');
        $data1 = $domxml->createTextNode($klic);
        $node1->appendChild($data1);
        $node->appendChild($node1);

        $node1 = $domxml->createElement('beschreibung');
        $data1 = $domxml->createTextNode($popis);
        $node1->appendChild($data1);
        $node->appendChild($node1);

        $root->appendChild($node);
    }
}
//$data = $domxml->createTextNode($kunde);
//$node->appendChild($data);


//$now = date('Y-m-d H:i:s');
$now = $zeitpunkt;

$node = $domxml->createElement('bisdatum');
$data = $domxml->createTextNode($now);
$node->appendChild($data);
$root->appendChild($node);

$knd = $kd;

if(strlen($teil)>0)
    $teilArray = array($teil=>"nic");
else
    $teilArray = AplDB::getInstance()->getActiveTeilArrayForKunde($knd,180);
    
//foreach ($teilArray as $klic => $hodnota) {
//    echo "klic = $klic, hodnota = $hodnota<br>";
//}

//echo "<hr>";
//$dil = 45;


foreach ($teilArray as $dil=>$hodnota){

    $node = $domxml->createElement('teil');


    $node1 = $domxml->createElement('teilnr');
    $data1 = $domxml->createTextNode($dil);
    $node1->appendChild($data1);
    $node->appendChild($node1);


    $row = AplDB::getInstance()->getLagerBestandForTeil($dil, $now);
    $inventurDatum = AplDB::getInstance()->getInventurDatumForTeil($dil);

    if(is_array($row)){
        $node1 = $domxml->createElement('inventurdatum');
        $data1 = $domxml->createTextNode($inventurDatum);
        $node1->appendChild($data1);
        $node->appendChild($node1);
    }

    if(!is_array($row)){
        $node1 = $domxml->createElement('error');
        $data1 = $domxml->createTextNode($row);
        $node1->appendChild($data1);
        $node->appendChild($node1);
    }
    else{
        $node1 = $domxml->createElement('sklady');
//        $data1 = $domxml->createTextNode("mam hodnoty skladu");
//        $node1->appendChild($data1);

        $lagerArray = array(
                            "0D",
//                            "0S",
                            "1R",
                            "2T",
                            "3P",
                            "4R",
                            "5K",
                            "5Q",
                            "6F",
                            "8E",
                            "XX",
                            "XY",
                            "8V",
                            "8X",
                            "9V",
                            "9R",
                            "A2",
                            "A4",
                            "A6",
                            "B2",
                            "B4",
                            "B6",
        );

        foreach($lagerArray as $lager){

            // bewegung plus
            $lager1 = "plus_".$lager;
            $node2 = $domxml->createElement($lager1);
            $data2 = $domxml->createTextNode($row[$lager1]);
            $node2->appendChild($data2);
            $node1->appendChild($node2);
            $node->appendChild($node1);

            // bewegung minus
            $lager1 = "minus_".$lager;
            $node2 = $domxml->createElement($lager1);
            $data2 = $domxml->createTextNode($row[$lager1]);
            $node2->appendChild($data2);
            $node1->appendChild($node2);
            $node->appendChild($node1);

            //inventur
            $lager1 = "inventur_".$lager;
            $node2 = $domxml->createElement($lager1);
            $data2 = $domxml->createTextNode($row['inventur'][$lager]);
            $node2->appendChild($data2);
            $node1->appendChild($node2);
            $node->appendChild($node1);
        }
    }

    $root->appendChild($node);


}

// pokusy s polem parameters
// rozkouskovat si ho na jednotlive polozky
// a vytvorim pole parametru
// TODO doplnit nejakou moznost zformatovani, napr. datum zadane ve formulari by melo nejak vypadat
// mozna by bylo lepsi resit to AJAXem uz pri zadavani do formulare
// ale uz pri generovani formulare budu muset pridat handlery podle planovaneho obsahu formulare
// tady uz to nepujde


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

$element = $domxml->createElement("parameters");
$parametry = $domxml->firstChild;
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


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S395.xml");

?>
