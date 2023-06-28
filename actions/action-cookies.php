<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cookies_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
    public function get_name() {
        return 'cookies';
    }

    public function get_label() {
        return __( 'Cookies', 'prek-elementor-actions' );
    }

    public function register_settings_section( $widget ) {
        $widget->start_controls_section(
            'section_cookies',
            [
                'label' => esc_html__( 'Cookies', 'prek-elementor-actions' ),
                'condition' => [
                    'submit_actions' => $this->get_name(),
                ],
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'cookie_form_name_enabled',
            [
                'label' => __( 'Use form data for cookie name', 'prek-elementor-actions' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'separator' => 'before',
                'value' => 'no',
                'description' => __( 'When enabled you can enter the form field id in the cookie name field below', 'cookies-elementor-integration' ),
            ]
        );

        // If not using cookie_form_name_enabled
        $repeater->add_control(
            'cookie_name',
            [
                'label' => esc_html__( 'Cookie Name', 'prek-elementor-actions' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'cookieName',
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_control(
            'cookie_name_prefix',
            [
                'label' => esc_html__( 'Cookie Name Prefix', 'prek-elementor-actions' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_control(
            'cookie_name_suffix',
            [
                'label' => esc_html__( 'Cookie Name Suffix', 'prek-elementor-actions' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        // Enable form field value
        $repeater->add_control(
            'cookie_form_value_enabled',
            [
                'label' => __( 'Use form data for cookie value', 'prek-elementor-actions' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'separator' => 'before',
                'value' => 'no',
                'description' => __( 'When enabled you can enter the form field id in the cookie value field below', 'prek-elementor-actions' ),
            ]
        );

        // If not using cookie_form_value_enabled
        $repeater->add_control(
            'cookie_value',
            [
                'label' => esc_html__( 'Cookie Value', 'prek-elementor-actions' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'cookieValue',
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_control(
            'cookie_time',
            [
                'label' => __( 'Cookie time', 'prek-elementor-actions' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '3600',
                'label_block' => true,
                'separator' => 'before',
                'description' => __( 'Enter your cookie time in seconds', 'prek-elementor-actions' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $widget->add_control(
            'cookies',
            [
                'label' => esc_html__( 'Cookies List', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'cookie_name' => '',
                        'cookie_value' => '',
                        'cookie_name_prefix' => '',
                        'cookie_name_suffix' => '',
                        'cookie_time' => '3600',
                    ]
                ],
                'title_field' => '{{{ cookie_name }}}',
            ]
        );

        $widget->end_controls_section();
    }

    public function on_export( $element ) {
        unset(
            $element['cookie_name'],
            $element['cookie_name_prefix'],
            $element['cookie_name_suffix'],
            $element['cookie_form_name_enabled'],
            $element['cookie_value'],
            $element['cookie_form_value_enabled'],
            $element['cookie_time']
        );

        return $element;
    }

    public function run( $record, $ajax_handler ) {
        $settings = $record->get( 'form_settings' );
        $cookies = $settings['cookies'];

        foreach ( $cookies as $cookie ) {
            $cookie_name = $cookie['cookie_name'];
            $cookie_name_prefix = $cookie['cookie_name_prefix'];
            $cookie_name_suffix = $cookie['cookie_name_suffix'];
            $cookie_value = $cookie['cookie_value'];
            $cookie_time = $cookie['cookie_time'] ?? 3600;

            // Get submitted Form data
            $raw_fields = $record->get( 'fields' );

            // Normalize the Form Data
            $formFields = [];
            foreach ( $raw_fields as $id => $field ) {
                $formFields[ $id ] = $field['value'];
            }

            if ( $cookie['cookie_form_name_enabled'] == 'yes' ) {
                $cookie_name = $formFields[$cookie_name];
            }

            if ( $cookie['cookie_form_value_enabled'] == 'yes' ) {
                $cookie_value = $formFields[$cookie_value];
            }

            $cookie_name = $cookie_name_prefix . $cookie_name . $cookie_name_suffix;

            setcookie($cookie_name, $cookie_value, time() + $cookie_time, '/' );

            do_action('prek_cookie_saved', $cookie_name, $cookie_value, $cookie_time);
        }
    }
}