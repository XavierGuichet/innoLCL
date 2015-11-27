var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
var is_explorer = navigator.userAgent.indexOf('MSIE') > -1;
var is_old_explorer = $("html").hasClass('lt-ie9');
var is_firefox = navigator.userAgent.indexOf('Firefox') > -1;
var is_safari = navigator.userAgent.indexOf("Safari") > -1;
var is_opera = navigator.userAgent.toLowerCase().indexOf("op") > -1;
if ((is_chrome)&&(is_safari)) {is_safari=false;}
if ((is_chrome)&&(is_opera)) {is_chrome=false;}

var timeOutId;

jQuery(document).ready( function($) {
    //Prepare la box de popin et celle de la video
    jQuery(document).foundation();
    Foundation.libs.reveal.settings.close_on_background_click = false;
    Foundation.libs.reveal.settings.multiple_opened = false;
    $("body").on("click","div.reveal-modal-bg", function() {
        $('.reveal-modal').foundation('reveal', 'close');
    });
    
    // initialise homepage scroll vers form Inscription
    $('.login a.login__inscription').on('click', function(event){
        event.preventDefault();
        var target = $(this.hash);
        if (target.length) {
            $('html,body').animate({
              scrollTop: target.offset().top
            }, 1000);
        }
    });
    // -------- fin
    
    // initialise popin de confirmation retour mail
    $('#formRegisterConfirmed').foundation('reveal', 'open');
    $("#formRegisterConfirmed").on('close.fndtn.reveal', function () {
        if($(this).data('reload')){
            location.replace($(this).data('reload'));
        }else{
            location.reload();
        }
    });
    $(function()
    {
        $('.js-custom-scroll').jScrollPane();
    });
    
    //Listener générique js-modal (form et a) #utilisation généralisable
    $('body').on('submit','form.js-modal', function(event) { modalajax(event, $(this)) });
    $('body').on('click','a.js-modal', function(event) { modalajax(event, $(this)) });
    
    // activation des événements sur formulaire inscription
    activateRegisterForm();    
    
    //Soumission des forms en ajax à transformer en function AjaxThisForm(form, callback)
    $("#formIdea").on("submit","form", function(e) {
        
        $('#formIdea #form_save').hide();        
            e.preventDefault();
            $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $("#suggest_idea_front").replaceWith(data.succespopup);
                $("#formIdea").on('close.fndtn.reveal', function () {
                    location.reload();
                });
                $('html,body').animate({
                      scrollTop: $("#formIdea").offset().top
                    }, 1000);
            }else {
				if(typeof data.error !== 'undefined'){
					$('#modal_empty .popup__title').html("Une erreur est survenue");
					$('#modal_empty .popup__content').html(data.error.message);
					$('#modal_empty').foundation('reveal', 'open');
				}
			}
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $('#formIdea #form_save').show();
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    $("#suggest_idea_front").replaceWith(jqXHR.responseJSON.form);
                    $('html,body').animate({
                      scrollTop: $("#formIdea").offset().top
                    }, 1000);
                }
            }else{
				$('#modal_empty .popup__title').html("Une erreur est survenue");
				$('#modal_empty .popup__content').html(errorThrown);
				$('#modal_empty').foundation('reveal', 'open');
            }
        });
    });
    
    // gestion longueur des textarea
    // pour les 200 caractères
    //gestionLimiteTextareaCaracteres();
    // pour les 200 mots
    gestionLimiteTextareaMots();
    
    if($('#js-video').length>0){
        var v = document.getElementById('js-video'); // /!\ A généré une erreur sur une page.
        v.onended = function() { //event de fin de lecture de la vidéo
            appelAjaxFinVideo();            
        };
    }
    
    $('.js-launchvideo').on('click', function() {        
        $('#videoModal').foundation('reveal', 'open');
        $('#videoModal').css('top','0px'); // Test d'ajustement du dialog modal
        
        if((is_explorer)&&($('html').hasClass('lt-ie10'))){
            // timer lance fin de lecture
            timeOutId = setTimeout(function(){appelAjaxFinVideo();}, 68*1000);
        }else{        
            v.play();
        }
    });
    
    $('.popin__video_fermer').on('click', function(event) {  
        event.preventDefault();
        if((is_explorer)&&($('html').hasClass('lt-ie10'))){
            
        }else{
            v.pause();
            v.currentTime = 0;
        }
        appelAjaxFinVideo();
    });
    

    $("span.btn--main-cta").on('click', function() {
      $('#formIdea').foundation('reveal','open');
    });


    // Si le navigateur ne prend pas en charge le placeholder
    if(document.createElement('input').placeholder == undefined ) {        
        // Au focus on clean si sa valeur équivaut à celle du placeholder
        $('[placeholder]').focus(function() {
            if ( $(this).val() == $(this).attr('placeholder') ) {
                                $(this).val(''); }
        });

        // Au blur on remet le placeholder si le champ est laissé vide
        $('[placeholder]').blur(function() {
            if ( $(this).val() == '' ) {
                if($(this).attr("type") != "password") {
                    $(this).val( $(this).attr('placeholder') );
                }
            }
        });

        // On déclenche un blur afin d'initialiser le champ
        $('[placeholder]').blur();

        // Au submit on clean pour éviter d'envoyer la valeur du placeholder
        $('[placeholder]').parents('form').submit(function() {
            $(this).find('[placeholder]').each(function() {
                    if ( $(this).val() == $(this).attr('placeholder') ) {
                            $(this).val(''); }
            });
        });
    }	
    
});

