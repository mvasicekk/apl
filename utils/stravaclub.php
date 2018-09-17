<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$content = file_get_contents('https://www.strava.com/clubs/446602/latest-rides/1b43fc36e3be0823e30a7a3a648067c67ca9a1d5?show_rides=true');
//$content = str_replace('</title>','</title><base href="https://www.google.com/calendar/" />', $content);
$content = str_replace('</head>','<link rel="stylesheet" href="../utils/gauges/css/style.css" /><meta charset="UTF-8"></head>', $content);
echo $content;
