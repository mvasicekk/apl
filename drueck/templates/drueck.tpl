<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Rückmeldungen / zadání výkonu
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<script type = "text/javascript" src = "../js/jquery.js"></script>
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script type="text/javascript" src="./js_functions.js"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
<script>
poleNapoveda = new Array("číslo zakázky",
							"datum výkonu",
							"číslo palety",
							"volba operace, hodnota >0 umožní zadat interní operaci",
							"1. operace",
							"2. operace",
							"3. operace",
							"4. operace",
							"5. operace",
							"6. operace",
							"osobní číslo",
							"směna",
							"počet dobrých kusů",
							"počet zmetků",
							"druh zmetku",
							"typ zmetku (2,4,6)",
							"čas výkonu od (zadává se bez dvojtečky)",
							"čas výkonu do (zadává se bez dvojtečky)",
							"pauza v minutách",
                                                        "organisationseinheit",
							"tlačítko pro uložení výkonu do databáze"
							);

// pole s hodnotama levelu pro enabled a display

var controls_levels_drueck = Array(
								'stornieren',2,2
							);

</script>

</head>

<body {if $stornoid>0} onLoad="init_drueck_form_edit('show');" {else} onLoad="init_level({$level},controls_levels_drueck);init_drueck_form('show');" {/if}>
{popup_init src="../js/overlib.js"}
  
<!--   
<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
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
{if $stornoid>0}
Drueck EDIT / editace položky
{else}
Rückmeldungen / zadání výkonu
{/if}
</div>

{if $prihlasen}
<div id="formular_telo">

