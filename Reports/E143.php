<meta charset="UTF-8">
<?php
session_start();
require_once '../db.php';

$exPol = array(
    "102"=>array(
	"popis"=>"smluvní hodinová mzda (odpracované dny-v hodinové mzdě)",
	"stunden"=>1,
	"stundenDB"=>"stundenZeit",
	"tage"=>1,
	"tageDB"=>"tageZeit",
	"betrag"=>1,
	"betragDB"=>"betragZeit",
	"aktiv"=>1
    ),
    "103"=>array(
	"popis"=>"smluvní úkolová mzda (odpracované dny v úkolové mzdě)",
	"stunden"=>1,
	"stundenDB"=>"stundenAkkord",
	"tage"=>1,
	"tageDB"=>"tageAkkord",
	"betrag"=>1,
	"betragDB"=>"betragAkkord",
	"aktiv"=>1
    ),
    "500"=>array(
	"popis"=>"počet dnů dovolene",
	"stunden"=>1,
	"stundenDB"=>"dStunden",
	"tage"=>1,
	"tageDB"=>"dTage",
	"betrag"=>0,
	"aktiv"=>1
    ),
    "511"=>array(
	"popis"=>"osobní překážky v práci na straně zaměstnance",
	"stunden"=>1,
	"stundenDB"=>"pStunden",
	"tage"=>1,
	"tageDB"=>"pTage",
	"betrag"=>0,
	"aktiv"=>1
    ),
    "510"=>array(
	"popis"=>"nahrada za svatek",
	"stunden"=>1,
	"stundenDB"=>"calSvatekStunden",
	"tage"=>1,
	"tageDB"=>"calSvatekTage",
	"betrag"=>0,
	"aktiv"=>1
    ),
    //TODO
    "202"=>array(
	"popis"=>"příplatek za práci ve svátek",
	"stunden"=>1,
	"stundenDB"=>"svatekStunden",
	"tage"=>1,
	"tageDB"=>"svatekTage",
	"betrag"=>0,
	"aktiv"=>1,
	"nodpp"=>TRUE,
    ),
    "206"=>array(
	"popis"=>"příplatek za práci v sobotu",
	"stunden"=>1,
	"stundenDB"=>"soStunden",
	"tage"=>0,
	"tageDB"=>"soTage",
	"betrag"=>0,
	"aktiv"=>1,
	"nodpp"=>TRUE,
    ),
    "207"=>array(
	"popis"=>"příplatek za práci v neděli",
	"stunden"=>1,
	"stundenDB"=>"neStunden",
	"tage"=>0,
	"tageDB"=>"neTage",
	"betrag"=>0,
	"nodpp"=>TRUE,
	"aktiv"=>1
    ),
    "203"=>array(
	"popis"=>"příplatek za práci v noci",
	"stunden"=>1,
	"stundenDB"=>"nachtStunden",
	"tage"=>0,
	"tageDB"=>"nachtTage",
	"betrag"=>0,
	"aktiv"=>1
    ),
    "321"=>array(
	"popis"=>"kvalifikační prémie",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"betragDB"=>"qPremieBetrag",
	"aktiv"=>1
    ),
    "322"=>array(
	"popis"=>"prémie za výkon",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"betragDB"=>"leistPremieBetrag",
	"aktiv"=>1
    ),
    "323"=>array(
	"popis"=>"prémie čtvrtletní",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"betragDB"=>"qtlPremieBetrag",
	"aktiv"=>1
    ),
    "303"=>array(
	"popis"=>"osobní ohodnocení",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"aktiv"=>0
    ),
    "324"=>array(
	"popis"=>"příplatek k normě",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"betragDB"=>"erschwernissBetrag",
	"aktiv"=>1
    ),
    "330"=>array(
	"popis"=>"A-prémie",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"betragDB"=>"aPremieBetrag",
	"aktiv"=>1
    ),
    "332"=>array(
	"popis"=>"vánoční prémie",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"aktiv"=>0
    ),
    "551"=>array(
	"popis"=>"neomluvená absence",
	"stunden"=>0,
	"stundenDB"=>"zStunden",
	"tage"=>1,
	"tageDB"=>"zTage",
	"betrag"=>0,
	"aktiv"=>1
    ),
    "552"=>array(
	"popis"=>"neplacené volno bez nároku dle ZP",
	"stunden"=>1,
	"stundenDB"=>"nvStunden",
	"tage"=>1,
	"tageDB"=>"nvTage",
	"betrag"=>0,
	"aktiv"=>1
    ),
    "333"=>array(
	"popis"=>"prémie prach",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"aktiv"=>0
    ),
    "610"=>array(
	"popis"=>"dny nemoci",
	"stunden"=>1,
	"stundenDB"=>"nStunden",
	"tage"=>1,
	"tageDB"=>"nTage",
	"betrag"=>0,
	"aktiv"=>0
    ),
    "334"=>array(
	"popis"=>"prémie rucni naradi",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"betragDB"=>"hfPremieBetrag",
	"aktiv"=>1
    ),
    "737"=>array(
	"popis"=>"doprava zahranici",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"aktiv"=>0
    ),
    "751"=>array(
	"popis"=>"srážka za dopravu",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"betragDB"=>"transportBetrag",
	"aktiv"=>1
    ),
    "760"=>array(
	"popis"=>"srážka za ubytování",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"aktiv"=>0
    ),
    "770"=>array(
	"popis"=>"pohledávky za zaměstnance",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"aktiv"=>0
    ),
    "746"=>array(
	"popis"=>"záloha na mzdu",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"betragDB"=>"vorschussBetrag",
	"aktiv"=>1
    ),
    "748"=>array(
	"popis"=>"srážka za obědy",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"betragDB"=>"essenBetrag",
	"aktiv"=>1
    ),
    "750"=>array(
	"popis"=>"pohl. zál 2",
	"stunden"=>0,
	"tage"=>0,
	"betrag"=>1,
	"aktiv"=>0
    ),
);


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

