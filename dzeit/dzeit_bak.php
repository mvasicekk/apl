<?
 session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <title>
      Dzeit
    </title>
<link rel="stylesheet" href="./styl.css" type="text/css">
<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "ajaxgold.js"></script>
</script>
<?
include "../fns_dotazy.php";

dbConnect();

?>
  </head>
<body onLoad="document.formDzeit.PersNr.focus();">
  
<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
</div>

<div align="center" id="podheader">
<?
  if(isset($_SESSION['user']) && isset($_SESSION['level']))
  {
	$user=$_SESSION['user'];
	$level=$_SESSION['level'];
	echo "$user  level=$level<a href=\"../index.php?akce=logout\">abmelden/odhlasit</a>";
	
  }
  else
  {
	echo "Benutzer nicht angemeldet/neprihlaseny uzivatel";
  }
?>
</div>
<div id="formular_header">
Anwesenheiterfassung / zadani dochazky
</div>

<div id="formular_telo">
<table cellpadding="10px" class="formulartable" border="1">
	<form method="post" action='save.php' name="formDzeit" onsubmit="beforeSubmit();">
    <tr>
	<td>
		<label>
        PersNr/Osobní Číslo
        </label>
        <input type="text" name="PersNr" id="PersNr" maxlength="5" size="5" onblur="stahniData(this.value);">
        <label>
        Name / jmeno
        </label>
        <input type="text" name="persName" id="persName" size="25" readonly>
        <label>
        Schicht/Smena
        </label>
        <input type="text" onfocus="this.select();" name="Schicht" id="Schicht" maxlength="2" size="2">
    </td>
	</tr>
	<tr>
	<td>
        <label>
        Datum 
        </label>
        <input type="text" name="Datum" id="Datum" size="10">
		<label>
        Von 
		</label>
        <input type="text" name="Von" id="Von" size="4" Value="00:00" onfocus="this.value='';" >
        <label>
        Bis
        </label>
        <input type="text" name="Bis" id="Bis" size="4" Value="00:00" onfocus="this.value='';" onBlur="pauza();">
        <label>
        Pause 1
        </label>
        <input type="text" name="pause1" id="pause1" size="4" >
        <label>
        Pause 2
        </label>
        <input type="text" name="pause2" id="pause2" size="4">
    </td>
	</tr>
	<tr>
	<td>
    <label>
    Tat
    </label>
    <select name="tatigkeit" id="tatigkeit">
<?      
    $res = mysql_query("select * from dtattypen");
        $i = 1;
        while($tattyp = mysql_fetch_array($res)){
        if($tattyp["tat"] == 'a'){
        echo "<option value='".$tattyp["tat"]."' selected='selected'>".$tattyp["tat"]."</option>";
        }else{
        echo "<option value='".$tattyp["tat"]."'>".$tattyp["tat"]."</option>";
        }
        $i++;
        } 
        mysql_close();
          ?>
        </select>
        <label >
          Stunden
        </label>
        <input type="text" name="stunden" id="stunden" size="4" readonly="readonly">
        <label >
        Stunden mit pauze
        </label>
        <input type="text" name="stundenmit" id="stundenmit" size="4" readonly="readonly">
        <input type="submit" value="Weiter/Daląí" id="weiter">
        <input type="button" value="Ende/Konec" id="konec"  onClick="location.href='../index.php'">
	</td>
	</tr>
      </form>
	  </table>
</div>
  </body>
</html>
