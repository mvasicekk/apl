<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Reparatureingabe
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<!--<link rel='stylesheet' href='./print.css' type='text/css' media="print"/>-->

<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">
<script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
<script type="text/javascript" src="./repeingabe.js"></script>


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
        Reparatureingabe
    </div>

    <div id="formular_telo">

        <div id="behaelterinfo">
            <input type="hidden" id="repid" value="0"/>
            
            <table>
                <tr>
                    <td>Geraet-Invnummer</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="20" id="invnummer" value="" acturl="./reparaturKopfUpdate.php"/></td>
                    <td id="invnummer_info"></td>
                </tr>
                <tr>
                    <td>Datum [DD.MM.JJJJ]</td>
                    <td><input class="datepicker entermove ui-widget" type="text" size="10" maxlength="10"  id="datum" acturl="./reparaturKopfUpdate.php"/></td>
                </tr>
                <tr>
                    <td>repariert von [Persnr]</td>
                    <td><input class="entermove ui-widget" type="text" size="5" maxlength="5"  value="" id="persnr_reparatur" acturl="./reparaturKopfUpdate.php"/></td>
                </tr>

                <tr>
                    <td>zur Reparatur von [Persnr]</td>
                    <td><input class="entermove ui-widget" type="text" size="5" maxlength="5"  value="" id="persnr_ma" acturl="./reparaturKopfUpdate.php"/></td>
                </tr>

                <tr>
                    <td>Reparaturzeit [Minuten]</td>
                    <td><input class="entermove ui-widget" type="text" size="5" maxlength="5"  value="0" id="repzeit" acturl="./updateRepZeit.php"/></td>
                </tr>

                <tr>
                    <td>Reparaturbemerkung</td>
                    <td><input class="entermove ui-widget" type="text" size="40" maxlength="255"  value="" id="repbemerkung" acturl="./updateRepBemerkung.php"/></td>
                </tr>
                <tr>
                    <td id="reparaturIDinfo">&nbsp;</td>
                    <td><input class="entermove ui-widget" type="button" value="neue Reparatur" id="repneu" acturl="./reparaturInsert.php"/></td>
                </tr>

            </table>
        </div>

        <hr>
        <div id="ersatzteileeingabetable">
        </div>
    </div>
</body>
</html>