$a = AplDB::getInstance();

$von = $jahr . "-" . $monat . "-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;

$user = $_SESSION['user'];
$password = $_GET['password'];

//printf("monat: %02d\n",$monat);
//printf("jahr: %04d\n",$jahr);
//printf("persvon: %05d\n",$persvon);
//printf("persbis: %05d\n",$persbis);
//echo "<hr>";

//$pA = array(2411,4815,2440,5887,276,567,5557,2310,1490);
$sql = "select dpers_isp.PersNr as persnr from dpers_isp where PersNr between '$persvon' and '$persbis' order by PersNr";
$pRs = $a->getQueryRows($sql);
$pA = array();
if($pRs!==NULL){
    foreach ($pRs as $pR){
	array_push($pA, $pR['persnr']);
    }
}

//AplDB::varDump($pA);

//exit();
//if(count($pA)==0){
//    exit();
//}

// a-premie
// v E143 je toto
$aPremienArray = $a->getPersnrApremieArray($monat, $jahr, $persvon, $persbis, '*',FALSE);
//AplDB::varDump($aPremienArray);


// dochazka, dovolena nemoci, zakladni udaje o persnr
$joinDpersISP = "dpers_isp";
$sql="";
$sql.=" select";
$sql.="     dpers.persnr,";
$sql.="     dpers.`Name` as name,";
$sql.="     dpers.`Vorname` as vorname,";
$sql.="     CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as vollname,";
$sql.="     dpers.lohnfaktor/60 as perslohnfaktor,";
$sql.="     dpers.leistfaktor,";
$sql.="     dpers.premie_za_vykon,";
$sql.="     dpers.regeloe,";
$sql.="     dpers.alteroe,";
$sql.="     dpers.premie_za_kvalitu,";
$sql.="     dpers.qpremie_akkord,";
$sql.="     dpers.qpremie_zeit,";
$sql.="     dpers.premie_za_prasnost,";
$sql.="     dpers.premie_za_3_mesice,";
$sql.="     dpers.MAStunden,";
$sql.="     dpers.dpersstatus,";
$sql.="     if(dpersbewerber.exekution is null,0,dpersbewerber.exekution) as exekution,";
$sql.="     DATE_FORMAT(dpers.eintritt,'%y-%m-%d') as eintritt,";
$sql.="     DATE_FORMAT(dpers.austritt,'%y-%m-%d') as austritt,";
$sql.="     DATE_FORMAT(dpers.geboren,'%Y-%m-%d') as geboren,";
$sql.="     DATE_FORMAT(dpersdetail1.dobaurcita,'%y-%m-%d') as dobaurcita,";
$sql.="     DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%y-%m-%d') as zkusebni_doba_dobaurcita,";
//$sql.="     dzeit.Datum as datum,";
$sql.="     sum(dzeit.`Stunden`) as sumstunden,";
$sql.="     sum(if(dtattypen.oestatus='a',dzeit.`Stunden`,0)) as sumstundena,";
$sql.="     sum(if(dtattypen.oestatus='a' and dtattypen.akkord<>0,dzeit.`Stunden`,0)) as sumstundena_akkord,";
$sql.="     sum(if(dtattypen.erschwerniss<>0,dzeit.`Stunden`*6,0)) as erschwerniss,";
$sql.="     sum(if(dzeit.tat='z',1,0)) as tage_z,";
$sql.="     sum(if(dzeit.tat='z',dzeit.Stunden,0)) as stunden_z,";
$sql.="     sum(if(dzeit.tat='nv',1,0)) as tage_nv,";
$sql.="     sum(if(dzeit.tat='nv',dzeit.Stunden,0)) as stunden_nv,";
$sql.="     sum(if(dzeit.tat='nw',1,0)) as tage_nw,";
$sql.="     sum(if(dzeit.tat='d',1,0)) as tage_d,";
$sql.="     sum(if(dzeit.tat='d',dzeit.Stunden,0)) as stunden_d,";
$sql.="     sum(if(dzeit.tat='np',1,0)) as tage_np,";
$sql.="     sum(if(dzeit.tat='n',1,0)) as tage_n,";
$sql.="     sum(if(dzeit.tat='n',dzeit.Stunden,0)) as stunden_n,";
$sql.="     sum(if(dzeit.tat='nu',1,0)) as tage_nu,";
$sql.="     sum(if(dzeit.tat='p',1,0)) as tage_p,";
$sql.="     sum(if(dzeit.tat='p',dzeit.Stunden,0)) as stunden_p,";
$sql.="     sum(if(dzeit.tat='u',1,0)) as tage_u,";
$sql.="     sum(if(dzeit.tat='?',1,0)) as tage_frage";
$sql.="     ,sum(if(calendar.cislodne<>7 and calendar.svatek<>0,dzeit.Stunden/8,0)) as tage_svatek";
$sql.="     ,sum(if(calendar.cislodne<>7 and calendar.svatek<>0,dzeit.Stunden,0)) as stunden_svatek";
$sql.="     ,sum(if(dtattypen.fr_sp='N',dzeit.stunden,0)) as nachtstd";
$sql.="     ,durlaub1.jahranspruch";
$sql.="     ,durlaub1.rest";
$sql.="     ,durlaub1.gekrzt";
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
//$sql.="     dzeit.datum";

