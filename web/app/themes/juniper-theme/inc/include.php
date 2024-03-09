<?php

use Dzialkowik\Admin\RodAdmin;
use Dzialkowik\Cpt\EventsCPT;
use Dzialkowik\Cpt\PlotsCPT;
use Dzialkowik\Cpt\RODCPT;
use Dzialkowik\Cron\CronScheduler;
use Dzialkowik\Email\MailEventComing;
use Dzialkowik\Forms\FormConfig;
use Dzialkowik\Logger\Logger;
use Dzialkowik\Taxonomies\CityTax;
use Dzialkowik\Taxonomies\RODTax;
use Dzialkowik\Users\PlotUser;
use Dzialkowik\Users\RODUser;
use Dzialkowik\Users\UserConfig;
use Juniper\Blocks\JuniperBlocks;
define( 'LOGGER_FILE', '/inc/Logger/logs/error_log.txt' );

$juniper_blocks = new JuniperBlocks();
$juniper_blocks->include_blocks_functions();
$dzialkowik_plots_cpt = new PlotsCPT();
$dzialkowik_rod_cpt   = new RODCPT();
$rod_user             = new RODUser();
$dzialkowik_user      = new UserConfig();
$city_taxonomy        = new CityTax();
$rod_taxonomy         = new RODTax();
$plot_user            = new PlotUser();
$rod_admin            = new RodAdmin();
$plot_user->set_dashboard_access();
$plot_user->set_current_user_id();
$event_cpt = new EventsCPT();


add_action( 'init', array( $dzialkowik_plots_cpt, 'register_custom_cpt' ) );
add_action( 'init', array( $dzialkowik_rod_cpt, 'register_custom_cpt' ) );
add_action( 'init', array( $city_taxonomy, 'register_custom_taxonomy' ) );
add_action( 'init', array( $rod_taxonomy, 'register_custom_taxonomy' ) );

add_action( 'acf/save_post', array( $dzialkowik_plots_cpt, 'update_plot_data' ) );
add_filter( 'post_type_link', array( $dzialkowik_rod_cpt, 'change_rod_link_to_match_city_tax' ), 10, 2 );
add_filter( 'post_type_link', array( $dzialkowik_plots_cpt, 'change_link_hierarchy_for_single_plot' ), 10, 2 );
add_filter( 'term_link', array( $city_taxonomy, 'change_term_link' ), 10, 3 );
add_action( 'save_post', array( $dzialkowik_rod_cpt, 'set_rod_city_tax' ) );
add_action( 'pre_user_query', array( $rod_user, 'hide_other_roles' ) );
add_filter( 'editable_roles', array( $rod_user, 'prevent_to_set_specific_role' ) );

$dzialkowik_user->register_user_role( $plot_user );
$dzialkowik_user->register_user_role( $rod_user );
$rod_user->add_RODCPT_caps();
$plot_user->add_PLOT_caps();

add_action( 'pre_get_posts', array( $rod_user, 'show_user_specific_content' ) );
add_filter( 'users_list_table_query_args', array( $rod_user, 'list_only_users_created_by_current_user' ) );
add_filter( 'user_register', array( $rod_user, 'update_user_meta_on_create' ) );

$event_cpt->register_custom_cpt();
add_filter( 'acf/init', array( $event_cpt, 'events_admin_fields' ) );
$mail_event_coming                    = new MailEventComing();
$maybe_send_event_email_cron_schedule = new CronScheduler( 'maybe_send_event_email' );
add_action( 'maybe_send_event_email', array( $mail_event_coming, 'send_event_email' ) );
add_action( 'init', array( $maybe_send_event_email_cron_schedule, 'schedule_event' ) );

add_action(
	'wp_mail_failed',
	function ( $error ) {
		if ( ! empty( $error->errors ) ) {
			$message = implode( ', ', $error->errors['wp_mail_failed'] );
		} else {
			$message = 'Email send failed';
		}
		$logger = new Logger();
		$logger->log( $message );
	}
);

