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
    
    
    
    
});
