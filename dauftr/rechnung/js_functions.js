// JavaScript Document
// dauftr_schnell_erfassen

EventUtil.addEventHandler(document, "keypress", checkCR);


var pole = new Array(	"teil",
							"pal_nr",
							"stk_pro_pal",
							"pal_erst",
							"increment"
);

var radekClassNameOld = new Array;
var radekIdArray = new Array;
var radekRechnungNrOld = 0;

// Removes leading whitespaces
function LTrim( value ) {
	
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	
}

// Removes ending whitespaces
function RTrim( value ) {
	
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	
}

// Removes leading and ending whitespaces
function trim( value ) {
	
	return LTrim(RTrim(value));
	
}

function rechnungteilen(rechnung)
{
	var div = document.getElementById('rechnungteilenform');
	div.style.visibility = 'visible';

    var rechnrNeu = document.getElementById('rechnrneu');
    var taetnrbedingung = document.getElementById('taetnrbedingung');

    var noveCislo = rechnung*1;
    rechnrNeu.value = noveCislo;
    taetnrbedingung.value = 2000;
	taetnrbedingung.focus();
	taetnrbedingung.select();
}

function rechnungNeuMarkierenCancel()
{
	var div = document.getElementById('rechnungteilenform');
    restoreRadekClassName();
	div.style.visibility = 'hidden';
}

/**
 * oznaci vsechny radky v tabulce vyhovujici zadane podmince
 */
function rechnungNeuMarkieren()
{
    var taetnrbedingung = document.getElementById('taetnrbedingung');
    var bedingungHodnota = taetnrbedingung.value*1;

    var rechnungNeu = document.getElementById('rechnrneu');
    var rechnungNeuHodnota = rechnungNeu.value*1;

    //alert("bedingunghodnota="+bedingungHodnota);

    // budu prochazet vsechny radky
    var tabulka = document.getElementById('rechnung_table');
    var radkyArray = tabulka.getElementsByTagName('tr');
    var abgnrValue = 0;
    // smazu obsah pole se staryma hodnotama jmen trid pro radky
    restoreRadekClassName();
    
    for(i=0;i<radkyArray.length;i++){
        var radek = radkyArray.item(i);
        var idRadku = radek.getAttribute('id');

        if(idRadku!=null){
            if((idRadku.lastIndexOf("radek",0)==0)){
                // oddelim si id polozky
                var idPolozky = idRadku.substr(6);
                //radky += "idRadku="+idRadku+"idpolozky="+idPolozky;
                // vyberu si input v bunce s abgnr
                var abgnrId = "abgnr_"+ idPolozky;
                var rechNrNeuId = "auftragsnr_" + idPolozky;

                var abgnrInput = document.getElementById(abgnrId);
                var rechNrInput = document.getElementById(rechNrNeuId);

                abgnrValue = abgnrInput.value*1;
                if(abgnrValue>bedingungHodnota){
                    //radky += "abgnrValue="+abgnrValue+"bedingungHodnota="+bedingungHodnota;
                    // oznacit odpovidajici radky
                    radekIdArray.push(idRadku);
                    radekClassNameOld.push(radek.className);
                    radek.className='rechnungTeilenHighlight';
                    radekRechnungNrOld = rechNrInput.getAttribute('value');
                    rechNrInput.setAttribute('value',rechnungNeuHodnota);
                }
            }
        }
    }
    //alert(radky);
}

function restoreRadekClassName(){
    //var debugString="";
    //alert("radekIdArray.length="+radekIdArray.length);
    
    for(i=0;i<radekIdArray.length;i++){
        var idRadku = radekIdArray[i];
        var idPolozky = idRadku.substr(6);
        var rechNrNeuId = "auftragsnr_" + idPolozky;
        var rechNrInput = document.getElementById(rechNrNeuId);

        var className = radekClassNameOld[i];
        var radek = document.getElementById(idRadku);
        radek.className = className;
        if(radekRechnungNrOld>0)
               rechNrInput.setAttribute('value',radekRechnungNrOld);
        //debugString += "idRadku="+idRadku;
    }
    //alert(debugString);
    radekClassNameOld.length=0;
    radekIdArray.length=0;
}

function rechnungNeuTeilen(){

    var rechnungNeu = document.getElementById('rechnrneu');
    var rechnungNeuHodnota = rechnungNeu.value*1;

    //alert("bedingunghodnota="+bedingungHodnota);

    // budu prochazet vsechny radky
    var tabulka = document.getElementById('rechnung_table');
    var radkyArray = tabulka.getElementsByTagName('tr');
    //var abgnrValue = 0;
    // smazu obsah pole se staryma hodnotama jmen trid pro radky
    //restoreRadekClassName();

    var seznamIdPolozek="";

    for(i=0;i<radkyArray.length;i++){
        var radek = radkyArray.item(i);
        var idRadku = radek.getAttribute('id');

        if(idRadku!=null){
            if((idRadku.lastIndexOf("radek",0)==0)){
                // oddelim si id polozky
                var idPolozky = idRadku.substr(6);
                var radekClassName = radek.className;

                if(radekClassName=="rechnungTeilenHighlight"){
                    seznamIdPolozek += idPolozky+",";
                }
            }
        }
    }
    //alert(seznamIdPolozek);
    var sUrl = "./rechnungteilen.php?rechnungNeu="+rechnungNeuHodnota+"&seznampolozek="+seznamIdPolozek.substr(0,seznamIdPolozek.length-1);
    //alert(encode(sUrl));
    YAHOO.util.Connect.asyncRequest('GET',sUrl, rechnungTeilenUpdate);
}

//----------------------------------------------------------------------

