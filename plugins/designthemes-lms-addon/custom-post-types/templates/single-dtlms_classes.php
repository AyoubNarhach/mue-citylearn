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

						$class_id = get_the_ID(); 
						$class_title = get_the_title();
						$class_permalink = get_permalink();

						$current_user = wp_get_current_user();
						$user_id = $current_user->ID;

						$author_id = get_the_author_meta('ID');

						$class_type = get_post_meta($class_id, 'dtlms-class-type', true);

						$page_layout = get_post_meta($class_id, 'page-layout', true);
						$page_layout = ($page_layout != '') ? $page_layout : 'type1';


						$product = dtlms_get_product_object($class_id);
						$woo_price = dtlms_get_item_price_html($product);

						$free_class = false;
						if($woo_price == '') {
							$free_class = true;
						}	

						$active_package_classes = dtlms_get_user_active_packages($user_id, 'classes');
						$active_package_classes = (is_array($active_package_classes) && !empty($active_package_classes)) ? $active_package_classes : array();

						$assigned_classes = get_user_meta($user_id, 'assigned_classes', true);
						$assigned_classes = (is_array($assigned_classes) && !empty($assigned_classes)) ? $assigned_classes : array();

						$purchased_classes = get_user_meta($user_id, 'purchased_classes', true);
						$purchased_classes = (is_array($purchased_classes) && !empty($purchased_classes)) ? $purchased_classes : array();

						$purchased_paid_class = false;
						if(in_array($class_id, $active_package_classes) || in_array($class_id, $assigned_classes) || in_array($class_id, $purchased_classes)) {
							$purchased_paid_class = true;
						}

						$started_classes = get_user_meta($user_id, 'started_classes', true);
						$started_classes = (is_array($started_classes) && !empty($started_classes)) ? $started_classes : array();

						$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
						$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? $submitted_classes : array();

						$completed_classes = get_user_meta($user_id, 'completed_classes', true);
						$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? $completed_classes : array();

						$class_content_options = get_post_meta($class_id, 'dtlms-class-content-options', true);

						$class_courses = get_post_meta($class_id, 'dtlms-class-courses', true);
						$total_curriculum_count = 0;
						if(is_array($class_courses) && !empty($class_courses)) {
							$total_curriculum_count = count($class_courses);
						}


						$enable_certificate = get_post_meta($class_id, 'enable-certificate', true);
						$enable_badge = get_post_meta($class_id, 'enable-badge', true);
						$featured_item = get_post_meta($class_id, 'dtlms-class-featured', true);

						$additional_class = '';
						if($enable_certificate || $enable_badge || (isset($featured_item) && $featured_item == 'true')) {	
							$additional_class = 'with-dynamic-content';
						}					
						?>

						<article id="class-<?php echo esc_attr($class_id); ?>" <?php post_class(array ('dtlms-class-detail', $page_layout, $additional_class)); ?>>

							<?php
							$enable_sidebar = get_post_meta($class_id, 'enable-sidebar', true);
							$sidebar_content_string = '';
							if($enable_sidebar == 'true') {
								$sidebar_content = get_post_meta($class_id, 'sidebar-content', true);
								$sidebar_content_string = do_shortcode($sidebar_content);
							}

							if($page_layout == 'type4') {
								?>

							    <div class="dtlms-class-detail-header">
							        <?php echo dtlms_class_single_image($class_id); ?>
							        <div class="dtlms-class-detail-content-holder">
									    <div class="dtlms-class-detail-header-inner">
									    	<div class="dtlms-class-detail-header-inner-content">
											    <?php echo dtlms_class_listing_single_featured($class_id); ?>
											    <div class="dtlms-class-detail-purchaseprogress-content">
												    <?php echo dtlms_class_listing_single_purchase_status($purchased_paid_class, $class_id, $active_package_classes, $assigned_classes, $purchased_classes); ?>
												    <?php echo dtlms_class_listing_single_progress_details($purchased_paid_class, $free_class, $class_id, $started_classes, $submitted_classes, $completed_classes, 'single'); ?>
												</div>
											</div>
										    <?php echo dtlms_class_listing_single_certificatenbadge($class_id); ?>
									    </div>
							            <?php echo dtlms_class_single_title($class_id, $class_title); ?>
							            <?php echo dtlms_class_listing_single_class_type($class_id, $class_type); ?>
							            <div class="dtlms-class-detail-content left">                    
							                <div class="dtlms-class-detail-content-meta">
							                    <?php echo dtlms_class_single_author($class_id, $author_id, $page_layout); ?>
							                    <?php echo dtlms_class_single_courses_count($class_id, $page_layout); ?>
							                    <?php echo dtlms_class_single_review($class_id, $page_layout); ?>
							                    <?php echo dtlms_class_single_seats_available($class_id, $class_type, $page_layout); ?>	
							                </div>
							            </div>
							            <div class="dtlms-class-detail-content right"><?php echo dtlms_class_listing_single_price($purchased_paid_class, $free_class, $woo_price); ?><?php echo dtlms_class_listing_single_addtocart($purchased_paid_class, $class_id, $free_class, $user_id); ?></div>   
							        </div>
							    </div>

								<?php
							} else if($page_layout == 'type3') {								
								?>

								<div class="dtlms-class-detail-header">
									<?php echo dtlms_class_single_image($class_id); ?>
									<div class="dtlms-class-detail-header-holder">
								        <div class="dtlms-column dtlms-one-fourth no-space first">
								        	<div class="dtlms-class-detail-header-inner-detail">
							        			<div class="dtlms-class-detail-image-holder">
										        	<?php echo get_the_post_thumbnail($class_id, 'dtlms-420x330'); ?>
										        	<?php echo dtlms_class_single_seats_available($class_id, $class_type, $page_layout); ?>
										        	<?php echo dtlms_class_listing_single_class_type($class_id, $class_type); ?>
									        	</div>
									        </div>
									        <?php echo dtlms_generate_class_startnprogress($class_id, $user_id); ?>
									        <?php echo ($sidebar_content_string); ?>
								        </div>
								        <div class="dtlms-column dtlms-three-fourth no-space">
								            <div class="dtlms-class-detail-content-holder">
											    <div class="dtlms-class-detail-header-inner">
											    	<div class="dtlms-class-detail-header-inner-content">
													    <?php echo dtlms_class_listing_single_featured($class_id); ?>
													    <div class="dtlms-class-detail-purchaseprogress-content">
														    <?php echo dtlms_class_listing_single_purchase_status($purchased_paid_class, $class_id, $active_package_classes, $assigned_classes, $purchased_classes); ?>
														    <?php echo dtlms_class_listing_single_progress_details($purchased_paid_class, $free_class, $class_id, $started_classes, $submitted_classes, $completed_classes, 'single'); ?>
														</div>
													</div>
												    <?php echo dtlms_class_listing_single_certificatenbadge($class_id); ?>
											    </div>							            	
								                <div class="dtlms-class-detail-content left">
								                    <?php echo dtlms_class_single_title($class_id, $class_title); ?>
								                    <div class="dtlms-class-detail-content-meta">
								                        <?php echo dtlms_class_single_author($class_id, $author_id, 'type3'); ?>
								                        <?php echo dtlms_class_single_courses_count($class_id, $page_layout); ?>
								                        <?php echo dtlms_class_single_review($class_id, ''); ?>	
								                    </div>
								                </div>
								                <div class="dtlms-class-detail-content right"><?php echo dtlms_class_listing_single_price($purchased_paid_class, $free_class, $woo_price); ?><?php echo dtlms_class_listing_single_addtocart($purchased_paid_class, $class_id, $free_class, $user_id); ?></div>   
								            </div>
								            <?php dtlms_class_single_tab_content($class_id, $user_id, $author_id, $page_layout); ?>
								        </div>
							        </div>
								</div>

								<?php
							} else if($page_layout == 'type2') {
								?>

								<div class="dtlms-class-detail-header">
									<?php echo dtlms_class_single_image($class_id); ?>
									<div class="dtlms-class-detail-content left">
									    <?php echo dtlms_class_single_author($class_id, $author_id, 'type2'); ?>
									    <div class="dtlms-class-detail-content-inner">
										    <div class="dtlms-class-detail-header-inner">
										    	<div class="dtlms-class-detail-header-inner-content">
												    <?php echo dtlms_class_listing_single_featured($class_id); ?>
												    <div class="dtlms-class-detail-purchaseprogress-content">
													    <?php echo dtlms_class_listing_single_purchase_status($purchased_paid_class, $class_id, $active_package_classes, $assigned_classes, $purchased_classes); ?>
													    <?php echo dtlms_class_listing_single_progress_details($purchased_paid_class, $free_class, $class_id, $started_classes, $submitted_classes, $completed_classes, 'single'); ?>
													</div>
												</div>
											    <?php echo dtlms_class_listing_single_certificatenbadge($class_id); ?>
										    </div>										    	
									    	<?php echo dtlms_class_single_title($class_id, $class_title); ?>
									    	<?php echo dtlms_class_listing_single_class_type($class_id, $class_type); ?>
									    	<?php echo dtlms_class_single_review($class_id, ''); ?>
									    	<?php
											$started_users = get_post_meta($class_id, 'started_users', true);
											$student_enrolled = count($started_users);
											$seats_available = dtlms_calculate_class_available_seats($class_id);
									    	?>
							                <div class="dtlms-class-detail-content-meta">
							                	<?php
							                	if($class_content_options == 'course') {
							                		?>
								                    <div class="dtlms-class-detail-curriculum">
								                       <span></span>
								                       <span><?php echo sprintf(esc_html__('%1$s Courses', 'dtlms'), $total_curriculum_count); ?></span>
								                    </div>
								                    <?php
								                }
								                ?>
							                    <div class="dtlms-class-detail-students-enrolled">
							                       <span></span>
							                       <span><?php echo sprintf(esc_html__('%1$s Students', 'dtlms'), $student_enrolled); ?></span>
							                    </div>
							                    <?php
							                    if($class_type == 'onsite') {
							                    	?>
								                    <div class="dtlms-class-detail-seats-available">
								                       <span></span>
								                       <span><?php echo sprintf(esc_html__('%1$s Seats Available', 'dtlms'), $seats_available); ?></span>
								                    </div>
								                    <?php
								                }
								                ?>
							                </div>  
									    </div>
									</div>
									<div class="dtlms-class-detail-content right"><?php echo dtlms_class_listing_single_price($purchased_paid_class, $free_class, $woo_price); ?><?php echo dtlms_class_listing_single_addtocart($purchased_paid_class, $class_id, $free_class, $user_id); ?></div>
								</div>

								<?php
							} else {
								?>

								<div class="dtlms-class-detail-header">
								    <?php echo dtlms_class_single_image($class_id); ?>
								    <div class="dtlms-class-detail-header-inner">
								    	<div class="dtlms-class-detail-header-inner-content">
										    <?php echo dtlms_class_listing_single_featured($class_id); ?>
										    <div class="dtlms-class-detail-purchaseprogress-content">
											    <?php echo dtlms_class_listing_single_purchase_status($purchased_paid_class, $class_id, $active_package_classes, $assigned_classes, $purchased_classes); ?>
											    <?php echo dtlms_class_listing_single_progress_details($purchased_paid_class, $free_class, $class_id, $started_classes, $submitted_classes, $completed_classes, 'single'); ?>
											</div>
										</div>
									    <?php echo dtlms_class_listing_single_certificatenbadge($class_id); ?>
								    </div>									    
								    <div class="dtlms-class-detail-content">
								        <div class="dtlms-class-detail-content left">
								            <?php echo dtlms_class_single_title($class_id, $class_title); ?>
								            <?php echo dtlms_class_listing_single_class_type($class_id, $class_type); ?>
								            <div class="dtlms-class-detail-content-meta">
								                <?php echo dtlms_class_single_author($class_id, $author_id, 'type1'); ?>
								                <?php echo dtlms_class_single_courses_count($class_id, $page_layout); ?>
								                <?php echo dtlms_class_single_review($class_id, ''); ?>	
								                <?php echo dtlms_class_single_seats_available($class_id, $class_type, $page_layout); ?>		
								            </div>
								        </div>
								        <div class="dtlms-class-detail-content right"><?php echo dtlms_class_listing_single_addtocart($purchased_paid_class, $class_id, $free_class, $user_id); ?><?php echo dtlms_class_listing_single_price($purchased_paid_class, $free_class, $woo_price); ?></div>   
								    </div>
								</div>

								<?php
							}

							if($page_layout == 'type4') {
								echo '<div class="dtlms-column dtlms-three-fourth no-space first">';
									dtlms_class_single_tab_content($class_id, $user_id, $author_id, $page_layout);
								echo '</div>';
								echo '<div class="dtlms-column dtlms-one-fourth no-space">';
									echo dtlms_generate_class_startnprogress($class_id, $user_id);
									echo ($sidebar_content_string);						
								echo '</div>';
							} else if($page_layout == 'type3') {
								
							} else if($page_layout == 'type2') {
								echo '<div class="dtlms-column dtlms-three-fourth no-space first">';
									dtlms_class_single_tab_content($class_id, $user_id, $author_id, $page_layout);
								echo '</div>';
								echo '<div class="dtlms-column dtlms-one-fourth no-space">';
									echo dtlms_generate_class_startnprogress($class_id, $user_id);
									echo ($sidebar_content_string);
								echo '</div>';
							} else {
								echo '<div class="dtlms-column dtlms-three-fourth no-space first">';
									dtlms_class_single_tab_content($class_id, $user_id, $author_id, $page_layout);
								echo '</div>';
								echo '<div class="dtlms-column dtlms-one-fourth no-space">';
									echo dtlms_generate_class_startnprogress($class_id, $user_id);
									echo ($sidebar_content_string);
								echo '</div>';
							}

							?>

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