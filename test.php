<?php
session_start();
//require './fns_dotazy.php';
require './db.php';

$apl = AplDB::getInstance();

// vytvoreni kalendare a vlozeni do DB
//
//$startDatum = '2013-01-04';
//
//$stampStart = mktime(2,2,2, 1, 4, 2013);
//$stampAktual = $stampStart;
//
//for($den=0;$den<=700;$den++){
//    $dateAktual = date('Y-m-d', $stampAktual);
//    $cisloDne = date('N', $stampAktual);
//    echo "<br>$dateAktual, cislo dne : $cisloDne";
//    $stampAktual += 60*60*24;
//    $sql = "insert into calendar (datum,cislodne) values('$dateAktual',$cisloDne)";
//    $apl->query($sql);
//}

//$datumDB = '2012-08-16';
//$persnr = 413;
//
//$persnrRows = $apl->getPersnrRowsFromDrueckDatum($datumDB);
//
//$persnrRows = array(1477);
//if ($persnrRows !== NULL) {
//
////    foreach ($persnrRows as $persnrRow) {
//        foreach ($persnrRows as $persnr) {
////        $persnr = $persnrRow['persnr'];
//        $rows = $apl->getLeistungAnwesenheitRows($datumDB, $persnr);
//
//        echo "<h1>Leistung=>Anwesenheit  $persnr ($datumDB)</h1>";
//
//        if ($rows !== NULL) {
//            echo "<h1>Leistung $persnr ($datumDB)</h1>";
//            echo "<table style='border: solid 1px black; border-collapse: collapse;'>";
//            $style='';
//            $oeold='';
//            foreach ($rows as $row) {
//                $oe = $row['oe'];
//                $von = $row['von'];
//                $bis = $row['bis'];
//                $datum = $row['datum'];
//                $pause = $row['sumpause'];
//                if($oe!=$oeold){
//                    $oeold=$oe;
//                    $style='background-color:#ccffcc;';
//                }
//                else
//                    $style='';
//                
//                echo "<tr style='$style'>";
//                echo "<td style='border: solid 1px black;padding: 5px;'>$oe</td><td style='padding: 5px;border: solid 1px black;'>$von</td><td style='padding: 5px;border: solid 1px black;'>$bis</td><td style='padding: 5px;border: solid 1px black;'>$datum</td><td style='padding: 5px;border: solid 1px black;'>$pause</td>";
//                echo "</tr>";
//            }
//            echo "</table>";
//
//
//            echo "<table style='border: solid 1px black; border-collapse: collapse;'>";
//            $oeold = '';
//            $oeold = $rows[0]['oe'];
//            $bisP = $rows[0]['bis'];
//            $vonP = $rows[0]['von'];
//            $datum = $rows[0]['datum'];
//
//            if (count($rows) == 1) {
//                echo "<tr>";
//                echo "<td style='border: solid 1px black;padding: 5px;'>$oeold</td><td style='border: solid 1px black;padding: 5px;'>$vonP</td><td style='border: solid 1px black;padding: 5px;'>$bisP</td><td style='border: solid 1px black;padding: 5px;'>$datum</td>";
//                echo "</tr>";
////                echo "<br>OE=$oeold,von=$vonP,bis=$bisP,datum=$datum";
//            } else {
//                foreach ($rows as $row) {
//                    $oe = $row['oe'];
//                    $von = $row['von'];
//                    $bis = $row['bis'];
//                    $datum = $row['datum'];
//                    if ($oe != $oeold) {
//                        echo "<tr>";
//                        echo "<td style='border: solid 1px black;padding: 5px;'>$oeold</td><td style='border: solid 1px black;padding: 5px;'>$vonP</td><td style='border: solid 1px black;padding: 5px;'>$bisP</td><td style='border: solid 1px black;padding: 5px;'>$datum</td>";
//                        echo "</tr>";
//                        $vonP = $von;
//                        $oeold = $oe;
//                        // posledni
//                        $bisP = $bis;
//                    } else {
//                        $bisP = $bis;
//                    }
//                }
//                echo "<tr>";
//                echo "<td style='border: solid 1px black;padding: 5px;'>$oeold</td><td style='border: solid 1px black;padding: 5px;'>$vonP</td><td style='border: solid 1px black;padding: 5px;'>$bis</td><td style='border: solid 1px black;padding: 5px;'>$datum</td>";
//                echo "</tr>";
//
////                echo "<br>OE=$oeold,von=$vonP,bis=$bis,datum=$datum";
//            }
//            echo "</table>";
//        }
//        // pridat info z dochazkoveho systemu
//        $edataRows = $apl->getAnwesenheitFromEdata($datumDB,$persnr);
//        if($edataRows!==NULL){
//            echo "<table style='border: solid 1px black; border-collapse: collapse;'>";            
//            foreach($edataRows as $edataRow){
//                $von = $edataRow['von'];
//                $bis = $edataRow['bis'];
//                echo "<tr>";
//                echo "<td style='border: solid 1px black;padding: 5px;'>Anwesenheit aus EData Reader</td>";
//                echo "<td style='border: solid 1px black;padding: 5px;'>$von</td><td style='border: solid 1px black;padding: 5px;'>$bis</td>";
//                echo "</tr>";
//            }
//            echo "</table>";
//        }
//        echo "<hr>";
//    }
//}

