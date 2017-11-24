<?php get_header(); ?>
<div class="container main-container">
	
	<?php extras::flash_message($data); ?>
	
	<div class="row">
		<div class="col-md-6">
			<h2>Log In</h2>
			<hr>

			<form action="" method="post">
				<div class="form-group">
					<label for="input-name">Username</label>
					<input type="text" id="input-username" class="form-control" name="username" value="<?php echo isset( $data['repopulate']['username'] ) ? $data['repopulate']['username'] : '' ?>">
				</div>
				<div class="form-group">
					<label for="input-price">Password</label>
					<input type="password" id="input-password" class="form-control" name="password" value="<?php echo isset( $data['repopulate']['password'] ) ? $data['repopulate']['password'] : '' ?>">
				</div>
				<div class="form-group">
					<input type="submit" value="Login to your dashboard" class="btn btn-primary">
				</div>
			</form>
		</div>
	</div>
</div>

<?php get_footer(); ?>