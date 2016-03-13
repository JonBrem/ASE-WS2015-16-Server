<script type="text/x-template" id="processing_template">
	<div class="being_processed_item row align-middle <%= item.status %>" data-item-id="<%= item.id %>">
		<div class="small-2 large-3 columns being_processed_item_image_wrapper"><img class="thumbnail" src="<?php include('check_image_template.php'); ?>" /></div>
		<div class="small-5 large-5 columns being_processed_item_info">
			<div class="row">
				<div class="small-12 columns being_processed_item_title">
					<?php include('check_title_template.php'); ?>
				</div>
			</div>
			<div class="row">
				<div class="small-12 columns being_processed_item_url">
					<?php include('check_url_template.php'); ?>
				</div>
			</div>
		</div>

		<div class="small-3 large-2 columns being_processed_item_progress">
		<!--__ -->
		</div>
	</div>
</script>