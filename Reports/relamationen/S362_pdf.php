<?php
session_start();

set_time_limit(20);

require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S362";
$doc_subject = "S362";
$doc_keywords = "S362";

// necham si vygenerovat XML

$parameters=$_GET;

$a = AplDB::getInstance();

$reklnr=$_GET['reklnr'];

$reklnr = strtr($reklnr, '*', '%');
$user = $_SESSION['user'];


require_once('S362_xml.php');

//exit;
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
//parameters
$parameters=$domxml->getElementsByTagName("parameters");


foreach ($parameters as $param)
{
        // N1, N2, N3, N4
	$parametry=$param->childNodes;
	// v ramci parametru si prectu label a hodnotu
	//foreach($parametry as $parametr)
	//{
                // label, value
		//$parametr=$parametr->childNodes;
		//foreach($parametr as $par)
		//{
                //        if($par->nodeValue=="Erhalten am von")
                //                $label="Erhalten am";
                //        else if($par->nodeValue=="Erhalten am bis")
                //                $label=" - ";
                //        else if($par->nodeName=="label")
		//		$label=$par->nodeValue;
		//	else if($par->nodeName=="value")
		//		$value=$par->nodeValue;
		//}
                
                $value1 = $parametry->item(0)->lastChild->nodeValue;
                $value2 = $parametry->item(1)->lastChild->nodeValue;
                $value3 = $parametry->item(2)->lastChild->nodeValue;
                $value4 = $parametry->item(3)->lastChild->nodeValue;
                
                $params =   "ReklNr: "       . $value1;
//                            " Kunde bis: "      . $value2 .
//                            " Erhalten am: "    . $value3 .
//                            " - "               . $value4 ;
                
		//if(strtolower($label)!="password")
                //   if ($label = " - ");
		//	$params .= $label.": ".$value."  ";
	//}
}


// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$pole,$headervyskaradku,$popisek, $fontSize)
{
	
	//$pdfobjekt->SetFont("FreeSans", "B", 8);
	//$pdfobjekt->Cell(0,$headervyskaradku,$popisek,'0',1,'L',0);
	$pdfobjekt->SetFillColor(255,255,200,1);
	
	$pdfobjekt->SetFontSize($fontSize);
	
        // foreach header line
        foreach($pole as $row)
	{
            // foreach cell in header line
            foreach($row as $cell)
            {
                if (array_key_exists("hight", $cell))
                    $pdfobjekt->MyMultiCell($cell["sirka"],$cell["hight"],$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']); 
                else
                    $pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
            }
            $pdfobjekt->Ln(); // new line in header
        }
	$pdfobjekt->Ln();
        $pdfobjekt->Ln();
	//$pdfobjekt->Ln();
}

function getValueForNode($nodelist,$nodename)
{
	$nodevalue="";
	foreach($nodelist as $node)
	{
		if($node->nodeName==$nodename)
		{
			$nodevalue=$node->nodeValue;
			return $nodevalue;
		}
	}
	return $nodevalue;
}

function getClosestSpace($text, $searchString, $referenceValue)
{
    $offset = -1;
    $difference = 9999;
    // strpos ( string $haystack , mixed $needle [, int $offset = 0 ] )
    while ($result = strpos($text, $searchString, $offset+1))
    {
        if ($referenceValue - $result < $difference && $referenceValue - $result >= 0)            
        {    
            $difference = $referenceValue - $result;
        }
        else
        {
            
            return $offset;
        }
        $offset = $result;        
    }
    
    return false;
}

function SplittingObsah($pdfobjekt, $obsah, $cell, $lineNum, $Zelle)
{
    if (strpos($obsah,"\r\n"))
    {
        $SplitContent = split("\r\n", $obsah);
        if ($cell["zeilen"][$lineNum-1]["node"] == $Zelle["node"])
        {
            if ($cell["zeilen"][$lineNum-2]["node"] == $Zelle["node"])
            {
                $obsah = trim ($SplitContent[2]);
            }
            else
            {
                $obsah = trim ($SplitContent[1]);
            }
        }
        else
        {
            $obsah = trim ($SplitContent[0]);
        }
    }
    else
    {
        $obsah = trim (substr ($obsah, $Zelle["substr"][0], $Zelle["substr"][1]));
    }
                
    // just to be sure that none string which will be writen in the pdf is longer than the cell
    if ($cell["sirka"] > 1)
    {
        $stopCounter = 0;
        while ($pdfobjekt->GetStringWidth($obsah) > $cell["sirka"] - 1.5)
        {
             $stopCounter++;
             if ($stopCounter > 100)
             {
                  echo "Achtung! Endlosschleife!";
                  return;
             }
             // removes the last sign of a string
             $obsah = substr($obsah, 0, -1);                   
        }
    }
    
    return $obsah;
}

$fontSizeHeader = 12;
$fontStyleHeader = "b";

$fontSizeSubheaders = 9;
$fontStyleSubheaders = "u";

$fontSizeContent = 8;
$fontStyleContent = "";

$fontSizeSubContent = 7;
$fontStyleSubContent = "";

$cells = 
array(
  
//"rekl_nr" 
//=> array ("content"=>"Rekl.-Nr.:    +","width"=>80,"hight"=>10,"x"=>115,"y"=>20,"ram"=>'',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeHeader,"fontStyle"=>$fontStyleHeader),   
    
"1"    
=> array ("content"=>"Rekl.-Nr.:","width"=>35.5,"hight"=>5,"x"=>10,"y"=>30,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),
    
"rekl_nr" 
=> array ("content"=>"","width"=>35.5,"hight"=>6,"x"=>10,"y"=>35,"ram"=>'LR',"align"=>"C","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>"b"),

"kd_rekl_nr" 
=> array ("content"=>"2) +","width"=>35.5,"hight"=>3,"x"=>10,"y"=>41,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubContent,"fontStyle"=>$fontStyleSubContent),

"kd_kd_rekl_nr" 
=> array ("content"=>"3) +","width"=>35.5,"hight"=>3,"x"=>10,"y"=>44,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubContent,"fontStyle"=>$fontStyleSubContent),

"kd_kd_kd_rekl_nr" 
=> array ("content"=>"4) +","width"=>35.5,"hight"=>3,"x"=>10,"y"=>47,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubContent,"fontStyle"=>$fontStyleSubContent),    
    
"2"    
=> array ("content"=>"Auftrag:","width"=>35.5,"hight"=>5,"x"=>50.5,"y"=>30,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),
    
"import" 
=> array ("content"=>"IM:   +","width"=>35.5,"hight"=>7.5,"x"=>50.5,"y"=>35,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
   
"export" 
=> array ("content"=>"EX:  +","width"=>35.5,"hight"=>7.5,"x"=>50.5,"y"=>42.5,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"3"    
=> array ("content"=>"Teil:","width"=>23.5,"hight"=>5,"x"=>10,"y"=>55,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  
    
"teil" 
=> array ("content"=>"","width"=>23.5,"hight"=>5,"x"=>10,"y"=>60,"ram"=>'LR',"align"=>"C","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"gewicht" 
=> array ("content"=>" + kg","width"=>23.5,"hight"=>5,"x"=>10,"y"=>65,"ram"=>'LR',"align"=>"C","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),    

"giesstag" 
=> array ("content"=>"","width"=>23.5,"hight"=>5,"x"=>10,"y"=>70,"ram"=>'LRB',"align"=>"C","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),        
    
"4"    
=> array ("content"=>"Stück:","width"=>23.5,"hight"=>5,"x"=>38.5,"y"=>55,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  
    
"4.1" 
=> array ("content"=>"Expediert:","width"=>23.5,"hight"=>7.5,"x"=>38.5,"y"=>60,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"stk_expediert" 
=> array ("content"=>"","width"=>23.5,"hight"=>7.5,"x"=>38.5,"y"=>60,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
 
"4.2" 
=> array ("content"=>"Reklamiert:","width"=>23.5,"hight"=>7.5,"x"=>38.5,"y"=>67.5,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
    
"stk_reklammiert" 
=> array ("content"=>"","width"=>23.5,"hight"=>7.5,"x"=>38.5,"y"=>67.5,"ram"=>'LRB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"5"    
=> array ("content"=>"Beschreibung der Abweichung:","width"=>50,"hight"=>5,"x"=>10,"y"=>80,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  
    
"beschr_abweichung" 
=> array ("content"=>" *3","width"=>50,"hight"=>5,"x"=>10,"y"=>85,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"6"    
=> array ("content"=>"Beschreibung der Ursache:","width"=>50,"hight"=>5,"x"=>65,"y"=>80,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  
    
"beschr_ursache" 
=> array ("content"=>" *3","width"=>50,"hight"=>5,"x"=>65,"y"=>85,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
   
"7"    
=> array ("content"=>"Art der Beseitigung:","width"=>50,"hight"=>5,"x"=>120,"y"=>80,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  
    
"beschr_beseitigung" 
=> array ("content"=>" *3","width"=>50,"hight"=>5,"x"=>120,"y"=>85,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
   
"8"    
=> array ("content"=>"Bemerkungen:","width"=>110,"hight"=>5,"x"=>175,"y"=>80,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  
    
"bemerkung" 
=> array ("content"=>" *3","width"=>110,"hight"=>5,"x"=>175,"y"=>85,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

// anerkannt Ausschuss
"9"    
=> array ("content"=>"Ausschuss:","width"=>25,"hight"=>5,"x"=>67,"y"=>55,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  

"10" 
=> array ("content"=>"Stück:","width"=>25,"hight"=>5,"x"=>67,"y"=>60,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"anerkannt_stk_ausschuss" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>67,"y"=>60,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"11" 
=> array ("content"=>"Gewicht:","width"=>25,"hight"=>5,"x"=>67,"y"=>65,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"anerkannt_gew_ausschuss" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>67,"y"=>65,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
    
"12" 
=> array ("content"=>"[CZK]:","width"=>25,"hight"=>5,"x"=>67,"y"=>70,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"anerkannt_wert_ausschuss" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>67,"y"=>70,"ram"=>'LRB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
 
// anerkannt Nacharbeit
"13"    
=> array ("content"=>"Nacharbeit:","width"=>25,"hight"=>5,"x"=>92,"y"=>55,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  

"14" 
=> array ("content"=>"Stück:","width"=>25,"hight"=>5,"x"=>92,"y"=>60,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"anerkannt_stk_nacharbeit" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>92,"y"=>60,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"15" 
=> array ("content"=>"Gewicht:","width"=>25,"hight"=>5,"x"=>92,"y"=>65,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"anerkannt_gew_nacharbeit" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>92,"y"=>65,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
    
"16" 
=> array ("content"=>"[CZK]:","width"=>25,"hight"=>5,"x"=>92,"y"=>70,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"anerkannt_wert_nacharbeit" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>92,"y"=>70,"ram"=>'LRB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
    
// Nicht anerkannt
"17"    
=> array ("content"=>"Nicht Anerkannt:","width"=>25,"hight"=>5,"x"=>117,"y"=>55,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  

"18" 
=> array ("content"=>"Stück:","width"=>25,"hight"=>5,"x"=>117,"y"=>60,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"anerkannt_stk_nein" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>117,"y"=>60,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"19" 
=> array ("content"=>"Gewicht:","width"=>25,"hight"=>5,"x"=>117,"y"=>65,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"anerkannt_gew_nein" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>117,"y"=>65,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"20" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>117,"y"=>70,"ram"=>'LRB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

 // Unklar
"171"    
=> array ("content"=>"Unklar:","width"=>25,"hight"=>5,"x"=>142,"y"=>55,"ram"=>'LRT',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),  

"181" 
=> array ("content"=>"Stück:","width"=>25,"hight"=>5,"x"=>142,"y"=>60,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"stk_unklar" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>142,"y"=>60,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"191" 
=> array ("content"=>"Gewicht:","width"=>25,"hight"=>5,"x"=>142,"y"=>65,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"gew_unklar" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>142,"y"=>65,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
   
"21" 
=> array ("content"=>"","width"=>25,"hight"=>5,"x"=>142,"y"=>70,"ram"=>'LRB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"22" 
=> array ("content"=>"Interne Bewertung:","width"=>35.5,"hight"=>5,"x"=>91,"y"=>30,"ram"=>'LTR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),
"interne_bewertung" 
=> array ("content"=>"","width"=>35.5,"hight"=>15,"x"=>91,"y"=>35,"ram"=>'LRB',"align"=>"C","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"23" 
=> array ("content"=>"Datum:","width"=>35.5,"hight"=>5,"x"=>131.5,"y"=>30,"ram"=>'LTR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),
"24" 
=> array ("content"=>"Erhalten am:","width"=>35.5,"hight"=>7.5,"x"=>131.5,"y"=>35,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"erhalten_am" 
=> array ("content"=>"","width"=>35.5,"hight"=>7.5,"x"=>131.5,"y"=>35,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"25" 
=> array ("content"=>"Erledigt am:","width"=>35.5,"hight"=>7.5,"x"=>131.5,"y"=>42.5,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"erledigt_am" 
=> array ("content"=>"","width"=>35.5,"hight"=>7.5,"x"=>131.5,"y"=>42.5,"ram"=>'LRB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"26" 
=> array ("content"=>"Strafen:","width"=>90,"hight"=>5,"x"=>175,"y"=>105,"ram"=>'LRTB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeSubheaders,"fontStyle"=>$fontStyleSubheaders),
"27" 
=> array ("content"=>"Pers.-Nr.:","width"=>15,"hight"=>5,"x"=>175,"y"=>110,"ram"=>'LRTB',"align"=>"C","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"1strafe_persnr" 
=> array ("content"=>"1strafe_persnr","width"=>15,"hight"=>5,"x"=>175,"y"=>115,"ram"=>'LRTB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"2strafe_persnr" 
=> array ("content"=>"2strafe_persnr","width"=>15,"hight"=>5,"x"=>175,"y"=>120,"ram"=>'LRTB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"3strafe_persnr" 
=> array ("content"=>"3strafe_persnr","width"=>15,"hight"=>5,"x"=>175,"y"=>125,"ram"=>'LRTB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"4strafe_persnr" 
=> array ("content"=>"4strafe_persnr","width"=>15,"hight"=>5,"x"=>175,"y"=>130,"ram"=>'LRTB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

"28" 
=> array ("content"=>"CZK:","width"=>15,"hight"=>5,"x"=>190,"y"=>110,"ram"=>'LRTB',"align"=>"C","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),  
"1strafe_wert" 
=> array ("content"=>"1strafe_wert","width"=>15,"hight"=>5,"x"=>190,"y"=>115,"ram"=>'LRTB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"2strafe_wert" 
=> array ("content"=>"2strafe_wert","width"=>15,"hight"=>5,"x"=>190,"y"=>120,"ram"=>'LRTB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"3strafe_wert" 
=> array ("content"=>"3strafe_wert","width"=>15,"hight"=>5,"x"=>190,"y"=>125,"ram"=>'LRTB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"4strafe_wert" 
=> array ("content"=>"4strafe_wert","width"=>15,"hight"=>5,"x"=>190,"y"=>130,"ram"=>'LRTB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
  
"29" 
=> array ("content"=>"Bemerkung:","width"=>60,"hight"=>5,"x"=>205,"y"=>110,"ram"=>'LRTB',"align"=>"C","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),  
"1strafe_bemerkung" 
=> array ("content"=>"1strafe_bemerkung","width"=>60,"hight"=>5,"x"=>205,"y"=>115,"ram"=>'LRTB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"2strafe_bemerkung" 
=> array ("content"=>"2strafe_bemerkung","width"=>60,"hight"=>5,"x"=>205,"y"=>120,"ram"=>'LRTB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"3strafe_bemerkung" 
=> array ("content"=>"3strafe_bemerkung","width"=>60,"hight"=>5,"x"=>205,"y"=>125,"ram"=>'LRTB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
"4strafe_bemerkung" 
=> array ("content"=>"4strafe_bemerkung","width"=>60,"hight"=>5,"x"=>205,"y"=>130,"ram"=>'LRTB',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
    
    
//"26" 
//=> array ("content"=>"Ein:","width"=>35,"hight"=>7.5,"x"=>130,"y"=>35,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
//"erhalten_am" 
//=> array ("content"=>"","width"=>35,"hight"=>7.5,"x"=>130,"y"=>35,"ram"=>'LR',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),

//"27" 
//=> array ("content"=>"Aus:","width"=>35,"hight"=>7.5,"x"=>130,"y"=>42.5,"ram"=>'LR',"align"=>"L","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent),
//"erledigt_am" 
//=> array ("content"=>"","width"=>35,"hight"=>7.5,"x"=>130,"y"=>42.5,"ram"=>'LRB',"align"=>"R","newLine"=>0,"fill"=>0,"fontSize"=>$fontSizeContent,"fontStyle"=>$fontStyleContent)

        );

function removeCarriageReturn($string, $first)
{
    // removing line breaks to represent two lines in one line $first is the number of lines in each line
    $Split = split("\r\n", $string);  
    $result = $Split[0];
    
    foreach ($Split as $lineNum=>$line)
    {
        if ($lineNum != 0)
        {
            if (($lineNum) % $first == 0)
            {
                $result .= "\r\n" . $line;
            }
            else
            {
                $result .= " " . $line;
            }
        }
    }
    
    return $result;
}

function howOftenContains($haystack, $needle, $offset = 0)
{
    $howOftenContains = 0;
    while(offset < strlen($haystack))
    {
        if(strpos($haystack, $needle, $offset))
        {
            $offset = strpos($haystack, $needle, $offset) + 1;
            $howOftenContains++;
        }
        else 
        {   
            return $howOftenContains;
        }
    }
    return $howOftenContains;
}

function getContent($Zelle, $nodelist, $cellName, $domxml2)
{
    if ($Zelle["content"] != "")
    {       
        // if there's a + put the content out of array and out of the xml togethter
        if (strpos($Zelle["content"], "+") && getValueForNode($nodelist, $cellName)!= "")
        {
            $add = explode("+", $Zelle["content"]);
            $content = $add[0] . getValueForNode($nodelist, $cellName) . $add[1];
        }    
        
        // if there's a * put the content in a array for writing an exact amount of lines
        else if (strpos($Zelle["content"], "*") == 1)
        {
            //$numOfLines = split("*", $Zelle["content"]);
            //$numOfLines = $numOfLines[1];
            $numOfLines = 3;
            
            $content = getValueForNode($nodelist, $cellName);
            //$content = removeCarriageReturn($content, 2);
            //$content = split("\r\n", $content);
            
            //while (count($content) < $numOfLines)
            //{
            //     $content[] = "";
            //}
            
            $missingLines = $numOfLines - howOftenContains($content, "\r\n") - 1;
            
            //echo howOftenContains("1212121212121212", "1");
            
            while ($missingLines > 0)
            {
                $content .= "\r\n";
                $missingLines--;
            }
            while (howOftenContains($content, "\r\n")+1 > $numOfLines)
            {
                $content = substr($content, 0, strrpos($content, "\r\n"));
            }
        }
        else if (strpos($Zelle["content"], "strafe_"))
        {
            $index = substr($Zelle["content"], 0, 1) - 1;
            $strafen = $domxml2->getElementsByTagName("strafe");
            
            foreach ($strafen as $strafe)
            {
                $values = $strafe->childNodes;
                $arr[] = array (getValueForNode($values, "persnr"),
                                getValueForNode($values, "vorschlag_betrag"),
                                getValueForNode($values, "betr"),
                                getValueForNode($values, "vorschlag_bemerkung"));
            }
            
            if (strpos($Zelle["content"], "strafe_persnr"))
                    $content = $arr[$index][0];
            else if (strpos($Zelle["content"], "strafe_wert"))
                    $content = $arr[$index][1];
            else if (strpos($Zelle["content"], "strafe_bemerkung"))
            {        
                if(strlen($arr[$index][3]) > 60)
                    $content = substr($arr[$index][3],0,60);
                else 
                    $content = $arr[$index][3];
            }
        }
        else if (!strpos($Zelle["content"], "+"))
            $content = $Zelle["content"];
    }
    else
    {    
        $content = getValueForNode($nodelist, $cellName);
        //$content = removeCarriageReturn($content, "2"); 
        
        
    }
    
    return $content;
}

/**
 *
 * @param TCPDF $pdf
 * @param type $left
 * @param type $top
 * @param type $breite, if = 0 the to the right edge of the page
 * @param type $zeilen 
 */
function printSchulungTable($pdf,$left,$top,$zeilen,$breite=0){
    // heading
    $pdf->SetXY($left, $top);
    $pdf->Cell(0, 5, "Schulung", '0', 1, 'L', 0);
    // calculate line height
    $wholeTableHeight = $pdf->getPageHeight()-PDF_MARGIN_BOTTOM-$top;
    $lineHeight = $wholeTableHeight / ($zeilen+1);
    $breiteAktual = $breite;
    if($breite==0){
	$wholeTableWidth = $pdf->getPageWidth()-PDF_MARGIN_RIGHT+5-$left;
	$breiteAktual=$wholeTableWidth;
    }
    
    for($line=0;$line<$zeilen;$line++){
	$pdf->SetX($left);
	$pdf->Cell($breiteAktual/3, $lineHeight, '', '1', 0, 'L', 0);
	$pdf->Cell(2*$breiteAktual/6, $lineHeight, '', '1', 0, 'L', 0);
	$pdf->Cell(2*$breiteAktual/6, $lineHeight, '', '1', 1, 'L', 0);
    }
}

/**
 *
 * @param TCPDF $pdf
 * @param type $left
 * @param type $top
 * @param type $childs 
 */
function printAFO($pdf,$left,$top,$childs){
    // header/label
    $pdf->SetXY($left, $top);
    $pdf->Cell(0, 5, 'Arbeitsfolgen (AFO)', 'B', 1, 'L', 0);
    $pdf->Ln(1);
    $dW = 25;
    $pdf->SetX($left);
    $yAFO = $pdf->GetY();
    $pdf->Cell($dW, 5, 'Mitteilung per', '0', 0, 'L', 0);
    $pdf->Cell(20, 5, 'Fax', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'mt_fax')!=0?'x':'';
    $pdf->Cell(5, 5, $o, '1', 1, 'C', 0);
    
    $pdf->SetX($left);
    $pdf->Cell($dW, 5, '', '0', 0, 'L', 0);
    $pdf->Cell(20, 5, 'Email', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'mt_email')!=0?'x':'';
    $pdf->Cell(5, 5, $o, '1', 1, 'C', 0);
    
    $pdf->SetX($left);
    $pdf->Cell($dW, 5, '', '0', 0, 'L', 0);
    $pdf->Cell(20, 5, 'Brief', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'mt_brief')!=0?'x':'';
    $pdf->Cell(5, 5, $o, '1', 1, 'C', 0);
    
    $pdf->SetX($left);
    $pdf->Cell($dW, 5, '', '0', 0, 'L', 0);
    $pdf->Cell(20, 5, 'Tel.', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'mt_telefon')!=0?'x':'';
    $pdf->Cell(5, 5, $o, '1', 1, 'C', 0);
    
    $pdf->SetX($left);
    $pdf->Cell($dW, 5, '', '0', 0, 'L', 0);
    $pdf->Cell(20, 5, 'Mündlich', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'mt_mund')!=0?'x':'';
    $pdf->Cell(5, 5, $o, '1', 1, 'C', 0);
    
    $pdf->Ln(1);
    
    $pdf->SetX($left);
    $pdf->Cell($dW+25, 5, 'Muster / Eingelagert. am:', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'muster_platz').' / '.getValueForNode($childs, 'muster_vom');
    $pdf->Cell(70-5, 5, $o, 'B', 1, 'L', 0);

    $pdf->SetX($left);
    $pdf->Cell($dW+25, 5, 'Negativmuster bei Aby/Kunde :', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'negativ_muster');
    $pdf->Cell(70-5, 5, $o, 'B', 1, 'L', 0);
    
    $pdf->SetX($left);
    $pdf->Cell($dW+25, 5, 'Charge o.a.Identifizierungshinweise :', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'identif_hinweise');
    $pdf->Cell(70-5, 5, $o, 'B', 1, 'L', 0);
    
    $pdf->SetXY($left+$dW+27,$yAFO);
    $pdf->Cell(35, 5, 'Bearbeiter beim Kunden:', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'bearbeiter_kunde');
    $pdf->Cell(20+8, 5, $o, 'B', 1, 'L', 0);
    
    $pdf->SetXY($left+$dW+27,$yAFO+5);
    $pdf->Cell(35, 5, 'Datum Mitteilung:', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'mt_datum');
    $pdf->Cell(20+8, 5, $o, 'B', 1, 'L', 0);
    
    $pdf->SetXY($left+$dW+27,$yAFO+10);
    $pdf->Cell(35, 5, 'Abydos Annahme:', '0', 0, 'L', 0);
    $o =  getValueForNode($childs, 'erstellt');
    $pdf->Cell(20+8, 5, $o, 'B', 1, 'L', 0);
}

/**
 *
 * @param TCPDF $pdf
 * @param type $left
 * @param type $top 
 */

function printInterneBewertung($pdf,$left,$top,$childs){
    $iBew = getValueForNode($childs, 'interne_bewertung');
    // label
    $pdf->SetXY($left, $top);
    $pdf->Cell(25, 5, "Interne Bewertung", '0', 1, 'L', 0);
    $pdf->SetX($left);
    $pdf->Cell(25, 5, "1 - 10", '0', 0, 'C', 0);
    $pdf->Cell(20, 5, $iBew, '1', 0, 'C', 0);
}

/**
 *
 * @param type $pdfobjekt
 * @param type $nodelist
 * @param type $rgb
 * @param type $cells 
 */
function insertFields($pdfobjekt, $nodelist, $rgb, $cells,$domxml2)
{
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    
    
    $content = getValueForNode($nodelist, "erstellt");
    
    // MultiCell(float w , float h , string txt [, mixed border] [, string align] [, integer fill])
    
    foreach ($cells as $cellName=>$Zelle)
    {
        $pdfobjekt->SetFont("FreeSans",$Zelle["fontStyle"],$Zelle["fontSize"]);
        
        $content = getContent($Zelle, $nodelist, $cellName, $domxml2);
        
        $x = $pdfobjekt->GetX();
        $y = $pdfobjekt->GetY();
        
        if (is_array($content) && strpos($Zelle["content"], "*") == 1)
        {
            foreach ($content as $lineNum=>$cont)
            {
                $ram = $Zelle["ram"];
                //echo $cont . "<br>";
                //echo $lineNum . "<br>";
                //echo "<br>";
                if ($lineNum == (count($content)-1))
                {
                    $ram = "LBR";
                }
                else 
                {
                    $ram = "LR";
                }
                
                // SetX doesn't set the value...? but SetXY works fine
                $pdfobjekt->SetXY($Zelle["x"], $Zelle["y"] + $lineNum * $Zelle["hight"]);
                $pdfobjekt->MultiCell($Zelle["width"], $Zelle["hight"], $cont, $ram, $Zelle["align"], $Zelle["fill"]);
            }
        }
        else
        {
            $pdfobjekt->SetXY($Zelle["x"], $Zelle["y"]);
            $pdfobjekt->MultiCell($Zelle["width"], $Zelle["hight"], $content, $Zelle["ram"], $Zelle["align"], $Zelle["fill"]);  
        }
    }    
}

function teloMultiZeilen($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist, $fontSize)
{
    $pdfobjekt->SetFontSize($fontSize);
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    
    $x = $pdfobjekt->GetX();
    $y = $pdfobjekt->GetY();
    
    foreach ($pole as $key=>$cell)
    {
        $zeilen = $cell["zeilen"];
        $pdfobjekt->SetY($y);
        
        // echo $key; // Rekl-Nr Import
        
        foreach($zeilen as $zeilenr=>$Zelle)
        {
            $lineNum = $zeilenr;
            $pdfobjekt->SetX($x);
            $nodeName = $Zelle["node"];
            $obsah = getValueForNode($nodelist, $nodeName); // contains content of xml-element
            
            // preparing the content to fit the cell in pdf
            //if (array_key_exists("substr", $Zelle))
            //{                   
            //    $obsah = SplittingObsah($pdfobjekt, $obsah, $cell, $lineNum, $Zelle);
            //}
            
            if (array_key_exists("nf", $Zelle))
                $obsah = number_format($obsah, $Zelle["nf"][0], $Zelle["nf"][1], $Zelle["nf"][2]);
                        
            $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$Zelle["ram"],1,$Zelle["align"],$Zelle["fill"]);
        }
        $x += $cell["sirka"];
    }
}

// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist, $fontSize)
{
	$pdfobjekt->SetFontSize($fontSize);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $nodename=>$cell)
	{
                // conversion of number_format innessesary? Provoces error: return value from getValueForNode doesnt fit double as parameter
		//if(array_key_exists("nf",$cell))
		//{
                //        if (getValueForNode($nodelist,$nodename) !== double)
                //        {
                //            echo getValueForNode($nodelist,$nodename) . "\n";
                //        }
		//	$cellobsah = 
		//	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		//}
		//else
		//{
			$cellobsah=getValueForNode($nodelist,$nodename);
		//}
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
}

// funkce ktera vrati hodnotu podle nodename
// predam ji nodelist a jmeno node ktereho hodnotu hledam
// zaokrouhlovani jde nahoru

function kopf_kunde($pdfobjekt,$vyskaradku,$rgb,$childnodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 7);

	$kundeNr = getValueForNode($childnodes,'kundenr');
        $kundeNa = getValueForNode($childnodes,'kunde_name');
	$pdfobjekt->Cell(0,$vyskaradku,"Kunde: ".$kundeNr." - ".$kundeNa,'LRBT',1,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 7);

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function fuss_kunde($pdfobjekt,$vyskaradku,$rgb,$childnodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	// dummy
	$obsah="";
	$pdfobjekt->Cell(0,$vyskaradku,"",'LRTB',1,'L',$fill);
}

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
	}
}
				
function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		return 1;
	}
	else
		return 0;
}


require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S362 - Mängelrüge", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT-5);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 7));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setFont("FreeSans", '', 6);

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();


$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde)
{
    $kundeChildNodes = $kunde->childNodes;
    $reklamationen = $kunde->getElementsByTagName("reklamation");
    $kundenr = getValueForNode($kundeChildNodes, "kundenr");
    
    foreach($reklamationen as $reklamation)
    {
        // new Page for each reklamation
        $pdf->AddPage();
        //pageheader($pdf, $cells_header, 5, "", $fontSizeLegend);
    
        test_pageoverflow($pdf,5,Array($cells_header1, $cells_header2));       
    
	$reklChilds = $reklamation->childNodes;
        
        insertFields($pdf,$reklChilds,array(255,255,255), $cells,$domxml2);
        
	//teloMultiZeilen($pdf,$cellsMultiZeilen,3,array(255,255,255),"",$reklChilds,$fontSizeBody);
	printSchulungTable($pdf, 180, 150, 7);
//	printInterneBewertung($pdf, 110, 150, $reklChilds);
	printAFO($pdf, 170, 30, $reklChilds);
    }
//    fuss_kunde($pdf, 5, array(200,255,200), $kundeChildNodes);
}
	

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
