/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	CKEDITOR.config.toolbar_Full =
/*[
	{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
	{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
	{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 
 
         'HiddenField' ] },
	'/',
	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
	{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
	{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
	'/',
	{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
	{ name: 'colors', items : [ 'TextColor','BGColor' ] },
	{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About','-','asistevar' ] }
];*/
[
	{ name: 'document', items : [ 'Source','Preview','Print'] },
	{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','Undo','Redo' ] },
	{ name: 'editing', items : [ 'Find','Replace','SelectAll','SpellChecker', 'Scayt' ]},
	{ name: 'insert', items : [ 'Image','Table','HorizontalRule','SpecialChar','PageBreak'] },	
	{ name: 'tools',  items : ['asistevar'] },
	'/',
	{ name: 'styles', items : ['Font','FontSize' ]},
	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike']},
	{ name: 'colors', items : ['TextColor','BGColor' ] },
	{ name: 'paragraph', items : [ 'NumberedList','BulletedList','Outdent','Indent','CreateDiv','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] }

];
	config.toolbar 						= 'Full';
	config.startupOutlineBlocks 		= true;
	config.extraPlugins					= "asistevar";
	config.skin							= 'chris';
	config.entities						= 'false';
	config.enterMode					= CKEDITOR.ENTER_BR;
	config.shiftEnterMode				= CKEDITOR.ENTER_DIV;
	config.removePlugins 				= 'resize';
	//config.filebrowserBrowseUrl		= '/misc/ckeditor/plugins/pdwfilebrowser/index.php?editor=ckeditor';
	//config.filebrowserImageBrowseUrl 	= '/misc/ckeditor/plugins/pdwfilebrowser/index.php?editor=ckeditor&filter=image';
	//config.filebrowserFlashBrowseUrl 	= '/misc/ckeditor/plugins/pdwfilebrowser/index.php?editor=ckeditor&filter=flash';
	//config.filebrowserUploadUrl 		= '/misc/ckeditor/plugins/pdwfilebrowser/swfupload/upload.php';
	//config.filebrowserBrowseUrl = '/ckfinder/ckfinder.html';
	
	CKEDITOR.plugins.load('pgrfilemanager');
	
};
