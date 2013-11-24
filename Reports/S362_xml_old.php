<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";
require_once '../db.php';


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");


// SQL

$sql.=" select";
//$sql.="     dkurs.kurs,";
$sql.="     dreklamation.kunde,";
$sql.="     dksd.name1 as name,";
$sql.="     dreklamation.rekl_nr,";
$sql.="     dreklamation.kd_rekl_nr,";
$sql.="     dreklamation.kd_kd_rekl_nr,";
$sql.="     dreklamation.id,";
$sql.="     dreklamation.import,";
$sql.="     dreklamation.export,";
$sql.="     DATE_FORMAT(dreklamation.rekl_datum,'%Y-%m-%d') as erhalten_am,";
$sql.="     DATE_FORMAT(dreklamation.rekl_erledigt_am,'%Y-%m-%d') as erledigt_am,";
$sql.="     dreklamation.beschr_abweichung,";
$sql.="     dreklamation.beschr_ursache,";
$sql.="     dreklamation.beschr_beseitigung,";
$sql.="     dreklamation.teil,";
$sql.="     dkopf.Gew as gewicht,";
$sql.="     dkopf.kosten_stk_auss as kosten_auss,";
$sql.="     dkopf.`Muster-Platz` as muster_platz,";
$sql.="     DATE_FORMAT(dkopf.`Muster-vom`,'%Y-%m-%d') as muster_vom,";
$sql.="     dreklamation.stk_expediert,";
$sql.="     dreklamation.stk_reklammiert,";
$sql.="     dreklamation.interne_bewertung,";
//$sql.="     dreklamation.anerkannt_stk_ja,";
$sql.="     dreklamation.anerkannt_stk_nein,";
$sql.="     dreklamation.anerkannt_stk_ausschuss,";
// zadat ?
//$sql.="     dreklamation.anerkannt_wert_ausschuss,";
$sql.="     dreklamation.anerkannt_stk_nacharbeit,";
//$sql.="     dreklamation.anerkannt_wert_nacharbeit,";
$sql.="     dreklamation.strafe_persnr,";
$sql.="     dreklamation.strafe_wert,";
$sql.="     dreklamation.erstellt,";
$sql.="     dreklamation.bemerkung,";
$sql.="     dreklamation.andere_kosten,";
$sql.="     dreklamation.bearbeiter_kunde,";
$sql.="     DATE_FORMAT(dreklamation.mt_datum,'%Y-%m-%d') as mt_datum,";
$sql.="     dreklamation.mt_fax,";
$sql.="     dreklamation.mt_email,";
$sql.="     dreklamation.mt_brief,";
$sql.="     dreklamation.mt_telefon,";
$sql.="     dreklamation.mt_mund,";
$sql.="     dreklamation.negativ_muster,";
$sql.="     dreklamation.identif_hinweise,";
$sql.="     dreklamation.giesstag";
$sql.=" from ";
$sql.="     dreklamation";
$sql.=" join dksd on dksd.kunde=dreklamation.kunde";
//$sql.=" join dkurs on dkurs.kunde=dreklamation.kunde";
$sql.=" left join dkopf on dkopf.teil=dreklamation.teil";
//$sql.=" left join (dkopf,dksd,dkurs) on ";
//$sql.="             (dkopf.Teil=dreklamation.teil and ";
//$sql.="             dksd.kunde=dreklamation.kunde and ";
//$sql.="             ('$erhbis' between dkurs.gilt_von and dkurs.gilt_bis))";
$sql.=" where";
$sql.="     ( dreklamation.rekl_nr like '$reklnr')";
//$sql.="     and";
//$sql.="     ( dreklamation.rekl_datum between '$erhvon' and '$erhbis')";
$sql.=" order by";
$sql.="     dreklamation.kunde,";
$sql.="     dreklamation.rekl_nr,";
$sql.="     dreklamation.rekl_datum";

//echo "sql=$sql"."<br>";
//return;

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$options = array(
					'encoder'=>false,
					'rootTag'=>'S362',
					'idColumn'=>'kunde',
					'rowTag'=>'kunde',
					'elements'=>array(
//                                                'kurs' => 'kurs',                                        
						'reklamationen'=>array(
                                                        'rootTag'=>'reklamationen',
							'rowTag'=>'reklamation',
							'idColumn'=>'id',
							'elements'=>array(
                                                                'kundenr'=>'kunde',
                                                                'kunde_name'=>'name',
								'rekl_nr',
								'kd_rekl_nr',
								'kd_kd_rekl_nr',
                                                                'import',
                                                                'export',
								'erhalten_am',
                                                                'erledigt_am',
								'beschr_abweichung',
                                                                'beschr_ursache',
                                                                'beschr_beseitigung',
								'teil',
								'gewicht',
                                                                'kosten_auss',
								'muster_platz',
								'muster_vom',
                                                                'stk_expediert',
                                                                'stk_reklammiert',
                                                                'interne_bewertung',
//                                                                'anerkannt_stk_ja',
                                                                'anerkannt_stk_nein',
                                                                'anerkannt_stk_ausschuss',
								//'anerkannt_wert_ausschuss',
                                                                'anerkannt_stk_nacharbeit',
								//'anerkannt_wert_nacharbeit',
                                                                'strafe_persnr',
                                                                'strafe_wert',
                                                                'erstellt',
                                                                'bemerkung',
                                                                'andere_kosten',
                                                                'giesstag',
								'bearbeiter_kunde',
								'mt_datum',
								'mt_fax',
								'mt_email',
								'mt_brief',
								'mt_telefon',
								'mt_mund',
								'negativ_muster',
								'identif_hinweise',
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

// Money exchange value EUR_CZK - will appear in header of the pdf output
$EUR_CZK = 25;

$importsCount = 0;

$imports = array();

$apl = AplDB::getInstance();
$EUR_CZK = $apl->getKurs(date('Y-m-d'),'EUR','CZK');

foreach($kunden as $kunde)
{
    $temp = $kunde->getElementsByTagName("kurs");
    $temp = $temp->item(0);
//    $EUR_CZK = $temp->nodeValue;
    
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
        
        // getting import numbers 
        $importsCount++;
        $imports[] = getValueForNode($reklChilds, "import");
    }
}

if (array_key_exists(0, $imports))
{
    // getting value for price calc: nacharbeit
    $sql = "SELECT                                          ";
    $sql.= "    drueck.AuftragsNr,                          ";
    $sql.= "    round(sum(drueck.`Stück` * `VZ-IST`)*7)     ";
    $sql.= "FROM                                            ";
    $sql.= "    drueck                                      ";
    $sql.= "WHERE                                           ";
    $sql.= "    drueck.AuftragsNr='$imports[0]'             ";
    $counter = 1;
    while (array_key_exists($counter, $imports))
    {
        $counter++;
        $sql.= "OR   drueck.AuftragsNr='$imports[$counter]' ";
    }
    $sql.= "GROUP BY                                        ";
    $sql.= "    drueck.AuftragsNr                           ";  

    //echo $sql;
    //return;
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
        $wight = round(getValueForNode($reklChilds,'kosten_auss') * getValueForNode($reklChilds,'anerkannt_stk_nacharbeit') * $EUR_CZK,0);
        $element = $domxml->createElement('anerkannt_wert_nacharbeit', $wight);
        $reklamation->appendChild($element);
       
        $reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'kosten_auss') * getValueForNode($reklChilds,'anerkannt_stk_ausschuss') * $EUR_CZK,0);
        $element = $domxml->createElement('anerkannt_wert_ausschuss', $wight);
        $reklamation->appendChild($element);
    }
}


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S362.xml");
?>
