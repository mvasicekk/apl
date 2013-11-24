<?php
  require "../fns_dotazy.php";
  
  dbConnect();

  $persnr     = $_POST["PersNr"];
  $schicht    = $_POST["Schicht"];
  
  $datum      = $_POST["datum"];
  $od         = $_POST["od"];
  $do         = $_POST["do"];
  $pause      = $_POST["pause"];
  $verbZeit   = $_POST["verbZeit"];
  
  
  $auftragsNr = $_POST["AuftragsNr"];
  $palNr      = $_POST["palnr"];
  $teil       = $_POST["teil"];
  
  $taetNr     = $_POST["tatigkeit"];
  $vzAby      = $_POST["tatzeit"];
  $vzKd       = $_POST["kz"];
  
  $stueck     = $_POST["stueck"];
  $ausStueck  = $_POST["ausstueck"];
  $ausart     = $_POST["ausart"];
  $austyp     = $_POST["austyp"];
  
  $s_user = $_SERVER['REMOTE_ADDR'];


    $odSt  = mktime($odH = substr($od, 0, 2), $odM = substr($od, 2, 2), '00');
    $doSt  = mktime($doH = substr($do, 0, 2), $doM = substr($do, 2, 2), '00');
    
    $od = date("Y-m-d H:i:s", $odSt);
    $do = date("Y-m-d H:i:s", $doSt);
    
    $dat = explode(".", $datum);
    
    $datSt =  mktime($dat[1], $dat[0], $dat[2]);
    $str_datum = date("Y-m-d",$datSt)." 00:00:00";
    $str_stamp = date("Y-m-d h:m:s");
    
    $sql_insert = "insert into drueck (`auftragsnr`,`Teil`,`pos-pal-nr`,
    `TaetNr`,`persnr`,`Stück`,`auss-stück`,`datum`,`vz-soll`,`VZ-IST`,
    `verb-zeit`,`auss-art`,`auss_typ`,`verb-von`,`verb-bis`,`verb-pause`,
    `schicht`,`marke-aufteilung`,`comp_user_accessuser`,`insert_stamp`) 
    values($auftragsNr,'$teil',$palNr,$taetNr,$persnr,$stueck,
    $ausStueck,'$str_datum','$vzKd','$vzAby',$verbZeit,
    $ausart ,$austyp ,'$od','$do',$pause,$schicht,'',
    '$s_user', '$str_stamp')";

	//echo $sql_insert;
	$res_insert = mysql_query($sql_insert) or die(mysql_error());
 
    

    /*  udelam zapis do tabulky DLagerbew
        zjistim nazvy lagru
        kolik a jake operace jsou zadany
        1.operace, ta je tam vzdy*/
    $l_von = lager_von($teil, $taetNr);

    $l_nach = lager_nach($teil, $taetNr);

    
    $sql_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,
    gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser,abgnr) 
    values('$teil',$auftragsNr,$palNr,$stueck,0,'$l_von','$l_nach',
    '$s_user',$taetNr)";
    

    $res_insert = mysql_query($sql_insert) or die(mysql_error());

    /* 060308 test na bedarf taetigkeit
     1. test zda je s_taetnr bedarf teat*/
     
    If (isBedarf($taetNr, $teil)) {
        //upravim ukazatele skladu
      updateLagerBedarfPointer($taetNr, $teil, $auftragsNr, $palNr, $l_von, $l_nach);
     }
     
    
    If ($ausStueck <> 0){ 
        // udelam zapis do tabulky DLagerbew pokud mam nejake zmetky
        // zjistim nazvy lagru
        // kolik a jake operace jsou zadany
        // 1.operace, ta je tam vzdy
        $l_von = "A0";
        $l_nach = "AX";
        If($austyp == 2) {$l_nach = "A2";}
        If($austyp == 4) {$l_nach = "A4";}
        If($austyp == 6) {$l_nach = "A6";}
        
        $sql_insert = "insert into dlagerbew (teil,auftrag_import,
        pal_import,gut_stk,auss_stk,lager_von,lager_nach,
        comp_user_accessuser,abgnr) values('$teil,$auftragsNr,$palNr,
        0,$ausStueck,'$l_von','$l_nach','$s_user',$taetNr[0])";

        //$res = mysql_query($sql_insert) or die(mysql_error());
    }


	header("location:./drueckMa.php");
    
Function lager_von($teil, $taet) {

  $sql = "SELECT `lager_von` FROM `DPOS` WHERE (((`teil`) = '$teil') 
  And ((`lager_von`) Is Not Null) and (`taetnr-aby`='$taet'))";
  
  $res = mysql_query($sql) or die(mysql_error());
  
  If(mysql_affected_rows()>0){ // kontrola zda vratil nejaky zaznam
      $zaznam = mysql_fetch_array($res);
      If ($zaznam['lager_von']!=''){ //kontrola zda ten zaznam neni nulovy
          return $zaznam['lager_von'];
      }Else{
          return "0D";
      }
  }Else{
      return "0D";
  }

}

Function lager_nach($teil, $taet) {

  $sql = "SELECT `lager_nach` FROM `DPOS` WHERE (((`teil`) = '$teil') 
  And ((`lager_nach`) Is Not Null) and (`taetnr-aby`='$taet'))";
  
  $res = mysql_query($sql) or die(mysql_error());
  
  If(mysql_affected_rows()>0){ // kontrola zda vratil nejaky zaznam
      $zaznam = mysql_fetch_array($res);
      If ($zaznam['lager_nach']!=''){ //kontrola zda ten zaznam neni nulovy
          return $zaznam['lager_nach'];
      }Else{
          return "0D";
      }
  }Else{
      return "0D";
  }

}
    
    
Function isBedarf($taet, $teil){

  $sql = "select `bedarf_typ` from `DPOS` where ((`TaetNr-Aby`=$taet) and (teil='$teil'))";
  $res = mysql_query($sql) or die(mysql_error());
    
  
  If(mysql_affected_rows()>0){
      $zaznam = mysql_fetch_array($res);
      
      $bedarf = $zaznam['bedarf_typ'];
      
    If($bedarf == "B"){
        return True;
    }Else{
        return False;
    }
    
  }Else{
      return False;
      }
  }

Function updateLagerBedarfPointer($taet, $teil, $auftragsNr, $palNr, $l_nach, $l_von){

  $sql = "select `abgnr`,`lager_von`,`lager_nach` from `DPosBedarfLager` 
  where ((`abgnr_bedarf`=$taet) and (`teil`='$teil'))";
  $res = mysql_query($sql) or die(mysql_error());
  
  If(mysql_affected_rows()>0){
      $sql_update = "update dlagerbew set `lager_von`='$l_von',`lager_nach`='$l_nach' 
      where ((`auftrag_import`=$auftragsNr) and (`pal_import`=$palNr) and (`teil`='$teil') and (`abgnr`=$taet))";
      echo "<br /><br />$sql_update";
      $res = mysql_querry($sql_update) or die(mysql_error());

      
      return True;
  }Else{
  
      return False;
  }

}

    mysql_close();

?>
