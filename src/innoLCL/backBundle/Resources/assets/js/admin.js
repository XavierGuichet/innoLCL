$(document).ready( function () {
var formtr;

function loadingchangeStatut() {
    val_statut = $("#form_statuts").val();
    $("span.icone-validation").removeClass("choisi");
    if(val_statut == "notset") {
        
    }
    else {
        $("span.icone-validation[data-val='"+val_statut+"']").addClass("choisi");
    }
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

                }
                $('.form_error').html(jqXHR.responseJSON.message);

 
            } else {
                alert(errorThrown);
            }
 
        });    
}

       $("#idealist").on("click","span.icone-validation:not('.lecteur')", function() {
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
                    $("tr.js-getidea.active").removeClass("active");
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
                if(data.error == 1) {
                    alert(data.message);
                    
                }                
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
        
      $("#idealist").on("click","button.js-selected", function() {
          nb_idea_selected = $("button.isselect").length;
          if(nb_idea_selected == 10 && !$(this).hasClass("isselect")) { 
              alert("Vous ne pouvez selectionner que 10 idées maximum. Pour sélectionner celle ci, il faudra en déselectionner une autre");
              return false;
            }
          $(this).toggleClass("isselect");
          action = $(this).attr('data-action');
          $.ajax({
            type: "POST",
            url: action
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                if(data.error == 1) {
                    alert(data.message);
                }
                else {                
                    $(this).toggleClass("isselect");
                   $("#ideacount").html(10 - $("button.isselect").length);
                }
                $("tr.trcontainer").remove();
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    //$(this).replaceWith(jqXHR.responseJSON.form);
                }

            } else {
                alert(errorThrown);
            }
 
        });
      });
      
      $("#validateselection").click( function() {
          nb_idea_selected = $("button.isselect").length;
          if(nb_idea_selected == 10) { 
                action = $(this).attr('data-action');
                $.ajax({
                    type: "POST",
                    url: action
                })
                .done(function (data) {
                    if (typeof data.message !== 'undefined') {
                        if(data.error == 1) {
                            alert(data.message);
                        }
                        else {                
                            $("#validateselection").remove();
                            location.reload();
                        }
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    if (typeof jqXHR.responseJSON !== 'undefined') {
                        if (jqXHR.responseJSON.hasOwnProperty('form')) {
                            //$(this).replaceWith(jqXHR.responseJSON.form);
                        }

                    } else {
                        alert(errorThrown);
                    }
         
                });
            }
            else {
                alert("Vous ne pouvez valider la selection que si 10 idées sont sélectionnées");
              return false;
            }
      });
});
