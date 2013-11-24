// JavaScript Document


function stahniData(auftr,pal,teil,akce) {

	
  var hlavicka = document.getElementsByTagName("head")[0];
  var dataLoader = document.getElementById("scriptLoader");
  
    if(dataLoader) hlavicka.removeChild(dataLoader);
    
  script = document.createElement("script");
  script.id = "scriptLoader";
  script.src = "./operace.php?auftr="+auftr+"&pal="+pal+"&teil="+teil+"&akce="+akce;
  var x = document.getElementsByTagName("head")[0];
  x.appendChild(script);
  
  return false;
}

function stahniDataPers(persNr,akce) {
  var hlavicka = document.getElementsByTagName("head")[0];
  var dataLoader = document.getElementById("scriptLoader");
  
    if(dataLoader) hlavicka.removeChild(dataLoader);
    
  script = document.createElement("script");
  script.id = "scriptLoader";
  script.src = "./operace.php?pers="+persNr+"&akce="+akce;
  var x = document.getElementsByTagName("head")[0];
  x.appendChild(script);
  
  return false;
}

function stahniDataMinuty(auftr, teil, tat, akce, feld) {
if (tat!=0){
  var hlavicka = document.getElementsByTagName("head")[0];
  var dataLoader = document.getElementById("scriptLoader");
  
    if (dataLoader) hlavicka.removeChild(dataLoader);
    
  script = document.createElement("script");
  script.id = "scriptLoader";
  script.src = "./operace.php?auftr="+auftr+"&teil="+teil+"&tat="+tat+"&akce="+akce+"&feld="+feld;
  var x = document.getElementsByTagName("head")[0];
  x.appendChild(script);
  }else{
  document.getElementById('tat'+feld+'zeit').value = 0;
  document.getElementById('kz'+feld).value = 0;
  }
  return false;
  
}

function stahniDataAusTyp(art){
if (art!=0){
  var hlavicka = document.getElementsByTagName("head")[0];
  var dataLoader = document.getElementById("scriptLoader");
  
    if(dataLoader) hlavicka.removeChild(dataLoader);
    
  script = document.createElement("script");
  script.id = "scriptLoader";
  script.src = "./operace.php?art="+art+"&akce=ausTyp";
  var x = document.getElementsByTagName("head")[0];
  x.appendChild(script);
  }
  return false;
}

function naplnPalety(pole){
  var polePalety = pole.split(",");
  var i = 0;
  document.getElementById('palnr').options.length = 0;
  for (i=0;i < (polePalety.length);i++){
  
    if (document.all) {
       var arg1=document.getElementById('palnr').length;
    }else{
       var arg1=0
    }

    var no = new Option();
    no.value = polePalety[i];
    no.text = polePalety[i];
    document.getElementById('palnr').add(no,arg1);
  }
}


function naplnDily(pole){
  var dily = pole.split(",");
  var i = 0;
    document.getElementById('teil').options.length = 0;
  for (i=0;i <(dily.length);i++){
    
    if (document.all) {
      var arg1=document.getElementById('teil').length;
    }else{
      var arg1=0
    }
    
    var no = new Option();
    no.value = dily[i];
    no.text = dily[i];
    document.getElementById('teil').add(no,arg1)
  }
}

function naplnOperace(pole){
  var operace = pole.split(",");
    
  for(u=1; u<7 ; u++){
  document.getElementById('tatigkeit'+u).options.length = 0;
    for (i=0; i<(operace.length);i++){
      if (document.all) {
        var arg1=document.getElementById('tatigkeit'+u).length;
      }else{
        var arg1=0
      }
      
      var no = new Option();
      no.value = operace[i];
      no.text = operace[i];
      document.getElementById('tatigkeit'+u).add(no,arg1)
    }
  }
}


function naplnPers(name,schicht){
  document.getElementById('Schicht').value = schicht;
  document.getElementById('PersName').value = name;
}

function naplnAusTyp(typ){
var typ = typ.split(",");
var i;
    document.getElementById('austyp').options.length = 0;
  for (i=0;i <(typ.length);i++){
    
    if (document.all) {
      var arg1=document.getElementById('austyp').length;
    }else{
      var arg1=0;
    }
    
    var no = new Option();
    no.value = typ[i];
    no.text = typ[i];
    void document.getElementById('austyp').add(no,arg1);
  }
}