<table cellpadding="5px" class="formulartable" border="0" cellpadding='0' cellspacing='0'>
	<form autocomplete="off" method="post" action='' name="auftragsuchen_formular" onsubmit="">
    <tr>
		<td>
	
			<label for="auftragsnr"><b><u>A</u></b>uftragsnr</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if} accesskey='a' {popup text="zde zadejte cislo zakazky podle pracovniho papiru"} onblur="getDataReturnXml('./validate_auftragsnr.php?&controlid='+this.id+'&value='+this.value, validate_auftragsnr);"  onfocus='markfocus(this);this.select();' maxlength='8' size="8" type="text" id="auftragsnr" name="auftragsnr" value="{$auftragsnr_value}"/>
                        <input type="text" disabled="disabled" id="pg" value="" size="1"/>
                        <input type="text" disabled="disabled" id="kunde" value="" size="3"/>
		</td>
	
		<td colspan='1'>
		
			<label for="pal"><b><u>P</u></b>alette / paleta</label><br>
			<input  acturl='./onPalUpdate.php' {if $exportFlag>0}disabled='disabled'{/if} accesskey='p' {popup text="zadejte paletu dle prac. papiru,<br>muzete zadat i paletu, ktera jiz byla vyexportovana, pro tuto paletu muzete zadat pouze interni operaci."} onblur="getDataReturnXml('./validate_pal.php?&controlid='+this.id+'&value='+this.value+'&auftragsnr_value='+document.getElementById('auftragsnr').value,validate_pal);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="pal" name="pal" value="{$pal_value}"/>
			
		</td>
		
		<td>
		
			<label for="datum"><b><u>D</u></b>atum</label><br>
			<input  accesskey='d' {popup text="zadejte datum podle pracovniho papiru"} onfocus='markfocus(this);this.select();' onblur="getDataReturnText('./validate_datum.php?what=datum&value='+this.value+'&controlid='+this.id, refreshdatum);" maxlength='10' size="10" type="text" id="datum" name="datum" value="{$datum_value}"/>
			
		</td>

	
		<td colspan='6'>
		
			<label for="tatnrarray">Mögliche Tätigkeiten</label><br>
			<input disabled {popup text="zde vidite seznam operaci ze kterych muzete pro dany dil a paletu vybirat."} size="90" type="text" id="tatnrarray" name="tatnrarray" value="{$tatnrarray_value}"/>
			<input type='button' id='showaplinfo' accesskey='i' title='zkratka Alt+i' value='Info' onclick="getDataReturnXml('./showaplinfo.php?&controlid='+this.id,refreshaplinfo);">			
		</td>

	</tr>
	<tr>
	
		<td>
		
			<label for="teil">Teil / díl</label><br>
			<input disabled onfocus='markfocus(this);this.select();' maxlength='10' size="10" type="text" id="teil" name="teil" value="{$teil_value}"/>
			
		</td>

		<td colspan='2'>
		
			<label for="mehr">Mehr</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if} {popup text="zde zadejte cislo vetsi nez 0 v pripade, ze chcete zadat vicepraci. Pro operaci zadanou v zakazce zde nechte nulu."} onblur="getDataReturnXml('./validate_mehr.php?&controlid='+this.id+'&value='+this.value+'&teil_value='+document.getElementById('teil').value+'&auftragsnr_value='+document.getElementById('auftragsnr').value+'&pal_value='+document.getElementById('pal').value+'&teilbez_value='+document.getElementById('teilbez').value,validate_mehr);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="mehr" name="mehr" value="{$mehr_value}"/>
			
		</td>

		<td>
		
			<label for="tat1">Tat / operace</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if} onkeyup="abgnr_onkeyup(this.id,this.value);" onblur="document.getElementById('neu').disabled=true;getDataReturnXml('./validate_abgnr.php?&mehr_value='+document.getElementById('mehr').value+'&controlid='+this.id+'&value='+this.value+'&auftragsnr_value='+document.getElementById('auftragsnr').value+'&pal_value='+document.getElementById('pal').value+'&teil_value='+document.getElementById('teil').value,validate_abgnr1);"	onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="tat1" name="tat1" value="{$tat1_value}"/>
			
		</td>


		<td>
		
			<label for="tat2">2</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if}  onkeyup="abgnr_onkeyup(this.id,this.value);" onblur="getDataReturnXml('./validate_abgnr.php?&mehr_value='+document.getElementById('mehr').value+'&controlid='+this.id+'&value='+this.value+'&auftragsnr_value='+document.getElementById('auftragsnr').value+'&pal_value='+document.getElementById('pal').value+'&teil_value='+document.getElementById('teil').value,validate_abgnr1);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="tat2" name="tat2" value="{$tat2_value}"/>
			
		</td>

		<td>
		
			<label for="tat3">3</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if}  onkeyup="abgnr_onkeyup(this.id,this.value);" onblur="getDataReturnXml('./validate_abgnr.php?&mehr_value='+document.getElementById('mehr').value+'&controlid='+this.id+'&value='+this.value+'&auftragsnr_value='+document.getElementById('auftragsnr').value+'&pal_value='+document.getElementById('pal').value+'&teil_value='+document.getElementById('teil').value,validate_abgnr1);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="tat3" name="tat3" value="{$tat3_value}"/>
			
		</td>

		<td>
		
			<label for="tat4">4</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if}  onkeyup="abgnr_onkeyup(this.id,this.value);" onblur="getDataReturnXml('./validate_abgnr.php?&mehr_value='+document.getElementById('mehr').value+'&controlid='+this.id+'&value='+this.value+'&auftragsnr_value='+document.getElementById('auftragsnr').value+'&pal_value='+document.getElementById('pal').value+'&teil_value='+document.getElementById('teil').value,validate_abgnr1);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="tat4" name="tat4" value="{$tat4_value}"/>
			
		</td>

		<td>
		
			<label for="tat5">5</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if}  onkeyup="abgnr_onkeyup(this.id,this.value);" onblur="getDataReturnXml('./validate_abgnr.php?&mehr_value='+document.getElementById('mehr').value+'&controlid='+this.id+'&value='+this.value+'&auftragsnr_value='+document.getElementById('auftragsnr').value+'&pal_value='+document.getElementById('pal').value+'&teil_value='+document.getElementById('teil').value,validate_abgnr1);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="tat5" name="tat5" value="{$tat5_value}"/>
			
		</td>

		<td>
		
			<label for="tat6">6</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if}  onkeyup="abgnr_onkeyup(this.id,this.value);" onblur="getDataReturnXml('./validate_abgnr.php?&mehr_value='+document.getElementById('mehr').value+'&controlid='+this.id+'&value='+this.value+'&auftragsnr_value='+document.getElementById('auftragsnr').value+'&pal_value='+document.getElementById('pal').value+'&teil_value='+document.getElementById('teil').value,validate_abgnr1);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="tat6" name="tat6" value="{$tat6_value}"/>
			
		</td>

	</tr>


	<tr>
	
		<td colspan='3'>
		
			<input disabled maxlength='30' size="30" type="text" id="teilbez" name="teilbez" value="{$teilbez_value}"/>
			
			<input maxlength='6' size="4" type="hidden" id="tat1_kdmin" name="tat1_kdmin" value="{$tat1_kdmin_value}"/>
			<input maxlength='6' size="4" type="hidden" id="tat2_kdmin" name="tat2_kdmin" value="{$tat2_kdmin_value}"/>
			<input maxlength='6' size="4" type="hidden" id="tat3_kdmin" name="tat3_kdmin" value="{$tat3_kdmin_value}"/>
			<input maxlength='6' size="4" type="hidden" id="tat4_kdmin" name="tat4_kdmin" value="{$tat4_kdmin_value}"/>
			<input maxlength='6' size="4" type="hidden" id="tat5_kdmin" name="tat5_kdmin" value="{$tat5_kdmin_value}"/>
			<input maxlength='6' size="4" type="hidden" id="tat6_kdmin" name="tat6_kdmin" value="{$tat6_kdmin_value}"/>
			
		</td>

		<td>
		
			<input disabled maxlength='6' size="4" type="text" id="tat1_abymin" name="tat1_abymin" value="{$tat1_abymin_value}"/>
			
		</td>


		<td>
		
			<input disabled maxlength='6' size="4" type="text" id="tat2_abymin" name="tat2_abymin" value="{$tat2_abymin_value}"/>
			
		</td>

		<td>
		
			<input disabled maxlength='6' size="4" type="text" id="tat3_abymin" name="tat3_abymin" value="{$tat3_abymin_value}"/>
			
		</td>

		<td>
		
			<input disabled maxlength='6' size="4" type="text" id="tat4_abymin" name="tat4_abymin" value="{$tat4_abymin_value}"/>
			
		</td>

		<td>
		
			<input disabled maxlength='6' size="4" type="text" id="tat5_abymin" name="tat5_abymin" value="{$tat5_abymin_value}"/>
			
		</td>

		<td>
		
			<input disabled maxlength='6' size="4" type="text" id="tat6_abymin" name="tat6_abymin" value="{$tat6_abymin_value}"/>
			
		</td>

	</tr>

	<tr>
	
		<td>
			<input disabled='disabled' onclick="getDataReturnXml('./showattachment.php?controlid='+this.id+'&value='+this.value+'&teil_value='+document.getElementById('teil').value+'&typ=PPA',showattachment);" type="button" id="ppa" name="ppa" value="PPA"/>		
			
		</td>

                <td colspan="2">
                    <div id="ambeweingabe">
                        <label for="amnr">ArbeitsmittelNr</label><br>
                        <input  accesskey='a' {popup text="cislo pracovniho prostredku"} onblur="getDataReturnXml('./validate_amnr.php?&controlid='+this.id+'&value='+this.value,validate_amnr);" onfocus='markfocus(this);this.select();' maxlength='7' size="7" type="text" id="amnr" name="amnr" value="{$amnr_value}"/><br>
			<input disabled size="20" type="text" id="amnrpopis" name="amnrpopis" value="{$amnrpopis_value}"/><br>
			<label for="ausstk">Ausgabe<b><u>s</u></b>tück / vydané kusy</label><br>
			<input  accesskey='s' onblur="js_validate_float(this);" onfocus='markfocus(this);this.select();' maxlength='6' size="6" type="text" id="ausstk" name="austk" value="{$ausstk_value}"/><br>
			<label for="rueckstk">Rückgabe s<b><u>t</u></b>ück / vrácené kusy</label><br>
			<input  accesskey='t' onblur="js_validate_float(this);" onfocus='markfocus(this);this.select();' maxlength='6' size="6" type="text" id="rueckstk" name="rueckstk" value="{$rueckstk_value}"/>
                    </div>
                </td>
		<td colspan='2'>
		
			<input disabled maxlength='30' size="30" type="text" id="tatbez" name="tatbez" value="{$tatbez_value}"/>
			
		</td>

		<td colspan='4' bgcolor='lightgrey'>
		
			<label for="sumvzaby">VaAby</label>
			<input class='noborder' {popup text="muzete stisknout Alt+a pro presun do tohoto policka"} disabled size="4" type="text" id="sumvzaby" name="sumvzaby" value="{$sumvzaby_value}"/>
			
			<label for="sumverb">VerbZ</label>
			<input class='noborder' disabled size="4" type="text" id="sumverb" name="sumverb" value="{$sumverb_value}"/>
			
			<label for="leist_procent">Leistung</label>
			<input class='noborder' disabled size="3" type="text" id="leist_procent" name="leist_procent" value="{$leist_procent_value}"/>%
			
		</td>

	</tr>

	<tr>
	
		<td>
		
			<label for="persnr">P<b><u>e</u></b>rsnr</label><br>
			<input  accesskey='e' onblur="getDataReturnXml('./validate_persnr.php?&controlid='+this.id+'&value='+this.value+'&oe='+document.getElementById('oerabgnr').value+'&oeall='+document.getElementById('oeall').value+'&pg='+document.getElementById('pg').value,{if $stornoid>0}validate_persnredit{else}validate_persnr{/if});" onfocus='markfocus(this);this.select();' maxlength='5' size="5" type="text" id="persnr" name="persnr" value="{$persnr_value}"/>
                        <input type="text" id="regeloe" size="3" value="" disabled="disabled"/>
		</td>

		<td colspan='3'>
		
			<label for="persname"></label>
			<input disabled size="40" type="text" id="persname" name="persname" value="{$persname_value}"/>
			
		</td>

		<td>
		
			<label for="schicht"><b><u>S</u></b>chicht</label><br>
			<!-- <input  accesskey='s' onfocus="markfocus(this);this.select();" onblur="getDataReturnXml('./validate_schicht.php?controlid='+this.id+'&value='+this.value,validate_schicht);" maxlength='5' size="5" type="text" id="schicht" name="schicht" value="{$schicht_value}"/>  -->
			<input  accesskey='s' onfocus="markfocus(this);this.select();" onblur="js_validate_float(this);" maxlength='5' size="5" type="text" id="schicht" name="schicht" value="{$schicht_value}"/>
			
		</td>
	
	</tr>
	
	<tr>
	
		<td colspan='2'>
		
			<label for="stk">S<b><u>t</u></b>k / ks</label><br>
			<input  {if $exportFlag>0}disabled='disabled'{/if} accesskey='t' onblur="js_stk_validate();" onfocus='markfocus(this);this.select();' maxlength='6' size="6" type="text" id="stk" name="stk" value="{$stk_value}"/>
			
		</td>

		<td colspan='2'>
		
			<label for="auss_stk">A-Stk / z-ks</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if} onblur="js_auss_stk_validate();" onfocus='markfocus(this);this.select();' maxlength='6' size="6" type="text" id="auss_stk" name="auss_stk" value="{$auss_stk_value}"/>
			
		</td>
		
		<td colspan='2'>
		
			<label for="auss_art">A-Nr / z-č.</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if} onblur="getDataReturnXml('./validate_auss_art.php?controlid='+this.id+'&value='+this.value,validate_auss_art);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="auss_art" name="auss_art" value="{$auss_art_value}"/>
			
		</td>
	
		<td colspan='2'>
		
			<label for="auss_typ">A-Typ / z-typ</label><br>
			<input {if $exportFlag>0}disabled='disabled'{/if} onblur="getDataReturnXml('./validate_auss_typ.php?&controlid='+this.id+'&value='+this.value+'&auss_art='+document.getElementById('auss_art').value,validate_auss_typ);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="auss_typ" name="auss_typ" value="{$auss_typ_value}"/>
			
		</td>

	</tr>
	
	<tr>
	
		<td colspan='2'>
		
			<label for="vzaby_pro_stk">VzAby/Stk</label><br>
			<input onfocus="markfocus(this);this.select();" onblur="js_validate_float(this);prenos_vzaby(this);spocti_vykon();" disabled maxlength='6' size="6" type="text" id="vzaby_pro_stk" name="vzaby_pro_stk" value="{$vzaby_pro_stk_value}"/>
			
		</td>
		
		<td colspan='2'>
		
			<label for="von"><b><u>V</u></b>on / od</label><br>
			<input accesskey='v' title='Alt+v' onblur="refreshtime('von');getDataReturnXml('./validate_von.php?&controlid='+this.id+'&value='+this.value+'&bis='+document.getElementById('bis').value+'&datum='+document.getElementById('datum').value+'&persnr='+document.getElementById('persnr').value+'&pg='+document.getElementById('pg').value+'&oe='+document.getElementById('oerpersnr').value,validate_von);" onfocus='markfocus(this);this.select();' maxlength='5' size="5" type="text" id="von" name="von" value="{$von_value}"/>
			
		</td>
		
		<td colspan='1'>
		
			<label for="bis"><b><u>B</u></b>is / do</label><br>
			<input accesskey='b' title='Alt+b'  onblur="refreshtime('bis');getDataReturnXml('./validate_bis.php?&controlid='+this.id+'&value='+this.value+'&von='+document.getElementById('von').value+'&datum='+document.getElementById('datum').value+'&persnr='+document.getElementById('persnr').value,validate_bis);" onfocus='markfocus(this);this.select();' maxlength='5' size="5" type="text" id="bis" name="bis" value="{$bis_value}"/>
			
		</td>

		<td colspan='1'>
		
			<label for="pause">Pause</label><br>
			<input onblur="refreshpause();" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="pause" name="pause" value="{$pause_value}"/>
			
		</td>


                <td colspan='1'>
			<label for="oeselect">OE</label><br>
                        <select id="oeselect">
                            {html_options values=$oes output=$oes selected=$oeselected}
                        </select>
                        <input type="text" id="oeall" value="?GF" size="30"/><br>
                        <input type="text" id="oerabgnr" value="?GF" size="30"/><br>
                        <input type="text" id="oerpersnr" value="?GF" size="30"/><br>

                        <!--<input type="text" id="oe" value="?GF" size="10"/>-->
                        <input type="text" id="oesel" value="?GF" size="4"/>
		</td>

		<td colspan='1'>
		
			<label for="verb">verb</label><br>
			<input {if $exportFlag>0}{else}disabled='disabled'{/if} maxlength='4' size="4" type="text" id="verb" name="verb" value="{$verb_value}"/>
			
		</td>
	
	</tr>
	
	<tr>
	
		<td colspan='1'>
		{if $stornoid}
			<input accesskey='r' type="button" id="cancel" name="cancel" onclick='window.history.back();' value="Abbruch EDIT / zrušit"/>
		{else}
			<input accesskey='r' type="button" id="cancel" name="cancel" onclick='window.location.reload();' value="Abbruch / zrušit"/>
		{/if}
			
		</td>

		<td colspan='1'>
		{if $stornoid>0}		
			<input onfocus='markfocus(this);' onclick="disableneu();getDataReturnXml('./save_drueck_edit.php?auftragsnr='+encodeControlValue('auftragsnr')
												+'&datum='+encodeControlValue('datum')
												+'&pal='+encodeControlValue('pal')
												+'&teil='+encodeControlValue('teil')
												+'&mehr='+encodeControlValue('mehr')
												+'&tat1='+encodeControlValue('tat1')
												+'&tat2='+encodeControlValue('tat2')
												+'&tat3='+encodeControlValue('tat3')
												+'&tat4='+encodeControlValue('tat4')
												+'&tat5='+encodeControlValue('tat5')
												+'&tat6='+encodeControlValue('tat6')
												+'&tat1_abymin='+encodeControlValue('tat1_abymin')
												+'&tat2_abymin='+encodeControlValue('tat2_abymin')
												+'&tat3_abymin='+encodeControlValue('tat3_abymin')
												+'&tat4_abymin='+encodeControlValue('tat4_abymin')
												+'&tat5_abymin='+encodeControlValue('tat5_abymin')
												+'&tat6_abymin='+encodeControlValue('tat6_abymin')
												+'&tat1_kdmin='+encodeControlValue('tat1_kdmin')
												+'&tat2_kdmin='+encodeControlValue('tat2_kdmin')
												+'&tat3_kdmin='+encodeControlValue('tat3_kdmin')
												+'&tat4_kdmin='+encodeControlValue('tat4_kdmin')
												+'&tat5_kdmin='+encodeControlValue('tat5_kdmin')
												+'&tat6_kdmin='+encodeControlValue('tat6_kdmin')
												+'&persnr='+encodeControlValue('persnr')
												+'&schicht='+encodeControlValue('schicht')
                                                                                                +'&oe='+encodeSelectControlValue('oeselect')
												+'&stk='+encodeControlValue('stk')
												+'&auss_stk='+encodeControlValue('auss_stk')
												+'&auss_art='+encodeControlValue('auss_art')
												+'&auss_typ='+encodeControlValue('auss_typ')
												+'&von='+encodeControlValue('von')
												+'&bis='+encodeControlValue('bis')
												+'&pause='+encodeControlValue('pause')
												+'&verb='+encodeControlValue('verb')
												+'&vzaby_pro_stk='+encodeControlValue('vzaby_pro_stk')
												+'&stornoid={$stornoid}'
												+'&stornoidarray={$stornoidarray}'
												, saverefreshedit);" type="button" id="neu" name="neu" value="ENTER"/>
		{else}
			<input onclick="disableneu();getDataReturnXml('./save_drueck.php?auftragsnr='+encodeControlValue('auftragsnr')
												+'&datum='+encodeControlValue('datum')
												+'&pal='+encodeControlValue('pal')
												+'&teil='+encodeControlValue('teil')
												+'&mehr='+encodeControlValue('mehr')
												+'&tat1='+encodeControlValue('tat1')
												+'&tat2='+encodeControlValue('tat2')
												+'&tat3='+encodeControlValue('tat3')
												+'&tat4='+encodeControlValue('tat4')
												+'&tat5='+encodeControlValue('tat5')
												+'&tat6='+encodeControlValue('tat6')
												+'&tat1_abymin='+encodeControlValue('tat1_abymin')
												+'&tat2_abymin='+encodeControlValue('tat2_abymin')
												+'&tat3_abymin='+encodeControlValue('tat3_abymin')
												+'&tat4_abymin='+encodeControlValue('tat4_abymin')
												+'&tat5_abymin='+encodeControlValue('tat5_abymin')
												+'&tat6_abymin='+encodeControlValue('tat6_abymin')
												+'&tat1_kdmin='+encodeControlValue('tat1_kdmin')
												+'&tat2_kdmin='+encodeControlValue('tat2_kdmin')
												+'&tat3_kdmin='+encodeControlValue('tat3_kdmin')
												+'&tat4_kdmin='+encodeControlValue('tat4_kdmin')
												+'&tat5_kdmin='+encodeControlValue('tat5_kdmin')
												+'&tat6_kdmin='+encodeControlValue('tat6_kdmin')
                                                                                                +'&amnr='+encodeControlValue('amnr')
                                                                                                +'&ausstk='+encodeControlValue('ausstk')
                                                                                                +'&rueckstk='+encodeControlValue('rueckstk')
												+'&persnr='+encodeControlValue('persnr')
												+'&schicht='+encodeControlValue('schicht')
                                                                                                +'&oe='+encodeSelectControlValue('oeselect')
												+'&stk='+encodeControlValue('stk')
												+'&auss_stk='+encodeControlValue('auss_stk')
												+'&auss_art='+encodeControlValue('auss_art')
												+'&auss_typ='+encodeControlValue('auss_typ')
												+'&von='+encodeControlValue('von')
												+'&bis='+encodeControlValue('bis')
												+'&pause='+encodeControlValue('pause')
												+'&verb='+encodeControlValue('verb')
												+'&vzaby_pro_stk='+encodeControlValue('vzaby_pro_stk')
												, saverefresh);" type="button" id="neu" name="neu" value="ENTER"/>
		{/if}		
			
		</td>

		<td colspan='2'>
		{if !$stornoid}
			<input type="button" id="arbeitsmittelausgabe" onclick="window.location.href='../dambew/dambew.php';" name="arbeitsmittelausgabe" value="Arbeitsmittelausgabe"/>
		{/if}
		</td>

		<td colspan='3'>
			<input disabled='disabled' id='elementaktual' type='hidden' value='' size='30' />
		</td>	
	</tr>
   </form>
	</table>
