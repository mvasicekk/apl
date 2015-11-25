<?php
require '../db.php';
$apl = AplDB::getInstance();

// vyber dma

$sql = "select * from dma where stamp between '2015-01-01' and '2015-01-31'";

$dmaRows = $apl->getQueryRows($sql);

//["id"]=>
//  string(3) "399"
//  ["imanr"]=>
//  string(18) "IMA_195_1501050916"
//  ["emanr"]=>
//  string(12) "EMA_195_0001"
//  ["teil"]=>
//  string(7) "2152564"
//  ["auftragsnrarray"]=>
//  string(6) "198515"
//  ["ema_auftragsarray"]=>
//  string(6) "198515"
//  ["ema_auftragsarray_genehmigt"]=>
//  string(6) "198515"
//  ["ima_auftragsnrarray_genehmigt"]=>
//  string(6) "198515"
//  ["palarray"]=>
//  string(89) "5050;5060;5070;5080;5090;5100;5110;5120;5130;5140;5150;5160;5170;5180;5190;5200;5210;5220"
//  ["ima_dauftrid_array"]=>
//  string(143) "2648374;2648381;2648388;2648395;2648402;2648409;2648416;2648423;2648430;2648437;2648444;2648451;2648458;2648465;2648472;2648479;2648486;2648493"
//  ["ima_dauftrid_array_genehmigt"]=>
//  string(143) "2648374;2648381;2648388;2648395;2648402;2648409;2648416;2648423;2648430;2648437;2648444;2648451;2648458;2648465;2648472;2648479;2648486;2648493"
//  ["ema_dauftrid_array"]=>
//  string(143) "2648374;2648381;2648388;2648395;2648402;2648409;2648416;2648423;2648430;2648437;2648444;2648451;2648458;2648465;2648472;2648479;2648486;2648493"
//  ["ema_dauftrid_array_genehmigt"]=>
//  string(143) "2648374;2648381;2648388;2648395;2648402;2648409;2648416;2648423;2648430;2648437;2648444;2648451;2648458;2648465;2648472;2648479;2648486;2648493"
//  ["ema_palarray"]=>
//  string(89) "5050;5060;5070;5080;5090;5100;5110;5120;5130;5140;5150;5160;5170;5180;5190;5200;5210;5220"
//  ["ema_palarray_genehmigt"]=>
//  string(89) "5050;5060;5070;5080;5090;5100;5110;5120;5130;5140;5150;5160;5170;5180;5190;5200;5210;5220"
//  ["ima_palarray_genehmigt"]=>
//  string(89) "5050;5060;5070;5080;5090;5100;5110;5120;5130;5140;5150;5160;5170;5180;5190;5200;5210;5220"
//  ["tatundzeitarray"]=>
//  string(6) "2010:4"
//  ["ema_tatundzeitarray"]=>
//  string(8) "2010:4:4"
//  ["ema_tatundzeitarray_genehmigt"]=>
//  string(8) "2010:4:4"
//  ["ima_tatundzeitarray_genehmigt"]=>
//  string(6) "2010:4"
//  ["bemerkung"]=>
//  string(16) "Verblechte Teile"
//  ["ema_anlagen_array"]=>
//  NULL
//  ["imavon"]=>
//  string(20) "PHP_172.16.1.135/pvo"
//  ["ima_genehmigt"]=>
//  string(1) "0"
//  ["ema_genehmigt"]=>
//  string(1) "1"
//  ["ima_genehmigt_user"]=>
//  string(0) ""
//  ["ema_genehmigt_user"]=>
//  string(3) "pvo"
//  ["ima_genehmigt_stamp"]=>
//  string(19) "2015-03-25 09:32:34"
//  ["ema_genehmigt_stamp"]=>
//  string(19) "2015-03-25 09:32:34"
//  ["ima_genehmigt_bemerkung"]=>
//  NULL
//  ["ema_genehmigt_bemerkung"]=>
//  NULL
//  ["ema_antrag_vom"]=>
//  string(9) "Petr Volf"
//  ["ema_antrag_text"]=>
//  string(0) ""
//  ["ema_antrag_am"]=>
//  string(19) "2015-01-12 17:08:30"
//  ["stamp"]=>
//  string(19) "2015-01-05 09:16:49"
//}
foreach ($dmaRows as $dma){
    echo "<h1>".$dma['imanr']."</h1>";
    //AplDB::varDump($dma);
    // pro kazdy import z auftragsnrarray, zkusim najit id_dauftr
    $teil = $dma['teil'];
    $imaAntrag_AuftragArray = split(';', $dma['auftragsnrarray']);
    $imaAntrag_PalArray = split(';', $dma['palarray']);
    $imaAntrag_DIdArray = split(';', $dma['ima_dauftrid_array']);
    
    $emaAntrag_AuftragArray = split(';', $dma['ema_auftragsarray']);
    $emaAntrag_PalArray = split(';', $dma['ema_palarray']);
    $emaAntrag_DIdArray = split(';', $dma['ema_dauftrid_array']);
    
    $imaGenehmigt_AuftragArray = split(';', $dma['ima_auftragsnrarray_genehmigt']);
    $imaGenehmigt_PalArray = split(';', $dma['ima_palarray_genehmigt']);
    $imaGenehmigt_DIdArray = split(';', $dma['ima_dauftrid_array_genehmigt']);
    
    $emaGenehmigt_AuftragArray = split(';', $dma['ema_auftragsarray_genehmigt']);
    $emaGenehmigt_PalArray = split(';', $dma['ema_palarray_genehmigt']);
    $emaGenehmigt_DIdArray = split(';', $dma['ema_dauftrid_array_genehmigt']);
    
    
    $importPalArray = array(
	'imaAntrag' => array(
	    'imArray'=>$imaAntrag_AuftragArray,
	    'palArray'=>$imaAntrag_PalArray,
	    'DIdArray'=>$imaAntrag_DIdArray,
	),
	'emaAntrag' => array(
	    'imArray'=>$emaAntrag_AuftragArray,
	    'palArray'=>$emaAntrag_PalArray,
	    'DIdArray'=>$emaAntrag_DIdArray,
	),
	'imaGenehmigt' => array(
	    'imArray'=>$imaGenehmigt_AuftragArray,
	    'palArray'=>$imaGenehmigt_PalArray,
	    'DIdArray'=>$imaGenehmigt_DIdArray,
	),
	'emaGenehmigt' => array(
	    'imArray'=>$emaGenehmigt_AuftragArray,
	    'palArray'=>$emaGenehmigt_PalArray,
	    'DIdArray'=>$emaGenehmigt_DIdArray,
	),
    );
    
    
    foreach ($importPalArray as $typ => $imPalA) {
	echo "<h3>".$typ."</h3>";
	$DIdArray = array();
	if (is_array($imPalA['imArray'])) {
	    foreach ($imPalA['imArray'] as $imaAntrag_Auftrag) {
		// kazdej auftrag zkusim zkombinovat s kazdou paletou a najit odpovidajici id_dauftr v kombinaci s teil a kzgut='G'
		if (is_array($imPalA['palArray'])) {
		    foreach ($imPalA['palArray'] as $imaAntrag_Pal) {
			$sql = "select dauftr.id_dauftr from dauftr where auftragsnr='$imaAntrag_Auftrag' and `pos-pal-nr`='$imaAntrag_Pal' and teil='$teil' and KzGut='G'";
			$dauftrRows = $apl->getQueryRows($sql);
			$id_dauftr = 0;
			if ($dauftrRows !== NULL) {
			    $id_dauftr = $dauftrRows[0]['id_dauftr'];
			}
			if ($id_dauftr > 0) {
			    echo "$imaAntrag_Auftrag - $imaAntrag_Pal ($teil) - $id_dauftr<br>";
			    array_push($DIdArray, $id_dauftr);
			}
		    }
		}
		echo "<hr>";
	    }
	}
	sort($DIdArray);
	echo "DIdArray (read):".join(',', $DIdArray)."<br>";
	sort($imPalA['DIdArray']);
	echo "DIdArray (saved) :".join(',', $DIdArray);
    }
    
}