var rechnungTeilenUpdate = {
	success:	function(o){
		var domDocument = o.responseXML;
        
        
		var rechnungArray = domDocument.getElementsByTagName('rechnungNeu');

		var idArray = domDocument.getElementsByTagName('id');

		var rowsArray = domDocument.getElementsByTagName('affectedrows');
		var errorArray = domDocument.getElementsByTagName('error');

		var rechnungNeu = rechnungArray.item(0).firstChild.data;



		var rows = rowsArray.item(0).firstChild.data;

    var radky = Array();

    var tabulka = document.getElementById('rechnung_table');

	if(rows>0){
        // povedlo se, smazu z tabulky oznacene radky
        alert("Rechnung geteilt. Neue Rechnung hat Nummer :"+rechnungNeu);
        // budu prochazet vsechny radky
        
        var radkyArray = tabulka.getElementsByTagName('tr');

        for(i=0;i<radkyArray.length;i++){
            var radek = radkyArray.item(i);
            var idRadku = radek.getAttribute('id');

            if(idRadku!=null){
                if((idRadku.lastIndexOf("radek",0)==0)){
                    // oddelim si id polozky
                    var idPolozky = idRadku.substr(6);
                    var radekClassName = radek.className;

                    if(radekClassName=="rechnungTeilenHighlight"){
                        //seznamIdPolozek += idPolozky+",";
                        radky.push(idRadku);
                    }
                }
            }
        }

        var dString="";
        while(radky.length>0){
            var idrdk = radky.pop();
            dString += "idrdk="+idrdk;
            var rad = document.getElementById(idrdk);
            dString += "rad="+rad.toString();
            rad.style.display='none';
            //tabulka.removeChild(rad);
            
        }
        //alert(dString);
    }
    else{
        if(errorArray.item(0).hasChildNodes()){
            var error = errorArray.item(0).firstChild.data;
			alert(error);
		}
		else{

			}
		}
	}
}

//------------------------------------------------------------------------------
function refreshdatum(text)
{
	//alert(text);
	
	// control id je posladni cast textu pred znakem >
	var controlid=text.substring(text.lastIndexOf('>')+1,text.length);
	// hodnota pro zobrazeni v textboxu je od zacatku retezce, po znamenko >
	var hodnota=text.substring(0,text.indexOf('>'));
	// objekt s obsahem ke zmene
	var control = document.getElementById(controlid);
	
	if(hodnota=="ERROR")
	{
		control.style.backgroundColor='red';
		control.value=hodnota;
	}
	else
	{
		control.style.backgroundColor='';
		control.value=hodnota;
	}
}


function spocti_vykon()
{
	var stk_value=document.getElementById('stk').value*1;
	var auss_stk_value=document.getElementById('auss_stk').value*1;
	var vzaby_pro_stk_value=document.getElementById('vzaby_pro_stk').value*1;
	var e_sumvzaby=document.getElementById('sumvzaby');
	var verb_value=document.getElementById('verb').value*1;
	var e_sumverb=document.getElementById('sumverb');
	var e_leist_procent=document.getElementById('leist_procent');
	var e_auss_typ=document.getElementById('auss_typ');
	var e_auss_stk=document.getElementById('auss_stk');

	// vypoctu sumu casu abydos pro dany pocet kusu a cas
	
	if(e_auss_typ.value==4)
		summinuten = (stk_value+e_auss_stk.value*1) * vzaby_pro_stk_value;
	else
		summinuten = stk_value * vzaby_pro_stk_value;
		
	e_sumvzaby.value=Math.round(summinuten);
	
	if(e_sumvzaby.value!=0)
		e_sumvzaby.style.backgroundColor='yellow';
	else
		e_sumvzaby.style.backgroundColor='';

	if(verb_value!=0)
	{
		leistung_procent=100*summinuten/verb_value;
		
		e_leist_procent.value=Math.round(leistung_procent);
		
		e_sumverb.value=Math.round(verb_value);
		
		e_sumverb.style.backgroundColor='yellow';
		e_leist_procent.style.backgroundColor='yellow';
	}
	else
	{
		leistung_procent=0;
		e_sumverb.style.backgroundColor='';
		e_leist_procent.style.backgroundColor='0';
	}
}


function spocti_verb()
{
	var von = document.getElementById('von');
	var bis = document.getElementById("bis");
	var pause = document.getElementById("pause");
	
	var vonh= von.value.substr(0,2);
	var vonm= von.value.substr(3,2);
	var bish= bis.value.substr(0,2);
	var bism= bis.value.substr(3,2);
	var vonsc = new Date(0,0,0,vonh,vonm,0);
	var bissc = new Date(0,0,0,bish,bism,0);
	// vypocitam cas v sekundach
	var time = (bissc.getTime() - vonsc.getTime())/60/1000;
	return time-pause.value*1;
}

function refreshtime(jaky)
{
	var von = document.getElementById('von');
	var bis = document.getElementById("bis");
	
	var hodiny,minuty;
	
	if(jaky=='von')
		e=von;
	else
		e=bis;
		
	if(e.value.length>=4)
	{
		if(e.value.length==4)
		{
			hodiny=e.value.substr(0,2);
			minuty=e.value.substr(2,2);
			e.value=hodiny+":"+minuty;
		}
	
		var cas = spocti_verb();
	
		if(cas<0)
		{
			bis.style.backgroundColor='red';
			von.style.backgroundColor='red';
		}
		else
		{
			bis.style.backgroundColor='';
			von.style.backgroundColor='';
		}
		document.getElementById('verb').value = cas;
		spocti_vykon();
	}
	else
	{
		e.style.backgroundColor='red';
		e.value='ERROR';
		e.select();
		e.focus();
	}
}

