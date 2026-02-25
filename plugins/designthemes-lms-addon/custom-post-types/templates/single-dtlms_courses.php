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

				$course_id = get_the_ID(); 
				$course_title = get_the_title();
				$course_permalink = get_permalink();

				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;

				$author_id = get_the_author_meta('ID');


				$product = dtlms_get_product_object($course_id);				
				$woo_price = dtlms_get_item_price_html($product);

				$free_course = false;
				if($woo_price == '') {
					$free_course = true;
				}


				$active_package_courses = dtlms_get_user_active_packages($user_id, 'courses');
				$active_package_courses = (is_array($active_package_courses) && !empty($active_package_courses)) ? $active_package_courses : array();

				$purchased_class_courses = dtlms_get_user_purchased_class_courses($user_id);
				$purchased_class_courses = (is_array($purchased_class_courses) && !empty($purchased_class_courses)) ? $purchased_class_courses : array();

				$assigned_courses = get_user_meta($user_id, 'assigned_courses', true);
				$assigned_courses = (is_array($assigned_courses) && !empty($assigned_courses)) ? $assigned_courses : array();

				$purchased_courses = get_user_meta($user_id, 'purchased_courses', true);
				$purchased_courses = (is_array($purchased_courses) && !empty($purchased_courses)) ? $purchased_courses : array();

				$purchased_paid_course = false;
				if(in_array($course_id, $active_package_courses) || in_array($course_id, $purchased_class_courses) || in_array($course_id, $assigned_courses) || in_array($course_id, $purchased_courses)) {
					$purchased_paid_course = true;
				}


				$started_courses = get_user_meta($user_id, 'started_courses', true);
				$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array();

				$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
				$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? $submitted_courses : array();	

				$completed_courses = get_user_meta($user_id, 'completed_courses', true);
				$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? $completed_courses : array();

				$page_layout = get_post_meta($course_id, 'page-layout', true);
				$page_layout = ($page_layout != '') ? $page_layout : 'type1';
				

				$enable_certificate = get_post_meta($course_id, 'enable-certificate', true);
				$enable_badge = get_post_meta($course_id, 'enable-badge', true);
				$featured_course = get_post_meta($course_id, 'featured-course', true);

				$additional_class = '';
				if($enable_certificate || $enable_badge || (isset($featured_course) && $featured_course == 'true')) {
					$additional_class = 'with-dynamic-content';
				}	

				?>

				<article id="course-<?php echo esc_attr($course_id); ?>" <?php post_class(array ('dtlms-course-detail', $page_layout, $additional_class)); ?>>

					<?php

					$enable_sidebar = get_post_meta($course_id, 'enable-sidebar', true);
					$sidebar_content_string = '';
					if($enable_sidebar == 'true') {
						$sidebar_content = get_post_meta($course_id, 'sidebar-content', true);
						$sidebar_content_string = do_shortcode($sidebar_content);
					}

					if($page_layout == 'type5') {
						?>

						<div class="dtlms-course-detail-header">
						    <?php echo dtlms_course_single_image($course_id); ?>
						    <div class="dtlms-course-detail-header-inner">
						    	<div class="dtlms-course-detail-header-inner-content">
								    <?php echo dtlms_course_single_featured($course_id); ?>
								    <div class="dtlms-course-detail-purchaseprogress-content">
									    <?php echo dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses); ?>
									    <?php echo dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'single'); ?>
									</div>
							    </div>
							    <?php echo dtlms_course_single_certificatenbadge($course_id); ?>
						    </div>						    
						    <div class="dtlms-course-detail-content">
						        <div class="dtlms-course-detail-content left">
									<div class="dtlms-course-detail-titleprice-meta">
										<?php echo dtlms_course_single_title($course_id, $course_title); ?>
										<?php echo dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price); ?>
									</div>
						            <div class="dtlms-course-detail-content-meta">
						                <?php echo dtlms_course_single_author($course_id, $author_id, 'type1'); ?>
						                <?php echo dtlms_course_single_curriculum_count($course_id, $author_id); ?>
						                <?php echo dtlms_course_single_review($course_id, ''); ?>			
						            </div>
									<div class="dtlms-course-detail-shareforum-meta">
										<?php echo dtlms_generate_course_social_share($course_id, $page_layout); ?>
										<?php
										$course_forum_id = get_post_meta($course_id, 'dtlms-course-forum-id', true);
										if($course_forum_id > 0) {
											echo '<div class="dtlms-forum-button-holder dtlms-view-forum"><a href="'.get_permalink($course_forum_id).'" class="dtlms-button dtlms-button-forum-link small"><span class="fas fa-comments"></span>'.esc_html__('View Forum', 'dtlms').'</a></div>';
										}
										?>
									</div>						            
						        </div>
						        <div class="dtlms-course-detail-content right"><?php echo dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses); ?></div>   
						    </div>						
						</div>

						<?php					
					} else if($page_layout == 'type4') {
						$total_curriculum_count = dtlms_course_curriculum_counts($course_id, true);
						?>

					    <div class="dtlms-course-detail-header">
					        <?php echo dtlms_course_single_image($course_id); ?>
					        <div class="dtlms-course-detail-content-holder">
							    <div class="dtlms-course-detail-header-inner">
							    	<div class="dtlms-course-detail-header-inner-content">
									    <?php echo dtlms_course_single_featured($course_id); ?>
									    <div class="dtlms-course-detail-purchaseprogress-content">
										    <?php echo dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses); ?>
										    <?php echo dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'single'); ?>
										</div>
								    </div>
								    <?php echo dtlms_course_single_certificatenbadge($course_id); ?>
							    </div>					        	
					            <?php echo dtlms_course_single_title($course_id, $course_title); ?>
					            <div class="dtlms-course-detail-content left">                    
					                <div class="dtlms-course-detail-content-meta">
					                    <?php echo dtlms_course_single_author($course_id, $author_id, 'type4'); ?>
					                    <div class="dtlms-course-detail-curriculum">
					                        <span><?php echo esc_html__('Curriculum', 'dtlms'); ?></span>
					                        <?php echo sprintf(esc_html__('%1$s Items', 'dtlms'), $total_curriculum_count); ?>
					                    </div>
					                    <?php echo dtlms_course_single_review($course_id, 'type4'); ?>	
					                </div>
					            </div>
					            <div class="dtlms-course-detail-content right"><?php echo dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price); ?><?php echo dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses); ?>		<?php
									$course_forum_id = get_post_meta($course_id, 'dtlms-course-forum-id', true);
									if($course_forum_id > 0) {
										echo '<div class="dtlms-forum-button-holder"><a href="'.get_permalink($course_forum_id).'" class="dtlms-button dtlms-button-forum-link small">'.esc_html__('View Forum', 'dtlms').'</a></div>';
									}
						        	?></div>   
					        </div>
					    </div>

						<?php
					} else if($page_layout == 'type3') {
						$total_curriculum_count = dtlms_course_curriculum_counts($course_id, true);
						?>

						<div class="dtlms-course-detail-header">
							<?php echo dtlms_course_single_image($course_id); ?>
							<div class="dtlms-course-detail-header-holder">
						        <div class="dtlms-column dtlms-one-fourth no-space first">
						        	<div class="dtlms-course-detail-header-inner-detail">
						        		<div class="dtlms-course-detail-image-holder">
								        	<?php echo get_the_post_thumbnail($course_id, 'dtlms-420x330'); ?>
								        	<?php
											$course_forum_id = get_post_meta($course_id, 'dtlms-course-forum-id', true);
											if($course_forum_id > 0) {
												echo '<div class="dtlms-forum-button-holder"><a href="'.get_permalink($course_forum_id).'" class="dtlms-button dtlms-button-forum-link small">'.esc_html__('View Forum', 'dtlms').'</a></div>';
											}
								        	?>
							        	</div>							        	
							        	<?php echo dtlms_course_single_info($course_id, false, 'type3'); ?>
							        </div>
						        	<?php echo dtlms_generate_course_startnprogress($course_id, $user_id); ?>
						        	<?php echo dtlms_generate_course_social_share($course_id, $page_layout); ?>
						        	<?php echo ($sidebar_content_string); ?>							        
						        </div>
						        <div class="dtlms-column dtlms-three-fourth no-space">
						            <div class="dtlms-course-detail-content-holder">
									    <div class="dtlms-course-detail-header-inner">
									    	<div class="dtlms-course-detail-header-inner-content">
											    <?php echo dtlms_course_single_featured($course_id); ?>
											    <div class="dtlms-course-detail-purchaseprogress-content">
												    <?php echo dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses); ?>
												    <?php echo dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'single'); ?>
												</div>
										    </div>
										    <?php echo dtlms_course_single_certificatenbadge($course_id); ?>
									    </div>						            	
						                <div class="dtlms-course-detail-content left">
						                    <?php echo dtlms_course_single_title($course_id, $course_title); ?>
						                    <div class="dtlms-course-detail-content-meta">
						                        <?php echo dtlms_course_single_author($course_id, $author_id, 'type3'); ?>
						                        <div class="dtlms-course-detail-curriculum">
						                            <i class="fas fa-book"></i>
						                            <?php echo sprintf(esc_html__('%1$s Curriculum', 'dtlms'), $total_curriculum_count); ?>
						                        </div>
						                        <?php echo dtlms_course_single_review($course_id, ''); ?>	
						                    </div>
						                </div>
						                <div class="dtlms-course-detail-content right"><?php echo dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price); ?><?php echo dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses); ?></div>   
						            </div>
						            <?php dtlms_course_single_tab_content($course_id, $user_id, $author_id, $page_layout); ?>
						        </div>
					        </div>
						</div>

						<?php
					} else if($page_layout == 'type2') {
						?>

						<div class="dtlms-course-detail-header">
							<?php echo dtlms_course_single_image($course_id); ?>
							<div class="dtlms-course-detail-content left">
							    <?php echo dtlms_course_single_author($course_id, $author_id, 'type2'); ?>
							    <div class="dtlms-course-detail-content-inner">
								    <div class="dtlms-course-detail-header-inner">
								    	<div class="dtlms-course-detail-header-inner-content">
										    <?php echo dtlms_course_single_featured($course_id); ?>
										    <div class="dtlms-course-detail-purchaseprogress-content">
											    <?php echo dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses); ?>
											    <?php echo dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'single'); ?>
											</div>
									    </div>
									    <?php echo dtlms_course_single_certificatenbadge($course_id); ?>
								    </div>							    	
							    	<?php echo dtlms_course_single_title($course_id, $course_title); ?>
							    	<?php echo dtlms_course_single_review($course_id, ''); ?>
							    	<?php
							    	$duration = dtlms_get_course_duration($course_id, '', 'course');
							    	$total_curriculum_count = dtlms_course_curriculum_counts($course_id, true);
									$started_users = get_post_meta($course_id, 'started_users', true);
									$student_enrolled = count($started_users);
							    	?>
					                <div class="dtlms-course-detail-content-meta">
					                	<?php
					                	if($duration != '') {
					                		?>
						                    <div class="dtlms-course-detail-duration">
						                       <span></span>
						                       <span><?php echo esc_html( $duration ); ?></span>
						                    </div>
						                    <?php
						                }
					                	if($total_curriculum_count != '' && $total_curriculum_count > 0) {
					                		?>						                
						                    <div class="dtlms-course-detail-curriculum">
						                       <span></span>
						                       <span><?php echo sprintf(esc_html__('%1$s Curriculum', 'dtlms'), $total_curriculum_count); ?></span>
						                    </div>
						                    <?php
						                }
					                	if($student_enrolled != '' && $student_enrolled > 0) {
					                		?>							                    
						                    <div class="dtlms-course-detail-students-enrolled">
						                       <span></span>
						                       <span><?php echo sprintf(esc_html__('%1$s Students', 'dtlms'), $student_enrolled); ?></span>
						                    </div>
						                    <?php
						                }
						                if(get_the_term_list($course_id, 'course_category', '', ', ', '') != '') {
							                ?>
						                    <div class="dtlms-course-detail-category">
						                       <span></span>
						                       <span><?php echo get_the_term_list($course_id, 'course_category', '', ', ', ''); ?></span>
						                    </div>
						                    <?php
						                }
						                ?>
					                </div>  
							    </div>
							</div>
							<div class="dtlms-course-detail-content right"><?php echo dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price); ?><?php echo dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses); ?><?php
								$course_forum_id = get_post_meta($course_id, 'dtlms-course-forum-id', true);
								if($course_forum_id > 0) {
									echo '<div class="dtlms-forum-button-holder"><a href="'.get_permalink($course_forum_id).'" class="dtlms-button dtlms-button-forum-link small">'.esc_html__('View Forum', 'dtlms').'</a></div>';
								}
					        	?></div>
						</div>

						<?php
					} else {
						?>

						<div class="dtlms-course-detail-header">
						    <?php echo dtlms_course_single_image($course_id); ?>
						    <div class="dtlms-course-detail-header-inner">
						    	<div class="dtlms-course-detail-header-inner-content">
								    <?php echo dtlms_course_single_featured($course_id); ?>
								    <div class="dtlms-course-detail-purchaseprogress-content">
									    <?php echo dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses); ?>
									    <?php echo dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'single'); ?>
									</div>
							    </div>
							    <?php echo dtlms_course_single_certificatenbadge($course_id); ?>
						    </div>						    
						    <div class="dtlms-course-detail-content">
						        <div class="dtlms-course-detail-content left">
						            <?php echo dtlms_course_single_title($course_id, $course_title); ?>
						            <div class="dtlms-course-detail-content-meta">
						                <?php echo dtlms_course_single_author($course_id, $author_id, 'type1'); ?>
						                <?php echo dtlms_course_single_curriculum_count($course_id, $author_id); ?>
						                <?php echo dtlms_course_single_review($course_id, ''); ?>			
						            </div>
								    <?php echo dtlms_generate_course_social_share($course_id, $page_layout); ?>
						        	<?php
									$course_forum_id = get_post_meta($course_id, 'dtlms-course-forum-id', true);
									if($course_forum_id > 0) {
										echo '<div class="dtlms-forum-button-holder"><a href="'.get_permalink($course_forum_id).'" class="dtlms-button dtlms-button-forum-link small">'.esc_html__('View Forum', 'dtlms').'</a></div>';
									}
						        	?>							            
						        </div>
						        <div class="dtlms-course-detail-content right"><?php echo dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses); ?><?php echo dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price); ?></div>   
						    </div>						
						</div>

						<?php
					}

					if($page_layout == 'type5') {
						dtlms_course_single_tab_content($course_id, $user_id, $author_id, $page_layout);
					} else if($page_layout == 'type4') {
						echo '<div class="dtlms-column dtlms-three-fourth no-space first">';
							dtlms_course_single_tab_content($course_id, $user_id, $author_id, $page_layout);
						echo '</div>';
						echo '<div class="dtlms-column dtlms-one-fourth no-space">';
							echo dtlms_generate_course_startnprogress($course_id, $user_id);
							echo dtlms_course_single_info($course_id, true, 'type2');
							echo dtlms_generate_course_social_share($course_id, $page_layout);
							echo ($sidebar_content_string);						
						echo '</div>';
					} else if($page_layout == 'type3') {
						
					} else if($page_layout == 'type2') {
						echo '<div class="dtlms-column dtlms-three-fourth no-space first">';
							dtlms_course_single_tab_content($course_id, $user_id, $author_id, $page_layout);
						echo '</div>';
						echo '<div class="dtlms-column dtlms-one-fourth no-space">';
							echo dtlms_generate_course_startnprogress($course_id, $user_id);
							echo dtlms_course_single_info($course_id, true, 'type2');
							echo dtlms_generate_course_social_share($course_id, $page_layout);
							echo ($sidebar_content_string);
						echo '</div>';
					} else {
						echo '<div class="dtlms-column dtlms-three-fourth no-space first">';
							dtlms_course_single_tab_content($course_id, $user_id, $author_id, $page_layout);
						echo '</div>';
						echo '<div class="dtlms-column dtlms-one-fourth no-space">';
							echo dtlms_generate_course_startnprogress($course_id, $user_id);
							echo dtlms_course_single_info($course_id, true, 'type1');
							echo ($sidebar_content_string);
						echo '</div>';
					}
				
					?>

				</article>

				<?php

				$show_related_course = get_post_meta($course_id, 'show-related-course', true);
				if(isset($show_related_course) && $show_related_course == 'true'):
		
					$category_ids = array();
					$allcats  = wp_get_object_terms($course_id, 'course_category');
					foreach($allcats as $category) {
						$category_ids[] = $category->term_id;
					}
					
					$data_listing_attributes = array ();
					$data_listing_attributes['column'] = 3;
					$data_listing_attributes['column_class'] = 'dtlms-column dtlms-one-third';
					$data_listing_attributes['carousel_class'] = '';
					$data_listing_attributes['display_type'] = 'grid';
					$data_listing_attributes['apply_isotope'] = 'false';
					$data_listing_attributes['enable_category_isotope_filter'] = 'false';
					if($page_layout == 'type5') {
						$data_listing_attributes['type'] = 'type6';
						$data_listing_attributes['hide_labels'] = true;
						$data_listing_attributes['show_description'] = true;
						$data_listing_attributes['show_author_details'] = true;
					} else {
						$data_listing_attributes['type'] = 'type1';
						$data_listing_attributes['show_description'] = '';
						$data_listing_attributes['show_author_details'] = false;
					}
					$data_listing_attributes['hide_category'] = false;
					$data_listing_attributes['hide_addtocartbutton'] = false;				
					$data_listing_attributes['class'] = '';

					$args = array (
							'orderby' => 'rand', 
							'showposts' => '3', 
							'post__not_in' => array ($course_id), 
							'tax_query' => array ( 
											array ( 
												'taxonomy' => 'course_category', 
												'field' => 'id', 
												'operator' => 'IN',
												'terms' => $category_ids 
											)
										)
							);

					$related_courses_query = new WP_Query( $args );
							
					if ( $related_courses_query->have_posts() ) :

						if($page_layout == 'type5') {
							echo '<div class="dtlms-course-detail-related-courses-list-wrapper">';						
						}

						?>

						<div class="dtlms-course-detail-related-courses-list">

							<div class="dtlms-title"><?php echo esc_html__('Related Courses', 'dtlms'); ?>
								<?php
								if($page_layout == 'type5') {
									echo '<span>'.esc_html__('Courses you might be interested in', 'dtlms').'</span>';
								}
								?>
							</div> 

							<?php

							$i = 1;
							while ( $related_courses_query->have_posts() ) : 
								$related_courses_query->the_post();

								if($i == 1) { $first_class = 'first';  } else { $first_class = ''; }
								if($i == 3) { $i = 1; } else { $i = $i + 1; }

								$data_listing_attributes['first_class'] = $first_class;

								echo dtlms_course_data_listing($user_id, $data_listing_attributes);

							endwhile;
							wp_reset_postdata();

							?>

						</div> 

						<?php

						if($page_layout == 'type5') {
							echo '</div>';						
						}						

					endif;

				endif; 

				if($page_layout == 'type5') {
					echo ($sidebar_content_string);						
				}

				?>

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