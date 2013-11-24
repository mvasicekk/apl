<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <title>
      Arbeitsmittel Ausgabe / výdej materiálu
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<script type = "text/javascript" src = "../js/jquery.js"></script>
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
<script>

poleNapoveda = new Array(
							"osobní číslo",
                                                        'OE',
							"datum",
							"číslo materiálu",
							"inventární číslo",
							"vydané kusy",
							"vrácené kusy",
							"důvod",
							"poznámka",
							"uloží záznam"
						);

var pole = new Array(	
							"persnr",
                                                        "oeselect",
							"datum",
							"amnr",
							"invnr",
							"ausstk",
							"rueckstk",
							"grund",
							"bemerkung",
							"neu"
					);

</script>

</head>

<body onload="init_dambew();">

{popup_init src="../js/overlib.js"}
  
<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
</div>



<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
	Arbeitsmittel Ausgabe / výdej materiálu
</div>

{if $prihlasen}
<div id="formular_telo">
<table cellpadding="5px" class="formulartable" border="0" cellpadding='0' cellspacing='0'>
	<form autocomplete="off" method="post" action='' name="" onsubmit="">
    <tr>
		<td>
			<label for="persnr">P<b><u>e</u></b>rsnr</label><br>
			<input  accesskey='e' onblur="getDataReturnXml('./validate_persnr.php?&controlid='+this.id+'&value='+this.value,{if $stornoid>0}validate_persnredit{else}validate_persnr{/if});" onfocus='markfocus(this);this.select();' maxlength='5' size="5" type="text" id="persnr" name="persnr" value="{$persnr_value}"/>
		</td>
		<td>
			<label for="persname"></label>
			<input disabled size="40" type="text" id="persname" name="persname" value="{$persname_value}"/>
			
		</td>
                <td>
       			<label for="oeselect">OE</label><br>
                        <select id="oeselect">
                            {html_options values=$oes output=$oes selected=$oeselected}
                        </select>
                </td>
	</tr>
	
	<tr>		
		<td>
			<label for="datum"><b><u>D</u></b>atum</label><br>
			<input  accesskey='d' {popup text="zadejte datum"} onfocus='markfocus(this);this.select();' onblur="getDataReturnText('./validate_datum.php?what=datum&value='+this.value+'&controlid='+this.id, refreshdatum);" maxlength='10' size="10" type="text" id="datum" name="datum" />
		</td>
	</tr>
	
	<tr>
		<td>
			<label for="amnr"><b><u>A</u></b>rbeitsmittelNr</label><br>
			<input  accesskey='a' {popup text="cislo pracovniho prostredku"} onblur="getDataReturnXml('./validate_amnr.php?&controlid='+this.id+'&value='+this.value,validate_amnr);" onfocus='markfocus(this);this.select();' maxlength='7' size="7" type="text" id="amnr" name="amnr" value="{$amnr_value}"/>
		</td>
		<td>
			<label for="amnrpopis"></label>
			<input disabled size="40" type="text" id="amnrpopis" name="amnrpopis" value="{$amnrpopis_value}"/>
		</td>
		
		<td>
			<label for="invnr"><b><u>I</u></b>nv. Nummer</label><br>
			<input  accesskey='i' {popup text="inventarni cislo"} onblur="getDataReturnXml('./validate_invnr.php?&controlid='+this.id+'&value='+this.value,validate_invnr);" onfocus='markfocus(this);this.select();' maxlength='4' size="4" type="text" id="invnr" name="invnr" value="{$pal_value}"/>
		</td>
		<td>
			<label for="invnrpopis"></label>
			<input disabled size="40" type="text" id="invnrpopis" name="invnrpopis" value="{$invnrpopis_value}"/>
		</td>
		
	</tr>
	
	<tr>
		<td>
			<label for="ausstk">Ausgabe<b><u>s</u></b>tück / vydané kusy</label><br>
			<input  accesskey='s' onblur="js_validate_float(this);" onfocus='markfocus(this);this.select();' maxlength='6' size="6" type="text" id="ausstk" name="austk" value="{$ausstk_value}"/>
		</td>
		<td>
			<label for="rueckstk">Rückgabe s<b><u>t</u></b>ück / vrácené kusy</label><br>
			<input  accesskey='t' onblur="js_validate_float(this);" onfocus='markfocus(this);this.select();' maxlength='6' size="6" type="text" id="rueckstk" name="rueckstk" value="{$rueckstk_value}"/>
		</td>
		<td>
			<label for="grund"><b><u>G</u></b>rund / důvod</label><br>
			<input  accesskey='g' onblur="js_validate_float(this);" onfocus='markfocus(this);this.select();' maxlength='6' size="6" type="text" id="grund" name="grund" value="{$grund_value}"/>
		</td>

	</tr>
	
	<tr>
		<td colspan='2'>
			<label for="bemerkung">Bemerkung / poznámka</label><br>
			<input onfocus="markfocus(this);this.select();"  maxlength='255' size="50" type="text" id="bemerkung" name="bemerkung" value="{$bemerkung_value}"/>
		</td>
	</tr>		
	<tr>
		<input disabled='disabled' id='elementaktual' type='hidden' value='' size='30' />
		<td colspan='1'>
			<input accesskey='r' type="button" id="cancel" name="cancel" onclick='window.location.reload();' value="Abbruch / zrusit"/>
		</td>
		<td colspan='1'>
			<input onfocus='markfocus(this);' onclick="disableneu();getDataReturnXml('./save_dambew.php?persnr='
												+encodeControlValue('persnr')
                                                                                                +'&oeselect='+encodeSelectControlValue('oeselect')
												+'&datum='+encodeControlValue('datum')
												+'&amnr='+encodeControlValue('amnr')
												+'&invnr='+encodeControlValue('invnr')
												+'&ausstk='+encodeControlValue('ausstk')
												+'&rueckstk='+encodeControlValue('rueckstk')
												+'&grund='+encodeControlValue('grund')
												+'&bemerkung='+encodeControlValue('bemerkung')
												, saverefresh);" type="button" id="neu" name="neu" value="erfassen / vlozit"/>
		</td>

	</tr>
   </form>
	</table>

   <div id='chybacasu'>
	
	</div>

	<div id='eaktualinfo'>
	
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
			<td>schicht</td>
			<td>auft</td>
			<td>user</td>
		</tr>
		{foreach from=$stornorows item=polozka}
		<tr id='tr{$polozka.drueck_id}' class='{cycle values="lichy,sudy"}'>
			<td>{$polozka.auftragsnr}</td>
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
			<td align='right'>{$polozka.schicht}</td>
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
		<input id='korekce' disabled='disabled' class='formularbutton' type='button' value='Storno' onclick="document.location.href='./storno.php';"/>
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="window.history.back();"/>
	</td>

</tr>
</div>

{/if}

</body>
</html>