// pridat operaci do pracovniho planu
//$sql = "select dpos.teil from dpos";
//$sql.=" join dkopf on dkopf.Teil=dpos.Teil";
//$sql.=" where dkopf.Kunde=355 and dpos.`TaetNr-Aby`=446";
//
//$cisloOperace = 2546;
//
//$rows = $apl->getQueryRows($sql);
//foreach ($rows as $row){
//    echo "<br>".$row['teil'];
//    $sqlInsert = "insert into dpos (teil,`TaetNr-Aby`) values('".$row['teil']."',2546)";
//    echo "<br>$sqlInsert";
//    $apl->getQueryRows($sqlInsert);
//}

// zmena cisel operaci, omezeni jen na zakaznika
//$kunde = 355;
//$von = 2030;
//$nach = 2317;
//
//$vonNachArray = array(
//    2010=>2310,
//    2030=>2327,
//    2046=>2346,
//    2049=>2349,
//    2051=>2351,
//    2202=>2302,
//    2212=>2312,
//    2246=>2346,
//    2249=>2349,
//    2251=>2351,
//    4010=>4310,
//    4035=>4317,
//    4046=>4346,
//    4049=>4349,
//    4051=>4351,
//    6402=>6302,
//    6412=>6312,
//    6430=>6327,
//    6441=>6346,
//    6449=>6349,
//    6451=>6351
//);
//
//
//foreach ($vonNachArray as $von=>$nach){
//
//echo "<br>von:$von nach:$nach";
//// zucastnene tabulky
//// dauftr.abgnr
//$sql = " update dauftr";
//$sql.= " join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
//$sql.=" set abgnr=$nach";
//$sql.= " where dauftr.abgnr=$von and daufkopf.kunde=$kunde";
//echo "<br>$sql";
//// dlagerbew.abgnr
//$sql = " update dlagerbew";
//$sql.= " join daufkopf on daufkopf.auftragsnr=dlagerbew.auftrag_import";
//$sql.=" set abgnr=$nach";
//$sql.= " where dlagerbew.abgnr=$von and daufkopf.kunde=$kunde";
//echo "<br>$sql";
//// dpos.TaetNr-Aby
//$sql = " update dpos";
//$sql.= " join dkopf on dkopf.teil=dpos.Teil";
//$sql.=" set `TaetNr-Aby`=$nach";
//$sql.= " where dpos.`TaetNr-Aby`=$von and dkopf.kunde=$kunde";
//echo "<br>$sql";
//// dposbedarflager.abgnr
//
//// drech.abgnr
//$sql = " update drech";
//$sql.= " join daufkopf on daufkopf.auftragsnr=drech.AuftragsNr";
//$sql.=" set drech.abgnr=$nach";
//$sql.= " where drech.abgnr=$von and daufkopf.kunde=$kunde";
//echo "<br>$sql";
//// drechbew.abgnr
//$sql = " update drechbew";
//$sql.= " join daufkopf on daufkopf.auftragsnr=drechbew.AuftragsNr";
//$sql.=" set drechbew.abgnr=$nach";
//$sql.= " where drechbew.abgnr=$von and daufkopf.kunde=$kunde";
//echo "<br>$sql";
//// drechneu.abgnr
//$sql = " update drechneu";
//$sql.= " join daufkopf on daufkopf.auftragsnr=drechneu.origauftrag";
//$sql.=" set drechneu.abgnr=$nach";
//$sql.= " where drechneu.abgnr=$von and daufkopf.kunde=$kunde";
//echo "<br>$sql";
//// drueck.TaetNr
//$sql = " update drueck";
//$sql.= " join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
//$sql.=" set drueck.TaetNr=$nach";
//$sql.= " where drueck.TaetNr=$von and daufkopf.kunde=$kunde";
//echo "<br>$sql";
//}

