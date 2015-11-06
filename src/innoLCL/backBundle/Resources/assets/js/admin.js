$(document).ready( function () {
var formtr;

function loadingchangeStatut() {
    val_statut = $("#form_statuts").val();
    $("span.icone-validation").removeClass("choisi");
    if(val_statut == "notset") {
        console.log("do nothing");
    }
    else {
        $("span.icone-validation[data-val='"+val_statut+"']").addClass("choisi");
    }
    console.log(val_statut);
}

function getAjax(url) {
    $.ajax({
            type: "POST",
            url: action
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $("#formcontainer").html(data.HTMLcontent);
                $("#trcontainer").slideDown();
                loadingchangeStatut();
                console.log(data.message);
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    $('#form_body').html(jqXHR.responseJSON.form);
                    console.log(jqXHR.responseJSON.form);
                }
                $('.form_error').html(jqXHR.responseJSON.message);
                 console.log(jqXHR.responseJSON.message);
 
            } else {
                alert(errorThrown);
            }
 
        });    
}

       $("#idealist").on("click","span.icone-validation", function() {
            val_statut = $(this).attr("data-val");
            $("span.icone-validation").removeClass("choisi");
            $(this).addClass("choisi");
            $("#form_statuts").val(val_statut);
            $("#idealist").find("#form_save").trigger("click");
        });

        $("#idealist").on("click","tr.js-getidea", function() {
                $("tr.trcontainer").remove();            
                if($(this).hasClass("active")) {
                    $(this).removeClass("active");
                }
                else {
                    currenttr = $(this);
                    $(this).addClass("active");
                    action = currenttr.attr("data-action");
                    $("#idealist tr.active").after('<tr class="trcontainer"><td colspan="7" id="formcontainer"></td></tr>');
                    getAjax(action);                
                }
        });
        
        $("#idealist").on("submit","form", function(e) {
            if($(this).hasClass("js-validateur")) {
                if($("#form_statuts").val() == "refused") {
                    var motifdurefus = prompt("Merci de donner un motif au refus.", "");
                    $("#form_refusalreason").val(motifdurefus);
                }
                
            }
            
            e.preventDefault();
            $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $("tr.trcontainer").remove();
                $("tr.active").remove();
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    //$(this).replaceWith(jqXHR.responseJSON.form);
                }
 
                $('.form_error').html(jqXHR.responseJSON.message);
            } else {
                alert(errorThrown);
            }
 
        });
        });
});
