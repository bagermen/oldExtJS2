Ext.onReady(function() {
    var panel = Ext.create({
        xtype: 'panel',
        title: 'Test grid',
        height: window.innerHeight,
        layout: {
            type: 'vbox',
            pack: 'start',
            align: 'stretch'
        },
        items: [
            {
                xtype: 'box',
                html: 'Disabled auto save mode! <br>1. To create new record: push "create" button, fill fields then push "save" button<br>2.To update record: modify data then push "save" button',
                height: 80
            },
            {
                xtype: 'app_grid',
                flex: 1
            }
        ],
        renderTo: 'body'
    });

    window.onresize = function(event) {
        panel.setHeight(window.innerHeight);
        panel.setWidth(window.innerWidth);
        panel.doLayout();
    };
    panel.render();
});



