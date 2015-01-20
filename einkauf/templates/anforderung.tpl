<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />          
    <title>
      APL Abydos
    </title>    
    <link rel="stylesheet" href="../styl_common.css" type="text/css">
    <link rel="stylesheet" href="./styl.css" type="text/css">
    
    <!--jQuery dependencies-->
    <link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />
    <script src="../js/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>
    <!--PQ Grid files-->
    <link href="../libs/pqgrid/css/pqgrid.min.css" rel="stylesheet" type="text/css"/>
    <script src="../libs/pqgrid/js/pqgrid.min.js" type="text/javascript"></script>
    <!--PQ Grid Office theme-->
    <link rel="stylesheet" href="../libs/pqgrid/themes/office/pqgrid.css" />

    {*<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">
    <script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
    <script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>*}
    <script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
    <script type = "text/javascript" src = "./einkauf.js"></script>
</head>
<body>
{include file='../../templates/heading.tpl'}
	<div id="formular_header">
	    Einkauf Anforderung / pozadavek na nakup
	</div>
{if $flashmessage neq ""}
    <div class="flashmessage">
	{$flashmessage}
    </div>
{/if}
<div class='inputform'>
    <form action="anforderung.php" method="post">
	
	<span>
	<label>Typ:</label>
	<input type='radio' id='einkauf' name='anftyp' value='einkauf' class='entermove' checked='checked' autofocus/>
	<label for='einkauf'>nakup</label>
	<input type='radio' id='reparatur' name='anftyp' value='reparatur' class='entermove'/>
	<label for='reparatur'>oprava</label>
	</span>

	<span>
	<label for="artikel">Artikel:</label>
	<input type='text' id='artikel' name='artikel' class='entermove' placeholder="artikel beschreibung"/>
	</span>
	<span>
	<label for="anzahl">pocet kusů:</label>
	<input type='number' id='anzahl' name='anzahl'  class='entermove' style="width: 3em;" placeholder="ks"/>
	</span>
	<span>
	<label for="bemerk">poznamka:</label>
	<input type='text' id='bemerk' name='bemerk'  class='entermove' placeholder="poznamka"/>
	</span>
	<span>
	<label for="abdatum">od kdy potrebuji:</label>
	<input class="datepicker entermove" type='text' id='abdatum' name='abdatum' value=''/>
	</span>

	<span>
	<label for="prio">prio:</label>
	<select id='prio' name='prio' class='entermove'>
	    <option value='a' >a - urgent</option>
	    <option value='b' selected="selected">b - standard</option>
{*	    <option value='c' >c - chci to, ale vlastně nepotřebuju</option>*}
	</select>
	</span>
	
	<input type="submit" value='eingeben / vložit' id='eingeben' name='eingeben' class='entermove submit abyStartButton'/>
    </form>
</div>
	
	
<input type="hidden" name="user" id="user" value="{$user}" />
<input type="hidden" name="showalllist" id="showalllist" value="{$display_sec.anforderungenlist}" />

{*	<div style="display:{$display_sec.anforderungenlist};" id="anforderungenlist">*}
	<div id="anforderungenlist">
	    <div id="grid_array" style="margin:auto;"></div>
	</div>
</body>
</html>
