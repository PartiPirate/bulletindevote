function toggleCandidates() {
	$(".data").hide().each(function() {
		var line = $(this);

		var force = 0;
		var threshold = 0;

		if ($("button[value=candidate]").hasClass("active") && line.hasClass("candidate")) force++;
		else if ($("button[value=substitute]").hasClass("active") && line.hasClass("substitute")) force++;
		else if ($("button[value=representative]").hasClass("active") && line.hasClass("representative")) force++;
		else if ($("button[value=eligible]").hasClass("active") && line.hasClass("eligible")) force++;
		else if ($("button[value=filler]").hasClass("active") && line.hasClass("filler")) force++;

		if ($("button[value=male]").hasClass("active") && line.hasClass("male")) force++;
		else if ($("button[value=female]").hasClass("active") && line.hasClass("female")) force++;

		if ($("button[value=all-answered]").hasClass("active") && line.hasClass("all-answered")) force++;
		else if ($("button[value=some-answered]").hasClass("active") && line.hasClass("some-answered")) force++;
		else if ($("button[value=none-answered]").hasClass("active") && line.hasClass("none-answered")) force++;


		threshold += ($("#positions button.active").length) ? 1 : 0;
		threshold += ($("#sexes button.active").length) ? 1 : 0;
		threshold += ($("#contacted button.active").length) ? 1 : 0;

		if (force >= threshold) {
			line.show();
		}
	});

	$(".found_persons").text($(".data:visible").length);
}

$(function() {
	$("#positions,#sexes,#contacted").on("click", "button", function() {
		$(this).toggleClass("active");

		toggleCandidates();
	});
});