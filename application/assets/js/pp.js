var donateUrl = "https://don.partipirate.org";
var joinUrl = "https://adhesion.partipirate.org";

$(function() {

	function check(form) {
		var status = true;

		return status;
	}

	function progressHandlingFunction(e) {
	    if (e.lengthComputable){
//	        $('progress').attr({value:e.loaded, max:e.total});
//	        console.log(e.loaded / e.total);
	    }
	}

	function submit(form) {
		if (!check(form)) return;

		$("#volunteerVeil").show();

	    var formData = new FormData(form[0]);
	    $.ajax({
	        url: 'do_volunteer.php',  //Server script to process data
	        type: 'POST',
	        xhr: function() {  // Custom XMLHttpRequest
	            var myXhr = $.ajaxSettings.xhr();
	            if(myXhr.upload){ // Check if upload property exists
	                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
	            }
	            return myXhr;
	        },
	        //Ajax events
	        success: function(data) {
    			$("#volunteerVeil").hide();
        		data = JSON.parse(data);

        		if (data.ko) {

        		}
        		else {
	    			$("#contactForm").hide();
	    			$("#response").show();
        		}
	        },
	        data: formData,
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	}

	$("#zoneButtons button").click(function() {
		if (!$(this).hasClass("active")) {
			$("#zoneButtons button").removeClass("active");
			$(this).addClass("active");

			const zone = $(this).val();
			
			$(".zone-dependant").hide();
			$(".zone-" + zone).show();

			$("#zoneInput").val($(this).val());
		}
	});
	
	$(".zone-dependant").hide();

	$('#confirmationMail').bind('paste', function(event) {
		event.preventDefault();
	});

	$("#contactForm").submit(function(event) {
		event.preventDefault();
		submit($("#contactForm"));
	});

	$("#icandidateButton").click(function(event) {
		event.preventDefault();
		submit($("#contactForm"));
	});

	$(".ijoinButton").click(function(event) {
		event.preventDefault();
		window.location.replace(joinUrl);
	});

	$(".idonateButton").click(function(event) {
		event.preventDefault();
		window.location.replace(donateUrl);
	});

	$("input[type=checkbox]").click(function(event) {
		if ($(this).attr("checked")) {
			$(this).removeAttr("checked");
		}
		else {
			$(this).attr("checked", "checked");
		}
	});

	$(".photo-element").hide();
});