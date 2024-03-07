/****************************************************************************   
DHTML library from DHTMLCentral.com
*   Copyright (C) 2001 Thomas Brattli 2001
*   This script was released at DHTMLCentral.com
*   Visit for more great scripts!
*   This may be used and changed freely as long as this msg is intact!
*   We will also appreciate any links you could give us.
*
*   Made by Thomas Brattli 2001
***************************************************************************/


/******************************************************************************************************///
/*   FUNCION QUE EJECUTA UNA ACCION DEPENDEIENDO DE LA RESPUESTA DEL CUADRO DE CONFIRMACION **EDWIN****///
/******************************************************************************************************///
/*function confirmado(id){																				
	if(id == 0){} // ESCITORIO												
	if(id == 1){document.form1.submit();}																
	if(id == 2){}//ESCRITORIO											
	if(id == 3){termina_autoriza();}
	if(id == 4){termina_guardar();}
	if(id == 5){termina_pasar();}
	if(id == 6){termina_eliminar();}
}*/																										//			
																										//
/******************************************************************************************************///
function tamano_ven(cual){
	var Tam = TamVentana();
	if(cual == 0){return Tam[0];}
	if(cual == 1){return Tam[1];}
}



// FUNCION DE LLAMADO AJAX
function creaAjax(){
         var objetoAjax=false;
         try {
          //Para navegadores distintos a internet explorer
          objetoAjax = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
          try {
                   //Para explorer
                   objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
                   }
                   catch (E) {
                   objetoAjax = false;
          }
         }

         if (!objetoAjax && typeof XMLHttpRequest!='undefined') {
          objetoAjax = new XMLHttpRequest();
         }
         return objetoAjax;
}

function FAjax (url,capa,valores,metodo,loading)
{
          var ajax=creaAjax();
          var capaContenedora = document.getElementById(capa);
		  if(valores == ''){
		  	valores = 'myancho='+tamano_ven(0)+'&myalto='+tamano_ven(1);
		  }else{
		  	valores += '&myancho='+tamano_ven(0)+'&myalto='+tamano_ven(1);
		  }
		  //valores = escape(valores);


    //Creamos y ejecutamos la instancia si el metodo elegido es POST
    if(metodo.toUpperCase()=='POST'){
             ajax.open ('POST', url, true);
             ajax.onreadystatechange = function() {
             if (ajax.readyState==1) {
			 	    if(loading == 'true'){
			 		    muestra_loading();
				    }else if(loading == 'false'){
					
				    }else if(loading == 'email'){	//ENVIO DE EMAILS
					    capaContenedora.innerHTML="<div style=\"float:left\"><img src=\"images/mail.gif\" ></div><div style=\"float:left\">Enviando Email.......</div>";
				    }else if(loading == 'loading3'){ //PRUEBA DE CONEXION SMTP - PANEL DE CONTROL
					    capaContenedora.innerHTML="<img src=\"images/loading3.gif\" >";
				    }else{
					    capaContenedora.innerHTML="<img src=\"images/loading.gif\" >";
				    }
             }
             else if (ajax.readyState==4){
                       if(ajax.status==200)
                       {
                            document.getElementById(capa).innerHTML=ajax.responseText;
						    if(loading == 'true'){
							    oculta_loading();
						    }
                       }
                       else if(ajax.status==404)
                                                 {

                                capaContenedora.innerHTML = "La direccion no existe";
                                                 }
                               else
                                                 {
                                capaContenedora.innerHTML = "Error: ".ajax.status;
                                                 }
                                        }
                      }
             ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		     ajax.setRequestHeader('charset','iso-8859-1');
		     ajax.send(valores);
             return;
    }

    //Creamos y ejecutamos la instancia si el metodo elegido es GET
    if (metodo.toUpperCase()=='GET'){

             ajax.open ('GET', url, true);
             ajax.onreadystatechange = function() {
             if (ajax.readyState==1) {
                                          capaContenedora.innerHTML="Cargando.......";
             }
             else if (ajax.readyState==4){
                       if(ajax.status==200){
                                                 document.getElementById(capa).innerHTML=ajax.responseText;
                       }
                       else if(ajax.status==404)
                                                 {

                                capaContenedora.innerHTML = "La direccion no existe";
                                                 }
                                                 else
                                                 {
                                capaContenedora.innerHTML = "Error: ".ajax.status;
                                                 }
                                        }
                      }
             ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		     ajax.setRequestHeader('charset','iso-8859-1');      
		     ajax.send(null);
             return
    }
} 

