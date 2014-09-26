<?php
session_start();
//require './fns_dotazy.php';
require '../fns_dotazy.php';
dbConnect();
$el = erster_lager('2191172', 198275, 5030);
echo "$el";