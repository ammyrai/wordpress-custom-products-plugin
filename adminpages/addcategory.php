<?php
if( is_user_logged_in() ) {
  $user = wp_get_current_user();
  $membership = ( array ) $user->membership_level;
  $role = ( array ) $user->roles;

  $meta = get_user_meta( get_current_user_id(), 'pmpro_approval_' . $membership['ID'], true);
  if($meta['status'] == 'pending')
  { ?>
      <div class="wrap">
        <div style="margin-top: 5%;">
          <div id="error_msg" class="error notice is-dismissible" style="display:block;">
              <p><strong>Hello <?php echo ucfirst($user->display_name); ?></strong>. </p>
              <p>You do not have permissions to perform this action untill admin approve your account.</p>
              <button type="button" class="notice-dismiss">
                  <span class="screen-reader-text">Dismiss this notice.</span>
              </button>
          </div>
        </div>
      </div>
  <?php }
  else { ?>
<div class="wrap">
  <?php
      global $wpdb;
      $cate_table = WP_TABLE_PREFIX."pro_category";
      if(isset($_REQUEST['edit']))
  		  $edit = intval($_REQUEST['edit']);
  	  else
  		  $edit = false;

        if(isset($_REQUEST['delete']))
      		$delete = intval($_REQUEST['delete']);
      	else
      		$delete = false;
        if($edit)
						{
							?>
							<h2>
								<?php
							if($edit > 0)
								echo __("Edit Category", 'pro-products' );

							else
								echo __("Add New Category", 'pro-products' );
								?>
								</h2>
								<?php
						}
					?>

 				<?php if($edit) {
        if($edit < 0)
        {
            if($_POST['savecategory'] && $_POST['savecategory'] != '')
            {
              $name = $_POST['name'];
              $getcate = $wpdb->get_row( "SELECT count(*) as countcat FROM $cate_table WHERE category_name = '$name'" );
							if($getcate->countcat == 0)
              {
                  $insetcate = $wpdb->insert( $cate_table,
                    array(
                        'category_name' => trim($name),
                        'create_at' => date("Y-m-d")
                    ),
                    array(
                        '%s',
                        '%s',
                    )
                 );
								 if ($wpdb->insert_id == FALSE) { ?>
										 	<div id="error_msg" class="error notice is-dismissible updated fade">
												 <p>Something went wrong. Please again later. </p>
												 <button type="button" class="notice-dismiss">
														 <span class="screen-reader-text">Dismiss this notice.</span>
												 </button>
										 	</div>
							<?php } else { ?>
										<div id="success_msg" class="updated notice is-dismissible updated fade">
											 <p>Category "<?php echo $name; ?>" added successfully! </p>
											 <button type="button" class="notice-dismiss">
													 <span class="screen-reader-text">Dismiss this notice.</span>
											 </button>
								 		</div>
							<?php }
              }
							else { ?>
										<div id="already_exitsts_error" class="error notice is-dismissible updated fade">
												<p>Category "<?php echo $name; ?>" is already exists. Please try to add a new one. </p>
												<button type="button" class="notice-dismiss">
														<span class="screen-reader-text">Dismiss this notice.</span>
												</button>
										</div>
						<?php	}
            }?>

					<?php
        }
        if($edit >0)
        {
            if($_POST['editcategory'])
            {
              $update = $wpdb->update(
                  $cate_table,
                  array(
                    'category_name' => trim($_POST['name']),	// string
                  ),
                  array( 'ID' =>  $edit),
                  array(
                    '%s',	// value1
                  ),
                  array( '%d' )
                  );
									if ($update == FALSE) { ?>
										<div id="error_msg" class="error notice is-dismissible updated fade">
											 <p>Something went wrong. Please again later. </p>
											 <button type="button" class="notice-dismiss">
													 <span class="screen-reader-text">Dismiss this notice.</span>
											 </button>
										</div>
									<?php } else { ?>
										<div id="success_msg" class="updated notice is-dismissible updated fade">
											 <p>Category "<?php echo $_POST['name']; ?>" updated successfully! </p>
											 <button type="button" class="notice-dismiss">
													 <span class="screen-reader-text">Dismiss this notice.</span>
											 </button>
										</div>
									<?php }
            }
            $getcate = $wpdb->get_row( "SELECT * FROM $cate_table WHERE id = $edit" );
        }
    ?>
      <form action="" method="post">
        <table class="form-table">
        <tbody>
          <tr>
            <th scope="row" valign="top"><label for="name">Category Name:</label></th>
            <td><input name="name" type="text" size="50" value="<?php if($getcate){ echo $getcate->category_name; } ?>" required></td>
          </tr>
          <tr>
            <th scope="row" valign="top"></th>
            <td><input type="submit" name="<?php if($edit > 0) { echo 'editcategory'; } else { echo 'savecategory'; } ?>" v class="button button-primary" value="submit"/></td>
          </tr>
        </tbody>
      </table>
      </form>
    <?php }else { ?>
		<h2>
			<?php _e('Product Categories', 'pro-products' );?>
			<a href="admin.php?page=pro-addcategory&edit=-1" class='add-new-h2'><?php _e('Add New Product Category', 'pro-products' );?></a>
		</h2>
		<?php
				if($delete)
				{
					$del = $wpdb->delete( $cate_table, array( 'id' => $delete ) );
					if ($del === false) { ?>
						<div id="error_msg" class="error notice is-dismissible updated fade">
							 <p>Something went wrong. Please again later. </p>
							 <button type="button" class="notice-dismiss">
									 <span class="screen-reader-text">Dismiss this notice.</span>
							 </button>
						</div>
			    <?php }
					else {
			        ?>
							<div id="success_msg" class="updated notice is-dismissible updated fade">
								 <p>Category daleted successfully! </p>
								 <button type="button" class="notice-dismiss">
										 <span class="screen-reader-text">Dismiss this notice.</span>
								 </button>
							</div>
			        <?php $url = ADMIN_URL."admin.php?page=pro-addcategory";
			        ?><script>window.setTimeout(function () {
			                window.location.href = '<?php echo $url; ?>';
			            }, 1000);</script>
			        <?php
			    }
				} ?>
		<?php
			$sqlQuery = "SELECT  * FROM $cate_table ";
			$category = $wpdb->get_results($sqlQuery, OBJECT);

			$totalrows = $wpdb->get_var( "SELECT FOUND_ROWS() as found_rows" );

			if ( !empty($category) ) {
			?>
			<p class="subsubsub"><?php printf( __( "%d categories found.", 'pro-products' ), $totalrows ); ?></span></p>
			<?php
		}
		?>
		<table class="widefat" id="category_listing">
		<thead>
			<tr>
				<th><?php _e('ID', 'pro-products' );?></th>
				<th><?php _e('Name', 'pro-products' );?></th>
        <th><?php _e('Date', 'pro-products' );?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if(!$category)
				{
				?>
					<tr><td colspan="7" class="pmpro_pad20">
						<p class="create_category_p"><?php _e('Product category allow you to add new category.', 'pro-products' );?> <a href="admin.php?page=pro-addcategory&edit=-1"><?php _e('Create your first category now', 'pro-products' );?></a>.</p>
					</td></tr>
				<?php
				}
				else
				{
					$count = 0;
					foreach($category as $cat)
					{
					?>
					<tr <?php if($count++ % 2 == 1) { ?> class="alternate"<?php } ?>>
						<td><?php echo $cat->id; ?></td>
            <td><?php echo $cat->category_name; ?></td>
						<td><?php echo $cat->create_at; ?></td>
						<td>
							<a href="?page=pro-addcategory&edit=<?php echo $cat->id?>"><?php _e('edit', 'pro-products' );?></a> | <a href="javascript:askfirst('<?php echo str_replace("'", "\'", sprintf(__('Are you sure you want to delete the %s category?', 'pro-products' ), $cat->category_name));?>', '<?php echo wp_nonce_url(admin_url('admin.php?page=pro-addcategory&delete=' . $cat->id), 'delete', 'pro_discountcodes_nonce');?>'); void(0);"><?php _e('delete', 'pro-products' );?></a>
						</td>
					</tr>
					<?php
					}
				}
				?>
		</tbody>
		</table>
		<?php  } ?>
  </div>
<?php } } ?>
