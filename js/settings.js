var Settings = (function() {
	var that = {},
	$settingsElement = undefined,
	$accordionElement = undefined,

	waitingForServerResponses = undefined,
	receivedServerResponses = undefined,

	explanations = {
		"exe_path" : "Pfad zum ausführbaren Programm, das die Texterkennung vornimmt",
		"ffmpeg_path" : "Pfad zu ffmpeg (kann leer gelassen werden, wenn der ffmpeg-Befehl auf der Kommandozeile funktioniert)",
		"ffprobe_path" : "Pfad zu ffprobe (ähnlich wie ffmpeg)",
		"recognition_config" : "'quality' oder 'speed' sind zulässige Werte"
	},

	init = function() {
		$settingsElement = $("#settings_contents");
		$accordionElement = $(".accordion");

		$accordionElement.on("down.zf.accordion", function(e) {
			setTimeout(function() {
				if($("#settings_accordion_item").hasClass('is-active')) {
					updateSettings();
				}
			}, 300);
		});
		$("#save_settings_button").on("click", saveValues);
	},

	updateSettings = function() {
		$.ajax({
			url: 'php_scripts/get_config.php',
			dataType: 'json',
			success: function(e) {
				buildStatusInputs(e);
			}, error: function(e) {
				console.log(e); 
				alert(e);
			}
		});
	},

	saveValues = function(e) {
		var $inputs = $(".settings_input");
		receivedServerResponses = 0;
		waitingForServerResponses = $inputs.length;

		for(var i = 0; i < $inputs.length; i++) {
			$.ajax({
				url: 'php_scripts/set_config.php',
				data: {
					"which" : $inputs.eq(i).attr("data-key"),
					"val" : $inputs.eq(i).val()
				},
				success: function(e) {
					onSettingSaveResponse();
				}, error: function(e) {
					onSettingSaveResponse();
				}
			});
		}
	},

	onSettingSaveResponse = function() {
		receivedServerResponses++;
		if(receivedServerResponses == waitingForServerResponses) {
			updateSettings();
			alert("Einstellungen gespeichert");
		}
	},

	buildStatusInputs = function(e) {
		$settingsElement.empty();
		for(var key in e) {
			if(key == "queue_status") continue;
			$settingsItemRow = $("<div class='row'>" +
				"<div class='small-12 columns'>" + 
					"<label>" + key + 
						"<input class='settings_input' data-key='" + key + "' type='text' value='" + e[key] + "'/>" + 
					"</label>" + 
					((key in explanations)? ("<p class='help-text'>" + explanations[key] + "</p>") : "") +
				"</div>" + 
			"</div>");
			$settingsElement.append($settingsItemRow);
		}
	};

	that.init = init;
	return that;
})();

Settings.init();