//FRUNCIONES QUE MUESTRAN Y OCULTAN EL LOADING
/*function muestra_loading(){
	document.getElementById('LOADING').style.top= '0px';
	document.getElementById('LOADING').style.left= '0px';
	
}
function oculta_loading(){
	document.getElementById('LOADING').style.top= '-2000px';
	document.getElementById('LOADING').style.left= '-2000px';
	
}*/
//------------------------------------------------

//FUNCION QUE MANEJA EL CONTEXMENU DE LAS PANTALLAS HIJAS Y DESACTIVA EL CONTEXMENU Y EL MENU DE INICIO
function disableRightClick(e){
		  if(!document.rightClickDisabled){
				if(document.layers){
					document.captureEvents(Event.MOUSEDOWN);
					document.onmousedown = disableRightClick;
				}else{
					document.oncontextmenu = disableRightClick; 
				}
				return document.rightClickDisabled = true;
		  }
 	var posx = 0;
	var posy = 0;
	if(!e){ var e = window.event } //PARA DESACTIVAR ESTA FUNCION COMENTARIAR ESTA LINEA
	posx = (e.pageX) ? e.pageX : window.event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
	posy = (e.pageY) ? e.pageY : window.event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
	var posicion = {y: posy, x:posx}
	if(document.layers || (document.getElementById && !document.all)){
		if (e.which==2||e.which==3){
			parent.click_derecho(posicion.x,posicion.y);
			return false;
		}
	}else{
		parent.click_derecho(posicion.x,posicion.y);
		return false;
	}
}
//---------------------------------------------------------------------------------------------------------------------

// Funcion que verifica el tamaño del area de trabajo de la ventana ***************** EDWIN
function TamVentana() {
var Tamanyo = [0, 0];
 	if (typeof window.innerWidth != 'undefined'){Tamanyo = [window.innerWidth,window.innerHeight];
	}else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth !='undefined' && document.documentElement.clientWidth != 0){Tamanyo = [document.documentElement.clientWidth,document.documentElement.clientHeight];
	}else{Tamanyo = [document.getElementsByTagName('body')[0].clientWidth,document.getElementsByTagName('body')[0].clientHeight];
	}return Tamanyo; 
}
// fin funcion ******* EDWIN

//FUNCION QUE CHEKEA EL EMAIL
/*function isEmailAddress(theElement)
{
	var s = document.getElementById(theElement).value;
	var filter=/^[A-Za-z][A-Za-z0-9_.-]*@[A-Za-z0-9_-]+\.[A-Za-z0-9_.-]+[A-za-z]$/;
	if (s.length == 0 ) return true;
	if (filter.test(s)){
		return true;
	}else{
		//Ext.MessageBox.alert('Agregar e-mail personal','e-mail incorrecto, por favor verifique!'); 
		//document.getElementById(theElement).focus();
		return false;
	}
}*/
// -------------------------------------------------------------------------------------------------------


