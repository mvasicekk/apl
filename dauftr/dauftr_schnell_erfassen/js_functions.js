// JavaScript Document
// dauftr_schnell_erfassen

EventUtil.addEventHandler(document, "keypress", checkCR);



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
    
    	// u policka teil jeste pre tim nez se presunu na dalsim vlozim hodnotu z tabulky
    	// pokud tam teda neco je
    	// zjistim si tabulku
    	if(element=='teil')
    	{
    		element.disabled=true;
    		var tabulka = document.getElementById('teilsuggesttable');
    		//alert('tabulka='+tabulka);
    		var pocetRadku = tabulka.rows.length;
    		//alert('tabulka pocet radku='+pocetRadku);
    		if(pocetRadku>=3)
    		{
    			// vyberu prvni radek s dilem
    			var radek = tabulka.rows[1];
    			//alert('id vybraneho radku'+radek.id);
    			// vyberu prvni policko na radku
    			var bunka = radek.cells[0];
    			var cisloDilu = bunka.innerHTML;
    			//alert('obsah vybrane bunky'+bunka.innerHTML);
    			document.getElementById('teil').value=cisloDilu;
    		}
    		
    	}
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
	var teil = document.getElementById('teil');
	teil.focus();
	teil.value='';
	
	var increment = document.getElementById('increment');
	increment.value=10;
}


function updateRadekGClass(element,g){

    var classname = element.className;

    if(g.toUpperCase()=='G'){
        if(classname=='selected'){
            element.className='Gselected';
        }
        else if(classname=='noselected'){
            element.className='Gnoselected';
        }
        else if(classname=='Gselected'){
            element.className='Gselected';
        }
        else{
            element.className='Gnoselected';
        }
    }
    else{
        if(classname=='selected'){
            element.className='selected';
        }
        else if(classname=='noselected'){
            element.className='noselected';
        }
        else if(classname=='Gselected'){
            element.className='selected';
        }
        else{
            element.className='noselected';
        }
    }

    testMoreG(element);
}

