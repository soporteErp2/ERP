<?php
	header("Content-type: text/css");
	
	session_start();
?>

html,body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,p,blockquote,th,td{margin:0;padding:0;}img,body,html{border:0;}address,caption,cite,code,dfn,em,strong,th,var{font-style:normal;font-weight:normal;}ol,ul {list-style:none;}caption,th {text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;}q:before,q:after{content:'';}.ext-el-mask {
    z-index: 100;
    position: absolute;
    top:0;
    left:0;
    -moz-opacity: 0.5;
    opacity: .50;
    filter: alpha(opacity=50);
    width: 100%;
    height: 100%;
    zoom: 1;
}

.ext-el-mask-msg {
    z-index: 20001;
    position: absolute;
    top: 0;
    left: 0;
    border:1px solid;
    background:repeat-x 0 -16px;
    padding:2px;
}

.ext-el-mask-msg div {
    padding:5px 10px 5px 10px;
    border:1px solid;
    cursor:wait;
}

.ext-shim {
    position:absolute;
    visibility:hidden;
    left:0;
    top:0;
    overflow:hidden;
}

.ext-ie .ext-shim {
    filter: alpha(opacity=0);
}

.ext-ie6 .ext-shim {
    margin-left: 5px;
    margin-top: 3px;
}

.x-mask-loading div {
    padding:5px 10px 5px 25px;
    background:no-repeat 5px 5px;
    line-height:16px;
}

/* class for hiding elements without using display:none */
.x-hidden, .x-hide-offsets {
    position:absolute !important;
    left:-10000px;
    top:-10000px;
    visibility:hidden;
}

.x-hide-display {
    display:none !important;
}

.x-hide-visibility {
    visibility:hidden !important;
}

.x-masked {
    overflow: hidden !important;
}
.x-masked-relative {
    position: relative !important;
}

.x-masked select, .x-masked object, .x-masked embed {
    visibility: hidden;
}

.x-layer {
    visibility: hidden;
}

.x-unselectable, .x-unselectable * {
    -moz-user-select: none;
    -khtml-user-select: none;
    -webkit-user-select:ignore;
}

.x-repaint {
    zoom: 1;
    background-color: transparent;
    -moz-outline: none;
}

.x-item-disabled {
    cursor: default;
    opacity: .6;
    -moz-opacity: .6;
    filter: alpha(opacity=60);
}

.x-item-disabled * {
    cursor: default !important;
}

.x-splitbar-proxy {
    position: absolute;
    visibility: hidden;
    z-index: 20001;
    zoom: 1;
    line-height: 1px;
    font-size: 1px;
    overflow: hidden;
}

.x-splitbar-h, .x-splitbar-proxy-h {
    cursor: e-resize;
    cursor: col-resize;
}

.x-splitbar-v, .x-splitbar-proxy-v {
    cursor: s-resize;
    cursor: row-resize;
}

.x-color-palette {
    width: 150px;
    height: 92px;
    cursor: pointer;
}

.x-color-palette a {
    border: 1px solid;
    float: left;
    padding: 2px;
    text-decoration: none;
    -moz-outline: 0 none;
    outline: 0 none;
    cursor: pointer;
}

.x-color-palette a:hover, .x-color-palette a.x-color-palette-sel {
    border: 1px solid;
}

.x-color-palette em {
    display: block;
    border: 1px solid;
}

.x-color-palette em span {
    cursor: pointer;
    display: block;
    height: 10px;
    line-height: 10px;
    width: 10px;
}

.x-ie-shadow {
    display: none;
    position: absolute;
    overflow: hidden;
    left:0;
    top:0;
    zoom:1;
}

.x-shadow {
    display: none;
    position: absolute;
    overflow: hidden;
    left:0;
    top:0;
}

.x-shadow * {
    overflow: hidden;
}

.x-shadow * {
    padding: 0;
    border: 0;
    margin: 0;
    clear: none;
    zoom: 1;
}

/* top  bottom */
.x-shadow .xstc, .x-shadow .xsbc {
    height: 6px;
    float: left;
}

/* corners */
.x-shadow .xstl, .x-shadow .xstr, .x-shadow .xsbl, .x-shadow .xsbr {
    width: 6px;
    height: 6px;
    float: left;
}

/* sides */
.x-shadow .xsc {
    width: 100%;
}

.x-shadow .xsml, .x-shadow .xsmr {
    width: 6px;
    float: left;
    height: 100%;
}

.x-shadow .xsmc {
    float: left;
    height: 100%;
    background: transparent;
}

.x-shadow .xst, .x-shadow .xsb {
    height: 6px;
    overflow: hidden;
    width: 100%;
}

.x-shadow .xsml {
    background: transparent repeat-y 0 0;
}

.x-shadow .xsmr {
    background: transparent repeat-y -6px 0;
}

.x-shadow .xstl {
    background: transparent no-repeat 0 0;
}

.x-shadow .xstc {
    background: transparent repeat-x 0 -30px;
}

.x-shadow .xstr {
    background: transparent repeat-x 0 -18px;
}

.x-shadow .xsbl {
    background: transparent no-repeat 0 -12px;
}

.x-shadow .xsbc {
    background: transparent repeat-x 0 -36px;
}

.x-shadow .xsbr {
    background: transparent repeat-x 0 -6px;
}

.loading-indicator {
    background: no-repeat left;
    padding-left: 20px;
    line-height: 16px;
    margin: 3px;
}

.x-text-resize {
    position: absolute;
    left: -1000px;
    top: -1000px;
    visibility: hidden;
    zoom: 1;
}

.x-drag-overlay {
    width: 100%;
    height: 100%;
    display: none;
    position: absolute;
    left: 0;
    top: 0;
    background-image:url(../images/default/s.gif);
    z-index: 20000;
}

.x-clear {
    clear:both;
    height:0;
    overflow:hidden;
    line-height:0;
    font-size:0;
}

.x-spotlight {
    z-index: 8999;
    position: absolute;
    top:0;
    left:0;
    -moz-opacity: 0.5;
    opacity: .50;
    filter: alpha(opacity=50);
    width:0;
    height:0;
    zoom: 1;
}

#x-history-frame {
    position:absolute;
    top:-1px;
    left:0;
	width:1px;
    height:1px;
    visibility:hidden;
}

#x-history-field {
    position:absolute;
    top:0;
    left:-1px;
	width:1px;
    height:1px;
    visibility:hidden;
}
.x-resizable-handle {
    position:absolute;
    z-index:100;
    /* ie needs these */
    font-size:1px;
    line-height:6px;
    overflow:hidden;
	filter:alpha(opacity=0);
	opacity:0;
	zoom:1;
}

.x-resizable-handle-east{
    width:6px;
    cursor:e-resize;
    right:0;
    top:0;
    height:100%;
}

.ext-ie .x-resizable-handle-east {
    margin-right:-1px; /*IE rounding error*/
}

.x-resizable-handle-south{
    width:100%;
    cursor:s-resize;
    left:0;
    bottom:0;
    height:6px;
}

.ext-ie .x-resizable-handle-south {
    margin-bottom:-1px; /*IE rounding error*/
}

.x-resizable-handle-west{
    width:6px;
    cursor:w-resize;
    left:0;
    top:0;
    height:100%;
}

.x-resizable-handle-north{
    width:100%;
    cursor:n-resize;
    left:0;
    top:0;
    height:6px;
}

.x-resizable-handle-southeast{
    width:6px;
    cursor:se-resize;
    right:0;
    bottom:0;
    height:6px;
    z-index:101;
}

.x-resizable-handle-northwest{
    width:6px;
    cursor:nw-resize;
    left:0;
    top:0;
    height:6px;
    z-index:101;
}

.x-resizable-handle-northeast{
    width:6px;
    cursor:ne-resize;
    right:0;
    top:0;
    height:6px;
    z-index:101;
}

.x-resizable-handle-southwest{
    width:6px;
    cursor:sw-resize;
    left:0;
    bottom:0;
    height:6px;
    z-index:101;
}

.x-resizable-over .x-resizable-handle, .x-resizable-pinned .x-resizable-handle{
    filter:alpha(opacity=100);
	opacity:1;
}

.x-resizable-over .x-resizable-handle-east, .x-resizable-pinned .x-resizable-handle-east,
.x-resizable-over .x-resizable-handle-west, .x-resizable-pinned .x-resizable-handle-west
{
	background-position: left;
}

.x-resizable-over .x-resizable-handle-south, .x-resizable-pinned .x-resizable-handle-south,
.x-resizable-over .x-resizable-handle-north, .x-resizable-pinned .x-resizable-handle-north
{
    background-position: top;
}

.x-resizable-over .x-resizable-handle-southeast, .x-resizable-pinned .x-resizable-handle-southeast{
    background-position: top left;
}

.x-resizable-over .x-resizable-handle-northwest, .x-resizable-pinned .x-resizable-handle-northwest{
    background-position:bottom right;
}

.x-resizable-over .x-resizable-handle-northeast, .x-resizable-pinned .x-resizable-handle-northeast{
    background-position: bottom left;
}

.x-resizable-over .x-resizable-handle-southwest, .x-resizable-pinned .x-resizable-handle-southwest{
    background-position: top right;
}

.x-resizable-proxy{
    border: 1px dashed;
    position:absolute;
    overflow:hidden;
    display:none;
	left:0;
    top:0;
    z-index:50000;
}

.x-resizable-overlay{
    width:100%;
	height:100%;
	display:none;
	position:absolute;
	left:0;
	top:0;
	z-index:200000;
	-moz-opacity: 0;
    opacity:0;
    filter: alpha(opacity=0);
}
.x-tab-panel {
    overflow:hidden;
}

.x-tab-panel-header, .x-tab-panel-footer {
	border: 1px solid;
    overflow:hidden;
    zoom:1;
}

.x-tab-panel-header {
	border: 1px solid;
	padding-bottom: 2px;
}

.x-tab-panel-footer {
	border: 1px solid;
	padding-top: 2px;
}

.x-tab-strip-wrap {
	width:100%;
    overflow:hidden;
    position:relative;
    zoom:1;
}

ul.x-tab-strip {
	display:block;
    width:5000px;
    zoom:1;
}

ul.x-tab-strip-top{
	padding-top: 1px;
	background: repeat-x bottom;
	border-bottom: 1px solid;
}

ul.x-tab-strip-bottom{
	padding-bottom: 1px;
	background: repeat-x top;
	border-top: 1px solid;
	border-bottom: 0 none;
}

.x-tab-panel-header-plain .x-tab-strip-top {
    background:transparent !important;
    padding-top:0 !important;
}

.x-tab-panel-header-plain {
    background:transparent !important;
    border-width:0 !important;
    padding-bottom:0 !important;
}

.x-tab-panel-header-plain .x-tab-strip-spacer,
.x-tab-panel-footer-plain .x-tab-strip-spacer {
    border:1px solid;
    height:2px;
    font-size:1px;
    line-height:1px;
}

.x-tab-panel-header-plain .x-tab-strip-spacer {
    border-top: 0 none;
}

.x-tab-panel-footer-plain .x-tab-strip-spacer {
    border-bottom: 0 none;
}

.x-tab-panel-footer-plain .x-tab-strip-bottom {
    background:transparent !important;
    padding-bottom:0 !important;
}

.x-tab-panel-footer-plain {
    background:transparent !important;
    border-width:0 !important;
    padding-top:0 !important;
}

.ext-border-box .x-tab-panel-header-plain .x-tab-strip-spacer,
.ext-border-box .x-tab-panel-footer-plain .x-tab-strip-spacer {
    height:3px;
}

ul.x-tab-strip li {
    float:left;
    margin-left:2px;
}

ul.x-tab-strip li.x-tab-edge {
    float:left;
    margin:0 !important;
    padding:0 !important;
    border:0 none !important;
    font-size:1px !important;
    line-height:1px !important;
    overflow:hidden;
    zoom:1;
    background:transparent !important;
    width:1px;
}

.x-tab-strip a, .x-tab-strip span, .x-tab-strip em {
	display:block;
}

.x-tab-strip a {
	text-decoration:none !important;
	-moz-outline: none;
	outline: none;
	cursor:pointer;
}

.x-tab-strip-inner {
    overflow:hidden;
	text-overflow: ellipsis;
}

.x-tab-strip span.x-tab-strip-text {
	white-space: nowrap;
	cursor:pointer;
    padding:4px 0;
}

.x-tab-strip-top .x-tab-with-icon .x-tab-right {
    padding-left:6px;
}

.x-tab-strip .x-tab-with-icon span.x-tab-strip-text {
	padding-left:20px;
    background-position: 0 3px;
    background-repeat: no-repeat;
}

.x-tab-strip-active, .x-tab-strip-active a.x-tab-right {
    cursor:default;
}

.x-tab-strip-active span.x-tab-strip-text {
	cursor:default;
}

.x-tab-strip-disabled .x-tabs-text {
	cursor:default;
}

.x-tab-panel-body {
    overflow:hidden;
}

.x-tab-panel-bwrap {
    overflow:hidden;
}

.ext-ie .x-tab-strip .x-tab-right {
    position:relative;
}

.x-tab-strip-top .x-tab-strip-active .x-tab-right {
    margin-bottom:-1px;
}

/*
 * Horrible hack for IE8 in quirks mode
 */
.ext-border-box .ext-ie8 .x-tab-strip .x-tab-right{
    top: 1px;
}

.x-tab-strip-top .x-tab-strip-active .x-tab-right span.x-tab-strip-text {
    padding-bottom:5px;
}

.x-tab-strip-bottom .x-tab-strip-active .x-tab-right {
    margin-top:-1px;
}

.x-tab-strip-bottom .x-tab-strip-active .x-tab-right span.x-tab-strip-text {
    padding-top:5px;
}

.x-tab-strip-top .x-tab-right {
	background: transparent no-repeat 0 -51px;
    padding-left:10px;
}

.x-tab-strip-top .x-tab-left {
	background: transparent no-repeat right -351px;
    padding-right:10px;
}

.x-tab-strip-top .x-tab-strip-inner {
	background: transparent repeat-x 0 -201px;
}

.x-tab-strip-top .x-tab-strip-over .x-tab-right {
	 background-position:0 -101px;
}

.x-tab-strip-top .x-tab-strip-over .x-tab-left {
	 background-position:right -401px;
}

.x-tab-strip-top .x-tab-strip-over .x-tab-strip-inner {
	 background-position:0 -251px;
}

.x-tab-strip-top .x-tab-strip-active .x-tab-right {
	background-position: 0 0;
}

.x-tab-strip-top .x-tab-strip-active .x-tab-left {
	background-position: right -301px;
}

.x-tab-strip-top .x-tab-strip-active .x-tab-strip-inner {
	background-position: 0 -151px;
}

.x-tab-strip-bottom .x-tab-right {
	background: no-repeat bottom right;
}

.x-tab-strip-bottom .x-tab-left {
	background: no-repeat bottom left;
}

.x-tab-strip-bottom .x-tab-strip-active .x-tab-right {
	background: no-repeat bottom left;
}

.x-tab-strip-bottom .x-tab-strip-active .x-tab-left {
	background: no-repeat bottom right;
}

.x-tab-strip-bottom .x-tab-left {
    padding:0 10px;
}

.x-tab-strip-bottom .x-tab-right {
    padding:0;
}

.x-tab-strip .x-tab-strip-close {
    display:none;
}

.x-tab-strip-closable {
    position:relative;
}

.x-tab-strip-closable .x-tab-left {
    padding-right:19px;
}

.x-tab-strip .x-tab-strip-closable a.x-tab-strip-close {
    opacity:.6;
    -moz-opacity:.6;
    background-repeat:no-repeat;
    display:block;
	width:11px;
    height:11px;
    position:absolute;
    top:3px;
    right:3px;
    cursor:pointer;
    z-index:2;
}

.x-tab-strip .x-tab-strip-active a.x-tab-strip-close {
    opacity:.8;
    -moz-opacity:.8;
}
.x-tab-strip .x-tab-strip-closable a.x-tab-strip-close:hover{
    opacity:1;
    -moz-opacity:1;
}

.x-tab-panel-body {
    border: 1px solid;
}

.x-tab-panel-body-top {
    border-top: 0 none;
}

.x-tab-panel-body-bottom {
    border-bottom: 0 none;
}

.x-tab-scroller-left {
    background: transparent no-repeat -18px 0;
    border-bottom: 1px solid;
    width:18px;
    position:absolute;
    left:0;
    top:0;
    z-index:10;
    cursor:pointer;
}
.x-tab-scroller-left-over {
    background-position: 0 0;
}

.x-tab-scroller-left-disabled {
    background-position: -18px 0;
    opacity:.5;
    -moz-opacity:.5;
    filter:alpha(opacity=50);
    cursor:default;
}

.x-tab-scroller-right {
    background: transparent no-repeat 0 0;
    border-bottom: 1px solid;
    width:18px;
    position:absolute;
    right:0;
    top:0;
    z-index:10;
    cursor:pointer;
}

.x-tab-scroller-right-over {
    background-position: -18px 0;
}

.x-tab-scroller-right-disabled {
    background-position: 0 0;
    opacity:.5;
    -moz-opacity:.5;
    filter:alpha(opacity=50);
    cursor:default;
}

.x-tab-scrolling-bottom .x-tab-scroller-left, .x-tab-scrolling-bottom .x-tab-scroller-right{
    margin-top: 1px;
}

.x-tab-scrolling .x-tab-strip-wrap {
    margin-left:18px;
    margin-right:18px;
}

.x-tab-scrolling {
    position:relative;    
}

.x-tab-panel-bbar .x-toolbar {
    border:1px solid;
    border-top:0 none;
    overflow:hidden;
    padding:2px;
}

.x-tab-panel-tbar .x-toolbar {
    border:1px solid;
    border-top:0 none;
    overflow:hidden;
    padding:2px;
}/* all fields */
.x-form-field{
    margin: 0 0 0 0;
}

.ext-webkit *:focus{
    outline: none !important;
}

/* ---- text fields ---- */
.x-form-text, textarea.x-form-field{
    padding:1px 3px;
    background:repeat-x 0 0;
    border:1px solid;
}

textarea.x-form-field {
    padding:2px 3px;
}

.x-form-text, .ext-ie .x-form-file {
    height:22px;
    line-height:18px;
    vertical-align:middle;
}

.ext-ie6 .x-form-text, .ext-ie7 .x-form-text {
    margin:-1px 0; /* ie bogus margin bug */
    height:22px; /* ie quirks */
    line-height:18px;
}

.ext-ie6 textarea.x-form-field, .ext-ie7 textarea.x-form-field {
    margin:-1px 0; /* ie bogus margin bug */
}

.ext-strict .x-form-text {
    height:18px;
}

.ext-safari.ext-mac textarea.x-form-field {
    margin-bottom:-2px; /* another bogus margin bug, safari/mac only */
}

.ext-strict .ext-ie8 .x-form-text, .ext-strict .ext-ie8 textarea.x-form-field {
	margin-bottom: 1px;
}

.ext-gecko .x-form-text , .ext-ie8 .x-form-text {
    padding-top:2px; /* FF won't center the text vertically */
    padding-bottom:0;
}

textarea {
    resize: none;  /* Disable browser resizable textarea */
}

/* select boxes */
.x-form-select-one {
    height:20px;
    line-height:18px;
    vertical-align:middle;
    border: 1px solid;
}

/* multi select boxes */

/* --- TODO --- */

/* 2.0.2 style */
.x-form-check-wrap {
    line-height:18px;
    height: 22px;
}

.ext-ie .x-form-check-wrap input {
    width:15px;
    height:15px;
}

.x-form-check-wrap input{
    vertical-align: bottom;
}

.x-editor .x-form-check-wrap {
    padding:3px;
}

.x-editor .x-form-checkbox {
    height:13px;
}

.x-form-check-group-label {
    border-bottom: 1px solid;
    margin-bottom: 5px;
    padding-left: 3px !important;
    float: none !important;
}

/* wrapped fields and triggers */
.x-form-field-wrap .x-form-trigger{
    width:17px;
    height:21px;
    border:0;
    background:transparent no-repeat 0 0;
    cursor:pointer;
    border-bottom: 1px solid;
    position:absolute;
    top:0;
}

