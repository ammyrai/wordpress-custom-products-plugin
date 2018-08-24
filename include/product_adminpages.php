<?php
/*
	Get array of products Capabilities
*/
function pro_getProductCaps()
{
  $pro_caps = array( 'pro_listing', 'pro_addnew','pro_addcategory');
	return $pro_caps;
}
/*
	Dashboard Menu
*/
function pro_add_pages()
{
	global $wpdb;

	//array of all caps in the menu
	$pro_caps = pro_getProductCaps();
  //the top level menu links to the first page they have access to
  foreach($pro_caps as $cap)
	{
		if(current_user_can($cap))
		{
			$top_menu_cap = $cap;
			break;
		}
	}
	if(empty($top_menu_cap))
		return;
	add_menu_page(__('Products', 'products' ), __('Products', 'products' ), 'pro_product_menu', 'pro-products', $top_menu_cap, 'dashicons-cart');
	add_submenu_page('pro-products', __('Products', 'products' ), __('Products', 'products' ), 'pro_listing', 'pro-products', 'pro_listing');
  add_submenu_page('pro-products', __('Add New', 'products' ), __('Add New', 'products' ), 'pro_addnew', 'pro-addnew', 'pro_addnew');
  add_submenu_page('pro-products', __('Add Category', 'products' ), __('Add Category', 'products' ), 'pro_addcategory', 'pro-addcategory', 'pro_addcategory');

}
add_action('admin_menu', 'pro_add_pages');


/*
	Admin Bar
*/
function pro_admin_bar_menu() {
	global $wp_admin_bar;

	//view menu at all?
	if ( !current_user_can('pro_product_menu') || !is_admin_bar_showing() )
		return;

	//array of all caps in the menu
	$pro_caps = pro_getProductCaps();

	//the top level menu links to the first page they have access to
	foreach($pro_caps as $cap)
	{
		if(current_user_can($cap))
		{
			$top_menu_page = str_replace("_", "-", $cap);
			break;
		}
	}

	$wp_admin_bar->add_menu( array(
	'id' => 'products',
	'title' => __( '<span class="ab-icon"></span>Products', 'products' ),
	'href' => get_admin_url(NULL, '/admin.php?page=' . $top_menu_page) ) );

	if(current_user_can('pro_listing'))
		$wp_admin_bar->add_menu( array(
		'id' => 'pro-product_listing',
		'parent' => 'products',
		'title' => __( 'Products', 'products' ),
		'href' => get_admin_url(NULL, '/admin.php?page=pro-products') ) );

	if(current_user_can('pro_addnew'))
		$wp_admin_bar->add_menu( array(
		'id' => 'pro-addnew',
		'parent' => 'products',
		'title' => __( 'Add New', 'products' ),
		'href' => get_admin_url(NULL, '/admin.php?page=pro-addnew') ) );
	if(current_user_can('pro_addcategory'))
		$wp_admin_bar->add_menu( array(
		'id' => 'pro-addcategory',
		'parent' => 'products',
		'title' => __( 'Add Category', 'products' ),
		'href' => get_admin_url(NULL, '/admin.php?page=pro-addcategory') ) );

}
add_action('admin_bar_menu', 'pro_admin_bar_menu', 1000);

/*
	Functions to load pages from adminpages directory
*/
function pro_addcategory()
{
	require_once(PRO_FILE_PATH . "/adminpages/addcategory.php");
}
function pro_addnew()
{
	require_once(PRO_FILE_PATH . "/adminpages/addnew.php");
}

function pro_listing()
{
	require_once(PRO_FILE_PATH . "/adminpages/productlisting.php");
}
