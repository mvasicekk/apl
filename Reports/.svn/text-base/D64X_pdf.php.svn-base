<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D64X";
$doc_subject = "D64X Report";
$doc_keywords = "D64X";

// necham si vygenerovat XML

$parameters=$_GET;

$aplDB = AplDB::getInstance();

$export=$_GET['export'];
$termin = make_DB_datum($aplDB->validateDatum($_GET['termin']));
$popisek = trim($_GET['popisek']);
$watermark = trim($_GET['watermark']);
$format = trim($_GET['format']);
$teil = trim($_GET['teil']);

$teil = str_replace('*', '%', $teil);

if(strlen(trim($export))>0){
    require_once('D64X_xml.php');
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
// zobraz_paletu($pdf,$paletteChildNodes,$importChildNodes);
function zobraz_paletu($pdfobjekt,$paletteChildNodes,$importChildNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=0;

	// hlavni tabulka ma 3 radky

	$x_pocatek=$pdfobjekt->GetX();
	$y_pocatek=$pdfobjekt->GetY();
	$y_offset = 10;
	$auftragsnr_vyska = 110;
	$paletaPoziceX = 150;
	$datumCas = date('d.m.Y H:i:s');
	$ident = get_user();
	
	$datumCas = $datumCas. " ( $ident )";
	
	$pdfobjekt->SetFont("Arial", "", 20);
	$pdfobjekt->Text(10,15+$y_offset,"Auftrag / zakazka");
	$pdfobjekt->Text(100,15+$y_offset,"Stck / kusu :");
	$pdfobjekt->SetFont("Arial", "B", 20);
	$pdfobjekt->Text(150,15+$y_offset,getValueForNode($paletteChildNodes,"stk"));
	
	$pdfobjekt->SetFont("Arial", "B", $auftragsnr_vyska);
	$pdfobjekt->Text(10,50+$y_offset,getValueForNode($paletteChildNodes,"auftragsnr")."/");
	
	$pdfobjekt->SetFont("Arial", "", 70);
	$pdfobjekt->Text($paletaPoziceX,50+$y_offset,getValueForNode($paletteChildNodes,"pal"));
	$pdfobjekt->SetFont("Arial", "", 15);
	$pdfobjekt->Text($paletaPoziceX+5,28+$y_offset,"Palette / paleta");
	
	$pdfobjekt->SetFont("Arial", "", 20);
	//$pdfobjekt->SetXY(10,10);
	$pdfobjekt->Text(10,70+$y_offset,"Teil / dil");
	$pdfobjekt->SetFont("Arial", "B", 90);
	$pdfobjekt->Text(10,100+$y_offset,getValueForNode($paletteChildNodes,"teil"));
	
	
	$pdfobjekt->SetFont("Arial", "", 10);
	$pdfobjekt->Text(10,130+$y_offset,"Abydos s.r.o."."   $datumCas");
	
	$pdfobjekt->SetFont("Arial", "", 20);
	$pdfobjekt->Text(120,130+$y_offset,"Gew / vaha");
	$pdfobjekt->Text(160,130+$y_offset,getValueForNode($paletteChildNodes,"gew")." Kg");
	
	$pdfobjekt->StartTransform();
	//point reflection at the lower left point of rectangle
	$pdfobjekt->MirrorP(105,150);
	
	$pdfobjekt->SetFont("Arial", "", 20);
	$pdfobjekt->Text(10,15+$y_offset,"Auftrag / zakazka");
	$pdfobjekt->Text(100,15+$y_offset,"Stck / kusu :");
	$pdfobjekt->SetFont("Arial", "B", 20);
	$pdfobjekt->Text(150,15+$y_offset,getValueForNode($paletteChildNodes,"stk"));
	
	$pdfobjekt->SetFont("Arial", "B", $auftragsnr_vyska);
	$pdfobjekt->Text(10,50+$y_offset,getValueForNode($paletteChildNodes,"auftragsnr")."/");
	
	$pdfobjekt->SetFont("Arial", "", 70);
	$pdfobjekt->Text($paletaPoziceX,50+$y_offset,getValueForNode($paletteChildNodes,"pal"));
	$pdfobjekt->SetFont("Arial", "", 15);
	$pdfobjekt->Text($paletaPoziceX+5,28+$y_offset,"Palette / paleta");
	
	$pdfobjekt->SetFont("Arial", "", 20);
	//$pdfobjekt->SetXY(10,10);
	$pdfobjekt->Text(10,70+$y_offset,"Teil / dil");
	$pdfobjekt->SetFont("Arial", "B", 90);
	$pdfobjekt->Text(10,100+$y_offset,getValueForNode($paletteChildNodes,"teil"));
	
	$pdfobjekt->SetFont("Arial", "", 10);
	$pdfobjekt->Text(10,130+$y_offset,"Abydos s.r.o."."   $datumCas");
	
	$pdfobjekt->SetFont("Arial", "", 20);
	$pdfobjekt->Text(120,130+$y_offset,"Gew / vaha");
	$pdfobjekt->Text(160,130+$y_offset,getValueForNode($paletteChildNodes,"gew")." Kg");
	
	//	Stop Transformation
	$pdfobjekt->StopTransform();
	
	
	/*
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"kunde"));$pdfobjekt->Ln();
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"name1"));$pdfobjekt->Ln();
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"name2"));$pdfobjekt->Ln();
*/
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


/**
 *
 * @param PDF_Transform $pdf
 * @param int $row
 * @param int $column
 * @param string $popisek
 */
function drawBehaelterBox($pdf,$row,$column,$popisek,$watermark){

    $sirkaBoxu = 95;
    $vyskaBoxu = 88;

    $sirkaLoga = 40;
    $vyskaLoga = 10;

    $leftMarginText = 5;
    $rozestupBoxuX = 6;
    $rozestupBoxuY = 9;
    
    $pocX = $column * $sirkaBoxu + ($column+1) * $rozestupBoxuX;
    $pocY = $row * $vyskaBoxu + ($row +1 ) * $rozestupBoxuY;

    //0. vodostisk
    $stredX = $sirkaBoxu/2 + $pocX;
    $stredY = $vyskaBoxu/2 + $pocY;
    $vyskaTextu = 20;
    

    
    if($watermark=="ja"){
        $pdf->SetFont("Arial", "B", $vyskaTextu);
        $sirkaTextu = $pdf->GetStringWidth($popisek);

        $pdf->SetTextColor(230,230,230);
        $pdf->StartTransform();

        $pdf->Rotate(45,$stredX,$stredY);
        $pdf->Scale(200, 200, $stredX, $stredY);
        $pdf->Text($stredX-$sirkaTextu/2,$stredY,$popisek);

        $pdf->StopTransform();
    }


    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont("Arial", "B", 20);

    
    //1. nakreslim ramecek
    $pdf->Rect($pocX, $pocY, $sirkaBoxu, $vyskaBoxu);

    //2.logo abydos
    $pdf->Image('../img/abydos_logo.png', $pocX+2, $pocY+2,$sirkaLoga,$vyskaLoga);

    //3. popisek stitku
    $pdf->SetFont("Arial", "B", 20);
	$pdf->Text($pocX+2+$sirkaLoga+6,$pocY+2+$vyskaLoga-2,$popisek);

    //4. podtrhnout hlavicku
    $pdf->Line($pocX, $pocY+2+$vyskaLoga+2, $pocX+$sirkaBoxu, $pocY+2+$vyskaLoga+2);

    //5. auftrag/paleta
    $pdf->SetFont("Arial", "BU", 8);
	$pdf->Text($pocX+$leftMarginText,$pocY+2+$vyskaLoga+2+10,"Auftrag / zakazka");
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$pocY+2+$vyskaLoga+2+10,"Behalter / prepravni box");

    $posY = $pocY+2+$vyskaLoga+2+10;
    $posY += 13;
    $pdf->SetFont("Arial", "", 8);
    $pdf->Text($pocX+$leftMarginText,$posY,"............................");
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,".......................................");

    $pdf->Text($pocX+$leftMarginText+28,$posY-3,"...... kg / Stk");
    $posY += 10;
    $pdf->SetFont("Arial", "BU", 8);
    $pdf->Text($pocX+$leftMarginText,$posY,"Teil / cislo dilu");
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,"Stk / pocet kusu");

    $posY +=11;

    $pdf->SetFont("Arial", "", 8);
    $pdf->Text($pocX+$leftMarginText,$posY,"..........................");
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,"........................");

    $posY += 7;
    $pdf->SetFont("Arial", "B", 7);
    $pdf->Text($pocX+$leftMarginText,$posY,"Bei Reklamationen bitte Auftrags-Nr./ Behaelter / Teil angeben !");

    $posY += 9;
    $pdf->SetFont("Arial", "", 8);
    $pdf->Text($pocX+$leftMarginText,$posY,"Datum :");
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,"Pruefer / kontrolor");

    $posY += 9;
    $pdf->SetFont("Arial", "", 8);
    $pdf->Text($pocX+$leftMarginText,$posY,"..........................");
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,"..........................");

}

