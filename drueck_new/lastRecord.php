<?php
session_start();
require "../fns_dotazy.php";
dbConnect();
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
mysql_query('set names utf8');
$data = file_get_contents("php://input");
$o = json_decode($data);




$sql = "select drueck_id,auftragsnr,teil,`pos-pal-nr`as pal,taetnr,`Stück` as stk,`auss-stück` as aussstk,`auss-art` as aart,auss_typ as atyp";
$sql.= ",`vz-soll` as vzkd,`vz-ist` as vzaby,DATE_FORMAT(datum,'%d.%m.%Y') as datum,persnr,DATE_FORMAT(`verb-von`,'%H:%i') as von,DATE_FORMAT(`verb-bis`,'%H:%i') as bis,`verb-zeit` as verb,`verb-pause` as pause";
$sql.= ",schicht,oe,`marke-aufteilung` as aufteilung,comp_user_accessuser as user";
$sql.= " from drueck order by stamp desc limit 5";

	$result = mysql_query($sql);

        
        $data = array();
        if(mysql_affected_rows()>0){
        while($row=mysql_fetch_array($result)){
            $data[] = array(
                "drueck_id" =>   $row['drueck_id'],
                "auftragsnr"  =>   $row['auftragsnr'],
                "teil"  =>   $row['teil'],
                "pal" =>   $row['pal'],
                "taetnr"  =>   $row['taetnr'],
                "stk" => $row['stk'],
                "aussstk" => $row['aussstk'],
                "aart" => $row['aart'],
                "atyp" => $row['atyp'],
                "vzkd" => $row['vzkd'],
                "vzaby" => $row['vzaby'],
                "datum" => $row['datum'],
                "persnr" => $row['persnr'],
                "von" => $row['von'],
                "bis" => $row['bis'],
                "verb" => $row['verb'],
                "pause" => $row['pause'],
                "oe" => $row['oe'],
                "aufteilung" => $row['aufteilung'],
                "user" => $row['user'],
             );
        }
           //print_r($row);
            print_r(json_encode($data));
            return json_encode($data);
         }  
        
        
        
        
        
        
        
        
        
