<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
     
    <title>
      APL Abydos
    </title>    
    <link rel="stylesheet" href="./styl.css" type="text/css">
    <link rel="stylesheet" href="./css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">
    <script type = "text/javascript" src = "./js/jquery-1.5.1.min.js"></script>
    <script type = "text/javascript" src = "./js/jquery-ui-1.8.14.custom.min.js"></script>
    <script type = "text/javascript" src = "./js/jquery.ui.datepicker-cs.js"></script>
    <script type = "text/javascript" src = "./js_functions.js"></script>

	

{literal}
<script type="text/javascript">
function setvar(hodnota)
{
	var prvek = document.getElementById('tl_tisk');
	prvek.value=hodnota;
	return true;
}
</script>
{/literal}
</head>
<body>
<!-- pomocna procedura k vyzkouseni -->

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

<div align="center" id="parametry">
{ if $paramok==1 }
<form action="viewreport.php" method="POST">
	<table class="paramtable" border="0" cellspacing="0">
	<tr>
		<td class="sestavypopis" colspan="2">
		&nbsp;{$nadpis}
		<input type="hidden" name="report" value="{$report}"/>
		</td>
	</tr>
	{foreach item=parametr from=$param}
	<tr>
        {if $parametr.typ eq "*CB"||$parametr.typ eq "*RA"||$parametr.typ eq "*CH"}
        <tr><td colspan="2"><hr/></td></tr>
        {/if}
		<td>&nbsp;{$parametr.label}</td>
        <!-- ted se budu rohodovat podle typu -->
        {if $parametr.typ eq "*CB"}
            <td>
             {html_options name=$parametr.var values=$parametr.val output=$parametr.val}
            </td>
        {elseif $parametr.typ eq "*CH"}
            <td>
             <input  type="checkbox" name="{$parametr.var}" value="{$parametr.val}" id="{$parametr.var}" class="abyStartButton"  />
<!--             {html_checkboxes separator="<br/>" name=$parametr.var values=$parametr.val output=$parametr.val}-->
            </td>
        {elseif $parametr.typ eq "*RA"}
            <td>
             {html_radios separator="<br/>" name=$parametr.var values=$parametr.val output=$parametr.val selected=$parametr.val[0]}
            </td>
        {else}
		<td><input  {if $parametr.typ eq "*DATE"}class="datepicker"{/if} type="{$parametr.typ}" name="{$parametr.var}" value="{$parametr.val}" id="{$parametr.var}" class="abyStartButton"  /></td>
        {/if}
		<input type="hidden" name="{$parametr.var}_label" value="{$parametr.label}"/>
	</tr>
	{/foreach}
    <tr><td colspan="2"><hr/></td></tr>
	<tr>
<!--		<td><input disabled='disabled' type="button" name="" value="Vorschau / nahled" id="tl_html" class="abyStartButton" onClick="setvar('html');form.submit();" /></td>-->
		<td><input type="button" name="" value="Druck / tisk" id="tl_pdf" class="abyStartButton" onClick="setvar('pdf');form.submit();" /></td>
		<input type="hidden" name="tl_tisk" id="tl_tisk" value="html"/>
	</tr>
	<tr>
		<td colspan="2">
			<input type="button" value="Ende / konec" id="ende" style="margin-top: 15px;"  onClick="history.back();">
		</td>
	</tr>
	</table>
</form>
{/if}
</div>

</body>
</html>
