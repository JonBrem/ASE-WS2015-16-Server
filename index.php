<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<title>Video Text Tagger Thingy</title>

	<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.structure.min.css">
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.theme.min.css">
	<link rel="stylesheet" type="text/css" href="css/foundation/foundation.min.css">
	<link rel="stylesheet" type="text/css" href="css/foundation/foundation-icons.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

	<div class="small-12 columns">

		<div class="row title_area">
			<div class="small-12 columns" style="padding: 0">
				<nav data-sticky-container>
					<div class="sticky" id="main_nav_wrapper" data-sticky data-margin-top="0" style="width:100%;" data-magellan-expedition="fixed" data-options="destination_threshold:20;fixed_top:0;throttle_delay:0;">
						<nav data-magellan id="main_nav">
							<div class="small-12 large-3 columns" data-equalizer-watch>
								<h2>TextFinder</h2>
							</div>			
							<ul class="horizontal menu expedition small-11 large-8 columns">
								<li style="width: 33%"><a href="#processing_area">In Verarbeitung</a></li>
								<li style="width: 33%"><a href="#queue_area">Queue</a></li>
								<li style="width: 33%"><a href="#history_area">Fertig verarbeitete Videos</a></li>
							</ul>
							<div class="small-1 large-1 columns">
								<i class="fi-widget" id="open_settings"></i>
							</div>
						</nav>
					</div>
				</nav>
			</div>
		</div>

		<div class="sections">
			<div class="processing_area" id="processing_area" data-magellan-target="processing_area">
				<div class="row">
					<div class="small-12 large-12 columns callout">
						<div class="row section_header">
							<div class="small-8 large-10 columns section_title">
								<h3>In Verarbeitung</h3>
							</div>
						</div>
						<div class="row section_body">
							<div class="small-12 columns" id="being_processed_item_wrapper">
								
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="queue_area" id="queue_area" data-magellan-target="queue_area">
				<div class="row">
					<div class="small-12 large-12 columns callout">
						<div class="row section_header">
							<div class="small-3 large-2 columns section_title">
								<h3>Queue</h3>
							</div>
							<div class="small-9 large-10 columns section_controls">
								<span class="section_control">
									<i class="fi-play" id="queue_control_play"></i>
								</span>
								<span class="section_control">
									<i class="fi-stop" id="queue_control_stop"></i>
								</span>
								<span class="section_control" style="float: right">
									<i class="fi-info" id="queue_control_info"></i>
								</span>
							</div>
						</div> <!-- /.section_header -->

						<div class="row section_body">
							<ol id="queue_list">
							</ol>
						</div> <!-- /.section_body -->
					</div>
				</div>
			</div> <!-- /.queue_area -->

			<div class="history_area" id="history_area" data-magellan-target="history_area">
				<div class="row">
					<div class="small-12 large-12 columns callout">
						<div class="row section_header">
							<div class="small-3 large-2 columns section_title">
								<h3>Fertig</h3>
							</div>
						</div> <!-- /.section_header -->

						<div class="row section_body">
							<ul id="history_list">
							</ul>
						</div>
					</div>
				</div>
			</div>
		

		</div> <!-- /.sections -->

	</div>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/underscore.min.js"></script>
	<script type="text/javascript" src="js/foundation.min.js"></script>
	<script type="text/javascript" src="js/what-input.min.js"></script>

	<!-- own scripts -->
	<script type="text/javascript" src="js/queue_script.js"></script>
	<script type="text/javascript" src="js/history_script.js"></script>
	<script type="text/javascript" src="js/settings.js"></script>

	<script>
		$(document).foundation();
	</script>

	<?php include("php_views/history_item_template.php"); ?>
	<?php include("php_views/processing_template.php"); ?>
	<?php include("php_views/queue_item_status_template.php"); ?>
	<?php include("php_views/queue_item_template.php"); ?>
	<?php include("php_views/processing_item_status_template.php"); ?>

</body>
</html>