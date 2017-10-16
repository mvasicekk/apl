<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />          
    <title>
      DKopf
    </title>

<link rel="stylesheet" href="../styl_common.css" type="text/css">    
<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../colorbox.css" type="text/css">
<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">

<script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>

<script type="text/javascript" src="./js/init_controls.js"></script>
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
<script>
var promenne = new Array("kz_druck","taetnr","bez_d","bez_t","mittel","vzkd","vzaby","KzGut","bedarf_typ","lager_von","lager_nach");
var onblur_function = new Array("","getDataReturnXml('./validate_taetnr.php?value='+this.value, validate_taetnr);savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);");
var editovat = new Array(0,0,1,1,1,1,1,1,1,0,0);
</script>
<script type = "text/javascript" src = "js_tablegrid.js"></script>
<script type="text/javascript" src="../js/colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="../plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="./dkopf.js"></script>




</head>

<body onload="init_level({$level});init_dkopf_form('show');">
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
Arbeitsplan pflegen / Sprava pracovniho planu - {$teil_value}
</div>

<div id="formular_telo">
	<form action="">
	<table cellpadding="1px" class="formulartable" border="0">
	<tr>
		<td>

			<span style="display:{$display_sec.kunde_sec};" id="kunde_sec">
			<label for="kunde">Kunde/zakaznik</label>
			<input {$edit_sec.kunde_sec} onblur="getDataReturnXml('./validate_kunde.php?value='+this.value, validate_kunde);" maxlength='3' size="3" type="text" id="kunde" name="kunde" value="{$kunde_value}"/>
			<input size='45' type='text' class='hidden' id='kunde_failed' value='Falsche Kundennummer / spatne cislo zakaznika' />
			</span>

			<span style="display:{$display_sec.teillang_sec};" id="teillang_sec">
			    <label for="teillang">Originalteilnummer/originalni cislo</label>
			    <input {$edit_sec.teillang_sec} maxlength='35' size="35" type="text" id="teillang" name="teillang" value="{$teillang_value}"/>
			</span>

			<span style="display:{$display_sec.status_sec};" id="status_sec">
			    <label for="status">Status</label>
			    <input {$edit_sec.status_sec} acturl="dkopf_update.php" maxlength='3' size="3" type="text" id="status" name="status" value="{$status_value}"/>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<label for="teil">Teil/Dil</label>
			<input class='disabled_bold' disabled readonly onblur="getDataReturnXml('./validate_teil.php?value='+this.value, validate_teil);" maxlength='10' size="10" type="text" id="teil" name="teil" value="{$teil_value}"/>
			<input size='45' type='text' class='hidden' id='teil_failed' value='Teilnummerfehler' />

			<span style="display:{$display_sec.bezeichnung_sec};" id="bezeichnung_sec">
			<label for="bezeichnung">Bezeichnung/oznaceni</label>
			<input {$edit_sec.bezeichnung_sec} size="40" type="text" id="bezeichnung" name="bezeichnung" value="{$bezeichnung_value}"/>
			</span>
			
			<span style="display:{$display_sec.rechnungeditflag_sec};" id="rechnungeditflag_sec">
			<label for="bezeichnung">RechEdit</label>
			<input {$edit_sec.rechnungeditflag_sec} size="1" type="text" id="rechnungeditflag" name="rechnungeditflag" value="{$rechnungeditflag_value}"/>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span style="display:{$display_sec.gew_sec};" id="gew_sec">
			    <label for="gew">Nettogewicht/netto vaha</label>
			    <input {$edit_sec.gew_sec} onblur="js_validate_float(this);" size="6" type="text" id="gew" name="gew" value="{$gew_value}"/>
			</span>
			
			<span style="display:{$display_sec.brgew_sec};" id="brgew_sec">
			    <label for="brgew">Bruttogewicht/brutto vaha</label>
			    <input {$edit_sec.brgew_sec} onblur="js_validate_float(this);" size="6" type="text" id="brgew" name="brgew" value="{$brgew_value}"/>
			</span>
			
			<span style="display:{$display_sec.wst_sec};" id="wst_sec">
			    <label for="wst">Werkst. / material {$werkstoffe_selected}</label>
			    <select name="wst" id="wst" {$edit_sec.wst_sec}>
				{html_options values=$werkstoffe_ids output=$werkstoffe_values selected=$werkstoffe_selected }
			    </select>
{*			    <input readonly size="3" type="text" value="{$wst_value}"/>*}
			</span>

			<span style="display:{$display_sec.fa_sec};" id="fa_sec">
			    <label for="fa">FA</label>
			    <input {$edit_sec.fa_sec} size="3" type="text" id="fa" name="fa" value="{$fa_value}"/>
			</span>
		</td>
	</tr>
	<tr>
		<td>
		<!-- 
			<label for="jb">Jahresbedarf / rocni spotreba</label>
			<input size="6" type="text" id="jb" name="jb" value="{$jb_value}"/>
 		-->
			<span style="display:{$display_sec.vm_sec};" id="vm_sec">
			    <label for="vm">Verpackungsmenge / balící předpis</label>
			    <input {$edit_sec.vm_sec} size="6" type="text" id="vm" name="vm" value="{$vm_value}"/>
			</span>
			
			<span style="display:{$display_sec.spg_sec};" id="spg_sec">
			    <label for="spg">Stk pro Gehänge</label>
			    <input {$edit_sec.spg_sec} size="4" type="text" id="spg" name="spg" value="{$spg_value}"/>
                        </span>
			
			<span style="display:{$display_sec.restmengen_verw_sec};" id="restmengen_verw_sec">
			    <label for="restmengen_verw">Restmengenverw.</label>
			    <input {$edit_sec.restmengen_verw_sec} acturl="dkopf_update.php" maxlength="6" size="5" type="text" id="restmengen_verw" name="restmengen_verw" value="{$restmengen_verw_value}"/>
			</span>
                </td>	
        </tr>
	<tr>
	    <td>
		<span style="display:{$display_sec.letzterekl_sec};" id="letzterekl_sec">
		    letzte Reklamationen :
		    {foreach from=$letzte_reklamationen_array item=reklamation}
			<a class='abutton' href='../Reports/S362_pdf.php?report=S362&reklnr={$reklamation.rekl_nr}&reklnr_label=ReklNr&tl_tisk=pdf'>{$reklamation.rekl_nr} ({$reklamation.rekl_datum})</a>&nbsp;
		    {/foreach}
		</span>
	    </td>
	</tr>
	<tr>
		<td>
		    <span style="display:{$display_sec.bemerk_sec};" id="bemerk_sec">
			<label for="bemerk">Anderungen/Bemerkung / poznamka</label>
			<input {$edit_sec.bemerk_sec} maxlength='55' size="30" type="text" id="bemerk" name="bemerk" value="{$bemerk_value}"/>
		    </span>
		    
		    <span style="display:{$display_sec.art_guseisen_sec};" id="art_guseisen_sec">
			<label for="art_guseisen">Art Gusseisen / druh litiny</label>
			<input {$edit_sec.art_guseisen_sec} size="10" type="text" id="art_guseisen" name="art_guseisen" value="{$art_guseisen_value}"/>
		    </span>
		</td>
	</tr>
        <tr>
            <td>
		<span style="display:{$display_sec.preis_stk_gut_sec};" id="preis_stk_gut_sec">
		    <label for="preis_stk_gut">Zielpreis gut </label>
		    <input {$edit_sec.preis_stk_gut_sec} acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="preis_stk_gut" value="{$preis_stk_gut_value}"/>
		</span>

		<span style="display:{$display_sec.preis_stk_auss_sec};" id="preis_stk_auss_sec">
		    <label for="preis_stk_auss">Zielpreis auss </label>
		    <input {$edit_sec.preis_stk_auss_sec} acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="preis_stk_auss" value="{$preis_stk_auss_value}"/>
		</span>

		<span style="display:{$display_sec.fremdauftr_dkopf_sec};" id="fremdauftr_dkopf_sec">
		    <label for="fremdauftr_dkopf">Fremdauftr</label>
		    <input {$edit_sec.fremdauftr_dkopf_sec} acturl="dkopf_update.php" maxlength='50' size="10" type="text" id="fremdauftr_dkopf" value="{$fremdauftr_dkopf_value}"/>
		</span>
            </td>

        </tr>
        <tr>
            <td>
		<span style="display:{$display_sec.jbvor_sec};" id="jbvor_sec">
		    <label for="jb_lfd_2">Jahresbedarf (2012)</label>
		    <input {$edit_sec.jbvor_sec} acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="jb_lfd_2" value="{$jb_lfd_2_value}"/>
		
		    <label for="jb_lfd_1">Jahresbedarf (2013)</label>
		    <input {$edit_sec.jbvor_sec} acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="jb_lfd_1" value="{$jb_lfd_1_value}"/>
                
		    <label for="jb_lfd_j">Jahresbedarf (2014)</label>
		    <input {$edit_sec.jbvor_sec} acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="jb_lfd_j" value="{$jb_lfd_j_value}"/>
		</span>
            </td>
        </tr>
	<tr>
	    <td>
		<span style="display:{$display_sec.jbfuture_sec};" id="jbfuture_sec">
		    <label for="jb_lfd_plus_1">Jahresbedarf (2015)</label>
		    <input {$edit_sec.jbfuture_sec} acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="jb_lfd_plus_1" value="{$jb_lfd_plus_1_value}"/>
		</span>
	    </td>
	</tr>
	<tr>
	    <td>
		<span style="display:{$display_sec.schwierigkeitsgrad_S11_sec};" id="schwierigkeitsgrad_S11_sec">
		    <label for="schwierigkeitsgrad_S11">Schwierigkeitsgrad S11 / obtížnost S11</label>
		    <input {$edit_sec.schwierigkeitsgrad_S11_sec} acturl="dkopf_update.php" maxlength='255' size="25" type="text" id="schwierigkeitsgrad_S11" value="{$schwierigkeitsgrad_S11_value}"/>
		</span>
	    </td>
	</tr>
	<tr>
	    <td>
		<span style="display:{$display_sec.schwierigkeitsgrad_S51_sec};" id="schwierigkeitsgrad_S51_sec">
		    <label for="schwierigkeitsgrad_S51">Schwierigkeitsgrad S51 / obtížnost S51</label>
		    <input {$edit_sec.schwierigkeitsgrad_S51_sec} acturl="dkopf_update.php" maxlength='255' size="25" type="text" id="schwierigkeitsgrad_S51" value="{$schwierigkeitsgrad_S51_value}"/>
		</span>
	    </td>
	</tr>

	<tr>
	    <td>
		<span style="display:{$display_sec.schwierigkeitsgrad_SO_sec};" id="schwierigkeitsgrad_SO_sec">
		    <label for="schwierigkeitsgrad_SO">Schwierigkeitsgrad SO / obtížnost SO</label>
		    <input {$edit_sec.schwierigkeitsgrad_SO_sec} acturl="dkopf_update.php" maxlength='255' size="25" type="text" id="schwierigkeitsgrad_SO" value="{$schwierigkeitsgrad_SO_value}"/>
		</span>
	    </td>
	</tr>
	<tr>
		<td>
			<fieldset>
			<legend>Musterlager / sklad vzoru / TeileDoku</legend>
                        <table>
                            <tr>
				 <td>
				     <span style="display:{$display_sec.showteildoku_sec};" id="showteildoku_sec">
					<input id='showteildoku' type='button' value="TeilDoku" acturl='./showTeilDoku.php' /> <label>( Anzahl Dokumenten : {$pocet_teildoku} )</label>
				     </span>
				 </td>
                                  <td>
				      <span style="display:{$display_sec.showlagerzettel_sec};" id="showlagerzettel_sec">
					<input id='showlagerzettel' class='' type='button' value='Lagerzettel' onclick="location.href='../get_parameters.php?popisky=Teil;DokuNr&promenne=teil;dokunr&values={$teil_value};{29}&report=D515'" />
				      </span>
                                 </td>
				 <td>
				     <span style="display:{$display_sec.showvpm_sec};" id="showvpm_sec">
					<input id='showvpm' type='button' value="VPM" acturl='./showVPM.php' />
				     </span>
				 </td>
 				 <td>
				     <span style="display:{$display_sec.showima_sec};" id="showima_sec">
					<input id='showima' type='button' value="IMA" acturl='./showIMA.php' />
				     </span>
				 </td>
 				 <td>
				     <span style="display:{$display_sec.showmittel_sec};" id="showmittel_sec">
					<input id='showmittel' type='button' value="AM / MM" acturl='./showMittel.php' />
				     </span>
				 </td>
                            </tr>
                        </table>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td>
			<fieldset>
			<legend>GDat Anlagen / GDat přílohy</legend>
                        <table>
                            <tr>
				 <td>
				     <input style="display:{$display_sec.show_att_muster};" id='show_att_muster' type='button' value="Muster" acturl='./showTeilAtt.php?att=muster' />
				 </td>
				 <td>
				     <input style="display:{$display_sec.show_att_empb};" id='show_att_empb' type='button' value="EMPB" acturl='./showTeilAtt.php?att=empb' />
				 </td>
				 <td>
				     <input style="display:{$display_sec.show_att_ppa};" id='show_att_ppa' type='button' value="PPA" acturl='./showTeilAtt.php?att=ppa' />
				 </td>
				 <td>
				     <input style="display:{$display_sec.show_att_gpa};" id='show_att_gpa' type='button' value="GPA" acturl='./showTeilAtt.php?att=gpa' />
				 </td>
 				 <td>
				     <input style="display:{$display_sec.show_att_vpa};" id='show_att_vpa' type='button' value="VPA" acturl='./showTeilAtt.php?att=vpa' />
				 </td>
				 <td>
				     <input style="display:{$display_sec.show_att_qanf};" id='show_att_qanf' type='button' value="Q-Anforderungen" acturl='./showTeilAtt.php?att=qanf' />
				 </td>
 				 <td>
				     <input style="display:{$display_sec.show_att_rekl};" id='show_att_rekl' type='button' value="Reklamation" acturl='./showTeilAtt.php?att=rekl' />
				 </td>
                            </tr>
                        </table>
			</fieldset>
		</td>
	</tr>
	</table>
	</form>
