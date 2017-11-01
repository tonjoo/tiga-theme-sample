<?php get_header(); ?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<br>
			<?php $data['flash']->display(); ?>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-6">
			<form action="" method="post">
				<div class="form-group">
					<label for="input-name">Name</label>
					<input type="text" id="input-name" class="form-control" name="name" value="<?php echo isset( $data['repopulate']['name'] ) ? $data['repopulate']['name'] : '' ?>">
				</div>
				<div class="form-group">
					<label for="input-price">Price</label>
					<input type="text" id="input-price" class="form-control" name="price" value="<?php echo isset( $data['repopulate']['price'] ) ? $data['repopulate']['price'] : '' ?>">
				</div>
				<div class="form-group">
					<label for="input-desc">Description</label>
					<textarea name="description" id="input-desc" class="form-control"><?php echo isset( $data['repopulate']['description'] ) ? $data['repopulate']['description'] : '' ?></textarea>
				</div>
				<div class="form-group">
					<input type="submit" value="Save" class="btn btn-primary">
				</div>
			</form>
		</div>
	</div>
</div>

<?php get_footer(); ?>
