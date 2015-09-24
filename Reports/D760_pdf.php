<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "D760";
$doc_subject = "D760 Report";
$doc_keywords = "D760";

// necham si vygenerovat XML

$parameters=$_GET;

$export=$_GET['auftragsnr'];

require_once('D760_xml.php');


// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");


foreach ($parameters as $param)
{
	$parametry=$param->childNodes;
	// v ramci parametru si prectu label a hodnotu
	foreach($parametry as $parametr)
	{
		$parametr=$parametr->childNodes;
		foreach($parametr as $par)
		{
			if($par->nodeName=="label")
				$label=$par->nodeValue;
			if($par->nodeName=="value")
				$value=$par->nodeValue;
		}
		$params .= $label.": ".$value."  ";
	}
}


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2


$cells = 
array(

"teilnr" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"teilbez" 
=> array ("popis"=>"","sirka"=>40,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"text1" 
=> array ("popis"=>"","sirka"=>40,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"palnr"
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"preis" 
=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"betrag" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"waehrung" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);


$cells_header = 
array(

"teil" 
=> array ("popis"=>"díl","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"bezeichnung" 
=> array ("popis"=>"označení","sirka"=>40,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"dienstleistung" 
=> array ("popis"=>"operace","sirka"=>40,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"palnr"
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"auss" 
=> array ("popis"=>"zmetky","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"gutstk" 
=> array ("popis"=>"kusy","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"preis" 
=> array ("popis"=>"cena/kus","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
	
"betrag" 
=> array ("popis"=>"cena celkem","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"dummy" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>1),

);


$sum_zapati_rechnung_array = array(	
								"betrag"=>0,
								);
global $sum_zapati_rechnung_array;


/////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce k vynulovani pole se sumama
// jako parametr predam asociativni pole
function nuluj_sumy_pole(&$pole)
{
	foreach($pole as $key=>$prvek)
	{
		$pole[$key]=0;
	}
}
////////////////////////////////////////////////////////////////////////////////////////////////////

 

// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$cells,$childnodes)
{
        global $parametersPDF;
        global $teilen;
        global $auftragsnrTeilen;
        global $dt;

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);

	$pdfobjekt->SetY(15);
	$pdfobjekt->Cell(60,5,"Abydos s.r.o., CZ - 35132 Hazlov 247",'0',0,'L',0);
	$pdfobjekt->Cell(0,5,"Strana ".$pdfobjekt->PageNo()." z {nb}",'0',1,'R',0);

	$pdfobjekt->SetY(35);
	// schovam si pozice xy
	$xOld=$pdfobjekt->GetX();
	$yOld=$pdfobjekt->GetY();

        // v zahlavi faktury
        // pro datumy nastavim dlouhy ( 10 cm ) levy okraj
        // 
	// adresa zakaznika
	$pdfobjekt->SetFont("FreeSans", "", 9);
	$pdfobjekt->Cell(100,5,getValueForNode($childnodes,"name1"),'0',1,'L',0);
	$name2 = trim(getValueForNode($childnodes,"name2"));
	if(strlen($name2)>0){
	    $pdfobjekt->Cell(100,5,$name2,'0',1,'L',0);
	}
	
	$pdfobjekt->Cell(100,5,getValueForNode($childnodes,"strasse"),'0',1,'L',0);
	//$pdfobjekt->Ln();
	$pdfobjekt->Cell(100,5,getValueForNode($childnodes,"land")."-".getValueForNode($childnodes,"plz")." ".getValueForNode($childnodes,"ort"),'0',1,'L',0);
	$pdfobjekt->Ln();
        $pdfobjekt->Cell(100,5,'IC: '.getValueForNode($childnodes,"ico")." DIC: ".getValueForNode($childnodes,"dic"),'0',1,'L',0);
	
	//datum faktury a lieferdatum
	$pdfobjekt->SetLeftMargin(100);
	
	//$pdfobjekt->SetX($xOld+100);
	$pdfobjekt->SetY($yOld);
	$pdfobjekt->Cell(0,5,"Datum vystavení:  ".getValueForNode($childnodes,"fertig"),'0',1,'R',0);
	//$pdfobjekt->SetX($xOld+100);
	$pdfobjekt->Cell(0,5,"Datum zdanitelného plnění :  ".getValueForNode($childnodes,"ausliefer_datum"),'0',1,'R',0);
	$pdfobjekt->Ln();
	
	
	
	$pdfobjekt->SetLeftMargin(PDF_MARGIN_LEFT);
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->SetLineWidth(0.5);
	$pdfobjekt->Cell(50,7,"D760",'TB',0,'L',0);
        if($teilen!=0){
            if($dt=='ma')
                $pdfobjekt->Cell(0,7,"Faktura číslo :  ".$auftragsnrTeilen,'TB',1,'R',0);
            else
                $pdfobjekt->Cell(0,7,"Faktura číslo :  ".getValueForNode($childnodes,"auftragsnr"),'TB',1,'R',0);
        }
        else
            $pdfobjekt->Cell(0,7,"Faktura číslo :  ".getValueForNode($childnodes,"auftragsnr"),'TB',1,'R',0);
	
	// toto zobrazim jen na prvni strance
	if($pdfobjekt->PageNo()==1)
	{
		$pdfobjekt->SetFont("FreeSans", "B", 8);
		$pdfobjekt->Cell(0,7,"Na základě Vaší objednávky ".getValueForNode($childnodes,"bestellnr")." Vám fakturujeme :",'0',1,'L',0);
	}
	else
	{
		$pdfobjekt->SetFont("FreeSans", "B", 8);
		$pdfobjekt->Cell(0,3,"",'0',1,'L',0);
	}
	
	$pdfobjekt->SetLineWidth(0.2);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	foreach($cells as $cell)
	{
		$pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$cell['fill']);
	}
	
}


// funkce pro vykresleni tela
function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $nodename=>$cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename);
		}
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 7);
}


// funkce ktera vrati hodnotu podle nodename
// predam ji nodelist a jmeno node ktereho hodnotu hledam
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
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_fremdpos($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(0,$vyskaradku,"",'T',1,'L',1);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_palette($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(0,$vyskaradku,"",'T',1,'L',1);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_rechnung($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sumarray)
{

	$dphP = 21;
        $waehrung = trim(getValueForNode($childNodes,"waehrung"));

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
        $pdfobjekt->Cell(0,2,"",'T',1,'R',1);
	$pdfobjekt->Cell(20+45+45+12+12,$vyskaradku,"",'0',0,'R',1);
	$pdfobjekt->Cell(20,$vyskaradku,"Cena celkem:",'0',0,'R',1);
        $betragN = round($sumarray['betrag'],2);
	$obsah=number_format($sumarray['betrag'],2,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'',0,'R',1);
	$pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childNodes,"waehrung"),'',1,'R',1);

        $apl = AplDB::getInstance();
        $kurs = $apl->getKurs(date('Y-m-d'), 'EUR', 'CZK');
        $betragNCZK = $betragN * $kurs;
	$multi = 1+($dphP/100);
        $celkemkuhrade = floor(($sumarray['betrag']*$multi)*10+0.9)/10;
        $kuhrade1 = $sumarray['betrag']*$multi;
        $dphN = $kuhrade1 - $sumarray['betrag'];
        $halvyrovnani = $celkemkuhrade - $kuhrade1;

//        $dphN = round(0.19*$betragN,2);
        $dph = number_format(($dphP/100)*$sumarray['betrag'],2,',',' ');
        $dphCZ = $kurs * ($dphP/100)*$sumarray['betrag'];

      	$pdfobjekt->Cell(20+45+45+12+12,$vyskaradku,"",'0',0,'R',1);
	$pdfobjekt->Cell(20,$vyskaradku,"DPH $dphP%:",'',0,'R',1);
	$pdfobjekt->Cell(20,$vyskaradku,$dph,'',0,'R',1);
	$pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childNodes,"waehrung"),'',1,'R',1);

        $obsah = number_format($kuhrade1,2,',',' ');
      	$pdfobjekt->Cell(20+45+45+12+12,$vyskaradku,"",'0',0,'R',1);
        $pdfobjekt->SetFont("FreeSans", "B", 9);
	$pdfobjekt->Cell(20,$vyskaradku,"Celkem k úhradě:",'T',0,'R',1);
	$pdfobjekt->Cell(20,$vyskaradku,$obsah.' ','T',0,'R',1);
	$pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childNodes,"waehrung"),'T',1,'R',1);

        if($waehrung!='CZK'){
            $pdfobjekt->Ln();
            $obsah = number_format($betragNCZK,2,',',' ');
            $pdfobjekt->Cell(20+45+45+12+12,$vyskaradku,"",'0',0,'R',1);
            $pdfobjekt->SetFont("FreeSans", "", 8);
            $pdfobjekt->Cell(20,$vyskaradku,"Cena celkem:",'T',0,'R',1);
            $pdfobjekt->Cell(20,$vyskaradku,$obsah.' ','T',0,'R',1);
            $pdfobjekt->Cell(0,$vyskaradku,'CZK','T',1,'R',1);

            $obsah = number_format($dphCZ,2,',',' ');
            $pdfobjekt->Cell(20+45+45+12+12,$vyskaradku,"",'0',0,'R',1);
            $pdfobjekt->SetFont("FreeSans", "", 8);
            $pdfobjekt->Cell(20,$vyskaradku,"DPH 20%:",'0',0,'R',1);
            $pdfobjekt->Cell(20,$vyskaradku,$obsah.' ','0',0,'R',1);
            $pdfobjekt->Cell(0,$vyskaradku,'CZK','0',1,'R',1);
        }
