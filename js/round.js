var t2 = document.getElementsByClassName("text_second"),
    t3 = document.getElementsByClassName("text_third"),
    c2 = document.getElementsByClassName("color_second"),
    c3 = document.getElementsByClassName("color_third"),
    t1 = document.getElementsByClassName("text_first"),
    c1 = document.getElementsByClassName("color_first");

var manageradiorel = jQuery("#count:checked").val();
if (manageradiorel == 1){
    jQuery(t2).hide();
    jQuery(t3).hide();
    jQuery(c2).hide();
    jQuery(c3).hide();
}else if (manageradiorel == 2){
    jQuery(t2).show();
    jQuery(t3).hide();
    jQuery(c2).show();
    jQuery(c3).hide();
}else {
    jQuery(t2).show();
    jQuery(t3).show();
    jQuery(c2).show();
    jQuery(c3).show();
}

// document.getElementsByClassName('count3').checked = true;

var check = document.querySelectorAll('input[type="radio"]');
for (var i=0;i<check.length;i++){
    check[i].addEventListener('click', function(event) {
        if(this.value == 1){
            jQuery(t2).hide();
            jQuery(t3).hide();
            jQuery(c2).hide();
            jQuery(c3).hide();
        }else if (this.value == 2){
            jQuery(t2).show();
            jQuery(t3).hide();
            jQuery(c2).show();
            jQuery(c3).hide();
        }else {
            jQuery(t2).show();
            jQuery(t3).show();
            jQuery(c2).show();
            jQuery(c3).show();
        }

    });
}
