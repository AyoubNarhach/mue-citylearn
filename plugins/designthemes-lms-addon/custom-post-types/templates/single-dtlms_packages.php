<?php get_header('dtlms'); ?>

    <?php
    /**
    * dtlms_before_main_content hook.
    */
    do_action( 'dtlms_before_main_content' );
    ?>

        <?php
        /**
        * dtlms_before_content hook.
        */
        do_action( 'dtlms_before_content' );
        ?>

			<?php
			if( have_posts() ): while( have_posts() ): the_post();

				$package_id = get_the_ID(); 
				$package_title = get_the_title();
				$package_permalink = get_permalink();

				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;


				$purchased_class_courses = dtlms_get_user_purchased_class_courses($user_id);
				$purchased_class_courses = (is_array($purchased_class_courses) && !empty($purchased_class_courses)) ? $purchased_class_courses : array();

				$purchased_courses = get_user_meta($user_id, 'purchased_courses', true);
				$purchased_courses = (is_array($purchased_courses) && !empty($purchased_courses)) ? $purchased_courses : array();

				$assigned_courses = get_user_meta($user_id, 'assigned_courses', true);
				$assigned_courses = (is_array($assigned_courses) && !empty($assigned_courses)) ? $assigned_courses : array();


				$purchased_classes = get_user_meta($user_id, 'purchased_classes', true);
				$purchased_classes = (is_array($purchased_classes) && !empty($purchased_classes)) ? $purchased_classes : array();

				$assigned_classes = get_user_meta($user_id, 'assigned_classes', true);
				$assigned_classes = (is_array($assigned_classes) && !empty($assigned_classes)) ? $assigned_classes : array();

				$subtitle = get_post_meta($package_id, 'subtitle', true);

				$product = dtlms_get_product_object($package_id);				
				$woo_price = dtlms_get_item_price_html($product);	
				
				$purchased_package = false;
				if(dtlms_check_user_package_is_active($user_id, $package_id)) {
					$purchased_package = true;
				}							
				?>

				<article id="package-<?php echo esc_attr($package_id); ?>" <?php post_class('dtlms-package-detail'); ?>>

					<h2><?php echo esc_html($package_title); ?></h2>
					
					<?php
					if($subtitle != '') {
						echo '<h3>'.esc_html($subtitle).'</h3>';
					}
					?>

					<div class="dtlms-column dtlms-one-third first">
						<?php
						if(has_post_thumbnail($package_id)) {
							echo get_the_post_thumbnail($package_id, 'full');
						}
						echo dtlms_packages_listing_purchase_status($purchased_package);
						?>
					</div>

					<div class="dtlms-column dtlms-two-third">

						<div class="dtlms-package-description">
							<?php echo get_the_content(); ?>
						</div>

						<div class="dtlms-payment-details">
							<?php 
							echo dtlms_packages_listing_single_price($woo_price, $package_id);
							echo dtlms_packages_listing_single_addtocart($purchased_package, $package_id, $user_id, $product, $woo_price);
							?>
						</div>

					</div>

					<div class="dtlms-package-items">

						<h3><?php echo esc_html__('Items Included', 'dtlms'); ?></h3>
						
						<?php
						$courses_included = get_post_meta($package_id, 'courses-included', true);
						if(is_array($courses_included) && !empty($courses_included)) {
							echo '<h4>'.esc_html__('Courses', 'dtlms').'</h4>';
							echo '<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<th scope="col">'.esc_html__('#', 'dtlms').'</th>
										<th scope="col">'.esc_html__('Title', 'dtlms').'</th>
										<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
									</tr>';
							$i = 1;
							foreach($courses_included as $course_id) {
								$course_product = dtlms_get_product_object($course_id);				
								$course_woo_price = dtlms_get_item_price_html($course_product);	

								$user_status_label = '';
								if(in_array($course_id, $purchased_class_courses)) {

									$user_status_label .= '<span class="dtlms-purchased">
																<span class="fa fa-cart-arrow-down"></span> '.esc_html__('Purchased Class','dtlms').
															'</span>';

								} else if(in_array($course_id, $assigned_courses)) {

									$user_status_label .= '<span class="dtlms-assigned">
																<span class="fa fa-cart-arrow-down"></span> '.esc_html__('Assigned','dtlms').
															'</span>';

								} else if(in_array($course_id, $purchased_courses)) {

									$user_status_label .= '<span class="dtlms-purchased">
																<span class="fa fa-cart-arrow-down"></span> '.esc_html__('Purchased','dtlms').
															'</span>';

								}

								echo '<tr>
										<td>'.$i.'</td>
										<td><a href="'.get_permalink($course_id).'">'.get_the_title($course_id).'</a>'.$user_status_label.'</td>
										<td>'.$course_woo_price.'</td>
									</tr>';
								$i++;
							}
							echo '</table>';
						}
						?>

						<?php
						$classes_included = get_post_meta($package_id, 'classes-included', true);
						if(is_array($classes_included) && !empty($classes_included)) {

							$class_plural_label = apply_filters( 'class_label', 'plural' );
							echo '<h4>'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label ).'</h4>'; 

							echo '<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<th scope="col">'.esc_html__('#', 'dtlms').'</th>
										<th scope="col">'.esc_html__('Title', 'dtlms').'</th>
										<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
									</tr>';
							$i = 1;
							foreach($classes_included as $class_id) {
								$class_product = dtlms_get_product_object($class_id);				
								$class_woo_price = dtlms_get_item_price_html($class_product);	

								$user_status_label = '';
								if(in_array($class_id, $assigned_classes)) {

									$user_status_label .= '<span class="dtlms-assigned">
																<span class="fa fa-cart-arrow-down"></span> '.esc_html__('Assigned','dtlms').
															'</span>';

								} else if(in_array($class_id, $purchased_classes)) {

									$user_status_label .= '<span class="dtlms-purchased">
																<span class="fa fa-cart-arrow-down"></span> '.esc_html__('Purchased','dtlms').
															'</span>';

								}

								echo '<tr>
										<td>'.$i.'</td>
										<td><a href="'.get_permalink($class_id).'">'.get_the_title($class_id).'</a>'.$user_status_label.'</td>
										<td>'.$class_woo_price.'</td>
									</tr>';
								$i++;
							}
							echo '</table>';

						}
						?>				
					</div>

				</article>

				<?php
			endwhile; endif;
			?>

		<?php
		/**
		* dtlms_after_content hook.
		*/
		do_action( 'dtlms_after_content' );
		?>

	<?php
	/**
	* dtlms_after_main_content hook.
	*/
	do_action( 'dtlms_after_main_content' );
	?>

<?php get_footer('dtlms'); ?>