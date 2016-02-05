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
							<ul class="horizontal menu expeditionpanded small-11 large-8 columns">
								<li style="width: 33%"><a href="#processing_area">In Verarbeitung</a></li>
								<li style="width: 33%"><a href="#queue_area">Queue</a></li>
								<li style="width: 33%"><a href="#history_area">Fertig verarbeitete Videos</a></li>
							</ul>
							<div class="small-1 large-1 columns">
							&nbsp; <!-- todo: settings -->
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

	<script>
		$(document).foundation();
	</script>

	<script type="text/x-template" id="queue_item_template">
		<li class="queue_list_item row align-middle <%= item.status %>" data-item-id="<%= item.id %>">
			<div class="small-1 large-1 columns queue_item_number"><%= item.number %></div>
			<div class="small-2 large-3 columns queue_item_image_wrapper"><img class="thumbnail" src="<%= item.preview_img %>" /></div>
			<div class="small-5 large-5 columns queue_item_info">
				<div class="row">
					<div class="small-12 columns queue_item_title"><%= item.title %></div>
				</div>
				<div class="row">
					<div class="small-12 columns queue_item_url"><a href="<%= item.url %>">Zur Mediathek</a></div>
				</div>
			</div>

			<div class="small-3 large-2 columns queue_item_progress">
			<!--__ -->
			</div>
			<div class="small-1 large-1 columns queue_item_cancel">
				x
			</div>
		</li>
	</script>

	<script type="text/x-template" id="processing_template">
		<div class="being_processed_item row align-middle <%= item.status %>" data-item-id="<%= item.id %>">
			<div class="small-2 large-3 columns being_processed_item_image_wrapper"><img class="thumbnail" src="<%= item.preview_img %>" /></div>
			<div class="small-5 large-5 columns being_processed_item_info">
				<div class="row">
					<div class="small-12 columns being_processed_item_title"><%= item.title %></div>
				</div>
				<div class="row">
					<div class="small-12 columns being_processed_item_url"><a href="<%= item.url %>">Zur Mediathek</a></div>
				</div>
			</div>

			<div class="small-3 large-2 columns being_processed_item_progress">
			<!--__ -->
			</div>
			<div class="small-1 large-1 columns being_processed_item_cancel">
				x
			</div>
		</div>
	</script>

	<script type="text/x-template" id="status_template">
		<%
			if (item.status=="downloading") {						
				%>
					<div class="queue_item_status queue_item_downloading">
						<i class="fi-download"></i>
						<small>Video-Download läuft</small>
					</div>
				<%
			} else if (item.status=="downloaded") {
				%>
					<div class="queue_item_status queue_item_downloaded">
						<i class="fi-download"></i>
						<small>Video-Download vollständig</small>
					</div>
				<%
			} else {

			}
		%>
	</script>

	<script type="text/x-template" id="process_status_template">
		<%
			if (item.status=="segmenting_video") {
				%>
					<div class="queue_item_status being_processed_status_segmenting_video">
						<small>Schritt 1 von 4: Video wird segmentiert...</small>
					</div>
				<%
			} else if (item.status=="finished_segmenting_video") {
				%>
					<div class="queue_item_status being_processed_status_finished_segmenting_video">
						<small>Schritt 1 von 4: Videosegmentierung abgeschlossen</small>
					</div>
				<%
			} else if (item.status=="being_processed") {
				%>
					<div class="queue_item_status being_processed_status_being_processed">
						<small>Schritt 2 von 4: Texterkennung läuft...</small>
					</div>
				<%						
			} else if (item.status=="finished_processing") {
				%>
					<div class="queue_item_status being_processed_status_finished_processing">
						<small>Schritt 2 von 4: Texterkennung abgeschlossen</small>
					</div>
				<%
			} else if (item.status=="evaluating_words") {
				%>
					<div class="queue_item_status being_processed_status_evaluating_words">
						<small>Schritt 3 von 4: Evaluierung der erkannten Wörter...</small>
					</div>
				<%
			} else if (item.status=="finished_evaluating_words") {
				%>
					<div class="queue_item_status being_processed_status_finished_evaluating_words">
						<small>Schritt 3 von 4: Evaluierung der erkannten Wörter abgeschlossen</small>
					</div>
				<%
			} else if (item.status=="looking_for_tags") {
				%>
					<div class="queue_item_status being_processed_status_looking_for_tags">
						<small>Schritt 4 von 4: EvaluierungWörter abgeschlossen</small>
					</div>
				<%
			} else if (item.status=="ready_for_history") {
				%>
					<div class="queue_item_status being_processed_status_ready_for_history">
						<small>Schritt 4 von 4: Verarbeitung abgeschlossen</small>
					</div>
				<%
			}
		%>
	</script>

	<script type="text/x-template" id="history_item_template">
		<li class="history_list_item row align-middle" data-item-id="<%= item.id %>">
			<div class="small-2 large-3 columns history_item_image_wrapper">
				<img class="thumbnail" src="<%= item.preview_image %>" />
			</div>
			<div class="small-5 large-5 columns history_item_info">
				<div class="row">
					<div class="small-12 columns history_item_title"><%= item.title %></div>
				</div>
				<div class="row">
					<div class="small-12 columns history_item_url"><a href="<%= item.url %>">Zur Mediathek</a></div>
				</div>
			</div>

			<div class="small-5 large-4 columns history_item_tags">
				<% for(var i = 0; i < item.tags.length; i++) { %>
					<span 
						class="<% if(item.tags[i].accepted==0){%>secondary<%} else {%>success<%} %> label history_item_tag"
						data-id="<%= item.tags[i].id %>"><%= item.tags[i].content %></label>
					</span>
				<% } %>
			</div>

		</li>
	</script>

</body>
</html>