//Browsercheck (needed) ***************
function lib_bwcheck(){ 

    this.ver=navigator.appVersion
    this.agent=navigator.userAgent
    //alert(this.agent);
    this.dom=document.getElementById?1:0
    this.opera5=(navigator.userAgent.indexOf("Opera")>-1 && document.getElementById)?1:0
    this.ie5=(this.ver.indexOf("MSIE 5")>-1 && this.dom && !this.opera5)?1:0; 
    this.ie6=(this.ver.indexOf("MSIE 6")>-1 && this.dom && !this.opera5)?1:0;
    this.ie7=(this.ver.indexOf("MSIE 7")>-1 && this.dom && !this.opera5)?1:0;
	this.ie8=(this.ver.indexOf("MSIE 8")>-1 && this.dom && !this.opera5)?1:0;
   // this.ie4=(document.all && !this.dom && !this.opera5)?1:0;
    this.ie=this.ie4||this.ie5||this.ie6||this.ie7||this.ie8
    this.mac=this.agent.indexOf("Mac")>-1
    this.ff = this.agent.indexOf("Firefox")>-1
    this.moz = this.agent.indexOf("Mozilla")>-1
    this.ns6=(this.dom && parseInt(this.ver) >= 5) ?1:0; 
    this.ns4=(document.layers && !this.dom)?1:0;
    this.bw=(this.ie6 || this.ie5 || this.ie4 || this.ns4 || this.ie7 || this.ie8 || this.moz || this.ff || this.opera5)
    return this

}
bw=new lib_bwcheck() //Browsercheck object

//Debug function ******************
function lib_message(txt){alert(txt); return false}

//Lib objects  ********************
function lib_obj(obj,nest){ 
  if(!bw.bw) return lib_message('Old browser')
  nest=(!nest) ? "":'document.'+nest+'.'
  this.evnt=bw.dom? document.getElementById(obj):
    bw.ie4?document.all[obj]:bw.ns4?eval(nest+"document.layers." +obj):0;	
  if(!this.evnt) return lib_message('The layer does not exist ('+obj+')' 
    +'- \nIf your using Netscape please check the nesting of your tags!')
  this.css=bw.dom||bw.ie4?this.evnt.style:this.evnt; 
  this.ref=bw.dom||bw.ie4?document:this.css.document;
  this.x=parseInt(this.css.left)||this.css.pixelLeft||this.evnt.offsetLeft||0;
  this.y=parseInt(this.css.top)||this.css.pixelTop||this.evnt.offsetTop||0
  this.w=this.evnt.offsetWidth||this.css.clip.width||
    this.ref.width||this.css.pixelWidth||0; 
  this.h=this.evnt.offsetHeight||this.css.clip.height||
    this.ref.height||this.css.pixelHeight||0
  this.c=0 //Clip values
  if((bw.dom || bw.ie4) && this.css.clip) {
  this.c=this.css.clip; this.c=this.c.slice(5,this.c.length-1); 
  this.c=this.c.split(' ');
  for(var i=0;i<4;i++){this.c[i]=parseInt(this.c[i])}
  }
  this.ct=this.css.clip.top||this.c[0]||0; 
  this.cr=this.css.clip.right||this.c[1]||this.w||0
  this.cb=this.css.clip.bottom||this.c[2]||this.h||0; 
  this.cl=this.css.clip.left||this.c[3]||0
  this.obj = obj + "Object"; eval(this.obj + "=this")
  return this
}

//Moving object to **************
lib_obj.prototype.moveIt = function(x,y){
  this.x=x;this.y=y; this.css.left=x;this.css.top=y
}

//Moving object by ***************
lib_obj.prototype.moveBy = function(x,y){
  this.css.left=this.x+=x; this.css.top=this.y+=y
}

//Showing object ************
lib_obj.prototype.showIt = function(){this.css.visibility="visible"}

//Hiding object **********
lib_obj.prototype.hideIt = function(){this.css.visibility="hidden"}

//Changing backgroundcolor ***************
lib_obj.prototype.bg = function(color){ 
	if(bw.opera) this.css.background=color
	else if(bw.dom || bw.ie4) this.css.backgroundColor=color
	else if(bw.ns4) this.css.bgColor=color  
}