$rows = $a->getQueryRows($sql);
$persRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	//$datum = $r['datum'];
	$persRows[$persnr]['grundinfo'] = $r;
	//$persRows[$persnr][$datum] = $r;
    }
}

//kontrola, zda ze samotneho apl nedostanu vic lidi nez pri propojeni s Premierem
$sql="";
$sql.=" select";
$sql.="     dpers.persnr,";
$sql.="     dpers.`Name` as name,";
$sql.="     dpers.`Vorname` as vorname,";
$sql.="     CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as vollname,";
$sql.="     dpers.lohnfaktor/60 as perslohnfaktor,";
$sql.="     dpers.leistfaktor,";
$sql.="     dpers.premie_za_vykon,";
$sql.="     dpers.regeloe,";
$sql.="     dpers.alteroe,";
$sql.="     dpers.premie_za_kvalitu,";
$sql.="     dpers.qpremie_akkord,";
$sql.="     dpers.qpremie_zeit,";
$sql.="     dpers.premie_za_prasnost,";
$sql.="     dpers.premie_za_3_mesice,";
$sql.="     dpers.MAStunden,";
$sql.="     dpers.dpersstatus,";
$sql.="     if(dpersbewerber.exekution is null,0,dpersbewerber.exekution) as exekution,";
$sql.="     DATE_FORMAT(dpers.eintritt,'%y-%m-%d') as eintritt,";
$sql.="     DATE_FORMAT(dpers.austritt,'%y-%m-%d') as austritt,";
$sql.="     DATE_FORMAT(dpers.geboren,'%Y-%m-%d') as geboren,";
$sql.="     DATE_FORMAT(dpersdetail1.dobaurcita,'%y-%m-%d') as dobaurcita,";
$sql.="     DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%y-%m-%d') as zkusebni_doba_dobaurcita,";
//$sql.="     dzeit.Datum as datum,";
$sql.="     sum(dzeit.`Stunden`) as sumstunden,";
$sql.="     sum(if(dtattypen.oestatus='a',dzeit.`Stunden`,0)) as sumstundena,";
$sql.="     sum(if(dtattypen.oestatus='a' and dtattypen.akkord<>0,dzeit.`Stunden`,0)) as sumstundena_akkord,";
$sql.="     sum(if(dtattypen.erschwerniss<>0,dzeit.`Stunden`*6,0)) as erschwerniss,";
$sql.="     sum(if(dzeit.tat='z',1,0)) as tage_z,";
$sql.="     sum(if(dzeit.tat='z',dzeit.Stunden,0)) as stunden_z,";
$sql.="     sum(if(dzeit.tat='nv',1,0)) as tage_nv,";
$sql.="     sum(if(dzeit.tat='nv',dzeit.Stunden,0)) as stunden_nv,";
$sql.="     sum(if(dzeit.tat='nw',1,0)) as tage_nw,";
$sql.="     sum(if(dzeit.tat='d',1,0)) as tage_d,";
$sql.="     sum(if(dzeit.tat='d',dzeit.Stunden,0)) as stunden_d,";
$sql.="     sum(if(dzeit.tat='np',1,0)) as tage_np,";
$sql.="     sum(if(dzeit.tat='n',1,0)) as tage_n,";
$sql.="     sum(if(dzeit.tat='n',dzeit.Stunden,0)) as stunden_n,";
$sql.="     sum(if(dzeit.tat='nu',1,0)) as tage_nu,";
$sql.="     sum(if(dzeit.tat='p',1,0)) as tage_p,";
$sql.="     sum(if(dzeit.tat='p',dzeit.Stunden,0)) as stunden_p,";
$sql.="     sum(if(dzeit.tat='u',1,0)) as tage_u,";
$sql.="     sum(if(dzeit.tat='?',1,0)) as tage_frage";
$sql.="     ,sum(if(calendar.cislodne<>7 and calendar.svatek<>0,dzeit.Stunden/8,0)) as tage_svatek";
$sql.="     ,sum(if(calendar.cislodne<>7 and calendar.svatek<>0,dzeit.Stunden,0)) as stunden_svatek";
$sql.="     ,sum(if(dtattypen.fr_sp='N',dzeit.stunden,0)) as nachtstd";
$sql.="     ,durlaub1.jahranspruch";
$sql.="     ,durlaub1.rest";
$sql.="     ,durlaub1.gekrzt";
$sql.=" from dpers";
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
$persAplRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	//$datum = $r['datum'];
	$persAplRows[$persnr]['grundinfo'] = $r;
	//$persRows[$persnr][$datum] = $r;
    }
}

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
    //exit;
}
//AplDB::varDump($persRows);

