<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D64Y";
$doc_subject = "D64Y Report";
$doc_keywords = "D64Y";

$pole = $_GET['pole'];

$poleArray = split(';', $pole);
// odeberu posledni prazdny prvek
array_pop($poleArray);

$export = $_GET['export'];
$export_datum = $_GET['export_datum'];

$pal2x = $_GET['pal2x'];
$erstpal = trim($_GET['erstpal']);

//echo $export_datum;
$watermark = 'nein';
$format=="2x auf A4";

$apl = AplDB::getInstance();

// vypis vygenerovanych palet podle zadani ve formulari
//
// pokusne spojeni
// 
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
function drawBehaelterBoxChildsA5($pdf,$row,$column,$popisek,$watermark,$dil,$export,$export_datum){

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
//    if($dil['kunde']==130)
//        $pdf->Image('../img/ewm_logo.png', $pocX+2, $pocY+2,$sirkaLoga,$vyskaLoga);
//    else
        $pdf->Image('../img/abydos_logo.png', $pocX+2, $pocY+2,$sirkaLoga,$vyskaLoga);

    //3. popisek stitku
    $pdf->SetFont("Arial", "B", 20);
    $sirkaTextu = $pdf->GetStringWidth($popisek);
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$pocY+2+$vyskaLoga-2,$popisek);

    $pdf->SetFont("Arial", "B", 25);
    $pdf->Text($pocX+$leftMarginText,$pocY+2+$vyskaLoga-2+$vyskaLoga+5,"EX: ".$export);
    $pdf->SetFont("Arial", "BU", 10);
    $sirkaTextu = $pdf->GetStringWidth("Behalter / prepravni box");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$pocY+2+$vyskaLoga-2+$vyskaLoga,"Behalter / prepravni box");
    $pdf->SetFont("Arial", "B", 20);
    $sirkaTextu = $pdf->GetStringWidth($dil['pal']);
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$pocY+2+$vyskaLoga-2+$vyskaLoga+5+2,$dil['pal']);

    $pdf->SetFont("Arial", "BU", 10);
    $chargeTxt = "______________";
    $sirkaTextu = $pdf->GetStringWidth("Charge");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$pocY+2+$vyskaLoga-2+$vyskaLoga+5+2+5,"Charge");
    $pdf->SetFont("Arial", "", 10);
    $sirkaTextuCharge = $pdf->GetStringWidth($chargeTxt);
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextuCharge-$zaPulku,$pocY+2+$vyskaLoga-2+$vyskaLoga+5+2+5+5,$chargeTxt);
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextuCharge-$zaPulku,$pocY+2+$vyskaLoga-2+$vyskaLoga+5+2+5+5+5,$chargeTxt);
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextuCharge-$zaPulku,$pocY+2+$vyskaLoga-2+$vyskaLoga+5+2+5+5+5+5,$chargeTxt);

    $pdf->SetFont("Arial", "B", 20);
    $sirkaTextu = $pdf->GetStringWidth($dil['charge']);
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$pocY+2+$vyskaLoga-2+$vyskaLoga+5+2+5+5+2,$dil['charge']);

    //4. podtrhnout hlavicku
    $pdf->Line($pocX, $pocY+2+$vyskaLoga+2, $pocX+$sirkaBoxu, $pocY+2+$vyskaLoga+2);

    //5. auftrag/paleta
    $pdf->SetFont("Arial", "BU", 10);
    $pdf->Text($pocX+$leftMarginText,$pocY+2+$vyskaLoga+2+10+10,"Auftrag / zakazka");

    $posY = $pocY+2+$vyskaLoga+2+10+10;
    $posY += 13;
    $pdf->SetFont("Arial", "B", 20);
    $pdf->Text($pocX+$leftMarginText,$posY,$dil['import']);

    $pdf->SetFont("Arial", "B", 10);
    $sirkaTextu = $pdf->GetStringWidth($dil['gew']."kg/ Stk");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu/2+$zaPulku,$posY-3,$dil['gew']."kg/ Stk");
    $posY += 10;
    $pdf->SetFont("Arial", "BU", 10);
    $pdf->Text($pocX+$leftMarginText,$posY,"Teil / cislo dilu");
    $sirkaTextu = $pdf->GetStringWidth("Stk / pocet kusu");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,"Stk / pocet kusu");

    $posY +=20;

    $pdf->SetFont("Arial", "B", 62);
    if($dil['kunde']==111)
        //$teilnr = substr($dil['teil'], 0, 7).'.'.substr($dil['teil'], 8);
	$teilnr = $dil['teil'];
    else
        $teilnr = $dil['teil'];
    
    $pdf->Text($pocX+$leftMarginText,$posY,$teilnr);
    $sirkaTextu = $pdf->GetStringWidth($dil['verpackungmenge']);
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu-$sirkaTextu-$zaPulku,$posY,$dil['verpackungmenge']);


    $posY += 15;
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Text($pocX+$leftMarginText,$posY,"Datum");
    $sirkaTextu = $pdf->GetStringWidth("Pruefer / kontrolor");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu/2,$posY,"Pruefer / kontrolor");

    $posY += 15;
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Text($pocX+$leftMarginText,$posY,$export_datum);
    $pdf->SetFont("Arial", "", 12);
    $sirkaTextu = $pdf->GetStringWidth("..........................");
    $pdf->Text($pocX+$leftMarginText+$sirkaBoxu/2,$posY,"..........................");

    $posY += 15;
    $pdf->SetFont("Arial", "", 12);
    $pdf->Text($pocX+$leftMarginText,$posY,"Bei Reklamationen bitte Teile-Nr. / Auftrags-Nr./ Behaelter-Nr./ Charge  angeben !");

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

// vytvorim si pole, podle ktereho budu vyrabet pole palet
if(strlen($export)>0){
    // mam nejake exportni cislo, vytvorim zaklad pro cislovani palet
    $tisice = intval(substr($export, strlen($export)-1));
    $palNrStart = 1000*$tisice+500;
}
else
    $palNrStart = 500;

if(strlen($erstpal)>0) $palNrStart = intval($erstpal);
$dilyArray = array();

$palNr = $palNrStart;

foreach ($poleArray as $poleRadek){
    $radekArray = split(',', $poleRadek);
    $teil = $radekArray[0];
    $verpackungmenge = $radekArray[1];
    $anzpal = $radekArray[2];
    $charge = $radekArray[3];
    $gew = $apl->getTeilGewicht($teil);
    $kunde = $apl->getKundeFromTeil($teil);
    for($i=0;$i<$anzpal;$i++){
        $palNr += 10;
        // kazdou paletu prosim 2x
        // TODO pridelat volnu do formulare, zda chci kazdou paletu dvakrat ....
        array_push($dilyArray, array('teil'=>$teil,'verpackungmenge'=>$verpackungmenge,'pal'=>$palNr,'charge'=>$charge,'gew'=>$gew,'kunde'=>$kunde));
        if($pal2x==1)
            array_push($dilyArray, array('teil'=>$teil,'verpackungmenge'=>$verpackungmenge,'pal'=>$palNr,'charge'=>$charge,'gew'=>$gew,'kunde'=>$kunde));
    }
}

$radek = 0;
$sloupec = 0;

foreach ($dilyArray as $dil) {
    if($radek>1){
        $radek = 0;
        $pdf->AddPage();
    }
    drawBehaelterBoxChildsA5($pdf, $radek, $sloupec, 'FREIGABE / UVOLNENI', $watermark, $dil,$export,$export_datum);
    $radek++;
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