//        $obsah = number_format($halvyrovnani,2,',',' ');
//      	$pdfobjekt->Cell(20+45+45+12+12,$vyskaradku,"",'0',0,'R',1);
//	$pdfobjekt->Cell(20,$vyskaradku,"Haléřové vyrovnání:",'B',0,'R',1);
//	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',1);
//	$pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childNodes,"waehrung"),'B',1,'R',1);
//
//        $obsah = number_format($celkemkuhrade,2,',',' ');
//        $pdfobjekt->SetFont("FreeSans", "B", 9);
//      	$pdfobjekt->Cell(20+45+45+12+12,$vyskaradku,"",'0',0,'R',1);
//	$pdfobjekt->Cell(20,$vyskaradku,"Celkem k úhradě:",'',0,'R',1);
//	$pdfobjekt->Cell(20,$vyskaradku,$obsah.' ','',0,'R',1);
//	$pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childNodes,"waehrung"),'',1,'R',1);


}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
        global $teilen;
        global $auftragsnrTeilen;
        global $dt;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Ln();
        $pdfobjekt->Ln();
	$pdfobjekt->Cell(75,$vyskaradku,"Částku prosím převeďte do  ".getValueForNode($childNodes,"zahlenbis")." na konto číslo : ",'0',0,'L',0);
        $pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childNodes,"textkonto"),'0',1,'L',0);
        //$pdfobjekt->Cell(0,$vyskaradku,"na konto číslo ".getValueForNode($childNodes,"textkonto"),'0',1,'1',0);
