<?php

/*
Plugin Name: Products
Plugin URI:
Description: Plugin to add and edit the Products from admin.
Author: Admin
Version: 1.0.1
Author URI:
*/
$siteurl = get_option('siteurl');
define('PRO_SITEURL',$siteurl);
define('ADMIN_URL', admin_url());
define('PRO_FOLDER', dirname(plugin_basename(__FILE__)));
define('PRO_URL', plugins_url('/') . PRO_FOLDER);
define('PRO_FILE_PATH', dirname(__FILE__));
define('PRO_DIR_NAME', basename(PRO_FILE_PATH));
// this is the table prefix
global $wpdb;
$wp_table_prefix=$wpdb->prefix;
define('WP_TABLE_PREFIX', $wp_table_prefix);

register_activation_hook(__FILE__,'wp_install');
register_deactivation_hook(__FILE__ , 'wp_uninstall' );

function wp_install()
{
    global $wpdb;

    $product_table = WP_TABLE_PREFIX."products";
    $pro_structure = "CREATE TABLE $product_table (
        id INT(30) NOT NULL AUTO_INCREMENT,
        user_id INT(30) NOT NULL,
        product_name VARCHAR(200) NOT NULL,
        product_image TEXT NOT NULL,
        health_attributes TEXT NOT NULL,
        allergens TEXT NOT NULL,
        Ingredients TEXT NOT NULL,
        certificates TEXT NOT NULL,
        other_info TEXT NOT NULL,
        rating INT(30) NOT NULL,
        likes INT(30) NOT NULL,
        create_at Date NOT NULL,
	      UNIQUE KEY id (id));";
    $wpdb->query($pro_structure);

    $category_table = WP_TABLE_PREFIX."products_category";
    $category_structure = "CREATE TABLE $category_table (
        id INT(30) NOT NULL AUTO_INCREMENT,
        product_id INT(30) NOT NULL,
        category_id INT(30) NOT NULL,
	      UNIQUE KEY id (id)
    );";
    $wpdb->query($category_structure);

    $cat_table = WP_TABLE_PREFIX."pro_category";
    $cat_structure = "CREATE TABLE $cat_table (
        id INT(30) NOT NULL AUTO_INCREMENT,
        category_name VARCHAR(200) NOT NULL,
        create_at Date NOT NULL,
	      UNIQUE KEY id (id)
    );";
    $wpdb->query($cat_structure);
}
function wp_uninstall()
{
    global $wpdb;
    $product_table = WP_TABLE_PREFIX."products";
    $query_structure = "drop table if exists $product_table";
    $wpdb->query($query_structure);

    $pro_category_table = WP_TABLE_PREFIX."products_category";
    $query_structure_pro_category = "drop table if exists $pro_category_table";
    $wpdb->query($query_structure_pro_category);

    $category_table = WP_TABLE_PREFIX."pro_category";
    $query_structure_category = "drop table if exists $category_table";
    $wpdb->query($query_structure_category);

}

require_once(PRO_FILE_PATH . "/include/scripts.php");
require_once( PRO_FILE_PATH . '/include/functions.php' );				// functions used by the plugin

require_once( PRO_FILE_PATH . '/include/product_adminpages.php' );				// admin pages
require_once( PRO_FILE_PATH . '/include/pro_capabilites.php' );				// capability pages

?>
