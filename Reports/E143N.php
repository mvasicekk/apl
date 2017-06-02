<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Personal - Lohn - New - Detail
	</title>
	<style>
	    body{
		font-family: "Courier New",monospace,sans-serif;
	    }
	    table {
		border-collapse: collapse;
		border:1px solid black;
	    }
	    td {
		border-collapse: collapse;
		border:1px solid black;
		padding: 3px;
	    }
	    th {
		border-collapse: collapse;
		border:1px solid black;
		padding: 3px;
		background-color: lightblue;
		font-size: small;
	    }
	    .negativ{
		color: red;
	    }
	</style>
    </head>
<?php
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$exPol = AplDB::$mzdPolozkyProExportISP;

function getMsRow($rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do){
    $exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
    return $exportRow;
}

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];



$lohnArray = $a->getLohnArray($persvon, $persbis, $jahr, $monat);

//AplDB::varDump($lohnArray);

$von = $jahr . "-" . $monat . "-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;

$user = $_SESSION['user'];
$password = $_GET['password'];

// dochazka, dovolena nemoci, zakladni udaje o persnr
$joinDpersISP = "dpers_isp";
$sql="";
$sql.=" select";
$sql.="     dpers.persnr,";
$sql.="     dpers.`Name` as name,";
$sql.="     dpers.`Vorname` as vorname,";
$sql.="     CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as vollname";
$sql.=" from dpers";
// TODO
// --------------------------------------------------------------------------------------------------------------
$sql.=" join dpers_isp on dpers_isp.persnr=dpers.persnr";	// abych exportoval jen ty, ktere mam v premieru
// --------------------------------------------------------------------------------------------------------------
$sql.=" join dzeit on dzeit.PersNr=dpers.PersNr";
$sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
$sql.=" join calendar on calendar.datum=dzeit.Datum";
$sql.=" left join dpersdetail1 on dpersdetail1.persnr=dpers.`PersNr`";
$sql.=" left join dpersbewerber on dpersbewerber.persnr=dpers.`PersNr`";
$sql.=" left join durlaub1 on durlaub1.`PersNr`=dpers.`PersNr`";
$sql.=" where";
$sql.=" (";
$sql.="     (dpers.austritt is null or dpers.austritt>='$von' or dpers.eintritt>dpers.austritt)";
$sql.="     and (dzeit.`Datum` between '$von' and '$bis')";
$sql.="     and (dpers.persnr between '$persvon' and '$persbis')";
$sql.=" )";
$sql.=" group by ";
$sql.="     dpers.`PersNr`";

$rows = $a->getQueryRows($sql);
$persRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persRows[$persnr]['grundinfo'] = $lohnArray['personen'][$persnr]['grundinfo'];
    }
}

//kontrola, zda ze samotneho apl nedostanu vic lidi nez pri propojeni s Premierem
$persAplRows = array();
// nove
foreach ($lohnArray['personen'] as $persnr=>$pR){
    $persAplRows[$persnr]['grundinfo'] = $pR['grundinfo'];
}

/*
if(count($persAplRows)!=count($persRows)){
    echo "rozdil mezi poctem MA ze samotneho APL a z APL kombinovanym s Premierem<hr>";
    echo "pocet(aplonly)=".count($persAplRows).", pocet(apl+premier)=".count($persRows)."<br>";
    foreach ($persAplRows as $persnr=>$pr){
	if(array_key_exists($persnr, $persRows)){
	    //mam
	}
	else{
	    //nemam
	    echo $persnr." - ".$pr['grundinfo']['name'].' '.$pr['grundinfo']['vorname']."<br>";
	}
    }
}
*/
//transport
$rows = $a->getTransportRows($persvon,$persbis,$von,$bis);
$persTransportRows = array();
if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persTransportRows[$persnr] = $r;
    }
}

// vorschuss
$rows = $a->getVorschussRows($persvon,$persbis,$von,$bis);
$persVorschussRows = array();
if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persVorschussRows[$persnr] = $r;
    }
}

//essen
$rows = $a->getEssenRows($persvon,$persbis,$von,$bis);
$persEssenRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persEssenRows[$persnr] = $r;
    }
}

// nachzuschlag, so ne
// nachtzuschlag
$rows = $a->getNachtSoNeRows($persvon,$persbis,$von,$bis);
$persNachtSoNeRows = array();
if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persNachtSoNeRows[$persnr] = $r;
    }
}

