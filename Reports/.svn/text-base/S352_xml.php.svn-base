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

$sql = "select behaelternr,`eink-artikel`.`art-name1`,dbehinventur.kunde,dksd.Name1,dbehinventur.zustand_id,dbehinventur.inhalt_id,dbehinventur.platz_id,dbehinventur.stk";
$sql.= " from dbehinventur";
$sql.= " join `eink-artikel` on `eink-artikel`.`art-nr`=dbehinventur.behaelternr";
$sql.= " join dksd on dksd.Kunde=dbehinventur.kunde";
$sql.= " where";
$sql.= "     datum='$invdatumDB'";
$sql.="      and dbehinventur.behaelternr between $behnrvon and $behnrbis";
$sql.= " order by dbehinventur.behaelternr,dbehinventur.kunde,dbehinventur.zustand_id,dbehinventur.inhalt_id";

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
    'encoder' => false,
    'rootTag' => 'S352',
    'idColumn' => 'behaelternr',
    'rowTag' => 'behaelter',
    'elements' => array(
        'behaelternr',
        'art-name1',
        'kunden' => array(
            'idColumn' => 'kunde',
            'rowTag' => 'kunde',
            'elements' => array(
                'kundenr' => 'kunde',
                'Name1',
                'zustande' => array(
                    'idColumn' => 'zustand_id',
                    'rowTag' => 'zustand',
                    'elements' => array(
                        'zustand_id',
                        'inhalte' => array(
                            'idColumn' => 'inhalt_id',
                            'rowTag' => 'inhalt',
                            'elements' => array(
                                'inhalt_id',
                                'zustand_id',
                                'plaetze' => array(
                                    'idColumn' => 'platz_id',
                                    'rowTag' => 'platz',
                                    'elements' => array(
                                        'zustand_id',
                                        'inhalt_id',
                                        'platz_id',
                                        'stk',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
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


//$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S352.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
