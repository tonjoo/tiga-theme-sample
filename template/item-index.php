<?php get_header(); ?>
<div class="container main-container">	
	<div class="row">
		<div class="col-md-3">
			<?php include( 'sidebar-admin.php' ) ?>
		</div>
		<div class="col-md-9">
			
			<?php extras::flash_message($data); ?>

			<div class="row">
				<div class="col-md-12">
					<a href="<?php echo site_url() ?>/items/new" class="btn btn-primary">Add New Item</a>
				</div>
			</div>
			<br>

			<table class="table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Price</th>
						<th>Description</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty( $data['items'] ) ) : ?>
						<?php foreach ( $data['items'] as $key => $value ) : ?>
							<tr>
								<td><?php echo $value->id ?></td>
								<td><?php echo $value->name ?></td>
								<td><?php echo $value->price ?></td>
								<td><?php echo $value->description ?></td>
								<td><a class="btn btn-sm btn-primary" href="<?php echo site_url() ?>/items/<?php echo $value->id ?>/edit">Edit</a> <a class="btn btn-sm btn-danger" href="<?php echo site_url() ?>/items/<?php echo $value->id ?>/delete">Delete</a></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php get_footer(); ?>
