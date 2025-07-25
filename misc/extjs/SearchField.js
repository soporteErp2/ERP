/*
 * Ext JS Library 2.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

Ext.app.SearchField = Ext.extend
(
Ext.form.TwinTriggerField, 
{
    initComponent : function()
	{
        Ext.app.SearchField.superclass.initComponent.call(this);
        this.on('specialkey', function(f, e)
		{
            if(e.getKey() == e.ENTER)
			{
                this.onTrigger2Click();
            }
        }, this);
		//this.on('keydown', this.onTrigger2Click, this, false);
    },

    validationEvent:false,
	selectOnFocus: true,
    validateOnBlur:false,
    trigger1Class:'x-form-clear-trigger',
    trigger2Class:'x-form-search-trigger',
    hideTrigger1:true,
    width:180,
    hasSearch : false,
    paramName : 'query',
	paramName2 : 'filtro2',

    onTrigger1Click : function(){
        if(this.hasSearch){
            this.el.dom.value = '';
            var o = {start: 0};
            this.store.baseParams = this.store.baseParams || {};
            this.store.baseParams[this.paramName] = '';
            this.store.reload({params:o});
            this.triggers[0].hide();
            this.hasSearch = false;
        }
    },

    onTrigger2Click : function(){
        var v = this.getRawValue();
		if(apuntador_este_gridraro == 1)
		{
			var d = campo_filtro.getValue(); 
			this.store.baseParams[this.paramName2] = d;
		}
        if(v.length < 1){
            this.onTrigger1Click();
            return;
        }
        var o = {start: 0};
        this.store.baseParams = this.store.baseParams || {};
        this.store.baseParams[this.paramName] = v;
        this.store.reload({params:o});
        this.hasSearch = true;
        this.triggers[0].show();
    }
});