//$persnrArray = $apl->getPersnrFromEintritt('2011-01-01',TRUE);
//
//foreach ($persnrArray as $persnr){
//    echo "<br>".$persnr;
//}
// doplnim vsem lidem radek do vertrag archivu

//$lr = $apl->hatMARechnung(107417);
//
//echo $lr;

//$models = array('rb20110216');
//
//$sql= "select";
//$sql.= "    daufkopf.kunde,";
//$sql.= "    drueck.`Teil`,";
//$sql.= "    dkopf.`Gew` as teil_gewicht,";
//$sql.= "    drueck.`TaetNr`,";
//$sql.= "    `dtaetkz-abg`.dtaetkz,";
//$sql.= "    `dtaetkz-abg`.vzaby_schw_grad,";
//$sql.= "    drueck.`VZ-SOLL` as vzkd_pro_stk,";
//$sql.= "    drueck.`VZ-IST` as vzaby_pro_stk,";
//$sql.= "    sum(drueck.`Stück`) as sum_stk,";
//$sql.= "    sum(drueck.`Verb-Zeit`) as sum_verb";
//$sql.= " from drueck";
//$sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.`TaetNr`";
//$sql.= " join daufkopf on daufkopf.auftragsnr=drueck.`AuftragsNr`";
//$sql.= " join dkopf on dkopf.`Teil`=drueck.`Teil`";
//$sql.= " where";
//$sql.= "    drueck.datum between '2011-01-01' and '2011-02-20'";
//$sql.= "    and `dtaetkz-abg`.`Stat_Nr`='S0061'";
////jen pro regulerni operace nikoliv pro vicepace zakaznicke, abydos .....
//$sql.= "    and drueck.`TaetNr`<2000";
//$sql.= " group by";
//$sql.= "    daufkopf.kunde,";
//$sql.= "    drueck.`Teil`,";
//$sql.= "    drueck.`TaetNr`";
//$sql.= " order by";
//$sql.= "    daufkopf.kunde,";
//$sql.= "    `dtaetkz-abg`.dtaetkz,";
//$sql.= "    dkopf.`Gew`";
//
//
//$koef_AB = array();
//
//
//function compute_AB_coef($coordArray){
//    $coef_AB = array();
//    $intervalCount = sizeof($coordArray)-1;
//
//    for($interval=0;$interval<$intervalCount;$interval++){
//    if(($coordArray[$interval][0] - $coordArray[$interval+1][0])!=0)
//        $a = ($coordArray[$interval][1] - $coordArray[$interval+1][1]) / ($coordArray[$interval][0] - $coordArray[$interval+1][0]);
//    else{
//        return NULL;
//    }
//    $b = $coordArray[$interval][1] - $a * $coordArray[$interval][0];
//    array_push($coef_AB, array('a'=>$a,'b'=>$b));
//    }
//    return $coef_AB;
//}
//
//function linearize($x,$intervalArray,$coefArray){
//    if($x<$intervalArray[0]) return 0;
//    $interval = 0;
//    for ($i=1;$i<sizeof($intervalArray);$i++){
//        $hodnotaHranice = $intervalArray[$i];
//        if($x<=$hodnotaHranice) {
//            return ($coefArray[$interval]['a']*$x+$coefArray[$interval]['b']);
//        }
//        $interval++;
//    }
//    return 0;
//}
//
//foreach ($models as $model) {
//    echo "<hr>Model: $model<br>";
//
//    $model_XY = $a->getGrundModelArray($model);
//    $intervalCount = sizeof($model_XY)-1;
//
////    echo "<br>Souradnice :<br>";
//    foreach ($model_XY as $souradnice) {
//        $vystup = sprintf("Gew [kg] =%.4f,VzAby [min] =%10.4f", $souradnice[0], $souradnice[1]);
//        echo "<br>$vystup";
//    }
//
//    echo "<br>";
//    $coef = compute_AB_coef($model_XY);
//
//    if ($coef === NULL) {
//        echo "<br>nemohl jsem spocitat koeficienty ";
//    } else {
//        for ($i = 0; $i < $intervalCount; $i++) {
//            echo "<br>Gewicht = < " . $model_XY[$i][0] . ' , ' . $model_XY[$i + 1][0] . " >   => : VzAby=" . $coef[$i]['a'] . "*Gewicht + " . $coef[$i]['b'];
//        }
//    }
//
//    echo "<br>";
//
//    $hranice = array();
//    foreach ($model_XY as $souradnice)
//        array_push($hranice, $souradnice[0]);