function appelAjaxFinVideo() {
    clearInterval(timeOutId);
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
                if(data.message == "ok"){
                    location.reload();
                }else{
                    alert(data.message);
                }
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('message')) {
                    alert(jqXHR.responseJSON.message);
                }
            } 
            else {
                alert(jqXHR.responseJSON.message);
            }
        });
    }
    else {

    }
    $('#videoModal').foundation('reveal', 'close');
}

function gestionLimiteTextareaCaracteres(){
    $(document).on('load change keyup', '#form_description', function () {
        var len = $(this).val().length;
        var targetCompteur = $('.compteurDesc');
        targetCompteur.html(targetCompteur.data('maxchar') - len);
    });        
    $('#form_description').trigger('change');
    
    $(document).on('load change keyup', '#form_customerprofit', function () {
        var len = $(this).val().length;
        var targetCompteur = $('.compteurProfit');
        targetCompteur.html(targetCompteur.data('maxchar') - len);
    });        
    $('#form_customerprofit').trigger('change');
    
    $(document).on('load change keyup', '#form_partnerprofit', function () {
        var len = $(this).val().length;
        var targetCompteur = $('.compteurPartner');
        targetCompteur.html(targetCompteur.data('maxchar') - len);
    });        
    $('#form_partnerprofit').trigger('change');
    
    $(document).on('load change keyup', '#form_bonuscontent', function () {
        var len = $(this).val().length;
        var targetCompteur = $('.compteurBonus');
        targetCompteur.html(targetCompteur.data('maxchar') - len);
    });        
    $('#form_bonuscontent').trigger('change');
}

