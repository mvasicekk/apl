<?php
require_once '../db.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$apl = AplDB::getInstance();
$persnrArray = $apl->getPersnrFromEintritt('1990-01-01',TRUE);
echo "<table>";
foreach ($persnrArray as $person){
    echo "<tr>";
    echo "<td>person:".$person."</td>";
    $u=$apl->getUrlaubBisDatum($person, '2016-12-31');
    echo "<td>rest=".$u['rest']."</td>"
	."<td>anspruch=".$u['anspruch']."</td>"
	."<td>alt=".$u['alt']."</td>"
	."<td>gekrzt=".$u['gekrzt']."</td>"
	."<td>genommen=".$u['genommen']."</td>";
    //$ar=$apl->updateUrlaubField($person,'rest',$u['rest']);
    //$ar=$apl->updateUrlaubField($person,'jahranspruch',20);
    echo "<td>ar=$ar</td>";
    echo "</tr>";
}
echo "</table>";