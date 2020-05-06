(function() {
    var postsValuesRound = [];
    jQuery.each(postsValues_round_button, function(key, value) {
        postsValuesRound.push({text:value, value:key});
    });
    tinymce.create("tinymce.plugins.button_round_item", {
        init : function(ed, url) {
            ed.addButton("button_round_item", {
                type: 'listbox',
                text: 'Преимущества',
                values: postsValuesRound,
                onselect: function(e) {

                    var selected_text = ed.selection.getContent();
                    var v = this.value();
                    var return_text = '[rounds id=' + v + ']' + selected_text + '[/rounds]';
                    ed.execCommand("mceInsertContent", 0, return_text);
                }
            });
        },

        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add("button_round_item", tinymce.plugins.button_round_item);
})();