function refreshpause()
{
	cas=spocti_verb();
	verb.value=cas;
	
	if(cas<0)
	{
		pause.style.backgroundColor='red';
	}
	else
	{
		pause.style.backgroundColor='';
	}
	
	spocti_vykon();
}



function zjistiId(element){
for(i=0;i<pole.length; i++){
if(pole[i]== element){return i;}
}
}


function checkCR() {

	var oEvent = EventUtil.getEvent();
	
							
    var element =  oEvent.target.id;

    if (oEvent.keyCode == 13) {
		var i = zjistiId(element) +1;
		if(i<pole.length)
			document.getElementById(pole[i]).focus();
		//oEvent.preventDefault();
	  }
    else{
      return true;
    } 
	
}
  




function init_form(rezim)
{
	//var teil = document.getElementById('auslieferdatum');
	//teil.focus();
	//teil.value='';
	
}


function js_validate_G(control)
{

	var hodnota = control.value
	
	
	if(hodnota=='G'||hodnota=='g')
	{
		control.value=hodnota.toUpperCase();
		control.setAttribute('value',hodnota.toUpperCase());
		control.style.backgroundColor='';
	}
	else
	{
		//chyba validace
		control.value='';
		control.setAttribute('value','');
		control.style.backgroundColor='';
		//failed.className='error';
		//failed.value=error_description;
	}
}

function js_validate_float(control)
{

	var hodnota = control.value
	
	re = /,/
	novahodnota=hodnota.replace(re,".");
	
	floatvalue = parseFloat(novahodnota);
	
	if(!isNaN(floatvalue)&&(floatvalue>=0))
	{
		control.value=floatvalue;
		control.setAttribute('value',floatvalue);
		control.style.backgroundColor='';
	}
	else
	{
		//chyba validace
		control.style.backgroundColor='red';
		//failed.className='error';
		//failed.value=error_description;
	}
}

function js_validate_pal_nr(control)
{
	intvalue=parseInt(control.value);
	//alert(intvalue);
	if((intvalue>0))
	{
		control.style.backgroundColor='';
	}
	else
	{
		control.style.backgroundColor='red';
		control.value='0';
	}
}

function js_validate_stk_pro_pal(control)
{
	intvalue=parseInt(control.value);
	//alert(intvalue);
	if((intvalue>0))
	{
		control.style.backgroundColor='';
	}
	else
	{
		control.style.backgroundColor='red';
		control.value='0';
	}
}

function js_validate_pal_erst(control)
{
	intvalue=parseInt(control.value);
	//alert(intvalue);
	if((intvalue>0))
	{
		control.style.backgroundColor='';
	}
	else
	{
		control.style.backgroundColor='red';
		control.value='0';
	}
}


function js_validate_jn(control)
{
	var hodnota=control.value
	if((hodnota.toUpperCase()=='J')||(hodnota.toUpperCase()=='N'))
	{
		control.value=hodnota.toUpperCase();
		control.style.backgroundColor='';
	}
	else
	{
		control.style.backgroundColor='red';
		control.value='';
	}
}




function saverefresh(xml)
{
	//alert('saverefresh');
	var sqlarray = xml.getElementsByTagName('sql');
	//var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	//var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	var serieinsertu_array = xml.getElementsByTagName('lager_serieinsertu');
	
	
	
	if(serieinsertu_array.item(0).hasChildNodes())
	{
		var sqlinsert_array = xml.getElementsByTagName('lager_sqlinsert');
		var affected_rows_array = xml.getElementsByTagName('affected_rows');
		var mysqlerror_array = xml.getElementsByTagName('mysqlerror');

		for(i=0;i<sqlinsert_array.length;i++)
		{
			//alert(sqlinsert_array.item(i).firstChild.data);
			if(mysqlerror_array.item(0).hasChildNodes())
				alert(mysqlerror_array.item(i).firstChild.data);
		}
	}
	
	
	//nove = window.open('');
	
	/*
	for(i=0;i<sqlarray.length;i++)
	{
		sql=sqlarray.item(i).firstChild.data;
		//alert(sql);
		//nove.document.write('sql='+sql);
		//nove.document.write('<br>');
	}
	*/
	
	window.location.reload();
	
	//alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
		
}



function encodeControlValue(i)
{
	var control = document.getElementById(i);
	return encodeURI(control.value);
}


function encodeSelectControlValue(i)
{
	var control = document.getElementById(i);
	var optionarray = control.getElementsByTagName('option');
	var	selectedvalue=optionarray.item(control.selectedIndex).getAttribute('value');
	return encodeURI(selectedvalue);
}



function validate_auftragsnr(xml)
{
	var field=xml.getElementsByTagName('auftragsnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	
	//alert(field.substring(0,5));
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
	}
}

function validate_pal(xml)
{
	//alert(xml);
	var field=xml.getElementsByTagName('teil').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_teil=document.getElementById('teil');
	var abgnrarray=xml.getElementsByTagName('abgnr');
	
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		e.value=field;
		e_teil.value='';
		e.focus();
		e.select();
	}
	else
	{
		// vytvorim seznam moznych operaci
		operace='';
		for(i=0;i<abgnrarray.length;i++)
		{
			operace+=abgnrarray.item(i).firstChild.data+',';
		}
	
		e_tatnrarray=document.getElementById('tatnrarray');

		e_tatnrarray.value=operace.substring(0,operace.length-1);
		
		e_teilbez=document.getElementById('teilbez');

		e_teilbez.value=xml.getElementsByTagName('teilbez').item(0).firstChild.data;
		
		e.style.backgroundColor='';
		
		e_teil.value=field;
		
		/*
		if(e.value!=0)
		{
			e_teil.value=field;
			//e_teil.disabled='true';
			//document.getElementById('mehr').focus();
		}
		else
		{

			e_teil.select();
		}
		*/
	}
}

