<?php
session_start();
//require './fns_dotazy.php';
require './db.php';

$apl = AplDB::getInstance();

$persnrArray = $apl->getPersnrFromEintritt('1990-01-01',TRUE);
foreach($persnrArray as $persnr){
   $eintritt = substr($apl->getEintrittsDatumDB($persnr),0,10);
   $nameArray = $apl->getNameVorname($persnr);
   if($nameArray!==NULL)
       $name = $nameArray['name'].' '.$nameArray['vorname'];
   else
       $name = '';
   
   $plusminusStunden2011 = number_format($apl->getPlusMinusStunden(12, 2011, $persnr),1,'.','');
   $arbstunden2012 = number_format($apl->getArbStundenBetweenDatums($persnr,'2012-01-01','2012-12-31'),1,'.','');
   $plusminusStunden2012 = number_format($apl->getPlusMinusStunden(12, 2012, $persnr),1,'.','');
   $arbstunden2013 = number_format($apl->getArbStundenBetweenDatums($persnr,'2013-01-01','2013-12-31'),1,'.','');
   $plusminusStunden2013 = number_format($apl->getPlusMinusStunden(12, 2013, $persnr),1,'.','');
   echo "$persnr,$eintritt,$name,$plusminusStunden2011,$arbstunden2012,$plusminusStunden2012,$arbstunden2013,$plusminusStunden2013<br>";
}
