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

use Dzialkowik\Forms\FormConfig;
use Dzialkowik\Users\PlotUser;

$plot_user = new PlotUser();
$plot_user->set_user_role_slug();
$plot_user->set_current_user_id();
$context                 = Timber::context();
$context['current_post'] = new Timber\Post();
$post_id                 = get_the_ID();
if ( isset( $_GET['edit'] ) && $plot_user->is_plot_user() && $plot_user->is_user_allowed_to_edit_plot( $post_id ) ) {

	$form_config = new FormConfig( 'form-plot', 'plots', $post_id );
	$form_config->register_form();
	Timber::render( array( 'pages/plot/plot-edit.twig' ), $context );
} else {
	$context['plot_description'] = get_field( 'opis_dzialki' );
	Timber::render( array( 'pages/plot/plot.twig' ), $context );
}
