function BuscarFuncionario(id_field,field){

	//var myalto  = Ext.getBody().getHeight();
	//var myancho  = Ext.getBody().getWidth();

 	Win_BuscarFuncionario = new Ext.Window({
		id			: 'Win_BuscarFuncionario',
		width		: 600,
		height		: 450,
		/*boxMaxHeight: 550,*/
		title		: 'Buscar Funcionario' ,
		iconCls 	: 'user16',
		modal		: true,
		autoScroll	: true,
		closable	: true,
		autoDestroy : true,
		autoLoad	:
		{
			url		: '../funcionarios/busqueda_funcionarios.php',
			scripts	: true,
			nocache	: true,
			params	:
			{
				id_field : id_field,
				field    : field
			}
		}
	}).show();
}