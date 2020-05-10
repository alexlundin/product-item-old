const url = 'https://' + window.location.hostname;
const mainLink = url + '/wp-json/wp/v2/wares/';
const image = url + '/wp-json/wp/v2/media/';
const elementsWares = document.querySelectorAll(".ajax-content");
const idList = [];
elementsWares.forEach((item, i, elementsWares) => {
    idList.push(item.getAttribute('data-id'));
});

for (let i = 0; i < idList.length; i++) {
    let data = {
        action: "wares",
        id: idList[i],
    };

    jQuery.post(wares_ajax.wares_url, data, function (response) {
        jQuery('div[data-id="'+ idList[i] +'"]').html(response);
    })
}