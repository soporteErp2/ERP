(function(){
	 var a = {
			 exec:function(editor){
				var myalto2  = Ext.getBody().getHeight();
				WinVarAsiste = new Ext.Window
				(
					{
						title		: 'Variables del Sistema',
						id			: 'WinVarAsiste',
						width 		: 400,
						height 		: myalto2 - 20,
						modal		: true,
						autoDestroy : true,
						autoScroll	: true,
						bodyStyle   : 'background-color:#FFF;',
						autoLoad:
						{
							url		:  'asistevar.php',
							scripts	: true,
							nocache	: true
						}
					}
				).show();
			}
		},
		b = "asistevar";
		CKEDITOR.plugins.add(
			b,
			{
				init	:	function(editor){
					editor.addCommand(b,a);
					editor.ui.addButton(
						b,
						{
							label	:	"Variables del Sistema",
							icon	: 	this.path + "icon.png",
							command	:	b
						}
					);
				}
			}
		);
	}
)();