//transport
$pt="";
$pt.=" select dperstransport.persnr,sum(dperstransport.preis) as transport";
$pt.=" from dperstransport";
$pt.=" where dperstransport.persnr between '$persvon' and '$persbis' and dperstransport.datum between '$von' and '$bis'";
$pt.=" group by dperstransport.persnr";

$rows = $a->getQueryRows($pt);
$persTransportRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persTransportRows[$persnr] = $r;
    }
}

// vorschuss
$pt="";
$pt.=" select";
$pt.=" dvorschuss.persnr,sum(dvorschuss.vorschuss) as sumvorschuss from dvorschuss where dvorschuss.datum between '$von' and '$bis' and dvorschuss.persnr between '$persvon' and '$persbis' group by dvorschuss.persnr";

$rows = $a->getQueryRows($pt);
$persVorschussRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persVorschussRows[$persnr] = $r;
    }
}

//risiko
$pt="";
$pt.=" select";
$pt.="    drueck.PersNr as persnr,";
$pt.="     sum(oe_risiko_zuschlag.faktor/100*risikozuschlag.stunden_zuschlag*if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)/60) as risiko_zuschlag";
$pt.=" from";
$pt.="     drueck";
$pt.=" join dpers on dpers.PersNr=drueck.PersNr";
$pt.=" join oe_risiko_zuschlag on oe_risiko_zuschlag.oe=drueck.oe";
$pt.=" left join risikozuschlag on risikozuschlag.id=oe_risiko_zuschlag.risiko_zuschlag_id";
$pt.=" where";
$pt.="     drueck.Datum between '$von' and '$bis'";
$pt.="     and drueck.persnr between $persvon and $persbis";
$pt.=" group by";
$pt.="     drueck.PersNr";

$rows = $a->getQueryRows($pt);
$persRisikoRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persRisikoRows[$persnr] = $r;
    }
}

//essen

$pt="";
$pt.="select";
$pt.=" dzeit.`PersNr` as persnr,";
$pt.=" sum(dessen.essen_preis) as essen";
$pt.=" from dzeit ";
$pt.=" join dessen on dessen.id_essen=dzeit.id_essen";
$pt.=" where dzeit.`Datum` between '$von' and '$bis' and dzeit.`PersNr` between '$persvon' and '$persbis' and dzeit.essen<>0";
$pt.=" group by dzeit.`PersNr`";

$rows = $a->getQueryRows($pt);
$persEssenRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persEssenRows[$persnr] = $r;
    }
}

