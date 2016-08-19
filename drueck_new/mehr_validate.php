<?php
session_start();
require "../fns_dotazy.php";

    $data = file_get_contents("php://input");
    $o = json_decode($data);

    $auftragsnr = $o->auftragsnr;
    //$auftragsnr = 999999;
    $palette = trim($o->palette);
    //$palette = 0;
    $mehr_value = $o->mehr; 
    //$mehr_value = 0;
///////////////////////////////////////////////////////////
    $output = array();
    //zjistim zda uz je paleta exportovana 
    
    $hasexport = hasexport($auftragsnr,$palette);
   if( $hasexport){
        //mam export u palety
       if($mehr_value>0){
        //chci zadatavat vice praci
        // paleta uz je vyexportovana a nabidnu pro dany dil jen interni operace
	// vytahnu vsechny interni operace a rozlisim podle zakazky
           if($auftragsnr==999999 || $auftragsnr==99999999){
              $sql="select `abg-nr` as abgnr from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`>6999)) order by abgnr";
           }
           else{
               //2015-10-12 nepovolit 4000-4999, musi byt v auftragu 
             $sql="select `abg-nr` as abgnr,oper_CZ as bezt,oper_D as bezd from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`<7000) and (`abg-nr`<4000 or `abg-nr`>4999)) order by abgnr";    
           }
           
           $result_operace = mysql_query($sql);
           ///////////////////////////////////////////////////////////////////////////////////
           if(mysql_affected_rows()>0 ){
               while($row = mysql_fetch_array($result_operace)){
                   $output[] = $row['abgnr'];
                   //echo $output;
               }
           }
           else{
               // nemam zadne interni operace ? dost nepravdepodobny, ale stat se muze
               $output = "ERROR-MEHR_NOINTTAT ";
           }
           ///////////////////////////////////////////////////////////////////////////////////
           //konec if($mehr_value>0)
       }
       else{
           // chci zadavat operace z dauftr, ale to u exportovane palety nesmim
           $output = "ERROR-MEHR-EXPORTEDPAL";
       }      
   }
   else{
       //nema export 
       if($mehr_value>0){
           //chci zadat vicepraci
           //vytahnu vsechny interni operace arozlisim podle zakazky 
           if($auftragsnr==999999||$auftragsnr==99999999){
               $sql="select `abg-nr` as abgnr from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`>6999)) order by abgnr";
           }
           else{
              //2015-10-22 ne 4000-4999
               $sql="select `abg-nr` as abgnr from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`<7000) and (`abg-nr`<4000 or `abg-nr`>4999)) order by abgnr"; 
           }
           $result_operace = mysql_query($sql);
           if(mysql_affected_rows()>0 ) {
               while($row = mysql_fetch_array($result_operace)){
                   $output[] = $row['abgnr'];
               }
           }
           else{
               //nemam zadne interni operace? dost nepravdepodobny ale stat se to muze 
               $output ="ERROR-MEHR-NOINTTAT";
           }
           
       }
       else{
           //budu zadavat operace z dauftr 
           	$sql="select abgnr,dauftr.teil,teilbez from dauftr join dkopf on dkopf.teil=dauftr.teil where ((`pos-pal-nr`='".$palette."') and (auftragsnr='".$auftragsnr."')) order by abgnr";
                $result = mysql_query($sql);
                //pokud mi dotaz vrati nejake zaznamy, tak je projdu 
                if(mysql_affected_rows()>0){
                    while($row = mysql_fetch_array($result)){
                        $output[] = $row['abgnr'];
                    }
                }
                else{
                    //pro zadany dil nemam v dauftr zadne operace
                    $output = "ERROR-MEHR-NODAUFTRTAT";
                }
       }
       
   }
   
   
   $retArr = array(
       'teilb' => $output
   );
   
   echo json_encode($retArr);
   