<?php
/**
 * Index page
 *
 * @package Tiga Demo
 * @since   1.0.0
 * @version 1.0.0
 */

get_header();

?>

<div class="container">
	<?php
	if ( have_posts() ) :
		/* Start the loop */
		while ( have_posts() ) :
			the_post();
			if ( is_archive() || is_home() ) {
				echo "<br>";
				echo "<a href='".get_permalink()."'>".get_the_title()."</a>";
				echo "<br>";
				the_excerpt();
				echo "<hr>";
			} else {
				echo "<br>";
				the_title();
				echo "<br>";
				the_content();
			}

		endwhile;

	endif;

	?>
</div>

<?php get_footer(); ?>
