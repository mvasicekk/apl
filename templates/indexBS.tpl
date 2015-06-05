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
        <link rel="stylesheet" href="./libs/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="./libs/bootstrap/css/bootstrap-theme.min.css">
	
{*custom styl	*}
        <link rel="stylesheet" href="./styl_common.css" type="text/css">
        <link rel="stylesheet" href="./stylBS.css" type="text/css">

{*custom js	*}
	<script type="text/javascript" src="./apl.js"></script>
    </head>
    
    
    <body>
	<div class="container-fluid">
	    {include file='headingBS.tpl'}
	    {if $prihlasen}
	    <div class="container-fluid col-md-6" >

		<div  class="panel panel-success" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-user"></span> Personal</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dzeitedata};" type="button" value="Anwesenheitserfassung lt. Leistung" id="dzeitedata" onClick="location.href='./personal/doc_root/index.php?action=edataAnw&presenter=DpersAnwesenheit'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dpers};" type="button" value="Personal Pflegen" id="dpers" onClick="okno=window.open();okno.location.href='./personal/doc_root/index.php?presenter=Persinfo'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dzeit};" type="button" value="Anwesenheitserfassung" id="dzeit" onClick="location.href='./dzeit/dzeit.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.anwesenheitplan};" type="button" value="Anwesenheitplanung" id="anwesenheitplan"  onClick="location.href='./personal/doc_root/index.php?action=planAnwesenheit&presenter=DpersAnwesenheit'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.vorschuss} ;" type="button" value="Vorschuss" id="vorschuss"  onClick="location.href='./dpers/vorschuss.php'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success" >
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

		<div  class="panel panel-success" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-edit"></span> Auftraege / zakazky</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.daufkopf};" type="button" value="Aufträge Pflegen" id="daufkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dauftr/auftragsuchen.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dkopf};" type="button" value="Arbeitsplan Pflegen" id="dkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dkopf/teilsuchen.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.umtermin};" type="button" value="Umterminieren" id="umtermin" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./dauftr/umterminieren/umterminieren.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.rundlauf};" type="button" value="Rundlauf" id="rundlauf"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./napl/www/?action=rundlauf&presenter=Dispo'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.dispo};" type="button" value="Dispo" id="dispo"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./dispo_2/dispo.php'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block"style="display:{$display_sec.reklamation};" type="button" value="Reklamation" id="reklamation"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./napl/www/?action=reklamation&presenter=Reklamation&uziv={$user}'" /></div>
			    <div class="col-sm-6" ><input class="btn btn-default btn-block" style="display:{$display_sec.infopanely};" type="button" value="Infopanely" id="infopanely" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./infopanely/places.php'" /></div>
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

		<div  class="panel panel-success" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-hdd"></span> Laeger / sklady</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6">  <input class="btn btn-default btn-block" style="display:{$display_sec.lagerbew};" type="button" value="Lagerumbuchung / pridani do skladu" id="lagerbew" class="abyStartButton" onClick="location.href='./dlager/umbuchung.php'" /></div>
			    <div class="col-sm-6">  <input class="btn btn-default btn-block" style="display:{$display_sec.lagerstk};" type="button" value="Lager Inventur" id="lagerstk" class="abyStartButton" onClick="location.href='./dlagstk/dlagstk.php'" /></div>
      {*		    <input style="display:{$display_sec.behlagerbew};" type="button" value="Behaelterbewegung / palety pohyby" id="behlagerbew" class="abyStartButton" onClick="location.href='./dbehaelter/bewegung.php'" />*}
			    <div class="col-sm-6"> <input class="btn btn-default btn-block" style="display:{$display_sec.behlagerinv};" type="button" value="Behaelterinventur / palety inventura" id="behlagerinv" class="abyStartButton" onClick="location.href='./dbehaelter/inventur.php'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-folder-close"></span> Berichte / sestavy</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6">  <input class="btn btn-default btn-block"  style="display:{$display_sec.berichte};" type="button" value="Berichte Drucken" id="berichte" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/reports.php'" /></div>
			    <div class="col-sm-6">   <input class="btn btn-default btn-block"  style="display:{$display_sec.phpexcel};" type="button" value="PHPExcel - Exporte" id="phpexcel" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/excelreports.php'" /></div>
			    <div class="col-sm-6">   <input class="btn btn-default btn-block"  style="display:{$display_sec.showquery};" type="button" value="Schlüsseltabellen zeigen" id="showquery" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/st.php'" /></div>
      {*		    <input style="" type="button" value="Berichte Drucken" id="berichte" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/reports.php'" />*}
      {*		    <input style="" type="button" value="PHPExcel - Exporte" id="phpexcel" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/excelreports.php'" />*}
      {*		    <input style="display:{$display_sec.showquery};" type="button" value="Schlüsseltabellen zeigen" id="showquery" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/querys.php'" />*}
			</div>
		    </div>
		</div>

		<div  class="panel panel-success" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-wrench"></span> Reparaturen</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6"><input class="btn btn-default btn-block" style="display:{$display_sec.repeingabe};" type="button" value="Reparaturen" id="repeingabe" class="abyStartButton" onClick="okno=window.open();okno.location.href='./reparaturen/repeingabe.php'" /></div>
			</div>
		    </div>
		</div>

		<div  class="panel panel-success" >
		    <div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-shopping-cart"></span> Einkauf / nákup</h3>
		    </div>
		    <div class="panel-body ">
			<div class="row">
			    <div class="col-sm-6">   <input class="btn btn-default btn-block" style="display:{$display_sec.eink_anforderung};" type="button" value="Anforderung/požadavek" id="eink_anforderung" class="abyStartButton" onClick="okno=window.open();okno.location.href='./einkauf/anforderung.php'" /></div>
			</div>
		    </div>
		</div>
	    </div>
			
	    <div class="container-fluid col-md-6">
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
	    		<div id="myChart"></div>
		    </div>
		</div>	    
	    </div>
            {else}
		<div class="row">
	    <div class="container-fluid col-md-6 col-md-offset-3">
		<form action="indexBS.php" method="post" id="logintable" class="loginform">
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