function validate_abgnr1(xml)
{
	//alert('validate abgnr');
	var field=xml.getElementsByTagName('abgnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_tat_abymin=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data+'_abymin');
	var e_tat_kdmin=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data+'_kdmin');
	var e_sumvzaby=document.getElementById('vzaby_pro_stk');
	var e_tatbez=document.getElementById('tatbez');
	var mehr_value=document.getElementById('mehr').value;
	var controlid=xml.getElementsByTagName('controlid').item(0).firstChild.data;
	var bezd_array=xml.getElementsByTagName('bezd');
	var bezt_array=xml.getElementsByTagName('bezt');
	
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		e.value=field;
		e_tat_abymin.value='0';
		e.focus();
		e.select();
	}
	else
	{
		var field_vzaby=xml.getElementsByTagName('vzaby').item(0).firstChild.data;
		var field_vzkd=xml.getElementsByTagName('vzkd').item(0).firstChild.data;
		
		if(mehr_value>0)
		{
			document.getElementById('persnr').focus();
		}
		
		if((mehr_value>0)&&(controlid=='tat2'||controlid=='tat3'||controlid=='tat4'||controlid=='tat5'||controlid=='tat6'))
		{
			e_tat_abymin.value=0;
			e_tat_kdmin.value=0;
			e.style.backgroundColor='red';
			e.value=0;
		}
		else
		{
			if(bezt_array.item(0).hasChildNodes())
			{
				//alert('ma detatka');
				e_tatbez.value=e.value+' '+bezt_array.item(0).firstChild.data;
			}
			else
			{
				//alert('nema detatka');
				e_tatbez.value=' ';
			}
				
			e_tat_abymin.value=field_vzaby;
			e_tat_kdmin.value=field_vzkd;
			e.style.backgroundColor='';
		}
		
		soucet_vzaby=0;
		// projedu vsechny policka tatX_abymin a sectu cisla v nich obsazene
		for(i=1;i<7;i++)
		{
			var policko = document.getElementById('tat'+i+'_abymin');
			soucet_vzaby+=policko.value*1;
		}
		
		e_sumvzaby.value=soucet_vzaby;
		
	}
}

function validate_persnr(xml)
{
	
	var field=xml.getElementsByTagName('persnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_persname=document.getElementById('persname');
	var e_schicht=document.getElementById('schicht');
	
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
		e_persname.value=xml.getElementsByTagName('name').item(0).firstChild.data;
		e_schicht.value=xml.getElementsByTagName('schicht').item(0).firstChild.data;
	}
}

function js_auss_stk_validate()
{
	if((document.getElementById('stk').value==0)&&(document.getElementById('auss_stk').value==0))
	{
		document.getElementById('stk').style.backgroundColor='red';
		document.getElementById('auss_stk').style.backgroundColor='red';
	}
	else
	{
		document.getElementById('stk').style.backgroundColor='';
		document.getElementById('auss_stk').style.backgroundColor='';
	}

	spocti_vykon();
}


function js_stk_validate()
{
	var stk_value=document.getElementById('stk').value*1;
	var auss_stk_value=document.getElementById('auss_stk').value*1;
	var vzaby_pro_stk_value=document.getElementById('vzaby_pro_stk').value*1;
	var e_sumvzaby=document.getElementById('sumvzaby');
	
	spocti_vykon();
	
}



function validate_auss_art(xml)
{
	
	var field=xml.getElementsByTagName('auss_art').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	
	if(xml.getElementsByTagName('auss_typ').item(0).hasChildNodes())
		var auss_typ_value=xml.getElementsByTagName('auss_typ').item(0).firstChild.data;
	else
		var auss_typ_value=0;
		
	var e_auss_typ=document.getElementById('auss_typ');
	
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
		e_auss_typ.value=auss_typ_value;
	}
	
	spocti_vykon();
}

function validate_auss_typ(xml)
{
	//alert('validate_auss_typ');
	
	var field=xml.getElementsByTagName('auss_typ').item(0).firstChild.data;
	//var sql=xml.getElementsByTagName('sql').item(0).firstChild.data;
	//alert(field);
	//alert(sql);
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
	}
	spocti_vykon();
}


function validate_mehr(xml)
{
	var e_tatnrarray=document.getElementById('tatnrarray');
	var field=xml.getElementsByTagName('abgnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var abgnrarray=xml.getElementsByTagName('abgnr');
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
		// vytvorim seznam moznych operaci
		operace='';
		for(i=0;i<abgnrarray.length;i++)
		{
			operace+=abgnrarray.item(i).firstChild.data+',';
		}

		if(e.value>0)
		{
			e_tatnrarray.value=operace.substring(0,operace.length-1);
			// zpristupnit policko pro zmenu casu za minutu vzaby_pro_stk
			var e_vzaby_pro_stk = document.getElementById('vzaby_pro_stk');
			e_vzaby_pro_stk.disabled=false;
			//pridat do pole elementvzaby_pro_stk za element auss_typ
			novepole = new Array(pole.length+1);
			for(i=0;i<pole.length;i++)
			{
				novepole.push(pole[i]);
				if(pole[i]=='auss_typ')
					novepole.push('vzaby_pro_stk');
			}
			//obsah noveho pole nakopirovat zpet do puvodniho pole
			for(i=0;i<novepole.length;i++)
			{
				pole[i]=novepole[i];
			}
			//alert(pole.toString());
		}
		
	}
}


