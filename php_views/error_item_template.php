<script type="text/x-template" id="error_item_template">
	<li class="error_list_item row align-middle <%= item.status %>" data-item-id="<%= item.id %>">
		<div class="small-2 large-3 columns error_item_image_wrapper"><img class="thumbnail" src="<?php include('check_image_template.php'); ?>" /></div>
		<div class="small-5 large-5 columns error_item_info">
			<div class="row">
				<div class="small-12 columns error_item_title">
					<?php include('check_title_template.php'); ?>
				</div>
			</div>
			<div class="row">
				<div class="small-12 columns error_item_url">
					<?php include('check_url_template.php'); ?>
				</div>
			</div>
		</div>

		<div class="small-3 large-2 columns error_item_status">
		<!--__ -->
		</div>
		<div class="small-1 large-1 columns error_item_retry">
			<button type="button" class="alert button error_item_retry"><i class="fi-x"></i></button>
		</div>
		<div class="small-1 large-1 columns error_item_cancel">
			<button type="button" class="alert button error_item_delete"><i class="fi-x"></i></button>
		</div>
	</li>
</script>