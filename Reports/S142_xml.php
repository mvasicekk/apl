<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');
$aplDB = new AplDB();

global $db;

//$von = $jahr."-".$monat."-01";
//$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
//$bis = $jahr."-".$monat."-".$pocetDnuVMesici;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

$pcip=get_pc_ip();

//$views=array("anwesenheit","leistung","vorschuss","transport","essen","abmahnung","sonstpremie","nachtzuschlag","risiko","dpp");
$views=array("anwesenheit","vorschuss","transport","essen","abmahnung","sonstpremie","nachtzuschlag","dpp");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="    dpers.persnr,";
$pt.="    dpers.`Name` as name,";
$pt.="    dpers.`Vorname` as vorname,";
$pt.="    CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as vollname,";
$pt.="    dpers.`Schicht` as schicht,";
$pt.="    dpers.lohnfaktor/60 as perslohnfaktor,";
$pt.="    dpers.leistfaktor,";
$pt.="    dpers.premie_za_vykon,";
$pt.="    dpers.regeloe,";
$pt.="    dpers.premie_za_kvalitu,";
$pt.="    dpers.qpremie_akkord,";
$pt.="    dpers.qpremie_zeit,";
$pt.="    dpers.premie_za_prasnost,";
$pt.="    dpers.premie_za_3_mesice,";
$pt.="    dpers.MAStunden,";
$pt.="    if(dpersbewerber.exekution is null,0,dpersbewerber.exekution) as exekution,";
$pt.="    DATE_FORMAT(dpers.eintritt,'%y-%m-%d') as eintritt,";
$pt.="    DATE_FORMAT(dpers.austritt,'%y-%m-%d') as austritt,";
$pt.="    DATE_FORMAT(dpers.geboren,'%Y-%m-%d') as geboren,";
$pt.="    DATE_FORMAT(dpersdetail1.dobaurcita,'%y-%m-%d') as dobaurcita,";
$pt.="    DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%y-%m-%d') as zkusebni_doba_dobaurcita,";
$pt.="    sum(dzeit.`Stunden`) as sumstunden,";
$pt.="    sum(if(dtattypen.oestatus='a',dzeit.`Stunden`,0)) as sumstundena,";
$pt.="    sum(if(dtattypen.oestatus='a' and dtattypen.akkord<>0,dzeit.`Stunden`,0)) as sumstundena_akkord,";
// erschwerniss
$pt.="    sum(if(dtattypen.erschwerniss<>0,dzeit.`Stunden`*6,0)) as erschwerniss,";
$pt.="    sum(if(dzeit.tat='z',1,0)) as tage_z,";
$pt.="    sum(if(dzeit.tat='nv',1,0)) as tage_nv,";
$pt.="    sum(if(dzeit.tat='nw',1,0)) as tage_nw,";
$pt.="    sum(if(dzeit.tat='d',1,0)) as tage_d,";
$pt.="    sum(if(dzeit.tat='np',1,0)) as tage_np,";
$pt.="    sum(if(dzeit.tat='n',1,0)) as tage_n,";
$pt.="    sum(if(dzeit.tat='nu',1,0)) as tage_nu,";
$pt.="    sum(if(dzeit.tat='p',1,0)) as tage_p,";
$pt.="    sum(if(dzeit.tat='u',1,0)) as tage_u,";
$pt.="    sum(if(dzeit.tat='?',1,0)) as tage_frage";
$pt.="    ,sum(if(dtattypen.fr_sp='N',dzeit.stunden,0)) as nachtstd";
$pt.="    ,durlaub1.jahranspruch";
$pt.="    ,durlaub1.rest";
$pt.="    ,durlaub1.gekrzt";
$pt.=" from dpers";
$pt.=" join dzeit using(persnr)";
$pt.=" join dtattypen on dzeit.tat=dtattypen.tat";
$pt.=" join lohnabrechtyp on dpers.lohnabrechtyp=lohnabrechtyp.lohntyp";
$pt.=" left join dpersdetail1 on dpersdetail1.persnr=dpers.`PersNr`";
$pt.=" left join dpersbewerber on dpersbewerber.persnr=dpers.`PersNr`";
$pt.=" left join durlaub1 on durlaub1.`PersNr`=dpers.`PersNr`";
$pt.=" where";
$pt.=" (";
$pt.="    (dpers.austritt is null or dpers.austritt>='$von' or dpers.eintritt>dpers.austritt)";
$pt.="    and (dzeit.`Datum` between '$von' and '$bis')";
$pt.="    and (dpers.persnr between '$persvon' and '$persbis')";
if($lohnabrechtyp!==NULL){
    $pt.="    and (lohnabrechtyp.beschr_kurz='$lohnabrechtyp')";
}
$pt.=" )";
$pt.=" group by dpers.`PersNr`";