// nachzuschlag, so ne
// nachtzuschlag
$pt="";
$pt.="select";
$pt.="     dzeit.PersNr as persnr,";
$pt.="     sum(if(";
$pt.="          (DATE_FORMAT(dzeit.anw_von,'%H:%i')>='22:00' and DATE_FORMAT(dzeit.anw_von,'%H:%i')<='23:59') and (dzeit.anw_bis>dzeit.anw_von),";
$pt.="         if(TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))>4*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))-0.5*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))),";
$pt.="         if(";
$pt.="             DATE_FORMAT(dzeit.anw_von,'%H:%i')>='00:01' and DATE_FORMAT(dzeit.anw_von,'%H:%i')<='06:00' and DATE_FORMAT(dzeit.anw_bis,'%H:%i')<='06:00',";
$pt.="             if(TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))>4*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))-0.5*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))),";
$pt.="             if(";
$pt.="                 DATE_FORMAT(dzeit.anw_von,'%H:%i')>='00:01' and DATE_FORMAT(dzeit.anw_von,'%H:%i')<='06:00' and DATE_FORMAT(dzeit.anw_bis,'%H:%i')>='06:00',";
$pt.="                 if(TIME_TO_SEC(TIMEDIFF(ADDTIME(dzeit.datum,'06:00:00'),dzeit.anw_von))>4*60*60,TIME_TO_SEC(TIMEDIFF(ADDTIME(dzeit.datum,'06:00:00'),dzeit.anw_von))-0.5*60*60,TIME_TO_SEC(TIMEDIFF(ADDTIME(dzeit.datum,'06:00:00'),dzeit.anw_von))),";
$pt.="                 if(";
$pt.="                     DATE_FORMAT(dzeit.anw_bis,'%H:%i')>='22:00' and DATE_FORMAT(dzeit.anw_bis,'%H:%i')<='23:59' and DATE_FORMAT(dzeit.anw_von,'%H:%i')<='22:00',";
$pt.="                     if(TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,ADDTIME(dzeit.datum,'22:00:00')))>4*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,ADDTIME(dzeit.datum,'22:00:00')))*0.5*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,ADDTIME(dzeit.datum,'22:00:00')))),";
$pt.="                     0";
$pt.="                 )";
$pt.="             )";
$pt.="         )";
$pt.=" )/(60*60)) as nacht,";
$pt.=" sum(if(cislodne=6,dzeit.stunden,0)) as sostd,";
$pt.=" sum(if(cislodne=7,dzeit.stunden,0)) as nestd";
$pt.=" from";
$pt.="     dzeit";
$pt.=" join calendar on calendar.datum=dzeit.datum";
$pt.=" where";
$pt.="     dzeit.Datum between '$von' and '$bis'";
$pt.="     and dzeit.persnr between $persvon and $persbis";
$pt.=" group by";
$pt.="     dzeit.PersNr";

$rows = $a->getQueryRows($pt);
$persNachtSoNeRows = array();

//echo "<br>$pt";
//AplDB::varDump($rows);

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persNachtSoNeRows[$persnr] = $r;
    }
}

//premie za kvalitu
$pt="";
$pt.=" select";
$pt.="    drueck.persnr,";
// qpraemie
$pt .= "  sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie),(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie))) as qpraemie_kc,";
$pt .= "  sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie),(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie)),0)) as qpraemie_akkord_kc,";
$pt .= "  sum(if(dtattypen.akkord=0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie),(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie)),0)) as qpraemie_zeit_kc,";
$pt .= "  sum(if(dtattypen.akkord=0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.qualitatspraemie/100,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.qualitatspraemie/100),0)) as qpraemie_zeit_min";
$pt.=" from drueck";
$pt.=" join dtattypen on drueck.oe=dtattypen.tat";
$pt.=" join dpers on dpers.persnr=drueck.persnr";
$pt.=" left join dpersstempel on dpersstempel.persnr=drueck.persnr and dpersstempel.oe=drueck.oe";
$pt.=" where";
$pt.=" (";
$pt.="    (dpers.austritt is null or dpers.austritt>='$von' or dpers.eintritt>dpers.austritt)";
$pt.="    and (drueck.`Datum` between '$von' and '$bis')";
$pt.="    and (drueck.persnr between '$persvon' and '$persbis')";
$pt.=" )";
$pt.=" group by drueck.persnr";


$rows = $a->getQueryRows($pt);
$persQPremieRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persQPremieRows[$persnr] = $r;
    }
}

//abmahnung, mozna bude jako mzdova slozka, aktualne se odecte od premie za kvalitu
$pt = "";
$pt.=" select";
$pt.=" dabmahnung.persnr,sum(dabmahnung.betr) as abmahnung from dabmahnung where dabmahnung.betrdat between '$von' and '$bis' and dabmahnung.persnr between '$persvon' and '$persbis' group by dabmahnung.persnr";
$rows = $a->getQueryRows($pt);
$persAbmahnungRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persAbmahnungRows[$persnr] = $r;
    }
}

