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
    var $payment_action         = $('#payment_action');
    var $payment_form           = $('#payment_form');
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
     * Initialisation de la page
     ***********************************************************/
    // Chargement des styles dynamique
    $('input[type=checkbox]').iCheck({ checkboxClass: 'icheckbox_square-grey'});

    /*************************************************
     * Gestion de l'interface de la réservation
     ***********************************************************/
    if(_initLoader)
        _init_booking();

    /**
     * Chargement des éléments necessaire à la commande de billets
     *
     * @private
     */
    function _init_booking(){

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

        // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un.
        if (index == 0) {
            addVisitor();
        }
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
    });


    /*************************************************
     * Gestion de l'interface du paiement
     ***********************************************************/

    /*==================
     Initialisation du formulaire
     ==================================*/
    Stripe.setPublishableKey('pk_test_AeQt3sCiUWeWsBr0BdQKDfZ5');

    $('input[name=card_number]').formatter({ 'pattern': '{{9999}} {{9999}} {{9999}} {{9999}}'});
    $('input[name=ccv]').formatter({ 'pattern': '{{999}}' });

    /*==================
     Traduction du message de retour de stripe qui ne fournit pas de changement de langue
     ==================================*/
    if(_locale == 'fr'){
        var errorMessages = {
            incorrect_number: "Le numéro de carte est incorrect.",
            invalid_number: "Le numéro de carte n'est pas un numéro de carte de crédit valide.",
            invalid_expiry_month: "Le mois d'expiration de la carte est invalide.",
            invalid_expiry_year: "L'année d'expiration de la carte est invalide.",
            invalid_cvc: "Le code de sécurité de la carte n'est pas valide.",
            expired_card: "La carte a expiré.",
            incorrect_cvc: "Le code de sécurité de la carte est incorrect.",
            incorrect_zip: "Le code postal de la carte a échoué à la validation.",
            card_declined: "La carte a été refusée.",
            missing: "Il n'y a pas de carte sur un client qui est chargé",
            processing_error: "Une erreur s'est produite lors du traitement de la carte.",
            rate_limit:  "Une erreur s'est produite en raison des requêtes qui ont frappé l'API trop rapidement. Veuillez nous informer si vous rencontrez systématiquement cette erreur.",
            invalid_email: "L'adresse email est invalide"
        };
    }else{
        var errorMessages = {
            incorrect_number: "The card number is incorrect.",
            invalid_number: "The card number is not a valid credit card number.",
            invalid_expiry_month: "The card's expiration month is invalid.",
            invalid_expiry_year: "The card's expiration year is invalid.",
            invalid_cvc: "The card's security code is invalid.",
            expired_card: "The card has expired.",
            incorrect_cvc: "The card's security code is incorrect.",
            incorrect_zip: "The card's zip code failed validation.",
            card_declined: "The card was declined.",
            missing: "There is no card on a customer that is being charged.",
            processing_error: "An error occurred while processing the card.",
            rate_limit:  "An error occurred due to requests hitting the API too quickly. Please let us know if you're consistently running into this error.",
            invalid_email: "Email address is invalid"
        };
    }

    /*==================
     On va déclencher la validation du formulaire de payment
     ==================================*/
    $payment_action.on('click',function(e){
        $payment_form.submit();
    });

    /*==================
     Gestion de la soumission du formulaire
     ==================================*/
    $payment_form.on('submit', function(e){
        e.preventDefault();
        var message;

        $payment_action.attr('disabled', true);

        if(validateEmail($payment_form.find('input[name=email]').val())){
            Stripe.card.createToken($payment_form , function(status, response){
                if(response.error){
                    message = (response.error.type == 'card_error') ? errorMessages[ response.error.code ] : response.error.message;
                    displayErrorMessage(message);
                }else{
                    var token = response.id;
                    $payment_form.append('<input type="hidden" name="stripeToken" value="'+token+'">');
                    $payment_form.get(0).submit();
                }
            });
        }else{
            displayErrorMessage(errorMessages['invalid_email']);
        }


    });

    /**
     *  Vérifie si une adrese email est valide
     *
     * @param email
     * @returns {boolean}
     */
    function validateEmail($email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test( $email );
    }

    /**
     * Affiche un message d'erreur pour la page
     *
     * @param $message
     */
    function displayErrorMessage(message){
        $payment_form.find('.alert').remove();
        $payment_form.prepend('<div class="alert alert-danger"><ul class="list-unstyled"><li><span class="glyphicon glyphicon-exclamation-sign"></span>'+ message +'</li></ul>');
        $payment_action.attr('disabled', false);
    }






});