$db->query($pt);

//echo $pt;
//$viewname=$pcip.$views[1];
//$db->query("drop view $viewname");
//$pt="create view $viewname";
//$pt.=" as select";
//$pt.="    drueck.persnr,";
//$pt.="    dtattypen.og as og,";
//$pt .= "  sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as vzaby,";
//$pt .= "  sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_akkord,";
//
//// prepocet na kc podle faktoru u OE v tabulce dtattypen
//// zmena 2014-01-23
//// prepocet na kc bude podle faktoru ulozeneho u operace
////$pt .= "  sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor,(drueck.`Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor)) as vzaby_kc,";
////$pt .= "  sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor,(drueck.`Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor),0)) as vzaby_akkord_kc,";
//
//$pt .= "  sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor)) as vzaby_kc,";
//$pt .= "  sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor),0)) as vzaby_akkord_kc,";
//
//// qpraemie prozent
////$pt .= " if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von>=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie) as qpraemie_prozent,";
//// qpraemie
////$pt .= "  sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie),(drueck.`Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie))) as qpraemie_kc,";
////$pt .= "  sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie),(drueck.`Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie)),0)) as qpraemie_akkord_kc,";
////$pt .= "  sum(if(dtattypen.akkord=0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie),(drueck.`Stück`)*drueck.`VZ-IST`*`dtaetkz-abg`.lohn_faktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie)),0)) as qpraemie_zeit_kc,";
////$pt .= "  sum(if(dtattypen.akkord=0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.qualitatspraemie/100,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.qualitatspraemie/100),0)) as qpraemie_zeit_min";
//
//$pt .= "  sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie),(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie))) as qpraemie_kc,";
//$pt .= "  sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie),(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie)),0)) as qpraemie_akkord_kc,";
//$pt .= "  sum(if(dtattypen.akkord=0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie),(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor/100*if(dpersstempel.qpraemie_prozent is not null,if(dpersstempel.datum_von<=drueck.datum,dpersstempel.qpraemie_prozent,dtattypen.qualitatspraemie),dtattypen.qualitatspraemie)),0)) as qpraemie_zeit_kc,";
//$pt .= "  sum(if(dtattypen.akkord=0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.qualitatspraemie/100,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.qualitatspraemie/100),0)) as qpraemie_zeit_min";
//
//$pt.=" from drueck";
//$pt.=" join dtattypen on drueck.oe=dtattypen.tat";
////$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.taetnr";
//$pt.=" join dpers on dpers.persnr=drueck.persnr";
//$pt.=" left join dpersstempel on dpersstempel.persnr=drueck.persnr and dpersstempel.oe=drueck.oe";
//$pt.=" where";
//$pt.=" (";
//$pt.="    (dpers.austritt is null or dpers.austritt>='$von' or dpers.eintritt>dpers.austritt)";
//$pt.="    and (drueck.`Datum` between '$von' and '$bis')";
//$pt.="    and (drueck.persnr between '$persvon' and '$persbis')";
//$pt.=" )";
//$pt.=" group by drueck.persnr,dtattypen.og";
//
////echo $pt."<br>";
//$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" dvorschuss.persnr,sum(dvorschuss.vorschuss) as sumvorschuss from dvorschuss where dvorschuss.datum between '$von' and '$bis' and dvorschuss.persnr between '$persvon' and '$persbis' group by dvorschuss.persnr";
//echo $pt."<br>";
$db->query($pt);

