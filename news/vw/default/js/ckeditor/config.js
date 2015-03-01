/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	 //config.uiColor = '#FFFF00';
 	 config.extraPlugins = 'autogrow,ajaxsave,cancelsave';
	 config.autoGrow_onStartup = true;
	 config.autoGrow_minHeight = 150;
	// config.removePlugins: 'resize';  
	config.toolbar = [ [ 'Ajaxsave','Cancelsave','-','Undo','Redo','PasteFromWord','-','Bold', 'Italic','Underline','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],['BulletedList','-','Outdent','Indent','-','RemoveFormat'],['Font'],['FontSize','TextColor'],['Link','Unlink'],['Image','Table']];
		config.customConfig = 'custom-config.js'
};