//	$pdfobjekt->Cell(0,$vyskaradku,"".getValueForNode($childNodes,"textkonto")."",'0',1,'1',0);
	$zweck=getValueForNode($childNodes,"textverwzweck");
	if(strlen($zweck)>0){
            $zweckAuftrag=getValueForNode($childNodes,"auftragsnr");
            if($teilen!=0 && $dt=='ma') $zweckAuftrag=$auftragsnrTeilen;
		$pdfobjekt->Cell(75,$vyskaradku,"Jako účel platby zadejte prosím: ",'0',0,'L',0);
                $pdfobjekt->Cell(0,$vyskaradku,trim($zweck." ".$zweckAuftrag),'0',1,'L',0);
        }
		
	$dic=getValueForNode($childNodes,"dic");
	if((strlen($dic)>0)&&(substr($dic,0,2)!="CZ"))
	{
		$pdfobjekt->Cell(0,$vyskaradku,"Es handelt sich um eine steuerfreie innergemeinschaftliche Lieferung nach par.10 Abs. 5 UStG (Gesetzessammlung 235 / 2004).",'0',1,'1',0);
		$pdfobjekt->Cell(0,$vyskaradku,"Die USt.Id.Nr. des Rechnungsempfängers ist :".$dic,'0',1,'1',0);
	}
	$pdfobjekt->Ln();
        $pdfobjekt->Cell(0,$vyskaradku,"Jsme evidováni u Krajského soudu v Plzni, oddíl C, vložka 8536.",'0',1,'1',0);
        $pdfobjekt->Ln();
	$pdfobjekt->Cell(0,$vyskaradku,"S pozdravem",'0',1,'L',0);
	$pdfobjekt->Cell(0,$vyskaradku,"Abydos s.r.o.",'0',1,'L',0);
	//$obsah=number_format($sumarray['betrag'],2,',',' ');
	//$pdfobjekt->Cell(15,$vyskaradku,$obsah,'TB',0,'R',1);
	
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_fremdposition($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(20,$vyskaradku,"Best.Nr.:",'0',0,'L',1);
	$fremdauftr = getValueForNode($childNodes,"fremdauftrnr");
	$pdfobjekt->Cell(40,$vyskaradku,$fremdauftr,'0',0,'L',1);
	$fremdpos = getValueForNode($childNodes,"fremdposnr");
	$pdfobjekt->Cell(40,$vyskaradku,"Pos.:".$fremdpos,'0',1,'L',1);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_import($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	//misto pro teil
	$pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childNodes,""),'0',0,'L',$fill);
	$pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childNodes,"im"),'0',0,'L',$fill);
	$pdfobjekt->Cell(25,$vyskaradku,"Best.Nr.:".getValueForNode($childNodes,"fremdauftr"),'0',0,'L',$fill);
	$pdfobjekt->Cell(0,$vyskaradku,"Pos.:".getValueForNode($childNodes,"fremdpos"),'0',1,'L',$fill);


	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childNodes,"teilnr"),'0',0,'L',$fill);
	$pdfobjekt->Cell(50,$vyskaradku,getValueForNode($childNodes,"teillang"),'0',0,'L',$fill);

	$obsah=number_format(getValueForNode($childNodes,"gew"),3,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah."kg/Stk",'0',1,'R',$fill);

	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	}
}
				

