// JavaScript Document

//EventUtil.addEventHandler(window,"resize",rebuildpage);
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
		var neuButton = document.getElementById('neu').disabled=true;
	}
	else
	{
		control.style.backgroundColor='';
		control.value=hodnota;
		var neuButton = document.getElementById('neu').disabled=false;
	}
}

function zjistiId(element)
{
	for(i=0;i<pole.length; i++)
	{
		if(pole[i]== element)
		{
			return i;
		}
	}
}


function checkCR() 
{
	var oEvent = EventUtil.getEvent();
    var element =  oEvent.target.id;
    if (oEvent.keyCode == 13)
    {
		var i = zjistiId(element) +1;
		if(i<pole.length)
			document.getElementById(pole[i]).focus();
		//oEvent.preventDefault();
	}
    else
    {
    	if (oEvent.keyCode == 27) 
    	{
			//alert('ESC stisknuto');
			window.location.reload();
			//oEvent.preventDefault();
	  	}
    	else
    	{
      			return true;
    	}
    }
}
  

// funkce v jvsc

// nepouzivat on blur ale jen update

function init_dambew()
{
	var persnr = document.getElementById('persnr');
	persnr.focus();
	persnr.select();
	
	var datum = document.getElementById('datum');
	datum_value = new Date();
	
	mesic=datum_value.getMonth()+1;
	datum_value = datum_value.getDate()+'.'+mesic+'.'+datum_value.getFullYear();
	datum.value = datum_value;

	document.getElementById('ausstk').value=0;
	document.getElementById('rueckstk').value=0;
	document.getElementById('grund').value=0;
	document.getElementById('amnr').value=0;
	document.getElementById('invnr').value=0;
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
	var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	
	
	
	if(mysqlerrorarray.item(0).hasChildNodes())
		alert(mysqlerrorarray.item(0).firstChild.data);
	
	window.location.reload();
		
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
	return encodeURIComponent(selectedvalue);
}



function pocetPrvkuVPoli(pole,prvek)
{
	var pocetVyskytu = 0;
	for(i=0;i<pole.length;i++)
		if(pole[i]==prvek) pocetVyskytu++;
	return pocetVyskytu;
}

function odeberPrvekZPole(pole,prvek)
{
	// najdu si index odebiraneho prvku
	indexHledaneho = -1;
	for(i=0;i<pole.length;i++)
	{
	 if(pole[i]==prvek) indexHledaneho=i;
	}
	// odeberu prvek z pole
	if(indexHledaneho>=0)
		pole.splice(indexHledaneho,1);
}


function pridejPrvekDoPole(pole,prvek,zaprvek)
{
	var novePole = new Array(pole.length);
	for(i=0;i<pole.length;i++)
	{
		novePole.push(pole[i]);
		if(pole[i]==zaprvek)
		novePole.push(prvek);
	}
	// vycistim puvodni pole
	while(pole.length)
		pole.pop();
	//alert('delka stareho pole='+pole.length);
	//obsah noveho pole nakopirovat zpet do puvodniho pole
	//alert('seznamPoli='+seznamPoli);
	for(i=0;i<novePole.length;i++)
	{
		pole.push(novePole[i]);
	}
}

function validate_persnr(xml)
{
	//alert('validate_persnr');
	var field=xml.getElementsByTagName('persnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_persname=document.getElementById('persname');
        var regeloe = xml.getElementsByTagName('regeloe').item(0).firstChild.data;
	
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
		e_persname.value=xml.getElementsByTagName('name').item(0).firstChild.data;
		var neuButton = document.getElementById('neu').disabled=false;
                $('#oeselect').val(regeloe);
	}
}


function validate_amnr(xml)
{
	var errorArray = xml.getElementsByTagName('error')
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var amnrpopis=document.getElementById('amnrpopis');
	
	if(errorArray.length>0)
	{
		e.style.backgroundColor='red';
		amnrpopis.value='polozka nenalezena !!';
		document.getElementById('neu').disabled=true;
	}
	else
	{
		var popis='';
		var name1Node = xml.getElementsByTagName('name1').item(0);
		if(name1Node.hasChildNodes())
			popis+=name1Node.firstChild.data;
		var name2Node = xml.getElementsByTagName('name2').item(0);
		if(name2Node.hasChildNodes())
			popis+=' '+name2Node.firstChild.data;
		amnrpopis.value=popis;
		document.getElementById('neu').disabled=false;
	}
}

function validate_invnr(xml)
{
	var errorArray = xml.getElementsByTagName('error')
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var amnrpopis=document.getElementById('invnrpopis');
	
	if(errorArray.length>0)
	{
		e.style.backgroundColor='red';
		amnrpopis.value='polozka nenalezena !!';
		document.getElementById('neu').disabled=true;
	}
	else
	{
		var popis='';
		var name1Node = xml.getElementsByTagName('beschreibung').item(0);
		if(name1Node.hasChildNodes())
			popis+=name1Node.firstChild.data;
		amnrpopis.value=popis;
		document.getElementById('neu').disabled=false;
	}
}

function enableneu()
{
	var neuButton = document.getElementById('neu').disabled=false;
}



function disableneu()
{
  	var neuE = document.getElementById('neu');
  	neuE.disabled=true;
}

function markfocus(element)
{
	var e = document.getElementById(element.id);
	var eAktual = document.getElementById('elementaktual');
	var divAktual = document.getElementById('eaktualinfo');
	eAktual.value = e.id;
	i = zjistiId(e.id);
	divAktual.innerHTML = e.id+': <i>'+poleNapoveda[i]+'</i>';
	
	// oznaceni aktualniho pole zlutym pozadim
	e.style.backgroundColor='#ffffbb';
}

function makeButtonBusy(element)
{
	element.style.backgroundColor='#ffaaaa';
	element.disabled=true;
}

