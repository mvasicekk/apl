<?php
session_start();
require("../libs/Smarty.class.php");
require_once '../db.php';
$smarty = new Smarty;

// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
if (isset($_SESSION['user']) && isset($_SESSION['level'])) {
    $smarty->assign("user", $_SESSION['user']);
    $smarty->assign("level", $_SESSION['level']);
    $smarty->assign("prihlasen", 1);
} else {
    header("Location: ../index.php");
}
//nacteni roli podle prihlaseni
require_once '../assignsecurity.php';
$smarty->display('auftragpositionen.tpl');