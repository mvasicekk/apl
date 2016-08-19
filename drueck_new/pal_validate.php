<?php
session_start();
require "../fns_dotazy.php";
dbConnect();

    $data = file_get_contents("php://input");
    $o = json_decode($data);

    $auftragsnr = $o->auftragsnr;
    $palette = trim($o->palette);

     //$auftragsnr = 12200142;
     //$palette = 2330;

    //zjistim zda už je paleta exportovana 
    $hasExport=1;
    $outputTeilbez = array();
    if( strlen($palette) > 0)
    {
    $hasexport =  hasexport($auftragsnr,$palette) ;
    }
   
    // $hasexport = 0;
    
    $outputX = $hasexport;
    
    // 1. zjistim existenci palety v zakazce 
    $sql = "SELECT dauftr.teil, teilbez FROM dauftr JOIN dkopf USING(teil)"
           ."WHERE auftragsnr='$auftragsnr' and dauftr.`pos-pal-nr`='$palette'";
    
    mysql_query('SET NAMES UTF8');
    $resTeil = mysql_query($sql);
    
    if( (mysql_affected_rows()>0) && ( strlen($palette)>0 ) ){
        //takova paleta v zakazce existuje: 
        $row = mysql_fetch_array($resTeil);
        $outputTeil = $row['teil'];
        $outputPal = $palette;
        $outputT = $row['teilbez'];
        
        //ted vytahnu operace pro danou paletu a dil
        // nema export 
        if( $outputX == 0 ){
            $sql="SELECT abgnr,dauftr.teil,teilbez FROM dauftr join dkopf on dkopf.teil=dauftr.teil "
                    . "WHERE ((`pos-pal-nr`='".$palette."') and (auftragsnr='".$auftragsnr."')) ORDER BY abgnr";
            $result = mysql_query($sql);
            //pokud mi dotaz vrati nejake zaznamy => projdu je
            if(mysql_affected_rows()>0){
                
            while( $row = mysql_fetch_array($result) ){
                $outputTeilbez[] = $row['abgnr'];
                //echo $outputTeilbez;
                }          
            }
            else{
                $outputTeilbez[] = "ERROR-PAL-NOTAT";
            }           
        }
        
        else {
            // paleta uz je vyexportovana a nabidnu pro dany dil jen interni operace
	    // vytahnu vsechny interni operace a rozlisim podle zakazky
            
            if($auftragsnr<999999){
                $sql="select `abg-nr` as abgnr from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`<7000)) order by abgnr";
            }
            else{
                $sql="select `abg-nr` as abgnr from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`>6999)) order by abgnr";
            }
            
            $result_operace = mysql_query($sql) ;
            
            if(mysql_affected_rows()>0){
                
                while($row = mysql_fetch_array($result_operace)){
                    $outputTeilbez[] = $row['abgnr'];
                    //echo $outputTeilbez;
                }           
            }
            else{
                //nemam zadne interni operace ? dost nepravdepodobne ale stat se to muze
                $outputTeilbez = "ERROR-PAL-NOINTTAT";
            }
        }               
    }
    else {
        //takovou paletu v zakazce nemam
        $outputPal = "ERROR-NOPAL";
    }
    
    $retArr = array(
        'palette' => $outputPal,
        'teilbez' => $outputTeilbez,
         'teil'   => $outputTeil, 
         'export' => $outputX,
        'teilB'   => $outputT   
    );
    
    echo json_encode($retArr);