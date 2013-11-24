<?php
//session_start();
//require "./fns_dotazy.php";
require 'db.php';

$StatNr2LagerArray = array(
    'S0011'=>'3P',
    'S0041'=>'4R',
    'S0051'=>'5K',
    'S0061'=>'6F',
);

$kunde = 130;

$apl = AplDB::getInstance();

// Liste der Teile
$teileNrArray = $apl->getTeileNrArrayForKunde($kunde);
//echo "<pre>";
//var_dump($teileNrArray);
//echo "</pre>";

// suche die aktuelle G tat
foreach ($teileNrArray as $number => $teil) {
    $teilNr = $teil['teil'];
    // get G abgnr
    $abgnrG = $apl->getGAbgNrForTeil($teilNr);
    if ($abgnrG !== NULL) {
        $abgNrAktuellArray = $apl->getAbgNrArrayForTeilKleinerAls($teilNr, $abgnrG);
        echo "<br>$teilNr, Gtat=$abgnrG";
        $GStatNr = $apl->getStatNrForAbgnr($abgnrG);
        echo ",statnr = $GStatNr";
        if (key_exists($GStatNr, $StatNr2LagerArray)) {
            echo ", lager_nach='8E' lager_von=" . $StatNr2LagerArray[$GStatNr];
            $letzterLager = $StatNr2LagerArray[$GStatNr];

            if (is_array($abgNrAktuellArray)) {
                echo "<ul>";
                foreach ($abgNrAktuellArray as $value) {
                    $abgnr = $value['abgnr'];
                    $StatNr = $apl->getStatNrForAbgnr($abgnr);
                    if (key_exists($StatNr, $StatNr2LagerArray)) {
                        $lagerVon = $StatNr2LagerArray[$StatNr];
                        $lagerNach = $letzterLager;
                        $letzterLager = $lagerVon;
                        echo "<li>" . $abgnr . ":" . $StatNr . ":" . $lagerVon . "->" . $lagerNach . "</li>";
                    } else {
                        //nebudu delat nic
                        $lagerVon = '0D';
                        $lagerNach = '0D';
                    }
                    
                }
                echo "</ul>";
            }
        }
    }
}