//abmahnung, mozna bude jako mzdova slozka, aktualne se odecte od premie za kvalitu
$persAbmahnungRows = array();
$rows = $a->getAbmahnungRows($persvon,$persbis,$von,$bis);
if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persAbmahnungRows[$persnr] = $r;
    }
}

$persSvatkyTageRows = $a->getSvatkyTagePers($von,$bis,$persvon,$persbis);
$persSvatkyAllTageRows = $a->getSvatkyTagePers($von,$bis,$persvon,$persbis,FALSE);

//premie hf naradi
$persHFPremieRows = array();
$rows = $a->getHFPremieRows($persvon,$persbis,$von,$bis);

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persHFPremieRows[$persnr] = $r;
    }
}
	
$fieldSeparator = ';';
$msRows = array();

//AplDB::varDump($persLeistRows);

$slozkyDB = array();
//jednovelke pole s hodnotama z db, vytvarim osobni cisla podle pole persRows
foreach ($persRows as $persnr=>$persnrA){
    $pracovnik = $persnr;
    $vollname = $persnrA['vollname'];
    //vychozi nastaveni promennych
    
    $stundenZeit = 0;
    $tageZeit = 0;
    $betragZeit = 0;
    $stundenAkkord = 0;
    $tageAkkord = 0;
    $betragAkkord = 0;
    $dStunden = 0;
    $dTage = 0;
    $soStunden = 0;
    $soTage = 0;
    $neStunden = 0;
    $neTage = 0;
    $nachtStunden = 0;
    $nachtTage = 0;
    $qPremieBetrag = 0;
    $leistPremieBetrag = 0;
    $qtlPremieBetrag = 0;
    $hfPremieBetrag = 0;
    $erschwernissBetrag = 0;
    $aPremieBetrag = 0;
    $zStunden = 0;
    $zTage = 0;
    $transportBetrag = 0;
    $vorschussBetrag = 0;
    $essenBetrag = 0;
    $nStunden = 0;
    $nTage = 0;
    $pStunden = 0;
    $pTage = 0;
    $nvStunden = 0;
    $nvTage = 0;
    $svatekStunden = 0;
    $svatekTage = 0;
    $svatekAllTage = 0;
    $calSvatekStunden = 0;
    $calSvatekTage = 0;
    
    
    if(array_key_exists($persnr, $persRows)){
	$eintrittTimestamp = strtotime($persRows[$persnr]['grundinfo']['eintritt']);
	$persLohnFaktor = floatval($persRows[$persnr]['grundinfo']['perslohnfaktor']);
	$leistFaktor = floatval($persRows[$persnr]['grundinfo']['leistfaktor']);
	$bQPremie_zeit = $persRows[$persnr]['grundinfo']['qpremie_zeit']==0?FALSE:TRUE;
	$bQPremie_akkord = $persRows[$persnr]['grundinfo']['qpremie_akkord']==0?FALSE:TRUE;
	$bLeistPremie = $persRows[$persnr]['grundinfo']['premie_za_vykon']==0?FALSE:TRUE;
	$bErschwerniss = $persRows[$persnr]['grundinfo']['premie_za_prasnost']==0?FALSE:TRUE;
	$bQTLPremie = $persRows[$persnr]['grundinfo']['premie_za_3_mesice']==0?FALSE:TRUE;
	$stundenZeit = $persRows[$persnr]['grundinfo']['sumstundena']-$persRows[$persnr]['grundinfo']['sumstundena_akkord'];
	$stundenAkkord = $persRows[$persnr]['grundinfo']['sumstundena_akkord'];
	$dStunden = $persRows[$persnr]['grundinfo']['stunden_d'];
	$dTage = $persRows[$persnr]['grundinfo']['tage_d'];
	$zStunden = $persRows[$persnr]['grundinfo']['stunden_z'];
	$zTage = $persRows[$persnr]['grundinfo']['tage_z'];
	$nStunden = $persRows[$persnr]['grundinfo']['stunden_n'];
	$nTage = $persRows[$persnr]['grundinfo']['tage_n'];
	$pStunden = $persRows[$persnr]['grundinfo']['stunden_p'];
	$pTage = $persRows[$persnr]['grundinfo']['tage_p'];
	$nvStunden = $persRows[$persnr]['grundinfo']['stunden_nv'];
	$nvTage = $persRows[$persnr]['grundinfo']['tage_nv'];
	$svatekStunden = $persRows[$persnr]['grundinfo']['stunden_svatek'];
	$d = $dTage;
	$nw = $persRows[$persnr]['grundinfo']['tage_nw'];
	$nachtStunden = $persRows[$persnr]['grundinfo']['nachtstd'];
	//rozdeleni pracovnich dnu v pomeru hodin Akkord a Zeit
	//nove 2017-04-05
	$bMzdaPodleAdaptace = FALSE;
	$atage = $a->getATageProPersnrBetweenDatums($persnr, $von, $bis, 0);
	if (array_key_exists($persnr, $lohnArray['personen'])) {
	    $bMzdaPodleAdaptace = $lohnArray['personen'][$persnr]['mzdaPodleAdaptace'];
	    if ($bMzdaPodleAdaptace) {
		$tageAkkord = 0;
		$stundenAkkord = 0;
		$tageZeit = $atage;
		$stundenZeit = 0;
		if(is_array($lohnArray['personen'][$persnr]['adaptlohn']['tage'])){
		    foreach ($lohnArray['personen'][$persnr]['adaptlohn']['tage'] as $adaptTag) {
		    $stundenZeit += floatval($adaptTag['anwStunden']);
		}
		}
		else{
		    $stundenZeit = 0;
		}
		
	    } else {
		if (($stundenAkkord + $stundenZeit) != 0) {
		    $tageAkkord = round($atage * ($stundenAkkord / ($stundenAkkord + $stundenZeit)));
		} else {
		    $tageAkkord = 0;
		}
		$tageZeit = $atage - $tageAkkord;
	    }
	}



	if(intval($zTage>0)){
	    $bQPremie_zeit = FALSE;
	    $bQPremie_akkord = FALSE;
	}
    }
    
    //svatky dny jinak
    if($persSvatkyTageRows!==NULL){
	if(is_array($persSvatkyTageRows)){
	    if(array_key_exists($persnr, $persSvatkyTageRows)){
		$svatekTage = $persSvatkyTageRows[$persnr];
	    }
	}
    }
    
    if($persSvatkyAllTageRows!==NULL){
	if(is_array($persSvatkyAllTageRows)){
	    if(array_key_exists($persnr, $persSvatkyAllTageRows)){
		$svatekAllTage = $persSvatkyAllTageRows[$persnr];
	    }
	}
    }
    
    //nahrada za svatek, kdyz nebyl v praci
    $cal1Svatek = $a->getSvatkyTageCount($von, $bis,$persnr);
    //echo "cal1svatek=$cal1Svatek<br>";
    if($cal1Svatek>0){
	//jeste zkontrolovat zda mel v dane svatky platny prac. pomer
	$calSvatekTage = $cal1Svatek;
	//TODO 8 nahradit uvazkem, max. 8 hodin
	$regelStunden = $a->getRegelarbzeit($persnr);
	if($regelStunden>8){
	    $regelStunden = 8;
	}
	$calSvatekStunden = $calSvatekTage * $regelStunden;
    }
    //--------------------------------------------------------------------------
    
    //nove 2017-04-05
    if(array_key_exists($persnr, $lohnArray['personen'])){
	//TODO upravit pro mzdu v adaptaci
	if ($bMzdaPodleAdaptace) {
	    $betragAkkord = 0;
	    $betragZeit = $lohnArray['personen'][$persnr]['adaptlohn']['summeLohn'];
	}
	else{
	    $betragZeit = $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyZeitKc'];
	    $betragAkkord = $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyAkkordKc'];
	}
    }

    if(array_key_exists($persnr, $persNachtSoNeRows)){
	$soStunden = $persNachtSoNeRows[$persnr]['sostd'];
	$neStunden = $persNachtSoNeRows[$persnr]['nestd'];
    }
    
    //nove 2017-04-05
    if(array_key_exists($persnr, $lohnArray['personen'])){
	$qPremieZeit = $bQPremie_zeit?$lohnArray['personen'][$persnr]['premieZaKvalifikaci']['zeit']:0;
	$qPremieAkkord = $bQPremie_akkord?$lohnArray['personen'][$persnr]['premieZaKvalifikaci']['akkord']:0;
	//odecist abmahnung
	$abmahnung = 0;
	if(array_key_exists($persnr, $persAbmahnungRows)){
	    $abmahnung = floatval($persAbmahnungRows[$persnr]['abmahnung']);
	}
	$qPremieBetrag = $qPremieAkkord + $qPremieZeit - $abmahnung;
    }
    //nove 2017-04-05
    if(array_key_exists($persnr, $lohnArray['personen'])){
	$leistPremieBetrag = $bLeistPremie?$lohnArray['personen'][$persnr]['leistungPremie']['leistungsPremieBetrag']:0;
    }
    // qtl
    //nove 2017-04-05
    if(array_key_exists($persnr, $lohnArray['personen'])){
	$qtlPremieBetrag = $bQTLPremie==TRUE?$lohnArray['personen'][$persnr]['qtlPremie']['qtlPremieBetrag']:0;
    }
    //nove 2017-04-05
    if(array_key_exists($persnr, $lohnArray['personen'])){
	$aPremieBetrag = floatval($lohnArray['personen'][$persnr]['aPremie']['apremie']);
    }
    if(array_key_exists($persnr, $persTransportRows)){
	$transportBetrag = floatval($persTransportRows[$persnr]['transport']);
    }

    if(array_key_exists($persnr, $persVorschussRows)){
	    $vorschussBetrag = floatval($persVorschussRows[$persnr]['sumvorschuss']);
    }
    
    if(array_key_exists($persnr, $persEssenRows)){
	$essenBetrag = floatval($persEssenRows[$persnr]['essen']);
    }
    
    if(array_key_exists($persnr, $persHFPremieRows)){
	$hfPremieBetrag = floatval($persHFPremieRows[$persnr]['betrag']);
    }

    
    $slozkyDB[$persnr] = array(
	"stundenZeit"=>$stundenZeit,
	"tageZeit"=>$tageZeit,
	"betragZeit"=>$betragZeit,
	"stundenAkkord"=>$stundenAkkord,
	"tageAkkord"=>$tageAkkord,
	"betragAkkord"=>$betragAkkord,
	"dStunden"=>$dStunden,
	"dTage"=>$dTage,
	"soStunden"=>$soStunden,
	"soTage"=>$soTage,
	"neStunden"=>$neStunden,
	"neTage"=>$neTage,
	"nachtStunden"=>$nachtStunden,
	"nachtTage"=>$nachtTage,
	"qPremieBetrag"=>$bMzdaPodleAdaptace||$zTage>0?0:$qPremieBetrag,
	"leistPremieBetrag"=>$bMzdaPodleAdaptace||$zTage>0?0:$leistPremieBetrag,
	"qtlPremieBetrag"=>$bMzdaPodleAdaptace||$zTage>0?0:$qtlPremieBetrag,
	"hfPremieBetrag"=>$zTage>0&&$hfPremieBetrag>0?0:$hfPremieBetrag,
	"erschwernissBetrag"=>$erschwernissBetrag,
	"aPremieBetrag"=>$bMzdaPodleAdaptace||$zTage>0?0:$aPremieBetrag,
	"zStunden"=>$zStunden,
	"zTage"=>$zTage,
	"transportBetrag"=>$transportBetrag,
	"vorschussBetrag"=>$vorschussBetrag,
	"essenBetrag"=>$essenBetrag,
	"pStunden"=>$pStunden,
	"pTage"=>$pTage,
	"nvStunden"=>$nvStunden,
	"nvTage"=>$nvTage,
	"nStunden"=>$nStunden,
	"nTage"=>$nTage,
	"svatekStunden"=>$svatekStunden,
	"svatekTage"=>$svatekTage,
	"calSvatekStunden"=>$calSvatekStunden,
	"calSvatekTage"=>$calSvatekTage,
    );
}