function validate_bis(xml)
{
	var field=xml.getElementsByTagName('bis').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_von=document.getElementById('von');
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		// mam nejakou chybu pri zadavani casu
		// zjistim typ chyby
		
		typ_chyby=xml.getElementsByTagName('errordescription').item(0).firstChild.data;
		e.style.backgroundColor='red';
		e_von.style.backgroundColor='red';
		alert(typ_chyby);
		
		var lastvonbis_array=xml.getElementsByTagName('lastvonbis');
		if(lastvonbis_array.item(0).hasChildNodes())
		{
			var lvon=xml.getElementsByTagName('lvon').item(0).firstChild.data;
			var lbis=xml.getElementsByTagName('lbis').item(0).firstChild.data;
			text="posledni vykon od "+lvon.substring(10,lvon.length-3)+" do "+lbis.substring(10,lbis.length-3);
			alert(text);
		}
		//alert(e.value);
		
		//e.value=field;
		//e.focus();
		//e.select();
	}
	else
	{
		e.style.backgroundColor='';
		e_von.style.backgroundColor='';
	}
}

/* function that selects a range in the text object passed as parameter */
function selectRange(oText, start, length)
{
	// check to see if in IE or FF
	if (oText.createTextRange)
	{
		//IE
		var oRange = oText.createTextRange();
		oRange.moveStart("character", start);
		oRange.moveEnd("character", length - oText.value.length);
		oRange.select();
	}
	else
	// FF
	if (oText.setSelectionRange)
	{
		oText.setSelectionRange(start, length);
	}
	oText.focus();
}


function abgnr_onkeyup(controlid,value)
{
	var e_tatnrarray=document.getElementById('tatnrarray');
	var e_tatbez=document.getElementById('tatbez');
	var e=document.getElementById(controlid);
	
	k=e.value;
	// vytvorim si pole moznych cinnosti, hodnoty prevzate z policka tatnrarray
	abgnrarray = e_tatnrarray.value.split(',');
	nove_abgnrarray = new Array();
	nasel=0;
	cislo_policka=controlid.substr(3)*1;
	
	if(k!=0&&k.length>0)
	{
		// vytvorim si nove pole abgnrarray ve kterem nebudou prvky, ktere jsou obsazene v predchozich polickach
		for(i=0;i<abgnrarray.length;i++)
		{
			nasel_policko=0;
			
			for(j=1;j<cislo_policka;j++)
			{
				id_policka='tat'+j;
				hodnota_k_odstraneni=document.getElementById(id_policka).value;
				if(hodnota_k_odstraneni==abgnrarray[i])
				{
					nasel_policko=1;
					break;
				}
			}

			if(!nasel_policko)
			nove_abgnrarray.push(abgnrarray[i]);
		}
		
		for(i=0;i<nove_abgnrarray.length;i++)
		{
			if(nove_abgnrarray[i].indexOf(k)==0)
			{
				nasel=1;
				break;
			}
		}
	}
	
	if(nasel)
	{
		e_tatbez.value=nove_abgnrarray[i];
		e.value=nove_abgnrarray[i];
		
		// vyselektovat rozsah retezce k prepsani
		selectRange(e,k.length,10);
		
	}
	//e_tatbez.value=e.value;
	
}


function pissuggest(xml)
{
	//var foot = document.getElementById('form_footer_tlacitka_reporty');
	var foot = document.getElementById('suggest');
	var scroll = document.getElementById('scroll');
	var kw = document.getElementById('teil');

	var div ="";

	teilArray = xml.getElementsByTagName('teilnr');
	kundeArray =xml.getElementsByTagName('kunde');
	gewArray = xml.getElementsByTagName('gew');
	teillangArray = xml.getElementsByTagName('teillang');
	teilbezArray = xml.getElementsByTagName('bezeichnung');
	
// vytvorim tabulku s vysledkama
	var div="<table>";
	// pokud mam nejake vysledky, zobrazim hlavicku tabulky
	if(teilArray.length>0)
	{
		div+="<tr class='result_table_header'><td>teil</td><td>Bezeichnung</td><td>Gewicht</td></tr>";
	}
	for(i=0;i<teilArray.length;i++)
	{
		dil=teilArray.item(i).firstChild.data;

		div+="<tr id='tr"+i+"' onclick='handleOnClickSuggest(this);' onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);'>";
		//tlustydil="<font color='red'><b>"+dil.substring(0,kw.value.length)+"</b></font>";
		//zbytekdilu=dil.substring(kw.value.length,dil.length);		
		div+="<td id='a"+i+"'>"+teilArray.item(i).firstChild.data+"</td>";
		//div+="<td id='a"+i+"'>"+tlustydil+zbytekdilu+"</td>";
		div+="<td>"+teilbezArray.item(i).firstChild.data+"</td>";
		div+="<td align='right'>"+gewArray.item(i).firstChild.data+"</td>";
		div+="<tr>";
		//dily+="kunde:"++"teil:"+teilArray.item(i).firstChild.data+"bez:"+teilbezArray.item(i).firstChild.data+"original:"+teillangArray.item(i).firstChild.data+"gew:"+gewArray.item(i).firstChild.data+"<br>";
	}
	div+="</table>";
	foot.innerHTML=div;
	if(teilArray.length>0)
	{
		//scroll.style.left=getRealPos(kw,"x")+'px';//kw.style.left;
		//scroll.style.top=getRealPos(kw,"y")+'px';//kw.style.top;
		//bez.value=getRealPos(kw,"x")+'px'+";"+getRealPos(kw,"y")+'px';
		scroll.style.visibility = "visible";
		scroll.style.height="250px";
		scroll.scrollTop=0;
		autocompleteKeyword();
	}
	else
	{
		scroll.style.visibility = "hidden";
		scroll.style.height="0";
	}
}

