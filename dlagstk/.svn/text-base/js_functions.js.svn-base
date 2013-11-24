// JavaScript Document
function stahniData(cislo) {
var hlavicka = document.getElementsByTagName("head")[0];
var dataLoader = document.getElementById("scriptLoader");
if(dataLoader) hlavicka.removeChild(dataLoader);
script = document.createElement("script");
script.id = "scriptLoader";
script.src = "./operace.php?cislo="+cislo;
var x = document.getElementsByTagName("head")[0];
x.appendChild(script);
return false;
}
function lagerBestand(){
teil = document.getElementById('Teil').value;
window.location="./lagerbestand.php?teil=" +teil;
}

function vypis(D,l0S, Ra, T, P, Rb, K, Q, F, E,l8V, X, Rc,l9V, A2, A4, A6, B2, B4, B6, C4, XX, XY, datum){
void(document.getElementById('0DStk').value = D );
//void(document.getElementById('0SStk').value = l0S);
void(document.getElementById('1RStk').value = Ra);
void(document.getElementById('2TStk').value = T );
void(document.getElementById('3PStk').value = P );
void(document.getElementById('4RStk').value = Rb);
void(document.getElementById('5KStk').value = K );
void(document.getElementById('5QStk').value = Q );
void(document.getElementById('6FStk').value = F );
void(document.getElementById('8EStk').value = E );
void(document.getElementById('8VStk').value = l8V);
void(document.getElementById('8XStk').value = X );
void(document.getElementById('9RStk').value = Rc);
void(document.getElementById('9VStk').value = l9V);
void(document.getElementById('A2Stk').value = A2);
void(document.getElementById('A4Stk').value = A4);
void(document.getElementById('A6Stk').value = A6);
void(document.getElementById('B2Stk').value = B2);
void(document.getElementById('B4Stk').value = B4);
void(document.getElementById('B6Stk').value = B6);
void(document.getElementById('C4Stk').value = C4);
void(document.getElementById('XXStk').value = XX);
void(document.getElementById('XYStk').value = XY);
void(document.getElementById('datum_inventury').value = datum);
}

function zjistiId(element,pole){
  for(i=0;i<=pole.length; i++){
    if(pole[i]== element){return i;}
  }
}

function checkCR(event) {
//    var pole = new Array("Teil","datum","0DStk","0SStk","1RStk","2TStk","3PStk","4RStk","5KStk","6FStk","8EStk","8VStk","8XStk","9RStk","9VStk","A2Stk","A4Stk","A6Stk","B2Stk","B4Stk","B6Stk","C4Stk","XXStk","XYStk","save");
    var pole = new Array("Teil","datum","0DStk","1RStk","2TStk","3PStk","4RStk","5KStk","5QStk","6FStk","8EStk","8VStk","8XStk","9RStk","9VStk","A2Stk","A4Stk","A6Stk","B2Stk","B4Stk","B6Stk","C4Stk","XXStk","XYStk","save");
    var element =  window.event.srcElement.id;
    
    var event  = (window.event) ? window.event : ((window.event) ? window.event : null);
    var node = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);

    if (event.keyCode == 13) {
    var i = zjistiId(element, pole) +1;
        document.getElementById(pole[i]).focus();
        return false;  
      }
    else{
      return true;
    }  
}
  

    document.onkeypress = checkCR;
