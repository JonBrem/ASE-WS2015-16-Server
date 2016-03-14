jQuery(document).ready(function($) {
	$("#control_play").on("click", function(e) {onPlayPauseControlClick(true);});
	$("#control_stop").on("click", function(e) {onPlayPauseControlClick(false);});

	getRunningStatus();
	setInterval(getRunningStatus, 2000);
});
	

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

	function updateStatusDisplay(e) {
		if(e["queue_status"] == "running") {
			$("#control_play").addClass('on');
			$("#control_stop").removeClass('on');
		} else {
			$("#control_play").removeClass('on');
			$("#control_stop").addClass('on');
		}
	}