$persSvatkyTageRows = $a->getSvatkyTagePers($von,$bis,$persvon,$persbis);
$persSvatkyAllTageRows = $a->getSvatkyTagePers($von,$bis,$persvon,$persbis,FALSE);

//AplDB::varDump($persQPremieRows);

//AplDB::varDump($persNachtSoNeRows);
//------------------------------------------------------------------------------
// casova a vykonova mzda
$sql="";
$sql.=" select";
$sql.="     drueck.persnr,";
//$sql.="     drueck.Datum as datum,";
$sql.="     sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as vzaby,";
$sql.="     sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_akkord,";
$sql.="     sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor)) as vzaby_kc,";
$sql.="     sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor),0)) as vzaby_akkord_kc";
$sql.=" from drueck";
$sql.=" join dtattypen on drueck.oe=dtattypen.tat";
$sql.=" join dpers on dpers.persnr=drueck.persnr";
$sql.=" where";
$sql.=" (";
$sql.=" (dpers.austritt is null or dpers.austritt>='$von' or dpers.eintritt>dpers.austritt)";
$sql.=" and (drueck.`Datum` between '$von' and '$bis')";
$sql.=" and (drueck.persnr between '$persvon' and '$persbis')";
$sql.=" )";
$sql.=" group by ";
$sql.=" drueck.persnr";
//$sql.=" drueck.Datum";

//echo $sql;
$rows = $a->getQueryRows($sql);
$persLeistRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persLeistRows[$persnr] = $r;
    }
}

//premie hf naradi
$sql= " select dperspremie.persnr,dperspremie.betrag,dperspremie.datum";
$sql.= " from dperspremie";
$sql.= " join dpremietypen on dpremietypen.id=dperspremie.id_premie";
$sql.= " where";
$sql.= "     persnr between '$persvon' and '$persbis'";
$sql.= "     and";
$sql.= "     dpremietypen.premiebeschreibung='hf_reparaturen_premie'";
$sql.= "     and";
$sql.= "     dperspremie.datum between '$von' and '$bis'";

