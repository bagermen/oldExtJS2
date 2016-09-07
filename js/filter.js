Ext.define('App.Filter', {
    extend: 'Ext.Container',
    grid: {},
    layout: {
        type: 'hbox',
        flex: 1
    },

    initComponent: function() {
        this.items = this.getItemsCfg();
        App.Filter.superclass.initComponent.call(this);
    },

    getItemsCfg: function() {
        var cmp = this;
        var items = [];
        if (this.grid) {
            Ext.each(this.grid.columns, function(col) {
                items.push({
                    xtype: 'checkbox',
                    boxLabel: col.header,
                    name: col.dataIndex
                })
            }, this);
        }
        var menu = Ext.create({
            xtype: 'menu',
            items: items
        });
        return [
            {
                xtype: 'button',
                text: 'Select',
                menu: menu,
                itemId: 'menu'
            },
            new Ext.form.TwinTriggerField({
                itemId: 'text',
                flex: 1,
                trigger1Class : 'x-form-clear-trigger',
                trigger2Class : 'x-form-search-trigger',
                enableKeyEvents : true,
                onTrigger1Click : function() {
                    menu.items.each(function(item) {
                        item.reset();
                    });
                    this.setValue("");
                },
                onTrigger2Click : function() {
                    cmp.search();
                },
                listeners: {
                    'keypress': function(c, e) {
                        if (e.getKey() == e.ENTER) {
                            cmp.search();
                        }
                    }
                }
            })
        ]
    },

    search: function() {
        this.grid.fireEvent('search');
    },

    getValues: function() {
        var menu = this.getComponent('menu');
        var text = this.getComponent('text');
        var data = {text: "", fields: []};
        if (menu && text) {
            data.text = text.getValue();
            menu.menu.items.each(function(item) {
                if (item.checked) {
                    data.fields.push(item.name);
                }
            });
        }
        data.fields = Ext.encode(data.fields);
        return data;
    }
});

Ext.reg('app_filter', App.Filter);