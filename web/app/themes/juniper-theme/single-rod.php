<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Dzialkowik\GoogleMaps\GoogleMapsConfig;
use Dzialkowik\Taxonomies\CityTax;

$context         = Timber::context();
$timber_post     = Timber::query_post();
$context['post'] = $timber_post;

$args                    = array(
	'post_type'      => 'plots',
	'posts_per_page' => -1,
	'meta_query'     => array(
		array(
			'key'     => 'rod',
			'value'   => array( $timber_post->ID ),
			'compare' => 'IN',
		),
	),
);
$query                   = new WP_Query( $args );
$context['plots_in_rod'] = $query->get_posts();

$rod_address        = get_field( 'city' );
$city_tax           = new CityTax();
$get_city_weather   = $city_tax->get_taxonomy_weather_data( $rod_address );
$context['city_name'] = $rod_address;
$context['weather'] = $get_city_weather->main;


if ( post_password_required( $timber_post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-rod.twig' ), $context );
}
