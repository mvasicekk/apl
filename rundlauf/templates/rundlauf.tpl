<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Rundlauf
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<!--<link rel='stylesheet' href='./print.css' type='text/css' media="print"/>-->

<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">
<script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
<script type="text/javascript" src="./rundlauf.js"></script>


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

    <div id="formular_header">
        Rundlauf
    </div>

    
    <div id="formular_telo">
	<div id='chyba'>Info</div>
        <div id="behaelterinfo">
	                
            <table>
                <tr>
                    <td>EX</td>
                    <td><input class="entermove ui-widget" type="text" size="8" maxlength="6" id="ex" value="" acturl="./validateAuftrag.php"/></td>
                    <td>IM</td>
                    <td><input class="entermove ui-widget" type="text" size="8" maxlength="6" id="im" value="" acturl="./validateAuftrag.php"/></td>
                </tr>

		<!-- abfahtrt Aby -->
		<tr>
		    <td style="border-top: 1px solid black;" colspan="6">&nbsp;</td>
		</tr>
		<tr>
		    <td style="background-color: lightyellow;border-bottom: 1px solid black;" colspan="6">Abfahrt Abydos</td>
		</tr>
		<tr>
                    <td>Soll Datum</td>
                    <td><input class="datepicker entermove ui-widget" type="text" size="10" maxlength="10"  id="ab_aby_soll_date" acturl="./rundlaufKopfUpdate.php"/></td>
                    <td>Soll Zeit</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="10"  id="ab_aby_soll_time" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>
		<tr>
                    <td>Ist Datum</td>
                    <td><input class="datepicker entermove ui-widget" type="text" size="10" maxlength="10"  id="ab_aby_ist_date" acturl="./rundlaufKopfUpdate.php"/></td>
                    <td>Ist Zeit</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="10"  id="ab_aby_ist_time" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>
		<tr>
		    <td>Proforma</td>
		    <td><input class="entermove ui-widget" type="text" size="8" maxlength="10" id="proforma" value="" acturl="./rundlaufKopfUpdate.php"/></td>		    
		    <td>Spediteur</td>
		    <td><input class="entermove ui-widget" type="text" size="8" maxlength="10" id="spediteur_id" value="" acturl="./rundlaufKopfUpdate.php"/></td>		    
		    <td>Fahrer</td>
		    <td><input class="entermove ui-widget" type="text" size="8" maxlength="10" id="fahrername" value="" acturl="./rundlaufKopfUpdate.php"/></td>		    
		</tr>
		
		<tr>
		    <td>LKW KZ</td>
		    <td><input class="entermove ui-widget" type="text" size="8" maxlength="10" id="lkw_kz" value="" acturl="./rundlaufKopfUpdate.php"/></td>		    		    
		</tr>
		
		<!-- ankunft Kunde -->
		<tr>
		    <td style="border-top: 1px solid black;" colspan="6">&nbsp;</td>
		</tr>
		<tr>
		    <td style="background-color: lightyellow;border-bottom: 1px solid black;" colspan="6">Ankunft Kunde</td>
		</tr>
		<tr>
		    <td>Ort</td>
		    <td><input class="entermove ui-widget" type="text" size="30" maxlength="50"  id="an_kunde_ort" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>
		<tr>
                    <td>Soll Datum</td>
                    <td><input class="datepicker entermove ui-widget" type="text" size="10" maxlength="10"  id="an_kunde_soll_date" acturl="./rundlaufKopfUpdate.php"/></td>
                    <td>Soll Zeit</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="10"  id="an_kunde_soll_time" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>
		<tr>
                    <td>Ist Datum</td>
                    <td><input class="datepicker entermove ui-widget" type="text" size="10" maxlength="10"  id="an_kunde_ist_date" acturl="./rundlaufKopfUpdate.php"/></td>
                    <td>Ist Zeit</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="10"  id="an_kunde_ist_time" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>

		<!-- ankunft Abydos -->
		<tr>
		    <td style="border-top: 1px solid black;" colspan="6">&nbsp;</td>
		</tr>
		<tr>
		    <td style="background-color: lightyellow;border-bottom: 1px solid black;" colspan="6">Ankunft Abydos</td>
		</tr>
		<tr>
                    <td>Soll Datum</td>
                    <td><input class="datepicker entermove ui-widget" type="text" size="10" maxlength="10"  id="an_aby_soll_date" acturl="./rundlaufKopfUpdate.php"/></td>
                    <td>Soll Zeit</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="10"  id="an_aby_soll_time" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>
		<tr>
                    <td>Ist Datum</td>
                    <td><input class="datepicker entermove ui-widget" type="text" size="10" maxlength="10"  id="an_aby_ist_date" acturl="./rundlaufKopfUpdate.php"/></td>
                    <td>Ist Zeit</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="10"  id="an_aby_ist_time" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>
		<tr>
		    <td>Nutzlast [to]</td>
		    <td><input class="entermove ui-widget" type="text" size="10" maxlength="10"  id="an_aby_nutzlast" acturl="./rundlaufKopfUpdate.php"/></td>		    
		</tr>
		
		<!-- sonstiges -->
		<tr>
		    <td style="border-top: 1px solid black;" colspan="6">&nbsp;</td>
		</tr>
		<tr>
		    <td style="background-color: lightyellow;border-bottom: 1px solid black;" colspan="6">Sonstiges</td>
		</tr>
		<tr>
		    <td>Preis vereinbart</td>
		    <td><input class="entermove ui-widget" type="text" size="10" maxlength="10"  id="preis" acturl="./rundlaufKopfUpdate.php"/></td>
		    <td>Rabatt [%]</td>
		    <td><input class="entermove ui-widget" type="text" size="3" maxlength="4"  id="rabatt" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>
		<tr>
		    <td>Betrag</td>
		    <td><input class="entermove ui-widget" type="text" size="3" maxlength="4"  id="betrag" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>
		<tr>
		    <td>Rechnung</td>
		    <td><input class="entermove ui-widget" type="text" size="3" maxlength="4"  id="rechnung" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>
		<tr>
		    <td>Bemerkung</td>
		    <td><input class="entermove ui-widget" type="text" size="20" maxlength="255"  id="bemerkung" acturl="./rundlaufKopfUpdate.php"/></td>
		</tr>

                <tr>
                    <td><input class="entermove ui-widget" type="button" value="speichern / uložit" id="rundneu" acturl="./rundlaufInsert.php"/></td>
                </tr>

            </table>
        </div>

        <hr>
        <div id="ersatzteileeingabetable">
        </div>
    </div>
</body>
</html>
