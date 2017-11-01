<?php get_header(); ?>

<div class="container">
	<h1>Hello world!</h1>
	<button id="test">Test Ajax Call</button>

	<script>
		jQuery(function($){
			$('#test').on( 'click', function() {
				$.ajax({
					url: '<?php echo site_url() ?>/sample-ajax/',
					type: 'get',
					success: function(res) {
						$('#main > .container').append(res);
					},
					error: function(res) {
						console.log(res);
					}
				})
			});
		});
	</script>
</div>

<?php get_footer(); ?>