//AplDB::varDump($slozkyDB);


$rok = sprintf("%04d", $jahr);
$mesic = sprintf("%02d", $monat);
$stredisko = sprintf("%d", 0);
$zakazka = 0;
$da1 = '';
$da2 = '';
$da3 = '';
$dat_od = date("d.m.Y", strtotime($von));
$dat_do = date("d.m.Y", strtotime($bis));

//echo "slozkyDB";
//AplDB::varDump($slozkyDB);

echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th colspan='2'>";
echo "PersNr";
echo "</th>";
foreach ($exPol as $cisloSlozky=>$slozkaInfo){
    if(intval($slozkaInfo['aktiv'])>0){
	echo "<th>";
	echo $cisloSlozky."<br>";
	//echo $slozkaInfo['popis'];
	echo "</th>";
    }
}
echo "</tr>";
echo "</thead>";
foreach ($slozkyDB as $persnr=>$slA){
    $pracovnik = $persnr;
    $vollname = $persRows[$persnr]['grundinfo']['vollname'];
    echo "<tr>";
    echo "<td style='text-align:left;white-space:nowrap;'>";
    echo "$persnr<br>";
    echo "$vollname";
    echo "</td>";
    echo "<td style='text-align:right;white-space:nowrap;'>";
    echo "Kč<br>";
    echo "dny<br>";
    echo "hod";
    echo "</td>";
    
    //projdu aktivni slozky
    foreach ($exPol as $cisloSlozky=>$slozkaInfo){
	if(intval($slozkaInfo['aktiv'])>0){
	    //dalsi filtr - nebudu posilat polozky, ktere maji vsechny hodnoty dny,hodinym,korunyCelkem nulove
	    //AplDB::varDump($slozkaInfo);
	    $korunyCelkem1 = intval($slozkaInfo['betrag'])>0?$slA[$slozkaInfo['betragDB']]:0;
	    $dny1 = intval($slozkaInfo['tage'])>0?$slA[$slozkaInfo['tageDB']]:0;
	    $hodiny1 = intval($slozkaInfo['stunden'])>0?$slA[$slozkaInfo['stundenDB']]:0;
	    if($korunyCelkem1==0 && $dny1==0 && $hodiny1==0){
		echo "<td style='text-align:center;'>";
		echo "-";
		echo "</td>";
		continue;
	    }
	    $kod = $cisloSlozky;
	    $korunyCelkem = number_format($korunyCelkem1,0,',','');
	    $korunyCelkemF = number_format($korunyCelkem1,0,',',' ');
	    $dny = number_format($dny1,2,',','');
	    $hodiny = number_format($hodiny1,2,',','');
	    
	    $exportRow = getMsRow($rok, $mesic, $stredisko, $pracovnik, $kod, $korunyCelkem, $dny, $hodiny, $zakazka, $da1, $da2, $da3, $dat_od, $dat_do);
	    $dpersstatus = $persRows[$persnr]['grundinfo']['dpersstatus'];
	    if(($dpersstatus=='DOHODA') && ($slozkaInfo['nodpp']==TRUE)){
//		preskocit dohody pro vybrane slozky, neplati se svatky, soboty nedele
		continue;
	    }
	    $sumy[$kod]['koruny'] += $korunyCelkem1;
	    $sumy[$kod]['dny'] += $dny1;
	    $sumy[$kod]['hodiny'] += $hodiny1;
	    
	    $negKorunyClass = $korunyCelkem1<0?'negativ':'';
	    $negDnyClass = $dny1<0?'negativ':'';
	    $negHodinyClass = $hodiny1<0?'negativ':'';
	    
	    echo "<td style='text-align:right;white-space:nowrap;'>";
	    echo "<span class='$negKorunyClass'>$korunyCelkemF</span><br>";
	    echo "<span class='$negDnyClass'>$dny</span><br>";
	    echo "<span class='$negHodinyClass'>$hodiny</span>";
	    echo "</td>";
	    array_push($msRows, array("exrow"=>$exportRow,"comment"=>$slozkaInfo['popis']));
	}
    }
    echo "</tr>";
}
echo "<tfoot>";
echo "<tr>";
    echo "<th>Sum";
    echo "</th>";
    echo "<th style='text-align:right;white-space:nowrap;'>";
    echo "Kč<br>";
    echo "dny<br>";
    echo "hod";
    echo "</th>";
    foreach ($exPol as $cisloSlozky=>$slozkaInfo){
	if(intval($slozkaInfo['aktiv'])>0){
	    echo "<th style='text-align:right;white-space:nowrap;'>";
	    $korunyCelkemF = number_format($sumy[$cisloSlozky]['koruny'],0,',',' ');
	    $dny = number_format($sumy[$cisloSlozky]['dny'],2,',','');
	    $hodiny = number_format($sumy[$cisloSlozky]['hodiny'],2,',','');
	    echo "$korunyCelkemF<br>";
	    echo "$dny<br>";
	    echo "$hodiny";
	    echo "</th>";
	}
    }   
echo "<tr>";
echo "</tfoot>";
echo "</table>";
//AplDB::varDump($msRows);
//foreach ($msRows as $row){
//    echo $row['exrow'].' - '.$row['comment']."<br>";
//}
foreach ($exPol as $cisloSlozky=>$slozkaInfo){
	if(intval($slozkaInfo['aktiv'])>0){
	    printf("%03d - %s <br>",$cisloSlozky,$slozkaInfo['popis']);
	}
}
// ulozit do souboru
$timestamp = date('His');
$path = sprintf("%s%s/%04d%02d_%s.TXT",$a->getGdatPath(),$a->getDat99Path(),$jahr,$monat,$timestamp);

$fileRows = array();
foreach ($msRows as $r){
    array_push($fileRows, $r['exrow']);
}
file_put_contents($path, $fileRows);
?>
</html>