// na zkousku tabulku s 10 vypoctenyma hodnotama
//    echo "<br>Testtabelle";
//    $od = 0;//$hranice[0];
//    $do = 500;//$hranice[sizeof($hranice) - 1];
//    $krok = ($do - $od) / 100;
//
//    $od -= $krok;
//    $do += $krok;
//
//    $x = $od;
//
//    while ($x <= $do) {
//        $vystup = sprintf("%s;%s", number_format($x, 4,',',''), number_format(linearize($x, $hranice, $coef),4,',',''));
//        echo "<br>$vystup";
//        $x+=$krok;
//    }
//}

//$model_asc = $a->getGrundModelArray('asc');
//$model_rb1 = $a->getGrundModelArray('rb1');
//$model_rb2 = $a->getGrundModelArray('rb2');
//$model_rb2 = $a->getGrundModelArray('rb20110216');

//$coef_asc = compute_AB_coef($model_asc);
//$coef_rb1 = compute_AB_coef($model_rb1);
//$coef_rb2 = compute_AB_coef($model_rb2);
//$coef_rb2 = compute_AB_coef($model_rb2);

//$hranice_asc = array();
//$model_XY = $model_asc;
//foreach ($model_XY as $souradnice)
// array_push($hranice_asc, $souradnice[0]);
//
//$hranice_rb1 = array();
//$model_XY = $model_rb1;
//foreach ($model_XY as $souradnice)
// array_push($hranice_rb1, $souradnice[0]);

//$hranice_rb2 = array();
//$model_XY = $model_rb2;
//foreach ($model_XY as $souradnice)
// array_push($hranice_rb2, $souradnice[0]);
//
//
//
//$rows = $a->getQueryRows($sql);
//if($rows!==NULL){
//
//
//    echo "kunde;teil;teil_gewicht;taetnr;tatkz;schw_grad;vzkd_stk;vzaby_stk;sum_stk;sum_verb;vzaby_rb20110216<br>";
//    foreach ($rows as $row){
////        $vzaby_asc = 0;
////        $vzaby_rb1 = 0;
//        $vzaby_rb2 = 0;
//
//        foreach ($row as $fieldNAme=>$fieldValue){
//            if($fieldNAme=='teil_gewicht'){
////                $vzaby_asc = linearize(floatval($fieldValue), $hranice_asc, $coef_asc) * floatval($row['vzaby_schw_grad']);
////                $vzaby_rb1 = linearize(floatval($fieldValue), $hranice_rb1, $coef_rb1) * floatval($row['vzaby_schw_grad']);
//                $vzaby_rb2 = linearize(floatval($fieldValue), $hranice_rb2, $coef_rb2) * floatval($row['vzaby_schw_grad']);
//            }
//            echo strtr($fieldValue, '.', ',').";";
//        }
//        // vyprat vypoctene hodnoty vzaby pro asc, rb1,rb2
//        echo number_format($vzaby_rb2,4,',','');
//        echo "<br>";
//    }
//}
//else{
//    echo "<br>zadne zaznamy";
//}
//require "./fns_dotazy.php";
//require './db.php';
//
//$apl = AplDB::getInstance();

