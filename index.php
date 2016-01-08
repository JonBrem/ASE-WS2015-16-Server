<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<title>Video Text Tagger Thingy</title>

	<link rel="stylesheet" type="text/css" href="css/foundation/foundation.min.css">
	<link rel="stylesheet" type="text/css" href="css/foundation/foundation-icons.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div class="title_area">
		<div class="row">
			<div class="small-12 large-12 columns">
				<h1>TextFinder</h1>
			</div>			
		</div>
	</div>

	<div class="queue_area">
		<div class="row">
			<div class="small-12 large-12 columns callout">
				<div class="row section_header">
					<div class="small-6 large-8 columns section_title">
						<h3>Queue</h3>
					</div>
					<div class="small-6 large-4 columns section_controls">
						<span class="section_control">
							<i class="fi-play" id="queue_control_play"></i>
						</span>
						<span class="section_control">
							<i class="fi-stop" id="queue_control_stop"></i>
						</span>
						<span class="section_control">
							<i class="fi-info" id="queue_control_info"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="history_area">
		<div class="row">
			<div class="small-12 large-12 columns callout">
				<h3>Fertig</h3>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/underscore.min.js"></script>
	<script type="text/javascript" src="js/foundation.min.js"></script>
	<script type="text/javascript" src="js/what-input.min.js"></script>
	<script type="text/javascript" src="js/queue_script.js"></script>
	<script type="text/javascript" src="js/history_script.js"></script>

	<script>
		$(document).foundation();
	</script>
</body>
</html>