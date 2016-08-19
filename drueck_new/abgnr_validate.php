<?php
session_start();
require "../fns_dotazy.php";
require '../db.php';
$aplDB = AplDB::getInstance();

$data = file_get_contents("php://input");
$o = json_decode($data);

$value = $o->tat1.$o->tat2;
$auftragsnr = $o->auftragsnr;
$palette = $o->palette;
$teil = $o->teilnr;
$mehr_value=$o->mehr;
$tat1 = $o->tat1[0];

$outputAbgnr = array();
$outputVzaby = array();
////////////////////////////////////////////////////////////////////////////////
mysql_query("SET NAMES UTF8");

$pg = $aplDB->getPGFromAuftragsnr($auftragsnr);

if($mehr_value>0){
    // Budu kontrolovat jen interni operace
    // zkusim nejdriv najit operaci v pracovnim planu u dilu
    // 2015-10-22 nepovolit operace 4000 az 4999
    
    $sql="select `TaetBez-Aby-D` as bezd,`TaetBez-Aby-T` as bezt,`TaetNr-Aby` "
        . "as abgnr,`VZ-min-kunde` as vzkd,`vz-min-aby` as vzaby from dpos "
        . "join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby` "
        . "where ((dpos.`Teil`='".$teil."') and (dtaetkz='I')  and "
        . "(`TaetNr-Aby`<>'3') and (`TaetNr-Aby`<'4000' or `TaetNr-Aby`>'4999')"
        . " and (`TaetNr-Aby`='".$value."')) order by abgnr";
    
    $result = mysql_query($sql);
    
    if(mysql_affected_rows()>0){
        // operaci jsem nasel u dilu
        while( $row = mysql_fetch_array($result) ){
            //vytahnu si seznam OE pro dane ABGNR
            $oesString = $aplDB->getOEForAbgnr( $row['abgnr'] );
            
            $outputAbgnr[] = $row['abgnr'];
            $outputVzkd = $row['vzkd'];
            $outputVzaby[] = $row['vzaby'];
            $outputBezd = $row['bezd'];
            $outputBezt = $row['bezt'];
            $output = $oesString;     
        }   
    }
    else{
        // dil nema tuhle operaci v pracovnim planu, vytahnu info o operaci ze seznamu operaci a vzaby,vzkd nastavim na 0
        // a jeste musim rozlisit zdanejde o zakazku 999999
        if( $auftragsnr==999999||$auftragsnr==99999999 ){
            $sql="select `abg-nr` as abgnr,oper_CZ as bezt,oper_D as bezd from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`>6999) and (`abg-nr`='$value')) order by abgnr";
        }
        else{
            // 2015-10-22 nepovolit 400-4999, musi byt v auftragu
            $sql="select `abg-nr` as abgnr,oper_CZ as bezt,oper_D as bezd from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`<7000) and (`abg-nr`<4000 or `abg-nr`>4999) and (`abg-nr`='$value')) order by abgnr";              
        }
        
        $result = mysql_query($sql);
        
        if(mysql_affected_rows()>0){
            // operaci jsem nasel v katalogu operaci 
            $row = mysql_fetch_array($result);
            // vytahnu si seznam OE pro dane abgnr
            $oesString = $aplDB->getOEForAbgnr( $row['abgnr'] );
            
            $outputAbgnr[] = $row['abgnr'];
            $outputVzkd = 0;
            $outputVzaby[] = 0;
            $outputBezd = $row['bezd'];
            $outputBezt = $row['bezt'];
            $output = $oesString;     
        }
        else{
            // operaci jsem vubec nenasel
            // jeste zkusim jestli tam nezadal nulu 
            if( $value == 0 ){
                if($tat1[0] == 0){
                    $outputAbgnr[] = "ERROR-NOABGNR";
                }
                else{
                    $outputAbgnr[] = 0;
                    $outputVzkd = 0;
                    $outputVzaby[] = 0;
                    $outputBezd;
                    $outputBezt;
                }
                
            }
            else{
                // tak to je fakt konecna a hlasim chybu
                $outputAbgnr[] ="ERROR-NOABGNR";
            }
            
        }
        
    }
}
else{
    // neni to viceprace ale normalni operace z dauftr 
    $sql="select `Name` as bezd,`Name` as bezt,abgnr,vzkd,vzaby from dauftr "
            . "join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.`abgnr` "
            . "where ((`abgnr`='".$value."') and (`teil`='".$teil."') and "
            . "(`pos-pal-nr`='".$palette."') and (auftragsnr='".$auftragsnr."') "
            . "and (`auftragsnr-exp` is null) and (`pal-nr-exp` is null) and "
            . "(dauftr.abgnr<>3)) order by abgnr";
    
    $result = mysql_query($sql);
    // pokud mi dotaz vrati nejake zaznamy, tak je projdu 
    
    if(mysql_affected_rows()>0 ){
        while( $row = mysql_fetch_array($result) ){
            $oesString = $aplDB->getOEForAbgnr( $row['abgnr'] ) ;
            $outputAbgnr[] = $row['abgnr'];
            $outputVzkd = $row['vzkd'];
            $outputVzaby[] = $row['vzaby'];
            $outputBezd = $row['bezd'];
            $outputBezt = $row['bezt'];
            $output = $oesString;  
        } 
    }
    else{
        if($value == 0){
            if($tat1[0] == 0){
                $outputAbgnr[] = "ERROR-NOABGNR";
            }
            else{
                    $outputAbgnr[] = 0;
                    $outputVzkd = 0;
                    $outputVzaby[] = 0;
                    $outputBezd;
                    $outputBezt;
            }
        }
        else{
            $outputAbgnr[] = "ERROR-NOABGNR";
        }
    } 
}

$oeArray = split(';',$oesString);
$reducedOeArray = array();

if( count($oeArray)>1 ){
  // mam vice nez jedno oe, zkusim omezit jejich pocet podle PG zakaznika 
  // vytahnu si oe podle PG 
  $oeArrayPG = $aplDB->getOESForPG($pg);
  // ocistim si pole od mezer
  $oeArrayClean = array();
  
  foreach($oeArray as $oe){
       $oeClean = trim($oe);
       array_push($oeArrayClean,$oeClean);
       if(in_array($oeClean, $oeArrayPG)){
       array_push($reducedOEArray, $oeClean);
       }
  }
}
else{
     $oeArrayClean = array(trim($oesString));
}

if( count($reducedOEArray)>0 ){
    $outputOe = join(';',$reducedOEArray);
}
else{
    $outputOe = join(';',$oeArrayClean);
}
$outputO = join(';',$oeArrayClean);
$outputPg = $pg;

$retArray = array(
    'abgnr' => $outputAbgnr,
        'vzkd' => $outputVzkd,
            'vzaby' => $outputVzaby,
                'bezd' => $outputBezd,
                    'bezt' => $outputBezt,
                        'oe' => $output
);

echo json_encode($retArray);
