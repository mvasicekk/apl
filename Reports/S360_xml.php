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
$sql.="     dkurs.kurs,";
$sql.="     dreklamation.kunde,";
$sql.="     dksd.name1 as name,";
$sql.="     dreklamation.rekl_nr,";
$sql.="     dreklamation.kd_rekl_nr,";
$sql.="     dreklamation.kd_kd_rekl_nr,";
$sql.="     dreklamation.kd_kd_kd_rekl_nr,";
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
$sql.="     dreklamation.stk_expediert,";
$sql.="     dreklamation.stk_reklammiert,";
$sql.="     dreklamation.interne_bewertung,";
//$sql.="     dreklamation.anerkannt_stk_ja,";
$sql.="     dreklamation.anerkannt_stk_nein,";
$sql.="     dreklamation.anerkannt_stk_ausschuss,";
$sql.="     dreklamation.anerkannt_wert_ausschuss,";
$sql.="     dreklamation.anerkannt_stk_nacharbeit,";
$sql.="     dreklamation.anerkannt_wert_nacharbeit,";
$sql.="     dreklamation.strafe_persnr,";
$sql.="     dreklamation.strafe_wert,";
$sql.="     dreklamation.erstellt,";
$sql.="     dreklamation.bemerkung,";
$sql.="     dreklamation.andere_kosten,";
$sql.="     dreklamation.giesstag";
$sql.=" from ";
$sql.="     dreklamation";
$sql.=" left join (dkopf,dksd,dkurs) on ";
$sql.="             (dkopf.Teil=dreklamation.teil and ";
$sql.="             dksd.kunde=dreklamation.kunde and ";
$sql.="             ('$erhbis' between dkurs.gilt_von and dkurs.gilt_bis))";
$sql.=" where";
$sql.="     ( dreklamation.kunde between '$kundevon' and '$kundebis')";
$sql.="     and";
$sql.="     ( dreklamation.rekl_datum between '$erhvon' and '$erhbis')";
if(strlen($reklnr)>0)
$sql.="     and ( dreklamation.rekl_nr like '$reklnr')";
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
					'rootTag'=>'S360',
					'idColumn'=>'kunde',
					'rowTag'=>'kunde',
					'elements'=>array(
                                                'kurs' => 'kurs',                                        
						'kundenr'=>'kunde',
                                                'kunde_name'=>'name',
						'reklamationen'=>array(
							'rootTag'=>'reklamationen',
							'rowTag'=>'reklamation',
							'idColumn'=>'id',
							'elements'=>array(
								'rekl_nr',
								'kd_rekl_nr',
								'kd_kd_rekl_nr',
                                                                'kd_kd_kd_rekl_nr',
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
                                                                'stk_expediert',
                                                                'stk_reklammiert',
                                                                'interne_bewertung',
//                                                                'anerkannt_stk_ja',
                                                                'anerkannt_stk_nein',
                                                                'anerkannt_stk_ausschuss',
								'anerkannt_wert_ausschuss',
                                                                'anerkannt_stk_nacharbeit',
								'anerkannt_wert_nacharbeit',
                                                                'strafe_persnr',
                                                                'strafe_wert',
                                                                'erstellt',
                                                                'bemerkung',
                                                                'andere_kosten',
                                                                'giesstag',
							),
						),
					)
				);			
// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML
					
$domxml = $query2xml->getXML($sql,$options);

// second sql request to dabmahnungen for fees
$sql2=" select";
$sql2.="     dreklamation.rekl_nr,";
$sql2.="     dreklamation.id,";
$sql2.="     dabmahnung.persnr,";
$sql2.="     dabmahnung.vorschlag_betrag,";
$sql2.="     dabmahnung.betr,";
$sql2.="     dabmahnung.id as ab_ID";
$sql2.=" from ";
$sql2.="     dreklamation";
$sql2.=" left join dabmahnung on ";
$sql2.="     dreklamation.id = dabmahnung.dreklamation_id";
$sql2.=" where";
$sql2.="     ( dreklamation.kunde between '$kundevon' and '$kundebis')";
$sql2.="     and";
$sql2.="     ( dreklamation.rekl_datum between '$erhvon' and '$erhbis')";
if(strlen($reklnr)>0)
$sql2.="     and ( dreklamation.rekl_nr like '$reklnr')";
$sql2.=" order by";
$sql2.="     dreklamation.rekl_nr";