.x-form-field-wrap .x-form-date-trigger, .x-form-field-wrap .x-form-clear-trigger, .x-form-field-wrap .x-form-search-trigger{
    cursor:pointer;
}

.ext-webkit .x-form-field-wrap .x-form-trigger{
    right:0;
}

.x-form-field-wrap .x-form-twin-triggers .x-form-trigger{
    position:static;
    top:auto;
    vertical-align:top;
}

.x-form-field-wrap {
    position:relative;
    left:0;top:0;
    zoom:1;
    white-space: nowrap;
}

.x-form-field-wrap .x-form-trigger-over{
    background-position:-17px 0;
}

.x-form-field-wrap .x-form-trigger-click{
    background-position:-34px 0;
}

.x-trigger-wrap-focus .x-form-trigger{
    background-position:-51px 0;
}

.x-trigger-wrap-focus .x-form-trigger-over{
    background-position:-68px 0;
}

.x-trigger-wrap-focus .x-form-trigger-click{
    background-position:-85px 0;
}

.x-trigger-wrap-focus .x-form-trigger{
    border-bottom: 1px solid;
}

.x-item-disabled .x-form-trigger-over{
    background-position:0 0 !important;
    border-bottom: 1px solid;
}

.x-item-disabled .x-form-trigger-click{
    background-position:0 0 !important;
    border-bottom: 1px solid;
}

.x-trigger-noedit{
    cursor:pointer;
}

/* field focus style */
.x-form-focus, textarea.x-form-focus{
	border: 1px solid;
}

/* invalid fields */
.x-form-invalid, textarea.x-form-invalid{
	background:repeat-x bottom;
	border: 1px solid;
}

.ext-webkit .x-form-invalid{
	border: 1px solid;
}

.x-form-inner-invalid, textarea.x-form-inner-invalid{
	background:repeat-x bottom;
}

/* editors */
.x-editor {
    visibility:hidden;
    padding:0;
    margin:0;
}

.x-form-grow-sizer {
    left: -10000px;
	padding: 8px 3px;
    position: absolute;
    visibility:hidden;
    top: -10000px;
	white-space: pre-wrap;
    white-space: -moz-pre-wrap;
    white-space: -pre-wrap;
    white-space: -o-pre-wrap;
    word-wrap: break-word;
    zoom:1;
}

.x-form-grow-sizer p {
    margin:0 !important;
    border:0 none !important;
    padding:0 !important;
}

/* Form Items CSS */

.x-form-item {
    display:block;
    margin-bottom:4px;
    zoom:1;
}

.x-form-item label.x-form-item-label {
    display:block;
    float:left;
    width:100px;
    padding:3px;
    padding-left:0;
    clear:left;
    z-index:2;
    position:relative;
}

.x-form-element {
    padding-left:105px;
    position:relative;
}

.x-form-invalid-msg {
    padding:2px;
    padding-left:18px;
    background: transparent no-repeat 0 2px;
    line-height:16px;
    width:200px;
}

.x-form-label-left label.x-form-item-label {
   text-align:left;
}

.x-form-label-right label.x-form-item-label {
   text-align:right;
}

.x-form-label-top .x-form-item label.x-form-item-label {
    width:auto;
    float:none;
    clear:none;
    display:inline;
    margin-bottom:4px;
    position:static;
}

.x-form-label-top .x-form-element {
    padding-left:0;
    padding-top:4px;
}

.x-form-label-top .x-form-item {
    padding-bottom:4px;
}

/* Editor small font for grid, toolbar and tree */
.x-small-editor .x-form-text {
    height:20px;
    line-height:16px;
    vertical-align:middle;
}

.ext-ie6 .x-small-editor .x-form-text, .ext-ie7 .x-small-editor .x-form-text {
    margin-top:-1px !important; /* ie bogus margin bug */
    margin-bottom:-1px !important;
    height:20px !important; /* ie quirks */
    line-height:16px !important;
}

.ext-strict .x-small-editor .x-form-text {
    height:16px !important;
}

.ext-ie6 .x-small-editor .x-form-text, .ext-ie7 .x-small-editor .x-form-text {
    height:20px;
    line-height:16px;
}

.ext-border-box .x-small-editor .x-form-text {
    height:20px;
}

.x-small-editor .x-form-select-one {
    height:20px;
    line-height:16px;
    vertical-align:middle;
}

.x-small-editor .x-form-num-field {
    text-align:right;
}

.x-small-editor .x-form-field-wrap .x-form-trigger{
    height:19px;
}

.ext-webkit .x-small-editor .x-form-text{padding-top:3px;font-size:100%;}

.x-form-clear {
    clear:both;
    height:0;
    overflow:hidden;
    line-height:0;
    font-size:0;
}
.x-form-clear-left {
    clear:left;
    height:0;
    overflow:hidden;
    line-height:0;
    font-size:0;
}

.ext-ie6 .x-form-check-wrap input, .ext-border-box .x-form-check-wrap input{
   margin-top: 3px; 
}

.x-form-cb-label {
    position: relative;
    margin-left:4px;
    top: 2px;
}

.ext-ie .x-form-cb-label{
    top: 1px;
}

.ext-ie6 .x-form-cb-label, .ext-border-box .x-form-cb-label{
    top: 3px;
}

.x-form-display-field{
    padding-top: 2px;
}

.ext-gecko .x-form-display-field, .ext-strict .ext-ie7 .x-form-display-field{
    padding-top: 1px;
}

.ext-ie .x-form-display-field{
    padding-top: 3px;
}

.ext-strict .ext-ie8 .x-form-display-field{
    padding-top: 0;
}

.x-form-column {
    float:left;
    padding:0;
    margin:0;
    width:48%;
    overflow:hidden;
    zoom:1;
}

/* buttons */
.x-form .x-form-btns-ct .x-btn{
	float:right;
	clear:none;
}

.x-form .x-form-btns-ct .x-form-btns td {
	border:0;
	padding:0;
}

.x-form .x-form-btns-ct .x-form-btns-right table{
	float:right;
	clear:none;
}

.x-form .x-form-btns-ct .x-form-btns-left table{
	float:left;
	clear:none;
}

.x-form .x-form-btns-ct .x-form-btns-center{
	text-align:center; /*ie*/
}

.x-form .x-form-btns-ct .x-form-btns-center table{
	margin:0 auto; /*everyone else*/
}

.x-form .x-form-btns-ct table td.x-form-btn-td{
	padding:3px;
}

.x-form .x-form-btns-ct .x-btn-focus .x-btn-left{
	background-position:0 -147px;
}

.x-form .x-form-btns-ct .x-btn-focus .x-btn-right{
	background-position:0 -168px;
}

.x-form .x-form-btns-ct .x-btn-focus .x-btn-center{
	background-position:0 -189px;
}

.x-form .x-form-btns-ct .x-btn-click .x-btn-center{
	background-position:0 -126px;
}

.x-form .x-form-btns-ct .x-btn-click  .x-btn-right{
	background-position:0 -84px;
}

.x-form .x-form-btns-ct .x-btn-click .x-btn-left{
	background-position:0 -63px;
}

.x-form-invalid-icon {
    width:16px;
    height:18px;
    visibility:hidden;
    position:absolute;
    left:0;
    top:0;
    display:block;
    background:transparent no-repeat 0 2px;
}

/* fieldsets */
.x-fieldset {
    border:1px solid;
    padding:10px;
    margin-bottom:10px;
    display:block; /* preserve margins in IE */
}

/* make top of checkbox/tools visible in webkit */
.ext-webkit .x-fieldset-header {
    padding-top: 1px;
}        

.ext-ie .x-fieldset legend {
    margin-bottom:10px;
}

.ext-ie .x-fieldset {
    padding-top: 0;
    padding-bottom:10px;
}

.x-fieldset legend .x-tool-toggle {
    margin-right:3px;
    margin-left:0;
    float:left !important;
}

.x-fieldset legend input {
    margin-right:3px;
    float:left !important;
    height:13px;
    width:13px;
}

fieldset.x-panel-collapsed {
    padding-bottom:0 !important;
    border-width: 1px 1px 0 1px !important;
    border-left-color: transparent;
    border-right-color: transparent;
}      
  
.ext-ie6 fieldset.x-panel-collapsed{
    padding-bottom:0 !important;
    border-width: 1px 0 0 0 !important;
    margin-left: 1px;
    margin-right: 1px;
}

fieldset.x-panel-collapsed .x-fieldset-bwrap {
    visibility:hidden;
    position:absolute;
    left:-1000px;
    top:-1000px;
}

.ext-ie .x-fieldset-bwrap {
    zoom:1;
}

.x-fieldset-noborder {
    border:0px none transparent;
}

.x-fieldset-noborder legend {
    margin-left:-3px;
}

/* IE legend positioing bug */
.ext-ie .x-fieldset-noborder legend {
    position: relative;
    margin-bottom:23px;
}
.ext-ie .x-fieldset-noborder legend span {
    position: absolute;
    left:16px;
}
        
.ext-gecko .x-window-body .x-form-item {
    -moz-outline: none;
    overflow: auto;
}

.ext-gecko .x-form-item {
    -moz-outline: none;
}

.x-hide-label label.x-form-item-label {
     display:none;
}

.x-hide-label .x-form-element {
     padding-left: 0 !important;
}

.x-form-label-top .x-hide-label label.x-form-item-label{
    display: none;
}

.x-fieldset {
    overflow:hidden;
}

.x-fieldset-bwrap {
    overflow:hidden;
    zoom:1;
}

.x-fieldset-body {
    overflow:hidden;
}


.x-btn{
	cursor:pointer;
	white-space: nowrap;
}

.x-btn button{
    border:0 none;
    background:transparent;
    padding-left:3px;
    padding-right:3px;
    cursor:pointer;
    margin:0;
    overflow:visible;
    width:auto;
    -moz-outline:0 none;
    outline:0 none;
}

* html .ext-ie .x-btn button {
    width:1px;
}

.ext-gecko .x-btn button {
    padding-left:0;
    padding-right:0;
}

.ext-gecko .x-btn button::-moz-focus-inner {
    padding:0;
}

.ext-ie .x-btn button {
    padding-top:2px;
}

.x-btn td {
    padding:0 !important;
}

.x-btn-text {
    cursor:pointer;
	white-space: nowrap;
    padding:0;
}

/* icon placement and sizing styles */

/* Only text */
.x-btn-noicon .x-btn-small .x-btn-text{
	height: 16px;
}

.x-btn-noicon .x-btn-medium .x-btn-text{
    height: 24px;
}

.x-btn-noicon .x-btn-large .x-btn-text{
    height: 32px;
}

/* Only icons */
.x-btn-icon .x-btn-text{
    background-position: center;
	background-repeat: no-repeat;
}

.x-btn-icon .x-btn-small .x-btn-text{
	height: 16px;
	width: 16px;
}

.x-btn-icon .x-btn-medium .x-btn-text{
    height: 24px;
	width: 24px;
}

.x-btn-icon .x-btn-large .x-btn-text{
    height: 32px;
	width: 32px;
}

/* Icons and text */
/* left */
.x-btn-text-icon .x-btn-icon-small-left .x-btn-text{
    background-position: 0 center;
	background-repeat: no-repeat;
    padding-left:18px;
    height:16px;
}

.x-btn-text-icon .x-btn-icon-medium-left .x-btn-text{
    background-position: 0 center;
	background-repeat: no-repeat;
    padding-left:26px;
    height:24px;
}

.x-btn-text-icon .x-btn-icon-large-left .x-btn-text{
    background-position: 0 center;
	background-repeat: no-repeat;
    padding-left:34px;
    height:32px;
}

/* top */
.x-btn-text-icon .x-btn-icon-small-top .x-btn-text{
    background-position: center 0;
	background-repeat: no-repeat;
    padding-top:18px;
}

.x-btn-text-icon .x-btn-icon-medium-top .x-btn-text{
    background-position: center 0;
	background-repeat: no-repeat;
    padding-top:26px;
}

.x-btn-text-icon .x-btn-icon-large-top .x-btn-text{
    background-position: center 0;
	background-repeat: no-repeat;
    padding-top:34px;
}

/* right */
.x-btn-text-icon .x-btn-icon-small-right .x-btn-text{
    background-position: right center;
	background-repeat: no-repeat;
    padding-right:18px;
    height:16px;
}

.x-btn-text-icon .x-btn-icon-medium-right .x-btn-text{
    background-position: right center;
	background-repeat: no-repeat;
    padding-right:26px;
    height:24px;
}

.x-btn-text-icon .x-btn-icon-large-right .x-btn-text{
    background-position: right center;
	background-repeat: no-repeat;
    padding-right:34px;
    height:32px;
}

/* bottom */
.x-btn-text-icon .x-btn-icon-small-bottom .x-btn-text{
    background-position: center bottom;
	background-repeat: no-repeat;
    padding-bottom:18px;
}

.x-btn-text-icon .x-btn-icon-medium-bottom .x-btn-text{
    background-position: center bottom;
	background-repeat: no-repeat;
    padding-bottom:26px;
}

.x-btn-text-icon .x-btn-icon-large-bottom .x-btn-text{
    background-position: center bottom;
	background-repeat: no-repeat;
    padding-bottom:34px;
}

/* background positioning */
.x-btn-tr i, .x-btn-tl i, .x-btn-mr i, .x-btn-ml i, .x-btn-br i, .x-btn-bl i{
	font-size:1px;
    line-height:1px;
    width:3px;
    display:block;
    overflow:hidden;
}

.x-btn-tr i, .x-btn-tl i, .x-btn-br i, .x-btn-bl i{
	height:3px;
}

.x-btn-tl{
	width:3px;
	height:3px;
	background:no-repeat 0 0;
}
.x-btn-tr{
	width:3px;
	height:3px;
	background:no-repeat -3px 0;
}
.x-btn-tc{
	height:3px;
	background:repeat-x 0 -6px;
}

.x-btn-ml{
	width:3px;
	background:no-repeat 0 -24px;
}
.x-btn-mr{
	width:3px;
	background:no-repeat -3px -24px;
}

.x-btn-mc{
	background: repeat-x 0 -1096px;
    vertical-align: middle;
	text-align:center;
	padding:0 5px;
	cursor:pointer;
	white-space:nowrap;
}

/* Fixes an issue with the button height */
.ext-strict .ext-ie6 .x-btn-mc, .ext-strict .ext-ie7 .x-btn-mc {
    height: 100%;
}

.x-btn-bl{
	width:3px;
	height:3px;
	background:no-repeat 0 -3px;
}

.x-btn-br{
	width:3px;
	height:3px;
	background:no-repeat -3px -3px;
}

.x-btn-bc{
	height:3px;
	background:repeat-x 0 -15px;
}

.x-btn-over .x-btn-tl{
	/*background-position: -6px 0;*/
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.90); /*repeat-x 0 -1096px;*/
}

.x-btn-over .x-btn-tr{
	/*background-position: -9px 0;*/
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.90); /*repeat-x 0 -1096px;*/
}

.x-btn-over .x-btn-tc{
	/*background-position: 0 -9px;*/
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.90); /*repeat-x 0 -1096px;*/
    color # 000;
}

.x-btn-over .x-btn-ml{
	/*background-position: -6px -24px;*/
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.90); /*repeat-x 0 -1096px;*/
}

.x-btn-over .x-btn-mr{
	/*background-position: -9px -24px;*/
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.90); /*repeat-x 0 -1096px;*/
}

.x-btn-over .x-btn-mc{
	/*background-position: 0 -2168px;*/
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.40); /*repeat-x 0 -1096px;*/
}

.x-btn-over .x-btn-bl{
	/*background-position: -6px -3px;*/
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.90); /*repeat-x 0 -1096px;*/
}

.x-btn-over .x-btn-br{
	/*background-position: -9px -3px;*/
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.90); /*repeat-x 0 -1096px;*/
}

.x-btn-over .x-btn-bc{
	/*background-position: 0 -18px;*/
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.90); /*repeat-x 0 -1096px;*/
}

.x-btn-click .x-btn-tl, .x-btn-menu-active .x-btn-tl, .x-btn-pressed .x-btn-tl{
	background-position: -12px 0;
}

.x-btn-click .x-btn-tr, .x-btn-menu-active .x-btn-tr, .x-btn-pressed .x-btn-tr{
	background-position: -15px 0;
}

.x-btn-click .x-btn-tc, .x-btn-menu-active .x-btn-tc, .x-btn-pressed .x-btn-tc{
	background-position: 0 -12px;
}

.x-btn-click .x-btn-ml, .x-btn-menu-active .x-btn-ml, .x-btn-pressed .x-btn-ml{
	background-position: -12px -24px;
}

.x-btn-click .x-btn-mr, .x-btn-menu-active .x-btn-mr, .x-btn-pressed .x-btn-mr{
	background-position: -15px -24px;
}

.x-btn-click .x-btn-mc, .x-btn-menu-active .x-btn-mc, .x-btn-pressed .x-btn-mc{
	background-position: 0 -3240px;
}

.x-btn-click .x-btn-bl, .x-btn-menu-active .x-btn-bl, .x-btn-pressed .x-btn-bl{
	background-position: -12px -3px;
}

.x-btn-click .x-btn-br, .x-btn-menu-active .x-btn-br, .x-btn-pressed .x-btn-br{
	background-position: -15px -3px;
}

.x-btn-click .x-btn-bc, .x-btn-menu-active .x-btn-bc, .x-btn-pressed .x-btn-bc{
	background-position: 0 -21px;
}

.x-btn-disabled *{
	cursor:default !important;
}


/* With a menu arrow */
/* right */
.x-btn-mc em.x-btn-arrow {
    display:block;
    background:transparent no-repeat right center;
	padding-right:10px;
}

.x-btn-mc em.x-btn-split {
    display:block;
    background: transparent no-repeat right center;
	padding-right:14px;
}

/* bottom */
.x-btn-mc em.x-btn-arrow-bottom {
    display:block;
    background:transparent no-repeat center bottom;
	padding-bottom:14px;
}

.x-btn-mc em.x-btn-split-bottom {
    display:block;
    background:transparent no-repeat center bottom;
	padding-bottom:14px;
}

/* height adjustment class */
.x-btn-as-arrow .x-btn-mc em {
    display:block;
    background:transparent;
	padding-bottom:14px;
}


/* groups */
.x-btn-group {
    padding:1px;
    border-right:1px solid #8db2e3;
}

.x-btn-group-header {
    padding:2px;
    text-align:center;
}

.x-btn-group-tc {
	background: transparent repeat-x 0 0;
	overflow:hidden;
}

.x-btn-group-tl {
	background: transparent no-repeat 0 0;
	padding-left:3px;
    zoom:1;
}

.x-btn-group-tr {
	background: transparent no-repeat right 0;
	zoom:1;
    padding-right:3px;
}

.x-btn-group-bc {
	background: transparent repeat-x 0 bottom;
    zoom:1;
}

.x-btn-group-bc .x-panel-footer {
    zoom:1;
}

.x-btn-group-bl {
	background: transparent no-repeat 0 bottom;
	padding-left:3px;
    zoom:1;
}

.x-btn-group-br {
	background: transparent no-repeat right bottom;
	padding-right:3px;
    zoom:1;
}

.x-btn-group-mc {
    border:0 none;
    padding:1px 0 0 0;
    margin:0;
}

.x-btn-group-mc .x-btn-group-body {
    background:transparent;
    border: 0 none;
}

.x-btn-group-ml {
	background: transparent repeat-y 0 0;
	padding-left:3px;
    zoom:1;
}

.x-btn-group-mr {
	background: transparent repeat-y right 0;
	padding-right:3px;
    zoom:1;
}

.x-btn-group-bc .x-btn-group-footer {
    padding-bottom:6px;
}

.x-panel-nofooter .x-btn-group-bc {
	height:3px;
    font-size:0;
    line-height:0;
}

.x-btn-group-bwrap {
    overflow:hidden;
    zoom:1;
}

.x-btn-group-body {
    overflow:hidden;
    zoom:1;
}