//Writing content to object ***
lib_obj.prototype.writeIt = function(text,startHTML,endHTML){
	if(bw.ns4){
    if(!startHTML){startHTML=""; endHTML=""}
	  this.ref.open("text/html"); 
    this.ref.write(startHTML+text+endHTML); 
    this.ref.close()
	}else this.evnt.innerHTML=text
}

//Clipping object to ******
lib_obj.prototype.clipTo = function(t,r,b,l,setwidth){ 
  this.ct=t; this.cr=r; this.cb=b; this.cl=l
  if(bw.ns4){
    this.css.clip.top=t;this.css.clip.right=r
    this.css.clip.bottom=b;this.css.clip.left=l
  }else{
    if(t<0)t=0;if(r<0)r=0;if(b<0)b=0;if(b<0)b=0
    this.css.clip="rect("+t+","+r+","+b+","+l+")";
    if(setwidth){this.css.pixelWidth=this.css.width=r; 
    this.css.pixelHeight=this.css.height=b}
  }
}

//Clipping object by ******
lib_obj.prototype.clipBy = function(t,r,b,l,setwidth){ 
  this.clipTo(this.ct+t,this.cr+r,this.cb+b,this.cl+l,setwidth)
}

//Clip animation ************
lib_obj.prototype.clipIt = function(t,r,b,l,step,fn,wh){
  tstep=Math.max(Math.max(Math.abs((t-this.ct)/step),Math.abs((r-this.cr)/step)),
    Math.max(Math.abs((b-this.cb)/step),Math.abs((l-this.cl)/step)))
  if(!this.clipactive){
    this.clipactive=true; if(!wh) wh=0; if(!fn) fn=0
    this.clip(t,r,b,l,(t-this.ct)/tstep,(r-this.cr)/tstep,
      (b-this.cb)/tstep,(l-this.cl)/tstep,tstep,0, fn,wh)
  }
}
lib_obj.prototype.clip = function(t,r,b,l,ts,rs,bs,ls,tstep,astep,fn,wh){
  if(astep<tstep){
    if(wh) eval(wh); 
    astep++
    this.clipBy(ts,rs,bs,ls,1);
    setTimeout(this.obj+".clip("+t+","+r+","+b+","+l+","+ts+","+rs+","
      +bs+","+ls+","+tstep+","+astep+",'"+fn+"','"+wh+"')",50)
  }else{
    this.clipactive=false; this.clipTo(t,r,b,l,1);
    if(fn) eval(fn)
  }
}

//Slide animation ***********
lib_obj.prototype.slideIt = function(endx,endy,inc,speed,fn,wh){
  if(!this.slideactive){
    var distx = endx - this.x;
    var disty = endy - this.y
    var num = Math.sqrt(Math.pow(distx,2)+Math.pow(disty,2))/inc
    var dx = distx/num; var dy = disty/num
    this.slideactive = 1; 
    if(!wh) wh=0; if(!fn) fn=0
    this.slide(dx,dy,endx,endy,speed,fn,wh)
    }
}
lib_obj.prototype.slide = function(dx,dy,endx,endy,speed,fn,wh) {
  if(this.slideactive&&
  (Math.floor(Math.abs(dx))<Math.floor(Math.abs(endx-this.x))|| 
    Math.floor(Math.abs(dy))<Math.floor(Math.abs(endy-this.y)))){
    this.moveBy(dx,dy); 
    if(wh) eval(wh)
    setTimeout(this.obj+".slide("+dx+","+dy+","+endx+","+endy+","+speed+",'"
    +fn+"','"+wh+"')",speed)
  }else{
    this.slideactive = 0; 
    this.moveIt(endx,endy);
    if(fn) eval(fn)
  }
}

//Circle animation ****************
lib_obj.prototype.circleIt = function(rad,ainc,a,enda,xc,yc,speed,fn) {
  if((Math.abs(ainc)<Math.abs(enda-a))) {
    a += ainc
    var x = xc + rad*Math.cos(a*Math.PI/180)
    var y = yc - rad*Math.sin(a*Math.PI/180)
    this.moveIt(x,y)
    setTimeout(this.obj+".circleIt("+rad+","+ainc+","+a+","+enda+","
      +xc+","+yc+","+speed+",'"+fn+"')",speed)
  }else if(fn&&fn!="undefined") eval(fn)
}

