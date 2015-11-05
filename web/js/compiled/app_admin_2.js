$(document).ready( function () {
var formtr;

function getAjax(url) {
    $.ajax({
            type: "POST",
            url: action
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $("#formcontainer").html(data.HTMLcontent);
                $("#trcontainer").slideDown();
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

        $("#idealist").on("click","tr.js-getidea", function() {
            $("#idealist tr.active").removeClass("active");
            currenttr = $(this);
            $(this).addClass("active");
            action = currenttr.attr("data-action");
            /*if($("#trcontainer").length > 0) {
            $("#trcontainer").slideUp(500, function(action) {
                $("#trcontainer").remove();
                $("#idealist tr.active").after('<tr id="trcontainer"><td colspan="7" id="formcontainer"></td></tr>');
                $("#trcontainer").hide();
                getAjax(action);
            });
            }
            else {*/
                $("#idealist tr.active").after('<tr id="trcontainer"><td colspan="7" id="formcontainer"></td></tr>');
                //$("#trcontainer").hide();
                getAjax(action);                
            //}       
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
