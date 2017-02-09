<?
session_start();
require_once '../db.php';

    $inputData = $_GET;
    
    $reklid = $inputData['reklid'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    //informace
    $rekl = $apl->getReklamationenArray($reklid);
    if($rekl!==NULL){
	$rekl = $rekl[0];
	$teilbezeichnungA = $apl->getTeilInfoArray($rekl['teil']);
	$teilbezeichnung = $teilbezeichnungA['Teilbez'];
	$gew = floatval($teilbezeichnungA['Gew']);
	$rekl['teilbezeichnung'] = trim($teilbezeichnung);
	$rekl['teilgew'] = $gew;
	//files
	$rekl['savePath']=$apl->getGdatPath()."".$apl->getKundeGdatPath($rekl['kunde'])."/200 Teile/".$rekl['teil']."/".AplDB::$DIRS_FOR_TEIL_FINAL['100']."/".$rekl['rekl_nr'];
	$files = $apl->getFilesForPath($rekl['savePath']);
	$rekl['files'] = $files;
	//abmahnungen
	$abmahnungen = $apl->getAbmahnungenForReklamation($rekl['id']);
	$rekl['abmahnungen'] = $abmahnungen;
	//pridam si prihlaseneho uzivatele
	$rekl['user'] = $_SESSION['user'];
	//schulungen
	$schulungen = $apl->getSchulungenForReklamation($rekl['id']);
	$rekl['schulungen'] = $schulungen;
	
	// kurs
	$kurs = $apl->getKurs($rekl['rekl_datum'], 'EUR', 'CZK');
	$rekl['kurs_EUR_CZK'] = $kurs;
	
	
	// prepocitat kosten v EUR na CZK
	$rekl['anerkannt_ausschuss_preis_czk'] = number_format($kurs * $rekl['anerkannt_ausschuss_preis_eur'],2,'.','');
	$rekl['anerkannt_ausschuss_selbst_preis_czk'] = number_format($kurs * $rekl['anerkannt_ausschuss_selbst_preis_eur'],2,'.','');
	$rekl['anerkannt_nacharbeit_preis_czk'] = number_format($kurs * $rekl['anerkannt_nacharbeit_preis_eur'],2,'.','');
	$rekl['dif_falsch_deklariert_preis_czk'] = number_format($kurs * $rekl['dif_falsch_deklariert_preis_eur'],2,'.','');
	$rekl['verpackung_preis_czk'] = number_format($kurs * $rekl['verpackung_preis_eur'],2,'.','');
	$rekl['kreislauf_preis_czk'] = number_format($kurs * $rekl['kreislauf_preis_eur'],2,'.','');
	$rekl['pauschale_preis_czk'] = number_format($kurs * $rekl['pauschale_preis_eur'],2,'.','');
	
	
	$rekl['anerkannt_ausschuss_preis_eur'] = number_format($rekl['anerkannt_ausschuss_preis_eur'],4,'.','');
	$rekl['anerkannt_ausschuss_selbst_preis_eur'] = number_format($rekl['anerkannt_ausschuss_selbst_preis_eur'],4,'.','');
	$rekl['anerkannt_nacharbeit_preis_eur'] = number_format($rekl['anerkannt_nacharbeit_preis_eur'],4,'.','');
	$rekl['dif_falsch_deklariert_preis_eur'] = number_format($rekl['dif_falsch_deklariert_preis_eur'],4,'.','');
	$rekl['verpackung_preis_eur'] = number_format($rekl['verpackung_preis_eur'],4,'.','');
	$rekl['kreislauf_preis_eur'] = number_format($rekl['kreislauf_preis_eur'],4,'.','');
	$rekl['pauschale_preis_eur'] = number_format($rekl['pauschale_preis_eur'],4,'.','');
	
	$rekl['forecast_anerkannt_ausschuss_czk'] = number_format($kurs * $rekl['forecast_anerkannt_ausschuss_eur'],2,'.','');
	$rekl['forecast_anerkannt_ausschuss_selbst_czk'] = number_format($kurs * $rekl['forecast_anerkannt_ausschuss_selbst_eur'],2,'.','');
	$rekl['forecast_anerkannt_nacharbeit_czk'] = number_format($kurs * $rekl['forecast_anerkannt_nacharbeit_eur'],2,'.','');
	$rekl['forecast_dif_falsch_deklariert_czk'] = number_format($kurs * $rekl['forecast_dif_falsch_deklariert_eur'],2,'.','');
	$rekl['forecast_verpackung_czk'] = number_format($kurs * $rekl['forecast_verpackung_eur'],2,'.','');
	$rekl['forecast_kreislauf_czk'] = number_format($kurs * $rekl['forecast_kreislauf_eur'],2,'.','');
	$rekl['forecast_pauschale_czk'] = number_format($kurs * $rekl['forecast_pauschale_eur'],2,'.','');
	
	$rekl['forecast_anerkannt_ausschuss_eur'] = number_format($rekl['forecast_anerkannt_ausschuss_eur'],4,'.','');
	$rekl['forecast_anerkannt_ausschuss_selbst_eur'] = number_format($rekl['forecast_anerkannt_ausschuss_selbst_eur'],4,'.','');
	$rekl['forecast_anerkannt_nacharbeit_eur'] = number_format($rekl['forecast_anerkannt_nacharbeit_eur'],4,'.','');
	$rekl['forecast_dif_falsch_deklariert_eur'] = number_format($rekl['forecast_dif_falsch_deklariert_eur'],4,'.','');
	$rekl['forecast_verpackung_eur'] = number_format($rekl['forecast_verpackung_eur'],4,'.','');
	$rekl['forecast_kreislauf_eur'] = number_format($rekl['forecast_kreislauf_eur'],4,'.','');
	$rekl['forecast_pauschale_eur'] = number_format($rekl['forecast_pauschale_eur'],4,'.','');
	
    }
    
    $returnArray = array(
	"inputData"=>$inputData,
	"rekl"=>$rekl,
    );
    
    echo json_encode($returnArray);
