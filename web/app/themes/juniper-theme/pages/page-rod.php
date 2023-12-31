<?php
/*
	Template name: ROD
*/


$context                 = Timber::context();
$args                    = array(
	'post_type'      => 'rod',
	'posts_per_page' => -1,
);
$context['current_post'] = new Timber\Post();
$context['all_rods']     = Timber::get_posts( $args );

Timber::render( array( 'pages/page-rod.twig' ), $context );
