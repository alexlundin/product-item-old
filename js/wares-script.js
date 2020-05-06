const url = 'https://' + window.location.hostname;
const mainLink = url + '/wp-json/wp/v2/wares/';
const image = url + '/wp-json/wp/v2/media/';
const elementsWares = document.querySelectorAll(".ajax-content");
const idWares = [];
const idList = [];

elementsWares.forEach((item, i, elementsWares) => {
    idWares.push(item.getAttribute('id'));
    idList.push(item.getAttribute('data-id'));
});

for (let i = 0; i < idWares.length; i++) {
    let data = {
        action: "wares",
        id: idList[i],
    };

    jQuery.post(wares_ajax.wares_url, data, function (response) {
        jQuery('#' + idWares[i]).html(response);
    })
}