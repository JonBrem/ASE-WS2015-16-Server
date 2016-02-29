var Settings = (function() {
	var that = {},
	$settingsElement = undefined,
	$settingsModal = undefined,

	explanations = {
		"exe_path" : "Pfad zum Ausführbaren Programm, das die Texterkennung vornimmt",
		"ffmpeg_path" : "Pfad zu ffmpeg (kann leer gelassen werden, wenn der ffmpeg-Befehl auf der Kommandozeile funktioniert)",
		"ffprobe_path" : "Pfad zu ffprobe (vermutlich ähnlich wie ffmpeg)"
	},

	init = function() {
		$settingsElement = $("#settings_contents");
		$settingsModal = $("#settings_modal");

		$settingsModal.on("open.zf.reveal", updateSettings);
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

	buildStatusInputs = function(e) {
		$settingsElement.empty();
		for(var key in e) {
			if(key == "queue_status") continue;
			$settingsItemRow = $("<div class='row'>" +
				"<div class='small-12 columns'>" + 
					"<label>" + key + 
						"<input type='text' value='" + e[key] + "'/>" + 
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