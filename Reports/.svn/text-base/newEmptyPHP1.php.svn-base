<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    global $cells;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell($cells['export']['sirka']+
                $cells['teilnr']['sirka']+
                $cells['pal']['sirka']+
                $cells['stk_exp']['sirka']+
                $cells['stk']['sirka'],
                5,"Summe: ",'LRBT',0,'L',0);

        $obsah = $sumArray['gew_brutto'];
        $pdfobjekt->Cell($cells['gew_brutto']['sirka'],
                5,$obsah,'LRBT',0,'R',0);

        $obsah = $sumArray['gew_netto'];
        $pdfobjekt->Cell($cells['gew_netto']['sirka'],
                5,$obsah,'LRBT',0,'R',0);

        $pdfobjekt->Ln();
?>