/**
 *
 * @param PDF_Transform $pdf
 * @param int $row
 * @param int $column
 * @param string $popisek
 */
function drawBehaelterBoxChilds($pdf,$row,$column,$popisek,$watermark,$childs){

    $sirkaBoxu = 95;
    $vyskaBoxu = 88;

    $sirkaLoga = 40;
    $vyskaLoga = 10;

    $leftMarginText = 5;
    $rozestupBoxuX = 6;
    $rozestupBoxuY = 9;

    $pocX = $column * $sirkaBoxu + ($column+1) * $rozestupBoxuX;
    $pocY = $row * $vyskaBoxu + ($row +1 ) * $rozestupBoxuY;

    //0. vodostisk
    $stredX = $sirkaBoxu/2 + $pocX;
    $stredY = $vyskaBoxu/2 + $pocY;
    $vyskaTextu = 20;



    if($watermark=="ja"){
        $pdf->SetFont("Arial", "B", $vyskaTextu);
        $sirkaTextu = $pdf->GetStringWidth($popisek);

        $pdf->SetTextColor(230,230,230);
        $pdf->StartTransform();

        $pdf->Rotate(45,$stredX,$stredY);
        $pdf->Scale(200, 200, $stredX, $stredY);
        $pdf->Text($stredX-$sirkaTextu/2,$stredY,$popisek);

        $pdf->StopTransform();
    }


    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont("Arial", "B", 20);


    //1. nakreslim ramecek
    $pdf->Rect($pocX, $pocY, $sirkaBoxu, $vyskaBoxu);

    //2.logo abydos
    $pdf->Image('../img/abydos_logo.png', $pocX+2, $pocY+2,$sirkaLoga,$vyskaLoga);

    //3. popisek stitku
    $pdf->SetFont("Arial", "B", 20);
	$pdf->Text($pocX+2+$sirkaLoga+6,$pocY+2+$vyskaLoga-2,$popisek);
    $pdf->SetFont("Arial", "B", 10);
    $pdf->Text($pocX+$leftMarginText,$pocY+2+$vyskaLoga-2+$vyskaLoga-2,"EX: ".getValueForNode($childs,"exportnr"));

    //4. podtrhnout hlavicku
    $pdf->Line($pocX, $pocY+2+$vyskaLoga+2, $pocX+$sirkaBoxu, $pocY+2+$vyskaLoga+2);

    //5. auftrag/paleta
    $pdf->SetFont("Arial", "BU", 8);
	$pdf->Text($pocX+$leftMarginText,$pocY+2+$vyskaLoga+2+10,"Auftrag / zakazka");
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$pocY+2+$vyskaLoga+2+10,"Behalter / prepravni box");

    $posY = $pocY+2+$vyskaLoga+2+10;
    $posY += 13;
    $pdf->SetFont("Arial", "B", 20);
    $pdf->Text($pocX+$leftMarginText,$posY,getValueForNode($childs,"auftragsnr"));
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,getValueForNode($childs,"palnr"));

    $pdf->SetFont("Arial", "B", 8);
    $pdf->Text($pocX+$leftMarginText+28,$posY-3,getValueForNode($childs,"gew")."kg/ Stk");
    $posY += 10;
    $pdf->SetFont("Arial", "BU", 8);
    $pdf->Text($pocX+$leftMarginText,$posY,"Teil / cislo dilu");
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,"Stk / pocet kusu");

    $posY +=11;

    $pdf->SetFont("Arial", "B", 20);
    $pdf->Text($pocX+$leftMarginText,$posY,getValueForNode($childs,"teil"));
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,getValueForNode($childs,"exstk"));

    $posY += 7;
    $pdf->SetFont("Arial", "B", 7);
    $pdf->Text($pocX+$leftMarginText,$posY,"Bei Reklamationen bitte Auftrags-Nr./ Behaelter / Teil angeben !");

    $posY += 9;
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Text($pocX+$leftMarginText,$posY,"Datum :");
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,"Pruefer / kontrolor");

    $posY += 9;
    $pdf->SetFont("Arial", "B", 10);
    $pdf->Text($pocX+$leftMarginText,$posY,getValueForNode($childs,"termin"));
    $pdf->SetFont("Arial", "", 8);
    $pdf->Text($pocX+$leftMarginText+$sirkaLoga+6,$posY,"..........................");

}

