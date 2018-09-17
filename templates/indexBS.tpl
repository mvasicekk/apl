<!DOCTYPE html>
<html lang="cs">
    <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>
	    APL Abydos
	</title>    

	<script type="text/javascript" src="./js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="./js/jquery.jqplot.min.js"></script>

{*	jqplot*}
	<link rel="stylesheet" type="text/css" href="./js/jquery.jqplot.min.css"/>
	<script type="text/javascript" src="./js/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.canvasOverlay.min.js"></script>

{*bootstrap*}
	<link href="./brany/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="./brany/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	
{*d3	*}
	<script src="./brany/bower_components/d3/d3.min.js"></script>
{*custom styl	*}
        <link rel="stylesheet" href="./stylBS.css" type="text/css">

{*custom js	*}
	<script src="./brany/js/brany.js"></script>
	<script type="text/javascript" src="./apl.js"></script>
    </head>
    
    
    <body>
	<div class="container-fluid">
	    {include file='headingBS.tpl'}
	    {if $prihlasen}
	    <div class="container-fluid col-md-6" >

		<div  class="panel panel-success buttony" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-user"></span> Personal</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dzeitedata};" type="button" value="Anwesenheitserfassung lt. Leistung" id="dzeitedata" onClick="location.href='./personal/doc_root/index.php?action=edataAnw&presenter=DpersAnwesenheit'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dpers};" type="button" value="Personal Alt" id="dpers" onClick="okno=window.open();okno.location.href='./personal/doc_root/index.php?presenter=Persinfo'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dpersnew};" type="button" value="Personal pflegen" id="dpers" onClick="okno=window.open();okno.location.href='./pers/pers.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default  btn-block" style="display:{$display_sec.dzeit};" type="button" value="Anwesenheitserfassung" id="dzeit" onClick="location.href='./dzeit/dzeit.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default  btn-block" style="display:{$display_sec.anwesenheitplan};" type="button" value="Anwesenheitplanung" id="anwesenheitplan"  onClick="location.href='./personal/doc_root/index.php?action=planAnwesenheit&presenter=DpersAnwesenheit'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default  btn-block" style="display:{$display_sec.anwesenheitplan};" type="button" value="Anwesenheitplanung Neu" id="anwesenheitplanneu"  onClick="location.href='./pers/persplan.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default  btn-block" style="display:{$display_sec.vorschuss} ;" type="button" value="Vorschuss" id="vorschuss"  onClick="location.href='./dpers/vorschuss.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default  btn-block" style="display:{$display_sec.hodnoceni_firemni} ;" type="button" value="Bewertung - Firma" id="hodnoceni_firemni"  onClick="okno=window.open();okno.location.href='./hodnoceni/hodnoceni.php#/hodnoceni_firemni'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default  btn-block" style="display:{$display_sec.hodnoceni_faktory_oe} ;" type="button" value="Bewertung - OE-Matrix" id="hodnoceni_faktory_oe"  onClick="okno=window.open();okno.location.href='./hodnoceni/hodnoceni.php#/hodnoceni_faktory_oe'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success buttony" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-briefcase"></span> Kunden / zakaznici</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.kundepflegen};" type="button" value="Kunden Pflegen" id="kundepflegen" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./dksd/kundesuchen.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.telbuch};" type="button" value="Telefonbuch" id="telbuch" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./telbuch/telbuch.php'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success buttony" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-edit"></span> Auftraege / zakazky</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.daufkopf};" type="button" value="Aufträge Pflegen" id="daufkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./auftrag/auftrag.php#/list'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dkopf};" type="button" value="Arbeitsplan Pflegen" id="dkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dkopf/teilsuchen.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dkopf_new};" type="button" value="Arbeitsplan Pflegen Neu" id="dkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dkopf/dkopfjs.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.umtermin};" type="button" value="Umterminieren" id="umtermin" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./dauftr/umterminieren/umterminieren.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dispo};" type="button" value="Dispo" id="dispo"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./dispo_2/dispo.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block"style="display:{$display_sec.reklamation};" type="button" value="Reklamation" id="reklamation"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./reklamation/reklamation.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.infopanely};" type="button" value="Infopanely" id="infopanely" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./panely/panely.php'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-volume-up"></span> Rueckmeldungen / hlaseni vykonu</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6"><input class="btn btn-default btn-block" style="display:{$display_sec.drueck};" type="button" value="Rückmeldung Leist" id="drueck" class="abyStartButton" onClick="okno=window.open();okno.location.href='./drueck/drueck.php'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success buttony" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-hdd"></span> Laeger / sklady</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6">  <input class="btn btn-default btn-block" style="display:{$display_sec.lagerbew};" type="button" value="Lagerumbuchung / pridani do skladu" id="lagerbew" class="abyStartButton" onClick="location.href='./dlager/umbuchung.php'" /></div>
			    <div class="col-sm-6">  <input class="btn btn-default btn-block" style="display:{$display_sec.lagerstk};" type="button" value="Lager Inventur" id="lagerstk" class="abyStartButton" onClick="location.href='./dlagstk/dlagstk.php'" /></div>
			    <div class="col-sm-6"> <input class="btn btn-default btn-block" style="display:{$display_sec.behlagerinv};" type="button" value="Behaelterinventur / palety inventura" id="behlagerinv" class="abyStartButton" onClick="location.href='./dbehaelter/inventur.php'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success buttony" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-folder-close"></span> Berichte / sestavy</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6">  <input class="btn btn-default btn-block"  style="display:{$display_sec.berichte};" type="button" value="Berichte Drucken" id="berichte" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/reports.php'" /></div>
			    <div class="col-sm-6">   <input class="btn btn-default btn-block"  style="display:{$display_sec.phpexcel};" type="button" value="PHPExcel - Exporte" id="phpexcel" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/excelreports.php'" /></div>
			    <div class="col-sm-6">   <input class="btn btn-default btn-block"  style="display:{$display_sec.showquery};" type="button" value="Schlüsseltabellen zeigen" id="showquery" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/st.php?form_typ=schltabelle'" /></div>
			    <div class="col-sm-6">   <input class="btn btn-default btn-block"  style="display:{$display_sec.eforms};" type="button" value="eForms zeigen" id="eforms" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/st.php?form_typ=eform'" /></div>
			    <div class="col-sm-6">   <input class="btn btn-default btn-block"  style="display:{$display_sec.sonstforms};" type="button" value="Formulare - Sonst" id="sonstforms" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/st.php?form_typ=sonstform'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success buttony" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-wrench"></span> Reparaturen</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6"><input class="btn btn-default btn-block" style="display:{$display_sec.repeingabe};" type="button" value="Reparaturen" id="repeingabe" class="abyStartButton" onClick="okno=window.open();okno.location.href='./reparaturen/repeingabe.php'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success buttony" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-shopping-cart"></span> Einkauf / nákup</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6">   <input class="btn btn-default btn-block" style="display:{$display_sec.eink_anforderung};" type="button" value="Anforderung/požadavek" id="eink_anforderung" class="abyStartButton" onClick="okno=window.open();okno.location.href='./einkauf/anforderung.php'" /></div>
			    <div class="col-sm-6">   <input class="btn btn-default btn-block" style="display:{$display_sec.fasovani};" type="button" value="fasování" id="fasovani" class="abyStartButton" onClick="okno=window.open();okno.location.href='./fasovani/'" /></div>
			</div>
		    </div>
		</div>
	    </div>
			
	    <div class="container-fluid col-md-6">
		
		<div class="row">
		    <div class="col-xs-12">
			<div id="chatwindow" style="width:100%;">
			    <div class="panel panel-info">
				<a role="button" data-toggle="collapse" href="#chatinfo" aria-expanded="true" aria-controls="chatinfo">
				    <div class="panel-heading">
					<h4 class="panel-title panel-info">
					    <span class="glyphicon glyphicon-film" aria-hidden="true"></span>
					    &nbsp;Chat
					</h4>
				    </div>
				</a>
				<div id="chatinfo" class="panel-collapse collapse">
				    <div class="panel-body">
					<iframe src="http://172.16.1.113:8080/channel/apl" width="100%" height="600px"></iframe>
				    </div>
				</div>
			    </div>
			</div>
		    </div>
		</div>
		
		<div class="row">
		    <div class="col-xs-12">
			<div id="tvfiles" style="width:100%;">
			    <div class="panel panel-info">
				<a role="button" data-toggle="collapse" href="#tvinfo" aria-expanded="true" aria-controls="tvinfo">
				    <div class="panel-heading">
					<h4 class="panel-title panel-info">
					    <span class="glyphicon glyphicon-film" aria-hidden="true"></span>
					    &nbsp;TV
					</h4>
				    </div>
				</a>
				<div id="tvinfo" class="panel-collapse collapse">
				    <div class="panel-body">
					<table class="table">
					    {foreach key=poradi item=file from=$tvFiles}
						<tr>
						    <td>
							<a target='_blank' href="{$file.url}">{$file.filename}</a>
						    </td>
						</tr>
					    {/foreach}
					</table>
				    </div>
				</div>
			    </div>
			</div>
		    </div>
		</div>
		
	    <div class="row">
		<div class="col-xs-12">
		    <div id="branydiv">
			<input type="hidden" id="userinfo" value="{$user}" />
			<div class="container-fluid">
			    <div class="row">
				<div style="display:{$display_sec.branydiv};" class="col-md-4 text-center">
				    <input value="" type="password" maxlength="4" class="form-control text-center" id="branaPin" placeholder="PIN" />
				</div>
				<div class="col-md-4 text-center">
				    <button title='horní brána' class="btn btn-block" id="brana1Button">
{*				    <img id='brana1img' width="100%" src="http://a:a@172.16.1.102/Streaming/channels/801/picture" title='horní brána' >*}
				    horní brána
				    </button>
				</div>
				<div class="col-md-4 text-center">
				    <button title='dolní brána' class="btn btn-block" id="brana2Button">
{*				    <img id='brana2img' width="100%" src="http://a:a@172.16.1.102/Streaming/channels/401/picture" title='dolní brána' >*}
				    dolní brána
				    </button>
				</div>
			    </div>
			</div>
		    </div>
		</div>
	    </div>

			
		<div class="row">
		    <div class="col-xs-12">
		    <div class="table-responsive">
			<table  class="table table-bordered table-condensed table-striped">
			    <thead>
				<tr>
				    <td class="progresspopis">datum</td>
				    <td colspan="2" class="progresspopis">Guss / % Ziel</td>
				    <td colspan="2" class="progresspopis">NE / % Ziel</td>
				    <td colspan="2" class="progresspopis">Summe / % Ziel</td>
				</tr>
				<tr>
				    <td class="recordpopis">{$datum_rekord}</td>
				    <td colspan="2" class="recordpopis">{$pg1_vzkd}</td>
				    <td colspan="4" class="recordpopis">= rekord Guss</td>
				</tr>
				<tr>
				    <td class="ganzmonat">akt. Monat</td>
				    <td colspan="2" class="ganzmonat">{$sum_pg1|string_format:"%d"}</td>
				    <td colspan="2" class="ganzmonat">{$sum_pg4|string_format:"%d"}</td>
				    <td colspan="2" class="ganzmonat">{$sum_celkem|string_format:"%d"}</td>
				</tr>
			    </thead>
			    <tbody>
				{foreach key=poradi item=polozka from=$pole}
				<tr>
				    <td class='progresspolozka'>{$polozka.datum}</td>
				    <td class='progresspolozka'>{$polozka.pg1|string_format:"%d"}</td>
				    <td class='progresspolozka'>{$polozka.ziel_pg1|string_format:"%d"}%</td>
				    <td class='progresspolozka'>{$polozka.pg4|string_format:"%d"}</td>
				    <td class='progresspolozka'>{$polozka.ziel_pg4|string_format:"%d"}%</td>
				    <td class='progresspolozka'>{$polozka.celkem|string_format:"%d"}</td>
				    <td class='progresspolozka'>{$polozka.ziel_sum|string_format:"%d"}%</td>
				</tr>
				{/foreach}
			    </tbody>
			</table>
		    </div>
		    </div>
		</div>
		<div class="row">
		    <div class="col-xs-12">
			<div id="svgChart">
			    <svg width="100%" height="400" style="">
			    
			    </svg>
			</div>
		    </div>
		</div>	   
		<div class="row" id="option">
		    <div class="btn-group btn-group-sm btn-group-justified" role="group" aria-label="...">
			<div class="btn-group" role="group">
			    <button class="btn btn-info" value="week" onclick="updateData(7)">Woche</button>
			</div>
			<div class="btn-group" role="group">
			    <button class="btn btn-info" value="month" onclick="updateData(31)" >Monat</button>
			</div>
			<div class="btn-group" role="group">
			    <button class="btn btn-info" value="120 days" onclick="updateData(120)" >120 Tage</button>
			</div>
			<div class="btn-group" role="group">
			    <button class="btn btn-info" value="year" onclick="updateData(365)" >Jahr</button>
			</div>
			<div class="btn-group" role="group">
			    <button class="btn btn-info" value="year" onclick="updateData(730)" >2 Jahre</button>
			</div>
		    </div>
		</div>
	    </div>
            {else}
		<div class="row">
	    <div class="container-fluid col-md-6 col-md-offset-3">
		<form action="index.php" method="post" id="logintable" class="loginform">
		<div class="panel panel-default">
		    <div class="panel-heading" >Anmelden / Přihlásit se</div>
		    <div class="panel-body ">
			<div class="row">
			    <table class="table table-condensed "> <div class="col-md-12">
			    <tr>
				<td>
				    <div class="input-group">
					<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-user"></span></span>
					<input placeholder="Benutzername / Uživatel" type="text" name="username" id="username" class="form-control" aria-describedby="sizing-addon2">
				    </div>
				</td>
			    </tr>
			    <tr class="input_password">
				<td>
				    <div class="input-group">
					<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-cog"></span></span>
					<input placeholder=" Kennwort / Heslo" type="password" name="password" id="password" class="form-control" aria-describedby="sizing-addon2">
				    </div>
				</td>
			    </tr>
			    <tr>
				<td align="center" colspan="2">
				    <input class='btn btn-default' type="submit" name="loginbutton" id="loginbutton" value="Anmelden/prihlasit">
				</td>
			    </tr>
			    </table>
			</div>
		    </div>
		</div>
		</form>
	    </div>
		</div>
	    {/if}
	</div>
    </body>
</html>
