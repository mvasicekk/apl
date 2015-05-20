<?
session_start();
// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
if (!isset($_SESSION['user'])) {
    echo "Benutzer nicht angemeldet ! / Uzivatel neni prihlasen !<br>";
    echo "an der Hauptseite bitte anmelden ! / prihlaste se prosim na hlavni strance !";
    exit();
}