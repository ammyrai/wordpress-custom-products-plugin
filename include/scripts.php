<?php

if (!defined('ABSPATH')) {
    exit;
}  // if direct access

/**
 * Scripts and styles
 */
class WP_ProductPro_Scripts {

    /**
     * Script version number
     */
    protected $version;

    /**
     * Initialize the class
     */
    public function __construct() {
        $this->version = '20180807';

        add_action('wp_enqueue_scripts', array($this, 'wp_productpro_front_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'wp_productpro_admin_scripts'));
    }

    /**
     * Front Scripts
     */
    public function wp_productpro_front_scripts() {
        // CSS Files
        //wp_enqueue_style( 'slick', SP_TEAM_FREE_URL . 'assets/css/slick.css', false, $this->version );
        //JS Files
        //wp_enqueue_script( 'slick-min-js', SP_TEAM_FREE_URL . 'assets/js/slick.min.js', array( 'jquery' ), $this->version, true );
    }

    /**
     * Admin Scripts
     */
    public function wp_productpro_admin_scripts() {
        // CSS Files
        wp_enqueue_style('products-dataTable-style', PRO_URL . '/include/assets/admin/css/jquery.dataTables.min.css', false, $this->version);
        wp_enqueue_style('products-custom', PRO_URL . '/include/assets/admin/css/custom.css', false, $this->version);

        //JS Files
        wp_enqueue_script('products-min-js', PRO_URL . '/include/assets/admin/js/jquery.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script('products-dataTable-js', PRO_URL . '/include/assets/admin/js/jquery.dataTables.min.js', array('jquery'), $this->version, false);

        wp_enqueue_script('products-common-js', PRO_URL . '/include/assets/admin/js/common.js', array('jquery'), $this->version, false);

    }

}

new WP_ProductPro_Scripts();
