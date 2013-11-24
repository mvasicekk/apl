<?
  require "../fns_dotazy.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1250" />
  
  <meta name="generator" content="PSPad editor, www.pspad.com">
    <title>DRUECK
    </title>
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
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script charset="iso-8859-2" src="jsFunctions.js" type="text/javascript"></script>
<script charset="iso-8859-2" type="text/javascript">

function zjistiId(element,pole){
  for(i=0;i<=pole.length; i++){
    if(pole[i]== element){return i;}
  }
}

function checkCR() {
	
	//alert('onkeypress');
	//
	var oEvent = EventUtil.getEvent();
	
    var pole = new Array("AuftragsNr","datum","palnr","teil","taetMa","tatigkeit1","tatigkeit2","tatigkeit3","tatigkeit4","tatigkeit5","tatigkeit6","PersNr","Schicht","stueck","ausstueck","ausart","austyp","od","do","pause","neu");
    //var element =  window.event.srcElement.id;
	var element =  oEvent.target.id;
    
	//alert(element);
    //var event  = (window.event) ? window.event : ((window.event) ? window.event : null);
    //var node = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);


    if (oEvent.keyCode == 13) {
    var i = zjistiId(element, pole) +1;
      document.getElementById(pole[i]).focus();
      //document.getElementById(pole[i]).select();
      oEvent.preventDefault();
	  //return false;
	  }
    else{
      return true;
    } 
	
}
  
	EventUtil.addEventHandler(document, "keypress", checkCR);
	//EventUtil.addEventHandler(document.getElementById('AuftragsNr'),"blur",stahniData(document.getElementById('AuftragsNr').value,'0','0','palNr'));
    

