<?
  require "../fns_dotazy.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//CS">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <title>DRUECK Mehrarbeit
    </title>
<script charset="iso-8859-2" src="jsFunctions.js" type="text/javascript"></script>
<script charset="iso-8859-2" type="text/javascript">
/* <![CDATA[ */
function zjistiId(element,pole){
  for(i=0;i<=pole.length; i++){
    if(pole[i]== element){return i;}
  }
}

function checkCR(event) {
    var pole = new Array("AuftragsNr","datum","palnr","teil","tatigkeit","PersNr","Schicht","stueck","ausstueck","ausart","austyp","od","do","pause","neu");
    var element =  window.event.srcElement.id;
    
    var event  = (window.event) ? window.event : ((window.event) ? window.event : null);
    var node = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);

    if (event.keyCode == 13) {
    var i = zjistiId(element, pole) +1;
      document.getElementById(pole[i]).focus();
      return false;}
    else{
      return true;
    }  
}
  

    document.onkeypress = checkCR;
/* ]]> */
</script>

    <style type="text/css" media="screen">
      <!--
      body{padding-top:0px; background-color: rgb(202,202,202);}
      label{margin-left:10px;}
      #detail fieldset{padding:2px; margin:0px; width:170px; max-width:300px; height:140px;}
      .malySelect{width:130px;}
      .velky{height:30px; font:normal normal bold 20px bold Verdana; background-color:rgb(160,160,160);}
      .pocet{width:50px; height:40px;}
      //-->
    </style>
  </head>
  <body onload="document.getElementById('AuftragsNr').focus();">
  
  <form action="save.php" method="POST" accept-charset="iso-8859-2">
    <fieldset title="Drueck">
      <legend title="Drueck">
        Drueck Meahrarbeit
      </legend>
      <label for="AuftragsNr">
        AuftragsNr/Čislo zakazky
      </label>
      <input type="text" name="AuftragsNr" id="AuftragsNr" onblur="stahniData(this.value,'0','0','palNr');" />            
      <label for="datum">
        Datum
      </label>
      <input type="text" name="datum" id='datum' size='12' value='<? echo date("j.n.Y")?>' onfocus='this.select();' /><br /><br />
      <label for="palnr">
        Pal Nr.
      </label>
      <select name="palnr" id='palnr' onblur="stahniData(document.getElementById('AuftragsNr').value, this.value, '0', 'teilNr');" />
      </select>
      <br /><br />
      <label for="teil">
        Teil
      </label>
      <select name="teil" id='teil' onblur="stahniData(document.getElementById('AuftragsNr').value, '0',this.value,'tatigkeit');" />
      </select>      
      <label for="taetigkeit">
        Taetigkeit nr/Cislo Operace
      </label>
      <select name="tatigkeit" id='tatigkeit' onblur="stahniDataMinuty(document.getElementById('AuftragsNr').value, document.getElementById('teil').value,this.value, 'min');" />
      </select>                                                                                                                                                         
      <input type="text" name="beschreibung" size="25" readonly="readonly" />
      <br />
      <br />  
      <label for='PersNr'>
        PersNr
      </label>
      <input type='text' name='PersNr' id='PersNr' size='5' onblur="stahniDataPers(this.value,'pers');" />
      <input type='text' name='PersName' id='PersName' size='25' readonly='readonly' style='background-color: rgb(202,202,202); border:none;' />
      <label for='Schicht'>
        Schicht
      </label>
      <input type='text' name='Schicht' id='Schicht' size='2' />
      <br />
      <br />
      <label for='stueck'>
        Stueck
      </label>
      <input type='text' name='stueck' id='stueck' size='5' />      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <label for='ausstueck'>
        Auss-Stueck/Zmetek
      </label>
      <input type='text' name='ausstueck' id='ausstueck' value='0' size='5'/>
      <label for='ausart'>
        Art
      </label>
      <select name="ausart" id="ausart" onblur='stahniDataAusTyp(this.value);'>
      <option value="0">0</option>
<?
dbConnect();
  $sqlArt="SELECT * FROM `auss_typen` GROUP BY `auss-art`";
  $resArt=mysql_query($sqlArt) or die(mysql_error());
  
  while($art = mysql_fetch_array($resArt)){
    echo "<option value='".$art["auss-art"]."'>".$art["auss-art"]."</option>";
  }
mysql_close();
        ?>
        </select>
        <label for='austyp'>
          Typ
        </label>
        <select name='austyp' id='austyp' />
          <option value='0'>0</option>
        </select>
        <br />
        <br />
               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for='od'  style='margin-left:200px;'>
          Čas/ Zeit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Od/Von
        </label>
        <input type='text' name='od' id='od' size='5' />
        <label for='do'>
          Do/Bis
        </label>
        <input type='text' name='do' id='do' size='5' onblur="minutes(document.getElementById('od').value,this.value);" />
        <label for='pausa'>
          Pause
        </label>
        <input type='text' name='pause' id='pause' size='4' value='0' onchange="makePause(this.value);" />
        <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for='vz'  style='margin-left:150px;'>
          Verb.-Zeit / Spotř. čas
        </label>
        <input type='text' name='verbZeit' id='verbZeit' size='10' />
        <br />
        <br />
        <label for='tatzeit' style='margin-left:230px;'>Vz Aby/ Čas Abydos</label>
        <input type="text" name="tatzeit" id="tatzeit" size="4" value='0' />
            <input type="text" name="kz" id="kz" size="4" value='0' readonly="readonly"  style="display:none;" />
        <br />
        <br />                
        <input type='submit' Value='Neu/Nový' id='neu' /> <input type='button' value='Zrušit/Abbruch' id='reset' onClick="location.href='./drueckMa.php'" />
        <input type="button" value="Ende/Konec" id="konec" style="width: 100px; margin-left: 600px; margin-bottom:3px;" onClick="location.href='../index.php'" />
        </fieldset>
        </form>
  </body>
</html>