.x-btn-group-notitle .x-btn-group-tc {
	background: transparent repeat-x 0 0;
	overflow:hidden;
    height:2px;
}.x-toolbar{
    border-style:solid;
    border-width:0 0 1px 0;
    display: block;
	padding:2px;
    background:repeat-x top left;
    position:relative;
    left:0;
    top:0;
    zoom:1;
    overflow:hidden;
}

.x-toolbar-left {
    width: 100%;
}

.x-toolbar .x-item-disabled .x-btn-icon {
    opacity: .35;
    -moz-opacity: .35;
    filter: alpha(opacity=35);
}

.x-toolbar td {
	vertical-align:middle;
}

.x-toolbar td,.x-toolbar span,.x-toolbar input,.x-toolbar div,.x-toolbar select,.x-toolbar label{
	white-space: nowrap;
}

.x-toolbar .x-item-disabled {
	cursor:default;
	opacity:.6;
	-moz-opacity:.6;
	filter:alpha(opacity=60);
}

.x-toolbar .x-item-disabled * {
	cursor:default;
}

.x-toolbar .x-toolbar-cell {
    vertical-align:middle;
}

.x-toolbar .x-btn-tl, .x-toolbar .x-btn-tr, .x-toolbar .x-btn-tc, .x-toolbar .x-btn-ml, .x-toolbar .x-btn-mr,
.x-toolbar .x-btn-mc, .x-toolbar .x-btn-bl, .x-toolbar .x-btn-br, .x-toolbar .x-btn-bc
{
	background-position: 500px 500px;
}

/* These rules are duplicated from button.css to give priority of x-toolbar rules above */
.x-toolbar .x-btn-over .x-btn-tl{
	background-position: -6px 0;
}

.x-toolbar .x-btn-over .x-btn-tr{
	background-position: -9px 0;
}

.x-toolbar .x-btn-over .x-btn-tc{
	background-position: 0 -9px;
}

.x-toolbar .x-btn-over .x-btn-ml{
	background-position: -6px -24px;
}

.x-toolbar .x-btn-over .x-btn-mr{
	background-position: -9px -24px;
}

.x-toolbar .x-btn-over .x-btn-mc{
	background-position: 0 -2168px;
}

.x-toolbar .x-btn-over .x-btn-bl{
	background-position: -6px -3px;
}

.x-toolbar .x-btn-over .x-btn-br{
	background-position: -9px -3px;
}

.x-toolbar .x-btn-over .x-btn-bc{
	background-position: 0 -18px;
}

.x-toolbar .x-btn-click .x-btn-tl, .x-toolbar .x-btn-menu-active .x-btn-tl, .x-toolbar .x-btn-pressed .x-btn-tl{
	background-position: -12px 0;
}

.x-toolbar .x-btn-click .x-btn-tr, .x-toolbar .x-btn-menu-active .x-btn-tr, .x-toolbar .x-btn-pressed .x-btn-tr{
	background-position: -15px 0;
}

.x-toolbar .x-btn-click .x-btn-tc, .x-toolbar .x-btn-menu-active .x-btn-tc, .x-toolbar .x-btn-pressed .x-btn-tc{
	background-position: 0 -12px;
}

.x-toolbar .x-btn-click .x-btn-ml, .x-toolbar .x-btn-menu-active .x-btn-ml, .x-toolbar .x-btn-pressed .x-btn-ml{
	background-position: -12px -24px;
}

.x-toolbar .x-btn-click .x-btn-mr, .x-toolbar .x-btn-menu-active .x-btn-mr, .x-toolbar .x-btn-pressed .x-btn-mr{
	background-position: -15px -24px;
}

.x-toolbar .x-btn-click .x-btn-mc, .x-toolbar .x-btn-menu-active .x-btn-mc, .x-toolbar .x-btn-pressed .x-btn-mc{
	background-position: 0 -3240px;
}

.x-toolbar .x-btn-click .x-btn-bl, .x-toolbar .x-btn-menu-active .x-btn-bl, .x-toolbar .x-btn-pressed .x-btn-bl{
	background-position: -12px -3px;
}

.x-toolbar .x-btn-click .x-btn-br, .x-toolbar .x-btn-menu-active .x-btn-br, .x-toolbar .x-btn-pressed .x-btn-br{
	background-position: -15px -3px;
}

.x-toolbar .x-btn-click .x-btn-bc, .x-toolbar .x-btn-menu-active .x-btn-bc, .x-toolbar .x-btn-pressed .x-btn-bc{
	background-position: 0 -21px;
}

.x-toolbar div.xtb-text{
    padding:2px 2px 0;
    line-height:16px;
    display:block;
}

.x-toolbar .xtb-sep {
	background-position: center;
	background-repeat: no-repeat;
	display: block;
	font-size: 1px;
	height: 16px;
	width:4px;
	overflow: hidden;
	cursor:default;
	margin: 0 2px 0;
	border:0;
}

.x-toolbar .xtb-spacer {
    width:2px;
}

/* Paging Toolbar */
.x-tbar-page-number{
	width:30px;
	height:14px;
}

.ext-ie .x-tbar-page-number{
    margin-top: 2px;
}

.x-paging-info {
    position:absolute;
    top:5px;
    right: 8px;
}

/* floating */
.x-toolbar-ct {
    width:100%;
}

.x-toolbar-right td {
    text-align: center;
}

.x-panel-tbar, .x-panel-bbar, .x-window-tbar, .x-window-bbar, .x-tab-panel-tbar, .x-tab-panel-bbar, .x-plain-tbar, .x-plain-bbar {
    overflow:hidden;
    zoom:1;
}

.x-toolbar-more .x-btn-small .x-btn-text{
	height: 16px;
	width: 12px;
}

.x-toolbar-more em.x-btn-arrow {
    display:inline;
    background:transparent;
	padding-right:0;
}

.x-toolbar-more .x-btn-mc em.x-btn-arrow {
    background-image: none;
}

div.x-toolbar-no-items {
    color:gray !important;
    padding:5px 10px !important;
}

/* fix ie toolbar form items */
.ext-border-box .x-toolbar-cell .x-form-text {
    margin-bottom:-1px !important;
}

.ext-border-box .x-toolbar-cell .x-form-field-wrap .x-form-text {
    margin:0 !important;
}

.ext-ie .x-toolbar-cell .x-form-field-wrap {
    height:21px;
}

.ext-ie .x-toolbar-cell .x-form-text {
    position:relative;
    top:-1px;
}

.ext-strict .ext-ie8 .x-toolbar-cell .x-form-field-trigger-wrap .x-form-text, .ext-strict .ext-ie .x-toolbar-cell .x-form-text {
    top: 0px;
}

.x-toolbar-right td .x-form-field-trigger-wrap{
    text-align: left;
}

.x-toolbar-cell .x-form-checkbox, .x-toolbar-cell .x-form-radio{
    margin-top: 5px;
}

.x-toolbar-cell .x-form-cb-label{
    vertical-align: bottom;
    top: 1px;
}

.ext-ie .x-toolbar-cell .x-form-checkbox, .ext-ie .x-toolbar-cell .x-form-radio{
    margin-top: 4px;
}

.ext-ie .x-toolbar-cell .x-form-cb-label{
    top: 0;
}
/* Grid3 styles */
.x-grid3 {
	position:relative;
	overflow:hidden;
}

.x-grid-panel .x-panel-body {
    overflow:hidden !important;
}

.x-grid-panel .x-panel-mc .x-panel-body {
    border:1px solid;
}

.x-grid3 table {
    table-layout:fixed;
}

.x-grid3-viewport{
	overflow:hidden;
}

.x-grid3-hd-row td, .x-grid3-row td, .x-grid3-summary-row td{
    -moz-outline: none;
	-moz-user-focus: normal;
}

.x-grid3-row td, .x-grid3-summary-row td {
    line-height:13px;
    vertical-align: top;
	padding-left:1px;
    padding-right:1px;
    -moz-user-select: none;
    -khtml-user-select:none;
    -webkit-user-select:ignore;
}

.x-grid3-cell{
    -moz-user-select: none;
    -khtml-user-select:none;
    -webkit-user-select:ignore;
}

.x-grid3-hd-row td {
    line-height:15px;
    vertical-align:middle;
    border-left:1px solid;
    border-right:1px solid;
}

.x-grid3-hd-row .x-grid3-marker-hd {
    padding:3px;
}

.x-grid3-row .x-grid3-marker {
    padding:3px;
}

.x-grid3-cell-inner, .x-grid3-hd-inner{
	overflow:hidden;
	-o-text-overflow: ellipsis;
	text-overflow: ellipsis;
    padding:3px 3px 3px 5px;
    white-space: nowrap;
}

.x-grid3-hd-inner {
    position:relative;
	cursor:inherit;
	padding:4px 3px 4px 5px;
}

.x-grid3-row-body {
    white-space:normal;
}

.x-grid3-body-cell {
    -moz-outline:0 none;
    outline:0 none;
}

/* IE Quirks to clip */
.ext-ie .x-grid3-cell-inner, .ext-ie .x-grid3-hd-inner{
	width:100%;
}

/* reverse above in strict mode */
.ext-strict .x-grid3-cell-inner, .ext-strict .x-grid3-hd-inner{
	width:auto;
}

.x-grid-row-loading {
    background: no-repeat center center;
}

.x-grid-page {
    overflow:hidden;
}

.x-grid3-row {
	cursor: default;
    border: 1px solid;
    width:100%;
}

.x-grid3-row-over {
	border:1px solid;
    background: repeat-x left top;
}

.x-grid3-resize-proxy {
	width:1px;
    left:0;
	cursor: e-resize;
	cursor: col-resize;
	position:absolute;
	top:0;
	height:100px;
	overflow:hidden;
	visibility:hidden;
	border:0 none;
	z-index:7;
}

.x-grid3-resize-marker {
	width:1px;
	left:0;
	position:absolute;
	top:0;
	height:100px;
	overflow:hidden;
	visibility:hidden;
	border:0 none;
	z-index:7;
}

.x-grid3-focus {
	position:absolute;
	left:0;
	top:0;
	width:1px;
	height:1px;
    line-height:1px;
    font-size:1px;
    -moz-outline:0 none;
    outline:0 none;
    -moz-user-select: text;
    -khtml-user-select: text;
    -webkit-user-select:ignore;
}

/* header styles */
.x-grid3-header{
	background: repeat-x 0 bottom;
	cursor:default;
    zoom:1;
    padding:1px 0 0 0;
}

.x-grid3-header-pop {
    border-left:1px solid;
    float:right;
    clear:none;
}

.x-grid3-header-pop-inner {
    border-left:1px solid;
    width:14px;
    height:19px;
    background: transparent no-repeat center center;
}

.ext-ie .x-grid3-header-pop-inner {
    width:15px;
}

.ext-strict .x-grid3-header-pop-inner {
    width:14px; 
}

.x-grid3-header-inner {
    overflow:hidden;
    zoom:1;
    float:left;
}

.x-grid3-header-offset {
    padding-left:1px;
    text-align: left;
}

td.x-grid3-hd-over, td.sort-desc, td.sort-asc, td.x-grid3-hd-menu-open {
    border-left:1px solid;
    border-right:1px solid;
}

td.x-grid3-hd-over .x-grid3-hd-inner, td.sort-desc .x-grid3-hd-inner, td.sort-asc .x-grid3-hd-inner, td.x-grid3-hd-menu-open .x-grid3-hd-inner {
    background: repeat-x left bottom;

}

.x-grid3-sort-icon{
	background-repeat: no-repeat;
	display: none;
	height: 4px;
	width: 13px;
	margin-left:3px;
	vertical-align: middle;
}

.sort-asc .x-grid3-sort-icon, .sort-desc .x-grid3-sort-icon {
	display: inline;
}

/* Header position fixes for IE strict mode */
.ext-strict .ext-ie .x-grid3-header-inner, .ext-strict .ext-ie6 .x-grid3-hd {
    position:relative;
}

.ext-strict .ext-ie6 .x-grid3-hd-inner{
    position:static;
}

/* Body Styles */
.x-grid3-body {
	zoom:1;
}

.x-grid3-scroller {
	overflow:auto;
    zoom:1;
    position:relative;
}

.x-grid3-cell-text, .x-grid3-hd-text {
	display: block;
	padding: 3px 5px 3px 5px;
	-moz-user-select: none;
	-khtml-user-select: none;
    -webkit-user-select:ignore;
}

.x-grid3-split {
	background-position: center;
	background-repeat: no-repeat;
	cursor: e-resize;
	cursor: col-resize;
	display: block;
	font-size: 1px;
	height: 16px;
	overflow: hidden;
	position: absolute;
	top: 2px;
	width: 6px;
	z-index: 3;
}

/* Column Reorder DD */
.x-dd-drag-proxy .x-grid3-hd-inner{
	background: repeat-x left bottom;
	width:120px;
	padding:3px;
	border:1px solid;
	overflow:hidden;
}

.col-move-top, .col-move-bottom{
	width:9px;
	height:9px;
	position:absolute;
	top:0;
	line-height:1px;
	font-size:1px;
	overflow:hidden;
	visibility:hidden;
	z-index:20000;
    background:transparent no-repeat left top;
}

/* Selection Styles */
.x-grid3-row-selected {
	border:1px dotted;
}

.x-grid3-locked td.x-grid3-row-marker, .x-grid3-locked .x-grid3-row-selected td.x-grid3-row-marker{
    background: repeat-x 0 bottom !important;
    vertical-align:middle !important;
    padding:0;
    border-top:1px solid;
    border-bottom:none !important;
    border-right:1px solid !important;
    text-align:center;
}

.x-grid3-locked td.x-grid3-row-marker div, .x-grid3-locked .x-grid3-row-selected td.x-grid3-row-marker div{
    padding:0 4px;
    text-align:center;
}

/* dirty cells */
.x-grid3-dirty-cell {
    background: transparent no-repeat 0 0;
}

/* Grid Toolbars */
.x-grid3-topbar, .x-grid3-bottombar{
    overflow:hidden;
	display:none;
	zoom:1;
    position:relative;
}

.x-grid3-topbar .x-toolbar{
	border-right:0 none;
}

.x-grid3-bottombar .x-toolbar{
	border-right:0 none;
	border-bottom:0 none;
	border-top:1px solid;
}

/* Props Grid Styles */
.x-props-grid .x-grid3-cell{
	padding:1px;
}

.x-props-grid .x-grid3-td-name .x-grid3-cell-inner{
	background:transparent repeat-y -16px !important;
    padding-left:12px;
}

.x-props-grid .x-grid3-body .x-grid3-td-name{
    padding:1px;
    padding-right:0;
    border:0 none;
    border-right:1px solid;
}

/* dd */
.x-grid3-col-dd {
    border:0 none;
    padding:0;
    background:transparent;
}

.x-dd-drag-ghost .x-grid3-dd-wrap {
    padding:1px 3px 3px 1px;
}

.x-grid3-hd {
    -moz-user-select:none;
    -khtml-user-select:none;
    -webkit-user-select:ignore;
}

.x-grid3-hd-btn {
    display:none;
    position:absolute;
    width:14px;
    background:no-repeat left center;
    right:0;
    top:0;
    z-index:2;
	cursor:pointer;
}

.x-grid3-hd-over .x-grid3-hd-btn, .x-grid3-hd-menu-open .x-grid3-hd-btn {
    display:block;
}

a.x-grid3-hd-btn:hover {
    background-position:-14px center;
}

/* Expanders */
.x-grid3-body .x-grid3-td-expander {
    background:transparent repeat-y right;
}

.x-grid3-body .x-grid3-td-expander .x-grid3-cell-inner {
    padding:0 !important;
    height:100%;
}

.x-grid3-row-expander {
    width:100%;
    height:18px;
    background-position:4px 2px;
    background-repeat:no-repeat;
    background-color:transparent;
}

.x-grid3-row-collapsed .x-grid3-row-expander {
    background-position:4px 2px;
}

.x-grid3-row-expanded .x-grid3-row-expander {
    background-position:-21px 2px;
}

.x-grid3-row-collapsed .x-grid3-row-body {
    display:none !important;
}

.x-grid3-row-expanded .x-grid3-row-body {
    display:block !important;
}

/* Checkers */
.x-grid3-body .x-grid3-td-checker {
    background:transparent repeat-y right;
}

.x-grid3-body .x-grid3-td-checker .x-grid3-cell-inner, .x-grid3-header .x-grid3-td-checker .x-grid3-hd-inner {
    padding:0 !important;
    height:100%;
}

.x-grid3-row-checker, .x-grid3-hd-checker {
    width:100%;
    height:18px;
    background-position:2px 2px;
    background-repeat:no-repeat;
    background-color:transparent;
}

.x-grid3-row .x-grid3-row-checker {
    background-position:2px 2px;
}

.x-grid3-row-selected .x-grid3-row-checker, .x-grid3-hd-checker-on .x-grid3-hd-checker,.x-grid3-row-checked .x-grid3-row-checker {
    background-position:-23px 2px;
}

.x-grid3-hd-checker {
    background-position:2px 3px;
}

.x-grid3-hd-checker-on .x-grid3-hd-checker {
    background-position:-23px 3px;
}

/* Numberer */
.x-grid3-body .x-grid3-td-numberer {
    background:transparent repeat-y right;
}

.x-grid3-body .x-grid3-td-numberer .x-grid3-cell-inner {
    padding:3px 5px 0 0 !important;
    text-align:right;
}

/* Row Icon */

.x-grid3-body .x-grid3-td-row-icon {
    background:transparent repeat-y right;
    vertical-align:top;
    text-align:center;
}

.x-grid3-body .x-grid3-td-row-icon .x-grid3-cell-inner {
    padding:0 !important;
    background-position:center center;
    background-repeat:no-repeat;
    width:16px;
    height:16px;
    margin-left:2px;
    margin-top:3px;
}

/* All specials */
.x-grid3-body .x-grid3-row-selected .x-grid3-td-numberer,
.x-grid3-body .x-grid3-row-selected .x-grid3-td-checker,
.x-grid3-body .x-grid3-row-selected .x-grid3-td-expander {
	background:transparent repeat-y right;
}

.x-grid3-body .x-grid3-check-col-td .x-grid3-cell-inner {
    padding: 1px 0 0 0 !important;
}

.x-grid3-check-col {
    width:100%;
    height:16px;
    background-position:center center;
    background-repeat:no-repeat;
    background-color:transparent;
}

.x-grid3-check-col-on {
    width:100%;
    height:16px;
    background-position:center center;
    background-repeat:no-repeat;
    background-color:transparent;
}

/* Grouping classes */
.x-grid-group, .x-grid-group-body, .x-grid-group-hd {
    zoom:1;
}

.x-grid-group-hd {
    border-bottom: 2px solid;
    cursor:pointer;
    padding-top:6px;
}

.x-grid-group-hd div.x-grid-group-title {
    background:transparent no-repeat 3px 3px;
    padding:4px 4px 4px 17px;
}

.x-grid-group-collapsed .x-grid-group-body {
    display:none;
}

.ext-ie6 .x-grid3 .x-editor .x-form-text, .ext-ie7 .x-grid3 .x-editor .x-form-text {
    position:relative;
    top:-1px;
}

.ext-ie .x-props-grid .x-editor .x-form-text {
    position:static;
    top:0;
}

.x-grid-empty {
    padding:10px;
}

/* fix floating toolbar issue */
.ext-ie7 .x-grid-panel .x-panel-bbar {
    position:relative;
}


/* Reset position to static when Grid Panel has been framed */
/* to resolve 'snapping' from top to bottom behavior. */
/* @forumThread 86656 */
.ext-ie7 .x-grid-panel .x-panel-mc .x-panel-bbar {
    position: static;
}


.ext-ie6 .x-grid3-header {
    position: relative;
}

/* column lines */
.x-grid-with-col-lines .x-grid3-row td.x-grid3-cell {
    padding-right:0;
    border-right:1px solid;
}
.x-dd-drag-proxy{
	position:absolute;
	left:0;
    top:0;
	visibility:hidden;
	z-index:15000;
}

.x-dd-drag-ghost{
	-moz-opacity: 0.85;
    opacity:.85;
    filter: alpha(opacity=85);
    border: 1px solid;
	padding:3px;
	padding-left:20px;
	white-space:nowrap;
}