function gestionLimiteTextareaMots(){
    $(document).on('load keydown', '#form_description', function (e) {
        var words = $.trim(this.value).length ? this.value.match(/\S+/g).length : 0;
        var targetCompteur = $('.compteurDesc');
        if (words <= targetCompteur.data('maxchar')) {
            targetCompteur.html(targetCompteur.data('maxchar') - words);
        }else{
            if (e.which !== 8) e.preventDefault();
        }
    });        
    $('#form_description').trigger('keydown');
    
    $(document).on('load keydown', '#form_customerprofit', function (e) {
        var words = $.trim(this.value).length ? this.value.match(/\S+/g).length : 0;
        var targetCompteur = $('.compteurProfit');
        if (words <= targetCompteur.data('maxchar')) {
            targetCompteur.html(targetCompteur.data('maxchar') - words);
        }else{
            if (e.which !== 8) e.preventDefault();
        }
    });        
    $('#form_customerprofit').trigger('keydown');
    
    $(document).on('load keydown', '#form_partnerprofit', function (e) {
        var words = $.trim(this.value).length ? this.value.match(/\S+/g).length : 0;
        var targetCompteur = $('.compteurPartner');
        if (words <= targetCompteur.data('maxchar')) {
            targetCompteur.html(targetCompteur.data('maxchar') - words);
        }else{
            if (e.which !== 8) e.preventDefault();
        }
    });        
    $('#form_partnerprofit').trigger('keydown');
    
    $(document).on('load keydown', '#form_bonuscontent', function (e) {
        var words = $.trim(this.value).length ? this.value.match(/\S+/g).length : 0;
        var targetCompteur = $('.compteurBonus');
        if (words <= targetCompteur.data('maxchar')) {
            targetCompteur.html(targetCompteur.data('maxchar') - words);
        }else{
            if (e.which !== 8) e.preventDefault();
        }
    });        
    $('#form_bonuscontent').trigger('keydown');
    
}


