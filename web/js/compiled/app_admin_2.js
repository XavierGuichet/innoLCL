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

function updateMenuCount(newCat) {
var transform = new Array("all", "maybe", "valid", "refus");
csscat = transform[newCat];
CountToIncrement = $(".navidea .panel-heading .huge."+csscat).html();
CountToIncrement++;
$(".navidea .panel-heading .huge."+csscat).html(CountToIncrement);
if($(".section-all").length) { decrement = $(".navidea .panel-heading .huge.all");}
if($(".section-maybe").length) { decrement = $(".navidea .panel-heading .huge.maybe");}
if($(".section-validated").length) { decrement = $(".navidea .panel-heading .huge.valid");}
if($(".section-refused").length) { decrement = $(".navidea .panel-heading .huge.refus");}
CountToDecrement = decrement.html();
CountToDecrement--;
decrement.html(CountToDecrement);
}

       $("#idealist").on("click","span.js-icone-validation:not('.lecteur')", function() {
            val_statut = $(this).attr("data-val");
            $("span.icone-validation.js-icone-validation").removeClass("choisi");
            $(this).addClass("choisi");
            if($("#form_statuts").length) {
				$("#form_statuts").val(val_statut);
			}
			if($("#form_avis").length) {
				$("#form_avis").val(val_statut);
			}
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
				
				//Oblige à remplir le champ motif du refus en cas de refus
                if($("#form_statuts").val() == "refused" && !$.trim($("#form_refusalreason").val())) {
                    alert("Vous devez donner un motif au refus.");
                    e.preventDefault();
                    return;
                }                
            }
            if($("#form_statuts").length) {
				var newcat = $("#form_statuts")[0].selectedIndex;
			}
			if($("#form_avis").length) {
				var newcat = $("#form_avis")[0].selectedIndex;
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
					updateMenuCount(newcat);
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