.x-dd-drag-repair .x-dd-drag-ghost{
	-moz-opacity: 0.4;
    opacity:.4;
    filter: alpha(opacity=40);
	border:0 none;
	padding:0;
	background-color:transparent;
}

.x-dd-drag-repair .x-dd-drop-icon{
	visibility:hidden;
}

.x-dd-drop-icon{
    position:absolute;
	top:3px;
	left:3px;
	display:block;
	width:16px;
	height:16px;
	background-color:transparent;
	background-position: center;
	background-repeat: no-repeat;
	z-index:1;
}

.x-view-selector {
    position:absolute;
    left:0;
    top:0;
    width:0;
    border:1px dotted;
	opacity: .5;
    -moz-opacity: .5;
    filter:alpha(opacity=50);
    zoom:1;
}.ext-strict .ext-ie .x-tree .x-panel-bwrap{
    position:relative;
    overflow:hidden;
}

.x-tree-icon, .x-tree-ec-icon, .x-tree-elbow-line, .x-tree-elbow, .x-tree-elbow-end, .x-tree-elbow-plus, .x-tree-elbow-minus, .x-tree-elbow-end-plus, .x-tree-elbow-end-minus{
	border: 0 none;
	height: 18px;
	margin: 0;
	padding: 0;
	vertical-align: top;
	width: 16px;
    background-repeat: no-repeat;
}

.x-tree-node-collapsed .x-tree-node-icon, .x-tree-node-expanded .x-tree-node-icon, .x-tree-node-leaf .x-tree-node-icon{
	border: 0 none;
	height: 18px;
	margin: 0;
	padding: 0;
	vertical-align: top;
	width: 16px;
	background-position:center;
    background-repeat: no-repeat;
}

.ext-ie .x-tree-node-indent img, .ext-ie .x-tree-node-icon, .ext-ie .x-tree-ec-icon {
    vertical-align: middle !important;
}

.ext-strict .ext-ie8 .x-tree-node-indent img, .ext-strict .ext-ie8 .x-tree-node-icon, .ext-strict .ext-ie8 .x-tree-ec-icon {
    vertical-align: top !important;
}

/* checkboxes */

input.x-tree-node-cb {
    margin-left:1px;
    height: 19px;
	vertical-align: bottom;
}

.ext-ie input.x-tree-node-cb {
    margin-left:0;
    margin-top: 1px;
    width: 16px;
    height: 16px;
    vertical-align: middle;
}

.ext-strict .ext-ie8 input.x-tree-node-cb{
    margin: 1px 1px;
    height: 14px;
    vertical-align: bottom;
}

.ext-strict .ext-ie8 input.x-tree-node-cb + a{
    vertical-align: bottom;
}

.ext-opera input.x-tree-node-cb {
    height: 14px;
    vertical-align: middle;
}

.x-tree-noicon .x-tree-node-icon{
	width:0; height:0;
}

/* No line styles */
.x-tree-no-lines .x-tree-elbow{
	background:transparent;
}

.x-tree-no-lines .x-tree-elbow-end{
	background:transparent;
}

.x-tree-no-lines .x-tree-elbow-line{
	background:transparent;
}

/* Arrows */
.x-tree-arrows .x-tree-elbow{
	background:transparent;
}

.x-tree-arrows .x-tree-elbow-plus{
    background:transparent no-repeat 0 0;
}

.x-tree-arrows .x-tree-elbow-minus{
    background:transparent no-repeat -16px 0;
}

.x-tree-arrows .x-tree-elbow-end{
	background:transparent;
}

.x-tree-arrows .x-tree-elbow-end-plus{
    background:transparent no-repeat 0 0;
}

.x-tree-arrows .x-tree-elbow-end-minus{
    background:transparent no-repeat -16px 0;
}

.x-tree-arrows .x-tree-elbow-line{
	background:transparent;
}

.x-tree-arrows .x-tree-ec-over .x-tree-elbow-plus{
    background-position:-32px 0;
}

.x-tree-arrows .x-tree-ec-over .x-tree-elbow-minus{
    background-position:-48px 0;
}

.x-tree-arrows .x-tree-ec-over .x-tree-elbow-end-plus{
    background-position:-32px 0;
}

.x-tree-arrows .x-tree-ec-over .x-tree-elbow-end-minus{
    background-position:-48px 0;
}

.x-tree-elbow-plus, .x-tree-elbow-minus, .x-tree-elbow-end-plus, .x-tree-elbow-end-minus{
	cursor:pointer;
}

.ext-ie ul.x-tree-node-ct{
    font-size:0;
    line-height:0;
    zoom:1;
}

.x-tree-node{
	white-space: nowrap;
}

.x-tree-node-el {
    line-height:18px;
    cursor:pointer;
}

.x-tree-node a, .x-dd-drag-ghost a{
	text-decoration:none;
	-khtml-user-select:none;
	-moz-user-select:none;
    -webkit-user-select:ignore;
    -kthml-user-focus:normal;
    -moz-user-focus:normal;
    -moz-outline: 0 none;
    outline:0 none;
}

.x-tree-node a span, .x-dd-drag-ghost a span{
	text-decoration:none;
	padding:1px 3px 1px 2px;
}

.x-tree-node .x-tree-node-disabled .x-tree-node-icon{
	-moz-opacity: 0.5;
   opacity:.5;
   filter: alpha(opacity=50);
}

.x-tree-node .x-tree-node-inline-icon{
	background:transparent;
}

.x-tree-node a:hover, .x-dd-drag-ghost a:hover{
	text-decoration:none;
}

.x-tree-node div.x-tree-drag-insert-below{
 	 border-bottom:1px dotted;
}

.x-tree-node div.x-tree-drag-insert-above{
	 border-top:1px dotted;
}

.x-tree-dd-underline .x-tree-node div.x-tree-drag-insert-below{
 	 border-bottom:0 none;
}

.x-tree-dd-underline .x-tree-node div.x-tree-drag-insert-above{
	 border-top:0 none;
}

.x-tree-dd-underline .x-tree-node div.x-tree-drag-insert-below a{
 	 border-bottom:2px solid;
}

.x-tree-dd-underline .x-tree-node div.x-tree-drag-insert-above a{
	 border-top:2px solid;
}

.x-tree-node .x-tree-drag-append a span{
	 border:1px dotted;
}

.x-dd-drag-ghost .x-tree-node-indent, .x-dd-drag-ghost .x-tree-ec-icon{
	display:none !important;
}

/* Fix for ie rootVisible:false issue */
.x-tree-root-ct {
    zoom:1;
}
.x-date-picker {
    border: 1px solid;
    border-top:0 none;
	position:relative;
}

.x-date-picker a {
    -moz-outline:0 none;
    outline:0 none;
}

.x-date-inner, .x-date-inner td, .x-date-inner th{
    border-collapse:separate;
}

.x-date-middle,.x-date-left,.x-date-right {
	background: repeat-x 0 -83px;
	overflow:hidden;
}

.x-date-middle .x-btn-tc,.x-date-middle .x-btn-tl,.x-date-middle .x-btn-tr,
.x-date-middle .x-btn-mc,.x-date-middle .x-btn-ml,.x-date-middle .x-btn-mr,
.x-date-middle .x-btn-bc,.x-date-middle .x-btn-bl,.x-date-middle .x-btn-br{
	background:transparent !important;
    vertical-align:middle;
}

.x-date-middle .x-btn-mc em.x-btn-arrow {
    background:transparent no-repeat right 0;
}

.x-date-right, .x-date-left {
    width:18px;
}

.x-date-right{
    text-align:right;
}

.x-date-middle {
    padding-top:2px;
    padding-bottom:2px;
    width:130px; /* FF3 */
}

.x-date-right a, .x-date-left a{
    display:block;
    width:16px;
	height:16px;
	background-position: center;
	background-repeat: no-repeat;
	cursor:pointer;
    -moz-opacity: 0.6;
    opacity:.6;
    filter: alpha(opacity=60);
}

.x-date-right a:hover, .x-date-left a:hover{
    -moz-opacity: 1;
    opacity:1;
    filter: alpha(opacity=100);
}

.x-item-disabled .x-date-right a:hover, .x-item-disabled .x-date-left a:hover{
    -moz-opacity: 0.6;
    opacity:.6;
    filter: alpha(opacity=60);
}

.x-date-right a {
    margin-right:2px;
    text-decoration:none !important;
}

.x-date-left a{
    margin-left:2px;
    text-decoration:none !important;
}

table.x-date-inner {
    width: 100%;
    table-layout:fixed;
}

.ext-webkit table.x-date-inner{
    /* Fix for webkit browsers */
    width: 175px;
}


.x-date-inner th {
    width:25px;
}

.x-date-inner th {
    background: repeat-x left top;
    text-align:right !important;
	border-bottom: 1px solid;
	cursor:default;
    padding:0;
    border-collapse:separate;
}

.x-date-inner th span {
    display:block;
    padding:2px;
    padding-right:7px;
}

.x-date-inner td {
    border: 1px solid;
	text-align:right;
    padding:0;
}

.x-date-inner a {
    padding:2px 5px;
    display:block;
	text-decoration:none;
    text-align:right;
    zoom:1;
}

.x-date-inner .x-date-active{
	cursor:pointer;
	color:black;
}

.x-date-inner .x-date-selected a{
	background: repeat-x left top;
	border:1px solid;
    padding:1px 4px;
}

.x-date-inner .x-date-today a{
	border: 1px solid;
    padding:1px 4px;
}

.x-date-inner .x-date-prevday a,.x-date-inner .x-date-nextday a {
    text-decoration:none !important;
}

.x-date-bottom {
    padding:4px;
    border-top: 1px solid;
    background: repeat-x left top;
}

.x-date-inner a:hover, .x-date-inner .x-date-disabled a:hover{
    text-decoration:none !important;
}

.x-item-disabled .x-date-inner a:hover{
    background: none;
}

.x-date-inner .x-date-disabled a {
	cursor:default;
}

.x-date-menu .x-menu-item {
	padding:1px 24px 1px 4px;
	white-space: nowrap;
}

.x-date-menu .x-menu-item .x-menu-item-icon {
    width:10px;
    height:10px;
    margin-right:5px;
    background-position:center -4px !important;
}

.x-date-mp {
	position:absolute;
	left:0;
	top:0;
	display:none;
}

.x-date-mp td {
    padding:2px;
	font:normal 11px arial, helvetica,tahoma,sans-serif;
}

td.x-date-mp-month,td.x-date-mp-year,td.x-date-mp-ybtn {
    border: 0 none;
	text-align:center;
	vertical-align: middle;
	width:25%;
}

.x-date-mp-ok {
	margin-right:3px;
}

.x-date-mp-btns button {
	text-decoration:none;
	text-align:center;
	text-decoration:none !important;
	border:1px solid;
	padding:1px 3px 1px;
	cursor:pointer;
}

.x-date-mp-btns {
	background: repeat-x left top;
}

.x-date-mp-btns td {
	border-top: 1px solid;
    text-align:center;
}

td.x-date-mp-month a,td.x-date-mp-year a {
	display:block;
	padding:2px 4px;
	text-decoration:none;
	text-align:center;
}

td.x-date-mp-month a:hover,td.x-date-mp-year a:hover {
	text-decoration:none;
	cursor:pointer;
}

td.x-date-mp-sel a {
	padding:1px 3px;
	background: repeat-x left top;
	border:1px solid;
}

.x-date-mp-ybtn a {
    overflow:hidden;
    width:15px;
    height:15px;
    cursor:pointer;
    background:transparent no-repeat;
    display:block;
    margin:0 auto;
}

.x-date-mp-ybtn a.x-date-mp-next {
    background-position:0 -120px;
}

.x-date-mp-ybtn a.x-date-mp-next:hover {
    background-position:-15px -120px;
}

.x-date-mp-ybtn a.x-date-mp-prev {
    background-position:0 -105px;
}

.x-date-mp-ybtn a.x-date-mp-prev:hover {
    background-position:-15px -105px;
}

.x-date-mp-ybtn {
   text-align:center;
}

td.x-date-mp-sep {
   border-right:1px solid;
}.x-tip{
	position: absolute;
	top: 0;
    left:0;
    visibility: hidden;
	z-index: 20000;
    border:0 none;
}

.x-tip .x-tip-close{
	height: 15px;
	float:right;
	width: 15px;
    margin:0 0 2px 2px;
    cursor:pointer;
    display:none;
}

.x-tip .x-tip-tc {
	background: transparent no-repeat 0 -62px;
	padding-top:3px;
    overflow:hidden;
    zoom:1;
}

.x-tip .x-tip-tl {
	background: transparent no-repeat 0 0;
	padding-left:6px;
    overflow:hidden;
    zoom:1;
}

.x-tip .x-tip-tr {
	background: transparent no-repeat right 0;
	padding-right:6px;
    overflow:hidden;
    zoom:1;
}

.x-tip .x-tip-bc {
	background: transparent no-repeat 0 -121px;
	height:3px;
    overflow:hidden;
}

.x-tip .x-tip-bl {
	background: transparent no-repeat 0 -59px;
	padding-left:6px;
    zoom:1;
}

.x-tip .x-tip-br {
	background: transparent no-repeat right -59px;
	padding-right:6px;
    zoom:1;
}

.x-tip .x-tip-mc {
    border:0 none;
}

.x-tip .x-tip-ml {
	background: no-repeat 0 -124px;
	padding-left:6px;
    zoom:1;
}

.x-tip .x-tip-mr {
	background: transparent no-repeat right -124px;
	padding-right:6px;
    zoom:1;
}

.ext-ie .x-tip .x-tip-header,.ext-ie .x-tip .x-tip-tc {
    font-size:0;
    line-height:0;
}

.ext-border-box .x-tip .x-tip-header, .ext-border-box .x-tip .x-tip-tc{
    line-height: 1px;
}

.x-tip .x-tip-header-text {
    padding:0;
    margin:0 0 2px 0;
}

.x-tip .x-tip-body {
    margin:0 !important;
    line-height:14px;
    padding:0;
}

.x-tip .x-tip-body .loading-indicator {
    margin:0;
}

.x-tip-draggable .x-tip-header,.x-tip-draggable .x-tip-header-text {
    cursor:move;
}

.x-form-invalid-tip .x-tip-tc {
	background: repeat-x 0 -12px;
    padding-top:6px;
}

.x-form-invalid-tip .x-tip-bc {
	background: repeat-x 0 -18px;
    height:6px;
}

.x-form-invalid-tip .x-tip-bl {
	background: no-repeat 0 -6px;
}

.x-form-invalid-tip .x-tip-br {
	background: no-repeat right -6px;
}

.x-form-invalid-tip .x-tip-body {
    padding:2px;
}

.x-form-invalid-tip .x-tip-body {
    padding-left:24px;
    background:transparent no-repeat 2px 2px;
}

.x-tip-anchor {
    position: absolute;
    width: 9px;
    height: 10px;
    overflow:hidden;
    background: transparent no-repeat 0 0;
    zoom:1;
}
.x-tip-anchor-bottom {
    background-position: -9px 0;
}
.x-tip-anchor-right {
    background-position: -18px 0;
    width: 10px;
}
.x-tip-anchor-left {
    background-position: -28px 0;
    width: 10px;
}.x-menu {
	z-index: 15000;
	zoom: 1;
	background: repeat-y;
}

.x-menu-floating{
    border: 1px solid;
}

.x-menu a {
    text-decoration: none !important;
}

.ext-ie .x-menu {
    zoom:1;
    overflow:hidden;
}

.x-menu-list{
    padding: 2px;
	background:transparent;
	border:0 none;
    overflow:hidden;
    overflow-y: hidden;
}

.ext-strict .ext-ie .x-menu-list{
    position: relative;
}

.x-menu li{
	line-height:100%;
}

.x-menu li.x-menu-sep-li{
	font-size:1px;
	line-height:1px;
}

.x-menu-list-item{
    white-space: nowrap;
	display:block;
	padding:1px;
}

.x-menu-item{
    -moz-user-select: none;
    -khtml-user-select:none;
    -webkit-user-select:ignore;
}

.x-menu-item-arrow{
	background:transparent no-repeat right;
}

.x-menu-sep {
	display:block;
	font-size:1px;
	line-height:1px;
	margin: 2px 3px;
	border-bottom:1px solid;
    overflow:hidden;
}

.x-menu-focus {
	position:absolute;
	left:-1px;
	top:-1px;
	width:1px;
	height:1px;
    line-height:1px;
    font-size:1px;
    -moz-outline:0 none;
    outline:0 none;
    -moz-user-select: none;
    -khtml-user-select:none;
    -webkit-user-select:ignore;
    overflow:hidden;
    display:block;
}

a.x-menu-item {
    cursor: pointer;
    display: block;
    line-height: 16px;
    outline-color: -moz-use-text-color;
    outline-style: none;
    outline-width: 0;
    padding: 3px 21px 3px 27px;
    position: relative;
    text-decoration: none;
    white-space: nowrap;
}

.x-menu-item-active {
    background-repeat: repeat-x;
    background-position: left bottom;
    border-style:solid;
    border-width: 1px 0;
    margin:0 1px;
	padding: 0;
}

.x-menu-item-active a.x-menu-item {
    border-style:solid;
    border-width:0 1px;
    margin:0 -1px;
}

.x-menu-item-icon {
	border: 0 none;
	height: 16px;
	padding: 0;
	vertical-align: top;
	width: 16px;
	position: absolute;
    left: 3px;
    top: 3px;
    margin: 0;
    background-position:center;
}

.ext-ie .x-menu-item-icon {
    left: -24px;
}
.ext-strict .x-menu-item-icon {
    left: 3px;
}

.ext-ie6 .x-menu-item-icon {
    left: -24px;
}

.ext-ie .x-menu-item-icon {
    vertical-align: middle;
}

.x-menu-check-item .x-menu-item-icon{
	background: transparent no-repeat center;
}

.x-menu-group-item .x-menu-item-icon{
	background: transparent;
}

.x-menu-item-checked .x-menu-group-item .x-menu-item-icon{
    background: transparent no-repeat center;
}

.x-date-menu .x-menu-list{
    padding: 0;
}

.x-menu-date-item{
	padding:0;
}

.x-menu .x-color-palette, .x-menu .x-date-picker{
    margin-left: 26px;
	margin-right:4px;
}

.x-menu .x-date-picker{
    border:1px solid;
    margin-top:2px;
    margin-bottom:2px;
}

.x-menu-plain .x-color-palette, .x-menu-plain .x-date-picker{
	 margin: 0;
	 border: 0 none;
}

.x-date-menu {
   padding:0 !important;
}

/*
 * Ugly mess to remove the white border under the picker
 */
.ext-ie .x-date-menu{
    height: 199px;
}

.ext-strict .ext-ie .x-date-menu, .ext-border-box .ext-ie8 .x-date-menu{
    height: 197px;
}

.ext-strict .ext-ie7 .x-date-menu{
    height: 195px;
}

.ext-strict .ext-ie8 .x-date-menu{
    height: auto;
}

.x-cycle-menu .x-menu-item-checked {
    border:1px dotted !important;
	padding:0;
}

.x-menu .x-menu-scroller {
    width: 100%;
	background-repeat:no-repeat;
	background-position:center;
	height:8px;
    line-height: 8px;
	cursor:pointer;
    margin: 0;
    padding: 0;
}

.x-menu .x-menu-scroller-active{
    height: 6px;
    line-height: 6px;
}

.x-menu-list-item-indent{
    padding-left: 27px;
}
/*
 Creates rounded, raised boxes like on the Ext website - the markup isn't pretty:
  <div class="x-box-blue">
        <div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>
        <div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc">
            <h3>YOUR TITLE HERE (optional)</h3>
            <div>YOUR CONTENT HERE</div>
        </div></div></div>
        <div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>
    </div>
 */

.x-box-tl {
	background: transparent no-repeat 0 0;
    zoom:1;
}

.x-box-tc {
	height: 8px;
	background: transparent repeat-x 0 0;
	overflow: hidden;
}

.x-box-tr {
	background: transparent no-repeat right -8px;
}

.x-box-ml {
	background: transparent repeat-y 0;
	padding-left: 4px;
	overflow: hidden;
    zoom:1;
}

