<?php

use Dzialkowik\Cpt\EventsCPT;
use Dzialkowik\Cpt\PlotsCPT;
use Dzialkowik\Cpt\RODCPT;
use Dzialkowik\Forms\FormConfig;
use Dzialkowik\Users\PlotUser;
use Dzialkowik\Users\RODUser;
use Dzialkowik\Users\UserConfig;
use Juniper\Blocks\JuniperBlocks;

$juniper_blocks = new JuniperBlocks();
$juniper_blocks->include_blocks_functions();
$dzialkowik_plots_cpt = new PlotsCPT();
$dzialkowik_rod_cpt   = new RODCPT();
$dzialkowik_rod_cpt   = new EventsCPT();
$rod_user             = new RODUser();
$dzialkowik_user      = new UserConfig();

$dzialkowik_user->register_user_role( new PlotUser() );
$dzialkowik_user->register_user_role( $rod_user );
$rod_user->add_RODCPT_caps();

add_action( 'acf/init', 'init_forms' );
function init_forms() {
	$form_config = new FormConfig( 'form-plot', '11', 'plots' );
	$form_config->register_form();
}


//TODO User is registering, he is adding his plot to the ROD. Render form and test.
//TODO Add Events and calendar and display in ROD, and Działka View
//TODO Add API Weather
//TODO Add Plot User login page
//TODO Add Działka View
//TODO Add ROD View