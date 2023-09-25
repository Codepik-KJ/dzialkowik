<?php

use Dzialkowik\Cpt\EventsCPT;
use Dzialkowik\Cpt\PlotsCPT;
use Dzialkowik\Cpt\RODCPT;
use Dzialkowik\Forms\FormConfig;
use Dzialkowik\Taxonomies\CityTax;
use Dzialkowik\Users\PlotUser;
use Dzialkowik\Users\RODUser;
use Dzialkowik\Users\UserConfig;
use Juniper\Blocks\JuniperBlocks;

$juniper_blocks = new JuniperBlocks();
$juniper_blocks->include_blocks_functions();
$dzialkowik_plots_cpt = new PlotsCPT();
$dzialkowik_rod_cpt   = new RODCPT();
$rod_user             = new RODUser();
$dzialkowik_user      = new UserConfig();
$city_taxonomy        = new CityTax();

add_action( 'init', array( $dzialkowik_plots_cpt, 'register_custom_cpt' ) );
add_action( 'init', array( $dzialkowik_rod_cpt, 'register_custom_cpt' ) );
add_action( 'init', array( $city_taxonomy, 'register_custom_taxonomy' ) );
add_action( 'acf/init', 'init_forms' );
function init_forms() {
	$form_config = new FormConfig( 'form-plot', '11', 'plots' );
	$form_config->register_form();
}
add_action( 'acf/save_post', array( $dzialkowik_plots_cpt, 'update_plot_data' ) );
add_filter( 'post_type_link', array( $dzialkowik_rod_cpt, 'change_rod_title_as_city_tax' ), 10, 2 );
add_filter( 'post_type_link', array( $dzialkowik_plots_cpt, 'post_type_as_link' ), 10, 2 );
add_action( 'save_post', array( $dzialkowik_rod_cpt, 'set_rod_city_tax' ) );
add_action( 'pre_user_query', array( $rod_user, 'hide_other_roles' ) );
add_filter( 'editable_roles', array( $rod_user, 'prevent_to_set_specific_role' ) );

$dzialkowik_user->register_user_role( new PlotUser() );
$dzialkowik_user->register_user_role( $rod_user );
$rod_user->add_RODCPT_caps();


//TODO User is registering, he is adding his plot to the ROD. Render form and test.
//TODO Add Events and calendar and display in ROD, and Działka View
//TODO Add API Weather
//TODO Add Plot User login page
//TODO Add Działka View
//TODO Add ROD View