.x-box-mc {
	background: repeat-x 0 -16px;
	padding: 4px 10px;
}

.x-box-mc h3 {
	margin: 0 0 4px 0;
    zoom:1;
}

.x-box-mr {
	background: transparent repeat-y right;
	padding-right: 4px;
	overflow: hidden;
}

.x-box-bl {
	background: transparent no-repeat 0 -16px;
    zoom:1;
}

.x-box-bc {
	background: transparent repeat-x 0 -8px;
	height: 8px;
	overflow: hidden;
}

.x-box-br {
	background: transparent no-repeat right -24px;
}

.x-box-tl, .x-box-bl {
	padding-left: 8px;
	overflow: hidden;
}

.x-box-tr, .x-box-br {
	padding-right: 8px;
	overflow: hidden;
}.x-combo-list {
    border:1px solid;
    zoom:1;
    overflow:hidden;
}

.x-combo-list-inner {
    overflow:auto;
    position:relative; /* for calculating scroll offsets */
    zoom:1;
    overflow-x:hidden;
}

.x-combo-list-hd {
    border-bottom:1px solid;
    padding:3px;
}

.x-resizable-pinned .x-combo-list-inner {
    border-bottom:1px solid;
}

.x-combo-list-item {
    padding:2px;
    border:1px solid;
    white-space: nowrap;
    overflow:hidden;
    text-overflow: ellipsis;
}

.x-combo-list .x-combo-selected{
	border:1px dotted !important;
    cursor:pointer;
}

.x-combo-list .x-toolbar {
    border-top:1px solid;
    border-bottom:0 none;
}.x-panel {
    border-style: solid;
    border-width:0;
}

.x-panel-header {
    overflow:hidden;
    zoom:1;
    padding:5px 3px 4px 5px;
    border:1px solid;
    line-height: 15px;
    background: transparent repeat-x 0 -1px;
}

.x-panel-body {
    border:1px solid;
    border-top:0 none;
    overflow:hidden;
    position: relative; /* added for item scroll positioning */
}

.x-panel-bbar .x-toolbar, .x-panel-tbar .x-toolbar {
    border:1px solid;
    border-top:0 none;
    overflow:hidden;
    padding:2px;
}

.x-panel-tbar-noheader .x-toolbar, .x-panel-mc .x-panel-tbar .x-toolbar {
    border-top:1px solid;
    border-bottom: 0 none;
}

.x-panel-body-noheader, .x-panel-mc .x-panel-body {
    border-top:1px solid;
}

.x-panel-header {
    overflow:hidden;
    zoom:1;
}

.x-panel-tl .x-panel-header {
    padding:5px 0 4px 0;
    border:0 none;
    background:transparent;
}

.x-panel-tl .x-panel-icon, .x-window-tl .x-panel-icon {
    padding-left:20px !important;
    background-repeat:no-repeat;
    background-position:0 4px;
    zoom:1;
}

.x-panel-inline-icon {
    width:16px;
	height:16px;
    background-repeat:no-repeat;
    background-position:0 0;
	vertical-align:middle;
	margin-right:4px;
	margin-top:-1px;
	margin-bottom:-1px;
}

.x-panel-tc {
	background: transparent repeat-x 0 0;
	overflow:hidden;
}

/* fix ie7 strict mode bug */
.ext-strict .ext-ie7 .x-panel-tc {
    overflow: visible;
}

.x-panel-tl {
	background: transparent no-repeat 0 0;
	padding-left:6px;
    zoom:1;
    border-bottom:1px solid;
}

.x-panel-tr {
	background: transparent no-repeat right 0;
	zoom:1;
    padding-right:6px;
}

.x-panel-bc {
	background: transparent repeat-x 0 bottom;
    zoom:1;
}

.x-panel-bc .x-panel-footer {
    zoom:1;
}

.x-panel-bl {
	background: transparent no-repeat 0 bottom;
	padding-left:6px;
    zoom:1;
}

.x-panel-br {
	background: transparent no-repeat right bottom;
	padding-right:6px;
    zoom:1;
}

.x-panel-mc {
    border:0 none;
    padding:0;
    margin:0;
    padding-top:6px;
}

.x-panel-mc .x-panel-body {
    background:transparent;
    border: 0 none;
}

.x-panel-ml {
	background: repeat-y 0 0;
	padding-left:6px;
    zoom:1;
}

.x-panel-mr {
	background: transparent repeat-y right 0;
	padding-right:6px;
    zoom:1;
}

.x-panel-bc .x-panel-footer {
    padding-bottom:6px;
}

.x-panel-nofooter .x-panel-bc, .x-panel-nofooter .x-window-bc {
	height:6px;
    font-size:0;
    line-height:0;
}

.x-panel-bwrap {
    overflow:hidden;
    zoom:1;
    left:0;
    top:0;
}
.x-panel-body {
    overflow:hidden;
    zoom:1;
}

.x-panel-collapsed .x-resizable-handle{
    display:none;
}

.ext-gecko .x-panel-animated div {
    overflow:hidden !important;
}

/* Plain */
.x-plain-body {
    overflow:hidden;
}

.x-plain-bbar .x-toolbar {
    overflow:hidden;
    padding:2px;
}

.x-plain-tbar .x-toolbar {
    overflow:hidden;
    padding:2px;
}

.x-plain-bwrap {
    overflow:hidden;
    zoom:1;
}

.x-plain {
    overflow:hidden;
}

/* Tools */
.x-tool {
    overflow:hidden;
    width:15px;
    height:15px;
    float:right;
    cursor:pointer;
    background:transparent no-repeat;
    margin-left:2px;
}

/* expand / collapse tools */
.x-tool-toggle {
    background-position:0 -60px;
}

.x-tool-toggle-over {
    background-position:-15px -60px;
}

.x-panel-collapsed .x-tool-toggle {
    background-position:0 -75px;
}

.x-panel-collapsed .x-tool-toggle-over {
    background-position:-15px -75px;
}


.x-tool-close {
    background-position:0 -0;
}

.x-tool-close-over {
    background-position:-15px 0;
}

.x-tool-minimize {
    background-position:0 -15px;
}

.x-tool-minimize-over {
    background-position:-15px -15px;
}

.x-tool-maximize {
    background-position:0 -30px;
}

.x-tool-maximize-over {
    background-position:-15px -30px;
}

.x-tool-restore {
    background-position:0 -45px;
}

.x-tool-restore-over {
    background-position:-15px -45px;
}

.x-tool-gear {
    background-position:0 -90px;
}

.x-tool-gear-over {
    background-position:-15px -90px;
}

.x-tool-pin {
    background-position:0 -135px;
}

.x-tool-pin-over {
    background-position:-15px -135px;
}

.x-tool-unpin {
    background-position:0 -150px;
}

.x-tool-unpin-over {
    background-position:-15px -150px;
}

.x-tool-right {
    background-position:0 -165px;
}

.x-tool-right-over {
    background-position:-15px -165px;
}

.x-tool-left {
    background-position:0 -180px;
}

.x-tool-left-over {
    background-position:-15px -180px;
}

.x-tool-up {
    background-position:0 -210px;
}

.x-tool-up-over {
    background-position:-15px -210px;
}

.x-tool-down {
    background-position:0 -195px;
}

.x-tool-down-over {
    background-position:-15px -195px;
}

.x-tool-refresh {
    background-position:0 -225px;
}

.x-tool-refresh-over {
    background-position:-15px -225px;
}

.x-tool-minus {
    background-position:0 -255px;
}

.x-tool-minus-over {
    background-position:-15px -255px;
}

.x-tool-plus {
    background-position:0 -240px;
}

.x-tool-plus-over {
    background-position:-15px -240px;
}

.x-tool-search {
    background-position:0 -270px;
}

.x-tool-search-over {
    background-position:-15px -270px;
}

.x-tool-save {
    background-position:0 -285px;
}

.x-tool-save-over {
    background-position:-15px -285px;
}

.x-tool-help {
    background-position:0 -300px;
}

.x-tool-help-over {
    background-position:-15px -300px;
}

.x-tool-print {
    background-position:0 -315px;
}

.x-tool-print-over {
    background-position:-15px -315px;
}

/* Ghosting */
.x-panel-ghost {
    z-index:12000;
    overflow:hidden;
    position:absolute;
    left:0;top:0;
    opacity:.65;
    -moz-opacity:.65;
    filter:alpha(opacity=65);
}

.x-panel-ghost ul {
    margin:0;
    padding:0;
    overflow:hidden;
    font-size:0;
    line-height:0;
    border:1px solid;
    border-top:0 none;
    display:block;
}

.x-panel-ghost * {
    cursor:move !important;
}

.x-panel-dd-spacer {
    border:2px dashed;
}

/* Buttons */
.x-panel-btns {
    padding:5px;
    overflow:hidden;
}

.x-panel-btns td.x-toolbar-cell{
	padding:3px;
}

.x-panel-btns .x-btn-focus .x-btn-left{
	background-position:0 -147px;
}

.x-panel-btns .x-btn-focus .x-btn-right{
	background-position:0 -168px;
}

.x-panel-btns .x-btn-focus .x-btn-center{
	background-position:0 -189px;
}

.x-panel-btns .x-btn-over .x-btn-left{
	background-position:0 -63px;
}

.x-panel-btns .x-btn-over .x-btn-right{
	background-position:0 -84px;
}

.x-panel-btns .x-btn-over .x-btn-center{
	background-position:0 -105px;
}

.x-panel-btns .x-btn-click .x-btn-center{
	background-position:0 -126px;
}

.x-panel-btns .x-btn-click  .x-btn-right{
	background-position:0 -84px;
}

.x-panel-btns .x-btn-click .x-btn-left{
	background-position:0 -63px;
}

.x-panel-fbar td,.x-panel-fbar span,.x-panel-fbar input,.x-panel-fbar div,.x-panel-fbar select,.x-panel-fbar label{
	white-space: nowrap;
}
/**
 * W3C Suggested Default style sheet for HTML 4
 * http://www.w3.org/TR/CSS21/sample.html
 *
 * Resets for Ext.Panel @cfg normal: true
 */
.x-panel-reset .x-panel-body html,
.x-panel-reset .x-panel-body address,
.x-panel-reset .x-panel-body blockquote,
.x-panel-reset .x-panel-body body,
.x-panel-reset .x-panel-body dd,
.x-panel-reset .x-panel-body div,
.x-panel-reset .x-panel-body dl,
.x-panel-reset .x-panel-body dt,
.x-panel-reset .x-panel-body fieldset,
.x-panel-reset .x-panel-body form,
.x-panel-reset .x-panel-body frame, frameset,
.x-panel-reset .x-panel-body h1,
.x-panel-reset .x-panel-body h2,
.x-panel-reset .x-panel-body h3,
.x-panel-reset .x-panel-body h4,
.x-panel-reset .x-panel-body h5,
.x-panel-reset .x-panel-body h6,
.x-panel-reset .x-panel-body noframes,
.x-panel-reset .x-panel-body ol,
.x-panel-reset .x-panel-body p,
.x-panel-reset .x-panel-body ul,
.x-panel-reset .x-panel-body center,
.x-panel-reset .x-panel-body dir,
.x-panel-reset .x-panel-body hr,
.x-panel-reset .x-panel-body menu,
.x-panel-reset .x-panel-body pre 			  { display: block }
.x-panel-reset .x-panel-body li              { display: list-item }
.x-panel-reset .x-panel-body head            { display: none }
.x-panel-reset .x-panel-body table           { display: table }
.x-panel-reset .x-panel-body tr              { display: table-row }
.x-panel-reset .x-panel-body thead           { display: table-header-group }
.x-panel-reset .x-panel-body tbody           { display: table-row-group }
.x-panel-reset .x-panel-body tfoot           { display: table-footer-group }
.x-panel-reset .x-panel-body col             { display: table-column }
.x-panel-reset .x-panel-body colgroup        { display: table-column-group }
.x-panel-reset .x-panel-body td,
.x-panel-reset .x-panel-body th 	          { display: table-cell }
.x-panel-reset .x-panel-body caption         { display: table-caption }
.x-panel-reset .x-panel-body th              { font-weight: bolder; text-align: center }
.x-panel-reset .x-panel-body caption         { text-align: center }
.x-panel-reset .x-panel-body body            { margin: 8px }
.x-panel-reset .x-panel-body h1              { font-size: 2em; margin: .67em 0 }
.x-panel-reset .x-panel-body h2              { font-size: 1.5em; margin: .75em 0 }
.x-panel-reset .x-panel-body h3              { font-size: 1.17em; margin: .83em 0 }
.x-panel-reset .x-panel-body h4,
.x-panel-reset .x-panel-body p,
.x-panel-reset .x-panel-body blockquote,
.x-panel-reset .x-panel-body ul,
.x-panel-reset .x-panel-body fieldset,
.x-panel-reset .x-panel-body form,
.x-panel-reset .x-panel-body ol,
.x-panel-reset .x-panel-body dl,
.x-panel-reset .x-panel-body dir,
.x-panel-reset .x-panel-body menu            { margin: 1.12em 0 }
.x-panel-reset .x-panel-body h5              { font-size: .83em; margin: 1.5em 0 }
.x-panel-reset .x-panel-body h6              { font-size: .75em; margin: 1.67em 0 }
.x-panel-reset .x-panel-body h1,
.x-panel-reset .x-panel-body h2,
.x-panel-reset .x-panel-body h3,
.x-panel-reset .x-panel-body h4,
.x-panel-reset .x-panel-body h5,
.x-panel-reset .x-panel-body h6,
.x-panel-reset .x-panel-body b,
.x-panel-reset .x-panel-body strong          { font-weight: bolder }
.x-panel-reset .x-panel-body blockquote      { margin-left: 40px; margin-right: 40px }
.x-panel-reset .x-panel-body i,
.x-panel-reset .x-panel-body cite,
.x-panel-reset .x-panel-body em,
.x-panel-reset .x-panel-body var,
.x-panel-reset .x-panel-body address    	  { font-style: italic }
.x-panel-reset .x-panel-body pre,
.x-panel-reset .x-panel-body tt,
.x-panel-reset .x-panel-body code,
.x-panel-reset .x-panel-body kbd,
.x-panel-reset .x-panel-body samp       	  { font-family: monospace }
.x-panel-reset .x-panel-body pre             { white-space: pre }
.x-panel-reset .x-panel-body button,
.x-panel-reset .x-panel-body textarea,
.x-panel-reset .x-panel-body input,
.x-panel-reset .x-panel-body select   		  { display: inline-block }
.x-panel-reset .x-panel-body big             { font-size: 1.17em }
.x-panel-reset .x-panel-body small,
.x-panel-reset .x-panel-body sub,
.x-panel-reset .x-panel-body sup 			  { font-size: .83em }
.x-panel-reset .x-panel-body sub             { vertical-align: sub }
.x-panel-reset .x-panel-body sup             { vertical-align: super }
.x-panel-reset .x-panel-body table           { border-spacing: 2px; }
.x-panel-reset .x-panel-body thead,
.x-panel-reset .x-panel-body tbody,
.x-panel-reset .x-panel-body tfoot           { vertical-align: middle }
.x-panel-reset .x-panel-body td,
.x-panel-reset .x-panel-body th          	  { vertical-align: inherit }
.x-panel-reset .x-panel-body s,
.x-panel-reset .x-panel-body strike,
.x-panel-reset .x-panel-body del  			  { text-decoration: line-through }
.x-panel-reset .x-panel-body hr              { border: 1px inset }
.x-panel-reset .x-panel-body ol,
.x-panel-reset .x-panel-body ul,
.x-panel-reset .x-panel-body dir,
.x-panel-reset .x-panel-body menu,
.x-panel-reset .x-panel-body dd        	  { margin-left: 40px }
.x-panel-reset .x-panel-body ul, .x-panel-reset .x-panel-body menu, .x-panel-reset .x-panel-body dir { list-style-type: disc;}
.x-panel-reset .x-panel-body ol              { list-style-type: decimal }
.x-panel-reset .x-panel-body ol ul,
.x-panel-reset .x-panel-body ul ol,
.x-panel-reset .x-panel-body ul ul,
.x-panel-reset .x-panel-body ol ol    		  { margin-top: 0; margin-bottom: 0 }
.x-panel-reset .x-panel-body u,
.x-panel-reset .x-panel-body ins          	  { text-decoration: underline }
.x-panel-reset .x-panel-body br:before       { content: "\A" }
.x-panel-reset .x-panel-body :before, .x-panel-reset .x-panel-body :after { white-space: pre-line }
.x-panel-reset .x-panel-body center          { text-align: center }
.x-panel-reset .x-panel-body :link, .x-panel-reset .x-panel-body :visited { text-decoration: underline }
.x-panel-reset .x-panel-body :focus          { outline: thin dotted invert }

/* Begin bidirectionality settings (do not change) */
.x-panel-reset .x-panel-body BDO[DIR="ltr"]  { direction: ltr; unicode-bidi: bidi-override }
.x-panel-reset .x-panel-body BDO[DIR="rtl"]  { direction: rtl; unicode-bidi: bidi-override }
.x-window {
    zoom:1;
}

.x-window .x-resizable-handle {
    opacity:0;
    -moz-opacity:0;
    filter:alpha(opacity=0);
}

.x-window-proxy {
    border:1px solid;
    z-index:12000;
    overflow:hidden;
    position:absolute;
    left:0;top:0;
    display:none;
    opacity:.5;
    -moz-opacity:.5;
    filter:alpha(opacity=50);
}

.x-window-header {
    overflow:hidden;
    zoom:1;
}

.x-window-bwrap {
    z-index:1;
    position:relative;
    zoom:1;
    left:0;top:0;
}

.x-window-tl .x-window-header {
    padding:5px 0 4px 0;
}

.x-window-header-text {
    cursor:pointer;
}

.x-window-tc {
	background: transparent repeat-x 0 0;
	overflow:hidden;
    zoom:1;
}

.x-window-tl {
	background: transparent no-repeat 0 0;
	padding-left:6px;
    zoom:1;
    z-index:1;
    position:relative;
}

.x-window-tr {
	background: transparent no-repeat right 0;
	padding-right:6px;
}

.x-window-bc {
	background: transparent repeat-x 0 bottom;
    zoom:1;
}

.x-window-bc .x-window-footer {
    padding-bottom:6px;
    zoom:1;
    font-size:0;
    line-height:0;
}

.x-window-bl {
	background: transparent no-repeat 0 bottom;
	padding-left:6px;
    zoom:1;
}

.x-window-br {
	background: transparent no-repeat right bottom;
	padding-right:6px;
    zoom:1;
}

.x-window-mc {
    border:1px solid;
    padding:0;
    margin:0;
}

.x-window-ml {
	background: transparent repeat-y 0 0;
	padding-left:6px;
    zoom:1;
}

.x-window-mr {
	background: transparent repeat-y right 0;
	padding-right:6px;
    zoom:1;
}

.x-window-body {
    overflow:hidden;
}

.x-window-bwrap {
    overflow:hidden;
}

.x-window-maximized .x-window-bl, .x-window-maximized .x-window-br,
    .x-window-maximized .x-window-ml, .x-window-maximized .x-window-mr,
    .x-window-maximized .x-window-tl, .x-window-maximized .x-window-tr {
    padding:0;
}

.x-window-maximized .x-window-footer {
    padding-bottom:0;
}

.x-window-maximized .x-window-tc {
    padding-left:3px;
    padding-right:3px;
}

.x-window-maximized .x-window-mc {
    border-left:0 none;
    border-right:0 none;
}

.x-window-tbar .x-toolbar, .x-window-bbar .x-toolbar {
    border-left:0 none;
    border-right: 0 none;
}

.x-window-bbar .x-toolbar {
    border-top:1px solid;
    border-bottom:0 none;
}

.x-window-draggable, .x-window-draggable .x-window-header-text {
    cursor:move;
}

.x-window-maximized .x-window-draggable, .x-window-maximized .x-window-draggable .x-window-header-text {
    cursor:default;
}

.x-window-body {
    background:transparent;
}

.x-panel-ghost .x-window-tl {
    border-bottom:1px solid;
}