//transport
$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dperstransport.persnr,sum(dperstransport.preis) as transport";
$pt.=" from dperstransport";
$pt.=" where dperstransport.persnr between '$persvon' and '$persbis' and dperstransport.datum between '$von' and '$bis'";
$pt.=" group by dperstransport.persnr";
$db->query($pt);

//essen
$viewname=$pcip.$views[3];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" dzeit.`PersNr` as persnr,";
$pt.=" sum(dessen.essen_preis) as essen";
$pt.=" from dzeit ";
$pt.=" join dessen on dessen.id_essen=dzeit.id_essen";
$pt.=" where dzeit.`Datum` between '$von' and '$bis' and dzeit.`PersNr` between '$persvon' and '$persbis' and dzeit.essen<>0";
$pt.=" group by dzeit.`PersNr`";
$db->query($pt);

//abmahnung
$viewname=$pcip.$views[4];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" dabmahnung.persnr,sum(dabmahnung.betr) as abmahnung from dabmahnung where (dabmahnung.vorschlag=0) and dabmahnung.betrdat between '$von' and '$bis' and dabmahnung.persnr between '$persvon' and '$persbis' group by dabmahnung.persnr";
//echo $pt."<br>";
$db->query($pt);

// sonstpremie
$viewname=$pcip.$views[5];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" dperspremie.persnr,sum(dperspremie.betrag) as sonstpremie from dperspremie where dperspremie.datum between '$von' and '$bis' and dperspremie.persnr between '$persvon' and '$persbis' group by dperspremie.persnr";
//echo $pt."<br>";
$db->query($pt);

// nachtzuschlag
$viewname=$pcip.$views[6];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     dzeit.PersNr,";
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
$pt.=" sum(if(cislodne=6 or cislodne=7,dzeit.stunden,0)) as sonestd";
$pt.=" from";
$pt.="     dzeit";
$pt.=" join calendar on calendar.datum=dzeit.datum";
$pt.=" where";
$pt.="     dzeit.Datum between '$von' and '$bis'";
$pt.="     and dzeit.persnr between $persvon and $persbis";
$pt.=" group by";
$pt.="     dzeit.PersNr";
//echo $pt."<br>";
$db->query($pt);

//echo $pt;

// risiko
//$viewname=$pcip.$views[8];
//$pt=" create view $viewname";
//$pt.=" as select";
//$pt.="    drueck.PersNr as persnr,";
//// zmena 2013-01-23 misto verb-zeit se bude pro vypocet pouzivat vzaby
////$pt.="     sum(abgnr_risiko_zuschlag.faktor/100*risikozuschlag.stunden_zuschlag*drueck.`Verb-Zeit`/60) as risiko_zuschlag";
//$pt.="     sum(abgnr_risiko_zuschlag.faktor/100*risikozuschlag.stunden_zuschlag*if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)/60) as risiko_zuschlag";
//$pt.=" from";
//$pt.="     drueck";
//$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
//$pt.=" join dpers on dpers.PersNr=drueck.PersNr";
//$pt.=" join abgnr_risiko_zuschlag on abgnr_risiko_zuschlag.abgnr=drueck.TaetNr";
//$pt.=" left join risikozuschlag on risikozuschlag.id=abgnr_risiko_zuschlag.risiko_zuschlag_id";
//$pt.=" where";
//$pt.="     drueck.Datum between '$von' and '$bis'";
//$pt.="     and drueck.persnr between $persvon and $persbis";
//$pt.=" group by";
//$pt.="     drueck.PersNr";