</div>


<div id='apl_table' style="display:{$display_sec.apl_table};">
	<div id='scroll_apl'>	
		<table id='apltable' class='apl_table' border='0'>
		<tr class='apl_table_header'>
			<td>druck</td>
			<td>teatnr</td>
			<td>Bezeichnung (Deutsch)</td>
			<td>oznaceni (cesky)</td>
			<td>Mittel</td>
			{if $display_sec.dposvzkd_sec=="inline-block"}
			<td>
			    vzkd
			</td>
			{/if}
			<td>vzaby</td>
			<td>G</td>
			<td>Bedarf</td>
			<td>l. von</td>
			<td>l. nach</td>
			{if $display_sec.dposedit=="inline-block"}
			<td width='60'>&nbsp;</td>
			{/if}
		</tr>
		{foreach from=$dpos item=polozka}
		{if $polozka.KzGut eq "G"}
		<tr id='tr{$polozka.dpos_id}' class='Grow'>
		{else}
		    <!-- otevreni reklamaci z tabulky dreklamation -->
		<tr id='tr{$polozka.dpos_id}' bgcolor='{cycle values="#eeeeee,#dddddd"}'>
		{/if}
		
			{if $display_sec.kzdruck_sec=="inline-block"}
				<td onmouseover="this.style.cursor='pointer';" onclick="getDataReturnText('./toggle_kz_druck.php?dpos_id={$polozka.dpos_id}', toggle_kz_druck);" id='druck{$polozka.dpos_id}' {if $polozka.kz_druck==0} bgcolor='grey'{else} bgcolor='red'{/if}>&nbsp;</td>
			{else}
				<td id='druck{$polozka.dpos_id}' {if $polozka.kz_druck==0} bgcolor='grey'{else} bgcolor='red'{/if}>&nbsp;</td>
			{/if}
			
			<td id='td_select_taetnr{$polozka.dpos_id}' align='right'>{$polozka.taetnr}</td>
			<td>{$polozka.bez_d}</td>
			<td>{$polozka.bez_t}</td>
			<td>{$polozka.mittel}</td>
			{if $display_sec.dposvzkd_sec=="inline-block"}
			    <td align='right'>
				
					{$polozka.vzkd|string_format:"%.4f"}
			    </td>
			{/if}
			
			<td align='right'>{$polozka.vzaby|string_format:"%.4f"}</td>
			<td>{$polozka.KzGut|string_format:"%2s"}</td>
			<td>{$polozka.bedarf_typ|string_format:"%2s"}</td>
			
			<td id='td_select_lager_von{$polozka.dpos_id}'>{$polozka.lager_von}</td>
			<td id='td_select_lager_nach{$polozka.dpos_id}'>{$polozka.lager_nach}</td>
			
			{if $display_sec.dposedit=="inline-block"}
				<td onmouseover="this.style.cursor='pointer';" id='tdedit{$polozka.dpos_id}'><a style="display:{$display_sec.dposedit}" id='edit{$polozka.dpos_id}' onclick="getDataReturnXml('./edit_dpos_row.php?dpos_id={$polozka.dpos_id}', edit);" href='#'>edit</a></td>
			{/if}
		</tr>
		{/foreach}
		</table>
	</div>
