var donateUrl = "https://don.partipirate.org";
var joinUrl = "https://adhesion.partipirate.org";

function createDestinationsInput() {
	var destinations = [];

	$("#destinations .destination").each(function() {
		var row = $(this);
		var destination = {};

		destination.type = row.data("type");
		destination.proportion = row.data("proportion");

		destination.zone = row.find(".zone").text();
		destination.insee =	row.data("insee");

//		destination.zone = row.find(".zone").text();

		destinations.push(destination);
	});

	$("#destinations-input").val(JSON.stringify(destinations));
}

$(function() {

	function check(form) {
		var status = true;

		if (!$("#xxx").val()) {
			status = false;
		}
		else if ($("#xxx").val() != $("#xxx").val()) {
			status = false;
		}

		return status;
	}

	function progressHandlingFunction(e) {
	    if (e.lengthComputable){
//	        $('progress').attr({value:e.loaded, max:e.total});
//	        console.log(e.loaded / e.total);
	    }
	}

	function submit(form) {
		createDestinationsInput();
		
		if (!check(form)) return;

		$("#volunteerVeil").show();

	    var formData = new FormData(form[0]);
	    $.ajax({
	        url: 'do_finance.php',  //Server script to process data
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

	$("#finance-button").click(function(event) {
		event.preventDefault();
		submit($("#contactForm"));
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