function js_validate_G(control)
{

	var hodnota = control.value
    var radek = control.parentNode.parentNode;

    //alert('radek.id='+radek.id);
    radekClass = radek.className;
	//alert('radekClass='+radekClass);

	if(hodnota=='G'||hodnota=='g')
	{
		control.value=hodnota.toUpperCase();
		control.setAttribute('value',hodnota.toUpperCase());
		control.style.backgroundColor='';
        //zmenim tridu radku na Gselected nebo Gnoselected
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
    updateRadekGClass(radek,hodnota);
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
	var div="<table id='teilsuggesttable'>";
	// pokud mam nejake vysledky, zobrazim hlavicku tabulky
	if(teilArray.length>0)
	{
		div+="<tr class='result_table_header'><td>teil</td><td>Bezeichnung</td><td>Gewicht</td></tr>";
	}
	for(i=0;i<teilArray.length;i++)
	{
		dil=teilArray.item(i).firstChild.data;

		/*onclick='handleOnClickSuggest(this);'*/
		div+="<tr id='tr"+dil+"'  onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);' onclick='handleClickTeil(this);'>";
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
		//autocompleteKeyword();
	}
	else
	{
		scroll.style.visibility = "hidden";
		scroll.style.height="0";
	}
	if(teilArray.length==1)
	{
		autocompleteKeyword();
	}
	
	kw.disabled=false;
	kw.focus();
	//kw.setSelectionRange(0,kw.value.length);
	//kw.focus();

}

function handleClickTeil(element){
    dil = element.id.substr(2);
    document.getElementById('teil').value = dil;
    document.getElementById('teil').style.backgroundColor = '';
    document.getElementById('teil').focus();
    document.getElementById('teil').blur();
}

function autocompleteKeyword()
{
	var oKeyword = document.getElementById("teil");
	position = 0;
	deselectAll();
	
	start=oKeyword.value.length;
	document.getElementById("tr0").className="highlightrow";	
	
	updateKeywordValue(document.getElementById("tr0"));
	//selectRange(oKeyword,start,oKeyword.value.length);
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
	
	updateKeywordValue(oTr);
	var oKeyword = document.getElementById("teil");
	
	//document.location.href='dkopf.php?teil='+oKeyword.value;
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
		e.value=typ_chyby;
		e.disabled=false;
	}
	else
	{
		e.style.backgroundColor='';
		// pokud mam polozky fremdauftr, fremdpos a fremdausauftrag, tak je zapisu do
		// odpovidajicich textboxu
		if(xml.getElementsByTagName('fremdausauftrag').item(0).hasChildNodes())
			var fremdausauftrag = xml.getElementsByTagName('fremdausauftrag').item(0).firstChild.nodeValue;
		else
			fremdausauftrag = '';
			
		if(xml.getElementsByTagName('fremdauftrag').item(0).hasChildNodes())
			var fremdauftrag = xml.getElementsByTagName('fremdauftrag').item(0).firstChild.nodeValue;
		else
			fremdauftrag = '';
		
		if(xml.getElementsByTagName('fremdpos').item(0).hasChildNodes())
			var fremdpos = xml.getElementsByTagName('fremdpos').item(0).firstChild.nodeValue;
		else
			fremdpos = '';

        if(xml.getElementsByTagName('bezeichnung').item(0).hasChildNodes())
			var bezeichnung = xml.getElementsByTagName('bezeichnung').item(0).firstChild.nodeValue;
		else
			bezeichnung = '';

        if(xml.getElementsByTagName('rest').item(0).hasChildNodes())
			var rest = xml.getElementsByTagName('rest').item(0).firstChild.nodeValue;
		else
			rest = '';

        if(xml.getElementsByTagName('gew').item(0).hasChildNodes())
			var gew = xml.getElementsByTagName('gew').item(0).firstChild.nodeValue;
		else
			gew = '';

        if(xml.getElementsByTagName('brgew').item(0).hasChildNodes())
			var brgew = xml.getElementsByTagName('brgew').item(0).firstChild.nodeValue;
		else
			brgew = '';

        if(xml.getElementsByTagName('status').item(0).hasChildNodes())
			var status = xml.getElementsByTagName('status').item(0).firstChild.nodeValue;
		else
			status = '';


		// zajistim si pristup na textboxy
		var e_fremdausauftrag = document.getElementById('fremdausauftrag');
		e_fremdausauftrag.value=fremdausauftrag;
		
		var e_fremdauftrag = document.getElementById('fremdauftr');
		e_fremdauftrag.value=fremdauftrag;
		
		var e_fremdpos = document.getElementById('fremdpos');
		e_fremdpos.value=fremdpos;

        var e_bezeichnung = document.getElementById('bezeichnung');
        e_bezeichnung.value = bezeichnung;
        e_bezeichnung.style.visibility = 'visible';

        var e_rest = document.getElementById('rest');
        e_rest.value = rest;
        e_rest.style.visibility = 'visible';

        var e_gew = document.getElementById('netgewicht');
        e_gew.value = gew;
        e_gew.style.visibility = 'visible';

        var e_brgew = document.getElementById('brgewicht');
        e_brgew.value = brgew;
        e_brgew.style.visibility = 'visible';

        var e_status = document.getElementById('status');
        if(status=="ALT"){
            e_status.value = status + " ALTES Teil, nicht benutzen !!!";

            e_status.style.visibility = 'visible';
            document.getElementById('pos_erstellen').disabled=true;
            // zakazu vsechny inputy krome dilu
            var inputy = document.getElementsByTagName('input');
            for(i=0;i<inputy.length;i++){
                var input = inputy[i];
                input.disabled=true;
            }
        }
	else if(status=="GSP"){
            e_status.value = status + " gesperrtes Teil, nicht benutzen !!!";

            e_status.style.visibility = 'visible';
            document.getElementById('pos_erstellen').disabled=false;
            inputy = document.getElementsByTagName('input');
            for(i=0;i<inputy.length;i++){
                input = inputy[i];
                input.disabled=false;
            }
        }
        else{
            e_status.style.visibility = 'hidden';
            document.getElementById('pos_erstellen').disabled=false;
            inputy = document.getElementsByTagName('input');
            for(i=0;i<inputy.length;i++){
                input = inputy[i];
                input.disabled=false;
            }
        }

		// vytvorit tabulku s pozicema z pracovniho planu
		tabulka="<table border='0' cellspacing='2' id='positionenTable'>";
		var div = document.getElementById('suggest');
		var abgnrarray = xml.getElementsByTagName('abgnr');
		var tatarray = xml.getElementsByTagName('tat');
		var vzkdarray = xml.getElementsByTagName('vzkd');
		var preisarray = xml.getElementsByTagName('preis');
		var vzabyarray = xml.getElementsByTagName('vzaby');
		var kzgutarray = xml.getElementsByTagName('kzgut');
        var lvonarray = xml.getElementsByTagName('lager_von');
        var lnacharray = xml.getElementsByTagName('lager_nach');
        var bedarfarray = xml.getElementsByTagName('bedarf_typ');
		var kzdruckarray = xml.getElementsByTagName('kzdruck');
		var runden = xml.getElementsByTagName('runden').item(0).firstChild.data;
		

		tabulka+="<tr style='background-color:blue;color:white;font-weight:bold;'>";
		tabulka+='<td>';
		tabulka+='tat';
		tabulka+='</td>';
		tabulka+='<td>';
		tabulka+='abgnr';
		tabulka+='</td>';

   		tabulka+='<td>';
		tabulka+='Gtat';
		tabulka+='</td>';

		tabulka+='<td>';
		tabulka+='preis';
		tabulka+='</td>';
		tabulka+='<td>';
		tabulka+='vzkd';
		tabulka+='</td>';
		tabulka+='<td>';
		tabulka+='vzaby';
		tabulka+='</td>';

   		tabulka+='<td>';
		tabulka+='Lag.VON';
		tabulka+='</td>';

   		tabulka+='<td>';
		tabulka+='Lag.NACH';
		tabulka+='</td>';

   		tabulka+='<td>';
		tabulka+='Bedarf';
		tabulka+='</td>';

    tabulka+='<tr>';
		
		
		
		var kundeValue = document.getElementById('kunde').value;		
		var auftragsnrValue = document.getElementById('auftragsnr').value;

		//alert('kundeValue='+kundeValue+' auftragsnrValue='+auftragsnrValue);		

        var pocetVybranychGCinnosti = 0;

		for(i=0;i<abgnrarray.length;i++)
		{
			trid='tr'+i;
			preisid='preis_value'+i;
			
			var preis=parseFloat(preisarray.item(i).firstChild.data);
			preis_hodnota=preis.toFixed(runden);

			var vzkd=parseFloat(vzkdarray.item(i).firstChild.data);
			vzkd_hodnota=vzkd.toFixed(4);

			var vzaby=parseFloat(vzabyarray.item(i).firstChild.data);
			vzaby_hodnota=vzaby.toFixed(4);
			
			var kzdruck=parseInt(kzdruckarray.item(i).firstChild.data);
			kzdruck_hodnota=kzdruck;
			
			if(kzgutarray.item(i).hasChildNodes())
					kzgut_hodnota=kzgutarray.item(i).firstChild.data;
				else
					kzgut_hodnota='';
				
			
			if(kzdruck_hodnota!=0)
			{
				if(kzgut_hodnota=='G'){
					tabulka+="<tr id='tr"+i+"' class='Gselected'>";
                    pocetVybranychGCinnosti++;
                }
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

                // G tat
				tabulka+="<td align='right'>";
				tabulka+="<input onblur='js_validate_G(this);' class='edit_dpos' maxlength='1' size='1' name='kzgut' id='kzgut"+i+"' value='"+kzgut_hodnota+"'/>"
				tabulka+="</td>";

				tabulka+="<td align='right' id='"+preisid+"'>";
				tabulka+=preis_hodnota;
				tabulka+="</td>";

				//alert('kundeValue='+kundeValue+' auftragsnrValue='+auftragsnrValue);
				tabulka+="<td align='right'>";
				tabulka+="<input onblur="+'"';
				tabulka+="js_validate_float(this);";
				tabulka+="getDataReturnXml('./validateVzKd.php?vzkd='+this.value+'&kunde="+kundeValue
				tabulka+="&auftragsnr="+auftragsnrValue
				tabulka+="&preisid="+preisid
				tabulka+="', validateVzkd);"+'"'+" class='edit_dpos' maxlength='9' size='6' name='vzkd' id='vzkd"+i+"' value='"+vzkd_hodnota+"'/>"
				tabulka+="</td>";


				tabulka+="<td align='right'>";
				tabulka+="<input onblur='js_validate_float(this);' class='edit_dpos' maxlength='9' size='6' name='vzaby' id='vzaby"+i+"' value='"+vzaby_hodnota+"'/>"
				tabulka+="</td>";

				tabulka+="<td align='left'>";
				tabulka+=lvonarray.item(i).firstChild.data;
				tabulka+="</td>";

				tabulka+="<td align='left'>";
				tabulka+=lnacharray.item(i).firstChild.data;
				tabulka+="</td>";

				tabulka+="<td align='left'>";
				tabulka+=bedarfarray.item(i).firstChild.data;
				tabulka+="</td>";

			tabulka+="</tr>";
		}
		tabulka+="</table>";
		div.innerHTML=tabulka;

        // pokud mam pocet vybranych cinnosti >1, tak nepovolim vytvoreni pozic
        if(pocetVybranychGCinnosti>1){
            //zakazu tlacitko s positionerstellen
            document.getElementById('pos_erstellen').disabled=true;
        }

		//alert(field);
	}

	/*
	var field=xml.getElementsByTagName('bis').item(0).firstChild.data;
	
	var e_von=document.getElementById('von');
	
	//alert(field);
	
	*/
	
	e.disabled=false;
}

function testMoreG(element){
       var tabulka = document.getElementById('positionenTable')
       //alert("id tabulky = "+tabulka.id);
       // vytahnu si pole vsech radku
       radkyArray = tabulka.getElementsByTagName("tr");
       //alert('pocetradku = '+radkyArray.length);
       //projdu radky
       var pocetGvybranych = 0;
       for(i=0;i<radkyArray.length;i++){
           var radek = radkyArray.item(i);
           trida = radek.className;
           if(trida=="Gselected") pocetGvybranych++;
           //alert('id radku = '+radek.id+'\ntrida='+trida);
       }
       if(pocetGvybranych>1){
        //alert('mehrere Gtat auf der Palette !!\nvice G cinnosti na jedne palete !!!');
        document.getElementById('pos_erstellen').disabled=true;
       }
       else
           document.getElementById('pos_erstellen').disabled=false;
}

function toggle_kzdruck(id)
{

//	alert(id);
	radek=document.getElementById(id);
	
	if(radek.className=='selected')
	{
		radek.className='noselected';
        testMoreG(radek);
		return;
	}
	
	if(radek.className=='noselected')
	{
		radek.className='selected';
        testMoreG(radek);
		return;
	}
	

	if(radek.className=='Gnoselected')
	{
		radek.className='Gselected';
        testMoreG(radek);
		return;
	}

	if(radek.className=='Gselected')
	{
		radek.className='Gnoselected';
        testMoreG(radek);
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
        var str_netgewicht=document.getElementById('netgewicht').value;
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
        seznam+="&netgewicht="+encodeControlValue('netgewicht');
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

function validateVzkd(xml)
{
	
	var preisNode = xml.getElementsByTagName('preis').item(0);
	var preisidNode = xml.getElementsByTagName('preisid').item(0);
	var preisValue = 0;
	var preisid = preisidNode.firstChild.nodeValue;
	var preisElement = document.getElementById(preisid);
	
	//alert('preisElement='+preisElement);
	
	
	
	if(preisNode.hasChildNodes())
	{
		preisValue = parseFloat(preisNode.firstChild.nodeValue);
		preisValue = preisValue.toFixed(4);
		preisTextNode = document.createTextNode(preisValue);
		preisElement.removeChild(preisElement.firstChild);
		preisElement.appendChild(preisTextNode);
	}
	
	//alert('preis='+preisValue);
}

function erfassenRefresh(xml)
{

	//alert('erfassenrefresh');
	
	var rowArray = xml.getElementsByTagName('row');
	var errorArray = xml.getElementsByTagName('error');
	var str="";

	
	if(errorArray.length>0)
	{
		var errorDescription = xml.getElementsByTagName('errordescription').item(0).firstChild.nodeValue;
		alert(errorDescription);
	}
	else
	{
		var auftragsnr = xml.getElementsByTagName('auftragsnr').item(0).firstChild.nodeValue;
		location.href='../dauftr.php?auftragsnr='+auftragsnr;
	}

/*
	for(i=0;i<rowArray.length;i++)
	{
		for(j=0;j<rowArray.item(i).childNodes.length-1;j++)
		{
			str+="\n";
			var rowNodeName = rowArray.item(i).childNodes[j].nodeName;
			str+=rowNodeName+"=";
			if(rowArray.item(i).childNodes[j].hasChildNodes())
				var rowNodeValue = rowArray.item(i).childNodes[j].firstChild.nodeValue;
			else
				var rowNodeValue = '';
			str+=rowNodeValue;
		}
	}


	// jeste projedu pozice cinnosti

	positionArray = xml.getElementsByTagName('position');
	for(i=0;i<positionArray.length-1;i++)
	{
		str+="\n";
		for(j=0;j<positionArray.item(i).childNodes.length;j++)
		{
			//str+="\n";
			var nodeName = positionArray.item(i).childNodes[j].nodeName;
			str+=nodeName+"=";
			if(positionArray.item(i).childNodes[j].hasChildNodes())
				var nodeValue = positionArray.item(i).childNodes[j].firstChild.nodeValue;
			else
				var nodeValue = '';
			str+=nodeValue;

		}
	}

	alert(str);
*/
}

function validate_palnr(xml)
{
	var errorArray = xml.getElementsByTagName('error');
	var e = document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	
	//alert('validate_palnr');
	
	if(errorArray.length>0)
	{
		// mam nejakou chybu
		//alert('nejaka chyba');
		var errorDescription = xml.getElementsByTagName('errordescription').item(0).firstChild.data;
		alert(errorDescription);
		//e.value='ERROR';
		//document.getElementById('pos_erstellen').disabled=true;
		e.style.backgroundColor='red';
		//e.focus();
		//e.select();
	}
	else
	{
		// vse ok
		e.style.backgroundColor='';
		document.getElementById('pos_erstellen').disabled=false;
	}
}