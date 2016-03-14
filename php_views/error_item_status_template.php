<script type="text/x-template" id="error_item_status_template">
	<%
		if (item.status=="download_error") {						
			%>
				<div class="queue_item_status error_item_download_error">
					<small>Fehler beim Herunterladen der Datei</small>
				</div>
			<%
		} else if (item.status=="segmenting_error") {
			%>
				<div class="queue_item_status queue_item_segmenting_error">
					<small>Fehler bei der Extraktion der Video-Frames</small>
				</div>
			<%
		} else if(item.status=="segmenting_error") {
			%>
				<div class="queue_item_status queue_item_processing_error">
					<small>Fehler bei der Textdetektion</small>
				</div>
			<%
		} else if(item.status=="evaluating_error") {
			%>
				<div class="queue_item_status queue_item_evaluating_error">
					<small>Fehler bei der Evaluation der erkannten Wörter</small>
				</div>
			<%
		} else {

		}
	%>
</script>