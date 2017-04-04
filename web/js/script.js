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
    var index                   = $container.find(':input').length;
    var minDate;

    /*************************************************
     * Changement de la langue du site
     ***********************************************************/
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

    // On cache le bouton scroll si l'utilisateur est en haut de la page
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('#gotoTop').fadeIn();
        } else {
            $('#gotoTop').fadeOut();
        }
    });

    /*************************************************
     * Gestion de l'interface de la réservation des tickets
     ***********************************************************/

    /*
    Gestion de la date de la visite
     */

    // On vérifie si on peut encore réserver des billets pour aujourd'hui
    if(DateOfToday.get('hours') >= maxHoursToOrder) {
        minDate = DateOfToday.add(1,'days');
    }else{
        minDate = DateOfToday;
    }

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

        // Dans le cas ou l'utilisateur a choisi une date,
        // on vérifie que le musée n'est pas complet
        $.post( _path_web_quota, { date: date.format('YYYY-MM-DD') })
            .done(function( data ) {
                if(!data.errorCode)
                    if(data.availability){
                        $resumeDateContainer.html(date.format('Do MMMM YYYY'));
                        $ticketControl.removeAttr('disabled');
                        // On ajuste le nombre de billets disponibles en fonction de la capacité restante
                        for(i = 2; i <= data.remaining_purchase_item; i++){
                            $ticketControl.append('<option value="'+i+'">'+i+'</option>');
                        }
                    }else{
                        $datetimepicker.find('input').val('');
                        $ticketControl.attr('disabled', 'disabled');
                        swal({ title: '', html : _over_quota, type :'error' });
                    }
            });
    });

    /*
    Gestion du champs du nombre de billets
     */
    $ticketControl.on('change', function(e){
        $resumeTicketContainer.html('X'+this.value);

        // On génére aussi le formulaire pour l'ajout des billets
        // en évitant d'écraser tous les billets
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

    /*
     Gestion du type de billets
     */
    $('input[type=radio]').iCheck({radioClass: 'iradio_square-grey'})
        .on('ifChecked', function(event){
            (this.value == 1) ? $resumeHoursContainer.html(_full_hours) : $resumeHoursContainer.html(_half_hours);
    });

    /*************************************************
     * Forulaire selection des visiteurs
     ***********************************************************/
    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un.
    if (index == 0) {
        addVisitor();
    }

    // Creation d'un nouveau formulaire pour l'ajout d'un visiteur
    function addVisitor() {
        var template = $container.attr('data-prototype');
        template.replace(/__name__label__/g, 'Catégorie n°' + (index+1));
        template.replace(/__name__/g, index);

        var $prototype = $(template);
        $container.append('<div class="ticket_visitor"></div>');
        $lastContainer = $('div.ticket_visitor').last();
        $lastContainer.append('<h4>#'+(index+1)+'</h4>');
        $lastContainer.append($prototype);
        index++;

        // Changement du style de la checkbox
        $('input[type=checkbox]').iCheck({
            checkboxClass: 'icheckbox_square-grey',
            radioClass: 'iradio_square-grey'
        }).on('ifChanged', function(event){
            //alert(event.type + ' callback');
        });
    }

    // Supression du dernier billet visiteurs créer.
    function deleteVisitor(){
        $('div.ticket_visitor').last().remove();
        index--;
    }
});
