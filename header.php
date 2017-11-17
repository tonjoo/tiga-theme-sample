<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class( $body_class ); ?>>
	<div id="main">
		<nav class="navbar navbar-expand-md navbar-light bg-light">
			<div class="container">
				<a class="navbar-brand" href="<?php echo site_url() ?>">Tiga Demo</a>

				<div class="collapse navbar-collapse">
        			<ul class="navbar-nav mr-auto">
          				<li class="nav-item">
            				<a class="nav-link" href="<?php echo site_url('items') ?>">Sample CRUD</a>
          				</li>
          				<li class="nav-item">
            				<a class="nav-link" href="<?php echo site_url('sample') ?>">Sample Ajax</a>
          				</li>
          			</ul>
				</div>
			</div>
		</nav>

