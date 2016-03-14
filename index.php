<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<title>Texterkennung: Administration</title>

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
					<div class="sticky" id="main_nav_wrapper" data-sticky data-margin-top="0" style="width:100%;">
						<nav id="main_nav">
							<div class="small-12 large-3 columns" data-equalizer-watch>
								<h2>Texterkennungstool</h2>
							</div>			

							<div class="small-1 large-1 columns">
								<a href="#main_nav_wrapper" class="fi-widget" id="open_settings" data-toggle="settings_modal"></a>
								<div id="settings_modal" class="reveal" data-reveal data-animation-in="fade-in" style="width: 100%">
							 		<h2>Einstellungen</h2>
							 		<form><div id="settings_contents">&nbsp;</div></form>
							 		<div>
										<button type="button" class="success button" id="save_settings_button">Speichern</button>
										<button type="button" class="secondary button" id="cancel_settings_button">Abbrechen</button>
									</div>
								</div>
							</div>
						</nav>
					</div>
				</nav>
			</div>
		</div>

		<div class="row">
			<div class="small-12 columns">
				<ul class="accordion sections" data-accordion data-multi-expand="true" data-allow-all-closed="true">
					<li class="accordion-item" data-accordion-item>
						<a href="#" class="accordion-title">Fehlerhafte Einträge</a>
						<div class="accordion-content error_area" id="error_area" data-tab-content>
							<div class="row">
								<div class="small-12 large-12 columns callout">
									<div class="row section_body">
										<ul id="error_list">
										</ul>
									</div>
								</div>
							</div>
						</div>
					</li>

					<li class="accordion-item" data-accordion-item>
						<a href="#" class="accordion-title">In Verarbeitung</a>
						<div class="accordion-content processing_area" id="processing_area" data-tab-content>
							<div class="row">
								<div class="small-12 large-12 columns callout">
									<div class="row section_body">
										<div class="small-12 columns" id="being_processed_item_wrapper">
											
										</div>
									</div>
								</div>
							</div>
						</div>
					</li>


					<li class="accordion-item" data-accordion-item>
						<a href="#" class="accordion-title">Warteschlange</a>					
						<div class="accordion-content queue_area" id="queue_area" data-tab-content>
							<div class="row">
								<div class="small-12 large-12 columns callout">
									<div class="row section_header">
										<div class="small-12 columns section_controls">
											<span class="section_control">
												<i class="fi-play" id="queue_control_play"></i>
											</span>
											<span class="section_control">
												<i class="fi-stop" id="queue_control_stop"></i>
											</span>
											<span class="section_control" style="float: right">
												<i class="fi-info" id="queue_control_info"></i>
											</span>
											<span class="section_control" style="float: right; margin-right: 10px">
												<a data-toggle="add_modal"><i class="fi-plus" id="queue_control_add"></i></a>
												<?php include('php_views/add_modal.php'); ?>
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
					</li>

					<li>
						<a href="#" class="accordion-title">Fertig</a>					
						<div class="history_area accordion-content" id="history_area" data-tab-content>
							<div class="row">
								<div class="small-12 large-12 columns callout">

									<div class="row section_body">
										<ul id="history_list">
										</ul>
									</div>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/underscore.min.js"></script>
	<script type="text/javascript" src="js/foundation.js"></script>
	<script type="text/javascript" src="js/what-input.min.js"></script>

	<!-- own scripts -->
	<script type="text/javascript" src="js/basic_view_model.js"></script>

	<script type="text/javascript" src="js/queue/queue_item_model.js"></script>
	<script type="text/javascript" src="js/queue/queue_item_view.js"></script>
	<script type="text/javascript" src="js/queue/being_processed_item_model.js"></script>
	<script type="text/javascript" src="js/queue/being_processed_item_view.js"></script>
	<script type="text/javascript" src="js/queue/queue_script.js"></script>

	<script type="text/javascript" src="js/history/history_script.js"></script>
	<script type="text/javascript" src="js/history/history_item_model.js"></script>
	<script type="text/javascript" src="js/history/history_item_view.js"></script>

	<script type="text/javascript" src="js/error/error_script.js"></script>
	<script type="text/javascript" src="js/error/error_item_model.js"></script>
	<script type="text/javascript" src="js/error/error_item_view.js"></script>

	<script type="text/javascript" src="js/settings.js"></script>
	<script type="text/javascript" src="js/add_video_to_queue.js"></script>

	<script>
		$(document).foundation();
	</script>

	<?php include("php_views/history_item_template.php"); ?>

	<?php include("php_views/processing_template.php"); ?>
	<?php include("php_views/processing_item_status_template.php"); ?>

	<?php include("php_views/queue_item_status_template.php"); ?>
	<?php include("php_views/queue_item_template.php"); ?>

	<?php include("php_views/error_item_template.php"); ?>
	<?php include("php_views/error_item_status_template.php"); ?>

</body>
</html>