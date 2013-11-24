<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Behaelter Bewegung
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<!--<link rel='stylesheet' href='./print.css' type='text/css' media="print"/>-->

<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">
    <script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
    <script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
    <script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
<!--<script type="text/javascript" src="../js/jquery.js"></script>-->
<script type="text/javascript" src="./bewegung.js"></script>


</head>


{popup_init src="../js/overlib.js"}


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
        Behaelter Bewegung
    </div>

<!--    <div id="helpdiv">

    </div>-->
    <div id="formular_telo">

        <div id="behaelterinfo">
            <table>

                <tr>
                    <td>Import</td>
                    <td><input class="entermove" type="text" size="10" maxlength="20" id="im" acturl="./importBewUpdate.php"/></td>
                    
                </tr>
                <tr>
                    <td colspan="3" id="import_info">&nbsp;</td>
                </tr>
                <tr>
                    <td>Export</td>
                    <td><input class="entermove" type="text" size="10" maxlength="20" id="ex" acturl="./exportBewUpdate.php"/></td>
                </tr>
                <tr>
                    <td colspan="3" id="export_info">&nbsp;</td>
                </tr>
                <tr>
                    <td>Kunde von</td>
                    <td><input class="entermove" type="text" size="4" maxlength="4" style="text-align: right;" id="kundevon" acturl="./kundeVonUpdate.php"/></td>
                    <td colspan="3" id="kundevon_info"></td>
                </tr>
                <tr>
                    <td>Kunde nach</td>
                    <td><input class="entermove" type="text" size="4" maxlength="4" style="text-align: right;" id="kundenach" acturl="./kundeNachUpdate.php"/></td>
                    <td colspan="3" id="kundenach_info"></td>
                </tr>
                <tr>
                    <td>Datum [DD.MM.JJJJ]</td>
                    <td><input class="datepicker entermove" type="text" size="10" maxlength="10"  id="datum" acturl="./datumBehBewUpdate.php"/></td>
                </tr>

                <tr>
                    <td>BehaelterNr</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="20" id="behaelternr" acturl="./behaelternrBewegungUpdate.php"/></td>
                    <td id="behaelternr_info"></td>
                    <td>Behaelterinhalt</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="20" id="bein" acturl="."/></td>
                    <td>Behaelterzustand</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="20" id="bezu" acturl="."/></td>
                    <td>Stk</td>
                    <td><input class="entermove" type="text" size="6" maxlength="6" style="text-align: right;" id="stk" value="0" acturl="./stkBewegungUpdate.php"/></td>
                </tr>

                <tr>
                    <td><input class="entermove" type="button" value="Enter" id="enter" acturl="./enterBehBew.php"</td>
                    <td><input type="button" value="Abbruch" id="abbruch"</td>
                </tr>
                <tr>
                    <td colspan="6" id="enter_info">&nbsp;</td>
                </tr>
            </table>
        </div>

        <div id="bewegungendiv">
            
        </div>
    </div>
</body>
</html>
