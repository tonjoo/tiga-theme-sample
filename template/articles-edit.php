<?php get_header(); ?>
<div class="container main-container">
	<div class="row">
		<div class="col-md-3">
			<?php include( 'sidebar-admin.php' ) ?>
		</div>
		<div class="col-md-9">
			<h2>
			  	Articles
			  	<small class="text-muted">Edit Article</small>
			</h2>

			<?php extras::flash_message($data); ?>

			<form action="" method="post">
				<?php extras::nonce_field( 'article_actions_nonce' ); ?>

				<div class="row">
					<div class="col-md-8">				
						<div class="form-group">
							<label for="input-title">Title</label>
							<input type="text" id="input-title" class="form-control" name="title" value="<?php echo $data['post']->post_title ?>">
						</div>				
						<div class="form-group">
							<label for="input-price"></label>
							<?php wp_editor( $data['post']->post_content, 'content', array() ); ?> 
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="input-desc">Actions</label><br>
							<input type="submit" value="Update" class="btn btn-primary">
							<a href="<?php echo site_url('articles/' . $data['post']->ID . '/trash') ?>?_cmsnonce=<?php echo wp_create_nonce( 'article_actions_nonce' ) ?>" class="btn btn-danger">Move to trash</a>
						</div>
						<div class="form-group">
							<label for="input-desc">Status</label><br>
							<select class="form-control" name="status">
								<?php foreach ($data['statuses'] as $key => $value): ?>						

								<option value="<?php echo $key ?>" <?php if( $data['post']->post_status == $key ) echo "selected" ?> >
									<?php echo $value ?>
								</option>

								<?php endforeach ?>
							</select>
						</div>
						<div class="form-group">
							<label for="input-desc">Categories</label>
							<div class="card">
								<div class="card-body" style="height: 200px; overflow-y: auto;">
									<?php wp_category_checklist($data['post']->ID); ?>
								</div>
							</div>
						</div>
						<div class="form-group" id="custom-img-uploader">
							<label for="input-desc">Featured Image</label>

							<?php if(! empty($data['thumbnail_id']) && $data['thumbnail_src']) : ?>

								<a href="javascript:;" class="upload-custom-img hidden">Upload Image</a>
								<a href="javascript:;" class="delete-custom-img">Delete Image</a>
								<div class="custom-img-container">
									<img src="<?php echo $data['thumbnail_src'][0] ?>" alt="" style="max-width:100%;"/>
								</div>
								<input type="hidden" class="custom-img-id hidden" name="featured_image" value="<?php echo $data['thumbnail_id'] ?>">

							<?php else: ?>

								<a href="javascript:;" class="upload-custom-img">Upload Image</a>
								<a href="javascript:;" class="delete-custom-img hidden">Delete Image</a>
								<div class="custom-img-container"></div>
								<input type="hidden" class="custom-img-id hidden" name="featured_image">

							<?php endif; ?>
							
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<?php get_footer(); ?>
