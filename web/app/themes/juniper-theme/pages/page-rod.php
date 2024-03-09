<?php
/*
	Template name: ROD
*/

//TODO ustawiłem stronę ROD i nie ładuje mi się twig który powinien :|

$context                 = Timber::context();
$args                    = array(
	'taxonomy'       => 'city',
	'posts_per_page' => -1,
);
$context['current_post'] = new Timber\Post();
$get_city_terms          = get_terms( $args );
$rod_cities              = array();
foreach ( $get_city_terms as $city ) {
	$rod_cities[] = array(
		'link' => get_term_link( $city ),
		'name' => $city->name,
	);
}
$context['rod_cities'] = $rod_cities;

Timber::render( array( 'pages/page-rod.twig' ), $context );