function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		//pageheader($pdfobjekt,$cellhead,$vysradku);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
		return 1;
	}
	else
		return 0;
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", "");
$pdf->setRechnungFoot(true);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER+3);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);



// prvni stranka
$pdf->AddPage();
//pageheader($pdf,$cells_header,5);

$z1 = "Abydos s.r.o.                  Hazlov 247                 Tel:+420 354 595 337               DIC / Ust/Id/Nr:CZ25206958";
$z2 = "                                       35132 Hazlov             Fax:+420 354 596 993                                         ";
$z3 = "";

$pdf->setRechnungZeilen($z1, $z2, $z3);

// zacinam po rechnungach

$rechnunge=$domxml->getElementsByTagName("rechnung");
foreach($rechnunge as $rechnung)
{
	$rechnungChildNodes = $rechnung->childNodes;

	//test_pageoverflow($pdf,5,$cells_header);
	//zahlavi_rechnung($pdf,5,array(255,255,255),$exportChildNodes);
	pageheader($pdf,$cells_header,$rechnungChildNodes);

	// ted jdu po fremdauftr
	$fremdauftraege=$rechnung->getElementsByTagName("fremdauftr");
	foreach($fremdauftraege as $fremdauftrag)
	{
		$fremdauftragChildNodes = $fremdauftrag->childNodes;
		//test_pageoverflow($pdf,5,$cells_header);
		//zahlavi_fremdauftrag($pdf,5,array(255,255,255),$teilChildNodes);

		// ted pujdu po fremdpositionech
		$fremdpositionen = $fremdauftrag->getElementsByTagName("fremdpos");
		foreach($fremdpositionen as $fremdposition)
		{
			$fremdpositionChildNodes = $fremdposition->childNodes;
			//test_pageoverflow($pdf,5,$cells_header);
			//if(test_pageoverflow_noheader($pdf,5))
			//			pageheader($pdf,$cells_header,$rechnungChildNodes);
			//zahlavi_fremdposition($pdf,5,array(255,255,255),$fremdpositionChildNodes);
			//nuluj_sumy_pole($sum_zapati_import_array);

			// ted jdu po dilech
			$teile = $fremdposition->getElementsByTagName("teil");
			foreach($teile as $teil)
			{
				$teilChildNodes = $teil->childNodes;

//				$paletten = $teil->getElementsByTagName("palette");
//				foreach($paletten as $palette)
//				{
//					$paletteChildNodes=$palette->childNodes;
				$taetigkeiten = $teil->getElementsByTagName("taetigkeit");
				
        			foreach($taetigkeiten as $taetigkeit)
				{
					$taetigkeitChildNodes = $taetigkeit->childNodes;
					if(test_pageoverflow_noheader($pdf,5))
						pageheader($pdf,$cells_header,$rechnungChildNodes);
					
					detaily($pdf,$cells,5,array(255,255,255),$taetigkeitChildNodes);
					//	aktualizuju sumy pro zapati faktury
					foreach($sum_zapati_rechnung_array as $key=>$prvek)
					{
						$hodnota=getValueForNode($taetigkeitChildNodes,$key);
						$sum_zapati_rechnung_array[$key]+=$hodnota;
					}
				}
//					// zapati palette
//					if(test_pageoverflow_noheader($pdf,1))
//						pageheader($pdf,$cells_header,$rechnungChildNodes);
//					zapati_palette($pdf,1,array(255,255,255),$paletteChildNodes);
//				}
			}
			// zapati position
			//if(test_pageoverflow_noheader($pdf,1))
			//			pageheader($pdf,$cells_header,$rechnungChildNodes);
			//zapati_fremdpos($pdf,1,array(255,255,255),$fremdpositionChildNodes);
		}
	}
	// zapati rechnung
	if(test_pageoverflow_noheader($pdf,6))
			pageheader($pdf,$cells_header,$rechnungChildNodes);
	zapati_rechnung($pdf,6,array(255,255,255),$rechnungChildNodes,$sum_zapati_rechnung_array);
}


zapati_sestava($pdf,6,array(255,255,255),$rechnungChildNodes);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