// 2014-03-11 vzpocet rizikoveho priplatku podle OE

//$pt=" create view $viewname";
//$pt.=" as select";
//$pt.="    drueck.PersNr as persnr,";
//// zmena 2013-01-23 misto verb-zeit se bude pro vypocet pouzivat vzaby
////$pt.="     sum(abgnr_risiko_zuschlag.faktor/100*risikozuschlag.stunden_zuschlag*drueck.`Verb-Zeit`/60) as risiko_zuschlag";
//$pt.="     sum(oe_risiko_zuschlag.faktor/100*risikozuschlag.stunden_zuschlag*if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)/60) as risiko_zuschlag";
//$pt.=" from";
//$pt.="     drueck";
////$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
//$pt.=" join dpers on dpers.PersNr=drueck.PersNr";
//$pt.=" join oe_risiko_zuschlag on oe_risiko_zuschlag.oe=drueck.oe";
//$pt.=" left join risikozuschlag on risikozuschlag.id=oe_risiko_zuschlag.risiko_zuschlag_id";
//$pt.=" where";
//$pt.="     drueck.Datum between '$von' and '$bis'";
//$pt.="     and drueck.persnr between $persvon and $persbis";
//$pt.=" group by";
//$pt.="     drueck.PersNr";
////echo $pt;
//$db->query($pt);

// dpp
$viewname=$pcip.$views[7];
$pt=" create view $viewname";
$pt.=" as select";
$pt.="    persnr,";
$pt.="    eintritt,befristet";
$pt.=" from";
$pt.="     dpersvertrag";
$pt.=" join `dvertragtyp` on `dvertragtyp`.`id`=dpersvertrag.vertragtyp_id";
$pt.=" where";
$pt.="     ((eintritt between '$von' and '$bis')";
$pt.="     or (befristet between '$von' and '$bis')";
$pt.="     or ((eintritt<'$bis') and (befristet is null))";
$pt.=")";
$pt.="     and (dvertragtyp.typ_kz='DPP')";
$pt.="     and dpersvertrag.persnr between $persvon and $persbis";

$db->query($pt);

$anwesenheit=$pcip.$views[0];
//$leistung=$pcip.$views[1];
$vorschuss = $pcip.$views[1];
$transport = $pcip.$views[2];
$essen = $pcip.$views[3];
$abmahnung = $pcip.$views[4];
$sonstpremie = $pcip.$views[5];
$nachtzuschlag = $pcip.$views[6];
//$risiko = $pcip.$views[8];
$dpp = $pcip.$views[7];

$sql = "select $anwesenheit.persnr,";
$sql.= " $anwesenheit.name,";
$sql.= " $anwesenheit.vorname,";
$sql.= " $anwesenheit.vollname,";
$sql.= " $anwesenheit.schicht,";
$sql.= " $anwesenheit.eintritt,";
$sql.= " $anwesenheit.austritt,";
$sql.= " $anwesenheit.geboren,";
$sql.= " $anwesenheit.dobaurcita,";
$sql.= " $anwesenheit.regeloe,";
$sql.= " $anwesenheit.zkusebni_doba_dobaurcita,";
$sql.= " $anwesenheit.sumstunden,";
$sql.= " $anwesenheit.sumstundena,";
$sql.= " $anwesenheit.sumstundena_akkord,";
$sql.= " $anwesenheit.erschwerniss,";
//$sql.= " $anwesenheit.essen,";
$sql.= " $anwesenheit.premie_za_vykon,";
$sql.= " $anwesenheit.premie_za_kvalitu,";
$sql.= " $anwesenheit.qpremie_akkord,";
$sql.= " $anwesenheit.qpremie_zeit,";
$sql.= " $anwesenheit.premie_za_prasnost,";
$sql.= " $anwesenheit.premie_za_3_mesice";
$sql.= " ,$anwesenheit.MAStunden";
$sql.= " ,$anwesenheit.exekution";
$sql.=" ,'$bis' as datumbis";
$sql.=" ,'$von' as datumvon,";

