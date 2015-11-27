var timeOutSessionId;
function maintienSession() {
	$.ajax({
             type: "POST",
             url: "./session_check"
        })
}


jQuery(document).ready( function() {
	timeOutSessionId = setInterval(function(){maintienSession();}, 5*1000);	
});