$rows = $a->getQueryRows($sql);
$persHFPremieRows = array();

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
	//echo "<br>$persnr v persRows existuje";
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
	
	//$svatekTage = $persRows[$persnr]['grundinfo']['tage_svatek'];
	
	$d = $dTage;
	$nw = $persRows[$persnr]['grundinfo']['tage_nw'];
	$nachtStunden = $persRows[$persnr]['grundinfo']['nachtstd'];
	
	//rozdeleni pracovnich dnu v pomeru hodin Akkord a Zeit
	$atage = $a->getATageProPersnrBetweenDatums($persnr, $von, $bis,0);
	if(($stundenAkkord+$stundenZeit)!=0){
	    $tageAkkord = round($atage * ($stundenAkkord/($stundenAkkord+$stundenZeit)));
	}
	else{
	    $tageAkkord = 0;
	}
	
	$tageZeit = $atage - $tageAkkord;
	
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
    
    $cal1Svatek = $a->getSvatkyTageCount($von, $bis);
    if($cal1Svatek>$svatekAllTage){
	$calSvatekTage = $cal1Svatek-$svatekAllTage;
	//TODO 8 nahradit uvazkem, max. 8 hodin
	$regelStunden = $a->getRegelarbzeit($persnr);
	if($regelStunden>8){
	    $regelStunden = 8;
	}
	$calSvatekStunden = $calSvatekTage * $regelStunden;
    }
    //--------------------------------------------------------------------------
    
    if(array_key_exists($persnr, $persLeistRows)){
	$betragZeit = ($persLeistRows[$persnr]['vzaby']-$persLeistRows[$persnr]['vzaby_akkord'])*$persLohnFaktor;
	$betragAkkord = $persLeistRows[$persnr]['vzaby_akkord_kc'];
    }
    if(array_key_exists($persnr, $persNachtSoNeRows)){
	$soStunden = $persNachtSoNeRows[$persnr]['sostd'];
	$neStunden = $persNachtSoNeRows[$persnr]['nestd'];
    }
    
    if(array_key_exists($persnr, $persQPremieRows)) {
	$qPremieZeit = $bQPremie_zeit?$persQPremieRows[$persnr]['qpraemie_zeit_min']*$persLohnFaktor:0;
	$qPremieAkkord = $bQPremie_akkord?$persQPremieRows[$persnr]['qpraemie_akkord_kc']:0;
	//odecist abmahnung
	$abmahnung = 0;
	if(array_key_exists($persnr, $persAbmahnungRows)){
	    $abmahnung = floatval($persAbmahnungRows[$persnr]['abmahnung']);
	}
	$qPremieBetrag = $qPremieAkkord + $qPremieZeit - $abmahnung;
    }
    
    if(array_key_exists($persnr, $persLeistRows)){
	$pracovnik = $persnr;
	$vzaby = $persLeistRows[$pracovnik]['vzaby'];
	$vzaby_akkord = $persLeistRows[$pracovnik]['vzaby_akkord'];
	$vzaby_zeit = $vzaby - $vzaby_akkord;
	$gesamtVzabyAkkord = $vzaby_akkord;
	$gesamtLeistungZeit = $vzaby_zeit * $leistFaktor;
	
//	echo "<br>vzaby:$vzaby<br>";
//	echo "<br>vzaby_zeit:$vzaby_zeit<br>";
//	echo "<br>gesamtLeistungZeit:$gesamtLeistungZeit<br>";
//	
	
	$citatel = $gesamtLeistungZeit + $gesamtVzabyAkkord;
	
//	echo "<br>citatel:$citatel<br>";
	
	$aTageProMonat = $a->getArbTageBetweenDatums($von, $bis);
	$anwTageArbeitsTage = $a->getATageProPersnrBetweenDatums($pracovnik, $von, $bis, 1);
	$ganzMonatNormMinuten = $aTageProMonat * 8 * 60;
	//$d = 0;
	//$nw = 0;

	$vonTimestamp = strtotime($von);
	
	if ($eintrittTimestamp > $vonTimestamp)
	    $arbTage = $a->getArbTageBetweenDatums($eintritt, $bis);
	else
	    $arbTage = $a->getArbTageBetweenDatums($von, $bis);
	
	$monatNormStunden = 8 * ($arbTage-$d-$nw);
	
//	echo "<br>arbTage:$arbTage<br>";
//	echo "<br>d:$d<br>";
//	echo "<br>nw:$nw<br>";
	
	$monatNormMinuten = $monatNormStunden * 60;
	
	if ($monatNormMinuten != 0)
	    $leistungsGrad = round(($citatel) / $monatNormMinuten, 2);
	else
	    $leistungsGrad = 0;

	
	if ($ganzMonatNormMinuten != 0)
	    $leistungsGradGanzMonat = round(($citatel) / $ganzMonatNormMinuten, 2);
	else
	    $leistungsGradGanzMonat = 0;
	
//	echo "<br>leistungsGrad:$leistungsGrad<br>";
//	echo "<br>leistungsGradGanzMonat:$leistungsGradGanzMonat<br>";
	
	$leistPraemieBerechnet1 = $a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonat) * $aTageProMonat;
        if ($a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonat) == 200)
	    $leistPraemieBerechnet = $leistPraemieBerechnet1;
	else {
	    if ($a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGrad) > $a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonat))
		$leistPraemieBerechnet = $a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGrad) * $anwTageArbeitsTage;
	    else
		$leistPraemieBerechnet = $leistPraemieBerechnet1;
	}
	
	$leistPremieBetrag = $bLeistPremie?$leistPraemieBerechnet:0;
