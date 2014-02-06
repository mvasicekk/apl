<?php
session_start();
//require './fns_dotazy.php';
require './db.php';

$apl = AplDB::getInstance();

$persnrArray = $apl->getPersnrFromEintritt('1990-01-01',TRUE);
foreach ($persnrArray as $person){
    echo "<br>person:".$person;
    $u=$apl->getUrlaubBisDatum($person, '2013-12-31');
    echo "rest=".$u['rest']
	.', anspruch='.$u['anspruch']
	.', alt='.$u['alt']
	.', gekrzt='.$u['gekrzt']
	.', genommen='.$u['genommen'];
    //$ar=$apl->updateUrlaubField($person,'rest',$u['rest']);
    //$ar=$apl->updateUrlaubField($person,'jahranspruch',20);
    echo ", ar=$ar";
}