//$persnrArray = array(44017);
//$persnrArray = $apl->getPersnrFromEintritt('1990-01-01',TRUE);
//$jahr = 2011;
//foreach ($persnrArray as $persnr){
//    echo "<br>PersNr:$persnr";
//    $pmStundenE = $apl->getPlusMinusStunden(12, 2011, $persnr, '2010-12-31');
//    $pmStunden = $apl->getPlusMinusStunden(12, 2011, $persnr);
//    echo "<br>+-Stunden zu 31.12.2011:$pmStundenE, +-StundenGesamt zu 31.12.2011:$pmStunden";
//}
//$persnrArray = $apl->getPersnrFromEintritt('1990-01-01',TRUE);
//$persnrArray = array(104);
//
//    $qArray = $apl->getQualifikationenProQTyp(NULL);
//    foreach ($qArray as $q){
//        echo "<br>qualifid = ".$q['id'];
//    }
//foreach($persnrArray as $persnr){
//   $eintritt = substr($apl->getEintrittsDatumDB($persnr),0,10);
//   $nameArray = $apl->getNameVorname($persnr);
//   if($nameArray!==NULL)
//       $name = $nameArray['name'].' '.$nameArray['vorname'];
//   else
//       $name = '';
//   $plusminusStunden2011 = number_format($apl->getPlusMinusStunden(12, 2011, $persnr),1,'.','');
//   $arbstunden2012 = number_format($apl->getArbStundenBetweenDatums($persnr,'2012-01-01','2012-12-31'),1,'.','');
//   $plusminusStunden2012 = number_format($apl->getPlusMinusStunden(12, 2012, $persnr),1,'.','');
//   $arbstunden2013 = number_format($apl->getArbStundenBetweenDatums($persnr,'2013-01-01','2013-12-31'),1,'.','');
//   $plusminusStunden2013 = number_format($apl->getPlusMinusStunden(12, 2013, $persnr),1,'.','');
////   $plusminusStunden3 = number_format($apl->getPlusMinusStunden(7, 2011, $persnr),1,',',' ');
//   echo "$persnr,$eintritt,$name,$plusminusStunden2011,$arbstunden2012,$plusminusStunden2012,$arbstunden2013,$plusminusStunden2013<br>";
////    $apl->addQualifikationen($persnr, $qArray, 0, 0);
//}
//foreach ($persnrArray as $persnr) {
//
//    echo "<hr>PersNr: $persnr";
//    echo "<table border='1'>";
//    echo "<tr>";
//    echo "<th>Jahr</th>";
//    echo "<th>Monat</th>";
//    $pmStundenArray = $apl->getPlusMinusStundenVerbose(1, 2010, 104);
//    foreach ($pmStundenArray as $key => $value) {
//        echo "<th>$key</th>";
//    }
//    echo "</tr>";
//    for ($monat = 1; $monat <= 12; $monat++) {
//        echo "<tr>";
//        echo "<td>$jahr</td><td align='right' >$monat</td>";
//        $pmStundenArray = $apl->getPlusMinusStundenVerbose($monat, $jahr, $persnr);
//        if (is_array($pmStundenArray)) {
//            foreach ($pmStundenArray as $value) {
//                echo "<td align='right' width='80px'>" . $value . "</td>";
//            }
//        }
//        echo "</tr>";
//    }
//    echo "</table>";
//}
//$noExPalArray = $apl->getNoExRows('06017272');
//echo "<pre>";
//print_r($noExPalArray);
//echo "</pre>";
//
//$abgnrInfoArrayForAbgNrUndTeil = $apl->getAbgNrInfoArrayForTeil('06017272',2053);
//echo "<pre>";
//print_r($abgnrInfoArrayForAbgNrUndTeil);
//echo "</pre>";
//
//foreach ($noExPalArray as $noExPal){
//    $ar=$apl->insertDauftrRow($noExPal['auftragsnr'], '06017272', $abgnrInfoArrayForAbgNrUndTeil['preis'], $noExPal['stk'], $noExPal['termin'], $abgnrInfoArrayForAbgNrUndTeil['tat'], $noExPal['pos-pal-nr'], 2053,$abgnrInfoArrayForAbgNrUndTeil['vzkd'], $abgnrInfoArrayForAbgNrUndTeil['vzaby'], 'PHP-Addprogram');
//    echo "<br>affected Rows = $ar";
//}
//$teilArray = array(
//    '3024514712'=>'3024510427',
//    '3024510401'=>'3024510421',
//    '302451040712'=>'3024510427',
//    '3034510409'=>'3034510429',
//    '3064510404'=>'3064510424',
//    '3064510410'=>'3064510430',
//    '3064512200'=>'3064512220',
//    '4223570123'=>'4223570143',
//    '4223570125'=>'4223570145',
//    '8263570100'=>'8263570184',
//    '8263570101'=>'8263570185',
//    '8263570147'=>'8263570186',
//    '8263570149'=>'8263570187',
//    '8263570163'=>'8263570188',
//    '8263570165'=>'8263570189',
//    '16024510413'=>'16024510433',
//    '16114510406'=>'16114510426',
//);