function zeitSumm(){
var zeit;
var tz1 = document.getElementById('tat1zeit').value;
var tz2 = document.getElementById('tat2zeit').value;
var tz3 = document.getElementById('tat3zeit').value;
var tz4 = document.getElementById('tat4zeit').value;
var tz5 = document.getElementById('tat5zeit').value;
var tz6 = document.getElementById('tat6zeit').value;

tz1=parseFloat(tz1);
tz2=parseFloat(tz2);
tz3=parseFloat(tz3);
tz4=parseFloat(tz4);
tz5=parseFloat(tz5);
tz6=parseFloat(tz6);

 
 zeit=tz1+tz2+tz3+tz4+tz5+tz6;
 
 document.getElementById('tatZeitSumm').value = zeit;
}

function minutySumme(){
var stueck = document.getElementById('stueck').value;
 var min = document.getElementById('tatZeitSumm').value;
 var ausstueck = document.getElementById('ausstueck').value

 min=min/10;
 var summstueck = stueck+ausstueck;
var minSumm = min*summstueck;

document.getElementById('VzAby').value = minSumm;
}


function naplnMin(VzAby, VzKz, feld){
document.getElementById('tat'+feld+'zeit').value=VzAby;
document.getElementById('kz'+feld).value=VzKz;
}

function minutes(odTime,doTime, persNr){


odHour = odTime.substring(0,2);
odMinute= odTime.substring(2,4);
doHour = doTime.substring(0,2);
doMinute= doTime.substring(2,4);

firstTime = new Date("01 Jan 2006 "+odHour+":"+odMinute+":00");
secondTime = new Date("01 Jan 2006 "+doHour+":"+doMinute+":00");

totaltime =  secondTime-firstTime;
second = totaltime/1000;
minute = second/60;

document.getElementById('verbZeit').value = minute;
document.getElementById('VerbZ').value = minute;

timeCop(persNr);

vykon();


}

function makePause(pausTime){
minute = document.getElementById('VerbZ').value;
minute = minute-pausTime;
document.getElementById('VerbZ').value=minute;
document.getElementById('verbZeit').value=minute;
vykon();
}

function vykon(){
vzAby = document.getElementById('VzAby').value;
VerbZ = document.getElementById('VerbZ').value;

document.getElementById('vykon').value = vzAby/VerbZ*100;
}

function updateVykon(typ,ausstueck){
  if(typ==4){
    minutySumme();
    vykon();
  }
}

