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
  $content = '';
  $settings = array( 'media_buttons' => false );

  $product_table = WP_TABLE_PREFIX."products";
  $category_table = WP_TABLE_PREFIX."pro_category";
  $cate_pro_table = WP_TABLE_PREFIX."products_category";



  if (isset($_POST['create_product']) && $_POST['create_product'] != '')
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

      $product_img_url = '';

      if($_FILES['pro_image']['name'][0] != '')
    	{
        for($j=0; $j < count($_FILES['pro_image']['name']); $j++)
        {
            $product_img = $_FILES['pro_image']['name'][$j];
        		$pro_img = explode(".",$product_img);
        		if((!empty($pro_img[0])) && (!empty($pro_img[1])))
        		{
        		   $proimg = $pro_img[0].$ran.'_product.'.$pro_img[1];
        		}
        		$sourcefileupload = $_FILES['pro_image']['tmp_name'][$j];
        		$destinationupload   = $uploaddirmain. basename($proimg);
        		$product_img = PRO_URL .'/upload/'. basename($proimg);
            move_uploaded_file($sourcefileupload, $destinationupload);
            $product_img_url .= $product_img.',';
        }
      }

      $certificate_img_url = '';
      if($_FILES['certificates_image']['name'][0] != '')
    	{
        for($i=0; $i < count($_FILES['certificates_image']['name']); $i++)
        {
          $certpic = $_FILES['certificates_image']['name'][$i];
      		$pic1 = explode(".",$certpic);
      		if((!empty($pic1[0])) && (!empty($pic1[1])))
      		{
      		   $certimg = $pic1[0].$ran.'_certificate.'.$pic1[1];
      		}
      		$sourcecertifileupload = $_FILES['certificates_image']['tmp_name'][$i];
      		$destinationcertupload   = $uploaddirmain. basename($certimg);
      		$certpic = PRO_URL .'/upload/'. basename($certimg);
          move_uploaded_file($sourcecertifileupload, $destinationcertupload);
          $certificate_img_url .= $certpic.',';
        }
      }
      $wpdb->flush();
      $wpdb->insert( $product_table, array(
                'user_id' => get_current_user_id(),
                'product_name' => $_POST['product_name'],
                'product_image' => $product_img_url,
                'health_attributes' => htmlentities($_POST['health_attributes']),
                'allergens' => htmlentities($_POST['allergens']),
                'Ingredients' => $searilized_data,
                'certificates' => $certificate_img_url,
                'other_info' => htmlentities($_POST['other_info']),
                'create_at' => date('Y-m-d')
              ));
      echo $wpdb->print_error();
      if ($wpdb->insert_id == FALSE) { ?>
        <div id="error_msg" class="error notice is-dismissible">
            <p>Something went wrong. Please again later. </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>
      <?php
        } else {
          $product_id = $wpdb->insert_id;
          $wpdb->insert( $cate_pro_table, array(
                    'product_id' => $product_id,
                    'category_id' => $_POST['pro_cat'],
                  ));
        ?>
        <div id="success_msg" class="updated notice is-dismissible">
            <p>Post published. </p>
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
?>
<div class="wrap">
<?php echo "<h1 class='wp-heading-inline'>" . __('Add New Product') . "</h1>"; ?>
    <hr class="wp-header-end">
    <form action="" method="post" enctype="multipart/form-data">
        <table class="form-table">
            <tbody>
                <tr class="form-field form-required">
                    <th scope="row"><label>Product Name</label></th>
                    <td><input name="product_name" type="text" id="product_title" value="" required></td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row"><label>product_image</label></th>
                    <td>
                      <div id="div_pro_images">
                          <p>
                              <input name="pro_image[]" type="file" value="">
                          </p>
                      </div>
                      <a href="#" id="addpro_img">Add Another Product Image</a>
                    </td>
                </tr>
                 <tr class="form-field form-required">
                    <th scope="row"><label>Product Category</label></th>
                    <td><select name="pro_cat"><option value="">Select Product category</option>
                      <?php if($totalcatrows >0){
                        foreach($products_category as $pro_cat)
                        {
                          ?>
                          <option value="<?php echo $pro_cat->id; ?>"><?php echo $pro_cat->category_name; ?></option>
                      <?php  }
                      } ?>
                    </select></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="last_name">Health Attributes </label></th>
                    <td><?php
                          $editor_id = 'health_attributes';
                          wp_editor( $content, $editor_id, $settings );?>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="last_name">Allergens</label></th>
                    <td><?php
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

                          <?php $unseralized = unserialize($getproduct->Ingredients); ?>
                          <?php echo "<pre>"; print_r($unseralized); echo "</pre>"; //foreach($unseralized[0] as $product_array){ echo "<pre>"; print_r($product_array); echo "</pre>";?>

                          <?php //} ?>
                          <div class="removeme">
                          <div>
                            <label>Active Health Ingredients</label>
                              <input type="text" name="active_health0" value="" style="width:25%" required/>
                            <label>Fact:</label>
                              <input type="checkbox" name="active_health_fact0" value="active_health_fact0"/>
                            <label>Claim:</label>
                              <input type="checkbox" name="active_health_claim0" value="active_health_claim0" <?php echo $claim_disable; ?>/>
                          </div>
                          <div>
                            <label>Ingredient Health Claim </label>
                              <input type="text" name="Ingrednt_health10" value="" style="width:25%"/>
                            <label>Fact : </label>
                              <input type="checkbox" name="Ingrednt_health_fact10" value="Ingrednt_health_fact10"/>
                            <label> Claim: </label>
                              <input type="checkbox" name="Ingrednt_health_claim10" value="Ingrednt_health_claim10" <?php echo $claim_disable; ?>/>
                          </div>
                          <div>
                            <label>Ingredient Health Claim </label>
                              <input type="text" name="Ingrednt_health20" value="" style="width:25%"/>
                            <label>Fact : </label>
                              <input type="checkbox" name="Ingrednt_health_fact20" value="Ingrednt_health_fact20"/>
                            <label> Claim: </label>
                              <input type="checkbox" name="Ingrednt_health_claim20" value="Ingrednt_health_claim20" <?php echo $claim_disable; ?>/>
                          </div>
                          <div>
                            <label>Ingredient Health Claim </label>
                              <input type="text" name="Ingrednt_health30" value="" style="width:25%"/>
                            <label>Fact : </label>
                              <input type="checkbox" name="Ingrednt_health_fact30" value="Ingrednt_health_fact30"/>
                            <label> Claim: </label>
                              <input type="checkbox" name="Ingrednt_health_claim30" value="Ingrednt_health_claim30" <?php echo $claim_disable; ?>/>
                          </div>
                          <div class="<?php echo $div_class;?>">
                            <label>Ingredient Health Claim </label>
                              <input type="text" name="Ingrednt_health40" value="" style="width:25%" class="<?php echo $class;?>" <?php echo $disable; ?>/>
                            <label>Fact:</label>
                              <input type="checkbox" name="Ingrednt_health_fact40" value="Ingrednt_health_fact40" class="<?php echo $class;?>" <?php echo $disable; ?>/>
                            <label> Claim: </label>
                              <input type="checkbox" name="Ingrednt_health_claim40" value="Ingrednt_health_claim40" class="<?php echo $class;?>" <?php echo $disable; ?>/>
                          </div>
                          <div class="<?php echo $div_class;?>">
                            <label>Ingredient Health Claim </label><input type="text" name="Ingrednt_health50" value="" style="width:25%" class="<?php echo $class;?>" <?php echo $disable; ?>/>
                              <label>Fact : </label> <input type="checkbox" name="Ingrednt_health_fact50" value="Ingrednt_health_fact50" class="<?php echo $class;?>" <?php echo $disable; ?>/>
                              <label> Claim: </label><input type="checkbox" name="Ingrednt_health_claim50" value="Ingrednt_health_claim50" class="<?php echo $class;?>" <?php echo $disable; ?>/>
                          </div>
                        </div>
                      </div>
                        <input type="hidden" name="total_fields" value="0" id="total_fields"/>
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
                            <p>
                                <input name="certificates_image[]" type="file" value="">
                            </p>
                        </div>
                        <a href="#" id="addcertificate">Add Another Certificate</a>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label>Other Info</label></th>
                    <td>
                      <?php
                            $editor_id = 'other_info';
                            wp_editor( $content, $editor_id, $settings );
                      ?>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row"></th>
                    <td>
                      <input type="submit" name="create_product" id="create_product" class="button button-primary" value="Add New Product">
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<script>
$(function() {

        var proDiv = $('#div_pro_images');
        var k = $('#div_pro_images p').size() + 1;

        $('#addpro_img').on('click', function()
        {
            if(k >= 9)
            {
                return false;
            }
            if(k === 8)
            {
              $("#addpro_img").hide();
            }
            $('<p><input name="pro_image[]" type="file" value=""><a href="#" id="remimg">Remove</a></p>').appendTo(proDiv);
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
        var i = $('#div_certificates p').size() + 1;

        $('#addcertificate').on('click', function()
        {
            if(i >= 9)
            {
                return false;
            }
            if(i === 8)
            {
              $("#addcertificate").hide();
            }
            $('<p><input name="certificates_image[]" type="file" value=""><a href="#" id="remCertificate">Remove</a></p>').appendTo(scntDiv);
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
        var j = 1;

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