//echo $sql2;

$options2 = array(
					'encoder'=>false,
					'rootTag'=>'S360Strafen',
					'idColumn'=>'id',
					'rowTag'=>'reklamation',
					'elements'=>array(
                                                'rekl_nr',
						'id',
						'strafen'=>array(
							'rootTag'=>'strafen',
							'rowTag'=>'strafe',
							'idColumn'=>'ab_ID',
							'elements'=>array(
								'persnr',
								'vorschlag_betrag',
                                                                'betr',
							),
						),
					)
				);
$domxml2 = $query2xml->getXML($sql2,$options2);

$domxml->encoding="UTF-8";     
$domxml2->encoding="UTF-8";  
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

foreach($kunden as $kunde)
{
    $temp = $kunde->getElementsByTagName("kurs");
    $temp = $temp->item(0);
    $EUR_CZK = $temp->nodeValue;
    
    $kundeChildNodes = $kunde->childNodes;
    $reklamationen = $kunde->getElementsByTagName("reklamation");
    foreach($reklamationen as $reklamation)
    {
	$reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'gewicht') * getValueForNode($reklChilds,'anerkannt_stk_nein'),0);
        $element = $domxml->createElement('anerkannt_gew_nein', $wight);
        $reklamation->appendChild($element);
        
        $reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'gewicht') * getValueForNode($reklChilds,'anerkannt_stk_nacharbeit'),0);
        $element = $domxml->createElement('anerkannt_gew_nacharbeit', $wight);
        $reklamation->appendChild($element);
        
        $reklChilds = $reklamation->childNodes;
        $wight = round(getValueForNode($reklChilds,'gewicht') * getValueForNode($reklChilds,'anerkannt_stk_ausschuss'),0);
        $element = $domxml->createElement('anerkannt_gew_ausschuss', $wight);
        $reklamation->appendChild($element);
        
        // getting import numbers 
        $importsCount++;
        $imports[] = getValueForNode($reklChilds, "import");
    }
}
/*
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
}*/

// inserting calculated value: count*price = cost of reparation
// if there is no Ausschuss for client number 195 then use the value from database
//$kunden=$domxml->getElementsByTagName("kunde");
//foreach($kunden as $kunde)
//{
//    $kundeChildNodes = $kunde->childNodes;
//    
//    $kundenr = getValueForNode($kundeChildNodes, 'kundenr');
//    if ($kundenr == 195)
//    {
//        $reklamationen = $kunde->getElementsByTagName("reklamation");
//        foreach($reklamationen as $reklamation)
//        {           
//            $reklChilds = $reklamation->childNodes;
//            $price = round(getValueForNode($reklChilds,'kosten_auss') * getValueForNode($reklChilds,'anerkannt_stk_ausschuss') * $EUR_CZK,0);
//            foreach($reklChilds as $reklChild)
//            {
//                if ($reklChild->nodeName == 'anerkannt_wert_ausschuss')
//                    $reklChild->nodeValue = $price;
//            }
//        }
//    }
//}

// replace person.nr. 9999 with -
$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde)
{
    $kundeChildNodes = $kunde->childNodes;
    $reklamationen = $kunde->getElementsByTagName("reklamation");
    foreach($reklamationen as $reklamation)
    {        
        $reklChilds = $reklamation->childNodes;
        foreach($reklChilds as $values)
        {
            if($values->nodeName == 'strafe_persnr')
            {
                if($values->nodeValue == 9999)
                {
                    $values->nodeValue = "-";
                }
            }
        }
    }
}


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S360.xml");
//$domxml2->save("S360_2.xml");
?>
