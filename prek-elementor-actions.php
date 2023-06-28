<?php
/**
 * Plugin Name: Elementor Form Actions - Prek
 * Description: Custom addon which adds various form actions.
 * Plugin URI:  https://prek.no
 * Version:     1.0.2
 * Author:      Prek AS
 * Author URI:  https://prek.no
 * Text Domain: prek-elementor-actions
 *
 * GitHub Plugin URI: prek-no/wp-elementor-actions
 * GitHub Plugin URI: https://github.com/prek-no/wp-elementor-actions
 *
 * Elementor tested up to: 3.7.0
 * Elementor Pro tested up to: 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Add new subscriber to Sendy.
 *
 * @since 1.0.0
 * @param ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
 * @return void
 */
function prek_add_form_actions( $form_actions_registrar ) {
    include_once( __DIR__ .  '/actions/action-cookies.php' );

    $form_actions_registrar->register(new Cookies_Action_After_Submit());
}
add_action( 'elementor_pro/forms/actions/register', 'prek_add_form_actions' );