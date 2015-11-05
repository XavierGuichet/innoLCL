jQuery(document).ready( function($) {
    //Prepare la box de popin et celle de la video
    jQuery(document).foundation();
        
    
    //Trigger register form pour slide        /!\ Retour au step 1 à rajouter en cas d'erreur de validation par le client.
    $(".js-register-next").click( function(e) {
        e.preventDefault();
        /*
        // prevalidation du formulaire
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            
        });
        */
        // retour Ajax positif = slide charte sinon affichage erreurs
        $(".register__slider__item:eq(0)").css('margin-left','-100%');
    });
    
    //Soumission des forms en ajax à transformer en function AjaxThisForm(form, callback)
    $("#formIdea").on("submit","form", function(e) {
            e.preventDefault();
            $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $("#suggest_idea_front").replaceWith(data.succespopup);
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    $("#suggest_idea_front").replaceWith(jqXHR.responseJSON.form);
                    console.log(jqXHR.responseJSON.message);
                }
            } 
            else {
                console.log(errorThrown);
            } 
        });
    });
    
    
    if($('#js-video').length>0){
        var v = document.getElementById('js-video'); // /!\ A généré une erreur sur une page.
        v.onended = function() { //event de fin de lecture de la vidéo
            //le formulaire est présent, le formulaire n'est présent que quand la personne n'a pas vu la vidéo en entier.
            if($('#js-videoform').length == 1) {
                $("#form_videoseenon").val(true);
                $.ajax({
                    type: $('#js-videoform').attr('method'),
                    url: $('#js-videoform').attr('action'),
                    data: $('#js-videoform').serialize()
                })
                .done(function (data) {
                if (typeof data.message !== 'undefined') {
                    location.reload();
                }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    if (typeof jqXHR.responseJSON !== 'undefined') {
                        if (jqXHR.responseJSON.hasOwnProperty('message')) {
                            console.log(jqXHR.responseJSON.message);
                        }
                    } 
                    else {
                        console.log(errorThrown);
                    }
                });
            }
            else {
                alert("La videoest finie et pas de requete ajax.");
            }
            $('#videoModal').foundation('reveal', 'close');
        };
    }
    
    $('.js-launchvideo').on('click', function() {
        $('#videoModal').foundation('reveal', 'open');
        $('#videoModal').css('top','0px'); // Test d'ajustement du dialog modal
        v.play();
    });
});
