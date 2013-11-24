<?
  require "../fns_dotazy.php";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=cp1250">
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <title>      Dpers
    </title>
<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>

    <style type="text/css" media="screen">
      <!--
      body{padding-top:0px; background-color: rgb(202,202,202);}
      label{margin-left: 10px;}
      #detail fieldset{padding: 2px; margin: 0px; width: 170px; max-width: 300px; height: 140px;}
      .malySelect{ width: 130px;}
      .velky{ height: 30px; font: normal normal bold 20px bold Verdana; background-color: rgb(160,160,160);}
      //-->
    </style>
  </head>
  <body>
    <fieldset style="padding-bottom: 0px; padding-top: 0px; width: 980px;">
      <legend>        Dpers
      </legend>
      <form action="save.php" method="post" name="formDpers" accept-charset="windows-1250">
        <label for="PersNr" class="velky">          PersNr
        </label>
        <input type="text" class="velky" name="PersNr" id="PersNr" maxlength="5" size="5"  onblur="stahniData(this.value);">
        <label for="austritt">          Austritt/Vyjmout
        </label>
        <input type="text" name="austritt" id="austritt" size="8">
        <label for="eintritt">          Eintritt/Nástup
        </label>
        <input type="text" name="eintritt" id="eintritt" size="8">
        <label for="probezeit">          Zkuą. doba
        </label>
        <input type="text" name="probezeit" id="ppdatum" size="8">
        <label for="dobaurcita">          Doba Určitá
        </label>
        <input type="text" name="dobaurcita" id="dobaurcita" size="8">
        <br />
        <label for="zkDobaUrcita" style="margin-left: 660px;">          Zkuąební doba v době určité
        </label>
        <input type="text" name="zkDobaUrcita" id="zkDobaUrcita" size="10">
        <br />
        <fieldset style="width:205px; padding:2px; float: right;">
          <legend>            Urlaub
          </legend>
          <label for="jahr">            JahrAnspr
          </label>
          <label for="rest" style="margin-left: 20px;">            Rest
          </label>
          <label for="genomen">            Genom
          </label>
          <input type="text" name="jahr" id="jahr" size="6">
          <input type="text" name="rest" id="rest" size="4" >
          <input type="text" name="genomen" id="genomen" size="4">
          </fieldset>
          <label for="name" class="velky">            Name
          </label>
          <input type="text" class="velky" name="name" id="name" size="15">
          <label for="vorname" class="velky">            Vorname
          </label>
          <input type="text" class="velky" name="vorname" id="vorname" size="15">
          <br />
          <br />
          <label for="schicht">            Schicht
          </label>
          <select name="schicht" id="schicht">
<?
dbConnect();  
$res = mysql_query("select * from dSchicht");
while($schicht = mysql_fetch_array($res)){
echo "<option value='".$schicht["Schichtnr"]."'>".$schicht["Schichtnr"]."-".$schicht["Schichtfuehrer"]."</option>";
} 

            ?>
          </select>
          <label for="kommOrt">            Wohn Ort
          </label>
          <input type="text" name="kommOrt" id="kommOrt" size="10">          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="button" name="abmahnung" value="Abmahnung Eingeben"  style="height: 40px;">
          <fieldset id="detail" style="max-width:980px; margin: 2px; padding:2px;">
            <legend>              Details
            </legend>
            <span style="float: right;  margin-left: 5px;">
              <input type="checkbox" name="vykon" id="vykon">
              <label for="vykon">                Prémie za
                <br />                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;výkon
              </label>
              <br />
              <br />
              <input type="checkbox" name="kvalita" id="kvalita">
              <label for="kvalita">                Prémie za
                <br />                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kvalitu
              </label>
              <br />
              <br />
              <input type="checkbox" name="prasnost" id="prasnost">
              <label for="prasnost">                Prémie za
                <br />                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Praąnost
              </label>
              <br />
              <br />
              <input type="checkbox" name="mesice" id="mesice">
              <label for="mesice">                Prémie za
                <br />                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3 měsíce
              </label>
              <br />
              <br />
            </span>
            <span style="float: right; margin-left: 5px;">
              <label for="lohnkoef">                Lohnkoef.
              </label>
              <br />
              <input type="text" name="lohnkoef" size="4" id="lohnkoef">
              <br />
              <label for="podleSmen">                Mzda podle směn
              </label>
              <br />
              <input type="checkbox" name="podleSmen" id="podleSmen">
            </span>
            <span style="float: right; padding: 2px;">
              <fieldset >
                <label>                  Deutsch
                </label>
                <br />
                <select name="k6" id="k6">
                  <option value="0">                  Nein
                  </option>
                  <option value="1">                  Ja
                  </option>
                </select>
                <input type="text" name="kom6" id="kom6" size="10">
                <label>
                  Tel.
                </label>
                <br />
                <select name="k7" id="k7">
                  <option value="0">                  Nein
                  </option>
                  <option value="1">                  Ja
                  </option>
                </select>
                <input type="text" name="kom7" id="kom7" size="10">
                </fieldset>
            </span>
            <span style="float: right; padding: 2px;">
              <fieldset>
                <legend>                  Spind/Werkzeugmarke
                </legend>
                <label>                  Spind
                </label>
                <br />
                <select name="k4" id="k4">
                  <option value="0">                  Nein
                  </option>
                  <option value="1">                  Ja
                  </option>
                </select>
                <input type="text" name="kom4" id="kom4" size="5">
                <br />
                <label>                  Werkzeugmarke
                </label>
                <br />
                <select name="k5" id="k5">
                  <option value="0">                  Nein
                  </option>
                  <option value="1">                  Ja
                  </option>
                </select>
                <input type="text" name="kom5" id="kom5" size="5">
                </fieldset>
            </span>
            <span style="float: right; padding: 2px;">
              <fieldset>
                <legend>                  Arzt
                </legend>
                <label>                  Eintrittsuntersuchung:
                </label>
                <br />
                <select name="k2" id="k2">
                  <option value="0">                  Nein
                  </option>
                  <option value="1">                  Ja
                  </option>
                </select>
                <input type="text" name="kom2" id="kom2" size="12">
                <br />
                <label>                  Vorbeugeuntersuchung
                </label>
                <br />
                <select name="k3" id="k3">
                  <option value="0">                  Nein
                  </option>
                  <option value="1">                  Ja
                  </option>
                </select>
                <input type="text" name="kom3" id="kom3" size="12">
                </fieldset>
            </span>
            <span style="float: right; padding: 2px;">
              <fieldset>
                <legend>                  Ausbildung
                </legend>
                <select name="k1" id="k1">
