CKEDITOR.plugins.add('cancelsave',
    {
        init: function(editor)
        {
            var pluginName = 'cancelsave';
            editor.addCommand( pluginName,
            {
                exec : function( editor )
					{
					
					removeEditor();
                },
                canUndo : true
            });
            editor.ui.addButton('Cancelsave',
            {
                label: 'cancel Save',
				icon: this.path+'icons/cancel.png',
                command: pluginName
              //  className : 'cke_button_cancel'
								
            });
        }
    });