.x-panel-collapsed .x-window-tl {
    border-bottom:1px solid;
}

.x-window-maximized-ct {
    overflow:hidden;
}

.x-window-maximized .x-resizable-handle {
    display:none;
}

.x-window-sizing-ghost ul {
    border:0 none !important;
}

.x-dlg-focus{
	-moz-outline:0 none;
	outline:0 none;
	width:0;
	height:0;
	overflow:hidden;
	position:absolute;
	top:0;
	left:0;
}

.ext-webkit .x-dlg-focus{
    width: 1px;
    height: 1px;
}

.x-dlg-mask{
    z-index:10000;
    display:none;
    position:absolute;
    top:0;
    left:0;
    -moz-opacity: 0.5;
    opacity:.50;
    filter: alpha(opacity=50);
}

body.ext-ie6.x-body-masked select {
	visibility:hidden;
}

body.ext-ie6.x-body-masked .x-window select {
	visibility:visible;
}

.x-window-plain .x-window-mc {
    border: 1px solid;
}

.x-window-plain .x-window-body {
    border: 1px solid;
    background:transparent !important;
}.x-html-editor-wrap {
    border:1px solid;
}

.x-html-editor-tb .x-btn-text {
    background:transparent no-repeat;
}

.x-html-editor-tb .x-edit-bold, .x-menu-item img.x-edit-bold {
    background-position:0 0;
    background-image:url(../images/default/editor/tb-sprite.gif);    
}

