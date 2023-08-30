<?php
/*
	Template name: ROD
*/

$funds_array = array();

$context          = Timber::context();
$args             = array(
	'post_type'      => 'rod',
	'posts_per_page' => -1,
);
$context['post']  = new Timber\Post();
$context['posts'] = Timber::get_posts( $args );

Timber::render( array( 'pages/page-rod.twig' ), $context );