//Document size object ********
function lib_doc_size(){ 
  this.x=0;this.x2=bw.ie && document.body.offsetWidth-20||innerWidth||0;
  this.y=0;this.y2=bw.ie && document.body.offsetHeight-5||innerHeight||0;
  if(!this.x2||!this.y2) return message('Document has no width or height') 
  this.x50=this.x2/2;this.y50=this.y2/2;
  return this;
}

//Drag drop functions start *******************
dd_is_active=0; dd_obj=0; dd_mobj=0
function lib_dd(){
  dd_is_active=1
  if(bw.ns4){
    document.captureEvents(Event.MOUSEMOVE|Event.MOUSEDOWN|Event.MOUSEUP)
  }
  document.onmousemove=lib_dd_move;
  document.onmousedown=lib_dd_down
  document.onmouseup=lib_dd_up
}
lib_obj.prototype.dragdrop = function(obj){
  if(!dd_is_active) lib_dd()
  this.evnt.onmouseover=new Function("lib_dd_over("+this.obj+")")
  this.evnt.onmouseout=new Function("dd_mobj=0")
  if(obj) this.ddobj=obj
}
lib_obj.prototype.nodragdrop = function(){
  this.evnt.onmouseover=""; this.evnt.onmouseout=""
  dd_obj=0; dd_mobj=0
}
//Drag drop event functions
function lib_dd_over(obj){dd_mobj=obj}
function lib_dd_up(e){dd_obj=0}
function lib_dd_down(e){ //Mousedown
  if(dd_mobj){
    x=(bw.ns4 || bw.ns6)?e.pageX:event.x||event.clientX
    y=(bw.ns4 || bw.ns6)?e.pageY:event.y||event.clientY
    dd_obj=dd_mobj
    dd_obj.clX=x-dd_obj.x; 
    dd_obj.clY=y-dd_obj.y
  }
}
function lib_dd_move(e,y,rresize){ //Mousemove
  x=(bw.ns4 || bw.ns6)?e.pageX:event.x||event.clientX
  y=(bw.ns4 || bw.ns6)?e.pageY:event.y||event.clientY
  if(dd_obj){
    nx=x-dd_obj.clX; ny=y-dd_obj.clY
    if(dd_obj.ddobj) dd_obj.ddobj.moveIt(nx,ny)
    else dd_obj.moveIt(nx,ny)
  }
  if(!bw.ns4) return false      
}
//Drag drop functions end *************

//***********************************************************************************************************************
//dhtmlapi.js************************************************************************************************************
//myElement= new DomElement(string id,[string styleId]);


