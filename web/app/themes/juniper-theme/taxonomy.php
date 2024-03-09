<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package    WordPress
 * @subpackage Timber
 * @since      Timber 0.1
 */



$context = Timber::context();

$query_obj            = get_queried_object();
$timber_post          = new Timber\Term( $query_obj->slug );
$context['term']      = $timber_post;
$args                 = array(
	'post_type'      => 'rod',
	'posts_per_page' => -1,
	'tax_query'      => array(
		array(
			'taxonomy' => $query_obj->taxonomy,
			'field'    => 'slug',
			'terms'    => $query_obj->slug,
		),
	),
);
$context['city_rods'] = Timber::get_posts( $args );



Timber::render( array( 'rod-listing.twig' ), $context );