$sql.= " $anwesenheit.tage_d,";
$sql.= " $anwesenheit.tage_p,";
$sql.= " $anwesenheit.tage_z,";
$sql.= " $anwesenheit.tage_nv,";
$sql.= " $anwesenheit.tage_n,";
$sql.= " $anwesenheit.tage_np,";
$sql.= " $anwesenheit.tage_frage,";
$sql.= " $anwesenheit.nachtstd,";
$sql.= " $anwesenheit.tage_u,";
$sql.= " $anwesenheit.tage_nw,";
$sql.= " $anwesenheit.tage_nu,";
$sql.= " $anwesenheit.jahranspruch,";
$sql.= " $anwesenheit.rest,";
$sql.= " $anwesenheit.gekrzt,";

$sql.= " if($vorschuss.sumvorschuss is not null,$vorschuss.sumvorschuss,0) as vorschuss,";
$sql.= " if($transport.transport is not null,$transport.transport,0) as transport,";
$sql.= " if($essen.essen is not null,$essen.essen,0) as essen,";
$sql.= " if($abmahnung.abmahnung is not null,$abmahnung.abmahnung,0) as abmahnung,";
$sql.= " if($sonstpremie.sonstpremie is not null,$sonstpremie.sonstpremie,0) as sonstpremie,";
//pokud je pocet nocnich hodin vetsi nez 4 odectu pul hodiny pauzu
//$sql.= " if($nachtzuschlag.nacht is not null,if($nachtzuschlag.nacht>4,$nachtzuschlag.nacht-0.5,$nachtzuschlag.nacht),0) as nachtstd,";
//$sql.= " if($nachtzuschlag.nacht is not null,$nachtzuschlag.nacht,0) as nachtstd,";
$sql.= " if($nachtzuschlag.sonestd is not null,$nachtzuschlag.sonestd,0) as sonestd,";
//$sql.= " if($risiko.risiko_zuschlag is not null,$risiko.risiko_zuschlag,0) as risiko,";
//$sql.= " $leistung.og,";
//$sql.= " if(dperslohnfaktor.faktor is null,1,dperslohnfaktor.faktor/60) as og_personalfaktor,";
$sql.= " $anwesenheit.perslohnfaktor,";
$sql.= " $anwesenheit.leistfaktor";
//$sql.= " $leistung.vzaby,";
//$sql.= " $leistung.vzaby_akkord,";
//
//$sql.= " $leistung.vzaby_kc,";
//$sql.= " $leistung.vzaby_akkord_kc,";
//$sql.= " $leistung.qpraemie_kc,";
//$sql.= " $leistung.qpraemie_akkord_kc,";
//$sql.= " $leistung.qpraemie_zeit_min,";
//$sql.= " $leistung.qpraemie_zeit_kc";
$sql.= " ,if($dpp.eintritt is not null,DATE_FORMAT($dpp.eintritt,'%y-%m-%d'),'') as dpp_von";
$sql.= " ,if($dpp.befristet is not null,DATE_FORMAT($dpp.befristet,'%y-%m-%d'),'') as dpp_bis";

// mzda v korunach
//$sql.=" if(if(dperslohnfaktor.faktor is null,1,dperslohnfaktor.faktor/60)<>1,vzaby*if(dperslohnfaktor.faktor is null,1,dperslohnfaktor.faktor/60),vzaby_kc) as lohn_kc,";
//$sql.=" if(if(dperslohnfaktor.faktor is null,1,dperslohnfaktor.faktor/60)<>1,vzaby_akkord*if(dperslohnfaktor.faktor is null,1,dperslohnfaktor.faktor/60),vzaby_akkord_kc) as lohn_akkord_kc";

