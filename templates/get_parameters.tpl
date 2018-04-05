<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />          
    <title>
      APL Abydos
    </title>    
    <link rel="stylesheet" href="./styl_common.css" type="text/css">
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
<div id="parametry">
{if $paramok==1 }
<form action="viewreport.php" method="POST">
    <table class="paramtable" border="0" cellspacing="0">
	<tr>
	    <td class="sestavypopis" colspan="2">
		{$nadpis}
		<input type="hidden" name="report" value="{$report}"/>
	    </td>
	</tr>
	
	{foreach item=parametr from=$param}

	<tr>
        
	{if $parametr.typ eq "*CB"||$parametr.typ eq "*RA"||$parametr.typ eq "*CH"}
        <tr>
	    <td colspan="2"><hr></td>
	</tr>
        {/if}
	
	<td class='paramlabel'>&nbsp;{$parametr.label}</td>
        <!-- ted se budu rohodovat podle typu -->
        {if $parametr.typ eq "*CB"}
            <td>
             {html_options name=$parametr.var values=$parametr.val output=$parametr.val}
            </td>
        {elseif $parametr.typ eq "*CH"}
            <td>
             <input  {if $parametr.val eq "checked"}checked="checked"{/if}  type="checkbox" name="{$parametr.var}" value="{$parametr.val}" id="{$parametr.var}" class="paraminput"  />
<!--             {html_checkboxes separator="<br/>" name=$parametr.var values=$parametr.val output=$parametr.val}-->
            </td>
        {elseif $parametr.typ eq "*RA"}
            <td>
             {html_radios separator="<br/>" name=$parametr.var values=$parametr.val output=$parametr.val selected=$parametr.val[0]}
            </td>
        {else}
<!--		<td><input  {if $parametr.typ eq "*DATE"}class="datepicker"{/if} type="{$parametr.typ}" name="{$parametr.var}" value="{$parametr.val}" id="{$parametr.var}" class="paraminput"  /></td>-->
		<td><input  {if $parametr.typ eq "*DATE"}class="datepicker"{/if} type="{$parametr.inputtype}" name="{$parametr.var}" value="{$parametr.val}" id="{$parametr.var}" class="paraminput"  /></td>
        {/if}
		<input type="hidden" name="{$parametr.var}_label" value="{$parametr.label}"/>
	</tr>
	{/foreach}
    <tr><td colspan="2"><hr/></td></tr>
	<tr>
		<td colspan="2"><input type="button" name="" value="Druck / tisk" id="tl_pdf" class="abyStartButton" onClick="setvar('pdf');form.submit();" /></td>
		<input type="hidden" name="tl_tisk" id="tl_tisk" value="html"/>
	</tr>
	<tr>
	    <td colspan="2">
			<input type="button" class="abyStartButton" value="Ende / konec" id="ende" style="margin-top: 15px;"  onClick="history.back();">
		</td>
	</tr>
	</table>
</form>
{/if}
</div>

</body>
</html>