//	echo "<br>leistPremieBetrag:$leistPremieBetrag<br>";
    }
    
    // qtl
    if($bQTLPremie) {
	$pracovnik = $persnr;
	$leistungArray = array('leistung_min' => 0, 'leistung_kc' => 0);
	if ($monat % 3 == 0) {
	    $qtl = ceil($monat / 3);
	    $qtlTageSoll = $a->sollTageQTLProPersNr($jahr, $qtl, $pracovnik);
	    $leistungArray = $a->getQTLLeistungProPersNr($jahr, $qtl, $pracovnik);
	}

        $qtlLeistungIst = $leistungArray['leistung_min'];
        $qtlLeistungIstKc = $leistungArray['leistung_kc'];
        $qtlLeistungSoll = isset($qtlTageSoll) ? $qtlTageSoll * 480 : 0;
        $qtlPraemie = $bQTLPremie == true ? round(0.1 * $qtlLeistungIstKc) : 0;
	if ($qtlLeistungIst < $qtlLeistungSoll){
	    $qtlPraemie = 0;
	}

	$qtlPremieBetrag = $qtlPraemie;
    }
    
    if(array_key_exists($persnr, $persRisikoRows)) {
	$pracovnik = $persnr;
	$erschwernissBetrag = $bErschwerniss?floatval($persRisikoRows[$pracovnik]['risiko_zuschlag']):0;
    }
    
    if(array_key_exists($persnr, $aPremienArray)) {
	$aPremieBetrag = floatval($aPremienArray[$pracovnik]['apremie']);
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
	"qPremieBetrag"=>$qPremieBetrag,
	"leistPremieBetrag"=>$leistPremieBetrag,
	"qtlPremieBetrag"=>$qtlPremieBetrag,
	"hfPremieBetrag"=>$hfPremieBetrag,
	"erschwernissBetrag"=>$erschwernissBetrag,
	"aPremieBetrag"=>$aPremieBetrag,
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

foreach ($slozkyDB as $persnr=>$slA){
    $pracovnik = $persnr;
    //projdu aktivni slozky
    foreach ($exPol as $cisloSlozky=>$slozkaInfo){
	if(intval($slozkaInfo['aktiv'])>0){
	    //dalsi filtr - nebudu posilat polozky, ktere maji vsechny hodnoty dny,hodinym,korunyCelkem nulove
	    //AplDB::varDump($slozkaInfo);
	    $korunyCelkem1 = intval($slozkaInfo['betrag'])>0?$slA[$slozkaInfo['betragDB']]:0;
	    $dny1 = intval($slozkaInfo['tage'])>0?$slA[$slozkaInfo['tageDB']]:0;
	    $hodiny1 = intval($slozkaInfo['stunden'])>0?$slA[$slozkaInfo['stundenDB']]:0;
	    if($korunyCelkem1==0 && $dny1==0 && $hodiny1==0){
		continue;
	    }
	    $kod = $cisloSlozky;
	    $korunyCelkem = number_format($korunyCelkem1,0,',','');
	    $dny = number_format($dny1,2,',','');
	    $hodiny = number_format($hodiny1,2,',','');
	    
	    $exportRow = getMsRow($rok, $mesic, $stredisko, $pracovnik, $kod, $korunyCelkem, $dny, $hodiny, $zakazka, $da1, $da2, $da3, $dat_od, $dat_do);
	    $dpersstatus = $persRows[$persnr]['grundinfo']['dpersstatus'];
	    if(($dpersstatus=='DOHODA') && ($slozkaInfo['nodpp']==TRUE)){
//		preskocit dohody pro vybrane slozky, neplati se svatky, soboty nedele
		continue;
	    }
	    array_push($msRows, array("exrow"=>$exportRow,"comment"=>$slozkaInfo['popis']));
	}
    }
}

//AplDB::varDump($msRows);
foreach ($msRows as $row){
    echo $row['exrow'].' - '.$row['comment']."<br>";
}

// ulozit do souboru
$timestamp = date('His');
$path = sprintf("%s%s/%04d%02d_%s.TXT",$a->getGdatPath(),$a->getDat99Path(),$jahr,$monat,$timestamp);

$fileRows = array();
foreach ($msRows as $r){
    array_push($fileRows, $r['exrow']);
}
file_put_contents($path, $fileRows);

//
//date_default_timezone_set('Europe/Prague');
//
///** PHPExcel */
//require_once '../Classes/PHPExcel.php';
//
//// Create new PHPExcel object
//$objPHPExcel = new PHPExcel();
//
////$user = get_user_pc();
//$user = "aplgenerator";
//// Set properties
//$objPHPExcel->getProperties()->setCreator($user)
//							 ->setLastModifiedBy($user)
//							 ->setTitle("E143")
//							 ->setSubject("E143")
//							 ->setDescription("E143")
//							 ->setKeywords("office openxml php")
//							 ->setCategory("phpexcel");
//
//// popisky sloupcu
//$radek = 2;
//$sloupec = 1;
//
//$popisky = array(
//    "rok",
//    "mesic",
//    "str",
//    "persnr",
//    "mzd",
//    "castka",
//    "dny",
//    "hodiny",
//    "a1",
//    "a2",
//    "a3",
//    "a4",
//    "od",
//    "do",
//    "comment"
//);
//
//foreach($popisky as $p){
//    //echo "$p";
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $sloupec);
//    $sloupec++;
//}
//
//
//$radek++;
//$sloupec = 1;
//
//
//foreach ($msRows as $row){
//    $exrowA = split(';', $row['exrow']);
//    $comment = $row['comment'];
//    foreach ($exrowA as $p){
//	$objPHPExcel->setActiveSheetIndex(0)
//            ->setCellValueByColumnAndRow($sloupec, $radek, $p);
//	$sloupec++;
//    }
//    $objPHPExcel->setActiveSheetIndex(0)
//            ->setCellValueByColumnAndRow($sloupec, $radek, $comment);
//    $radek++;
//    $sloupec = 1;
//}
//
//
//// Rename sheet
////$objPHPExcel->getActiveSheet()->setTitle('E143');
//
//
//// Set active sheet index to the first sheet, so Excel opens this as the first sheet
//
//$objPHPExcel->setActiveSheetIndex(0);
//
//
//// Redirect output to a client’s web browser (Excel5)
//header('Content-Type: application/vnd.ms-excel');
//header('Content-Disposition: attachment;filename="E143.xls"');
//header('Cache-Control: max-age=0');
//
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save('php://output');
//exit;
