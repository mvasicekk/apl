<?php
session_start();

require "../fns_dotazy.php";
dbConnect();

  $persnr = $_POST["PersNr"];
  $schicht = $_POST["Schicht"];
  $datum = $_POST["Datum"];
  $von = $_POST["Von"];
  $bis = $_POST["Bis"];
  $pause1 = $_POST["pause1"];
  $pause2 = $_POST["pause2"];
  $stunden = $_POST["stunden"];
  $tat = $_POST["tatigkeit"];
  
 if($pause2 == ""){$pause2 = 0;} //Pokud je pauza2 hodnoty NULL, pak j� nastav�me '0'!
 if($tat != "a"){$stunden = 0;} // Pokud neni �innost nastavena na "a" pak nastav�me po�et hodin na '0'!

 
 $datumRoz = explode(".",$datum); // Roz�e�eme datum na jednotliv� �daje
 $datum = $datumRoz[2]."-".$datumRoz[1]."-".$datumRoz[0]; // Op�t ho spoj�me
 
 $vonHod = substr($von,0,2); // roz�e�eme p��chod na �daje
 $vonMin = substr($von,3,2); // roz�e�eme p��chod na �daje
 
 $bisHod = substr($bis,0,2); // roz�e�eme odchod na �daje
 $bisMin = substr($bis,3,2); // roz�e�eme odchod na �daje
 
$von = date("y-m-d H:i:s",mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2])); // sestav�me nov� p��chod i s datumem
$bis = date("y-m-d H:i:s",mktime($bisHod, $bisMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2])); // sestav�me nov� odchod i s datumem
 
$pocitac=gethostbyaddr($_SERVER["REMOTE_ADDR"]);
$ident=$pocitac."/".$_SESSION["user"]; 

    /*Z�pis do datab�ze*/
    $sql = "insert into dzeit
    (Persnr, Datum, Stunden, Schicht, tat, anw_von, anw_bis, pause1, pause2,comp_user_accessuser) values
    ('$persnr', '$datum', '$stunden', '$schicht', '$tat', '$von', '$bis', $pause1, $pause2,'$ident')";
    $res = mysql_query($sql) or die(mysql_error());
    header("location:./dzeit.php?lastdatum=$datum");
?>
