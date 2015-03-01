CKEDITOR.editorConfig = function( config ) {

	 //config.uiColor = '#FFFF00';
 	 config.extraPlugins = 'autogrow,ajaxsave,cancelsave,insertpre';
	 config.autoGrow_onStartup = true;
	 config.autoGrow_minHeight = 150;
	 //config.removePlugins =  'resize';  
	config.toolbar = [ [ 'Ajaxsave','Cancelsave','-','Undo','Redo','PasteFromWord','-','Bold', 'Italic','Underline','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],['BulletedList','-','Outdent','Indent','-','RemoveFormat'],['Font','Format'],['FontSize','TextColor'],['Link','Unlink'],['Image','Table'],['Source'],['InsertPre']];
	CKEDITOR.config.insertpre_class = 'code';
};