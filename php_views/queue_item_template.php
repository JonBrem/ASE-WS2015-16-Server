<script type="text/x-template" id="queue_item_template">
	<li class="queue_list_item row align-middle" data-item-id="<%= item.id %>">
		<div class="small-1 large-1 columns queue_item_number"><%= item.number %></div>
		<div class="small-2 large-3 columns queue_item_image_wrapper"><img class="thumbnail" src="<?php include('check_image_template.php'); ?>" /></div>
		<div class="small-5 large-5 columns queue_item_info">
			<div class="row">
				<div class="small-12 columns queue_item_title">
					<?php include('check_title_template.php'); ?>
				</div>
			</div>
			<div class="row">
				<div class="small-12 columns queue_item_url">
					<?php include('check_url_template.php'); ?>
				</div>
			</div>
			<div class="row">
				<div class="small-12 columns queue_item_assigned_id">
					<?php include('check_assigned_id_template.php'); ?>
				</div>
			</div>
		</div>

		<div class="small-3 large-2 columns queue_item_progress">
		<!--__ -->
		</div>
		<div class="small-1 large-1 columns queue_item_cancel">
			<button type="button" class="button queue_item_edit has-tip top" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="2" title="Videodaten bearbeiten"><i class="fi-pencil"></i></button>
			<button type="button" class="alert button queue_item_delete has-tip top" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="2" title="Video löschen"><i class="fi-x"></i></button>
		</div>
	</li>
</script>