//        $datum = '31.10.2010';
//        echo "<br>$datum<br>";
//
//        $datumRoz = explode(".",$datum); // Roz�e�eme datum na jednotliv� �daje
//        $datumOriginal = $datum;
//
//        print_r($datumRoz);
//
//        $datum = $datumRoz[2]."-".$datumRoz[1]."-".$datumRoz[0]; // Op�t ho spoj�me
//
//        $von = '00:01';
//        $bis = '05:45';
//
//        $vonHod = intval(substr($von,0,2)); // roz�e�eme p��chod na �daje
//        echo "<br>vonHod=$vonHod";
//        $vonMin = intval(substr($von,3,2)); // roz�e�eme p��chod na �daje
//        echo "<br>vonMin=$vonMin";
//
//        $vonOriginal = $von;
//        $bisOriginal = $bis;
//
//        $bisHod = intval(substr($bis,0,2)); // roz�e�eme odchod na �daje
//        echo "<br>bisHod=$bisHod";
//        $bisMin = intval(substr($bis,3,2)); // roz�e�eme odchod na �daje
//        echo "<br>bisMin=$bisMin";
//
//        //mktime($hour, $minute, $second, $month, $day, $year, $is_dst)
//        $vonStamp = mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2],0);
//        $bisStamp = mktime($bisHod, $bisMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2],0);
//        $von = date("y-m-d H:i:s",$vonStamp); // sestav�me nov� p��chod i s datumem
//        $bis = date("y-m-d H:i:s",$bisStamp); // sestav�me nov� odchod i s datumem
//
//        echo "<br>vonStamp=$vonStamp";
//        echo "<br>bisStamp=$bisStamp";
//
//        echo "<br>von=$von";
//        echo "<br>bis=$bis";
//
//        $stunden = 1;
//        $bPrepocitat = TRUE;
//        $pause1 = 0.5;
//
//        if($stunden!=0 && $bPrepocitat){
//            // jeste jednou si prepocitam stunden
//            $rozdil = $bisStamp - $vonStamp;
//            $hodin = ($bisStamp - $vonStamp) / 60 / 60;
//            $stunden = round($hodin,2);
//        }
//
//        echo "<br>hodin=$hodin";
//        echo "<br>stunden=$stunden";
//
//        $stundenNetto = $stunden - $pause1-$pause2;
//
//        echo "<br>rozdil=$rozdil";
//        echo "<br>stundenNetto=$stundenNetto";

//$apl = AplDB::getInstance();
//$insertArray = array();
//$rowsArray = $apl->getDrueckRowsFor('4108403358',40);
////var_dump($rowsArray);
//foreach ($rowsArray as $key => $row) {
//    foreach ($row as $field => $value) {
////        echo "$field = $value, ";
//        $insertRow[$field]=$value;
//    }
//    $insertRow['TaetNr']=2030;
//    $insertRow['VZ-SOLL']=2.5;
//    $insertRow['VZ-IST']=2.5;
//    $insertRow['Verb-Zeit']=0;
//    $insertRow['verb-pause']=0;
//    $insertRow['marke-aufteilung']='';
//    $insertRow['comp_user_accessuser']='PHP_jr_Program';
//
//    $insertSql = "insert into drueck (auftragsnr,teil,taetnr,`Stück`,`Auss-Stück`,`vz-soll`,`vz-ist`,`verb-zeit`,persnr,datum,`pos-pal-nr`,`auss-art`,`verb-von`,`verb-bis`,`verb-pause`,`marke-aufteilung`,schicht,oe,auss_typ,comp_user_accessuser,insert_stamp,kzGut)";
//    $insertSql.="values("
//                            .$insertRow['AuftragsNr'].
//                            ",'"
//                            .$insertRow['Teil'].
//                            "',"
//                            .$insertRow['TaetNr'].
//                            ","
//                            .$insertRow['Stück'].
//                            ","
//                            .$insertRow['Auss-Stück'].
//                            ","
//                            .$insertRow['VZ-SOLL'].
//                            ","
//                            .$insertRow['VZ-IST'].
//                            ","
//                            .$insertRow['Verb-Zeit'].
//                            ","
//                            .$insertRow['PersNr'].
//                            ",'"
//                            .$insertRow['Datum'].
//                            "',"
//                            .$insertRow['pos-pal-nr'].
//                            ","
//                            .$insertRow['auss-art'].
//                            ",'"
//                            .$insertRow['verb-von'].
//                            "','"
//                            .$insertRow['verb-bis'].
//                            "',"
//                            .$insertRow['verb-pause'].
//                            ",'"
//                            .$insertRow['marke-aufteilung'].
//                            "',"
//                            .$insertRow['schicht'].
//                            ",'"
//                            .$insertRow['oe'].
//                            "',"
//                            .$insertRow['auss_typ'].
//                            ",'"
//                            .$insertRow['comp_user_accessuser'].
//                            "','"
//                            .$insertRow['insert_stamp'].
//                            "','"
//                            .$insertRow['kzGut'].
//                "');";
//
//    // ring breaker for ringer on Palm Treo 75005554999
//
//    array_push($insertArray, $insertSql);
////    echo "<br>";
//}
//
//foreach ($insertArray as $insertRow){
//    echo "<br>".$insertRow;
//}

