<div id="add_modal" class="reveal" data-reveal data-animation-in="fade-in" style="width: 100%">
	<h2>Video hinzufügen</h2>
	<div class="add_modal_contents">
		<div class="row">
			<div class="small-12 columns">
				<label>
					Video-ID (video_id)
					<input type="text" id="add_modal_input_assigned_id" />
				</label>
				<p class="help-text">ID, mit der der Status des Videos später eingesehen werden kann. Darf leergelassen werden; Videostatus ist auch per video_file_url abrufbar.</p>
			</div>
		</div>
		<div class="row">
			<div class="small-12 columns">
				<label>
					Video-Titel (title)
					<input type="text" id="add_modal_input_title" />
				</label>
				<p class="help-text">Darf leergelassen werden.</p>
			</div>
		</div>
		<div class="row">
			<div class="small-12 columns">
				<label>
					Vorschaubild (preview_image)
					<input type="text" id="add_modal_input_preview_image" />
				</label>
				<p class="help-text">Bild, das in der Liste beim Video angezeigt wird. Darf leergelassen werden.</p>
			</div>
		</div>
		<div class="row">
			<div class="small-12 columns">
				<label>
					Mediathek-Url (url)
					<input type="text" id="add_modal_input_video_url" />
				</label>
				<p class="help-text">Darf leergelassen werden.</p>
			</div>
		</div>
		<div class="row">
			<div class="small-12 columns">
				<label>
					Video-Datei-Url (video_file_url)
					<input type="text" id="add_modal_input_video_file_url" />
				</label>
				<p class="help-text">URL der .mp4-Datei auf einem Server. Muss angegeben werden.</p>
			</div>
		</div>


	</div>
		<div>
		<button type="button" class="success button" id="save_add_video_button">Video hinzufügen</button>
		<button type="button" class="secondary button" id="cancel_add_video_button">Abbrechen</button>
	</div>
</div>