CKEDITOR.plugins.add('ajaxsave',
    {
		lang: 'af,ar,bg,bn,bs,ca,cs,cy,da,de,el,en-au,en-ca,en-gb,en,eo,es,et,eu,fa,fi,fo,fr-ca,fr,gl,gu,he,hi,hr,hu,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt-br,pt,ro,ru,sk,sl,sq,sr-latn,sr,sv,th,tr,ug,uk,vi,zh-cn,zh', // %REMOVE_LINE_CORE%
		//icons: 'save', // %REMOVE_LINE_CORE%
    init: function(editor)
        {
          var pluginName = 'ajaxsave';
          editor.addCommand( pluginName,
          {
            exec : function( editor )
						{
							ajax('Article/editCell/'+ editor.id, {
							value: editor.getData()
							});
							removeEditor();
            },
            canUndo : true
          });
            editor.ui.addButton('Ajaxsave',
            {
                label: editor.lang.save.toolbar,
								icon: this.path+'icons/save.png',
                command: pluginName
               // className : 'cke_button_save'
            });
        }
    });