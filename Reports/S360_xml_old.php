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


// SQL

$sql.=" select";
$sql.="     dreklamation.kunde,";
$sql.="     dreklamation.rekl_nr,";
$sql.="     dreklamation.id,";
$sql.="     dreklamation.import,";
$sql.="     dreklamation.export,";
$sql.="     DATE_FORMAT(dreklamation.rekl_datum,'%Y-%m-%d') as erhalten_am,";
$sql.="     DATE_FORMAT(dreklamation.rekl_erledigt_am,'%Y-%m-%d') as erledigt_am,";
$sql.="     dreklamation.beschr_abweichung,";
$sql.="     dreklamation.beschr_ursache,";
$sql.="     dreklamation.teil,";
$sql.="     dkopf.Gew as gewicht,";
$sql.="     dkopf.kosten_stk_auss as kosten_auss,";
$sql.="     dreklamation.stk_expediert,";
$sql.="     dreklamation.stk_reklammiert,";
$sql.="     dreklamation.interne_bewertung,";
//$sql.="     dreklamation.anerkannt_stk_ja,";
$sql.="     dreklamation.anerkannt_stk_nein,";
$sql.="     dreklamation.anerkannt_stk_ausschuss,";
$sql.="     dreklamation.anerkannt_stk_nacharbeit,";
$sql.="     dreklamation.strafe_persnr,";
$sql.="     dreklamation.strafe_wert,";
$sql.="     dreklamation.erstellt,";
$sql.="     dreklamation.bemerkung,";
$sql.="     dreklamation.andere_kosten";
$sql.=" from ";
$sql.="     dreklamation";
$sql.=" left join dkopf on dkopf.Teil=dreklamation.teil";
$sql.=" where";
$sql.="     ( dreklamation.kunde between '$kundevon' and '$kundebis')";
$sql.="     and";
$sql.="     ( dreklamation.rekl_datum between '$erhvon' and '$erhbis')";
$sql.=" order by";
$sql.="     dreklamation.kunde,";
$sql.="     dreklamation.rekl_datum,";
$sql.="     dreklamation.rekl_nr";

//echo "sql=$sql"."<br>";
//return;

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$options = array(
					'encoder'=>false,
					'rootTag'=>'S360',
					'idColumn'=>'kunde',
					'rowTag'=>'kunde',
					'elements'=>array(
						'kundenr'=>'kunde',
						'reklamationen'=>array(
							'rootTag'=>'reklamationen',
							'rowTag'=>'reklamation',
							'idColumn'=>'id',
							'elements'=>array(
								'rekl_nr',
                                                                'import',
                                                                'export',
								'erhalten_am',
                                                                'erledigt_am',
								'beschr_abweichung',
                                                                'beschr_ursache',
								'teil',
								'gewicht',
                                                                'kosten_auss',
                                                                'stk_expediert',
                                                                'stk_reklammiert',
                                                                'interne_bewertung',
//                                                                'anerkannt_stk_ja',
                                                                'anerkannt_stk_nein',
                                                                'anerkannt_stk_ausschuss',
                                                                'anerkannt_stk_nacharbeit',
                                                                'strafe_persnr',
                                                                'strafe_wert',
                                                                'erstellt',
                                                                'bemerkung',
                                                                'andere_kosten',
                                                                
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

// inserting calculated value: wight*count for new field "total wight"
$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde)
{
    $kundeChildNodes = $kunde->childNodes;
    $reklamationen = $kunde->getElementsByTagName("reklamation");
    foreach($reklamationen as $reklamation)
    {
	$reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'gewicht') * getValueForNode($reklChilds,'anerkannt_stk_nein'),1);
        $element = $domxml->createElement('anerkannt_gew_nein', $wight);
        $reklamation->appendChild($element);
        
        $reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'gewicht') * getValueForNode($reklChilds,'anerkannt_stk_nacharbeit'),1);
        $element = $domxml->createElement('anerkannt_gew_nacharbeit', $wight);
        $reklamation->appendChild($element);
        
        $reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'gewicht') * getValueForNode($reklChilds,'anerkannt_stk_ausschuss'),1);
        $element = $domxml->createElement('anerkannt_gew_ausschuss', $wight);
        $reklamation->appendChild($element);
    }
}

// inserting calculated value: count*price = cost of reparation
$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde)
{
    $kundeChildNodes = $kunde->childNodes;
    $reklamationen = $kunde->getElementsByTagName("reklamation");
    foreach($reklamationen as $reklamation)
    {
	$reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'kosten_auss') * getValueForNode($reklChilds,'anerkannt_stk_nein'),0);
        $element = $domxml->createElement('anerkannt_wert_nein', $wight);
        $reklamation->appendChild($element);
        
        $reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'kosten_auss') * getValueForNode($reklChilds,'anerkannt_stk_nacharbeit'),0);
        $element = $domxml->createElement('anerkannt_wert_nacharbeit', $wight);
        $reklamation->appendChild($element);
        
        $reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'kosten_auss') * getValueForNode($reklChilds,'anerkannt_stk_ausschuss'),0);
        $element = $domxml->createElement('anerkannt_wert_ausschuss', $wight);
        $reklamation->appendChild($element);
    }
}


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S360.xml");
?>