//foreach ($teilArray as $teilOld=>$teilNew){
//    $apl->teilNrAendern($teilOld, $teilNew);
//}
//dbConnect();
//$sql = "select id,teil,auftrag_import,pal_import,lager_von,lager_nach,date_stamp,comp_user_accessuser,abgnr from dlagerbew where auftrag_import between 111000 and 111999 and (teil='05063966' or teil='05103963' or teil='05203964' or teil='06017272' or teil='06131865' or teil='06191864') and date_stamp>='2010-03-29 07:00:00' and lager_nach='XX' order by auftrag_import,pal_import";
////mysql_query('use apl_backup');
//
//
//
//$result = mysql_query($sql);
//$insertArray = array();
//while($row = mysql_fetch_assoc($result)){
//    $sql_insert = "insert into dlagerbew (id,teil,auftrag_import,export,pal_import,behaelter,gut_stk,auss_stk,lager_von,lager_nach,date_stamp,comp_user_accessuser,abgnr)";
//    $sql_insert.= " values(".$row['id'].",'".$row['teil']."',".$row['auftrag_import'].",".$row['export'].",".$row['pal_import'].",'".$row['behaelter']."',".$row['gut_stk'].",".$row['auss_stk'].",'".$row['lager_von']."','".$row['lager_nach']."','".$row['date_stamp']."','".$row['comp_user_accessuser']."',".$row['abgnr'].")";
//    array_push($insertArray, $sql_insert);
//    echo "<br>".$row['id'].",'".$row['teil']."',".$row['auftrag_import'].",".$row['pal_import']."',".$row['gut_stk'].",".$row['auss_stk'].",'".$row['lager_von']."','".$row['lager_nach']."','".$row['date_stamp']."','".$row['comp_user_accessuser']."',".$row['abgnr'].")";
//}

//echo "<br>pocet radku k vlozeni:".count($insertArray).".";
//mysql_query('use apl');
//$vlozeno = 0;
//foreach ($insertArray as $sqlinsert){
//    mysql_query($sqlinsert);
//    $vlozeno += mysql_affected_rows();
//}
//
//echo "<br>celkem vlozeno $vlozeno radku.";
//$reporty = array(
//    array("D740",111201,111204),
//    array("D740",111301,111345),
////    array("D740",111759,111761),
//    array("D740",122227,122231),
//    array("D763",461173,461217),
//    array("D741",194067,194093),
//);
//
//foreach($reporty as $report){
//$reportName = $report[0];
//$von = intval($report[1]);
//$bis = intval($report[2]);
//for($auftragsnr=$von;$auftragsnr<=$bis;$auftragsnr++){
//    $url = "Reports/".$reportName."_pdf.php?auftragsnr_label=Rechnung Nr.&auftragsnr=$auftragsnr";
////    $url = urlencode($url);
//    echo "<a href='$url'>".$auftragsnr."</a><br>";
    
//    header("Location: $url");
//    sleep(5);
//}
//}
//$aplDB = AplDB::getInstance();
//$ut = $aplDB->getNotInATageInArbeitCountBetweenDatums('2009-09-01', '2009-09-30', 104);
//echo $ut;
//dbConnect();
//$a = AplDB::getInstance();

// vytahnu si nejake radky z druecku
//$sql = "select drueck.drueck_id,drueck.`AuftragsNr` as auftragsnr, drueck.`pos-pal-nr` as pal,drueck.`TaetNr` as abgnr,drueck.`PersNr` as persnr,drueck.`verb-von` as von from drueck where drueck.datum>='2009-11-01'";
//$result = mysql_query($sql);
//echo "<table border='1'>";
//while($row = mysql_fetch_assoc($result)){
//    echo "<tr>";
//
//        echo "<td>".$row['auftragsnr'].'</td>';
//        echo "<td>".$row['abgnr'].'</td>';
//        echo "<td>".$row['pal'].'</td>';
//        echo "<td>".$row['persnr'].'</td>';
//        echo "<td>".$row['von'].'</td>';
        // vypreparovat hodnotu von
