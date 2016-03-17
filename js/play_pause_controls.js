jQuery(document).ready(function($) {
	$("#control_play").on("click", function(e) {
		onPlayPauseControlClick(true);
		activateRunscript();	
	});
	$("#control_stop").on("click", function(e) {onPlayPauseControlClick(false);});

	getRunningStatus();
	setInterval(getRunningStatus, 2000);

	retrieveLastExecuted();
	setInterval(retrieveLastExecuted, 2000);
});
	

	/**
	 * Gets called when the play button is clicked. Tries to set the queue status to "running"
	 * 
	 * @param on: true => running status will be "running", false => "stop"
	 */
	function onPlayPauseControlClick(on) {
		$.ajax({
			url: 'php_scripts/set_config.php',
			dataType: 'json',
			data: {
				"which" : "queue_status",
				"val" : (on? "running" : "stop")
			},
			success: function(e) {getRunningStatus();},
			error: function(e) {getRunningStatus();}
		});
	}

	/**
	 * Activates the runscript. If the main routine / runscript was already up and running, this does nothing anyway, so extra calls don't hurt.
	 */
	function activateRunscript() {
		$.ajax({
			url: 'php_scripts/runscript.php'
		});
	}

	/**
	 * Shows when the Script was last executed.
	 */
	function retrieveLastExecuted() {
		$.ajax({
			url: 'php_scripts/get_last_execution_time.php',
			success: function(e) {
				$("#last_executed").text(new Date(Number.parseInt(e)));
			},
		});
	}

	/**
	 * Retrieves the queue status from the server, calls "updateStatusDisplay" if it works.
	 */
	function getRunningStatus() {
		$.ajax({
			url: 'php_scripts/get_config.php',
			dataType: "json",
			data: {
				"which" : "queue_status"
			},
			success: function(e) {
				updateStatusDisplay(e);
			},
			error:function(e) {}
		});
	}

	/**
	 * if "queue_status" key in the param equals "running", the play-button will be highlighted / activated. 
	 * if not, the stop button will be highlighted / activated.
	 *
	 * @param e: Server Response
	 */
	function updateStatusDisplay(e) {
		if(e["queue_status"] == "running") {
			$("#control_play").addClass('on');
			$("#control_stop").removeClass('on');
		} else {
			$("#control_play").removeClass('on');
			$("#control_stop").addClass('on');
		}
	}