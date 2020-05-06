(function() {
    tinymce.create("tinymce.plugins.button_ware_asl", {
        init : function(ed) {
            ed.addButton("button_ware_asl", {
                text: 'Товары',
                onclick: function() {
                    setTimeout(()=>jQuery('#wares_search').focus(), 500);
                    jQuery(this).magnificPopup({
                        items: {
                            src: '#popup',
                            type: 'inline',
                            alignTop: true,

                        },
                    }).magnificPopup('open');
                },

            });
            let elements = document.querySelectorAll('.select_wares');
            console.log(elements.length);
            Array.from(elements).forEach(function (element) {
                element.addEventListener('click', function (e) {
                    let elem = e.target;
                    let id = elem.parentNode.id;
                    let return_text = '[ware_item id=' + id + '][/ware_item]';
                    ed.execCommand("mceInsertContent", 0, return_text);
                    document.querySelector('#wares_search').value= '';
                    jQuery.magnificPopup.close();
                })
            });
            return false;
        },

        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add("button_ware_asl", tinymce.plugins.button_ware_asl);

    document.querySelector('#wares_search').oninput= function () {
        let val = this.value.trim();
        let items = document.querySelectorAll('tbody .tb_second');

        if (val != '') {
            items.forEach(function (item) {
                let itemLow = item.innerHTML.toLowerCase();
                if (itemLow.search(val) == -1)
                {
                    item.parentNode.classList.add('hide');
                    item.classList.remove('vis');
                }else{
                    item.parentNode.classList.remove('hide');
                    item.classList.add('vis');
                }
            });
        }else{
            items.forEach(function (item) {
                item.parentNode.classList.remove('hide');
                item.classList.add('vis');

            });
        }
        let wordForm = function(num,word){
            let cases = [2, 0, 1, 1, 1, 2];
            return word[ (num%100>4 && num%100<20)? 2 : cases[(num%10<5)?num%10:5] ];
        }

        let count = document.querySelectorAll('.vis').length;
        let text = wordForm(count, ['Найден ', 'Найдено ', 'Найдено '])+ count + wordForm(count, [' товар',' товара',' товаров',]);
        jQuery('.small-right').text(text);
        if(count == 0){
            jQuery('#popup table').fadeOut();
        }else{
            jQuery('#popup table').fadeIn();
        }
    }

})();