</div>

<div id='dposnew'>
<form action="">
	<table>
		<tr>
			<td>
				<label for="newtatnr">TatNr/operace</label>
			</td>
			<td>
				<input onblur="getDataReturnXml('./validate_newtaetnr.php?value='+this.value+'&kunde='+document.getElementById('kunde').value+'&teil='+document.getElementById('teil').value, validate_newtaetnr);" maxlength='5' size="5" type="text" id="newtatnr" name="newtatnr" value=""/>
				<input type='text' class='hidden' id='newtatnr_failed' value='tatnrfehler' />
			</td>
		</tr>
		<tr>
			<td>
				<label for="newbez_d">Bezeichnung (Deutsch)</label>
			</td>
			<td>
				<input onblur="" maxlength='30' size="30" type="text" id="newbez_d" name="newbez_d" value=""/>
				<input type='text' class='hidden' id='newbez_d_failed' value='tatnrfehler' />
			</td>
		</tr>
		<tr>
			<td>
				<label for="newbez_t">oznaceni (cesky)</label>
			</td>
			<td>
				<input onblur="" maxlength='30' size="30" type="text" id="newbez_t" name="newbez_t" value=""/>
				<input type='text' class='hidden' id='newbez_t_failed' value='tatnrfehler' />
			</td>
		</tr>
		<tr>
			<td>
				<label for="mittel">Mittel</label>
			</td>
			<td>
				<input onblur="" maxlength='50' size="30" type="text" id="mittel" name="mittel" value=""/>
				<input type='text' class='hidden' id='mittel_failed' value='tatnrfehler' />
			</td>
		</tr>
		
		<tr>
			<td>
				<label for="newvzkd">VzKd</label>
			</td>
			<td>
				<input onblur="js_validate_float(this);" maxlength='10' size="10" type="text" id="newvzkd" name="newvzkd" value=""/>
				<input type='text' class='hidden' id='newvzkd_failed' value='tatnrfehler' />
			</td>
		</tr>

		<tr>
			<td>
				<label for="newvzaby">VzAby</label>
			</td>
			<td>
				<input onblur="js_validate_float(this);" maxlength='10' size="10" type="text" id="newvzaby" name="newvzaby" value=""/>
				<input type='text' class='hidden' id='newvzaby_failed' value='tatnrfehler' />
			</td>
		</tr>

		<tr>
			<td>
				<label for="newkzgut">KzGut</label>
			</td>
			<td>
				<input onblur="" maxlength='1' size="1" type="text" id="newkzgut" value=""/>
				<input type='text' class='hidden' id='newkagut_failed' value='tatnrfehler' />
			</td>
		</tr>
		
		<tr>
			<td>
				<label for="newbedarf">Bedarf</label>
			</td>
			<td>
				<input onblur="" maxlength='3' size="3" type="text" id="newbedarf" value=""/>
				<input type='text' class='hidden' id='newbedarf_failed' value='tatnrfehler' />
			</td>
		</tr>

		<tr>
			<td>
				<label for="newlagervon">Lager von</label>
			</td>
			<td>
				<select id="newlagervon">
					{html_options options=$lagry selected=$lager_selected}
				</select>
			</td>
		</tr>

		<tr>
			<td>
				<label for="newlagernach">Lager nach</label>
			</td>
			<td>
				<select id="newlagernach">
					{html_options options=$lagry selected=$lager_selected}
				</select>
			</td>
		</tr>

		<tr>
			<td>
				<input type='button' value='speichern / ulozit' onclick='insertposition();'/>
			</td>
			<td>
				<input type='button' value='abbrechen / zrusit' onclick='insertpositionCancel();' />
			</td>
		</tr>

	</table>
