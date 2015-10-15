<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../db.php';

$a = AplDB::getInstance();

$hasPrivilege = $a->getPrivilegeSecFull('auftrag', 'bestellnr', 'rk','schreiben');

echo "hasprivilege = $hasPrivilege<br>";