<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      DKsd
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">

<script type="text/javascript" src="./js/init_controls.js"></script>
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
</head>

<body onload="">
{popup_init src="../js/overlib.js"}

<!-- 
<div id='souradnice'>
souradnice
</div>
-->

<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
Kunden pflegen / Sprava zakazniku
</div>

<div id="formular_telo">


<form method="post" action='' id='dksd' name="dksd" onsubmit="">
	<fieldset id='dksdfieldset'>
		<legend>Kunden/zakaznici</legend>
<table>
<tr>
<td>
			<label for="kunde">Kunde/zakaznik</label>
			<input readonly='readonly'  maxlength='3' size="3" type="text" id="kunde" name="kunde" value="{$kunde_value}"/>
			<br/>
			
			<label for="name1">Name1/jmeno1</label>
			<input onblur="validatename1(this.id);" maxlength='40' size='35' type="text" id="name1" name="name1" value="{$name1_value}"/>
			<input size='45' type='text' class='hidden' id='name1_failed' value='' />

			<label for="name1">Name2/jmeno2</label>
			<input onblur="" maxlength='40' size='35' type="text" id="name2" name="name2" value="{$name2_value}"/>
			<input size='45' type='text' class='hidden' id='name2_failed' value='' />
			<br/>
			
			<label for="strasse">Strasse</label>
			<input onblur="validatestrasse(this.id);" maxlength='40' size='30' type="text" id="strasse" name="strasse" value="{$strasse_value}"/>
			<input size='45' type='text' class='hidden' id='strasse_failed' value='' />

			<label for="plz">PLZ</label>
			<input onblur="validateplz(this.id);" maxlength='6' size='5' type="text" id="plz" name="plz" value="{$plz_value}"/>
			<input size='45' type='text' class='hidden' id='plz_failed' value='' />

			<label for="ort">ORT</label>
			<input onblur="" maxlength='40' size='35' type="text" id="ort" name="ort" value="{$ort_value}"/>
			<input size='45' type='text' class='hidden' id='ort_failed' value='' />

			<label for="land">Land</label>
			<input onblur="" maxlength='3' size='3' type="text" id="land" name="land" value="{$land_value}"/>
			<input size='45' type='text' class='hidden' id='land_failed' value='' />
			<br/>
			
			<label for="tel">tel</label>
			<input onblur="" maxlength='20' size='16' type="text" id="tel" name="tel" value="{$tel_value}"/>
			<input size='45' type='text' class='hidden' id='tel_failed' value='' />

			<label for="fax">fax</label>
			<input onblur="" maxlength='20' size='16' type="text" id="fax" name="fax" value="{$fax_value}"/>
			<input size='45' type='text' class='hidden' id='fax_failed' value='' />
			
			<label for="ico">ico</label>
			<input onblur="validateico(this.id);" maxlength='11' size='16' type="text" id="ico" name="ico" value="{$ico_value}"/>
			<input size='45' type='text' class='hidden' id='ico_failed' value='' />

			<label for="dic">dic</label>
			<input onblur="" maxlength='50' size='16' type="text" id="dic" name="dic" value="{$dic_value}"/>
			<input size='45' type='text' class='hidden' id='dic_failed' value='' />

			<label for="konto">konto</label>
			<select id="konto" name="konto">
				{html_options options=$konto_options selected=$konto_selected}
			</select> 
			<input size='45' type='text' class='hidden' id='konto_failed' value='' />

			<label for="waehrkz">waehrkz</label>
			<input onblur="" maxlength='5' size='5' type="text" id="waehrkz" name="waehrkz" value="{$waehrkz_value}"/>
			<input size='45' type='text' class='hidden' id='waehrkz_failed' value='' />

			<label for="preisvzh">preisvzh</label>
			<input onblur="validatepreisvzh(this.id);" maxlength='16' size='5' type="text" id="preisvzh" name="preisvzh" value="{$preisvzh_value}"/>
			<input size='45' type='text' class='hidden' id='preisvzh_failed' value='' />

			<label for="preismin">preismin</label>
			<input onblur="validatepreismin(this.id);" maxlength='32' size='5' type="text" id="preismin" name="preismin" value="{$preismin_value}"/>
			<input size='45' type='text' class='hidden' id='preismin_failed' value='' />

</td>