</script>


  </head>
  <body onload="document.getElementById('AuftragsNr').focus();">
  
  <form action="save.php" method="POST" accept-charset="iso-8859-2">
    <fieldset title="Drueck">
      <legend title="Drueck">
        Drueck
      </legend>
      <label for="AuftragsNr">
        AuftragsNr/Číslo zakázky
      </label>
      <input type="text" name="AuftragsNr" id="AuftragsNr" onblur="stahniData(this.value,'0','0','palNr');">            
      <label for="datum">
        Datum
      </label>
      <input type="text" onfocus="this.select();" name="datum" id='datum' size='12' value='<? echo date("j.n.Y")?>' >
      <label for="palnr">
        Pal Nr.
      </label>
      <select name="palnr" id='palnr' onblur="stahniData(document.getElementById('AuftragsNr').value, this.value, '0', 'teilNr');" >
      </select>
      <br />
      <label for="teil">
        Teil
      </label>
      <select name="teil" id='teil' onblur="stahniData(document.getElementById('AuftragsNr').value, document.getElementById('palnr').value,this.value,'tatigkeit');" >
      </select>      
      <input type="text" name="taetMa" size="4" id="taetMa" value="0" onblur="jumpTo(this.value);"  onFocus='this.select();' />
      
      <label for="tatigkeit1">
        Taetigkeit nr/Číslo Operace
      </label>
      <select name="tatigkeit1" id='tatigkeit1' onblur="stahniDataMinuty(document.getElementById('AuftragsNr').value, document.getElementById('teil').value,this.value, 'min', 1);" >
            <option value="0">0</option>
      </select>                                                                                                                                                         
      <select name="tatigkeit2" id='tatigkeit2' onblur="stahniDataMinuty(document.getElementById('AuftragsNr').value, document.getElementById('teil').value,this.value, 'min', 2);" >
            <option value="0">0</option>
      </select>                                                                                                                                                         
      <select name="tatigkeit3" id='tatigkeit3' onblur="stahniDataMinuty(document.getElementById('AuftragsNr').value, document.getElementById('teil').value,this.value, 'min', 3);" >
            <option value="0">0</option>
      </select>                                                                                                                                                         
      <select name="tatigkeit4" id='tatigkeit4' onblur="stahniDataMinuty(document.getElementById('AuftragsNr').value, document.getElementById('teil').value,this.value, 'min', 4);" >
            <option value="0">0</option>
      </select>                                                                                                                                                         
      <select name="tatigkeit5" id='tatigkeit5' onblur="stahniDataMinuty(document.getElementById('AuftragsNr').value, document.getElementById('teil').value,this.value, 'min', 5);" >
            <option value="0">0</option>
      </select>
      <select name="tatigkeit6" id='tatigkeit6' onblur="stahniDataMinuty(document.getElementById('AuftragsNr').value, document.getElementById('teil').value,this.value, 'min', 6); zeitSumm();">
            <option value="0">0</option>
      </select>
      <br />
      <br />
      <table>
        <tr>
          <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
          </td>
          <td>
          </td>
          <td>
          </td>
          <td colspan='3'>
            <input type="text" name="tat1zeit" id="tat1zeit" size="4" value='0' readonly="readonly" />
            <input type="text" name="kz1" id="kz1" size="4" value='0' readonly="readonly"  style="display:none;" />
            <input type="text" name="tat2zeit" id="tat2zeit" size="4" value='0' readonly="readonly" />
            <input type="text" name="kz2" id="kz2" size="4" value='0' readonly="readonly"  style="display:none;" />
            <input type="text" name="tat3zeit" id="tat3zeit" size="4" value='0' readonly="readonly" />
            <input type="text" name="kz3" id="kz3" size="4" value='0' readonly="readonly"  style="display:none;" />
            <input type="text" name="tat4zeit" id="tat4zeit" size="4" value='0' readonly="readonly" />
            <input type="text" name="kz4" id="kz4" size="4" value='0' readonly="readonly"  style="display:none;" />
            <input type="text" name="tat5zeit" id="tat5zeit" size="4" value='0' readonly="readonly" />
            <input type="text" name="kz5" id="kz5" size="4" value='0' readonly="readonly"  style="display:none;" />
            <input type="text" name="tat6zeit" id="tat6zeit" size="4" value='0' readonly="readonly" />
            <input type="text" name="kz6" id="kz6" size="4" value='0' readonly="readonly"  style="display:none;" />            &nbsp;&nbsp;&nbsp;
            <input type="text" name="tatZeitSumm" id="tatZeitSumm" size="4" readonly="readonly" />
          </td>
        </tr>
        <tr>
          <td>
          </td>
          <td>
          </td>
          <td>
            <input type="text" name="beschreibung" id='beschreibung' size="25" readonly="readonly" />
          </td>
          <td>            
          </td>
          <td align='right'>
            <input type="text" name="VzAby" id="VzAby" size="3" readonly="readonly" class='pocet' value='0' />            
          </td>
          <td rowspan='2'>
            <input type="text" name="vykon" id='vykon' size="3" readonly="readonly" class='pocet' value='0' />%
             
          </td>
        </tr>
        <tr>
          <td>
          </td>
          <td>
          </td>
          <td>
          </td>
          <td>
          </td>
          <td align='right'>
            <input type="text" name="VerbZ" id="VerbZ" size="3" readonly="readonly" class='pocet' value='0' >
            
          </td>
        </tr>
      </table>      
      <label for='PersNr'>
        PersNr
      </label>
      <input type='text' onfocus="this.select();" name='PersNr' id='PersNr' size='5' onblur="stahniDataPers(this.value,'pers');" >
      <label for='Schicht'>
        Schicht
      </label>
      <input type='text' name='Schicht' id='Schicht' size='2' onfocus="this.select();">
      <input type='text' name='PersName' id='PersName' size='25' readonly='readonly' style='background-color: rgb(202,202,202); border:none;' onfocus="this.select();">
      <br />
      <label for='stueck'>
        Stueck
      </label>
      <input onfocus="this.select();" type='text' name='stueck' id='stueck' size='5' onblur="minutySumme();" >      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <label for='ausstueck'>
        Auss-Stueck/Zmetek
      </label>
      <input onfocus="this.select();" type='text' name='ausstueck' id='ausstueck' value='0' size='5'/>
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
        <select name='austyp' id='austyp' onblur='updateVykon(this.value, document.getElementById("ausstueck"));'>
          <option value='0'>0</option>
        </select>
        <br />
        <br />
               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for='od'>
          Od/Von
        </label>
        <input onfocus="this.select();" type='text' name='od' id='od' size='5' />
        <label for='do'>
          Do/Bis
        </label>
        <input onfocus="this.select();" type='text' name='do' id='do' size='5' onblur="minutes(document.getElementById('od').value,this.value,document.getElementById('PersNr').value);" >
        <label for='pause'>
          Pause
        </label>
        <input onfocus="this.select();" type='text' name='pause' id='pause' size='4' value='0' onblur="makePause(this.value);" >
        <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for='verbZeit'>
          Verb.-Zeit / Spotř. čas
        </label>
        <input onfocus="this.select();" type='text' name='verbZeit' id='verbZeit' size='10' >
        <br />
        <br />
        <br />
        <br />                
        <input type='submit' Value='Neu/Nový' id='neu' /> <input type='button' value='Zrušit/Abbruch' id='reset' onClick="location.href='./drueck.php'" />
        <input type="button" value="Ende/Konec" id="konec" style="width: 100px; margin-left: 600px; margin-bottom:3px;" onClick="location.href='../index.php'" />
        </fieldset>
        </form>
  </body>
</html>
