jQuery(document).ready( function($) {
    jQuery(document).foundation();
        
    $(".js-register-next").click( function() {
        $(".register__slider__item:eq(0)").css('margin-left','-100%');
    });
    

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
    
    
    var v = document.getElementById('js-video');
    v.onended = function() {
        //le formulaire est présent
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
    else {alert("La videoest finie et pas de requete ajax.");}
        $('#videoModal').foundation('reveal', 'close');
    };
    
    $('.js-launchvideo').on('click', function() {
        $('#videoModal').foundation('reveal', 'open');
        $('#videoModal').css('top','0px');
        v.play();
    });
    
    
});
