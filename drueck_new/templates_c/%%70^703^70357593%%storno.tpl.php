<?php /* Smarty version 2.6.14, created on 2011-05-24 16:40:18
         compiled from storno.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'popup_init', 'storno.tpl', 23, false),array('function', 'popup', 'storno.tpl', 57, false),array('function', 'cycle', 'storno.tpl', 99, false),array('modifier', 'string_format', 'storno.tpl', 108, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      STORNO Rueckmeldungen / stornovani vykonu
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
<script>


</script>

</head>

<body onLoad="document.getElementById('auftragsnr').focus();rebuildpage();">
<?php echo smarty_function_popup_init(array('src' => "../js/overlib.js"), $this);?>

  
<!--   
<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
</div>
 -->


<div align="center" id="podheader">
<?php if ($this->_tpl_vars['prihlasen']): ?>
	<?php echo $this->_tpl_vars['user']; ?>
  level=<?php echo $this->_tpl_vars['level']; ?>
<a href="../index.php?akce=logout">abmelden/odhlasit</a>
<?php else: ?>
	Benutzer nicht angemeldet/neprihlaseny uzivatel
<?php endif; ?>
</div>

<div id="formular_header">
STORNO Rueckmeldungen / stornovani vykonu
</div>

<?php if ($this->_tpl_vars['prihlasen']): ?>
<div id="filtr">
<table class='dauftr_table'>
	<tr class='dauftr_table_header'>
			<td><u>A</u>uftragsnr</td>
			<td><u>T</u>eil</td>
			<td><u>P</u>al</td>
			<td>Ta<u>e</u>tNr</td>
			<td><u>D</u>atum</td>
			<td>Pe<u>r</u>sNr</td>
			<td></td>
	</tr>
	<tr>
			<td><input id='auftragsnr'  accesskey='a' title='Alt+a' <?php echo smarty_function_popup(array('text' => "muzete stisknout Alt+a pro presun do tohoto policka"), $this);?>
 onfocus='this.select();' type='text' name='auftragsnr' size='15' /></td>
			<td><input id='teil'  accesskey='t' title='Alt+t' onfocus='this.select();' type='text' name='teil' size='15' /></td>
			<td><input id='pal' accesskey='p' title='Alt+p' onfocus='this.select();' type='text' name='palette' size='15' /></td>
			<td><input id='taetnr' accesskey='e' title='Alt+e' onfocus='this.select();' type='text' name='taetnr' size='15' /></td>
			<td><input id='datum' accesskey='d' title='Alt+d' onfocus='this.select();' onblur="getDataReturnText('./validate_datum.php?what=datum&allownull=1&value='+this.value+'&controlid='+this.id, refreshdatum);" type='text' name='datum' size='15' /></td>
			<td><input id='persnr' accesskey='r' title='Alt+r' onfocus='this.select();' type='text' name='persnr' size='15' /></td>
			<td>
				<input id='filtruj' accesskey='f' <?php echo smarty_function_popup(array('text' => "muzete stisknout Alt+f misto klikani mysi na toto tlacitko"), $this);?>
 onclick="makeButtonBusy(this);savefilterparam();getDataReturnXml('./refreshwhere.php?filterparam='+document.getElementById('filterparam').value,refreshwhere);" value='filtr' type='button' title='Alt+f'/>			
			</td>
			
			<input type='hidden' id='filterparam' name='filterparam'/>
	</tr>
</table>
</div>

<div id='drueck_table'>
	<div id='scroll_apl'>
	<table class='dauftr_table' id='druecktab'>
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
			<td>stamp</td>
			<td></td>
		</tr>
		<?php $_from = $this->_tpl_vars['stornorows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['polozka']):
?>
		<tr id='tr<?php echo $this->_tpl_vars['polozka']['drueck_id']; ?>
' class='<?php echo smarty_function_cycle(array('values' => "lichy,sudy"), $this);?>
'>
			<td><?php echo $this->_tpl_vars['polozka']['auftragsnr']; ?>
</td>
			<td><?php echo $this->_tpl_vars['polozka']['teil']; ?>
</td>
			<td><?php echo $this->_tpl_vars['polozka']['pal']; ?>
</td>
			<td align='right'><?php echo $this->_tpl_vars['polozka']['taetnr']; ?>
</td>
			<td align='right'><?php echo $this->_tpl_vars['polozka']['stk']; ?>
</td>
			<td align='right'><?php echo $this->_tpl_vars['polozka']['aussstk']; ?>
</td>
			<td align='right'><?php echo $this->_tpl_vars['polozka']['aart']; ?>
</td>
			<td align='right'><?php echo $this->_tpl_vars['polozka']['atyp']; ?>
</td>
			<td align='right'><?php echo ((is_array($_tmp=$this->_tpl_vars['polozka']['vzkd'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.4f") : smarty_modifier_string_format($_tmp, "%.4f")); ?>
</td>
			<td align='right'><?php echo ((is_array($_tmp=$this->_tpl_vars['polozka']['vzaby'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
			<td><?php echo $this->_tpl_vars['polozka']['datum']; ?>
</td>
			<td align='right'><?php echo $this->_tpl_vars['polozka']['persnr']; ?>
</td>
			<td><?php echo $this->_tpl_vars['polozka']['von']; ?>
</td>
			<td><?php echo $this->_tpl_vars['polozka']['bis']; ?>
</td>
			<td align='right'><?php echo $this->_tpl_vars['polozka']['verb']; ?>
</td>
			<td align='right'><?php echo $this->_tpl_vars['polozka']['pause']; ?>
</td>
			<td align='right'><?php echo $this->_tpl_vars['polozka']['oe']; ?>
</td>
			<td><?php echo $this->_tpl_vars['polozka']['aufteilung']; ?>
</td>
			<td><?php echo $this->_tpl_vars['polozka']['user']; ?>
</td>
			<td><?php echo $this->_tpl_vars['polozka']['stamp']; ?>
</td>
			<td>
				<input <?php echo smarty_function_popup(array('text' => "kliknutim vytvorite pozici s minusovym poctem dobrych kusu, zmetku a spotrebovaneho casu."), $this);?>
 onclick="getDataReturnXml('./stornorow.php?id='+this.id,removerow);" type='button' class='stornobutton' value='stor' id='<?php echo $this->_tpl_vars['polozka']['drueck_id']; ?>
'/>
				<input onclick="window.location.href='./editrow.php?id='+this.id;" type='button' class='editbutton' value='edit' id='<?php echo $this->_tpl_vars['polozka']['drueck_id']; ?>
'/>
			</td>
		</tr>
		<?php endforeach; endif; unset($_from); ?>
		</table>
		
	</div>
	
</div>

<div id='storno_form_footer'>
<table width='100%' border='0' cellspacing='0' cellpadding='1'>
<tr>
	<td>
	
	</td>
	<td>
		&nbsp;
	</td>
	<td>
		&nbsp;
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="history.back();"/>
	</td>

</tr>
</div>

<?php endif; ?>


</body>
</html>