$sql.= " from $anwesenheit";
//$sql.= " left join $leistung on $leistung.persnr=$anwesenheit.persnr";
$sql.= " left join $vorschuss on $vorschuss.persnr=$anwesenheit.persnr";
$sql.= " left join $transport on $transport.persnr=$anwesenheit.persnr";
$sql.= " left join $essen on $essen.persnr=$anwesenheit.persnr";
$sql.= " left join $abmahnung on $abmahnung.persnr=$anwesenheit.persnr";
$sql.= " left join $sonstpremie on $sonstpremie.persnr=$anwesenheit.persnr";
$sql.= " left join $nachtzuschlag on $nachtzuschlag.persnr=$anwesenheit.persnr";
//$sql.= " left join $risiko on $risiko.persnr=$anwesenheit.persnr";
$sql.= " left join $dpp on $dpp.persnr=$anwesenheit.persnr";

//$sql.=" left join dperslohnfaktor on dperslohnfaktor.persnr=$anwesenheit.persnr and dperslohnfaktor.og=$leistung.og";

$sql.= " order by $anwesenheit.persnr ";

//echo $sql;
//exit;
$query2xml = XML_Query2XML::factory($db);
	
function urlaubgenom($record)
{
        global $aplDB;
	return $aplDB->getUrlaubtageGenommenBis($record['persnr'], $record['datumbis']);
}

function offen($record){
    $gen = urlaubgenom($record);
    // 2010-01-05 gekrzt se bude zadavat s opacnym znamenkem, tedy kratim dovolenou o 3 dny - zadam -3
    $offen = $record['jahranspruch']+$record['rest']+$record['gekrzt']-$gen;
    return $offen;
}


$options = array(
		'encoder'=>false,
		'rootTag'=>'S142',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'vollname',
                    'eintritt',
                    'austritt',
                    'geboren',
                    'dobaurcita',
                    'regeloe',
                    'zkusebni_doba_dobaurcita',
                    'perslohnfaktor',
                    'leistfaktor',
                    'sumstunden',
                    'sumstundena',
                    'sumstundena_akkord',
                    'erschwerniss',
//                    'risiko',
                    'essen',
                    'sonstpremie',
                    'abmahnung',
                    'vorschuss',
                    'transport',
                    'premie_za_vykon',
                    'premie_za_kvalitu',
                    'qpremie_akkord',
                    'qpremie_zeit',
                    'premie_za_prasnost',
                    'premie_za_3_mesice',
                    'MAStunden',
                    'exekution',
                    'nachtstd',
                    'sonestd',
                    'tage_d',
                    'tage_p',
                    'tage_z',
                    'tage_nv',
                    'tage_n',
                    'tage_np',
                    'tage_frage',
//                    'nacht_stunden',
                    'tage_u',
                    'tage_nw',
                    'tage_nu',
                    'jahranspruch',
                    'rest',
                    'gekrzt',
		    'dpp_von',
		    'dpp_bis',
                    'genom'=>'#urlaubgenom();',
                    'offen'=>'#offen();',
//                    'ogs'=>array(
//                        'rootTag'=>'ogs',
//                        'idColumn'=>'og',
//                        'rowTag'=>'og',
//                        'elements'=>array(
//                            'ognr'=>'og',
////                            'og_personalfaktor',
//                            'vzaby',
//                            'vzaby_akkord',
//                            'vzaby_kc',
//                            'vzaby_akkord_kc',
//                            'qpraemie_kc',
//                            'qpraemie_akkord_kc',
//                            'qpraemie_zeit_kc',
//                            'qpraemie_zeit_min',
//                        ),
//                    )
                  ),
);
					

// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);
//$domxml->encoding="windows-1250";

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

// v promenne p bych mel mit seznam parametru, pridam ho do XML jako node do domxml
//

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


//header('Content-Type: application/xml');
//echo $proc->transformToXML($domxml);



// smazu pouzite pohledy
for($i=0;$i<sizeof($views);$i++)
{
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


//$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S142.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
