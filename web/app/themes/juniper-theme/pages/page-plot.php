<?php
/*
	Template name: Plot admin
*/

use Dzialkowik\Users\PlotUser;

$plot_user = new PlotUser();
$plot_user->set_user_role_slug();
$plot_user->set_current_user_id();

$context                 = Timber::context();
$context['current_post'] = new Timber\Post();

if ( ! $plot_user->is_plot_user() ) {
	Timber::render( array( 'pages/plot/plot-not-allowed.twig' ), $context );
} else {

	$args = array(
		'post_type'      => 'plots',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'     => 'plot_owner',
				'value'   => array( get_current_user_id() ),
				'compare' => 'IN',
			),
		),
	);


	$context['user_plots'] = new Timber\PostQuery( $args );

	Timber::render( array( 'pages/plot/plot-user-admin.twig' ), $context );
}
