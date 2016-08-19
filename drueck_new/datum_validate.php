<?php
session_start();
$data = file_get_contents("php://input");
$o = json_decode($data);
$datum = $o -> datum;
$output = "";
//**********************************************************************\\

    // casti datumu povolim oddelovaz znaky : , . - a mezera
    $vymenit=array(",", ".", "-", ":", " ","");

    if(strlen($datum)>=3 ){
        //datum byl zadan i s rokem
        //sjednotim si oddelovaci znak
        $novy_datum = str_replace($vymenit,"/",$datum);
        // rozkouskuji jednotlive casti
        $dily = explode("/",$novy_datum);
        $pocetDilu = count($dily);

        //otestuji jednotlive dily zda se nejedna o blbost
        if( ($dily[1]<13) && ($dily[1]>0) && ($dily[0]>0) && ($dily[0]<32)){

            if($pocetDilu == 2){
                //nezadal rok
                $dily[2] = date('Y');
            }

            if(($pocetDilu == 3) && (strlen($dily[2]) == 0)){
                //nezadal rok
                $dily[2] = date('Y');
            }
            
            $timestamp = mktime( 0,0,0,$dily[1],$dily[0],$dily[2]);
            $now = time();
            $rok = date("Y",$timestamp);
            $mesic = date("m",$timestamp);
            $den = date("d",$timestamp);
                
                if($timestamp < $now){
                    $output = $den.".".$mesic.".".$rok;
                }
                else{
                    $output = "ERROR";
                }
                  
        }
              
    }
    else{  
         
            $now = time();
            $rok = date("Y");
            $mesic = date("m");
            $den = date("d");
            $output = $den.".".$mesic.".".$rok;
        }



$retArr = array(
    'datum' => $output
);

echo json_encode($retArr);