function activateRegisterForm(){
    //Trigger register form pour slide        /!\ Retour au step 1 à rajouter en cas d'erreur de validation par le client.
    $(".js-register-next").click( function(e) {
        
       
       /*
       if($("#fos_user_registration_form_firstname").val() ==''){
           $("#fos_user_registration_form_firstname").focus();
           return false;
       }
       if($("#fos_user_registration_form_lastname").val() ==''){
           $("#fos_user_registration_form_lastname").focus();
           return false;
       }
       if($("#fos_user_registration_form_email").val() ==''){
           $("#fos_user_registration_form_email").focus();
           return false;
       }
       if($("#fos_user_registration_form_plainPassword_first").val() ==''){
           $("#fos_user_registration_form_plainPassword_first").focus();
           return false;
       }
       if($("#fos_user_registration_form_plainPassword_second").val() ==''){
           $("#fos_user_registration_form_plainPassword_second").focus();
           return false;
       }
        */
        /*var $theForm = $(this).closest('form');
        $theForm[0].checkValidity();*/
        /*if (( typeof($theForm[0].checkValidity) == "function" ) && !$theForm[0].checkValidity()) {
            return;
        }*/
        
        $('.showErrors').each(function(){ $(this).addClass('hidden');});
        
        var $theForm = $(this).closest('form');
        
        if (!is_safari && !is_old_explorer){
            if(!$("#fos_user_registration_form_firstname")[0].checkValidity()){
                return true;
            }
            if(!$("#fos_user_registration_form_lastname")[0].checkValidity()){
                return true;
            }
            if(!$("#fos_user_registration_form_email")[0].checkValidity()){
                return true;
            }
            if(!$("#fos_user_registration_form_plainPassword_first")[0].checkValidity()){
                return true;
            }
            if(!$("#fos_user_registration_form_plainPassword_second")[0].checkValidity()){
                return true;
            }  
        }
        
        // pour browser non compatible ou safari
        if($("#fos_user_registration_form_firstname").val() ==''){
            $("#fos_user_registration_form_firstname").focus();
            $('.showErrors.firstname').removeClass('hidden');
            $('.showErrors.firstname').html("Vous devez indiquer votre prénom.");
            return false;
        }
        if($("#fos_user_registration_form_lastname").val() ==''){
            $("#fos_user_registration_form_lastname").focus();
            $('.showErrors.lastname').removeClass('hidden');
            $('.showErrors.lastname').html("Vous devez indiquer votre nom.");
            return false;
        }
        if($("#fos_user_registration_form_email").val() ==''){
            $("#fos_user_registration_form_email").focus();
            $('.showErrors.email').removeClass('hidden');
            $('.showErrors.email').html("Vous devez indiquer une adresse mail.");
            return false;
        }
        if($("#fos_user_registration_form_plainPassword_first").val() ==''){
            $("#fos_user_registration_form_plainPassword_first").focus();
            $('.showErrors.passwordFirst').removeClass('hidden');
            $('.showErrors.passwordFirst').html("Vous devez indiquer un mot de passe.");
            return false;
        }
        if($("#fos_user_registration_form_plainPassword_second").val() ==''){
            $("#fos_user_registration_form_plainPassword_second").focus();
            $('.showErrors.passwordSecond').removeClass('hidden');
            $('.showErrors.passwordSecond').html("Vous devez confirmer le mot de passe.");
            return false;
        }
        
       // sinon on bloque et on glisse vers la suite
        e.preventDefault();       
        // retour Ajax positif = slide charte sinon affichage erreurs
        $(".register__slider__item:eq(0)").css('margin-left','-100%');
    });
    
    $('.submit_global').click( function(e) {        
        e.preventDefault();
        $('.showErrors.cgvaccepted').addClass('hidden');
        $('.showErrors.cgvaccepted').html("");
           
        if($("#fos_user_registration_form_cgvaccepted").is(':checked') == false){
           $("#fos_user_registration_form_cgvaccepted").focus();
           $('.showErrors.cgvaccepted').removeClass('hidden');
           $('.showErrors.cgvaccepted').html("Vous devez accepter les conditions de règlement du jeu.");
           return false;
        }
        $(this).closest("form").submit();
    });
    /*
    $('.register__submit').click( function(e) {
        e.preventDefault();
        if($("#fos_user_registration_form_cgvaccepted").is(':checked') == false){
           $("#fos_user_registration_form_cgvaccepted").focus();
           return false;
        }
        $(this).closest("form").submit();
    });
    */
    
    $('form.fos_user_registration_register').on('submit', function(event){
        event.preventDefault();                
        $.ajax({
             type: $(this).attr('method'),
             url: $(this).attr('action'),
             data: $(this).serialize()
        })
        .done(function (data) {
            $('form.fos_user_registration_register').css('visibility','hidden');
            $('body').append(data.popin);
            $('#formRegisterConfirmed').foundation('reveal', 'open');
            $("#formRegisterConfirmed").on('close.fndtn.reveal', function () {
                location.reload();
            });
        })
        .fail(function (jqXHR, textStatus, errorThrown) { 
            $(".register__slider__item:eq(0)").css('margin-left','0%');            
            if (typeof jqXHR.responseJSON !== 'undefined') {                
                if (jqXHR.responseJSON.hasOwnProperty('form')) {                    

                    $('form.fos_user_registration_register').replaceWith(jqXHR.responseJSON.form);
                    $('form.fos_user_registration_register').find('.showErrors').each(function(){
                       if($(this).html() != '') {
                           $(this).removeClass('hidden');
                           $(this).show(0);
                       }
                    });

                    activateRegisterForm();
                }
            } 
            else {

            } 
        });
    });
}

function modalajax(event, t) {
	event.preventDefault();
	var eventTarget = event.target;
	var method,url,serializeData;
	if(t.is('a')) {
		method = "GET";
		url = t.attr("href");
		serializeData = "";
	}
	else {
		if(t.is('form')) {
		method = t.attr('method');
		url = t.attr('action');
		serializeData = t.serialize();				
		}
		else {
			location.href(eventTarget);
		}
	}
	
	$.ajax({
             type: method,
             url: url,
             data: serializeData
        })
        .done(function (data) {
			$('#modal_empty .popup__title').html(data.title);
			$('#modal_empty .popup__content').html(data.content);
            $('#modal_empty').foundation('reveal', 'open');
        })
        .fail(function (jqXHR, textStatus, errorThrown) { 

        });
		
}