//Control Function

  function control(){

    var auftr = document.getElementById('AuftragsNr').value;
    var datum = document.getElementById('datum').value;
    var palnr = document.getElementById('palnr').value;
    var teil = document.getElementById('teil').value;
    
    var taetNr = new Array(document.getElementById('tatigkeit1').value, 
                           document.getElementById('tatigkeit2').value, 
                           document.getElementById('tatigkeit3').value, 
                           document.getElementById('tatigkeit4').value, 
                           document.getElementById('tatigkeit5').value, 
                           document.getElementById('tatigkeit6').value);
    
    var taetAbyZeit = new Array(document.getElementById('tat1zeit').value,
                                document.getElementById('tat2zeit').value,
                                document.getElementById('tat3zeit').value,
                                document.getElementById('tat4zeit').value,
                                document.getElementById('tat5zeit').value,
                                document.getElementById('tat6zeit').value);
                            
    var taetKdZeit = new Array(document.getElementById('kz1').value,
                               document.getElementById('kz2').value,
                               document.getElementById('kz3').value,
                               document.getElementById('kz4').value,
                               document.getElementById('kz5').value,
                               document.getElementById('kz6').value);
                           
    var persNr = document.getElementById('PersNr').value;
    var schicht = document.getElementById('Schicht').value;
    
    var stueck = document.getElementById('stueck').value;
    var ausstueck = document.getElementById('ausstueck').value;
    var ausart = document.getElementById('ausart').value;
    var austyp = document.getElementById('austyp').value;
    
    
    
    var today = new Date();
    var day = today.getDate();
    var month = today.getMonth()+1;
    var year = today.getFullYear();
    var date = day+"."+month+"."+year;
    var controlDate = new Date(date);
    var controlDateSec = controlDate.getTime();
    var timeOd = document.getElementById('do').value;
    var timeDo = document.getElementById('do').value;
    var pause = document.getElementById('pause').value;


    //Control of Datum, if greater then actuel Datum, then alert and focus field Datum!
    var datLeng = datum.length;
    
    if(datLeng < 9 || datLeng > 10){
      
      datum = datum.split(".");
      
      if(datum[0] > 31 || datum[0] < 1){
        alert("Zkontrolujte den v datumu");
        return false;
      }
      if(datum[1] < 1 || datum[1] > 12){
        alert("Zkontrolujte mìsíc v datumu");
        return false;
      }

      if(datum[2] < 2006){
        if(datum[2].length <= 2){
          datum[2] = 2000+parseFloat(datum[2]);
        }
      }
      var datumControl = new Date(datum[0]+"."+datum[1]+"."+datum[2]);
          datumSec = datumControl.getTime();
    }else{
    var datumControl = new Date(document.getElementById('datum').value);
          datumSec = datumControl.getTime();
    }
   
    if( datumSec > controlDateSec){
      alert("Byl zadán ¹patný datum! \nDas falsche Datum wurde eingegeben!");
      document.getElementById('datum').focus();
      return false;
    }
    
    //Control if first TaetNr and TaetAbyZeit are set 
    if(taetNr[0] == 0 || taetAbyZeit[0] == 0){
      alert("Zkontrolujte Operace!\n Ueberpruefen sie bitte die Taetigkeiten!");
      document.getElementById('tatigkeit1').focus();
      return false;
    }
    
    
    //Control of PersNr and Schicht
    
    if(persNr == '' || schicht == ''){
      alert("Zkontrolujte prosím osobní èíslo a ¹ichtu!\n Kontrolieren sie bitte die Personal Nummer und Schicht Nummer!");
      document.getElementById('PersNr').focus();
      return false;
    }
    
    // Control if are set Stueck or ausstueck
    if(ausstueck == 0 && stueck == 0){
      alert("Kusy a poèet kusù jsou nulové!");
      document.getElementById('stueck').focus();
      return false;
    }
    
    //Control of Ausart and Austyp if ausstueck is greather then 0
     
        if(ausstueck != 0){
      if(ausart != 0){
        if(austyp == 0){
          alert("Zkontrolujte typ zmetkù!\n Kontrolieren sie bitte den Ausschusstyp!");
          document.getElementById('austyp').focus();
          return false;
        }
      }else{
        alert("Zkontrolujte druh zmetkù!\n Kontrolieren sie bitte die Ausschussart!");
        document.getElementById('ausart').focus();
        return false;
      }
    }
    

    
  }

function timeCop(persNr){

var hlavicka = document.getElementsByTagName("head")[0];
  var dataLoader = document.getElementById("scriptLoader");
  
    if(dataLoader) hlavicka.removeChild(dataLoader);
    
  script = document.createElement("script");
  script.id = "scriptLoader";
  script.src = "./operace.php?persNr="+persNr+"&akce=timeCop";
  var x = document.getElementsByTagName("head")[0];
  x.appendChild(script);
  
  return false;
  
}

function timeCopDat(lastTime){

  var odTime = document.getElementById('od').value;

lastDate = lastTime.split(" ");
lastTime = lastDate[1].split(":");
 
odHour = odTime.substring(0,2);
odMinute= odTime.substring(2,4);

newTime = new Date("01 Jan 2006 "+odHour+":"+odMinute+":00");
newTimeSec = newTime.getTime();
time = new Date("01 Jan 2006 "+lastTime[0]+":"+lastTime[1]+":00");
timeSec = time.getTime();

if(timeSec == newTimeSec){
//    return true;
}else{
    if (confirm("Zadané èasy nesouhlasí, chcete je tak nechat?")){  
        return true; 
    }else
    {
     document.getElementById('od').focus();
     return false;}
    }

  
}

function jumpTo(taetMa){
  if(taetMa==0){
    return true;
  }else{
    window.open('../DrueckMa/drueckMa.php');
    return false;
  }

}