<div id='chybacasu'>
</div>
<div id="palinfodiv">
    palinfo
</div>
		
<div id='stornodiv'>stornodiv</div>

<div id='aplinfo'>
	<input type='button' value='zavřít/schliessen' accesskey='q' title='zkratka Alt+q' onclick="document.getElementById('aplinfo').style.visibility='hidden';"/>
</div>
	
<div id='eaktualinfo'>
oiwjfofwij
</div>
	
</div>

<div id='drueck_poslednizaznamy'>

<table {popup text="v teto tabulce vidite seznam 5 naposledy zadanych zpetnych hlaseni. Jsou serazeny od nejnovejsiho ke starsimu..."} class='posledni_table' id='druecktab'>
		<tr class='dauftr_table_header'>
			<td>Auftragsnr</td>
			<td>Teil</td>
			<td>Pal</td>
			<td>TaetNr</td>
			<td>Stk</td>
			<td>Auss</td>
			<td>AArt</td>
			<td>ATyp</td>
			<td>VzKd</td>
			<td>VzAby</td>
			<td>Datum</td>
			<td>PersNr</td>
			<td>von</td>
			<td>bis</td>
			<td>verb</td>
			<td>Pause</td>
			<td>OE</td>
			<td>auft</td>
			<td>user</td>
		</tr>
		{foreach from=$stornorows item=polozka}
		<tr id='tr{$polozka.drueck_id}' class='{cycle values="lichy,sudy"}'>
			<td title="drueck_id:{$polozka.drueck_id}">{$polozka.auftragsnr}</td>
			<td>{$polozka.teil}</td>
			<td>{$polozka.pal}</td>
			<td align='right'>{$polozka.taetnr}</td>
			<td align='right'>{$polozka.stk}</td>
			<td align='right'>{$polozka.aussstk}</td>
			<td align='right'>{$polozka.aart}</td>
			<td align='right'>{$polozka.atyp}</td>
			<td align='right'>{$polozka.vzkd|string_format:"%.4f"}</td>
			<td align='right'>{$polozka.vzaby|string_format:"%.2f"}</td>
			<td>{$polozka.datum}</td>
			<td align='right'>{$polozka.persnr}</td>
			<td>{$polozka.von}</td>
			<td>{$polozka.bis}</td>
			<td align='right'>{$polozka.verb}</td>
			<td align='right'>{$polozka.pause}</td>
			<td align='right'>{$polozka.oe}</td>
			<td>{$polozka.aufteilung}</td>
			<td>{$polozka.user}</td>
		</tr>
		{/foreach}
		</table>
</div>

<div id='dauftr_form_footer'>
<table width='100%' border='0' cellspacing='0' cellpadding='1'>
<tr>
	<td>
		&nbsp;
	</td>
	<td>
		&nbsp;
	</td>
	<td>
		<input id='stornieren' {if $stornoid>0}disabled='disabled'{/if} class='formularbutton' type='button' value='Storno' onclick="document.location.href='./storno.php';"/>
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="window.history.back();"/>
	</td>

</tr>
</div>

{/if}

</body>
</html>