</form>
</div>

<div id='dkopf_form_footer'>
<form action="">
<table width='100%' border='0' cellspacing='0'>
<tr>
	<td>
	    <span style="display:{$display_sec.teilsuchen_sec};" id="teilsuchen_sec">
		<input class='formularbutton' type='button' value='teil suchen / hledat dil' onclick="document.location.href='teilsuchen.php';"/>
	    </span>
	</td>
	<td>
	    <span style="display:{$display_sec.posneu_sec};" id="posneu_sec">
		<input class='formularbutton' id='position_neu' type='button' value='Neue Position / nova operace' onclick="positionneu();"/>
	    </span>
	</td>
</tr>
<tr>
	<td>
	    <span style="display:{$display_sec.D510info_sec};" id="D510info_sec">
		<input class='formularbutton' id='info_D510' type='button' value='Info APL D510' onClick="location.href='../get_parameters.php?popisky=Teil;mit MusterFotos,*CH;Foto-Sloupcu/Spalten&promenne=teil;musterfoto;sloupcu&values={$teil_value};a;2&report=D510'"/>
	    </span>
	</td>
	<td>
	    <span style="display:{$display_sec.teilsave_sec};" id="teilsave_sec">
		<input id='teil_save' class='formularbutton' type='button' value='Aenderungen speichern' 
		onclick="getDataReturnXml('./save_dkopf.php?teil='+encodeControlValue('teil')
												+'&kunde='+encodeControlValue('kunde')
												+'&teillang='+encodeControlValue('teillang')
												+'&status='+encodeControlValue('status')
												+'&bezeichnung='+encodeControlValue('bezeichnung')
												+'&gew='+encodeControlValue('gew')
												+'&brgew='+encodeControlValue('brgew')
												+'&wst='+encodeSelectControlValue('wst')
												+'&fa='+encodeControlValue('fa')
												+'&vm='+encodeControlValue('vm')
                                                                                                +'&spg='+encodeControlValue('spg')
//												+'&reklamation='+encodeControlValue('reklamation')
//												+'&letzte_reklamation='+encodeControlValue('letzte_reklamation')
												+'&bemerk='+encodeControlValue('bemerk')
												+'&art_guseisen='+encodeControlValue('art_guseisen')
//												+'&muster_vom='+encodeControlValue('muster_vom')
//												+'&muster_platz='+encodeControlValue('muster_platz')
//												+'&muster_vorher_vom='+encodeControlValue('muster_vorher_vom')
//												+'&muster_freigabe1_vom='+encodeControlValue('muster_freigabe1_vom')
//												+'&muster_freigabe1='+encodeSelectControlValue('muster_freigabe1')
//												+'&muster_freigabe2_vom='+encodeControlValue('muster_freigabe2_vom')
//												+'&muster_freigabe2='+encodeSelectControlValue('muster_freigabe2')
												, saverefresh);"/>
	    </span>
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="document.location.href='../index.php';"/>
	</td>

</tr>
</table>
</form>
</div>

</body>
</html>
