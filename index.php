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
		<?php include("php_views/edit_modal.php"); ?>

		<div class="row title_area">
			<div class="small-12 columns" style="padding: 0">
				<nav data-sticky-container>
					<div class="sticky" id="main_nav_wrapper" data-sticky data-margin-top="0" style="width:100%;">
						<nav id="main_nav">
							<div class="small-11 large-8 columns">
								<h2>Texterkennungstool: Administration</h2>
								<small style="float: right">benutzt bing.com &amp; canoo.net</small>
							</div>			
						</nav>
					</div>
				</nav>
			</div>
		</div>

		<div class="row">
			<div class="small-12 columns">

				<div class="stop_play_controls">
					<div class="row">
						<div class="small-12 columns">
							<span style="font-size: 12pt; color: #777">Status des Tools:</span> 
							<span class="stop_play_control">
								<i class="fi-play has-tip top" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="2" title="Warteschlange abarbeiten, Skripte ausführen" id="control_play"></i>
							</span>
							<span class="stop_play_control">
								<i class="fi-stop has-tip top" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="2" title="Nichts mehr tun (wird erst nach Fertigstellung der aktuellen Schritte aktiv)" id="control_stop"></i>
							</span>
						</div>
					</div>
					<div class="row">
						<div class="small-12 columns" id="last_exeuted_wrapper">
							<small>Hauptroutine zuletzt ausgeführt: <span id="last_executed"></span></small>
						</div>
					</div>
				</div>

				<ul class="accordion sections" data-accordion data-multi-expand="true" data-allow-all-closed="true">
					<li class="accordion-item" data-accordion-item>
						<a href="#" class="accordion-title">Fehlerhafte Einträge <span id="errors_title_addition"></span></a>
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

					<li class="accordion-item is-active" data-accordion-item>
						<a href="#" class="accordion-title">In Verarbeitung <small id="process_title_addition" style="font-size: 8pt"></small></a>
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


					<li class="accordion-item is-active" data-accordion-item>
						<a href="#" class="accordion-title">Warteschlange <span id="queue_title_addition"></span></a>					
						<div class="accordion-content queue_area" id="queue_area" data-tab-content>
							<div class="row">
								<div class="small-12 large-12 columns callout">
									<div class="row section_header">
										<div class="small-12 columns section_controls">
											<span class="section_control">
												<a data-toggle="add_modal" id="add_to_queue_toggle_modal_button">Video zur Warteschlange hinzufügen</a>
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

					<li class="accordion-item" data-accordion-item>
						<a href="#" class="accordion-title">Fertig <span id="history_title_addition"></span></a>					
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

					<li class="accordion-item" data-accordion-item id="settings_accordion_item">
						<a href="#" class="accordion-title">Einstellungen</a>		
						<div class="settings_area accordion-content" id="settings_area" data-tab-content>
					 		<form><div id="settings_contents">&nbsp;</div></form>
					 		<div>
								<button type="button" class="success button" id="save_settings_button">Speichern</button>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="js/lib/jquery.min.js"></script>
	<script type="text/javascript" src="js/lib/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/lib/underscore.min.js"></script>
	<script type="text/javascript" src="js/lib/foundation.js"></script>
	<script type="text/javascript" src="js/lib/what-input.min.js"></script>

	<!-- own scripts -->
	<script type="text/javascript" src="js/auto_update_data.js"></script>

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
	<script type="text/javascript" src="js/edit_video.js"></script>
	<script type="text/javascript" src="js/play_pause_controls.js"></script>

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