//        $von = substr($row['von'], 11, 2).substr($row['von'], 14, 2);
//        echo "<td>".$von.'</td>';
//        $v = $a->getSuggestedOE($row['auftragsnr'], $row['abgnr'], $row['persnr'], $von);
//        $a->setOEForDrueckID($row['drueck_id'],$v['OE']);
//        if(is_array($v)){
//            echo '<td>';
//            echo $row['drueck_id']." ".$v['OE'].":";
//            foreach ($v as $key=>$hodnota){
//                echo $key.':'.$hodnota." ";
//            }
//            echo '</td>';
//        }
//        else
//            echo "<td>".$v.'</td>';
//    echo "</tr>";
//}
//echo "</table>";
//dbConnect();
//
//
////echo $apldb->getVerbMinuten("07:30", "08:10")."<br>";
//$vystup=AplDB::getInstance()->insertAnwesenheit(104,18,'4.8.2009','06:00','14:00',0,0,8,'IT');
////$vystup = array("neco"=>1231,"necojinyho"=>423423);
//print_r($vystup);
//
//echo "<br>hasanwesenheit:".$apldb->hasAnwesenheit(104,'n','6.2.2009');

//mail("jr@abydos.cz","predmet - test","telo zpravy");

//$dil = '05103963';

//$oestatus = AplDB::getInstance()->getOEStatusForOE("IT");
//echo "oestatus = $oestatus";
//$knd = 111;
//$teilArray = AplDB::getInstance()->getActiveTeilArrayForKunde($knd,180);
//foreach ($teilArray as $klic => $hodnota) {
//    echo "klic = $klic, hodnota = $hodnota<br>";
//}
//
//echo "<hr>";
////$dil = 45;
//foreach ($teilArray as $dil=>$hodnota){
//    $row = AplDB::getInstance()->getLagerBestandForTeil($dil, '2009-04-22 07:11:48');
//    if(!is_array($row))
//        echo "nemuzu zjistit stav pro dil $dil ($row)";
//    else
//        print_r($row);
//    echo "<hr>";
//}
//$row = AplDB::getInstance()->getLagerBestandForTeil($dil, '2009-04-22 07:11:48');


//$row = AplDB::getInstance()->getInventurStandForTeil('05103963');
//if($row==null)
//    echo "nemuzu zjistit stav skladu pro dil $dil";
//else
//    print_r($row);
//exit;
/*
$import=117299;
$pal=10;
$drueck_id=1345670;
$id_dauftr=477914;

	$dauftrRow = getDauftrRowFromId($id_dauftr);
	$pocetPozicDauftrArray = getPalArrayFromDauftrAuftragsnrTeilAbgnr($dauftrRow['auftragsnr'],$dauftrRow['teil'],$dauftrRow['abgnr']);
	
	echo "dauftrarray";
		print_r($pocetPozicDauftrArray);
	echo "<br>";
	
	$pocetPozicDrueckArray = getPalArrayFromDrueckAuftragsnrTeilAbgnr($dauftrRow['auftragsnr'],$dauftrRow['teil'],$dauftrRow['abgnr']);
	echo "dauftrarray";
		print_r($pocetPozicDrueckArray);
	echo "<br>";
	
	$pocetPozicDrueckArray = getPalArrayFromDrueckAuftragsnrTeilAbgnrProPal($dauftrRow['auftragsnr'],$dauftrRow['teil'],$dauftrRow['abgnr'],$dauftrRow["pos-pal-nr"]);
	echo "drueckarraypropal";
		print_r($pocetPozicDrueckArray);
	echo "<br>";
	
	$pocetPozicDauftr=sizeof($pocetPozicDauftrArray);
	$pocetPozicDrueck=sizeof($pocetPozicDrueckArray);
*/

//echo "<h1>test</h1>";
//$pole = grantAccess("jr","krakatit",get_pc_ip());
//
//var_dump($pole);
// konec vkladani do DB
// soubor neukoncuju znackou pro konec php

//$persnrArray = $apl->getPersnrFromEintritt('1990-01-01',TRUE);
//foreach ($persnrArray as $person){
//    echo "<br>person:".$person;
//    $u=$apl->getUrlaubBisDatum($person, '2012-12-31');
//    echo "rest=".$u['rest']
//	.', anspruch='.$u['anspruch']
//	.', alt='.$u['alt']
//	.', gekrzt='.$u['gekrzt']
//	.', genommen='.$u['genommen'];
//    $ar=$apl->updateUrlaubField($person,'rest',$u['rest']);
//    echo ", ar=$ar";
//}

//$apl->changePersNr(99555, 5135);