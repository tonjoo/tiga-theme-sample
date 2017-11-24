<?php get_header(); ?>
<div class="container main-container">
	<div class="row">
		<div class="col-md-3">
			<?php include( 'sidebar-admin.php' ) ?>
		</div>
		<div class="col-md-9">
			<h2>Sample Ajax</h2>
			<button id="test" class="btn btn-primary">Test Ajax Call</button>
			<div id="ajax-receiver"></div>
		</div>
	</div>	
</div>

<script>
	jQuery(function($){
		$('#test').on( 'click', function() {
			$.ajax({
				url: '<?php echo site_url() ?>/sample-ajax/',
				type: 'get',
				success: function(res) {
					$('#ajax-receiver').append(res);
				},
				error: function(res) {
					console.log(res);
				}
			})
		});
	});
</script>

<?php get_footer(); ?>