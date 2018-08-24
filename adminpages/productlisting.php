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
	else {
	global $wpdb;
	$product_table = WP_TABLE_PREFIX."products";
	$category_table = WP_TABLE_PREFIX."pro_category";
	$cate_pro_table = WP_TABLE_PREFIX."products_category";

	if(isset($_REQUEST['delete']))
		$delete = intval($_REQUEST['delete']);
	else
		$delete = false;
	if(isset($_REQUEST['edit']))
		$edit = intval($_REQUEST['edit']);
	else
		$edit = false;
	?>

	<div class="wrap">
	<?php if($edit){

	if (isset($_POST['update_product']) && $_POST['update_product'] != '')
	{
			$array_active_health = [];
			for($i=0; $i <= $_POST['total_fields']; $i++)
			{
					$post_array  = array(
							'active_health'.$i => $_POST['active_health'.$i],
							'active_health_fact'.$i  => $_POST['active_health_fact'.$i],
							'active_health_claim'.$i => $_POST['active_health_claim'.$i],

							'Ingrednt_health1'.$i => $_POST['Ingrednt_health1'.$i],
							'Ingrednt_health_fact1'.$i => $_POST['Ingrednt_health_fact1'.$i],
							'Ingrednt_health_claim1'.$i  => $_POST['Ingrednt_health_claim1'.$i],

							'Ingrednt_health2'.$i  => $_POST['Ingrednt_health2'.$i],
							'Ingrednt_health_fact2'.$i => $_POST['Ingrednt_health_fact2'.$i],
							'Ingrednt_health_claim2'.$i => $_POST['Ingrednt_health_claim2'.$i],

							'Ingrednt_health3'.$i  => $_POST['Ingrednt_health3'.$i],
							'Ingrednt_health_fact3'.$i => $_POST['Ingrednt_health_fact3'.$i],
							'Ingrednt_health_claim3'.$i => $_POST['Ingrednt_health_claim3'.$i],

							'Ingrednt_health4'.$i  => $_POST['Ingrednt_health4'.$i],
							'Ingrednt_health_fact4'.$i => $_POST['Ingrednt_health_fact4'.$i],
							'Ingrednt_health_claim4'.$i => $_POST['Ingrednt_health_claim4'.$i],

							'Ingrednt_health5'.$i  => $_POST['Ingrednt_health5'.$i],
							'Ingrednt_health_fact5'.$i => $_POST['Ingrednt_health_fact5'.$i],
							'Ingrednt_health_claim5'.$i => $_POST['Ingrednt_health_claim5'.$i]);
							array_push($array_active_health,$post_array);
			}
			$array = ['total_fields'=>$_POST['total_fields'],$array_active_health];
			$searilized_data = serialize($array);
			$ran = rand(10,100);

			$uploaddirmain = PRO_FILE_PATH.'/upload/';

			for($j=0; $j<=$_POST['total_p_images']; $j++)
			{
					if (!is_uploaded_file($_FILES['pro_image'.$j]['tmp_name']))
					{
							$product_img_url .= $_POST['pimages'.$j].',';
					}
					else
					{
							$product_img = $_FILES['pro_image'.$j]['name'];
							$pro_img = explode(".",$product_img);
							if((!empty($pro_img[0])) && (!empty($pro_img[1])))
							{
								 $proimg = $pro_img[0].$ran.'_product.'.$pro_img[1];
							}
							$sourcefileupload = $_FILES['pro_image'.$j]['tmp_name'];
							$destinationupload   = $uploaddirmain. basename($proimg);
							$product_img = PRO_URL .'/upload/'. basename($proimg);
							move_uploaded_file($sourcefileupload, $destinationupload);
							$product_img_url .= $product_img.',';
					}
			}
			for($i=0; $i<=$_POST['total_cert_images']; $i++)
			{
					if (!is_uploaded_file($_FILES['certificates_image'.$i]['tmp_name']))
					{
							$certificate_img_url .= $_POST['cimages'.$i].',';
					}
					else
					{
						$certpic = $_FILES['certificates_image'.$i]['name'];
						$pic1 = explode(".",$certpic);
						if((!empty($pic1[0])) && (!empty($pic1[1])))
						{
							 $certimg = $pic1[0].$ran.'_certificate.'.$pic1[1];
						}
						$sourcecertifileupload = $_FILES['certificates_image'.$i]['tmp_name'];
						$destinationcertupload   = $uploaddirmain. basename($certimg);
						$certpic = PRO_URL .'/upload/'. basename($certimg);
						move_uploaded_file($sourcecertifileupload, $destinationcertupload);
						$certificate_img_url .= $certpic.',';
					}
			}

	    $update = $wpdb->update( $product_table, array(
				'product_name' => $_POST['product_name'],
				'product_image' => $product_img_url,
				'health_attributes' => $_POST['health_attri'],
				'allergens' => $_POST['allergens'],
				'Ingredients' => $searilized_data,
				'certificates' => $certificate_img_url,
				'other_info' => $_POST['other_info']
			),
			array( 'id' => $edit , 'user_id' => get_current_user_id()),
			array(
			));
			if ($update === false) { ?>
	      <div id="error_msg" class="error notice is-dismissible">
	          <p>Something went wrong. Please again later. </p>
	          <button type="button" class="notice-dismiss">
	              <span class="screen-reader-text">Dismiss this notice.</span>
	          </button>
	      </div>
	    <?php
	      } else {
					$update = $wpdb->update( $cate_pro_table, array(
						'category_id' => $_POST['pro_cat'],
					),
					array( 'id' => $_POST['cate_id'], 'product_id' => $edit),
					array(
					));

	      ?>
	      <div id="success_msg" class="updated notice is-dismissible">
	          <p>Post updated successfully. </p>
	          <button type="button" class="notice-dismiss">
	              <span class="screen-reader-text">Dismiss this notice.</span>
	          </button>
	      </div>
	      <?php
	    }
	}

		$sqlQuery = "SELECT  * FROM $category_table ";
		$products_category = $wpdb->get_results($sqlQuery, OBJECT);
		$totalcatrows = $wpdb->get_var( "SELECT FOUND_ROWS() as found_rows" );
		$getproduct = $wpdb->get_row( "SELECT *, $product_table.id as pro_ID, $cate_pro_table.id as cat_id  FROM $product_table join $cate_pro_table on $product_table.id = $cate_pro_table.product_id WHERE $product_table.id = $edit" );

		?>
		<?php echo "<h1 class='wp-heading-inline'>" . __('Edit Product') . "</h1>"; ?>
	    <hr class="wp-header-end">
	    <form action="" method="post" enctype="multipart/form-data">
	        <table class="form-table">
	            <tbody>
	                <tr class="form-field">
	                    <th scope="row"><label>Product Name</label></th>
	                    <td><input name="product_name" type="text" id="product_title" value="<?php echo $getproduct->product_name; ?>" required></td>
	                </tr>
	                <tr class="form-field">
	                    <th scope="row"><label>product_image</label></th>
	                    <td>
												<div id="div_pro_images">
												<?php
														if($getproduct->product_image != '')
														{
															$pro_img = explode(',',$getproduct->product_image);
															for($i=0; $i<count(array_filter($pro_img)); $i++)
															{
															?>
															<p>
																<input name="pro_image<?php echo $i; ?>" type="file" value="">
																<input type="hidden" name='pimages<?php echo $i; ?>' value="<?php echo $pro_img[$i]; ?>"/>
																<img src="<?php echo $pro_img[$i]; ?>" style="width:30px; height:30px;"/></p>
													<?php } ?>
															<input type="hidden" name="total_p_images" id="total_p_images" value="<?php echo count(array_filter($pro_img)); ?>"/>
														<?php }
														else {
															?>
																<input name="pro_image0" type="file" value="">
																<input type="hidden" name="total_p_images" id="total_p_images" value="0"/>
												<?php
														}
														 ?>
											</div>
											<a href="#" id="addpro_img">Add Another Product Image</a>
											</td>
	                </tr>
	                <tr class="form-field">
	                    <th scope="row"><label>Product Category</label></th>
	                    <td>
												<select name="pro_cat"><option value="">Select Product category</option>
	                      <?php if($totalcatrows >0){
	                        foreach($products_category as $pro_cat)
	                        {
	                        	if($getproduct->category_id == $pro_cat->id)
	                        	{
	                        		$selected = 'selected';
	                        	}
	                        	else
	                        	{
	                        		$selected = '';
	                        	}
	                          ?>
														<option value="<?php echo $pro_cat->id; ?>" <?php echo $selected; ?>><?php echo $pro_cat->category_name; ?></option>
	                      <?php  }
	                      } ?>
	                    </select>
											<input type="hidden" name="cate_id" value="<?php echo $getproduct->cat_id; ?>"/>
										</td>
	                </tr>
	                <tr class="form-field">
	                    <th scope="row"><label for="last_name">Health Attributes </label></th>
	                    <td><?php
	                    	  $content = $getproduct->health_attributes;
	                          $editor_id = 'health_attri';
	                          wp_editor( $content, $editor_id, $settings );?>
	                    </td>
	                </tr>
	                <tr class="form-field">
	                    <th scope="row"><label for="last_name">Allergens</label></th>
	                    <td><?php
	                    	  $content = $getproduct->allergens;
	                          $editor_id = 'allergens';
	                          wp_editor( $content, $editor_id, $settings );?>
	                    </td>
	                </tr>
	                <tr class="form-field">
	                    <th scope="row"><label for="last_name">Ingredients & Claims</label></th>
	                    <td>
	                        <hr/>
	                    </td>
	                </tr>
	                <tr class="form-field">
	                    <th scope="row"></th>
	                    <td>
	                      <div id="Ingredients_div">
	                        <?php
	                          if($membership != '' && $membership['ID'] == 1)
	                          {
	                              $disable = 'disabled';
	                              $class = "disabled";
	                              $div_class = "disabled_div";
	                              $claim_disable = 'disabled';
	                          }
	                          elseif ($membership != '' && $membership['ID'] == 2) {
	                            $disable = 'disabled';
	                            $class = "disabled";
	                            $div_class = "disabled_div";
	                            $claim_disable = '';
	                          } else { $disable = ''; $class = ''; $div_class = ''; $claim_disable = '';} ?>
														<?php $unseralized = unserialize($getproduct->Ingredients);
																	$h = 0;
																	foreach($unseralized[0] as $health_data)
																	{
																?>
														 <div class="removeme">
	                           <div>
	                             <label>Active Health Ingredients</label>
	                               <input type="text" name="active_health<?php echo $h; ?>" value="<?php echo $health_data['active_health'.$h]; ?>" style="width:25%" required/>
	                             <label>Fact:</label>
															 	 <?php
																 		if($health_data['active_health_fact'.$h] != '')
																		{
																			$checked = 'checked';
																		}
																		else {
																			$checked='';
																		} ?>
	                               <input type="checkbox" name="active_health_fact<?php echo $h; ?>" value="active_health_fact0" <?php echo $checked; ?>/>
	                             <label>Claim:</label>
															 	<?php
																	if($health_data['active_health_claim'.$h] != '')
																	{
																		$checked = 'checked';
																	}
																	else {
																		$checked='';
																	} ?>
	                               <input type="checkbox" name="active_health_claim<?php echo $h; ?>" value="active_health_claim0" <?php echo $claim_disable; ?> <?php echo $checked; ?>/>
	                           </div>
	                           <div>
	                             <label>Ingredient Health Claim </label>
	                               <input type="text" name="Ingrednt_health1<?php echo $h; ?>" value="<?php echo $health_data['Ingrednt_health1'.$h]; ?>" style="width:25%"/>
	                             <label>Fact : </label>
															 <?php
																if($health_data['Ingrednt_health_fact1'.$h] != '')
																{
																	$checked = 'checked';
																}
																else {
																	$checked='';
																} ?>
	                               <input type="checkbox" name="Ingrednt_health_fact1<?php echo $h; ?>" value="Ingrednt_health_fact1<?php echo $h; ?>" <?php echo $checked; ?>/>
	                             <label> Claim: </label>
																 <?php
																 if($health_data['Ingrednt_health_claim1'.$h] != '')
																 {
																	 $checked = 'checked';
																 }
																 else {
																	 $checked='';
																 } ?>
	                               <input type="checkbox" name="Ingrednt_health_claim1<?php echo $h; ?>" value="Ingrednt_health_claim1<?php echo $h; ?>" <?php echo $claim_disable; ?> <?php echo $checked; ?>/>
	                           </div>
	                           <div>
	                             <label>Ingredient Health Claim </label>
	                               <input type="text" name="Ingrednt_health2<?php echo $h; ?>" value="<?php echo $health_data['Ingrednt_health2'.$h]; ?>" style="width:25%"/>
	                             <label>Fact : </label>
																 <?php
																 if($health_data['Ingrednt_health_fact2'.$h] != '')
																 {
																	 $checked = 'checked';
																 }
																 else {
																	 $checked='';
																 } ?>
	                               <input type="checkbox" name="Ingrednt_health_fact2<?php echo $h; ?>" value="Ingrednt_health_fact2<?php echo $h; ?>" <?php echo $checked; ?>/>
	                             <label> Claim: </label>
																	 <?php
																	 if($health_data['Ingrednt_health_claim2'.$h] != '')
																	 {
																		 $checked = 'checked';
																	 }
																	 else {
																		 $checked='';
																	 } ?>
	                               <input type="checkbox" name="Ingrednt_health_claim2<?php echo $h; ?>" value="Ingrednt_health_claim2<?php echo $h; ?>" <?php echo $claim_disable; ?> <?php echo $checked; ?>/>
	                           </div>
	                           <div>
	                             <label>Ingredient Health Claim </label>
	                               <input type="text" name="Ingrednt_health3<?php echo $h; ?>" value="<?php echo $health_data['Ingrednt_health3'.$h]; ?>" style="width:25%"/>
	                             <label>Fact : </label>
																 <?php
																 if($health_data['Ingrednt_health_fact3'.$h] != '')
																 {
																	 $checked = 'checked';
																 }
																 else {
																	 $checked='';
																 } ?>
	                               <input type="checkbox" name="Ingrednt_health_fact3<?php echo $h; ?>" value="Ingrednt_health_fact3<?php echo $h; ?>" <?php echo $checked; ?>/>
	                             <label> Claim: </label>
																 <?php
																 if($health_data['Ingrednt_health_claim3'.$h] != '')
																 {
																	 $checked = 'checked';
																 }
																 else {
																	 $checked='';
																 } ?>
	                               <input type="checkbox" name="Ingrednt_health_claim3<?php echo $h; ?>" value="Ingrednt_health_claim3<?php echo $h; ?>" <?php echo $claim_disable; ?> <?php echo $checked; ?>/>
	                           </div>
	                           <div class="<?php echo $div_class;?>">
	                             <label>Ingredient Health Claim </label>
	                               <input type="text" name="Ingrednt_health4<?php echo $h; ?>" value="<?php echo $health_data['Ingrednt_health4'.$h]; ?>" style="width:25%" class="<?php echo $class;?>" <?php echo $disable; ?>/>
	                             <label>Fact:</label>
																 <?php
																 if($health_data['Ingrednt_health_fact4'.$h] != '')
																 {
																	 $checked = 'checked';
																 }
																 else {
																	 $checked='';
																 } ?>
	                               <input type="checkbox" name="Ingrednt_health_fact4<?php echo $h; ?>" value="Ingrednt_health_fact4<?php echo $h; ?>" class="<?php echo $class;?>" <?php echo $disable; ?> <?php echo $checked; ?>/>
	                             <label> Claim: </label>
																 <?php
																 if($health_data['Ingrednt_health_claim4'.$h] != '')
																 {
																	 $checked = 'checked';
																 }
																 else {
																	 $checked='';
																 } ?>
	                               <input type="checkbox" name="Ingrednt_health_claim4<?php echo $h; ?>" value="Ingrednt_health_claim4<?php echo $h; ?>" class="<?php echo $class;?>" <?php echo $disable; ?> <?php echo $checked; ?>/>
	                           </div>
	                           <div class="<?php echo $div_class;?>">
	                             <label>Ingredient Health Claim </label>
															 	<input type="text" name="Ingrednt_health5<?php echo $h; ?>" value="<?php echo $health_data['Ingrednt_health5'.$h]; ?>" style="width:25%" class="<?php echo $class;?>" <?php echo $disable; ?>/>
	                               <label>Fact : </label>
																	 <?php
																	if($health_data['Ingrednt_health_fact5'.$h] != '')
																	{
																		$checked = 'checked';
																	}
																	else {
																		$checked='';
																	} ?>
																 	<input type="checkbox" name="Ingrednt_health_fact5<?php echo $h; ?>" value="Ingrednt_health_fact5<?php echo $h; ?>" class="<?php echo $class;?>" <?php echo $disable; ?> <?php echo $checked; ?>/>
	                               <label> Claim: </label>
																	 <?php
																	if($health_data['Ingrednt_health_claim5'.$h] != '')
																	{
																		$checked = 'checked';
																	}
																	else {
																		$checked='';
																	} ?>
																 	<input type="checkbox" name="Ingrednt_health_claim5<?php echo $h; ?>" value="Ingrednt_health_claim5<?php echo $h; ?>" class="<?php echo $class;?>" <?php echo $disable; ?> <?php echo $checked; ?>/>
	                           </div>
	                         </div>
												 <?php $h++; } ?>
	                      </div>
	                        <input type="hidden" name="total_fields" value="<?php echo $unseralized['total_fields']; ?>" id="total_fields"/>
	                        <?php
	                          if($membership != '' && $membership['ID'] != 1)
	                          { ?>
	                            <a href="javascript:void(0);" id="addIngredient">Add Another Ingredient</a>
	                          <?php }
	                          else { ?>
	                              <a href="javascript:void(0);" id="addIngredient_1">Add Another Ingredient</a>
	                        <?php  }
	                        ?>
	                    </td>
	                </tr>
	                <tr class="form-field">
	                    <th scope="row"><label>Certificates</label></th>
	                    <td>
	                        <div id="div_certificates">
	                            <?php
																	if($getproduct->certificates != '')
																	{
																		$certificate_img = explode(',',$getproduct->certificates);
		                            	  for($i=0; $i<count(array_filter($certificate_img)); $i++)
		                            	  {
		                            	  ?>
		                            	  <p>
																			<input name="certificates_image<?php echo $i; ?>" type="file" value="">
																			<input type="hidden" name='cimages<?php echo $i; ?>' value="<?php echo $certificate_img[$i]; ?>"/>
		                            	  	<img src="<?php echo $certificate_img[$i]; ?>" style="width:30px; height:30px;"/></p>
	                        	  <?php } ?>
																		<input type="hidden" name="total_cert_images" id="total_cert_images" value="<?php echo count(array_filter($certificate_img)); ?>"/>
																<?php }
																else { ?>
																	<input name="certificates_image0" type="file" value="">
																	<input type="hidden" name="total_cert_images" id="total_cert_images" value="0"/>
													<?php } ?>
	                        </div>
	                        <a href="#" id="addcertificate">Add Another Certificate</a>
	                    </td>
	                </tr>
	                <tr class="form-field">
	                    <th scope="row"><label>Other Info</label></th>
	                    <td>
	                      <?php
	                      		$content = $getproduct->other_info;
	                            $editor_id = 'other_info';
	                            wp_editor( $content, $editor_id, $settings );
	                      ?>
	                    </td>
	                </tr>
	                <tr class="form-field">
	                    <th scope="row"></th>
	                    <td>
	                      <input type="submit" name="update_product" id="update_product" class="button button-primary" value="Save">
	                    </td>
	                </tr>
	            </tbody>
	        </table>

	    </form>
	<?php }
	else
	{ ?>
	<?php    echo "<h2>" . __( 'Products' ) ; ?>  <a href='<?php echo ADMIN_URL;  ?>admin.php?page=pro-addnew' class='add-new-h2'>Add New Product</a> </h2>

		<?php
				if($delete)
				{
					$del = $wpdb->delete( $product_table, array( 'id' => $delete ) );
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
								 <p>Product daleted successfully! </p>
								 <button type="button" class="notice-dismiss">
										 <span class="screen-reader-text">Dismiss this notice.</span>
								 </button>
							</div>
							<?php $url = ADMIN_URL."admin.php?page=pro-products";
							?>
							<script>window.setTimeout(function () {
											window.location.href = '<?php echo $url; ?>';
									}, 1000);</script>
							<?php
					}
				} ?>
	<?php
	   $userId = get_current_user_id();
	   if($role[0] == 'administrator')
	   {
	     $sqlQuery = "SELECT  * FROM $product_table";
	   }
	   else {
	     $sqlQuery = "SELECT  * FROM $product_table where user_id = $userId";
	   }

		 $products = $wpdb->get_results($sqlQuery, OBJECT);

		 $totalrows = $wpdb->get_var( "SELECT FOUND_ROWS() as found_rows" );

		 if ( !empty($products) ) {
		 ?>
		 <p class="subsubsub"><?php printf( __( "%d Product found.", 'pro-products' ), $totalrows ); ?></span></p>
		 <?php
	 }
	?>
	 <table class="widefat" id="category_listing" id="product_table_listing">
		 <thead>
			 <tr>
				 <th><?php _e('ID', 'pro-products' );?></th>
				 <th><?php _e('Product Name', 'pro-products' );?></th>
				 <th><?php _e('Health Attributes', 'pro-products' );?></th>
				 <th><?php _e('Allergens', 'pro-products' );?></th>
				 <th><?php _e('Rating', 'pro-products' );?></th>
				 <th><?php _e('Likes', 'pro-products' );?></th>
				 <th><?php _e('Date', 'pro-products' );?></th>
	       <th></th>
			 </tr>
		 </thead>
		 <tbody>
			 <?php
				 if(!$products)
				 {
				 ?>
					 <tr><td colspan="7" class="pmpro_pad20">
						 <p class="create_category_p"><?php _e('Products allow you to add new product.', 'pro-products' );?> <a href="admin.php?page=pro-addnew"><?php _e('Create your first product now', 'pro-products' );?></a>.</p>
					 </td></tr>
				 <?php
				 }
				 else
				 {
					 $count = 0;
					 foreach($products as $pro)
					 {
					 ?>
					 <tr <?php if($count++ % 2 == 1) { ?> class="alternate"<?php } ?>>
						 <td><?php echo $pro->id; ?></td>
						 <td><?php echo $pro->product_name; ?></td>
						 <td><?php if($pro->health_attributes != ''){ echo "Yes";} else { echo "No"; } ?></td>
						 <td><?php if($pro->allergens != ''){ echo "Yes";} else { echo "No"; } ?></td>
						 <td><?php echo $pro->rating; ?></td>
						 <td><?php echo $pro->likes; ?></td>
						 <td><?php echo $pro->create_at; ?></td>
	           <?php if( $pro->user_id ==$userId) { ?>
						 <td>
							 <a href="<?php echo admin_url('admin.php?page=pro-products&edit='. $pro->id); ?>"><?php _e('edit', 'pro-products' );?></a> | <a href="javascript:askfirst('<?php echo str_replace("'", "\'", sprintf(__('Are you sure you want to delete the %s product?', 'pro-products' ), $pro->product_name));?>', '<?php echo wp_nonce_url(admin_url('admin.php?page=pro-products&delete='. $pro->id), 'delete', 'pro_discountcodes_nonce');?>'); void(0);"><?php _e('delete', 'pro-products' );?></a>
						 </td>
	         <?php }else {
	           echo '<td></td>';
	         } ?>
					 </tr>
					 <?php
					 }
				 }
				 ?>
		 </tbody>
	 </table>
	<?php } ?>
	</div>

	<!--  SHOW LIST ON THE PAGE ENDS HERE!!     -->
	<script>
	$(function() {

	        var proDiv = $('#div_pro_images');

					if($("#total_p_images").val() !== 0)
					{
							var k = $("#total_p_images").val();
					}
					else {
							var k = parseInt($("#total_p_images").val())+1;
					}
	        $('#addpro_img').on('click', function()
	        {
							if(k >= 8)
							{
									return false;
							}
							if(k === 7)
							{
								$("#addpro_img").hide();
							}
							$("#total_p_images").val(parseInt(k)+1);
	            $('<p><input name="pro_image'+k+'" type="file" value=""><a href="#" id="remimg">Remove</a></p>').appendTo(proDiv);
	            k++;
	            return false;
	        });

	        $(document).on('click','#remimg', function()
	        {
	            if( k > 2 )
	            {
	                $(this).parents('p').remove();
	                k--;
	            }
	            return false;
	        });

	        var scntDiv = $('#div_certificates');
					if($("#total_cert_images").val() !== 0)
					{
							var i = $("#total_cert_images").val();
					}
					else {
							var i = parseInt($("#total_cert_images").val())+1;
					}
	        $('#addcertificate').on('click', function()
	        {
							if(i >= 8)
							{
									return false;
							}
							if(i === 7)
							{
								$("#addcertificate").hide();
							}
							$("#total_cert_images").val(parseInt(i)+1);
	            $('<p><input name="certificates_image'+i+'" type="file" value=""><a href="#" id="remCertificate">Remove</a></p>').appendTo(scntDiv);
	            i++;
	            return false;
	        });

	        $(document).on('click','#remCertificate', function()
	        {
	            if( i > 2 )
	            {
	                $(this).parents('p').remove();
	                i--;
	            }
	            return false;
	        });

	        var ingredientsDiv = $('#Ingredients_div');
	        var j = parseInt($("#total_fields").val()) + 1;
					$('#addIngredient').on('click', function()
	        {
	            if(j >= 5)
	            {
	                return false;
	            }
	            <?php
	              if($membership != '' && $membership['ID'] == 2)
	              { ?>
	                $("#addIngredient").hide();
	            <?php } ?>
	            if(j === 4)
	            {
	              $("#addIngredient").hide();
	            }
	            $("#total_fields").val(j);
	            $('<div class="removeme">'+
	                '<div>'+
	                    '<label>Active Health Ingredients</label>'+
	                    '<input type="text" name="active_health'+j+'" value="" style="width:25%" required/>'+
	                    '<label>Fact:</label> <input type="checkbox" name="active_health_fact'+j+'" value="health_fact'+j+'"/>'+
	                    '<label>Claim:</label> <input type="checkbox" name="active_health_claim'+j+'" value="claim_fact'+j+'"/>'+
	                '</div>'+
	              '<div>'+
	                  '<label>Ingredient Health Claim </label>'+
	                  '<input type="text" name="Ingrednt_health1'+j+'" value="" style="width:25%"/>'+
	                  '<label>Fact : </label><input type="checkbox" name="Ingrednt_health_fact1'+j+'" value="Ingrednt_health_fact1'+j+'"/>'+
	                  '<label> Claim: </label><input type="checkbox" name="Ingrednt_health_claim1'+j+'" value="Ingrednt_health_claim1'+j+'"/>'+
	              '</div>'+
	              '<div>'+
	                  '<label>Ingredient Health Claim </label>'+
	                  '<input type="text" name="Ingrednt_health2'+j+'" value="" style="width:25%"/>'+
	                  '<label>Fact : </label> <input type="checkbox" name="Ingrednt_health_fact2'+j+'" value="Ingrednt_health_fact2'+j+'"/>'+
	                  '<label> Claim: </label> <input type="checkbox" name="Ingrednt_health_claim2'+j+'" value="Ingrednt_health_claim2'+j+'"/>'+
	              '</div>'+
	              '<div>'+
	                  '<label>Ingredient Health Claim </label>'+
	                  '<input type="text" name="Ingrednt_health3'+j+'" value="" style="width:25%"/>'+
	                  '<label>Fact : </label> <input type="checkbox" name="Ingrednt_health_fact3'+j+'" value="Ingrednt_health_fact3'+j+'"/>'+
	                  '<label> Claim: </label> <input type="checkbox" name="Ingrednt_health_claim3'+j+'" value="Ingrednt_health_claim3'+j+'"/>'+
	              '</div>'+
	              '<div class="<?php echo $div_class;?>">'+
	                  '<label>Ingredient Health Claim </label>'+
	                  '<input type="text" name="Ingrednt_health4'+j+'" value="" style="width:25%" class="<?php echo $class;?>" <?php echo $disable; ?> />'+
	                  '<label>Fact : </label> <input type="checkbox" name="Ingrednt_health_fact4'+j+'" value="Ingrednt_health_fact4'+j+'" class="<?php echo $class;?>" <?php echo $disable; ?> />'+
	                  '<label> Claim: </label> <input type="checkbox" name="Ingrednt_health_claim4'+j+'" value="Ingrednt_health_claim4'+j+'" <?php echo $class; ?> <?php echo $disable; ?> />'+
	              '</div>'+
	              '<div class="<?php echo $div_class;?>">'+
	                  '<label>Ingredient Health Claim </label>'+
	                  '<input type="text" name="Ingrednt_health5'+j+'" value="" style="width:25%" class="<?php echo $class;?>" <?php echo $disable; ?> />'+
	                  '<label>Fact : </label> <input type="checkbox" name="Ingrednt_health_fact5'+j+'" value="Ingrednt_health_fact5'+j+'" class="<?php echo $class;?>" <?php echo $disable; ?> />'+
	                  '<label> Claim: </label><input type="checkbox" name="Ingrednt_health_claim5'+j+'" value="Ingrednt_health_claim5'+j+'" class="<?php echo $class;?>" <?php echo $disable; ?> />'+
	              '</div>'+
	            '<a href="#" id="remIngredient">Remove</a></div>').appendTo(ingredientsDiv);
	            j++;
	            return false;
	        });
	        $(document).on('click','#remIngredient', function()
	        {
	            if( j >= 2 )
	            {
	                $("#total_fields").val(j-2);
	                $("#addIngredient").show();
	                $(this).closest('div').remove();
	                j--;
	            }
	            return false;
	        });
	        $(document).on("click",".disabled_div",function(e)
	        {
	          alert("Please subscribe to paid membership to enable more fields");
	        });
	        $(document).on("click","#addIngredient_1",function(e)
	        {
	          alert("Please subscribe to paid membership to enable this option");
	          return false;
	        });
	});
	</script>
<?php } } ?>