<td>		


			<label for="rechanschr">rechanschr</label>
			<input onblur="getDataReturnXml('./validaterechanschr.php?kunde='+this.value+'&id='+this.id,validaterechanschr);" maxlength='3' size='3' type="text" id="rechanschr" name="rechanschr" value="{$rechanschr_value}"/>
			<input size='45' type='text' class='hidden' id='rechanschr_failed' value='' />

			<label for="preisfracht">preisfracht</label>
			<input onblur="js_validate_float(this);" maxlength='16' size='5' type="text" id="preisfracht" name="preisfracht" value="{$preisfracht_value}"/>
			<input size='45' type='text' class='hidden' id='preisfracht_failed' value='' />

			<label for="preiszoll">preiszoll</label>
			<input onblur="js_validate_float(this);" maxlength='16' size='5' type="text" id="preiszoll" name="preiszoll" value="{$preiszoll_value}"/>
			<input size='45' type='text' class='hidden' id='preiszoll_failed' value='' />

			<label for="preissonst">preissonst</label>
			<input onblur="js_validate_float(this);" maxlength='16' size='5' type="text" id="preissonst" name="preissonst" value="{$preissonst_value}"/>
			<input size='45' type='text' class='hidden' id='preissonst_failed' value='' />


			<label for="sachbearbeiteraby">sachbearbeiteraby</label>
			<input onblur="" maxlength='50' size='20' type="text" id="sachbearbeiteraby" name="sachbearbeiteraby" value="{$sachbearbeiteraby_value}"/>
			<input size='45' type='text' class='hidden' id='sachbearbeiteraby_failed' value='' />

			<label for="telaby">telaby</label>
			<input onblur="" maxlength='50' size='16' type="text" id="telaby" name="telaby" value="{$telaby_value}"/>
			<input size='45' type='text' class='hidden' id='telaby_failed' value='' />

			<label for="faxaby">faxaby</label>
			<input onblur="" maxlength='50' size='16' type="text" id="faxaby" name="faxaby" value="{$faxaby_value}"/>
			<input size='45' type='text' class='hidden' id='faxaby_failed' value='' />

			<label for="emailaby">emailaby</label>
			<input onblur="" maxlength='50' size='20' type="text" id="emailaby" name="emailaby" value="{$emailaby_value}"/>
			<input size='45' type='text' class='hidden' id='emailaby_failed' value='' />

			<label for="statcislo">statcislo</label>
			<input onblur="" maxlength='6' size='6' type="text" id="statcislo" name="statcislo" value="{$statcislo_value}"/>
			<input size='45' type='text' class='hidden' id='statcislo_failed' value='' />

			<label for="zahnlungziel">zahnlungziel</label>
			<input onblur="validatezahnlungziel(this.id);" maxlength='3' size='3' type="text" id="zahnlungziel" name="zahnlungziel" value="{$zahnlungziel_value}"/>
			<input size='45' type='text' class='hidden' id='zahnlungziel_failed' value='' />

			<label for="preis_runden">preis_runden</label>
			<input onblur="validatepreis_runden(this.id);" maxlength='1' size='1' type="text" id="preis_runden" name="preis_runden" value="{$preis_runden_value}"/>
			<input size='45' type='text' class='hidden' id='preis_runden_failed' value='' />

			<label for="kunden_stat_nr">kunden_stat_nr</label>
			<select id="kunden_stat_nr" name="kunden_stat_nr">
				{html_options options=$kunden_stat_nr_options selected=$kunden_stat_nr_selected}
			</select> 
			<input size='45' type='text' class='hidden' id='kunden_stat_nr_failed' value='' />
</td>
</tr>
</table>

{if $showDocsTable eq 1}	
<div style="height: 150px;width:100%;overflow-y: auto;border: 1px solid blue;">
    <table id='kddocs'>
    {foreach from=$docs item=doc}
    <tr>
	<td>{$doc.filename}</td>
	<td><a href='{$doc.url}'>offnen</a></td>
    </tr>
    {/foreach}
    </table>
</div>
{/if}	    

<table id='ehemaligepreise'>
	<thead>
	<tr>
		<th colspan='3'>Ehemalige Preise</th>
	</tr>
	<tr>
		<th>Preis</th>
		<th>Währung</th>
		<th>gültig bis</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$ehemaligepreise item=polozka}
	<tr>
		<td>{$polozka.preis}</td>
		<td>{$polozka.waehrung}</td>
		<td>{$polozka.gultigbis}</td>
	</tr>
	{/foreach}
	</tbody>
</table>

		</fieldset>
    </form>
 </div>

<div id='dkopf_form_footer'>
<table width='100%'>
<tr>
	<td>
		<input class='formularbutton' type='button' value='Kunde suchen / hledat zakaznika' onclick="document.location.href='kundesuchen.php';"/>
	</td>
	<td>
	</td>
	<td>
	</td>
	<td>
	</td>
</tr>
<tr>
	<td>
	</td>
	<td>
		<input id='teil_save' class='formularbutton' type='button' value='Aenderungen speichern' 
		onclick="getDataReturnXml('./save_dksd.php?kunde='+encodeControlValue('kunde')
				+'&amp;name1='+encodeControlValue('name1')
				+'&amp;name2='+encodeControlValue('name2')
				+'&amp;strasse='+encodeControlValue('strasse')
				+'&amp;plz='+encodeControlValue('plz')
				+'&amp;ort='+encodeControlValue('ort')
				+'&amp;land='+encodeControlValue('land')	
				+'&amp;tel='+encodeControlValue('tel')
				+'&amp;fax='+encodeControlValue('fax')
				+'&amp;preisvzh='+encodeControlValue('preisvzh')
				+'&amp;rechanschr='+encodeControlValue('rechanschr')
				+'&amp;konto='+encodeSelectControlValue('konto')
				+'&amp;waehrkz='+encodeControlValue('waehrkz')
				+'&amp;preismin='+encodeControlValue('preismin')
				+'&amp;preisfracht='+encodeControlValue('preisfracht')
				+'&amp;preiszoll='+encodeControlValue('preiszoll')
				+'&amp;preissonst='+encodeControlValue('preissonst')
				+'&amp;ico='+encodeControlValue('ico')
				+'&amp;dic='+encodeControlValue('dic')
				+'&amp;sachbearbeiteraby='+encodeControlValue('sachbearbeiteraby')
				+'&amp;telaby='+encodeControlValue('telaby')
				+'&amp;faxaby='+encodeControlValue('faxaby')
				+'&amp;emailaby='+encodeControlValue('emailaby')
				+'&amp;statcislo='+encodeControlValue('statcislo')
				+'&amp;zahnlungziel='+encodeControlValue('zahnlungziel')
				+'&amp;preis_runden='+encodeControlValue('preis_runden')
				+'&amp;kunden_stat_nr='+encodeSelectControlValue('kunden_stat_nr')
				, saverefresh);"/>
	</td>
	<td>
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="document.location.href='../index.php';"/>
	</td>

</tr>
</div>

</body>
</html>