function autocompleteKeyword()
{
	var oKeyword = document.getElementById("teil");
	position = 0;
	deselectAll();
	
	start=oKeyword.value.length;
	document.getElementById("tr0").className="highlightrow";	
	
	updateKeywordValue(document.getElementById("tr0"));
	selectRange(oKeyword,start,oKeyword.value.length);
}

function updateKeywordValue(oTr)
{
	var oKeyword=document.getElementById("teil");
	
	slovo=document.getElementById("a"+oTr.id.substring(2,oTr.id.length)).childNodes[0].data;
	//slovo="a"+oTr.id.substring(2,oTr.id.length);
	oKeyword.value=slovo;
}


function selectRange(oText, start, length)
{
// check to see if in IE or FF
if (oText.createTextRange)
{
//IE
var oRange = oText.createTextRange();
oRange.moveStart("character", start);
oRange.moveEnd("character", length - oText.value.length);
oRange.select();
}
else
// FF
if (oText.setSelectionRange)
{
oText.setSelectionRange(start, length);
}
oText.focus();
}

function deselectAll()
{
	
}

function handleOnMouseOver(oTr)
{
	//deselectAll();
	oTr.className="highlightrow";
	position = oTr.id.substring(2,oTr.id.length);
}

function handleOnMouseOut(oTr)
{
	oTr.className="";
	position = -1;
}

function handleOnClickSuggest(oTr)
{
	
	//updateKeywordValue(oTr);
	var rechnung = oTr.id
	
	document.location.href='./rechumrechwdhbearbeiten.php?rechnung='+rechnung.substring(2);
}

function validate_teil(xml)
{


	//alert('validate teil');
	
	var field=xml.getElementsByTagName('teil').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	
	if(field.substring(0,5)=="ERROR")
	{
		// mam nejakou chybu
		// zjistim typ chyby
		
		typ_chyby=xml.getElementsByTagName('errordescription').item(0).firstChild.data;
		e.style.backgroundColor='red';
		alert(typ_chyby);
	}
	else
	{
		e.style.backgroundColor='';
		// pokud mam polozky fremdauftr, fremdpos a fremdausauftrag, tak je zapisu do
		// odpovidajicich textboxu
		if(xml.getElementsByTagName('fremdausauftrag').item(0).hasChildNodes())
			var fremdausauftrag = xml.getElementsByTagName('fremdausauftrag').item(0).firstChild.nodeValue;
		else
			var fremdausauftrag = '';
			
		if(xml.getElementsByTagName('fremdauftrag').item(0).hasChildNodes())
			var fremdauftrag = xml.getElementsByTagName('fremdauftrag').item(0).firstChild.nodeValue;
		else
			var fremdauftrag = '';
		
		if(xml.getElementsByTagName('fremdpos').item(0).hasChildNodes())
			var fremdpos = xml.getElementsByTagName('fremdpos').item(0).firstChild.nodeValue;
		else
			var fremdpos = '';

		// zajistim si pristup na textboxy
		var e_fremdausauftrag = document.getElementById('fremdausauftrag');
		e_fremdausauftrag.value=fremdausauftrag;
		
		var e_fremdauftrag = document.getElementById('fremdauftr');
		e_fremdauftrag.value=fremdauftrag;
		
		var e_fremdpos = document.getElementById('fremdpos');
		e_fremdpos.value=fremdpos;



		
		// vytvorit tabulku s pozicema z pracovniho planu
		tabulka="<table border='0' cellspacing='2' id='positionenTable'>";
		var div = document.getElementById('suggest');
		var abgnrarray = xml.getElementsByTagName('abgnr');
		var tatarray = xml.getElementsByTagName('tat');
		var vzkdarray = xml.getElementsByTagName('vzkd');
		var preisarray = xml.getElementsByTagName('preis');
		var vzabyarray = xml.getElementsByTagName('vzaby');
		var kzgutarray = xml.getElementsByTagName('kzgut');
		var kzdruckarray = xml.getElementsByTagName('kzdruck');
		var runden = xml.getElementsByTagName('runden').item(0).firstChild.data;
		
		
		for(i=0;i<abgnrarray.length;i++)
		{
			trid='tr'+i;
			var preis=parseFloat(preisarray.item(i).firstChild.data);
			preis_hodnota=preis.toFixed(runden);

			var vzkd=parseFloat(vzkdarray.item(i).firstChild.data);
			vzkd_hodnota=vzkd.toFixed(runden);

			var vzaby=parseFloat(vzabyarray.item(i).firstChild.data);
			vzaby_hodnota=vzaby.toFixed(2);
			
			var kzdruck=parseInt(kzdruckarray.item(i).firstChild.data);
			kzdruck_hodnota=kzdruck;
			
			if(kzgutarray.item(i).hasChildNodes())
					kzgut_hodnota=kzgutarray.item(i).firstChild.data;
				else
					kzgut_hodnota='';
				
			
			if(kzdruck_hodnota!=0)
			{
				if(kzgut_hodnota=='G')
					tabulka+="<tr id='tr"+i+"' class='Gselected'>";
				else
					tabulka+="<tr id='tr"+i+"' class='selected'>";
			}
			else
			{
				if(kzgut_hodnota=='G')
					tabulka+="<tr id='tr"+i+"' class='Gnoselected'>";
				else
					tabulka+="<tr id='tr"+i+"' class='noselected'>";
			}

		
			str_onclick = "toggle_kzdruck('"+trid+"');";
			//alert(str_onclick);	
				tabulka+="<td title='kliknutim pridate/odeberete pozici do zakazky' onclick="+str_onclick+" align='left'>";
				tabulka+=tatarray.item(i).firstChild.data;
				tabulka+="</td>";
				
				tabulka+="<td align='right'>";
				tabulka+=abgnrarray.item(i).firstChild.data;
				tabulka+="</td>";

				tabulka+="<td align='right'>";
				tabulka+=preis_hodnota;
				tabulka+="</td>";

				tabulka+="<td align='right'>";
				tabulka+="<input onblur='js_validate_float(this);' class='edit_dpos' maxlength='6' size='6' name='vzkd' id='vzkd"+i+"' value='"+vzkd_hodnota+"'/>"
				tabulka+="</td>";

				tabulka+="<td align='right'>";
				tabulka+="<input onblur='js_validate_G(this);' class='edit_dpos' maxlength='1' size='1' name='kzgut' id='kzgut"+i+"' value='"+kzgut_hodnota+"'/>"
				tabulka+="</td>";

				tabulka+="<td align='right'>";
				tabulka+="<input onblur='js_validate_float(this);' class='edit_dpos' maxlength='6' size='6' name='vzaby' id='vzaby"+i+"' value='"+vzaby_hodnota+"'/>"
				tabulka+="</td>";
				

			tabulka+="</tr>";
		}
		tabulka+="</table>";
		
		div.innerHTML=tabulka;
		//alert(field);
	}

	/*
	var field=xml.getElementsByTagName('bis').item(0).firstChild.data;
	
	var e_von=document.getElementById('von');
	
	//alert(field);
	
	*/
}