/**
 *
 * @param PDF_Transform $pdf
 * @param int $row
 * @param int $column
 * @param string $popisek
 */
function drawBehaelterBoxChildsA5($pdf,$row,$column,$popisek,$watermark,$childs){

    $sirkaBoxu = 180;
    $vyskaBoxu = 130;

    $sirkaLoga = 40;
    $vyskaLoga = 10;


    $leftMarginText = 5;
    $rozestupBoxuX = 15;
    $rozestupBoxuY = 13;

    $pocX = $column * $sirkaBoxu + ($column+1) * $rozestupBoxuX;
    $pocY = $row * $vyskaBoxu + ($row +1 ) * $rozestupBoxuY;

    //0. vodostisk
    $stredX = $sirkaBoxu/2 + $pocX;
    $stredY = $vyskaBoxu/2 + $pocY;
    $vyskaTextu = 20;

    if($popisek=='FREIGABE') $popisek .= " / UVOLNENI";

    if($watermark=="ja"){
        $pdf->SetFont("Arial", "B", $vyskaTextu);
        $sirkaTextu = $pdf->GetStringWidth($popisek);

        $pdf->SetTextColor(230,230,230);
        $pdf->StartTransform();

        $pdf->Rotate(45,$stredX,$stredY);
        $pdf->Scale(200, 200, $stredX, $stredY);
        $pdf->Text($stredX-$sirkaTextu/2,$stredY,$popisek);

        $pdf->StopTransform();
    }


    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont("Arial", "B", 20);

    $zaPulku = 10;
    //1. nakreslim ramecek
    $pdf->Rect($pocX, $pocY, $sirkaBoxu, $vyskaBoxu);

    //2.logo abydos
    $pdf->Image('../img/abydos_logo.png', $pocX+2, $pocY+2,$sirkaLoga,$vyskaLoga);

    //3. popisek stitku
    $pdf->SetFont("Arial", "B", 20);
    $sirkaTextu = $pdf->GetStringWidth($popisek);
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$pocY+2+$vyskaLoga-2,$popisek);

    $pdf->SetFont("Arial", "B", 25);
    $pdf->Text($pocX+$leftMarginText,$pocY+2+$vyskaLoga-2+$vyskaLoga+5,"EX: ".getValueForNode($childs,"exportnr"));

    //4. podtrhnout hlavicku
    $pdf->Line($pocX, $pocY+2+$vyskaLoga+2, $pocX+$sirkaBoxu, $pocY+2+$vyskaLoga+2);

    //5. auftrag/paleta
    $pdf->SetFont("Arial", "BU", 10);
    $pdf->Text($pocX+$leftMarginText,$pocY+2+$vyskaLoga+2+10+10,"Auftrag / zakazka");
    $sirkaTextu = $pdf->GetStringWidth("Behalter / prepravni box");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$pocY+2+$vyskaLoga+2+10+10,"Behalter / prepravni box");

    $posY = $pocY+2+$vyskaLoga+2+10+10;
    $posY += 13;
    $pdf->SetFont("Arial", "B", 20);
    $pdf->Text($pocX+$leftMarginText,$posY,getValueForNode($childs,"auftragsnr"));
    $sirkaTextu = $pdf->GetStringWidth(getValueForNode($childs,"palnr"));
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,getValueForNode($childs,"palnr"));

    $pdf->SetFont("Arial", "B", 10);
    $pdf->Text($pocX+$leftMarginText+28,$posY-3,getValueForNode($childs,"gew")."kg/ Stk");
    $posY += 10;
    $pdf->SetFont("Arial", "BU", 10);
    $pdf->Text($pocX+$leftMarginText,$posY,"Teil / cislo dilu");
    $sirkaTextu = $pdf->GetStringWidth("Stk / pocet kusu");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,"Stk / pocet kusu");

    $posY +=20;

    $pdf->SetFont("Arial", "B", 62);
    $pdf->Text($pocX+$leftMarginText,$posY,getValueForNode($childs,"teil"));
    $sirkaTextu = $pdf->GetStringWidth(getValueForNode($childs,"exstk"));
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,getValueForNode($childs,"exstk"));


    $posY += 15;
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Text($pocX+$leftMarginText,$posY,"Datum");
    $sirkaTextu = $pdf->GetStringWidth("Pruefer / kontrolor");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,"Pruefer / kontrolor");

    $posY += 15;
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Text($pocX+$leftMarginText,$posY,getValueForNode($childs,"termin"));
    $pdf->SetFont("Arial", "", 12);
    $sirkaTextu = $pdf->GetStringWidth("..........................");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,"..........................");

    $posY += 15;
    $pdf->SetFont("Arial", "", 12);
    $pdf->Text($pocX+$leftMarginText,$posY,"Bei Reklamationen bitte Teile-Nr. / Auftrags-Nr./ Behaelter-Nr.  angeben !");

}

