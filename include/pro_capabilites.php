<?php
//make sure administrators have correct capabilities
function pro_check_admin_capabilities()
{
    // Grab the defined (needed) admin capabilities
    $roles = pro_get_capability_defs('administrator');

    $caps_configured = true;

    // check whether the current user has those capabilities already
    foreach( $roles as $r )
    {
        $caps_configured = $caps_configured && current_user_can($r);
    }

    // if not, set the
    if ( false === $caps_configured && current_user_can('administrator'))
    {
        pro_set_capabilities_for_role('administrator');
    }
    if ( false === $caps_configured && current_user_can('subscriber'))
    {
        pro_set_capabilities_for_role('subscriber');
    }
}
add_action('admin_init', 'pro_check_admin_capabilities', 10, 2);

// use the capability definition for $role_name and add/remove capabilities as requested
function pro_set_capabilities_for_role( $role_name, $action = 'enable' )
{
    $cap_array = pro_get_capability_defs($role_name);

    //add caps to specified role
    $role = get_role( $role_name );

    // Iterate through the relevant caps for the role & add or remove them
    foreach( $cap_array as $cap_name )
    {
        if ( $action == 'enable' )
            $role->add_cap($cap_name);

        if ( $action == 'disable' )
            $role->remove_cap($cap_name);
    }
}

// used to define what capabilities goes with what role.
function pro_get_capability_defs($role)
{
    // TODO: Add other standard roles (if/when needed)

    // caps for the administrator role
    $cap_array = array(
        'pro_product_menu',
        'pro_listing',
        'pro_addnew',
        'pro_addcategory'
    );

    return apply_filters( "pro_assigned_{$role}_capabilities", $cap_array);
}