function toggle_kzdruck(id)
{

	//alert(id);
	radek=document.getElementById(id);
	
	if(radek.className=='selected')
	{
		radek.className='noselected';
		return;
	}
	
	if(radek.className=='noselected')
	{
		radek.className='selected';
		return;
	}
	

	if(radek.className=='Gnoselected')
	{
		radek.className='Gselected';
		return;
	}

	if(radek.className=='Gselected')
	{
		radek.className='Gnoselected';
		return;
	}
	
}


function fillParamList()
{
	var paramList = document.getElementById('paramlist');
	var str_teil=document.getElementById('teil').value;
	var str_pal_nr=document.getElementById('pal_nr').value;
	var str_fremdauftr=document.getElementById('fremdauftr').value;
	var str_stk_pro_pal=document.getElementById('stk_pro_pal').value;
	var str_fremdpos=document.getElementById('fremdpos').value;
	var str_pal_erst=document.getElementById('pal_erst').value;
	var str_fremdausauftrag=document.getElementById('fremdausauftrag').value;
	var str_increment=document.getElementById('increment').value;
	var str_exgeplannt=document.getElementById('exgeplannt').value;

	seznam="";
	seznam+="teil="+encodeControlValue('teil');
	seznam+="&pal_nr="+encodeControlValue('pal_nr');
	seznam+="&fremdauftr="+encodeControlValue('fremdauftr');
	seznam+="&stk_pro_pal="+encodeControlValue('stk_pro_pal');
	seznam+="&fremdpos="+encodeControlValue('fremdpos');
	seznam+="&pal_erst="+encodeControlValue('pal_erst');
	seznam+="&fremdausauftrag="+encodeControlValue('fremdausauftrag');
	seznam+="&increment="+encodeControlValue('increment');
	seznam+="&exgeplannt="+encodeControlValue('exgeplannt');

	// vybrat z tabulky s pozicema radky s className=selected nebo className=Gselected
	var tabulka = document.getElementById('positionenTable');

	// vyberu radky a pujdu po radcich tabulky
	var radky = tabulka.getElementsByTagName('tr');

	seznam+="&positionen=";
	for(i=0;i<radky.length;i++)
	{
		var radek = radky.item(i);
		//alert("radekid="+radek.id+"className="+radek.className);
		if(radek.className=='selected' || radek.className=='Gselected')
		{
			//seznam+=radek.id;
			//alert("radekid="+radek.id+"className="+radek.className);
			// vyberu jednotliva policka na radku
			tdArray = radek.getElementsByTagName('td');
			// vytvorim retezec z obsahu jednotlivych bunek
			for(j=0;j<tdArray.length;j++)
			{
				bunka = tdArray.item(j);
				if(bunka.hasChildNodes())
				{
					// zkusim, jestli tam nemam element input
					if(bunka.firstChild.tagName=='INPUT')
					{
						seznam+=bunka.firstChild.getAttribute('value')+";";
					}
					else
					{
						seznam+=bunka.firstChild.nodeValue+";";
					}
				}
			}
			seznam+=":";
		}	
	}

	paramList.value=seznam;
}