/**
 *
 * @param PDF_Transform $pdf
 * @param int $row
 * @param int $column
 * @param string $popisek
 */
function drawBehaelterBoxA5($pdf,$row,$column,$popisek,$watermark,$childs,$teil){

    $sirkaBoxu = 180;
    $vyskaBoxu = 130;

    $sirkaLoga = 40;
    $vyskaLoga = 10;


    $leftMarginText = 5;
    $rozestupBoxuX = 15;
    $rozestupBoxuY = 13;

    $pocX = $column * $sirkaBoxu + ($column+1) * $rozestupBoxuX;
    $pocY = $row * $vyskaBoxu + ($row +1 ) * $rozestupBoxuY;

    //0. vodostisk
    $stredX = $sirkaBoxu/2 + $pocX;
    $stredY = $vyskaBoxu/2 + $pocY;
    $vyskaTextu = 20;

    if($popisek=='FREIGABE') $popisek .= " / UVOLNENI";

    if($watermark=="ja"){
        $pdf->SetFont("Arial", "B", $vyskaTextu);
        $sirkaTextu = $pdf->GetStringWidth($popisek);

        $pdf->SetTextColor(230,230,230);
        $pdf->StartTransform();

        $pdf->Rotate(45,$stredX,$stredY);
        $pdf->Scale(200, 200, $stredX, $stredY);
        $pdf->Text($stredX-$sirkaTextu/2,$stredY,$popisek);

        $pdf->StopTransform();
    }


    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont("Arial", "B", 20);

    $zaPulku = 10;
    //1. nakreslim ramecek
    $pdf->Rect($pocX, $pocY, $sirkaBoxu, $vyskaBoxu);

    //2.logo abydos
    $pdf->Image('../img/abydos_logo.png', $pocX+2, $pocY+2,$sirkaLoga,$vyskaLoga);

    //3. popisek stitku
    $pdf->SetFont("Arial", "B", 20);
    $sirkaTextu = $pdf->GetStringWidth($popisek);
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$pocY+2+$vyskaLoga-2,$popisek);

    $pdf->SetFont("Arial", "B", 25);
    $pdf->Text($pocX+$leftMarginText,$pocY+2+$vyskaLoga-2+$vyskaLoga+5,"EX: ");

    //4. podtrhnout hlavicku
    $pdf->Line($pocX, $pocY+2+$vyskaLoga+2, $pocX+$sirkaBoxu, $pocY+2+$vyskaLoga+2);

    //5. auftrag/paleta
    $pdf->SetFont("Arial", "BU", 10);
    $pdf->Text($pocX+$leftMarginText,$pocY+2+$vyskaLoga+2+10+10,"Auftrag / zakazka");
    $sirkaTextu = $pdf->GetStringWidth("Behalter / prepravni box");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$pocY+2+$vyskaLoga+2+10+10,"Behalter / prepravni box");

    $posY = $pocY+2+$vyskaLoga+2+10+10;
    $posY += 13;
    $pdf->SetFont("Arial", "B", 20);
    $pdf->Text($pocX+$leftMarginText,$posY,'');
    //$sirkaTextu = $pdf->GetStringWidth(getValueForNode($childs,"palnr"));
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,'');

    $pdf->SetFont("Arial", "B", 10);
    $pdf->Text($pocX+$leftMarginText+28,$posY-3,"kg/ Stk");
    $posY += 10;
    $pdf->SetFont("Arial", "BU", 10);
    $pdf->Text($pocX+$leftMarginText,$posY,"Teil / cislo dilu");
    $sirkaTextu = $pdf->GetStringWidth("Stk / pocet kusu");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,"Stk / pocet kusu");

    $posY +=20;

    $pdf->SetFont("Arial", "B", 62);
    $pdf->Text($pocX+$leftMarginText,$posY,$teil);
    //$sirkaTextu = $pdf->GetStringWidth(getValueForNode($childs,"exstk"));
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,'');


    $posY += 15;
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Text($pocX+$leftMarginText,$posY,"Datum");
    $sirkaTextu = $pdf->GetStringWidth("Pruefer / kontrolor");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,"Pruefer / kontrolor");

    $posY += 15;
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Text($pocX+$leftMarginText,$posY,'');
    $pdf->SetFont("Arial", "", 12);
    $sirkaTextu = $pdf->GetStringWidth("..........................");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,"..........................");

    $posY += 15;
    $pdf->SetFont("Arial", "", 12);
    $pdf->Text($pocX+$leftMarginText,$posY,"Bei Reklamationen bitte Teile-Nr. / Auftrags-Nr./ Behaelter-Nr.  angeben !");

}

