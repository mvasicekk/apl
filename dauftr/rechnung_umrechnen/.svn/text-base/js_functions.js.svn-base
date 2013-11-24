// JavaScript Document

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


function handleOnMouseOver(oTr)
{
	oTr.className="highlightrow";
}

function handleOnMouseOut(oTr)
{
	oTr.className="";
}



function zjistiId(element,pole){
for(i=0;i<pole.length; i++){
if(pole[i]== element){return i;}
}
}


function checkCR() {
	
	var oEvent = EventUtil.getEvent();
	
	// ktere policko mi poslalo event
	var element =  oEvent.target.id;
	var policko = oEvent.target;
	
	// pokud to byl teil budu testovat dat
	if((oEvent.keyCode==13)&&(element=='teil'))
	{
		//alert('je to z teil'+element);
		// zjistim kolik znaku uz mam v textovem poli
		hodnota = policko.value;
		if(hodnota.length>1)
		{
			//alert('hodnota v policku='+hodnota);
			// mam v tabulce pod polem jeden radek ?
			// zkusim ziskat tabulku
			var tabulka = document.getElementById('vysledky');
			if(tabulka)
			{
				//alert('mam tabulku:'+tabulka);
				var radek=tabulka.rows[1];
				if(radek)
				{
					//alert('mam radek:'+radek);
					oEvent.preventDefault();
					handleOnClickSuggest(radek);
				}
			}
		}
	}
}
  
//EventUtil.addEventHandler(document, "keypress", checkCR);
//EventUtil.addEventHandler(window,"load",init);


/**
 * handleonclick
 * @param {type} element 
 */
 function handleonclick(element) {
 	radek = element;
 	// vypisu si obsah vsech policek
 	bunky = radek.getElementsByTagName('td');
 	
 	//alert('bunky='+bunky);
 	for(i=0;i<bunky.length;i++)
 	{
 		bunka = bunky.item(i).firstChild.nodeValue;
 		if(i==0) document.getElementById('rechnung').value=trim(bunka);
 		if(i==1) document.getElementById('rechnungsdatum').value=trim(bunka);
 		if(i==2) document.getElementById('lieferdatum').value=trim(bunka);
 	}
 	
 	document.getElementById('vom').disabled=false;
 	document.getElementById('vom').focus();
 	document.getElementById('vom').select();
 	
 }
 
 var rechumrech = {
	success:	function(o){
		var domDocument = o.responseXML;
		
		var rechnungArray = domDocument.getElementsByTagName('rechnung');
		var rechnungsdatumArray = domDocument.getElementsByTagName('rechnungsdatum');
		var lieferdatumArray = domDocument.getElementsByTagName('lieferdatum');
		var vomArray = domDocument.getElementsByTagName('vom');
		var anArray = domDocument.getElementsByTagName('an');
		var menaArray = domDocument.getElementsByTagName('mena');
		var minpreisoriginalArray = domDocument.getElementsByTagName('minpreisoriginal');
		var minpreisneuArray = domDocument.getElementsByTagName('minpreisneu');
		var existsArray = domDocument.getElementsByTagName('oldexists');
		var letzterechnungArray = domDocument.getElementsByTagName('letzterechnung');

		if(rechnungArray.length>0)
		{
			rechnungValue = rechnungArray.item(0).firstChild.nodeValue;
			rechnungsdatum = rechnungsdatumArray.item(0).firstChild.nodeValue;
			lieferdatum = lieferdatumArray.item(0).firstChild.nodeValue;
			vom = vomArray.item(0).firstChild.nodeValue;
			an = anArray.item(0).firstChild.nodeValue;
		}
		
		
		// test jestli uz existuje stara faktura se stejnym cislem
		if(existsArray.length>0)
		{
			// existuje
			// dotaz, zda mam fakturu prepsat
			odpoved = confirm("Rechnung "+rechnungValue+" wurde schon umgerechnet\n Soll ich die alte loeschen ?");
			if(odpoved)
			{
				// smazat starou
				//alert("ajax, smazat starou");
				YAHOO.util.Connect.asyncRequest('GET','./rechumrech.php?rechnung='+rechnungValue+'&rechdatum='+rechnungsdatum+'&liefdatum='+lieferdatum+'&vom='+vom+'&an='+an+'&delold=1', rechumrech);
				return;				
			}
			else
			{
				// nedelam nic , koncim
				alert("Alte Rechnung wird nicht geloescht ! Ende.")
				return;
			}
		}
		else
		{
			// zadna stara nebyla
			// vytvoril jsem novou
			if(menaArray.length>0)
			{
				mena = menaArray.item(0).firstChild.nodeValue;
			}
			else
				mena='NODEF';
			
			if(minpreisoriginalArray.length>0)
			{
				minpreisoriginal = minpreisoriginalArray.item(0).firstChild.nodeValue;
			}
			else
				minpreisoriginal=0;
	
			if(minpreisneuArray.length>0)
			{
				minpreisneu = minpreisneuArray.item(0).firstChild.nodeValue;
			}
			else
				minpreisneu=0;
			/*
			alert('rechnung='+rechnungValue+
			'\nrechnungsdatum='+rechnungsdatum+
			'\nlieferdatum='+lieferdatum+
			'\nvom='+vom+
			'\nan='+an+
			'\nmena='+mena+
			'\nminpreisoriginal='+minpreisoriginal+
			'\nminpreisneu='+minpreisneu
			);*/
			letzterechnung = letzterechnungArray.item(0).firstChild.nodeValue*1;
			letzterechnung++;
			alert("Von der Rechnung "+rechnungValue+" wurde neue Rechnung "+letzterechnung+" erstellt.");
			
			//TODO:
			// zmenit pismeno u radku s vybranym pismenem
			
			//window.location.reload();
		}
	}
}

var refreshdatum = { 
	success: function(o)
	{
		//alert(text);
		text = o.responseText;
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
			var neuButton = document.getElementById('berechnenbutton').disabled=true;
		}
		else
		{
			control.style.backgroundColor='';
			control.value=hodnota;
			var neuButton = document.getElementById('berechnenbutton').disabled=false;
		}
	}
}

var refreshvom = { 
	success: function(o)
	{
		doc = o.responseXML;
		vomArray = doc.getElementsByTagName('vom');
		vom = vomArray.item(0).firstChild.nodeValue;		
		control = document.getElementById('vom');
		
		if(vom=="ERROR")
		{
			control.style.backgroundColor='red';
			control.value=vom;
			var neuButton = document.getElementById('berechnenbutton').disabled=true;
		}
		else
		{
			control.style.backgroundColor='';
			control.value=vom;
			var neuButton = document.getElementById('berechnenbutton').disabled=false;
		}
	}
}

var refreshan = { 
	success: function(o)
	{
		doc = o.responseXML;
		anArray = doc.getElementsByTagName('an');
		an = anArray.item(0).firstChild.nodeValue;		
		control = document.getElementById('an');
		
		if(an=="ERROR")
		{
			control.style.backgroundColor='red';
			control.value=an;
			var neuButton = document.getElementById('berechnenbutton').disabled=true;
		}
		else
		{
			control.style.backgroundColor='';
			control.value=an;
			var neuButton = document.getElementById('berechnenbutton').disabled=false;
		}
	}
}
