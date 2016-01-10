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

	<div class="history_area">
		<div class="row">
			<div class="small-12 large-12 columns callout">
				<h3>Fertig</h3>
			</div>
		</div>
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
			<div class="small-1 large-1 columns queue_item_number"><%= item.number+1 %></div>
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
				<%
					if(item.status=="being_processed") {

					} else if(item.status=="downloading") {						
						%>
							<div class="queue_item_status queue_item_downloading">
								<i class="fi-download"></i>
								<small>Video-Download läuft</small>
							</div>
						<%
					} else if(item.status=="downloaded") {
						%>
							<div class="queue_item_status queue_item_downloaded">
								<i class="fi-download"></i>
								<small>Video-Download vollständig</small>
							</div>
						<%
					} else {

					}
				%>
			</div>
			<div class="small-1 large-1 columns queue_item_cancel">
				x
			</div>
		</li>
	</script>

</body>
</html>