require('../fpdf/transform.php');

$pdf = new PDF_Transform('P','mm','A4',1);
//$pdf = new TCPDF('L','mm','A4',1);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

$pdf->AddPage();

if(strlen(trim($export))==0){

if($format=="6x auf A4"){
for($radek=0;$radek<3;$radek++)
    for($sloupec=0;$sloupec<2;$sloupec++)
        drawBehaelterBox($pdf, $radek, $sloupec,$popisek,$watermark);
}

if($format=="2x auf A4"){
    drawBehaelterBoxA5($pdf, $radek, $sloupec, $popisek, $watermark,$palChildNodes,$teil);
}
}
else
{
    // jdu po exportech
    $exporte = $domxml->getElementsByTagName("export");
    $citacPalet = 0;
    foreach($exporte as $ex){
        $exChildNodes = $ex->childNodes;
        $importe = $ex->getElementsByTagName("import");
        foreach($importe as $import){
            $importChildNodes = $import->childNodes;
            $paletten = $import->getElementsByTagName("pal");
            foreach($paletten as $pal){
                if($format=="6x auf A4"){
                    $palChildNodes = $pal->childNodes;
                    $zbytek = $citacPalet % 6;
                    $radek = floor($zbytek/2);
                    $sloupec = $zbytek % 2;
                    if(($zbytek==0)&&($citacPalet>5)) $pdf->AddPage();
                    drawBehaelterBoxChilds($pdf, $radek, $sloupec, $popisek, $watermark,$palChildNodes);
                    $citacPalet++;
                }
                if($format=="2x auf A4"){
                    $palChildNodes = $pal->childNodes;
//                    $radek = $citacPalet % 2;
                    $radek = 0;
                    $sloupec = 0;
//                    if($radek==0 && $citacPalet>1) $pdf->AddPage();
                    if($citacPalet>0) $pdf->AddPage();
                    drawBehaelterBoxChildsA5($pdf, $radek, $sloupec, $popisek, $watermark,$palChildNodes);
                    $citacPalet++;
                }
            }
        }
    }
}


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
