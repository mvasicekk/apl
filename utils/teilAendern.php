<?php
require_once '../db.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$apl = AplDB::getInstance();
$von = '261434';
$nach = 'R907261434';

$apl->teilNrAendern($von, $nach);