function rechnung_berechnen(xml)
{
	var errorArray = xml.getElementsByTagName('error');
	var errordescriptionArray = xml.getElementsByTagName('errordescription');
	//var vlozenoArray = xml.getElementsByTagName('vlozenoradku');
	var auslieferArray = xml.getElementsByTagName('auslieferdatum');
	var ausliefer2timeArray = xml.getElementsByTagName('auslieferdatum2time');
	var errordescription='';

	//alert("auslieferdatum="+auslieferArray.item(0).firstChild.nodeValue+"\nauslieferdatum2time="+ausliefer2timeArray.item(0).firstChild.nodeValue);

	// pri vytvareni faktury se vyskytla nejaka chyba
	if(errorArray.length>0)
	{
		if(errorArray.item(0).hasChildNodes())
		{
			errordescription=errordescriptionArray.item(0).firstChild.nodeValue;
			alert(errordescription);
		}
	}
	else
	{
		// faktura vytvorena bez chyb, muzu zobrazit tlacitka pro tisk faktur
		//var vlozenoradku = vlozenoArray.item(0).firstChild.nodeValue;
		//alert('faktura vytvorena, pocet radku :'+vlozenoradku);
		// a schovam tlacitko pro spusteni vypoctu faktury
		var tlacitko = document.getElementById('dorechnung');
		tlacitko.style.visibility="hidden";
                // a povolit deleni faktury
                $('#bt_rechnung_teilen').removeAttr('disabled');
	}
}



function erfassenRefresh(xml)
{

	var rowArray = xml.getElementsByTagName('row');
	var error = xml.getElementsByTagName('error').item(0).firstChild.nodeValue;
	var str="";

	if(error=="ERROR")
	{
		var errorDescription = xml.getElementsByTagName('error_description').item(0).firstChild.nodeValue;
		alert(errorDescription);
	}
	else
	{
		var auftragsnr = xml.getElementsByTagName('auftragsnr').item(0).firstChild.nodeValue;
		location.href='../dauftr.php?auftragsnr='+auftragsnr;
	}
}

//----------------------------------------------------------------------

var suggestrechnung = {
	success:	function(o){
		var domDocument = o.responseXML;
		
		var rechnungenArray = domDocument.getElementsByTagName('rechnung');
		var foot = document.getElementById('suggest');
		var scroll = document.getElementById('scroll');
		var kw = document.getElementById('auftragsnr');
		var div ="";

		// vytvorim tabulku s vysledkama
		var div="<table width='100%'>";
		// pokud mam nejake vysledky, zobrazim hlavicku tabulky
		if(rechnungenArray.length>0)
		{
			div+="<tr class='result_table_header'><td>Rechnungsnummer</td><td>Datum</td><td>Vom</td><td>An</td><td>Originalauftrag</td></tr>";
		}
		for(i=0;i<rechnungenArray.length;i++)
		{
			rechnung = rechnungenArray.item(i);
			var auftragsnr = rechnung.getElementsByTagName('auftragsnr').item(0).firstChild.data;
			var datum = rechnung.getElementsByTagName('datum').item(0).firstChild.data;
			var vom = rechnung.getElementsByTagName('vom').item(0).firstChild.data;
			var an = rechnung.getElementsByTagName('an').item(0).firstChild.data;
			var origauftrag = rechnung.getElementsByTagName('origauftrag').item(0).firstChild.data;
			
			div+="<tr id='tr"+auftragsnr+"' onclick='handleOnClickSuggest(this);' onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);'>";
			div+="<td id='auftragsnr"+i+"'>"+auftragsnr+"</td>";
			div+="<td id='datum"+i+"'>"+datum+"</td>";
			div+="<td id='vom"+i+"'>"+vom+"</td>";
			div+="<td id='an"+i+"'>"+an+"</td>";
			div+="<td id='origauftrag"+i+"'>"+origauftrag+"</td>";
			div+="<tr>";
		}
		div+="</table>";
		foot.innerHTML=div;
		
		if(rechnungenArray.length>0)
		{
			scroll.style.visibility = "visible";
			scroll.style.height="250px";
			scroll.scrollTop=0;
			//autocompleteKeyword();
		}
		else
		{
			scroll.style.visibility = "hidden";
			scroll.style.height="0";
		}
		
	}
}
//----------------------------------------------------------------------
//----------------------------------------------------------------------

var columnupdate = {
	success:	function(o){
		var domDocument = o.responseXML;
		
		var columnArray = domDocument.getElementsByTagName('column');
		var valueArray = domDocument.getElementsByTagName('value');
		var idArray = domDocument.getElementsByTagName('id');
		var rowsArray = domDocument.getElementsByTagName('affectedrows');
		var errorArray = domDocument.getElementsByTagName('error');
		var sqlArray = domDocument.getElementsByTagName('sql');
		
		var column = columnArray.item(0).firstChild.data;
		var id = idArray.item(0).firstChild.data;
		
		var inputFieldId = column + "_" + id;
		
		var inputField = document.getElementById(inputFieldId);
		
		var rows = rowsArray.item(0).firstChild.data;
		
		if(rows>0){
			inputField.style.fontWeight="bold";
			inputField.style.backgroundColor="yellow";
		}
		else{
			if(errorArray.item(0).hasChildNodes()){
				inputField.style.fontWeight="";
				inputField.style.backgroundColor="red";
				var error = errorArray.item(0).firstChild.data;
				alert(error);
			}
			else{
				inputField.style.fontWeight="normal";
				inputField.style.backgroundColor="white";
			}
		}
	}
}
//----------------------------------------------------------------------

function delRechnungUmrechPosition(element){
	if(confirm("Position wirklich loeschen ?")){
		var sUrl = './columnupdate.php?column='+element.id+'&value='+element.value; 
		YAHOO.util.Connect.asyncRequest('GET',sUrl, columnupdate);
		// dvakrat parent, 1. je td, 2. je tr, tj. zneviditelnim radek
		radek = element.parentNode.parentNode;
		radek.style.textDecoration='line-through';
		radek.style.backgroundColor='red';
		// projedu vsechny inputy a nastavim jim disabled na true
		var inputsArray = radek.getElementsByTagName('input');
		for(i = 0;i<inputsArray.length;i++){
			var input = inputsArray.item(i);
			input.disabled=true;
		}
	}
}
