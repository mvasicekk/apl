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

$sql = "select";
$sql.= "    dkopf.Kunde as kunde,";
$sql.= "     dksd.Name1 as kdname,";
$sql.= "     dpos.Teil as teil,";
$sql.= "     dkopf.Teilbez as teilbez,";
$sql.= "     dpos.`TaetNr-Aby` as abgnr,";
$sql.= "     dpos.`TaetBez-Aby-D` as schwgrad";
$sql.= " from dpos";
$sql.= " join dkopf on dkopf.Teil=dpos.Teil";
$sql.= " join dksd on dkopf.Kunde=dksd.Kunde";
$sql.= " where";
$sql.= "     dpos.`TaetNr-Aby`=2";
$sql.= "     and dkopf.kunde between '$kundevon' and '$kundebis'";
$sql.=" order by dkopf.Kunde,dpos.Teil";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

function get_schwgrad_num($record)
{
        $schwgradNum = floatval($record['schwgrad']);
        return $schwgradNum;
}

function get_anfang($record)
{
        $schwgradNum = floatval($record['schwgrad']);
        if($schwgradNum<=7)
            return 'J';
        else
            return 'N';
}

$options = array(
		'encoder'=>false,
		'rootTag'=>'S410',
                'idColumn'=>'kunde',
      		'rowTag'=>'kunde',
		'elements'=>array(
                    'kundenr1'=>'kunde',
                    'teile'=>array(
                        'idColumn'=>'teil',
                        'rowTag'=>'teil',
                        'elements'=>array(
                            'teilnr'=>'teil',
                            'kundenr2'=>'kunde',
                            'kdname',
                            'teilbez',
                            'abgnr',
                            'schwgrad',
                            'schwgrad_num'=>'#get_schwgrad_num();',
                            'anfang'=>'#get_anfang();',
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
$domxml->save("S410.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
