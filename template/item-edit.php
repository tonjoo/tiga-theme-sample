<?php get_header(); ?>
<div class="container main-container">
	<div class="row">
		<div class="col-md-3">
			<?php include( 'sidebar-admin.php' ) ?>
		</div>
		<div class="col-md-9">
		
			<?php extras::flash_message($data); ?>

			<form action="" method="post">
				<div class="form-group">
					<label for="input-name">Name</label>
					<input type="text" id="input-name" class="form-control" name="name" value="<?php echo isset( $data['repopulate']['name'] ) ? $data['repopulate']['name'] : $data['item']->name ?>">
				</div>
				<div class="form-group">
					<label for="input-price">Price</label>
					<input type="text" id="input-price" class="form-control" name="price" value="<?php echo isset( $data['repopulate']['price'] ) ? $data['repopulate']['price'] : $data['item']->price ?>">
				</div>
				<div class="form-group">
					<label for="input-desc">Description</label>
					<textarea name="description" id="input-desc" class="form-control"><?php echo isset( $data['repopulate']['description'] ) ? $data['repopulate']['description'] : $data['item']->description ?></textarea>
				</div>
				<div class="form-group">
					<input type="submit" value="Update" class="btn btn-primary">
				</div>
			</form>
		</div>
	</div>
</div>

<?php get_footer(); ?>
