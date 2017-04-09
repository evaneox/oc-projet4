/**
 Script JS pour blog
 auteur: Jérémy BOUHOUR
 **/
jQuery(function($) {

    /*************************************************
     * Déclaration des variables
     ***********************************************************/
    var $langSwitcher           = $('#lang');
    var maxHoursToOrder         = 14;
    var DateOfToday             = moment();
    var $datetimepicker         = $('#datetimepicker');
    var $resumeDateContainer    = $('.resume_date');
    var $resumeTicketContainer  = $('.resume_ticket');
    var $ticketControl          = $('#ticketCounterControl');
    var $resumeHoursContainer   = $('.resume_hours');
    var $container              = $('div#tickets_visitors');
    var $ticketZone             = $('div.ticket_visitor');
    var index                   = $container.find(':input').length;
    var $bookingAction          = $('#booking_action');
    var minDate;
    var oldDate;
    var lastAvailableDate;

    /*************************************************
     * Changement de la langue du site
     ***********************************************************/
    if(_locale == ""){
        _locale = "fr";
    }

    // Redirection vers la même page avec une autre langue
    $langSwitcher.on('change', function() {
        $(location).attr('href', this.value);
    });

    // On cache le bouton scroll quand le visiteur est en haut de la page
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('#gotoTop').fadeIn();
        } else {
            $('#gotoTop').fadeOut();
        }
    });

    /*************************************************
     * Scroll to top
     ***********************************************************/
    $('#gotoTop').on('click',function(e){
        $('html, body').animate({scrollTop : 0},800);
        return false;
    });

    // Le bouton scroll n'est pas affiché si le visiteur et en haut de la page
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('#gotoTop').fadeIn();
        } else {
            $('#gotoTop').fadeOut();
        }
    });

    /*************************************************
     * Gestion de l'interface de la réservation
     ***********************************************************/

    /*==================
     Initialisation de la page
     ==================================*/
    // On change le théme pour le champs checkbox
    $('input[type=checkbox]').iCheck({ checkboxClass: 'icheckbox_square-grey'});

    var entryDateValue = $datetimepicker.find('input').val();
    var ticketTypeValue = $("input[type='radio']:checked").val();

    (ticketTypeValue == 1) ? $resumeHoursContainer.html(_full_hours) : $resumeHoursContainer.html(_half_hours);

    // Lors de la soumission du formulaire et en cas d'erreur on met quand même à jours les informations
    // pour avoir une cohérence avec la page précédente.
    if(entryDateValue){
        var date = moment(entryDateValue).locale(_locale);
        var ticketNumber            = $ticketZone.length;
        preCheckDate(date,ticketNumber);
    }

    /*==================
     Gestion de l'interface pour la date de réservation
     ==================================*/
    // On ne peut pas réserver apres 14h00
    minDate = (DateOfToday.get('hours') >= maxHoursToOrder) ? minDate = DateOfToday.add(1,'days') : DateOfToday;

    // On peut maintenant créer le calendrier pour la selection de la date de la visite
    // avec les critéres suivants :
    // 1. On retire les date antérieurs
    // 2. On ne peut pas réserver aujourd'hui à partir de 14:00
    // 3. Fermeture du musée le mardi et dimanche
    // 4. Fermeture du musée le 1er mai, 1er novembre et 25 décembre
    // 5. Réservation uniquement possible sur une année

    $datetimepicker.datetimepicker({
        ignoreReadonly: true,
        locale: _locale,
        format: 'YYYY-MM-DD',
        useCurrent: false,
        minDate: minDate,
        maxDate: moment().subtract(1,'days').add(1,'years'),
        daysOfWeekDisabled: [2, 0],
        disabledDates: [
            moment([ moment().get('years'), 4, 1]),
            moment([ moment().add(1, 'years').get('years'), 4, 1]),
            moment([ moment().get('years'), 10, 1]),
            moment([ moment().add(1, 'years').get('years'), 10, 1]),
            moment([ moment().get('years'), 11, 25]),
            moment([ moment().add(1, 'years').get('years'), 11, 25]),
        ]
    }).on('dp.change', function(e){
        var date            = e.date;

        // On peut maintenant effectuer une requete ajax pour vérifier si la date est valide
        if(oldDate !== $datetimepicker.find('input').val()){
            oldDate         = $datetimepicker.find('input').val();
            console.log(oldDate);
            preCheckDate(date, $ticketControl.val());
        }
    });

    /*==================
     Gestion de la sélection du nombre de billets
     ==================================*/
    $ticketControl.on('change', function(e){
        $resumeTicketContainer.html('X'+this.value);

        // On génére aussi le formulaire pour l'ajout des billets en évitant d'écraser tous les billets
        var n = $('div.ticket_visitor').length;

        if(this.value > n){
            for(i = n+1; i <= this.value; i++){
                addVisitor();
            }
        }else if(this.value < n){
            for(i = this.value ; i < n; i++){
                deleteVisitor();
            }
        }
    });

    /*==================
     Gestion de la sélection du type de billets
     ==================================*/
    $('input[type=radio]').iCheck({radioClass: 'iradio_square-grey'})
        .on('ifChecked', function(event) {
            if(this.value == 1) {
                $resumeHoursContainer.html(_full_hours);
                $('input[type=checkbox]').iCheck('enable');
            } else{
                $resumeHoursContainer.html(_half_hours);
                $('input[type=checkbox]').iCheck('disable');
            }
        });

    /**
     * Vérification dynamique de la date de réservation
     * @param date
     * @param defautNumberTicket
     */
    function preCheckDate(date, defautNumberTicket){

        // Dans le cas ou l'utilisateur a choisi une date, on vérifie que le musée n'est pas complet
        $.post( _path_web_quota, { date: date.format('YYYY-MM-DD'), tickets: defautNumberTicket})
            .done(function( data ) {
                if(!data.errorCode)
                // la réservation n'est pas complète
                    if(data.availability){
                        var selected = (defautNumberTicket === 1) ? 'selected="selected"' : '';
                        $ticketControl.html('<option value="1" '+selected+'>1</option>');
                        // On ajuste le nombre de billets disponibles en fonction de la capacité restante
                        for(i = 2; i <= data.remaining_purchase_item; i++){
                            var selected = (i === defautNumberTicket) ? 'selected="selected"' : '';
                            $ticketControl.append('<option value="'+i+'" '+selected+'>'+i+'</option>');
                        }
                        lastAvailableDate = date.format('YYYY-MM-DD');
                        $ticketControl.val(defautNumberTicket);
                        $ticketControl.removeAttr('disabled');
                        $resumeDateContainer.html(date.format('Do MMMM YYYY'));
                        $resumeTicketContainer.html('X'+defautNumberTicket);
                    }
                    // Dans le cas contraire
                    else{
                        $datetimepicker.find('input').val('');
                        $ticketControl.attr('disabled', 'disabled');
                        swal({ title: '', html : _over_quota, type :'error' });
                    }
            });
    }

    /*==================
     Gestion de l'interface des visiteurs
     ==================================*/
    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un.
    if (index == 0) {
        addVisitor();
    }

    /**
     * Génére un formulaire pour l'ajout d'un nouveau visiteurs
     *
     * @return void
     */
    function addVisitor() {
        var template = $container.attr('data-prototype')
                .replace(/__name__/g,        index);

        var $prototype = $(template);
        $container.append($prototype);
        $('input[type=checkbox]').iCheck({ checkboxClass: 'icheckbox_square-grey'})
        index++;

    }

    /**
     * Supression du dernier formulaire créer pour un visiteur
     *
     * @return void
     */
    function deleteVisitor(){
        $('div.ticket_visitor').last().remove();
        index--;
    }

    /*==================
     Gestion de la soumission du formulaire
     ==================================*/
    $bookingAction.on('click',function(e){
        $('#order_process').find('form').submit();
    })
});
