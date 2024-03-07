<?php
header("Content-type: text/css");

session_start();
function GeneraCssImagenes($path){

	$css = '';
	$dir = dir($path);

	while($file = $dir->read()){
		$img = explode('.',$file);
		if($file != '' && $file != '.' && $file != '..'){
		   if($img[1] == "png" || $img[1] == "PNG"){
                $css .= '.'.$img[0].'{background-image: url('.$path.'/'.$file.') !important;}';
			}
		}
	}
	$dir->close();
	return $css;
}

echo GeneraCssImagenes('images/iconos');
echo GeneraCssImagenes('images/BotonesTabs');
echo GeneraCssImagenes('images/MaterialDesign');
//echo GeneraCssImagenes('images/formularios');

echo'
	/*************************************************************************************************************************/
	/**                  			              		  G E N E R A L 													**/
	/*************************************************************************************************************************/
	@font-face {
       font-family: "padaloma";
       src = url("font/Padaloma.ttf");
 	}


	body{
        color               : #333;
        width               : 100%;
        height              : 100%;
        font-family         : Verdana,sans-serif,Tahoma;
        font-size           : 11px;
    }

    #preloading0{
        background          : rgba(143,201,255,1);
        background          : -moz-radial-gradient(center, ellipse cover, rgba(143,201,255,1) 0%, rgba(32,124,229,1) 100%);
        background          : -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, rgba(143,201,255,1)), color-stop(100%, rgba(32,124,229,1)));
        background          : -webkit-radial-gradient(center, ellipse cover, rgba(143,201,255,1) 0%, rgba(32,124,229,1) 100%);
        background          : -o-radial-gradient(center, ellipse cover, rgba(143,201,255,1) 0%, rgba(32,124,229,1) 100%);
        background          : -ms-radial-gradient(center, ellipse cover, rgba(143,201,255,1) 0%, rgba(32,124,229,1) 100%);
        background          : radial-gradient(ellipse at center, rgba(143,201,255,1) 0%, rgba(32,124,229,1) 100%);
        font-family         : Verdana,sans-serif,Tahoma;
    }

    .LogoLoginPreloading{
        width               : 400px;
	    height              : 144px;
	    position            : absolute;
	    left                : 50%;
	    top                 : 50%;
	    margin              : -240px 0 0 -200px;
        font-size           : 14px;
        text-align          : center;
        color               : #FFF;
        font-weight         : bold;
        text-shadow         : 1px 1px 1px #333;
        font-family         : Verdana,sans-serif,Tahoma;
        background          : url("../../login/images/LogicalERP.png?v1") ;
        background-repeat   : no-repeat;
        background-size     : 100% auto ;
    }

	#logo 		{position:absolute; left:712px; top:20px;	width:289px; height:291px; z-index:1;}
	#foto 		{position:absolute; left:2px; top:-94px; width:41px; height:50px;	z-index:1000002;}
	a:link 		{color: #000000;	text-decoration: none;}
	a:visited 	{text-decoration: none; color: #000000;}
	a:hover 	{text-decoration: none; color: #006699;}
	a:active 	{text-decoration: none; color: #000000;}

    .defaultFont{
        font-family:"Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande";
        font-size:11px;
    }
	.Redondeado{
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
	}
	.Sombra{
		-webkit-box-shadow: 1px 1px 3px #666;
		-moz-box-shadow: 1px 1px 3px #666;
		box-shadow: 1px 1px 3px #666;
	}
	.RedondeadoSombra{
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		border-radius: 3px;
		-webkit-box-shadow: 1px 1px 3px #666;
		-moz-box-shadow: 1px 1px 3px #666;
		box-shadow: 1px 1px 3px #666;
	}
	.LetraAjustada{
	    white-space             : nowrap;
        overflow                : hidden;
        text-overflow           : ellipsis;
	}
	.DivNorth{
		white-space             : nowrap;
        overflow                : hidden;
        text-overflow           : ellipsis;
		font-family				: Verdana, Arial, sans-serif, "Lucida Grande";
        font-size				: 14px;
		text-shadow         	: 1px 1px 1px #333;
		margin					: 8px 5px 0 5px;
		width					: 40%;
		color					: #FFF;
		font-weight				: bold;
	}



	/*************************************************************************************************************************/
	/**                  			   I M A G E N E S     D E      B O T O N E S  											**/
	/*************************************************************************************************************************/

	.AjaxLoader			{background-image: url(images/ajax-loader.gif) !important;}

	/*************************************************************************************************************************/
	/**                  			              	W I Z A R D					  											**/
	/*************************************************************************************************************************/

	#WizCapaPrincipal{
		width			:	100%;
		float			:	left;
	}

	#WizCapaIzquierda{
		float				:	left;
		width				:	150px;
		height				:	250px;
		background-image	:	url(images/wizard/wizard.png);
		background-repeat	:	no-repeat;
	}

	#WizCapaDerecha{
		float			:	left;
		width			:	400px;
		padding			: 	20px 15px 0 15px;
	}

	.WizTitulo{
		font-size		:	16px;
		color			: 	#033999;
	}

	.WizContenido{
		margin			:   20px 0 0 0;
	}

	/*************************************************************************************************************************/
	/**                  			   		P A N E L   D E    C O N T R O L 	  											**/
	/*************************************************************************************************************************/

	.ContenedorGrupoPanelControl{
		width                     : 100%;
		float                     : left;
		margin                    : 0;
	 }

	.TituloPanelControl{
		width                     : 95%;
		float                     : left;
		font-size                 : 12px; padding:2px 2px 3px 8px;
		color                     : #003366;
		margin                    : 5px 0 5px 0;
		font-weight               : bold;

		-moz-border-radius        : 3px;
		-webkit-border-radius     : 3px;
		-webkit-box-shadow        : 1px 1px 3px #666;
		-moz-box-shadow           : 1px 1px 2px #666;
		box-shadow           	  : 1px 1px 2px #666;
		border-radius       	  : 3px;


		background                : -webkit-linear-gradient(#DFE8F6, #CDDBF0);
		background                : -moz-linear-gradient(#DFE8F6, #CDDBF0);
		background                : -o-linear-gradient(#DFE8F6, #CDDBF0);
		background                : linear-gradient(#DFE8F6, #CDDBF0);
	}

	.IconoPanelControl{
		float                     : left;
		width                     : 60px;
		height                    : 80px;
		margin                    : 5px 10px 5px 10px;
		color                     : #333;
		text-align                : center;
		font-family               : Tahoma;
		font-size                 : 10px;
		/*font-weight             : bold;	*/
	}

	.IconoPanelControlimg{
		margin                    : 0 0 0 8px;
		width                     : 44px;
		height                    : 44px;
		cursor                    : pointer;
	}

	.IconoPanelControltxt{
		width                   :60px;
		height                  :36px;
		cursor                  :pointer;
		text-align              :center;
	}

	/*************************************************************************************************************************/
	/**                  			   		 F I E L D S     Y      C O M B O S  											**/
	/*************************************************************************************************************************/


	.myfield{
		border                    : 0px solid #999;
		font-size                 : 12px;
		min-height                : 20px;
		margin                    : 0 0 0 0;
		padding 				  : 0 0 0 5px;
		-moz-border-radius        : 2px;
		-webkit-border-radius     : 2px;
		border-radius     		  : 2px;
		-webkit-box-shadow        : 0px 0px 3px #666;
		-moz-box-shadow           : 1px 1px 3px #999;
		box-shadow                : 1px 1px 3px #999;
	}

	.myfieldObligatorio{
		border-left         	  : 3px solid #F00;
		border-right         	  : 0px solid #F00;
		border-top         	      : 0px solid #F00;
		border-bottom        	  : 0px solid #F00;
		font-size                 : 12px;
		min-height                : 20px;
		margin                    : 0 0 0 0;
		padding 				  : 0 0 0 2px;
		-moz-border-radius        : 2px;
		-webkit-border-radius     : 2px;
		border-radius     		  : 2px;
		-webkit-box-shadow        : 0px 0px 3px #666;
		-moz-box-shadow           : 1px 1px 3px #999;
		box-shadow                : 1px 1px 3px #999;
		background-repeat		  : no-repeat;
		/*background-position	  : left top;
		background 				  : #FFE9E9;*/
		background-color		  : #FFE9E9;

	}

	option { min-height: 20px; }

	.myfieldBusqueda{
		border                    : 0px solid #999;
		font-size                 : 12px;
		min-height                : 20px;
		margin                    : 0 0 0 0;
		background-image          : url(images/BotonesTabs/buscar16.png);
		background-repeat         : no-repeat;
		padding                   : 0 0 0 27px;
		-moz-border-radius        : 3px;
		-webkit-border-radius     : 3px;
		-webkit-box-shadow        : 0px 0px 3px #666;
		-moz-box-shadow           : 1px 1px 3px #999;
		box-shadow           	  : 1px 1px 3px #999;
		border-radius       	  : 3px;
	}

	.myfield:focus, .myfieldObligatorio:focus, .myfieldBusqueda:focus{
		-webkit-box-shadow        : 0px 0px 5px #333;
		-moz-box-shadow           : 0px 0px 5px #333;
		box-shadow           	  : 0px 0px 5px #333;
	}

	/*================== HACK SELECT ==================*/
	/***************************************************/
	select{
		font-size         : 11px;
		width             : 100%;
		height            : 20px;
		max-height        : 25px;
		line-height       : 18px;
		margin            : 0;
		padding       	  : 0 0 0 5px;
		background        : #FFF none no-repeat;
		border            : 1px solid #d4d4d4;
		cursor            : pointer;
		/*webkit-box-shadow : none !important;
		box-shadow        : none !important;*/
	}

	select:hover{ background-color : #eef5f6; }

	@media screen and (min-width:0) {
		/* for relatively modern browsers including IE 8+ */
		select {
			background-image    : url("data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAaCAYAAACkVDyJAAAAlElEQVRIx+2VwQ3AIAhFwTiFi7KC7uYGHo1z0Et7MWkjaEiaQOJBDzy/wgdqrXwHWKyAiGAZDnSgHBhC+IfCUgqbKXxgGqhY4QyRQkUK35KLoK01iZd+xZKXRuEfbvdQPPGkAABEhMeLhohE5yfaAufk93791r13zQDmnLNqcOMYg1NKmoJgTRHtmDeaWZvPQweaAi8PXSmZJU3QRAAAAABJRU5ErkJggg==");
			background-position : -50px -50px;
		}
	}

	@media screen and (-webkit-min-device-pixel-ratio:0) {
		/* for Webkit */
		select {
			-webkit-appearance  : none;
			background-position : right center;
			/*padding             : 3px 32px 3px 5px;*/
			padding             : 0 0 0 5px;
		}
	}

	@-moz-document url-prefix() {
		/* for Firefox */
		select {
			-moz-appearance     : none;
			text-indent         : 0.01px;
			text-overflow       : "";
			background-position : right center;
			padding-right       : 16px;
		}

	  	/* hides the dotted outline on focus in FF (See SO#3773430) */
		select:-moz-focusring {
			color       : transparent;
			text-shadow : 0 0 0 #000;
		}
	}

	@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
		/* for IE10+ */
		select::-ms-expand { display : none; }
		select {
			background-position : right center;
			padding-right       : 30px;
		}
	}


	/*************************************************************************************************************************/
	/**                  			    V E N T A N A S    Y     E S C R I T O R I O										**/
	/*************************************************************************************************************************/
	body{
		font-family                   : "Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande";
		font-size                     : 12px;
	}

    .FUENTE11NEGRILLA {
	    font-family         : Verdana,sans-serif,Tahoma;
	    font-size           : 11px;
	    font-weight         : bold;
        color               : #333;

    }

    .FUENTE11NEGRILLABL {
	    font-family         : Verdana,sans-serif,Tahoma;
	    font-size           : 11px;
	    font-weight         : bold;
	    color               : #FFFFFF;
    }

    .FUENTE11 {
	    font-family         : Verdana,sans-serif,Tahoma;
	    font-size           : 11px;
	    font-weight         : normal;
        color               : #333;
        text-shadow         : 1px 1px 1px #FFF;
    }

    .FUENTE11BL {
	    font-family         : Verdana,sans-serif,Tahoma;
	    font-size           : 11px;
	    font-weight         : normal;
        color               : #FFF;
        text-shadow         : 1px 1px 1px #333;
    }

	.BOTON_BARRA_TAREAS{
        color                           : #FFF;
        text-shadow                     : 1px 1px 1px #333;
	}

	.BOTON_BARRA_TAREAS:hover{
        color                           : #FFF;
        text-shadow                     : 1px 1px 1px #333;
	   	border-right					: 1px solid;
	   	border-left						: 1px solid;
        /*border                          : 0 1px 0 1px solid;*/
        background                      : rgba(255,255,255,.2);
        background                      : -moz-radial-gradient(center, ellipse cover, rgba(255,255,255,.01) 0%, rgba(255,255,255,.4) 100%);
        background                      : -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, rgba(255,255,255,.01)), color-stop(100%, rgba(255,255,255,.4)));
        background                      : -webkit-radial-gradient(center, ellipse cover, rgba(255,255,255,.01) 0%, rgba(255,255,255,.4) 100%);
        background                      : -o-radial-gradient(center, ellipse cover, rgba(255,255,255,.01) 0%, rgba(255,255,255,.4) 100%);
        background                      : -ms-radial-gradient(center, ellipse cover, rgba(255,255,255,.01) 0%, rgba(255,255,255,.4) 100%);
        background                      : radial-gradient(ellipse at center, rgba(255,255,255,.01) 0%, rgba(255,255,255,.4) 100%);

	}

	.ElIcono{
		/*border				: 1px solid;*/
		position			: absolute;
		left				: -1000px;
		top					: -1000px;
		width				: 120px;
		height				: 120px;
		z-index				: 1;
	}
		.ElIconoContenedor{
			float				: left;
			width				: calc(100% - 15px);
			height				: calc(100% - 20px);
			padding				: 10px 0 0 5px;
			margin				: 10px;
			cursor				: pointer;
			border-radius       : 5px;
		}
		.ElIconoContenedor:hover {
			box-shadow			: 0px 0px 2px rgba(0,0,0,0.5);
			background			: rgba(255,255,255,0.1);
		}
			.ElIconoImagen{
				float				: left;
				width				: 44px;
				margin				: 0 0 0 28px;
			}
			.ElIconoLabel{
				float				: left;
				width				: 100px;
				height 				: 56px;
				margin				: 0px;
				text-shadow         : 1px 1px 1px #333;
				text-align			: center;
				font-size			: 12px;
				color				: #FFF;
			}


	.ICONO_VENTANA{
		margin-top                    : 3px;
		float                         : left;
		width                         : 35px;
	}

	.iconos_escritorio{
		cursor                        : pointer;
	}

	.iconos_escritorio_disabled{
		cursor                        : default;
		opacity                       : 0.3;
		filter                        : alpha(opacity=30);
		-moz-opacity                  : 0.3;
		-khtml-opacity                : 0.3;
	}

    #inicio, #barra{
        background              : rgba('.$_SESSION["COLOR_MENU"].',.95);
        -webkit-box-shadow      : 1px 1px 3px #333;
        box-shadow              : 1px 1px 3px #333;
    }

    #menu{
		background              : rgba('.$_SESSION["COLOR_MENU"].',.95);
        position                : absolute;
        left                    : 167px;
        top                     : 2123px;
        width                   : 350px;
        height                  : calc(100% - 58px);
        z-index                 : 800000;
        -webkit-box-shadow      : 1px 1px 3px #333;
        box-shadow              : 1px 1px 3px #333;
		border-radius			: 10px;
    }

	#ContenidoMenuInicio{
		float					: left;
		width					: 320px;
		height					: calc(100% - 45px);
		overflow				: auto;
		overflow-x				: hidden;
		padding					: 0 0 0 0;
		margin					: 20px 0 0 15px;
		font-size				: 12px;
		color					: #FFF;
		text-shadow				: 1px 1px 1px #333;
	}

	.MenuEsquina{
		position				: absolute;
		left					: 10px;
		bottom					: -15px;
		width					: 0;
		height					: 0;
		border-left				: 10px solid transparent;
		border-right			: 10px solid transparent;
		border-top				: 15px solid rgba('.$_SESSION["COLOR_MENU"].',.95);;
	}

    .ClassVentana{
        background              :rgba('.$_SESSION["COLOR_MENU"].',.95);
    }

	/*************************************************************************************************************************/
	/**                  						   B O T O N     D E     I N I C I O 										**/
	/*************************************************************************************************************************/


	#BTIcube {
		margin 					: -5px 0 0 15px;
		height					: 26px;
		width					: 26px;
		perspective				: 800px;
		perspective-origin		: 50% 50%;
		-webkit-perspective		: 700px;
		-webkit-perspective-origin: 50% 50%;
		transition				: -webkit-transform 3s linear;
		transform-style			: preserve-3d;
		Transform 				: rotateX(-25deg) rotateY(-45deg);
		-webkit-transition		: -webkit-transform 3s linear;
		-webkit-transform-style	: preserve-3d;
		-webkit-Transform 		: rotateX(-25deg) rotateY(-45deg);
	}
    .BTIface {
		position				: absolute;
		height					: 26px;
		width					: 26px;
		border-radius			: 1px;
		background-color		: #333;
    }

    #BTIcube .one  {
		transform				: rotateX(90deg) translateZ(13px);
      	-webkit-transform		: rotateX(90deg) translateZ(13px);
    }
    #BTIcube .two {
		transform				: translateZ(13px);
      	-webkit-transform		: translateZ(13px);
    }
    #BTIcube .three {
		transform				: rotateY(90deg) translateZ(13px);
      	-webkit-transform		: rotateY(90deg) translateZ(13px);
    }
    #BTIcube .four {
		transform				: rotateY(180deg) translateZ(30px);
      	-webkit-transform		: rotateY(180deg) translateZ(30px);
    }
	#BTIcuadro{
		width					: calc(50% - 2px);
		height					: calc(50% - 2px);
		float					: left;
		box-shadow				: 1px 1px 1px #000;
		border-radius			: 1px;
		margin					: 1px 1px 1px 1px;
	}

	#BTIcube .el1{background-color: #FFF;}
	#BTIcube .el2{background-color: #09C;}
	#BTIcube .el3{background-color: #0F3;}



	/*************************************************************************************************************************/
	/**                  						   L I S T A D O S   D E  D A T O S 										**/
	/*************************************************************************************************************************/


	/*************************************************************************************************************************/
	/**                  						   M O D U L O   L O G I S T I C O											**/
	/*************************************************************************************************************************/


    /*************************************************************************************************************************/
    /**                  			        A N I M A C I O N   L O A D I N G  LOGICALSOFT   								**/
    /*************************************************************************************************************************/

	#experiment {
		position 	: absolute;
		top 		: calc(50% - 50px);
		left 		: calc(50% - 50px);
		/*perspective	: 800px;
		perspective-origin: 50% 50%;
		-webkit-perspective: 700px;
		-webkit-perspective-origin: 50% 50%;*/
	}

	#cube {
		position: relative;
		margin: auto;
		height: 60px;
		width: 60px;
		/*****************************************************/
		transition: -webkit-transform 3s linear;
		transform-style: preserve-3d;
		Transform : rotateX(-45deg) rotateY(-45deg);
		animation: rotateplane 3.5s infinite ease-in-out;
		/*****************************************************/
		-webkit-transition: -webkit-transform 3s linear;
		-webkit-transform-style: preserve-3d;
		-webkit-Transform : rotateX(-45deg) rotateY(-45deg);
		-webkit-animation: rotateplane 3.5s infinite ease-in-out;
	}

    .face {
		position: absolute;
		height: 60px;
		width: 60px;
		border-radius: 4px;
		background-color: #333;
    }

	@-webkit-keyframes rotateplane {
		0% 	  { -webkit-transform: rotateX(-45deg)  rotateY(-45deg)  }
		12.5% { -webkit-transform: rotateX(-135deg) rotateY(-45deg)  }
		25%   { -webkit-transform: rotateX(-135deg) rotateY(-135deg) }
		37.5% { -webkit-transform: rotateX(-225deg) rotateY(-135deg) }
		50%   { -webkit-transform: rotateX(-225deg) rotateY(-225deg) }
		62.5% { -webkit-transform: rotateX(-315deg) rotateY(-225deg) }
		75%   { -webkit-transform: rotateX(-315deg) rotateY(-315deg) }
		87.5% { -webkit-transform: rotateX(-405deg) rotateY(-315deg) }
		100%  { -webkit-transform: rotateX(-405deg) rotateY(-405deg) }

	}

	@keyframes rotateplane {
		0% 	  { transform: rotateX(-45deg)  rotateY(-45deg);  }
		12.5% { transform: rotateX(-135deg) rotateY(-45deg);  }
		25%   { transform: rotateX(-135deg) rotateY(-135deg); }
		37.5% { transform: rotateX(-225deg) rotateY(-135deg); }
		50%   { transform: rotateX(-225deg) rotateY(-225deg); }
		62.5% { transform: rotateX(-315deg) rotateY(-225deg); }
		75%   { transform: rotateX(-315deg) rotateY(-315deg); }
		87.5% { transform: rotateX(-405deg) rotateY(-315deg); }
		100%  { transform: rotateX(-405deg) rotateY(-405deg); }
	}

    #cube .one  {
		transform: rotateX(90deg) translateZ(30px);
      	-webkit-transform: rotateX(90deg) translateZ(30px);
    }
    #cube .two {
		transform: translateZ(30px);
      	-webkit-transform: translateZ(30px);
    }
    #cube .three {
		transform: rotateY(90deg) translateZ(30px);
      	-webkit-transform: rotateY(90deg) translateZ(30px);
    }
    #cube .four {
		transform: rotateY(180deg) translateZ(30px);
      	-webkit-transform: rotateY(180deg) translateZ(30px);
    }
    #cube .five {
		transform: rotateY(-90deg) translateZ(30px);
      	-webkit-transform: rotateY(-90deg) translateZ(30px);
    }
    #cube .six {
		transform: rotateX(-90deg)  rotate(180deg) translateZ(30px);
     	-webkit-transform: rotateX(-90deg)  rotate(180deg) translateZ(30px);
    }
	#cube .seven {
		transform: rotateX(-90deg)  rotate(180deg) ;
     	-webkit-transform: rotateX(-90deg)  rotate(180deg) ;
    }

	#cuadro{
		width:calc(50% - 4px);
		height:calc(50% - 4px);
		float:left;
		box-shadow: 1px 1px 3px #000;
		border-radius: 4px;
		margin: 2px 2px 2px 2px;
	}

	.el1{background-color: #FFF;}
	.el2{background-color: #09C;}
	.el3{background-color: #0F3;}
	.el4{background-color: #F60;}
	.el5{background-color: #F00;}
	.el6{background-color: #FF0;}


	#LabelCargando{
		position	: relative;
		margin		: 30px 0 0 0;
		text-align	: center;
		text-shadow : 1px 1px 1px #333;
		font-size	: 20px;
		color		: #FFF;
		font-weight	: bold;
		font-family	: Arial, Helvetica, sans-serif;
	}

/*************************************************************************************************************************/
    /**                  					             D A S H B O A R D     												**/
    /*************************************************************************************************************************/
		.MainDashContent{
			margin:10px 0 0 0;
			width:100%;
			height:calc(100% - 85px);
			overflow:hidden;
			overflow-y:auto;
		}

		.DashContenedor{
			font-family		:   RobotoDraft, \'Helvetica Neue\', Helvetica, Arial;
			width:300px;
			height:200px;
			background-color:#FFF;
			margin:10px;
			padding:10px;
			float:left;
			/*border-bottom	: 	1px solid #EEE;*/
			-webkit-box-shadow: 1px 1px 15px 3px #EEE;
			-moz-box-shadow: 1px 1px 15px 3px #EEE;
			box-shadow: 1px 1px 15px 3px #EEE;
		}
				.DashPieChart{
					float:right;
					width:200px;
					height:100px;
				}
				.DashIndicador{
					float:left;
					width:100px;
					height:75px;
					text-align:center;
					font-size:50px;
					padding:25px 0 0 0;
				}
				.DashLabelTitle{
					float: left;
					width: 280px;
					margin: 20px 0 0 0;
					padding: 0 0 0 20px;
					color:#666;
					font-size:20px
				}
				.DashLabelDat{
					float			: 	left;
					width			: 	260px;
					margin			: 	10px 0 0 0;
					padding			: 	0 0 0 20px;
					color			:	#999;
					font-size		:	12px
				}

		.DashContenedorPerson{
			font-family		:   RobotoDraft, \'Helvetica Neue\', Helvetica, Arial;
			width			:	450px;
			height			:	100px;
			background-color:	#FFF;
			margin			:	10px;
			padding			:	5px 5px 15px 5px;
			float			:	left;
			-webkit-box-shadow: 1px 1px 15px 3px #EEE;
			-moz-box-shadow: 1px 1px 15px 3px #EEE;
			box-shadow: 1px 1px 15px 3px #EEE;
		}
				.DashContenedorPerson img{
					width:100;
					height:100px;
					border-radius: 100px;
					/*box-shadow: 2px 2px 1px #CCC;*/
					box-shadow:none;
					float:left;
				}
				.DashFotoPerson{
					width:100px;
					height:100px;
					float:left;
					margin: 5px 0 0 0;
				}
				.DashPersonNombre{
					width:300px;
					height:20px;
					float:left;
					margin:10px 0 0 10px;
				}
				.DashPersonChart{
					width:330px;
					height:100px;
					float:left;
					margin:0 0 0 10px;
				}
				.DashPersonIcono{
					width:48px;
					height:48px;
					float:left;

				}

		.MainDashContentCRM .DashContenedorPerson{
			font-family		:   RobotoDraft, \'Helvetica Neue\', Helvetica, Arial;
			width			:	100px;
			height			:	100px;
			background-color:	#FFF;
			margin			:	20px 0 0 10px;
			padding			:	5px 5px 15px 5px;
			float			:	left;
			-webkit-box-shadow: none
			-moz-box-shadow: none;
			box-shadow: none;
		}

		.MainDashContentCRM .DashContenedor{
			font-family		:   RobotoDraft, \'Helvetica Neue\', Helvetica, Arial;
			width:250px;
			height:140px;
			background-color:#FFF;
			margin:10px;
			padding:10px;
			float:left;
			/*border	: 	1px solid #EEE;*/

			-webkit-box-shadow: 1px 1px 15px 3px #EEE;
			-moz-box-shadow: 1px 1px 15px 3px #EEE;
			box-shadow: 1px 1px 15px 3px #EEE;
		}

		.MainDashContentCRM .DashIndicador{
			float:left;
			width:70px;
			height:50px;
			text-align:left;
			font-size:50px;
			padding:0 0 0 10px;
		}
		.MainDashContentCRM .DashLabelTitle{
			float: left;
			width: 240px;
			margin: 5px 0 0 0;
			padding: 0 2px 0 0;
			color:#666;
			font-size:20px
		}
		.MainDashContentCRM .DashLabelDat{
			float			: 	left;
			width			: 	250px;
			margin			: 	0 0 0 0;
			padding			: 	0 2px 0 0;
			color			:	#999;
			font-size		:	12px
		}

	/*************************************************************************************************************************/
    /**                  					                 C  R  M   												        **/
    /*************************************************************************************************************************/


	/* TOOLBAR */
	#ToolbarTareas{
		font-family		:   RobotoDraft, \'Helvetica Neue\', Helvetica, Arial;
		width			:	calc(100% - 40px);
		height			:	48px;
		background-color:	rgba('.$_SESSION["COLOR_MD_CALENDARIO"].',1);
		margin			:	0 0 10px 0;
		padding			:	20px;
		color			:	#FFF;
	}

	.TituloGrupo{
		font-size		: 	18px;
		font-weight		:	normal;
		font-family		:	RobotoDraft, \'Helvetica Neue\', Helvetica, Arial;
		color 			: 	#333;
		padding			:	10px 0 5px 0 ;
		margin-top		:	10px;
	}

';



?>

