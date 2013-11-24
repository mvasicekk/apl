<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Dzeit
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>

<!-- YUI stuff -->
<script type="text/javascript" src="../js/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../js/connection/connection-min.js"></script>


<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
<script type = "text/javascript" src = "../js/jquery.js"></script>

<script  type="text/javascript" src="js_functions.js"></script>
</head>

<body onLoad="document.formDzeit.PersNr.focus();">
{popup_init src="../js/overlib.js"}
  
<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
</div>



<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
Anwesenheiterfassung / zadani dochazky
</div>

<div id="formular_telo">
<table cellpadding="10px" class="formulartable" border="1">
    <form method="" action='' name="formDzeit" onsubmit="">
    <tr>
	<td>
		<label>
        PersNr/Osobní cislo
        </label>
<!--        <input type="text" name="PersNr" id="PersNr" maxlength="5" size="5" onblur="getDataReturnText('./operace.php?persnr='+this.value, pisjmeno);">-->
        <input type="text" name="PersNr" id="PersNr" maxlength="5" size="5"/>
        <input type='hidden' id='cva' size='5'/>
        <label>
        Name / jmeno
        </label>
        <input type="text" name="persName" id="persName" size="25" readonly>
        <label>
        Schicht/Smena
        </label>
        <input type="text" name="Schicht" id="Schicht" maxlength="2" size="2">
    </td>
	</tr>
	<tr>
	<td>
        <label>
        Datum 
        </label>
            <input type="text" name="Datum" id="Datum" value="{$datumvalue}" size="10">
<!--            <input type="text" name="Datum" id="Datum" onfocus="this.select();" onblur="getDataReturnText('./validate.php?what=datum&value='+this.value+'&persnr='+document.getElementById('PersNr').value, refreshdatum);"  value="{$datumvalue}" size="10">-->
            <input type="hidden" name="datumold" id="datumold"/>
            <input type="hidden" name="refreshedok" id="refreshedok"/>
        
		<label>
        Von 
		</label>
        <input type="text" name="Von" id="Von" size="4" Value="00:00" onfocus="this.select();" onblur="refreshvon();">
        <label>
        Bis
        </label>
        <input type="text" name="Bis" id="Bis" size="4" Value="00:00" onfocus="this.select();" onBlur="pauza();">
        <label>
        Pause 1
        </label>
        <input type="text" onblur="js_validate_float(this);" name="pause1" id="pause1" size="4" onfocus="this.select();" value="0">
        <label>
        Pause 2
        </label>
        <input type="text" onblur="js_validate_float(this);" name="pause2" id="pause2" size="4" onfocus="this.select();" value="0">
    </td>
	</tr>
	<tr>
	<td>
    <label>
    Tat
    </label>
    <select name="tatigkeit" id="tatigkeit">
	{html_options values=$tattypvalue output=$tattypoutput selected=""}
    </select>
        <label >
          Stunden
        </label>
        <input type="text" name="stunden" id="stunden" size="4" readonly="readonly">
        <input type="button"
                onclick="disableweiter();YAHOO.util.Connect.asyncRequest('GET','./dzeitupdate.php?persnr='
                        +encodeControlValue('PersNr')
                        +'&schicht='
                        +encodeControlValue('Schicht')
                        +'&datum='
                        +encodeControlValue('Datum')
                        +'&von='
                        +encodeControlValue('Von')
                        +'&bis='
                        +encodeControlValue('Bis')
                        +'&pause1='
                        +encodeControlValue('pause1')
                        +'&pause2='
                        +encodeControlValue('pause2')
                        +'&tatigkeit='
                        +encodeSelectControlValue('tatigkeit')
                        +'&stunden='
                        +encodeControlValue('stunden')

                        , dzeitupdate);"
                value="Weiter/Dalsi" id="weiter">

        <input type="button" value="Ende/Konec" id="konec"  onClick="location.href='../index.php'">
	</td>
	<td>
	    <input type="button" value="AnwesenheitTabelle" id="anwtableshow" />
	</td>
	</tr>
      </form>
	  </table>
</div>

<!--<div id="debuginfo"></div>-->

<div id='dzeit_poslednizaznamy'>

<table {popup text="v teto tabulce vidite seznam 10 naposledy zadanych hlaseni. Jsou serazeny od nejnovejsiho ke starsimu..."} class='posledni_table' id='dzeittab'>
		<tr class='posledni_table_header'>
			<td>PersNr</td>
			<td>Datum</td>
			<td align='right'>Stunden</td>
			<td align='right'>Schicht</td>
			<td align='right'>OE</td>
			<td align='right'>Von</td>
			<td align='right'>Bis</td>
			<td align='right'>Pause1</td>
			<td align='right'>Pause2</td>
			<td align='right'>erfasst von</td>
			<td align='right'>stamp</td>
		</tr>
		{foreach from=$lastrows item=polozka}
		<tr id='tr{$polozka.id}' class='{cycle values="lichy,sudy"}'>
			<td>{$polozka.persnr}</td>
			<td>{$polozka.datum}</td>
			<td align='right'>{$polozka.stunden}</td>
			<td align='right'>{$polozka.schicht}</td>
			<td align='right'>{$polozka.oe}</td>
			<td align='right'>{$polozka.von}</td>
			<td align='right'>{$polozka.bis}</td>
			<td align='right'>{$polozka.pause1}</td>
			<td align='right'>{$polozka.pause2}</td>
			<td align='right'>{$polozka.user}</td>
			<td align='right'>{$polozka.stamp}</td>
		</tr>
		{/foreach}
		</table>
</div>



<div id="form_footer_tlacitka_reporty">
			<input id="S160" onClick="location.href='../get_parameters.php?popisky=Datum;Schicht von;Schicht bis&promenne=datum;schicht_von;schicht_bis&values={$predchozi_den};1;99999&report=S160'" class='reportbutton' type="button"  name="S160" value="S160 - Leistung-Tag-Anwesenheit"/>
<!--			<input id="S168" onClick="location.href='../get_parameters.php?popisky=Datum;Schicht von;Schicht bis&promenne=datum;schicht_von;schicht_bis&values={$predchozi_den};1;99999&report=S168'" class='reportbutton' type="button"  name="S168" value="S168 - Leistung-Tag-Anwesenheit"/>-->
                        <input id="S168" onClick="location.href='../get_parameters.php?popisky=Datum&promenne=datum&values={$predchozi_den}&report=S168'" class='reportbutton' type="button"  name="S168" value="S168 - Leistung->Anwesenheit"/>
                        <input id="dkfzfahrten" acturl="./dkfzfahrten.php" class='reportbutton' type="button"  name="dkfzfahrten" value="KFZ Fahrten / Auta - jizdy"/>
</div>
  </body>
</html>