//LIB TOOLTIP **********************************************************************************************************
//CONTROLA LOS CUADROS DE DIALOGO EMERGENTES
//onmouseover="showTooltip('Plan economico','Incluye la entrada a todas las coferencias pero no incluye ni el transpore ni la alimentacion')"
//onmouseover="showTooltip('Simpe tooltip with additional info','This is simple tooltip with additional help info.','Press F1 for more help.','help.png','')"
//onmouseover="showTooltip('Complex tooltip','This is complex tooltip with image.','Press F1 for more help.','help.png','graf.png')" onmouseout="hideTooltip()"
	/*var lastTooltip =null;
    var tooltipBackColor='#EEEEF5';*/
    
    /*showTooltip = function (strTitle,strText,strHelpText,strhelpImage,strmainImage) {   
    if (lastTooltip==null){                                                               
           var newDiv = document.createElement("div");
           newDiv.style.background=tooltipBackColor;
           newDiv.style.color='#4C4C4C';           
           newDiv.style.position='absolute';
           newDiv.style.width='300px';
           newDiv.style.border='#767676 1px solid';         
           newDiv.style.visibility='hidden';
            
           var title = document.createElement("span"); 
           title.style.padding='6px 0 4px 10px';
           newDiv.style.font='bold 12px "Trebuchet MS" , "Arial"';
		   title.style.styleFloat='left';
		   title.style.cssFloat='left';
		   title.style.clear='both';
		   title.style.width='100%';           
           var titleText = document.createTextNode(strTitle);
           title.appendChild(titleText);    

           var mainParagraf = document.createElement("div");
           mainParagraf.style.font='normal 12px "Trebuchet MS" , "Arial"';
           mainParagraf.style.padding='0 2px 6px 20px';
           mainParagraf.style.margin='0';
           mainParagraf.style.styleFloat='left';
		   mainParagraf.style.cssFloat='left';           
            
           if (strmainImage) {
               var mainImg = document.createElement("img");    
               mainImg.setAttribute("src",strmainImage);
               mainImg.style.font='bold 12px "Trebuchet MS" , "Arial"';
               mainImg.style.marginRight='10px';
               mainImg.style.border='#BDBDBD 1px solid';
               mainImg.style.styleFloat='left';
		       mainImg.style.cssFloat='left';               
               mainParagraf.appendChild(mainImg);  
           }
           
           var mainText = document.createTextNode(strText);            
           mainParagraf.appendChild(mainText);             
           newDiv.appendChild(title);
           newDiv.appendChild(mainParagraf);
           
           if (strHelpText) {
               var horLine = document.createElement("hr"); 
               horLine.style.width='96%';
		       horLine.style.clear='both';    
                        
               var helpDiv = document.createElement("div");   
               helpDiv.style.styleFloat='left';
		       helpDiv.style.cssFloat='left';   
		       helpDiv.style.clear='both';
		       helpDiv.style.paddingLeft='6px';     
		       helpDiv.style.height='24px';     
               
               if (strhelpImage) {
                   var helpImg = document.createElement("img");    
                   helpImg.setAttribute("src",strhelpImage);
                   helpImg.style.marginRight='8px';
                   helpImg.style.verticalAlign='middle';
                   helpDiv.appendChild(helpImg);
               }

               var helpText = document.createTextNode(strHelpText);                           
               helpDiv.appendChild(helpText);  
               newDiv.appendChild(horLine);
               newDiv.appendChild(helpDiv);                         
           }                    
                                                                        
            lastTooltip=newDiv;    
            if (document.addEventListener) document.addEventListener("mousemove",moveTooltip, true);
            if (document.attachEvent) document.attachEvent("onmousemove",moveTooltip);  
            
            var bodyRef = document.getElementsByTagName("body").item(0);
            bodyRef.appendChild(newDiv);                    
            }                                       
        };*/
        
        
       /*moveTooltip = function (e) {
       if (lastTooltip){
               if (document.all)
                    e = event;
               if (e.target)
                    sourceEl = e.target;
               else if (e.srcElement)
                    sourceEl = e.srcElement;
                      
               var coors=findPos(sourceEl);
               var positionLeft = e.clientX;            
               var positionTop  = coors[1] + sourceEl.clientHeight + 20;
               
               lastTooltip.style.top=positionTop+'px';
               lastTooltip.style.left=positionLeft+'px';
               lastTooltip.style.visibility='visible';
           }
       }*/
    
       /*hideTooltip = function () {              
            var bodyRef = document.getElementsByTagName("body").item(0);
            if (lastTooltip) bodyRef.removeChild(lastTooltip);
            lastTooltip=null;
       };*/
       
       /*function findPos(obj) {
	        var curleft = curtop = 0;
	        if (obj.offsetParent) {
		        curleft = obj.offsetLeft
		        curtop = obj.offsetTop
		        while (obj = obj.offsetParent) {
			        curleft += obj.offsetLeft
			        curtop += obj.offsetTop
		        }
	        }
	        return [curleft,curtop];
        }*/