<?

  
$res = mysql_query("select * from DAusBildPPlan");
while($ausbild = mysql_fetch_array($res)){
echo "<option value='".$ausbild["id"]."'>".$ausbild["bezeichnung"]."</option>";
} 

                  ?>
                </select>
                <br />
                <label>                  Was
                </label>
                <br />
                <input type="text" name="kom1" id="kom1" size="16">
                </fieldset>
            </span>
            <span style="float: right; padding: 2px;">
              <fieldset style="width: 300px;">
                <legend>                  Abmahnung
                </legend>
<?    

for($i = 21; $i < 25;$i++){
        echo "<select name='k$i' id='k$i'  class='malySelect'>";  
$res = mysql_query("select * from dabmahnpplan");
while($abmahn = mysql_fetch_array($res)){
echo "<option value='".$abmahn["id"]."'>".$abmahn["bezeichnung"]."</option>";
}
echo "</select>";
 echo "<input type='text' name='kom$i' id='kom$i' size='20'><br />"; 
}

                ?>
                </fieldset>
            </span>
            <span style="float: right; padding: 2px;">
              <fieldset>
                <label>                  Grundieren
                </label>
                <br />
                <select name="k11" id="k11" class="malySelect">
<?    

  
$res = mysql_query("select * from DGrundierenPplan");
while($abmahn = mysql_fetch_array($res)){
echo "<option value='".$abmahn["id"]."'>".$abmahn["bezeichnung"]."</option>";
}

                  ?>
                </select>
                <br />
                <label>                  PC
                </label>
                <br />
                <select name="k12" id="k12">
                  <option value="0">                  Nein
                  </option>
                  <option value="1">                  Ja
                  </option>
                </select>
                <input type="text" name="kom12" id="kom12" size="12">
                </fieldset>
            </span>
            <span style="float: right; padding: 2px;">
              <fieldset>
                <label>                  Geprüfter Anschlagmittelanw.
                </label>
                <br />
                <input type="text" name="k10" id="k10" size="3">
                <input type="text" name="kom10" id="kom10" size="10">
                <br />
                <label>                  Sonstiges
                </label>
                <br />
                <input type="text" name="k20" id="k20" size="3">
                <input type="text" name="kom20" id="kom20" size="10">
                </fieldset>
            </span>
            <span style="float: right; padding: 2px;">
              <fieldset>
                <label>                  Schweissen
                </label>
                <br />
                <select name="k8" id="k8" class="malySelect">
<?    

  
$res = mysql_query("select * from DSchweissenPplan");
while($abmahn = mysql_fetch_array($res)){
echo "<option value='".$abmahn["id"]."'>".$abmahn["bezeichnung"]."</option>";
}

                  ?>
                </select>
                <br />
                <label>                  Stapler
                </label>
                <br />
                <select name="k9" id="k9" class="malySelect">
<?    

  
$res = mysql_query("select * from DStaplerPplan");
while($abmahn = mysql_fetch_array($res)){
echo "<option value='".$abmahn["id"]."'>".$abmahn["bezeichnung"]."</option>";
}
mysql_close();
                  ?>
                </select>
                </fieldset>
            </span>            
            <hr style="visibility: hidden; clear: both;">
            </fieldset>
            <input type="submit" name="save" value="Ulož" id="save" style="width: 100px; margin-bottom:3px;" />
            <input type="button" value="Ende/Konec" id="konec" style="width: 100px; margin-left: 600px; margin-bottom:3px;" onClick="location.href='../index.php'">
      </form>
      </fieldset>
  </body>
</html>
