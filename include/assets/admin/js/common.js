jQuery(document).ready(function () {
    jQuery('#category_listing').DataTable({
        "order": [[ 0, "desc" ]]
    } );
    jQuery('#product_table_listing').DataTable({
        'order': [[ 0, 'desc' ]]
    } );
});
