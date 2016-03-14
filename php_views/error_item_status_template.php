<script type="text/x-template" id="error_item_status_template">
	<%
		if (item.status=="download_error") {						
			%>
				<div class="queue_item_status error_item_download_error">
					<small data-tooltip aria-haspopup="true" class="has-tip top" data-disable-hover="false" tabindex="2" title="Die URL der Datei könnte falsch angegeben sein, der Server könnte keine Zugriffe von Programmen erlauben oder es gab Konnektivitätsprobleme.">Fehler beim Herunterladen der Datei</small>
				</div>
			<%
		} else if (item.status=="segmenting_error") {
			%>
				<div class="queue_item_status queue_item_segmenting_error">
					<small data-tooltip aria-haspopup="true" class="has-tip top" data-disable-hover="false" tabindex="2" title="Es kann ein Problem mit FFMPEG vorliegen; ist der Pfad in den Einstellungen richtig angegeben? Es könnte auch Probleme mit der Videodatei geben.">Fehler bei der Extraktion der Video-Frames</small>
				</div>
			<%
		} else if(item.status=="processing_error") {
			%>
				<div class="queue_item_status queue_item_processing_error">
					<small data-tooltip aria-haspopup="true" class="has-tip top" data-disable-hover="false" tabindex="2" title="Sind alle Bibliotheken (OpenCV, tesseract) richtig installiert? Werden die richtigen Cpp-Bibliotheken vom Server verwendet (z.B. die richtige Version von libgcc)? Das error_log des Servers könnte Informationen beinhalten.">Fehler bei der Textdetektion</small>
				</div>
			<%
		} else if(item.status=="evaluating_error") {
			%>
				<div class="queue_item_status queue_item_evaluating_error">
					<small data-tooltip aria-haspopup="true" class="has-tip top" data-disable-hover="false" tabindex="2" title="Ist Java auf dem Server installiert? Das error_log des Servers könnte ansonsten genauere Informationen beinhalten.">Fehler bei der Evaluation der erkannten Wörter</small>
				</div>
			<%
		} else {

		}
	%>
</script>