.x-html-editor-tb .x-edit-italic, .x-menu-item img.x-edit-italic {
    background-position:-16px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-underline, .x-menu-item img.x-edit-underline {
    background-position:-32px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-forecolor, .x-menu-item img.x-edit-forecolor {
    background-position:-160px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-backcolor, .x-menu-item img.x-edit-backcolor {
    background-position:-176px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-justifyleft, .x-menu-item img.x-edit-justifyleft {
    background-position:-112px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-justifycenter, .x-menu-item img.x-edit-justifycenter {
    background-position:-128px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-justifyright, .x-menu-item img.x-edit-justifyright {
    background-position:-144px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-insertorderedlist, .x-menu-item img.x-edit-insertorderedlist {
    background-position:-80px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-insertunorderedlist, .x-menu-item img.x-edit-insertunorderedlist {
    background-position:-96px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-increasefontsize, .x-menu-item img.x-edit-increasefontsize {
    background-position:-48px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-decreasefontsize, .x-menu-item img.x-edit-decreasefontsize {
    background-position:-64px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-sourceedit, .x-menu-item img.x-edit-sourceedit {
    background-position:-192px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tb .x-edit-createlink, .x-menu-item img.x-edit-createlink {
    background-position:-208px 0;
    background-image:url(../images/default/editor/tb-sprite.gif);
}

.x-html-editor-tip .x-tip-bd .x-tip-bd-inner {
    padding:5px;
    padding-bottom:1px;
}

.x-html-editor-tb .x-toolbar {
    position:static !important;
}.x-panel-noborder .x-panel-body-noborder {
    border-width:0;
}

.x-panel-noborder .x-panel-header-noborder {
    border-width:0 0 1px;
    border-style:solid;
}

.x-panel-noborder .x-panel-tbar-noborder .x-toolbar {
    border-width:0 0 1px;
    border-style:solid;
}

.x-panel-noborder .x-panel-bbar-noborder .x-toolbar {
    border-width:1px 0 0 0;
    border-style:solid;
}

.x-window-noborder .x-window-mc {
    border-width:0;
}

.x-window-plain .x-window-body-noborder {
    border-width:0;
}

.x-tab-panel-noborder .x-tab-panel-body-noborder {
	border-width:0;
}

.x-tab-panel-noborder .x-tab-panel-header-noborder {
    border-width: 0 0 0 0;
}

.x-tab-panel-noborder .x-tab-panel-footer-noborder {
    border-width: 1px 0 0 0;
}

.x-tab-panel-bbar-noborder .x-toolbar {
    border-width: 1px 0 0 0;
    border-style:solid;
}

.x-tab-panel-tbar-noborder .x-toolbar {
    border-width:0 0 1px;
    border-style:solid;
}.x-border-layout-ct {
    position: relative;
}

.x-border-panel {
    position:absolute;
    left:0;
    top:0;
}

.x-tool-collapse-south {
    background-position:0 -195px;
}

.x-tool-collapse-south-over {
    background-position:-15px -195px;
}

.x-tool-collapse-north {
    background-position:0 -210px;
}

.x-tool-collapse-north-over {
    background-position:-15px -210px;
}

.x-tool-collapse-west {
    background-position:0 -180px;
}

.x-tool-collapse-west-over {
    background-position:-15px -180px;
}

.x-tool-collapse-east {
    background-position:0 -165px;
}

.x-tool-collapse-east-over {
    background-position:-15px -165px;
}

.x-tool-expand-south {
    background-position:0 -210px;
}

.x-tool-expand-south-over {
    background-position:-15px -210px;
}

.x-tool-expand-north {
    background-position:0 -195px;
}
.x-tool-expand-north-over {
    background-position:-15px -195px;
}

.x-tool-expand-west {
    background-position:0 -165px;
}

.x-tool-expand-west-over {
    background-position:-15px -165px;
}

.x-tool-expand-east {
    background-position:0 -180px;
}

.x-tool-expand-east-over {
    background-position:-15px -180px;
}

.x-tool-expand-north, .x-tool-expand-south {
    float:right;
    margin:3px;
}

.x-tool-expand-east, .x-tool-expand-west {
    float:none;
    margin:3px auto;
}

.x-accordion-hd .x-tool-toggle {
    background-position:0 -255px;
}

.x-accordion-hd .x-tool-toggle-over {
    background-position:-15px -255px;
}

.x-panel-collapsed .x-accordion-hd .x-tool-toggle {
    background-position:0 -240px;
}

.x-panel-collapsed .x-accordion-hd .x-tool-toggle-over {
    background-position:-15px -240px;
}

.x-accordion-hd {
	padding-top:4px;
	padding-bottom:3px;
	border-top:0 none;
    background: transparent repeat-x 0 -9px;
}

.x-layout-collapsed{
    position:absolute;
    left:-10000px;
    top:-10000px;
    visibility:hidden;
    width:20px;
    height:20px;
    overflow:hidden;
	border:1px solid;
	z-index:20;
}

.ext-border-box .x-layout-collapsed{
    width:22px;
    height:22px;
}

.x-layout-collapsed-over{
    cursor:pointer;
}

.x-layout-collapsed-west .x-layout-collapsed-tools, .x-layout-collapsed-east .x-layout-collapsed-tools{
	position:absolute;
    top:0;
    left:0;
    width:20px;
    height:20px;
}


.x-layout-split{
    position:absolute;
    height:5px;
    width:5px;
    line-height:1px;
    font-size:1px;
    z-index:3;
    background-color:transparent;
}

/* IE6 strict won't drag w/out a color */
.ext-strict .ext-ie6 .x-layout-split{
    background-color: #fff !important;
    filter: alpha(opacity=1);
}

.x-layout-split-h{
    background-image:url(../images/default/s.gif);
    background-position: left;
}

.x-layout-split-v{
    background-image:url(../images/default/s.gif);
    background-position: top;
}

.x-column-layout-ct {
    overflow:hidden;
    zoom:1;
}

.x-column {
    float:left;
    padding:0;
    margin:0;
    overflow:hidden;
    zoom:1;
}

.x-column-inner {
    overflow:hidden;
    zoom:1;
}

/* mini mode */
.x-layout-mini {
    position:absolute;
    top:0;
    left:0;
    display:block;
    width:5px;
    height:35px;
    cursor:pointer;
    opacity:.5;
    -moz-opacity:.5;
    filter:alpha(opacity=50);
}

.x-layout-mini-over, .x-layout-collapsed-over .x-layout-mini{
    opacity:1;
    -moz-opacity:1;
    filter:none;
}

.x-layout-split-west .x-layout-mini {
    top:48%;
}

.x-layout-split-east .x-layout-mini {
    top:48%;
}

.x-layout-split-north .x-layout-mini {
    left:48%;
    height:5px;
    width:35px;
}

.x-layout-split-south .x-layout-mini {
    left:48%;
    height:5px;
    width:35px;
}

.x-layout-cmini-west .x-layout-mini {
    top:48%;
}

.x-layout-cmini-east .x-layout-mini {
    top:48%;
}

.x-layout-cmini-north .x-layout-mini {
    left:48%;
    height:5px;
    width:35px;
}

.x-layout-cmini-south .x-layout-mini {
    left:48%;
    height:5px;
    width:35px;
}

.x-layout-cmini-west, .x-layout-cmini-east {
    border:0 none;
    width:5px !important;
    padding:0;
    background:transparent;
}

.x-layout-cmini-north, .x-layout-cmini-south {
    border:0 none;
    height:5px !important;
    padding:0;
    background:transparent;
}

.x-viewport, .x-viewport body {
    margin: 0;
    padding: 0;
    border: 0 none;
    overflow: hidden;
    height: 100%;
}

.x-abs-layout-item {
    position:absolute;
    left:0;
    top:0;
}

.ext-ie input.x-abs-layout-item, .ext-ie textarea.x-abs-layout-item {
    margin:0;
}

.x-box-layout-ct {
    overflow:hidden;
    zoom:1;
}

.x-box-inner {
    overflow:hidden;
    zoom:1;
    position:relative;
    left:0;
    top:0;
}

.x-box-item {
    position:absolute;
    left:0;
    top:0;
}.x-progress-wrap {
    border:1px solid;
    overflow:hidden;
}

.x-progress-inner {
    height:18px;
    background:repeat-x;
    position:relative;
}

.x-progress-bar {
    height:18px;
    float:left;
    width:0;
    background: repeat-x left center;
    border-top:1px solid;
    border-bottom:1px solid;
    border-right:1px solid;
}

.x-progress-text {
    padding:1px 5px;
    overflow:hidden;
    position:absolute;
    left:0;
    text-align:center;
}

.x-progress-text-back {
    line-height:16px;
}

.ext-ie .x-progress-text-back {
    line-height:15px;
}

.ext-strict .ext-ie7 .x-progress-text-back{
    width: 100%;
}
.x-list-header{
	background: repeat-x 0 bottom;
	cursor:default;
    zoom:1;
    height:22px;
}

.x-list-header-inner div {
    display:block;
    float:left;
    overflow:hidden;
	-o-text-overflow: ellipsis;
	text-overflow: ellipsis;
    white-space: nowrap;
}

.x-list-header-inner div em {
    display:block;
    border-left:1px solid;
    padding:4px 4px;
    overflow:hidden;
    -moz-user-select: none;
    -khtml-user-select: none;
    line-height:14px;
}

.x-list-body {
    overflow:auto;
    overflow-x:hidden;
    overflow-y:auto;
    zoom:1;
    float: left;
    width: 100%;
}

.x-list-body dl {
    zoom:1;
}

.x-list-body dt {
    display:block;
    float:left;
    overflow:hidden;
	-o-text-overflow: ellipsis;
	text-overflow: ellipsis;
    white-space: nowrap;
    cursor:pointer;
    zoom:1;
}

.x-list-body dt em {
    display:block;
    padding:3px 4px;
    overflow:hidden;
    -moz-user-select: none;
    -khtml-user-select: none;
}

.x-list-resizer {
    border-left:1px solid;
    border-right:1px solid;
    position:absolute;
    left:0;
    top:0;
}

.x-list-header-inner em.sort-asc {
    background: transparent no-repeat center 0;
    border-style:solid;
    border-width: 0 1px 1px;
    padding-bottom:3px;
}

.x-list-header-inner em.sort-desc {
    background: transparent no-repeat center -23px;
    border-style:solid;
    border-width: 0 1px 1px;
    padding-bottom:3px;
}

/* Shared styles */
.x-slider {
    zoom:1;
}

.x-slider-inner {
    position:relative;
    left:0;
    top:0;
    overflow:visible;
    zoom:1;
}

.x-slider-focus {
	position:absolute;
	left:0;
	top:0;
	width:1px;
	height:1px;
    line-height:1px;
    font-size:1px;
    -moz-outline:0 none;
    outline:0 none;
    -moz-user-select: none;
    -khtml-user-select:none;
    -webkit-user-select:ignore;
	display:block;
	overflow:hidden;  
}

/* Horizontal styles */
.x-slider-horz {
    padding-left:7px;
    background:transparent no-repeat 0 -22px;
}

.x-slider-horz .x-slider-end {
    padding-right:7px;
    zoom:1;
    background:transparent no-repeat right -44px;
}

.x-slider-horz .x-slider-inner {
    background:transparent repeat-x 0 0;
    height:22px;
}

.x-slider-horz .x-slider-thumb {
    width:14px;
    height:15px;
    position:absolute;
    left:0;
    top:3px;
    background:transparent no-repeat 0 0;
}

.x-slider-horz .x-slider-thumb-over {
    background-position: -14px -15px;
}

.x-slider-horz .x-slider-thumb-drag {
    background-position: -28px -30px;
}

/* Vertical styles */
.x-slider-vert {
    padding-top:7px;
    background:transparent no-repeat -44px 0;
    width:22px;
}

.x-slider-vert .x-slider-end {
    padding-bottom:7px;
    zoom:1;
    background:transparent no-repeat -22px bottom;
}

.x-slider-vert .x-slider-inner {
    background:transparent repeat-y 0 0;
}

.x-slider-vert .x-slider-thumb {
    width:15px;
    height:14px;
    position:absolute;
    left:3px;
    bottom:0;
    background:transparent no-repeat 0 0;
}

.x-slider-vert .x-slider-thumb-over {
    background-position: -15px -14px;
}

.x-slider-vert .x-slider-thumb-drag {
    background-position: -30px -28px;
}.x-window-dlg .x-window-body {
    border:0 none !important;
    padding:5px 10px;
    overflow:hidden !important;
}

.x-window-dlg .x-window-mc {
    border:0 none !important;
}

.x-window-dlg .ext-mb-input {
    margin-top:4px;
    width:95%;
}

.x-window-dlg .ext-mb-textarea {
    margin-top:4px;
}

.x-window-dlg .x-progress-wrap {
    margin-top:4px;
}

.ext-ie .x-window-dlg .x-progress-wrap {
    margin-top:6px;
}

.x-window-dlg .x-msg-box-wait {
    background:transparent no-repeat left;
    display:block;
    width:300px;
    padding-left:18px;
    line-height:18px;
}

.x-window-dlg .ext-mb-icon {
    float:left;
    width:47px;
    height:32px;
}

.ext-ie .x-window-dlg .ext-mb-icon {
    width:44px; /* 3px IE margin issue */
}

.x-window-dlg .x-dlg-icon .ext-mb-content{
    zoom: 1; margin-left: 47px;
}

.x-window-dlg .ext-mb-info, .x-window-dlg .ext-mb-warning, .x-window-dlg .ext-mb-question, .x-window-dlg .ext-mb-error {
    background:transparent no-repeat top left;
}

.ext-gecko2 .ext-mb-fix-cursor {
    overflow:auto;
}.ext-el-mask {
    background-color: #ccc;
}

.ext-el-mask-msg {
    border-color:#6593cf;
    background-color:#c3daf9;
    background-image:url(../images/default/box/tb-blue.gif);
}
.ext-el-mask-msg div {
    background-color: #eee;
    border-color:#a3bad9;
    color:#222;
    font:normal 11px tahoma, arial, helvetica, sans-serif;
}

.x-mask-loading div {
    background-color:#fbfbfb;
    background-image:url(../images/default/grid/loading.gif);
}

.x-item-disabled {
    color: gray;
}

.x-item-disabled * {
    color: gray !important;
}

.x-splitbar-proxy {
    background-color: #aaa;
}

.x-color-palette a {
    border-color:#fff;
}

.x-color-palette a:hover, .x-color-palette a.x-color-palette-sel {
    border-color:#8bb8f3;
    background-color: #deecfd;
}

.x-color-palette em:hover, .x-color-palette span:hover{   
    background-color: #deecfd;
}

.x-color-palette em {
    border-color:#aca899;
}

.x-ie-shadow {
    background-color:#777;
}

.x-shadow .xsmc {
    background-image: url(../images/default/shadow-c.png);
}

.x-shadow .xsml, .x-shadow .xsmr {
    background-image: url(../images/default/shadow-lr.png);
}

.x-shadow .xstl, .x-shadow .xstc,  .x-shadow .xstr, .x-shadow .xsbl, .x-shadow .xsbc, .x-shadow .xsbr{
    background-image: url(../images/default/shadow.png);
}

.loading-indicator {
    font-size: 11px;
    background-image: url(../images/default/grid/loading.gif);
}

.x-spotlight {
    background-color: #ccc;
}
.x-tab-panel-header, .x-tab-panel-footer {
	background-color: #dfe8f6;
	border-color:#8db2e3;
    overflow:hidden;
    zoom:1;
}

.x-tab-panel-header, .x-tab-panel-footer {
	border-color:#8db2e3;
}

ul.x-tab-strip-top{
    background-color:#cedff5;
	background-image: url(../images/default/tabs/tab-strip-bg.gif);
	border-bottom-color:#8db2e3;
}

ul.x-tab-strip-bottom{
    background-color:#cedff5;
	background-image: url(../images/default/tabs/tab-strip-btm-bg.gif);
	border-top-color:#8db2e3;
}

.x-tab-panel-header-plain .x-tab-strip-spacer,
.x-tab-panel-footer-plain .x-tab-strip-spacer {
    border-color:#8db2e3;
    background-color: #deecfd;
}

.x-tab-strip span.x-tab-strip-text {
	font:normal 12px verdana,tahoma,arial,helvetica;
	color:#416aa3;
}

.x-tab-strip-over span.x-tab-strip-text {
	color:#15428b;
}

.x-tab-strip-active span.x-tab-strip-text {
	color:#15428b;
    font-weight:bold;
}

.x-tab-strip-disabled .x-tabs-text {
	color:#aaaaaa;
}

.x-tab-strip-top .x-tab-right, .x-tab-strip-top .x-tab-left, .x-tab-strip-top .x-tab-strip-inner{
	background-image: url(../images/default/tabs/tabs-sprite.png?v13);
}

.x-tab-strip-bottom .x-tab-right {
	background-image: url(../images/default/tabs/tab-btm-inactive-right-bg.gif);
}

.x-tab-strip-bottom .x-tab-left {
	background-image: url(../images/default/tabs/tab-btm-inactive-left-bg.gif);
}

.x-tab-strip-bottom .x-tab-strip-over .x-tab-right {
	background-image: url(../images/default/tabs/tab-btm-over-right-bg.gif);
}

.x-tab-strip-bottom .x-tab-strip-over .x-tab-left {
	background-image: url(../images/default/tabs/tab-btm-over-left-bg.gif);
}

.x-tab-strip-bottom .x-tab-strip-active .x-tab-right {
	background-image: url(../images/default/tabs/tab-btm-right-bg.gif);
}

.x-tab-strip-bottom .x-tab-strip-active .x-tab-left {
	background-image: url(../images/default/tabs/tab-btm-left-bg.gif);
}

.x-tab-strip .x-tab-strip-closable a.x-tab-strip-close {
	background-image:url(../images/default/tabs/tab-close.gif);
}

.x-tab-strip .x-tab-strip-closable a.x-tab-strip-close:hover{
	background-image:url(../images/default/tabs/tab-close.gif);
}

.x-tab-panel-body {
    border-color:#8db2e3;
    background-color:#fff;
}

.x-tab-panel-body-top {
    border-top: 0 none;
}

.x-tab-panel-body-bottom {
    border-bottom: 0 none;
}

.x-tab-scroller-left {
    background-image:url(../images/default/tabs/scroll-left.gif);
    border-bottom-color:#8db2e3;
}

.x-tab-scroller-left-over {
    background-position: 0 0;
}

.x-tab-scroller-left-disabled {
    background-position: -18px 0;
    opacity:.5;
    -moz-opacity:.5;
    filter:alpha(opacity=50);
    cursor:default;
}

.x-tab-scroller-right {
    background-image:url(../images/default/tabs/scroll-right.gif);
    border-bottom-color:#8db2e3;
}

.x-tab-panel-bbar .x-toolbar, .x-tab-panel-tbar .x-toolbar {
    border-color:#99bbe8;
}.x-form-field{
    font:normal 12px tahoma, arial, helvetica, sans-serif;
}

.x-form-text, textarea.x-form-field{
    background-color:#fff;
    background-image:url(../images/default/form/text-bg.gif);
    border-color:#b5b8c8;
}

.x-form-select-one {
    background-color:#fff;
    border-color:#b5b8c8;
}

.x-form-check-group-label {
    border-bottom: 1px solid #99bbe8;
    color: #15428b;
}

.x-editor .x-form-check-wrap {
    background-color:#fff;
}

.x-form-field-wrap .x-form-trigger{
    background-image:url(../images/default/form/trigger.gif);
    border-bottom-color:#b5b8c8;
}

.x-form-field-wrap .x-form-date-trigger{
    background-image: url(../images/default/form/date-trigger.gif);
}

.x-form-field-wrap .x-form-clear-trigger{
    background-image: url(../images/default/form/clear-trigger.gif);
}

.x-form-field-wrap .x-form-search-trigger{
    background-image: url(../images/default/form/search-trigger.gif);
}

.x-trigger-wrap-focus .x-form-trigger{
    border-bottom-color:#7eadd9;
}

.x-item-disabled .x-form-trigger-over{
    border-bottom-color:#b5b8c8;
}

.x-item-disabled .x-form-trigger-click{
    border-bottom-color:#b5b8c8;
}

.x-form-focus, textarea.x-form-focus{
	border-color:#7eadd9;
}

.x-form-invalid, textarea.x-form-invalid{
    background-color:#fff;
	background-image:url(../images/default/grid/invalid_line.gif);
	border-color:#c30;
}

.ext-webkit .x-form-invalid{
	background-color:#fee;
	border-color:#ff7870;
}

.x-form-inner-invalid, textarea.x-form-inner-invalid{
    background-color:#fff;
	background-image:url(../images/default/grid/invalid_line.gif);
}

.x-form-grow-sizer {
	font:normal 12px tahoma, arial, helvetica, sans-serif;
}

.x-form-item {
    font:normal 12px tahoma, arial, helvetica, sans-serif;
}

.x-form-invalid-msg {
    color:#c0272b;
    font:normal 11px tahoma, arial, helvetica, sans-serif;
    background-image:url(../images/default/shared/warning.gif);
}

.x-form-empty-field {
    color:gray;
}

.x-small-editor .x-form-field {
    font:normal 11px arial, tahoma, helvetica, sans-serif;
}

.ext-webkit .x-small-editor .x-form-field {
    font:normal 12px arial, tahoma, helvetica, sans-serif;
}

.x-form-invalid-icon {
    background-image:url(../images/default/form/exclamation.gif);
}

.x-fieldset {
    border-color:#b5b8c8;
}

.x-fieldset legend {
    font:bold 11px tahoma, arial, helvetica, sans-serif;
    color:#15428b;
}
.x-btn{
	font:normal 11px tahoma, verdana, helvetica;
}

.x-btn button{
    font:normal 11px arial,tahoma,verdana,helvetica;
    color:#333;
}

.x-btn em {
    font-style:normal;
    font-weight:normal;
}

.x-btn-tl, .x-btn-tr, .x-btn-tc, .x-btn-ml, .x-btn-mr, .x-btn-mc, .x-btn-bl, .x-btn-br, .x-btn-bc{
	background-image:url(../images/default/button/btn.gif);
}

.x-btn-click .x-btn-text, .x-btn-menu-active .x-btn-text, .x-btn-pressed .x-btn-text{
    color:#000;
}

.x-btn-disabled *{
	color:gray !important;
}

.x-btn-mc em.x-btn-arrow {
    background-image:url(../images/default/button/arrow.gif);
}

.x-btn-mc em.x-btn-split {
    background-image:url(../images/default/button/s-arrow.gif);
}

.x-btn-over .x-btn-mc em.x-btn-split, .x-btn-click .x-btn-mc em.x-btn-split, .x-btn-menu-active .x-btn-mc em.x-btn-split, .x-btn-pressed .x-btn-mc em.x-btn-split {
    background-image:url(../images/default/button/s-arrow-o.gif);
}

.x-btn-over .x-btn-text{
	color			:#FFF;
    text-shadow     : 1px 1px 1px #333;
}

.x-btn-mc em.x-btn-arrow-bottom {
    background-image:url(../images/default/button/s-arrow-b-noline.gif);
}

.x-btn-mc em.x-btn-split-bottom {
    background-image:url(../images/default/button/s-arrow-b.gif);
}

.x-btn-over .x-btn-mc em.x-btn-split-bottom, .x-btn-click .x-btn-mc em.x-btn-split-bottom, .x-btn-menu-active .x-btn-mc em.x-btn-split-bottom, .x-btn-pressed .x-btn-mc em.x-btn-split-bottom {
    background-image:url(../images/default/button/s-arrow-bo.gif);
}

.x-btn-group-header {
    color: #3e6aaa;
}

.x-btn-group-tc {
	/*background-image: url(../images/default/button/group-tb.gif);*/
}

.x-btn-group-tl {
	/*background-image: url(../images/default/button/group-cs.gif);*/
}

.x-btn-group-tr {
	/*background-image: url(../images/default/button/group-cs.gif);*/
}

.x-btn-group-bc {
	/*background-image: url(../images/default/button/group-tb.gif);*/
}

.x-btn-group-bl {
	/*background-image: url(../images/default/button/group-cs.gif);*/
}

.x-btn-group-br {
	/*background-image: url(../images/default/button/group-cs.gif);*/
}

.x-btn-group-ml {
	/*background-image: url(../images/default/button/group-lr.gif);*/
}
.x-btn-group-mr {
	/*background-image: url(../images/default/button/group-lr.gif);*/
}

.x-btn-group-notitle .x-btn-group-tc {
	/*background-image: url(../images/default/button/group-tb.gif);*/
    
}.x-toolbar{
	border:0px #a9bfd3;
    background-color:#dfe8f6;
    /*background-image:url(../images/default/toolbar/bg.gif);*/
}

.x-toolbar td,.x-toolbar span,.x-toolbar input,.x-toolbar div,.x-toolbar select,.x-toolbar label{
    font:normal 11px arial,tahoma, helvetica, sans-serif;
}

.x-toolbar .x-item-disabled {
	color:gray;
}

.x-toolbar .x-item-disabled * {
	color:gray;
}

.x-toolbar .x-btn-mc em.x-btn-split {
    background-image:url(../images/default/button/s-arrow-noline.gif);
}

.x-toolbar .x-btn-over .x-btn-mc em.x-btn-split, .x-toolbar .x-btn-click .x-btn-mc em.x-btn-split,
.x-toolbar .x-btn-menu-active .x-btn-mc em.x-btn-split, .x-toolbar .x-btn-pressed .x-btn-mc em.x-btn-split
{
    background-image:url(../images/default/button/s-arrow-o.gif);
}

.x-toolbar .x-btn-mc em.x-btn-split-bottom {
    background-image:url(../images/default/button/s-arrow-b-noline.gif);
}

.x-toolbar .x-btn-over .x-btn-mc em.x-btn-split-bottom, .x-toolbar .x-btn-click .x-btn-mc em.x-btn-split-bottom,
.x-toolbar .x-btn-menu-active .x-btn-mc em.x-btn-split-bottom, .x-toolbar .x-btn-pressed .x-btn-mc em.x-btn-split-bottom
{
    background-image:url(../images/default/button/s-arrow-bo.gif);
}

.x-toolbar .xtb-sep {
	background-image: url(../images/default/grid/grid-blue-split.gif);
}

.x-tbar-page-first{
	background-image: url(../images/default/grid/page-first.gif) !important;
}

.x-tbar-loading{
	background-image: url(../images/default/grid/refresh.gif) !important;
}

.x-tbar-page-last{
	background-image: url(../images/default/grid/page-last.gif) !important;
}

.x-tbar-page-next{
	background-image: url(../images/default/grid/page-next.gif) !important;
}

.x-tbar-page-prev{
	background-image: url(../images/default/grid/page-prev.gif) !important;
}

.x-item-disabled .x-tbar-loading{
	background-image: url(../images/default/grid/loading.gif) !important;
}

.x-item-disabled .x-tbar-page-first{
	background-image: url(../images/default/grid/page-first-disabled.gif) !important;
}

.x-item-disabled .x-tbar-page-last{
	background-image: url(../images/default/grid/page-last-disabled.gif) !important;
}

.x-item-disabled .x-tbar-page-next{
	background-image: url(../images/default/grid/page-next-disabled.gif) !important;
}

.x-item-disabled .x-tbar-page-prev{
	background-image: url(../images/default/grid/page-prev-disabled.gif) !important;
}

.x-paging-info {
    color:#444;
}

.x-toolbar-more-icon {
    background-image: url(../images/default/toolbar/more.gif) !important;
}.x-resizable-handle {
	background-color:#fff;
}

.x-resizable-over .x-resizable-handle-east, .x-resizable-pinned .x-resizable-handle-east,
.x-resizable-over .x-resizable-handle-west, .x-resizable-pinned .x-resizable-handle-west
{
    background-image:url(../images/default/sizer/e-handle.gif);
}

.x-resizable-over .x-resizable-handle-south, .x-resizable-pinned .x-resizable-handle-south,
.x-resizable-over .x-resizable-handle-north, .x-resizable-pinned .x-resizable-handle-north
{
    background-image:url(../images/default/sizer/s-handle.gif);
}

.x-resizable-over .x-resizable-handle-north, .x-resizable-pinned .x-resizable-handle-north{
    background-image:url(../images/default/sizer/s-handle.gif);
}
.x-resizable-over .x-resizable-handle-southeast, .x-resizable-pinned .x-resizable-handle-southeast{
    background-image:url(../images/default/sizer/se-handle.gif);
}
.x-resizable-over .x-resizable-handle-northwest, .x-resizable-pinned .x-resizable-handle-northwest{
    background-image:url(../images/default/sizer/nw-handle.gif);
}
.x-resizable-over .x-resizable-handle-northeast, .x-resizable-pinned .x-resizable-handle-northeast{
    background-image:url(../images/default/sizer/ne-handle.gif);
}
.x-resizable-over .x-resizable-handle-southwest, .x-resizable-pinned .x-resizable-handle-southwest{
    background-image:url(../images/default/sizer/sw-handle.gif);
}
.x-resizable-proxy{
    border-color:#3b5a82;
}
.x-resizable-overlay{
    background-color:#fff;
}
.x-grid3 {
    background-color:#fff;
}

.x-grid-panel .x-panel-mc .x-panel-body {
    border-color:#99bbe8;
}

.x-grid3-hd-row td, .x-grid3-row td, .x-grid3-summary-row td{
	font:normal 11px arial, tahoma, helvetica, sans-serif;
}

.x-grid3-hd-row td {
    border-left-color:#eee;
    border-right-color:#d0d0d0;
}

.x-grid-row-loading {
    background-color: #fff;
    background-image:url(../images/default/shared/loading-balls.gif);
}

.x-grid3-row {
    border-color:#ededed;
    border-top-color:#fff;
}

.x-grid3-row-alt{
	background-color:#fafafa;
}

.x-grid3-row-over {
	border-color:#ddd;
    background-color:#efefef;
    background-image:url(../images/default/grid/row-over.gif);
}

.x-grid3-resize-proxy {
    background-color:#777;
}

.x-grid3-resize-marker {
    background-color:#777;
}

.x-grid3-header{
    background-color:#f9f9f9;
	background-image:url(../images/default/grid/grid3-hrow.gif);
}

.x-grid3-header-pop {
    border-left-color:#d0d0d0;
}

.x-grid3-header-pop-inner {
    border-left-color:#eee;
    background-image:url(../images/default/grid/hd-pop.gif);
}

td.x-grid3-hd-over, td.sort-desc, td.sort-asc, td.x-grid3-hd-menu-open {
    border-left-color:#aaccf6;
    border-right-color:#aaccf6;
}

td.x-grid3-hd-over .x-grid3-hd-inner, td.sort-desc .x-grid3-hd-inner, td.sort-asc .x-grid3-hd-inner, td.x-grid3-hd-menu-open .x-grid3-hd-inner {
    background-color:#ebf3fd;
    background-image:url(../images/default/grid/grid3-hrow-over.gif);

}

.sort-asc .x-grid3-sort-icon {
	background-image: url(../images/default/grid/sort_asc.gif);
}

.sort-desc .x-grid3-sort-icon {
	background-image: url(../images/default/grid/sort_desc.gif);
}

.x-grid3-cell-text, .x-grid3-hd-text {
	color:#000;
}

.x-grid3-split {
	background-image: url(../images/default/grid/grid-split.gif);
}

.x-grid3-hd-text {
	color:#15428b;
}

.x-dd-drag-proxy .x-grid3-hd-inner{
    background-color:#ebf3fd;
	background-image:url(../images/default/grid/grid3-hrow-over.gif);
	border-color:#aaccf6;
}

.col-move-top{
	background-image:url(../images/default/grid/col-move-top.gif);
}

.col-move-bottom{
	background-image:url(../images/default/grid/col-move-bottom.gif);
}

.x-grid3-row-selected {
	background-color: #dfe8f6 !important;
	background-image: none;
	border-color:#a3bae9;
}

.x-grid3-cell-selected{
	background-color: #b8cfee !important;
	color:#000;
}

.x-grid3-cell-selected span{
	color:#000 !important;
}

.x-grid3-cell-selected .x-grid3-cell-text{
	color:#000;
}

.x-grid3-locked td.x-grid3-row-marker, .x-grid3-locked .x-grid3-row-selected td.x-grid3-row-marker{
    background-color:#ebeadb !important;
    background-image:url(../images/default/grid/grid-hrow.gif) !important;
    color:#000;
    border-top-color:#fff;
    border-right-color:#6fa0df !important;
}

.x-grid3-locked td.x-grid3-row-marker div, .x-grid3-locked .x-grid3-row-selected td.x-grid3-row-marker div{
    color:#15428b !important;
}

.x-grid3-dirty-cell {
    background-image:url(../images/default/grid/dirty.gif);
}

.x-grid3-topbar, .x-grid3-bottombar{
	font:normal 11px arial, tahoma, helvetica, sans-serif;
}

.x-grid3-bottombar .x-toolbar{
	border-top-color:#a9bfd3;
}

.x-props-grid .x-grid3-td-name .x-grid3-cell-inner{
	background-image:url(../images/default/grid/grid3-special-col-bg.gif) !important;
    color:#000 !important;
}

.x-props-grid .x-grid3-body .x-grid3-td-name{
    background-color:#fff !important;
    border-right-color:#eee;
}

.xg-hmenu-sort-asc .x-menu-item-icon{
	background-image: url(../images/default/grid/hmenu-asc.gif);
}

.xg-hmenu-sort-desc .x-menu-item-icon{
	background-image: url(../images/default/grid/hmenu-desc.gif);
}

.xg-hmenu-lock .x-menu-item-icon{
	background-image: url(../images/default/grid/hmenu-lock.gif);
}

.xg-hmenu-unlock .x-menu-item-icon{
	background-image: url(../images/default/grid/hmenu-unlock.gif);
}

.x-grid3-hd-btn {
    background-color:#c3daf9;
    background-image:url(../images/default/grid/grid3-hd-btn.gif);
}

.x-grid3-body .x-grid3-td-expander {
    background-image:url(../images/default/grid/grid3-special-col-bg.gif);
}

.x-grid3-row-expander {
    background-image:url(../images/default/grid/row-expand-sprite.gif);
}

.x-grid3-body .x-grid3-td-checker {
    background-image: url(../images/default/grid/grid3-special-col-bg.gif);
}

.x-grid3-row-checker, .x-grid3-hd-checker {
    background-image:url(../images/default/grid/row-check-sprite.gif);
}

.x-grid3-body .x-grid3-td-numberer {
    background-image:url(../images/default/grid/grid3-special-col-bg.gif);
}

.x-grid3-body .x-grid3-td-numberer .x-grid3-cell-inner {
	color:#444;
}

.x-grid3-body .x-grid3-td-row-icon {
    background-image:url(../images/default/grid/grid3-special-col-bg.gif);
}

.x-grid3-body .x-grid3-row-selected .x-grid3-td-numberer,
.x-grid3-body .x-grid3-row-selected .x-grid3-td-checker,
.x-grid3-body .x-grid3-row-selected .x-grid3-td-expander {
	background-image:url(../images/default/grid/grid3-special-col-sel-bg.gif);
}

.x-grid3-check-col {
	background-image:url(../images/default/menu/unchecked.gif);
}

.x-grid3-check-col-on {
	background-image:url(../images/default/menu/checked.gif);
}

.x-grid-group, .x-grid-group-body, .x-grid-group-hd {
    zoom:1;
}

.x-grid-group-hd {
    border-bottom-color:#99bbe8;
}

.x-grid-group-hd div.x-grid-group-title {
    background-image:url(../images/default/grid/group-collapse.gif);
    color:#3764a0;
    font:bold 11px tahoma, arial, helvetica, sans-serif;
}

.x-grid-group-collapsed .x-grid-group-hd div.x-grid-group-title {
    background-image:url(../images/default/grid/group-expand.gif);
}

.x-group-by-icon {
    background-image:url(../images/default/grid/group-by.gif);
}

.x-cols-icon {
    background-image:url(../images/default/grid/columns.gif);
}

.x-show-groups-icon {
    background-image:url(../images/default/grid/group-by.gif);
}

.x-grid-empty {
    color:gray;
    font:normal 11px tahoma, arial, helvetica, sans-serif;
}

.x-grid-with-col-lines .x-grid3-row td.x-grid3-cell {
    border-right-color:#ededed;
}

.x-grid-with-col-lines .x-grid3-row-selected {
	border-top-color:#a3bae9;
}.x-dd-drag-ghost{
	color:#000;
	font: normal 11px arial, helvetica, sans-serif;
    border-color: #ddd #bbb #bbb #ddd;
	background-color:#fff;
}

.x-dd-drop-nodrop .x-dd-drop-icon{
  background-image: url(../images/default/dd/drop-no.gif);
}

.x-dd-drop-ok .x-dd-drop-icon{
  background-image: url(../images/default/dd/drop-yes.gif);
}

.x-dd-drop-ok-add .x-dd-drop-icon{
  background-image: url(../images/default/dd/drop-add.gif);
}

.x-view-selector {
    background-color:#c3daf9;
    border-color:#3399bb;
}.x-tree-node-expanded .x-tree-node-icon{
	background-image:url(../images/default/tree/folder-open.gif);
}

.x-tree-node-leaf .x-tree-node-icon{
	background-image:url(../images/default/tree/leaf.gif);
}

.x-tree-node-collapsed .x-tree-node-icon{
	background-image:url(../images/default/tree/folder.gif);
}

.x-tree-node-loading .x-tree-node-icon{
	background-image:url(../images/default/tree/loading.gif) !important;
}

.x-tree-node .x-tree-node-inline-icon {
    background-image: none;
}

.x-tree-node-loading a span{
	 font-style: italic;
	 color:#444444;
}

.x-tree-lines .x-tree-elbow{
	background-image:url(../images/default/tree/elbow.gif);
}

.x-tree-lines .x-tree-elbow-plus{
	background-image:url(../images/default/tree/elbow-plus.gif);
}

.x-tree-lines .x-tree-elbow-minus{
	background-image:url(../images/default/tree/elbow-minus.gif);
}

.x-tree-lines .x-tree-elbow-end{
	background-image:url(../images/default/tree/elbow-end.gif);
}

.x-tree-lines .x-tree-elbow-end-plus{
	background-image:url(../images/default/tree/elbow-end-plus.gif);
}

.x-tree-lines .x-tree-elbow-end-minus{
	background-image:url(../images/default/tree/elbow-end-minus.gif);
}

.x-tree-lines .x-tree-elbow-line{
	background-image:url(../images/default/tree/elbow-line.gif);
}

.x-tree-no-lines .x-tree-elbow-plus{
	background-image:url(../images/default/tree/elbow-plus-nl.gif);
}

.x-tree-no-lines .x-tree-elbow-minus{
	background-image:url(../images/default/tree/elbow-minus-nl.gif);
}

.x-tree-no-lines .x-tree-elbow-end-plus{
	background-image:url(../images/default/tree/elbow-end-plus-nl.gif);
}

.x-tree-no-lines .x-tree-elbow-end-minus{
	background-image:url(../images/default/tree/elbow-end-minus-nl.gif);
}

.x-tree-arrows .x-tree-elbow-plus{
    background-image:url(../images/default/tree/arrows.gif);
}

.x-tree-arrows .x-tree-elbow-minus{
    background-image:url(../images/default/tree/arrows.gif);
}

.x-tree-arrows .x-tree-elbow-end-plus{
    background-image:url(../images/default/tree/arrows.gif);
}

.x-tree-arrows .x-tree-elbow-end-minus{
    background-image:url(../images/default/tree/arrows.gif);
}

.x-tree-node{
	color:#000;
	font: normal 11px arial, tahoma, helvetica, sans-serif;
}

.x-tree-node a, .x-dd-drag-ghost a{
	color:#000;
}

.x-tree-node a span, .x-dd-drag-ghost a span{
	color:#000;
}

.x-tree-node .x-tree-node-disabled a span{
	color:gray !important;
}

.x-tree-node div.x-tree-drag-insert-below{
 	 border-bottom-color:#36c;
}

.x-tree-node div.x-tree-drag-insert-above{
	 border-top-color:#36c;
}

.x-tree-dd-underline .x-tree-node div.x-tree-drag-insert-below a{
 	 border-bottom-color:#36c;
}

.x-tree-dd-underline .x-tree-node div.x-tree-drag-insert-above a{
	 border-top-color:#36c;
}

.x-tree-node .x-tree-drag-append a span{
	 background-color:#ddd;
	 border-color:gray;
}

.x-tree-node .x-tree-node-over {
	background-color: #eee;
}

.x-tree-node .x-tree-selected {
	background-color: #d9e8fb;
}

.x-tree-drop-ok-append .x-dd-drop-icon{
  background-image: url(../images/default/tree/drop-add.gif);
}

.x-tree-drop-ok-above .x-dd-drop-icon{
  background-image: url(../images/default/tree/drop-over.gif);
}

.x-tree-drop-ok-below .x-dd-drop-icon{
  background-image: url(../images/default/tree/drop-under.gif);
}

.x-tree-drop-ok-between .x-dd-drop-icon{
  background-image: url(../images/default/tree/drop-between.gif);
}.x-date-picker {
    border-color: #1b376c;
    background-color:#fff;
}

.x-date-middle,.x-date-left,.x-date-right {
	background-image: url(../images/default/shared/hd-sprite.gif);
	color:#fff;
	font:bold 11px "sans serif", tahoma, verdana, helvetica;
}

.x-date-middle .x-btn .x-btn-text {
    color:#fff;
}

.x-date-middle .x-btn-mc em.x-btn-arrow {
    background-image:url(../images/default/toolbar/btn-arrow-light.gif);
}

.x-date-right a {
    background-image: url(../images/default/shared/right-btn.gif);
}

.x-date-left a{
	background-image: url(../images/default/shared/left-btn.gif);
}

.x-date-inner th {
    background-color:#dfecfb;
    background-image:url(../images/default/shared/glass-bg.gif);
	border-bottom-color:#a3bad9;
    font:normal 10px arial, helvetica,tahoma,sans-serif;
	color:#233d6d;
}

.x-date-inner td {
    border-color:#fff;
}

.x-date-inner a {
    font:normal 11px arial, helvetica,tahoma,sans-serif;
    color:#000;
}

.x-date-inner .x-date-active{
	color:#000;
}

.x-date-inner .x-date-selected a{
    background-color:#dfecfb;
	background-image:url(../images/default/shared/glass-bg.gif);
	border-color:#8db2e3;
}

.x-date-inner .x-date-today a{
	border-color:darkred;
}

.x-date-inner .x-date-selected span{
    font-weight:bold;
}

.x-date-inner .x-date-prevday a,.x-date-inner .x-date-nextday a {
	color:#aaa;
}

.x-date-bottom {
    border-top-color:#a3bad9;
    background-color:#dfecfb;
    background-image:url(../images/default/shared/glass-bg.gif);
}

.x-date-inner a:hover, .x-date-inner .x-date-disabled a:hover{
    color:#000;
    background-color:#ddecfe;
}

.x-date-inner .x-date-disabled a {
	background-color:#eee;
	color:#bbb;
}

.x-date-mmenu{
    background-color:#eee !important;
}

.x-date-mmenu .x-menu-item {
	font-size:10px;
	color:#000;
}

.x-date-mp {
	background-color:#fff;
}

.x-date-mp td {
	font:normal 11px arial, helvetica,tahoma,sans-serif;
}

.x-date-mp-btns button {
	background-color:#083772;
	color:#fff;
	border-color: #3366cc #000055 #000055 #3366cc;
	font:normal 11px arial, helvetica,tahoma,sans-serif;
}

.x-date-mp-btns {
    background-color: #dfecfb;
	background-image: url(../images/default/shared/glass-bg.gif);
}

.x-date-mp-btns td {
	border-top-color: #c5d2df;
}

td.x-date-mp-month a,td.x-date-mp-year a {
	color:#15428b;
}

td.x-date-mp-month a:hover,td.x-date-mp-year a:hover {
	color:#15428b;
	background-color: #ddecfe;
}

td.x-date-mp-sel a {
    background-color: #dfecfb;
	background-image: url(../images/default/shared/glass-bg.gif);
	border-color:#8db2e3;
}

.x-date-mp-ybtn a {
    background-image:url(../images/default/panel/tool-sprites.png);
}

td.x-date-mp-sep {
   border-right-color:#c5d2df;
}.x-tip .x-tip-close{
	background-image: url(../images/default/qtip/close.gif);
}

.x-tip .x-tip-tc, .x-tip .x-tip-tl, .x-tip .x-tip-tr, .x-tip .x-tip-bc, .x-tip .x-tip-bl, .x-tip .x-tip-br, .x-tip .x-tip-ml, .x-tip .x-tip-mr {
	background-image: url(../images/default/qtip/tip-sprite.gif);
}

.x-tip .x-tip-mc {
    font: normal 11px tahoma,arial,helvetica,sans-serif;
}
.x-tip .x-tip-ml {
	background-color: #fff;
}

.x-tip .x-tip-header-text {
    font: bold 11px tahoma,arial,helvetica,sans-serif;
    color:#444;
}

.x-tip .x-tip-body {
    font: normal 11px tahoma,arial,helvetica,sans-serif;
    color:#444;
}

.x-form-invalid-tip .x-tip-tc, .x-form-invalid-tip .x-tip-tl, .x-form-invalid-tip .x-tip-tr, .x-form-invalid-tip .x-tip-bc,
.x-form-invalid-tip .x-tip-bl, .x-form-invalid-tip .x-tip-br, .x-form-invalid-tip .x-tip-ml, .x-form-invalid-tip .x-tip-mr
{
	background-image: url(../images/default/form/error-tip-corners.gif);
}

.x-form-invalid-tip .x-tip-body {
    background-image:url(../images/default/form/exclamation.gif);
}

.x-tip-anchor {
    background-image:url(../images/default/qtip/tip-anchor-sprite.gif);
}.x-menu {
    background-color:#f0f0f0;
	background-image:url(../images/default/menu/menu.gif);
}

.x-menu-floating{
    border-color:#718bb7;
}

.x-menu-nosep {
	background-image:none;
}

.x-menu-list-item{
	font:normal 11px arial,tahoma,sans-serif;
}

.x-menu-item-arrow{
	background-image:url(../images/default/menu/menu-parent.gif);
}

.x-menu-sep {
    background-color:#e0e0e0;
	border-bottom-color:#fff;
}

a.x-menu-item {
	color:#222;
}

.x-menu-item-active {
    background-image: url(../images/default/menu/item-over.gif);
	background-color: #dbecf4;
    border-color:#aaccf6;
}

.x-menu-item-active a.x-menu-item {
	border-color:#aaccf6;
}

.x-menu-check-item .x-menu-item-icon{
	background-image:url(../images/default/menu/unchecked.gif);
}

.x-menu-item-checked .x-menu-item-icon{
	background-image:url(../images/default/menu/checked.gif);
}

.x-menu-item-checked .x-menu-group-item .x-menu-item-icon{
    background-image:url(../images/default/menu/group-checked.gif);
}

.x-menu-group-item .x-menu-item-icon{
    background-image:none;
}

.x-menu-plain {
	background-color:#f0f0f0 !important;
    background-image: none;
}

.x-date-menu, .x-color-menu{
    background-color: #fff !important;
}

.x-menu .x-date-picker{
    border-color:#a3bad9;
}

.x-cycle-menu .x-menu-item-checked {
    border-color:#a3bae9 !important;
    background-color:#def8f6;
}

.x-menu-scroller-top {
    background-image:url(../images/default/layout/mini-top.gif);
}

.x-menu-scroller-bottom {
    background-image:url(../images/default/layout/mini-bottom.gif);
}
.x-box-tl {
	background-image: url(../images/default/box/corners.gif);
}

.x-box-tc {
	background-image: url(../images/default/box/tb.gif);
}

.x-box-tr {
	background-image: url(../images/default/box/corners.gif);
}

.x-box-ml {
	background-image: url(../images/default/box/l.gif);
}

.x-box-mc {
	background-color: #eee;
    background-image: url(../images/default/box/tb.gif);
	font-family: "Myriad Pro","Myriad Web","Tahoma","Helvetica","Arial",sans-serif;
	color: #393939;
	font-size: 12px;
}

.x-box-mc h3 {
	font-size: 14px;
	font-weight: bold;
}

.x-box-mr {
	background-image: url(../images/default/box/r.gif);
}

.x-box-bl {
	background-image: url(../images/default/box/corners.gif);
}

.x-box-bc {
	background-image: url(../images/default/box/tb.gif);
}

.x-box-br {
	background-image: url(../images/default/box/corners.gif);
}

.x-box-blue .x-box-bl, .x-box-blue .x-box-br, .x-box-blue .x-box-tl, .x-box-blue .x-box-tr {
	background-image: url(../images/default/box/corners-blue.gif);
}

.x-box-blue .x-box-bc, .x-box-blue .x-box-mc, .x-box-blue .x-box-tc {
	background-image: url(../images/default/box/tb-blue.gif);
}

.x-box-blue .x-box-mc {
	background-color: #c3daf9;
}

.x-box-blue .x-box-mc h3 {
	color: #17385b;
}

.x-box-blue .x-box-ml {
	background-image: url(../images/default/box/l-blue.gif);
}

.x-box-blue .x-box-mr {
	background-image: url(../images/default/box/r-blue.gif);
}.x-combo-list {
    border-color:#98c0f4;
    background-color:#ddecfe;
    font:normal 12px tahoma, arial, helvetica, sans-serif;
}

.x-combo-list-inner {
    background-color:#fff;
}

.x-combo-list-hd {
    font:bold 11px tahoma, arial, helvetica, sans-serif;
    color:#15428b;
    background-image: url(../images/default/layout/panel-title-light-bg.gif);
    border-bottom-color:#98c0f4;
}

.x-resizable-pinned .x-combo-list-inner {
    border-bottom-color:#98c0f4;
}

.x-combo-list-item {
    border-color:#fff;
}

.x-combo-list .x-combo-selected{
	border-color:#a3bae9 !important;
    background-color:#dfe8f6;
}

.x-combo-list .x-toolbar {
    border-top-color:#98c0f4;
}

.x-combo-list-small {
    font:normal 11px tahoma, arial, helvetica, sans-serif;
}.x-panel {
    border-color: #99bbe8;
}

.x-panel-header {
    color:#15428b;
	font-weight:bold; 
    font-size: 11px;
    font-family: tahoma,arial,verdana,sans-serif;
    border-color:#99bbe8;
    background-image: url(../images/default/panel/white-top-bottom.gif);
}

.x-panel-body {
    border-color:#99bbe8;
    background-color:#fff;
}

.x-panel-bbar .x-toolbar, .x-panel-tbar .x-toolbar {
    border-color:#99bbe8;
}

.x-panel-tbar-noheader .x-toolbar, .x-panel-mc .x-panel-tbar .x-toolbar {
    border-top-color:#99bbe8;
}

.x-panel-body-noheader, .x-panel-mc .x-panel-body {
    border-top-color:#99bbe8;
}

.x-panel-tl .x-panel-header {
    color:#15428b;
	font:bold 11px tahoma,arial,verdana,sans-serif;
}

.x-panel-tc {
	background-image: url(../images/default/panel/top-bottom.gif);
}

.x-panel-tl, .x-panel-tr, .x-panel-bl,  .x-panel-br{
	background-image: url(../images/default/panel/corners-sprite.gif);
    border-bottom-color:#99bbe8;
}

.x-panel-bc {
	background-image: url(../images/default/panel/top-bottom.gif);
}

.x-panel-mc {
    font: normal 11px tahoma,arial,helvetica,sans-serif;
    background-color:#dfe8f6;
}

.x-panel-ml {
	background-color: #fff;
    background-image:url(../images/default/panel/left-right.gif);
}

.x-panel-mr {
	background-image: url(../images/default/panel/left-right.gif);
}

.x-tool {
    background-image:url(../images/default/panel/tool-sprites.png?v6);
}

.x-panel-ghost {
    background-color:#cbddf3;
}

.x-panel-ghost ul {
    border-color:#99bbe8;
}

.x-panel-dd-spacer {
    border-color:#99bbe8;
}

.x-panel-fbar td,.x-panel-fbar span,.x-panel-fbar input,.x-panel-fbar div,.x-panel-fbar select,.x-panel-fbar label{
    font:normal 11px arial,tahoma, helvetica, sans-serif;
}
.x-window-proxy {
    background-color:#c7dffc;
    border-color:#99bbe8;
}

.x-window-tl .x-window-header {
    color:#FFF;
	font:bold 12px verdana,tahoma,arial,verdana,sans-serif;
}

.x-window-tc {
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,.8);
}

.x-window-tl {
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,1);
}

.x-window-tr {
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,1);    
}

.x-window-bc {
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,1);
}

.x-window-bl {
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,1);
}

.x-window-br {
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,1);
}

.x-window-mc {
    border-color:#99bbe8;
    font: normal 11px tahoma,arial,helvetica,sans-serif;
    background-color:#dfe8f6;
}

.x-window-ml {
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,1);
}

.x-window-mr {
    background: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,1);    
}

.x-window-maximized .x-window-tc {
    background-color:#fff;
}

.x-window-bbar .x-toolbar {
    border-top-color:#99bbe8;
}

.x-panel-ghost .x-window-tl {
    border-bottom-color:#99bbe8;
}

.x-panel-collapsed .x-window-tl {
    border-bottom-color:#84a0c4;
}

.x-dlg-mask{
   background-color:#ccc;
}

.x-window-plain .x-window-mc {
    background-color: #ccd9e8;
    border-color: #a3bae9 #dfe8f6 #dfe8f6 #a3bae9;
}

.x-window-plain .x-window-body {
    border-color: #dfe8f6 #a3bae9 #a3bae9 #dfe8f6;
}

body.x-body-masked .x-window-plain .x-window-mc {
    background-color: rgba(<?php echo $_SESSION["COLOR_MENU"] ?>,1);
    color: #FFF;
}

.x-html-editor-wrap {
    border-color:#a9bfd3;
    background-color:#fff;
}
.x-html-editor-tb .x-btn-text {
    background-image:url(../images/default/editor/tb-sprite.gif);
}.x-panel-noborder .x-panel-header-noborder {
    border-bottom-color:#99bbe8;
}

.x-panel-noborder .x-panel-tbar-noborder .x-toolbar {
    border-bottom-color:#99bbe8;
}

.x-panel-noborder .x-panel-bbar-noborder .x-toolbar {
    border-top-color:#99bbe8;
}

.x-tab-panel-bbar-noborder .x-toolbar {
    border-top-color:#99bbe8;
}

.x-tab-panel-tbar-noborder .x-toolbar {
    border-bottom-color:#99bbe8;
}.x-border-layout-ct {
    background-color:#dfe8f6;
}

.x-accordion-hd {
	color:#222;
    font-weight:normal;
    background-image: url(../images/default/panel/light-hd.gif);
}

.x-layout-collapsed{
    background-color:#d2e0f2;
	border-color:#98c0f4;
}

.x-layout-collapsed-over{
    background-color:#d9e8fb;
}

.x-layout-split-west .x-layout-mini {
    background-image:url(../images/default/layout/mini-left.gif);
}
.x-layout-split-east .x-layout-mini {
    background-image:url(../images/default/layout/mini-right.gif);
}
.x-layout-split-north .x-layout-mini {
    background-image:url(../images/default/layout/mini-top.gif);
}
.x-layout-split-south .x-layout-mini {
    background-image:url(../images/default/layout/mini-bottom.gif);
}

.x-layout-cmini-west .x-layout-mini {
    background-image:url(../images/default/layout/mini-right.gif);
}

.x-layout-cmini-east .x-layout-mini {
    background-image:url(../images/default/layout/mini-left.gif);
}

.x-layout-cmini-north .x-layout-mini {
    background-image:url(../images/default/layout/mini-bottom.gif);
}

.x-layout-cmini-south .x-layout-mini {
    background-image:url(../images/default/layout/mini-top.gif);
}.x-progress-wrap {
    border-color:#6593cf;
}

.x-progress-inner {
    background-color:#e0e8f3;
    background-image:url(../images/default/qtip/bg.gif);
}

.x-progress-bar {
    background-color:#9cbfee;
    background-image:url(../images/default/progress/progress-bg.gif);
    border-top-color:#d1e4fd;
    border-bottom-color:#7fa9e4;
    border-right-color:#7fa9e4;
}

.x-progress-text {
    font-size:11px;
    font-weight:bold;
    color:#fff;
}

.x-progress-text-back {
    color:#396095;
}.x-list-header{
    background-color:#f9f9f9;
	background-image:url(../images/default/grid/grid3-hrow.gif);
}

.x-list-header-inner div em {
    border-left-color:#ddd;
    font:normal 11px arial, tahoma, helvetica, sans-serif;
}

.x-list-body dt em {
    font:normal 11px arial, tahoma, helvetica, sans-serif;
}

.x-list-over {
    background-color:#eee;
}

.x-list-selected {
    background-color:#dfe8f6;
}

.x-list-resizer {
    border-left-color:#555;
    border-right-color:#555;
}

.x-list-header-inner em.sort-asc, .x-list-header-inner em.sort-desc {
    background-image:url(../images/default/grid/sort-hd.gif);
    border-color: #99bbe8;
}.x-slider-horz, .x-slider-horz .x-slider-end, .x-slider-horz .x-slider-inner {
    background-image:url(../images/default/slider/slider-bg.png);
}

.x-slider-horz .x-slider-thumb {
    background-image:url(../images/default/slider/slider-thumb.png);
}

.x-slider-vert, .x-slider-vert .x-slider-end, .x-slider-vert .x-slider-inner {
    background-image:url(../images/default/slider/slider-v-bg.png);
}

.x-slider-vert .x-slider-thumb {
    background-image:url(../images/default/slider/slider-v-thumb.png);
}.x-window-dlg .ext-mb-text,
.x-window-dlg .x-window-header-text {
    font-size:12px;
}

.x-window-dlg .ext-mb-textarea {
    font:normal 12px tahoma,arial,helvetica,sans-serif;
}

.x-window-dlg .x-msg-box-wait {
    background-image:url(../images/default/grid/loading.gif);
}

.x-window-dlg .ext-mb-info {
    background-image:url(../images/default/window/icon-info.png);
}

.x-window-dlg .ext-mb-warning {
    background-image:url(../images/default/window/icon-warning.png);
}

.x-window-dlg .ext-mb-question {
    background-image:url(../images/default/window/icon-question.png);
}

.x-window-dlg .ext-mb-error {
    background-image:url(../images/default/window/icon-error.png);
}