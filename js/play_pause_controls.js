jQuery(document).ready(function($) {
	$("#control_play").on("click", function(e) {onPlayPauseControlClick(true);});
	$("#control_stop").on("click", function(e) {onPlayPauseControlClick(false);});

	getRunningStatus();
	setInterval(getRunningStatus, 2000);
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