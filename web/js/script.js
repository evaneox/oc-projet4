/**
 Script JS pour blog
 auteur: Jérémy BOUHOUR
 **/
jQuery(function($) {

    /** Initialisation des variables  **/
    var langSwitcher = $('#lang-switcher');

    langSwitcher.on('change', function() {
        alert( this.value );
    });


    //Check to see if the window is top if not then display button
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('#gotoTop').fadeIn();
        } else {
            $('#gotoTop').fadeOut();
        }
    });

    //Click event to scroll to top
    $('#gotoTop').click(function(){
        $('html, body').animate({scrollTop : 0},800);
        return false;
    });


    $('input').iCheck({
        checkboxClass: 'icheckbox_square-grey',
        radioClass: 'iradio_square-grey'
    });
});
