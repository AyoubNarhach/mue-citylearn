<?php

// Set Commission
if(!function_exists('dtlms_get_set_commission_content')) {

	function dtlms_get_set_commission_content() {

		$output = '';

		$output .= '<div class="dtlms-settings-set-commission-container">';

			$output .= '<div class="dtlms-column dtlms-one-fifth first">';

				$instructor_label = apply_filters( 'instructor_label', 'singular' );
				$output .= '<label>'.sprintf( esc_html__('Choose %1$s', 'dtlms'), $instructor_label ).'</label>';

			$output .= '</div>';


			$output .= '<div class="dtlms-column dtlms-one-fifth">';

			    $output .= '<select class="dtlms-setcom-instructor" name="dtlms-setcom-instructor" style="width:50%;" data-placeholder="'.sprintf( esc_html__('Choose %1$s ...', 'dtlms'), $instructor_label ).'" class="dtlms-chosen-select">';

					$output .= '<option value="">'.esc_html__('None', 'dtlms').'</option>';

					$instructors = get_users ( array ('role' => 'instructor') );
			        if ( count( $instructors ) > 0 ) {
			            foreach ($instructors as $instructor) {
							$instructor_id = $instructor->data->ID;
			                $output .= '<option value="' . esc_attr( $instructor_id ) . '"' . selected( $instructor_id, '', false ) . '>' . esc_html( $instructor->data->display_name ) . '</option>';
			            }
			        }

			    $output .= '</select>';

			$output .= '</div>';

			$output .= dtlms_generate_loader_html(true);
			$output .= '<div class="dtlms-setcommission-container"></div>';

		$output .= '</div>';

		return $output;

	}

}

add_action( 'wp_ajax_dtlms_setcom_load_instructor_courses', 'dtlms_setcom_load_instructor_courses' );
add_action( 'wp_ajax_nopriv_dtlms_setcom_load_instructor_courses', 'dtlms_setcom_load_instructor_courses' );
function dtlms_setcom_load_instructor_courses($user_id = '') {

	$output = '';

	if($user_id != '') {
		$instructor_id = $user_id;
	} else {
		$instructor_id = $_REQUEST['instructor_id'];
	}

	if($instructor_id != '') {

		$commission_settings = get_option('dtlms-commission-settings');

		$output .= '<form name="formSetCommission" class="formSetCommission" method="post">';

			// Courses

			$args = array (
			    'author'        	=>  $instructor_id,
			    'orderby'       	=>  'post_date',
			    'order'         	=>  'ASC',
			    'post_type'         =>  'dtlms_courses',
			    'posts_per_page' 	=> -1
		    );

		    $instructor_posts = get_posts($args);

		    if(isset($instructor_posts) && !empty($instructor_posts)) {

		    	$output .= '<h3>'.esc_html__('Courses', 'dtlms').'</h3>';

				$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
								<thead>
									<tr>
										<th scope="col">'.esc_html__('#', 'dtlms').'</th>
										<th scope="col">'.esc_html__('Course', 'dtlms').'</th>
										<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
										<th scope="col">'.esc_html__('Commision Percentage ( % )', 'dtlms').'</th>
									</tr>
								</thead>
								<tbody>';

				$i = 1;

		    	foreach( $instructor_posts as $instructor_post ) {

		    		$course_id = $instructor_post->ID;

		    		$product = dtlms_get_product_object($course_id);				
					$woo_price = dtlms_get_item_price_html($product);	

					if($woo_price != '') {

						$commission_value = (isset($commission_settings[$instructor_id][$course_id]) && $commission_settings[$instructor_id][$course_id] > 0) ? $commission_settings[$instructor_id][$course_id] : '';

						$output .= '<tr>
										<td>'.$i.'</td>
										<td>'.$instructor_post->post_title.'</td>
										<td>'.$woo_price.'</td>
										<td><input type="number" name="course_commission['.$course_id.']" value="'.$commission_value.'" min="0" /></td>
									</tr>';
						
						$i++;

					}

		    	}

		    	$output .= '</tbody>
		    			</table>';

		    }


			// Classes

			if(dtlms_option('class','include-class-in-commission') == 'true') {
			
				$args = array (
				    'author'        	=>  $instructor_id,
				    'orderby'       	=>  'post_date',
				    'order'         	=>  'ASC',
				    'post_type'         =>  'dtlms_classes',
				    'posts_per_page' 	=> -1
			    );

			    $class_instructor_posts = get_posts($args);

			    if(isset($class_instructor_posts) && !empty($class_instructor_posts)) {

			    	$class_title_singular = apply_filters( 'class_label', 'singular' );
			    	$class_title_plural = apply_filters( 'class_label', 'plural' );

			    	$output .= '<h3>'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_title_plural ).'</h3>';		    	

					$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
									<thead>
										<tr>
											<th scope="col">'.esc_html__('#', 'dtlms').'</th>
											<th scope="col">'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_title_singular ).'</th>
											<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Commision Percentage ( % )', 'dtlms').'</th>
										</tr>
									</thead>
									<tbody>';

					$i = 1;

			    	foreach( $class_instructor_posts as $instructor_post ) {

			    		$course_id = $instructor_post->ID;

			    		$product = dtlms_get_product_object($course_id);				
						$woo_price = dtlms_get_item_price_html($product);	

						if($woo_price != '') {

							$commission_value = (isset($commission_settings[$instructor_id][$course_id]) && $commission_settings[$instructor_id][$course_id] > 0) ? $commission_settings[$instructor_id][$course_id] : '';

							$output .= '<tr>
											<td>'.$i.'</td>
											<td>'.$instructor_post->post_title.'</td>
											<td>'.$woo_price.'</td>
											<td><input type="number" name="course_commission['.$course_id.']" value="'.$commission_value.'" min="0" /></td>
										</tr>';
							
							$i++;

						}

			    	}

			    	$output .= '</tbody>
			    			</table>';

			    }

			}

			if((isset($instructor_posts) && !empty($instructor_posts)) || (dtlms_option('class','include-class-in-commission') == 'true' && isset($class_instructor_posts) && !empty($class_instructor_posts))) {

		    	$output .= '<div class="dtlms-commission-settings-response-holder"></div>';

		    	$output .= '<a href="#" class="dtlms-button dtlms-save-commission-settings small" data-instructorid="'.$instructor_id.'">'.esc_html__('Save Settings', 'dtlms').'</a>';

		    }

	    $output .= '</form>';		        

	} else {

		$output .= esc_html__('No records found!', 'dtlms');

	}

	echo $output;

	if($user_id == '') {
		die();
	}

}


add_action( 'wp_ajax_dtlms_save_commission_settings', 'dtlms_save_commission_settings' );
add_action( 'wp_ajax_nopriv_dtlms_save_commission_settings', 'dtlms_save_commission_settings' );
function dtlms_save_commission_settings() {

	$instructor_id = $_REQUEST['instructor_id'];
	$course_commission = $_REQUEST['course_commission'];

	$commission_settings = get_option('dtlms-commission-settings');

	foreach($course_commission as $course_commission_key => $course_commission_value) {
		if($course_commission_value != '' && $course_commission_value > 0) {
			$commission_settings[$instructor_id][$course_commission_key] = $course_commission_value;
		}
	}

	update_option('dtlms-commission-settings', $commission_settings);

	echo esc_html__('Commissions have been updated successfully!', 'dtlms');

	die();

}


// Pay Commission
if(!function_exists('dtlms_get_pay_commission_content')) {

	function dtlms_get_pay_commission_content() {

		$output = '';

		$output .= '<div class="dtlms-settings-pay-commission-container">';

			$output .= '<div class="dtlms-column dtlms-one-column no-space">';
			
				$output .= '<div class="dtlms-column dtlms-one-sixth no-space">';
					$instructor_label = apply_filters( 'instructor_label', 'singular' );
					$output .= '<label>'.sprintf( esc_html__('Choose %1$s', 'dtlms'), $instructor_label ).'</label>';
				$output .= '</div>';


				$output .= '<div class="dtlms-column dtlms-five-sixth no-space">';
					$output .= '<select class="dtlms-paycom-instructor" name="dtlms-paycom-instructor" style="width:50%;" data-placeholder="'.sprintf( esc_html__('Choose %1$s ...', 'dtlms'), $instructor_label ).'" class="dtlms-chosen-select">';
	
						$output .= '<option value="">'.esc_html__('None', 'dtlms').'</option>';
	
						$instructors = get_users ( array ('role' => 'instructor') );
						if ( count( $instructors ) > 0 ) {
							foreach ($instructors as $instructor) {
								$instructor_id = $instructor->data->ID;
								$output .= '<option value="' . esc_attr( $instructor_id ) . '"' . selected( $instructor_id, '', false ) . '>' . esc_html( $instructor->data->display_name ) . '</option>';
							}
						}
	
					$output .= '</select>';
				$output .= '</div>';
			$output .= '</div>';
			$output .= '<div class="dtlms-hr-invisible"></div>';

			$default_date = current_time(get_option('date_format'));
			
			$output .= '<div class="dtlms-column dtlms-one-column no-space">';

				$output .= '<div class="dtlms-column dtlms-one-sixth no-space">';
					$output .= '<label>'.esc_html__('From Date', 'dtlms').'</label>';
				$output .= '</div>';
	
				$output .= '<div class="dtlms-column dtlms-one-sixth no-space">';
					$output .= '<input type="text" class="dtlms-paycom-startdate dtlms-date-field" name="dtlms-paycom-startdate" value="'.$default_date.'" />';
				$output .= '</div>';
				
			$output .= '</div>';
			
			$output .= '<div class="dtlms-hr-invisible"></div>';
			
			$output .= '<div class="dtlms-column dtlms-one-column no-space">';

				$output .= '<div class="dtlms-column dtlms-one-sixth no-space">';
					$output .= '<label>'.esc_html__('To Date', 'dtlms').'</label>';
				$output .= '</div>';
	
				$output .= '<div class="dtlms-column dtlms-one-sixth no-space">';
					$output .= '<input type="text" class="dtlms-paycom-enddate dtlms-date-field" name="dtlms-paycom-enddate" value="'.$default_date.'" />';
				$output .= '</div>';
				
			$output .= '</div>';
			
			$output .= '<div class="dtlms-hr-invisible"></div>';

			$output .= '<div class="dtlms-column dtlms-one-column no-space">';
				$output .= '<a href="#" class="dtlms-button dtlms-load-paycom-datas small">'.esc_html__('Load', 'dtlms').'</a>';
			$output .= '</div>';


			$output .= '<div class="dtlms-hr-invisible"></div>';

			$output .= dtlms_generate_loader_html(true);

			$output .= '<div class="dtlms-paycommission-container">';
				
				$paycommission = isset($_REQUEST['paycommission']) ? $_REQUEST['paycommission'] : '';
				if($paycommission == 'success') {
					$output .= '<div class="dtlms-paycommission-response-holder">'.esc_html__('Commission payments saved', 'dtlms').'</div>';
				}


			$output .= '</div>';

		$output .= '</div>';

		return $output;

	}

}


add_action( 'wp_ajax_dtlms_load_paycom_datas', 'dtlms_load_paycom_datas' );
add_action( 'wp_ajax_nopriv_dtlms_load_paycom_datas', 'dtlms_load_paycom_datas' );
function dtlms_load_paycom_datas() {

	$instructor_id = $_REQUEST['instructor_id'];

	if($instructor_id != '') {

		$instructor_label = apply_filters( 'instructor_label', 'singular' );
		$instructor_info = get_userdata($instructor_id);

		$instructor_name = $instructor_info->display_name;
		$instructor_email = $instructor_info->user_email;

		$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
						<tbody>
							<tr>
								<td>'.$instructor_label.'</td>
								<td>'.$instructor_name.'</td>
							</tr>
							<tr>
								<td>'.esc_html__('Instructor PayPal Email', 'dtlms').'</td>
								<td><input type="text" name="instructor-paypal-email" class="instructor-paypal-email" value="'.$instructor_email.'" /></td>
							</tr>								
						</tbody>
					</table>';


		$startdate = date('d-m-Y', strtotime($_REQUEST['startdate']));
		$enddate = date('d-m-Y', strtotime($_REQUEST['enddate']));
		$startdate = strtotime($startdate);
		$enddate = strtotime($enddate);

		// Courses 

		$courses_subscribed = get_user_meta($instructor_id, 'courses-subscribed', true);

		$pay_commission_courses = array ();
		foreach($courses_subscribed as $courses_subscribed_key => $course_subscribed) {
			if (($courses_subscribed_key >= $startdate) && ($courses_subscribed_key <= $enddate)) {

				foreach($course_subscribed as $course_key => $course_datas) {
					if($course_datas['status'] == 'unpaid') {
						foreach($course_datas['users'] as $course_data_user) {
							$pay_commission_courses[$course_key][$courses_subscribed_key][] = $course_data_user;	
						}
					}
				}

		 	}
		}

		// Classes 

		$pay_commission_classes = array ();
		if(dtlms_option('class','include-class-in-commission') == 'true') {

			$classes_subscribed = get_user_meta($instructor_id, 'classes-subscribed', true);

			foreach($classes_subscribed as $class_subscribed_key => $class_subscribed) {
				if (($class_subscribed_key >= $startdate) && ($class_subscribed_key <= $enddate)) {

					foreach($class_subscribed as $class_key => $class_datas) {
						if($class_datas['status'] == 'unpaid') {
							foreach($class_datas['users'] as $class_data_user) {
								$pay_commission_classes[$class_key][$class_subscribed_key][] = $class_data_user;	
							}
						}
					}

			 	}
			}

		}

		$commission_settings = get_option('dtlms-commission-settings');

		if(!empty($pay_commission_courses)) {
			
			// Courses 

			$output .= '<h3>'.esc_html__('Courses', 'dtlms').'</h3>';

			$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
							<thead>
								<tr>
									<th scope="col">'.esc_html__('#', 'dtlms').'</th>
									<th scope="col">'.esc_html__('Course', 'dtlms').'</th>
									<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
									<th scope="col">'.esc_html__('Subscriptions', 'dtlms').'</th>
									<th scope="col">'.esc_html__('Commision Percentage ( % )', 'dtlms').'</th>
									<th scope="col">'.sprintf(esc_html__('Amount To Pay (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</th>
									<th scope="col">'.esc_html__('Select', 'dtlms').'</th>
								</tr>
							</thead>
							<tbody>';

							if(is_array($pay_commission_courses) && !empty($pay_commission_courses)) {
								$i = 1;
								foreach($pay_commission_courses as $pay_commission_course_key => $pay_commission_course) {

									$course_id = $pay_commission_course_key;

									$commission_percentage = (isset($commission_settings[$instructor_id][$course_id]) && $commission_settings[$instructor_id][$course_id] > 0) ? $commission_settings[$instructor_id][$course_id] : 0;

									if($commission_percentage > 0) {

										$subscription_detail_html = '';
										$total_subscriptions = 0;
										foreach($pay_commission_course as $subscription_detail_key => $subscription_detail) {
											$subscription_detail_html .= '<div class="dtlms-subscriber-details"><label>'.date(get_option('date_format'), $subscription_detail_key).'</label>';
											$subscription_detail_html .= '<ul>';
												foreach($subscription_detail as $user_id) {
													$subscription_detail_html .= '<li>'.get_the_author_meta('display_name', $user_id).'</li>';
													$total_subscriptions++;
												}
											$subscription_detail_html .= '</ul></div>';
										}

										$product = dtlms_get_product_object($course_id);
										$woo_price_html = dtlms_get_item_price_html($product);

										$amount_to_pay = 0;
										if($commission_percentage > 0 && $total_subscriptions > 0) {
											if($product->get_sale_price() != '') {
												$woo_price = $product->get_sale_price();
											} else {
												$woo_price = $product->get_regular_price();
											}
								    		
											$amount_to_pay = (($total_subscriptions*$woo_price)*$commission_percentage)/100;
										}

										$output .= '<tr>
														<td>'.$i.'</td>
														<td>'.get_the_title($course_id).'</td>
														<td>'.$woo_price_html.'</td>
														<td><p class="dtlms-statistics-count"><span>'.$total_subscriptions.'</span></p>';

														$output .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
														$output .= '<div class="dtlms-subscription-detail-holder">'.$subscription_detail_html.'</div></div>';

											$output .= '</td>
														<td>'.$commission_percentage.'</td>
														<td>'.$amount_to_pay.'</td>
														<td>';

												            $output .= '<div data-for="items-topay-commission" class="dtlms-checkbox-switch checkbox-switch-off items-topay-commission course-item" data-courseid="'.$course_id.'" data-amounttopay="'.$amount_to_pay.'" data-commissiondetails="'.$course_id.'-'.$total_subscriptions.'-'.$commission_percentage.'"></div>';
												            $output .= '<input id="items-topay-commission" class="hidden" type="checkbox" name="items-topay-commission" value="true" />';

											$output .= '</td>'; 
										$output .= '</tr>';

										$i++;

									}

								}
							} else {
								$output .= '<tr><td colspan="7">'.esc_html__('No records found!', 'dtlms').'</td></tr>';								
							}

			$output .= '</tbody></table>';

		}

		if(dtlms_option('class','include-class-in-commission') == 'true') {

			if(!empty($pay_commission_classes)) {

				$class_singular_label = apply_filters( 'class_label', 'singular' );
				$class_plural_label = apply_filters( 'class_label', 'plural' );

				// Classes 
				
				$output .= '<h3>'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label ).'</h3>';

				$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
								<thead>
									<tr>
										<th scope="col">'.esc_html__('#', 'dtlms').'</th>
										<th scope="col">'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ).'</th>
										<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
										<th scope="col">'.esc_html__('Subscriptions', 'dtlms').'</th>
										<th scope="col">'.esc_html__('Commision Percentage ( % )', 'dtlms').'</th>
										<th scope="col">'.sprintf(esc_html__('Amount To Pay (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</th>
										<th scope="col">'.esc_html__('Select', 'dtlms').'</th>
									</tr>
								</thead>
								<tbody>';

								if(is_array($pay_commission_classes) && !empty($pay_commission_classes)) {
									$i = 1;
									foreach($pay_commission_classes as $pay_commission_class_key => $pay_commission_class) {

										$class_id = $pay_commission_class_key;

										$commission_percentage = (isset($commission_settings[$instructor_id][$class_id]) && $commission_settings[$instructor_id][$class_id] > 0) ? $commission_settings[$instructor_id][$class_id] : 0;

										if($commission_percentage > 0) {

											$subscription_detail_html = '';
											$total_subscriptions = 0;
											foreach($pay_commission_class as $subscription_detail_key => $subscription_detail) {
												$subscription_detail_html .= '<div class="dtlms-subscriber-details"><label>'.date(get_option('date_format'), $subscription_detail_key).'</label>';
												$subscription_detail_html .= '<ul>';
													foreach($subscription_detail as $user_id) {
														$subscription_detail_html .= '<li>'.get_the_author_meta('display_name', $user_id).'</li>';
														$total_subscriptions++;
													}
												$subscription_detail_html .= '</ul></div>';
											}

											$product = dtlms_get_product_object($class_id);
											$woo_price_html = dtlms_get_item_price_html($product);

											$amount_to_pay = 0;
											if($commission_percentage > 0 && $total_subscriptions > 0) {
												if($product->get_sale_price() != '') {
													$woo_price = $product->get_sale_price();
												} else {
													$woo_price = $product->get_regular_price();
												}
									    		
												$amount_to_pay = (($total_subscriptions*$woo_price)*$commission_percentage)/100;
											}

											$output .= '<tr>
															<td>'.$i.'</td>
															<td>'.get_the_title($class_id).'</td>
															<td>'.$woo_price_html.'</td>
															<td>'.$total_subscriptions;
															
															$output .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
															$output .= '<div class="dtlms-subscription-detail-holder">'.$subscription_detail_html.'</div></div>';														
															

												$output .= '</td>
															<td>'.$commission_percentage.'</td>
															<td>'.$amount_to_pay.'</td>
															<td>';

													            $output .= '<div data-for="items-topay-commission" class="dtlms-checkbox-switch checkbox-switch-off items-topay-commission class-item" data-classid="'.$class_id.'" data-amounttopay="'.$amount_to_pay.'" data-commissiondetails="'.$class_id.'-'.$total_subscriptions.'-'.$commission_percentage.'"></div>';
													            $output .= '<input id="items-topay-commission" class="hidden" type="checkbox" name="items-topay-commission" value="true" />';

												$output .= '</td>'; 
											$output .= '</tr>';

											$i++;

										}

									}
								} else {
									$output .= '<tr><td colspan="7">'.esc_html__('No records found!', 'dtlms').'</td></tr>';								
								}
								
				$output .= '</tbody></table>';

			}

		}

		if(!empty($pay_commission_courses) || !empty($pay_commission_classes)) {

			$output .= '<div class="dtlms-paycommission-otheramount-button">';

				$output .= esc_html__('Other Amounts', 'dtlms').' '.get_woocommerce_currency_symbol().' <input type="number" name="other-amounts" class="other-amounts" data-totalcommissions="0" value="0" min="0" />';

			$output .= '</div>';

			$output .= '<div class="dtlms-paycommission-totalamount-button">';

				$output .= esc_html__('Total Amount', 'dtlms').' '.get_woocommerce_currency_symbol().' <input type="text" name="total-commission-topay" class="total-commission-topay" value="0" readonly />';

				$output .= '<input type="hidden" name="course-commission-topay" class="course-commission-topay" value="0" readonly />';
				$output .= '<input type="hidden" name="class-commission-topay" class="class-commission-topay" value="0" readonly />';

				$output .= '<input type="button" name="dtlms-pay-commission-via-paypal" class="dtlms-pay-commission-via-paypal" value="'.esc_html__('Pay Via PayPal' ,'dtlms').'" />';

				$output .= '<a href="#" class="dtlms-button dtlms-commission-markaspaid small" data-selectedcourses="" data-selectedclasses="" data-overallcommissiondetails="" data-instructorid="'.$instructor_id.'" data-startdate="'.$startdate.'" data-enddate="'.$enddate.'">'.esc_html__('Mark As Paid', 'dtlms').'</a>';

				$output .= '<form name="_xclick" action="https://www.paypal.com/in/cgi-bin/webscr" id="dtlmsPaypalForm" method="post" target="_blank">
								<input type="hidden" name="cmd" value="_xclick">
								<input type="hidden" name="business" class="emailid" value="'.$instructor_email.'">
								<input type="hidden" name="currency_code" value="'.get_woocommerce_currency().'">
								<input type="hidden" name="item_name" value="Instructor Commission">
								<input type="hidden" name="amount" class="amount" value="">
							</form>';

			$output .= '</div>';

		} else {

			$output .= '<div class="dtlms-paycommission-response-holder">';
				$output .= esc_html__('No records found!', 'dtlms');
			$output .= '</div>';

		}	

	} else {

		$output .= '<div class="dtlms-paycommission-response-holder">';
			$output .= esc_html__('Please make sure you have selected "Instructor"!', 'dtlms');
		$output .= '</div>';

	}

	echo $output;

	die();

}

add_action( 'wp_ajax_dtlms_paycommission_markaspaid', 'dtlms_paycommission_markaspaid' );
add_action( 'wp_ajax_nopriv_dtlms_paycommission_markaspaid', 'dtlms_paycommission_markaspaid' );
function dtlms_paycommission_markaspaid() {

	$instructor_id = $_REQUEST['instructor_id'];
	$startdate = $_REQUEST['start_date'];
	$enddate = $_REQUEST['end_date'];
	$selected_courses = ltrim($_REQUEST['selected_courses'], ',');
	$selected_courses = array_filter(explode(',', $selected_courses));
	$selected_classes = ltrim($_REQUEST['selected_classes'], ',');
	$selected_classes = array_filter(explode(',', $selected_classes));

	$instructor_paypal_email = $_REQUEST['instructor_paypal_email'];
	$course_commission_paid = isset($_REQUEST['course_commission_paid']) ? $_REQUEST['course_commission_paid'] : 0;
	$class_commission_paid = isset($_REQUEST['class_commission_paid']) ? $_REQUEST['class_commission_paid'] : 0;
	$total_commission_paid = isset($_REQUEST['total_commission_paid']) ? $_REQUEST['total_commission_paid'] : 0;
	$overall_commission_details = ltrim($_REQUEST['overall_commission_details'], ',');
	$overall_commission_details = array_filter(explode(',', $overall_commission_details));
	$other_amounts = $_REQUEST['other_amounts'];


	$output = '';

	if($instructor_id != '') {

		$current_timestamp = strtotime(current_time(get_option('date_format')));

		$commission_settings = get_option('dtlms-commission-settings');

		// Courses
		$courses_subscribed = get_user_meta($instructor_id, 'courses-subscribed', true);

		$commission_paid_courses = array();
		foreach($courses_subscribed as $courses_subscribed_key => $course_subscribed) {
			if (($courses_subscribed_key >= $startdate) && ($courses_subscribed_key <= $enddate)) {
				foreach($course_subscribed as $course_id => $course_datas) {
					if(in_array($course_id, $selected_courses)) {

						if($courses_subscribed[$courses_subscribed_key][$course_id]['status'] == 'unpaid') {

							$courses_subscribed[$courses_subscribed_key][$course_id]['status'] = 'paid';
							foreach($course_datas['users'] as $user) {
								$commission_paid_courses[$course_id][$courses_subscribed_key]['users'][] = $user;	
							}				

							foreach($overall_commission_details as $overall_commission_detail) {
								$overall_commission_detail_course_extracted = explode('-', $overall_commission_detail);	
								if (in_array($course_id, $overall_commission_detail_course_extracted)) {
									$course_item_commission = $overall_commission_detail_course_extracted[2];	
								}
							}

							if($course_item_commission != '') {
								$courses_subscribed[$courses_subscribed_key][$course_id]['commission'] = $course_item_commission;
							}

							$commission_paid_courses[$course_id][$courses_subscribed_key]['commission'] = $course_item_commission;

							$courses_subscribed[$courses_subscribed_key][$course_id]['commission-paid-date'] = $current_timestamp;

						}

					}
				}
			}
		}
		update_user_meta($instructor_id, 'courses-subscribed', $courses_subscribed);


		// Classes
		if(dtlms_option('class','include-class-in-commission') == 'true') {

			$classes_subscribed = get_user_meta($instructor_id, 'classes-subscribed', true);

			$commission_paid_classes = array();
			foreach($classes_subscribed as $class_subscribed_key => $class_subscribed) {
				if (($class_subscribed_key >= $startdate) && ($class_subscribed_key <= $enddate)) {
					foreach($class_subscribed as $class_id => $class_datas) {
						if(in_array($class_id, $selected_classes)) {

							if($classes_subscribed[$class_subscribed_key][$class_id]['status'] == 'unpaid') {

								$classes_subscribed[$class_subscribed_key][$class_id]['status'] = 'paid';
								foreach($class_datas['users'] as $user) {
									$commission_paid_classes[$class_id][$class_subscribed_key]['users'][] = $user;	
								}

								foreach($overall_commission_details as $overall_commission_detail) {
									$overall_commission_detail_class_extracted = explode('-', $overall_commission_detail);	
									if (in_array($class_id, $overall_commission_detail_class_extracted)) {
										$class_item_commission = $overall_commission_detail_class_extracted[2];	
									}
								}

								if($class_item_commission != '') {
									$classes_subscribed[$class_subscribed_key][$class_id]['commission'] = $class_item_commission;
								}

								$commission_paid_classes[$class_id][$class_subscribed_key]['commission'] = $class_item_commission;

								$classes_subscribed[$class_subscribed_key][$class_id]['commission-paid-date'] = $current_timestamp;

							}

						}
					}
				}
			}
			update_user_meta($instructor_id, 'classes-subscribed', $classes_subscribed);

		}

		// Commissions Received

		$commissions_received = get_user_meta($instructor_id, 'commissions-received', true);
		$commissions_received = (is_array($commissions_received) && !empty($commissions_received)) ? $commissions_received : array ();	
		$commissions_received_timestamp_keys = array_keys($commissions_received);
		if(in_array($current_timestamp, $commissions_received_timestamp_keys)) {
			$commissions_received[$current_timestamp]['total-commission'] = $commissions_received[$current_timestamp]['total-commission'] + $total_commission_paid;
			$commissions_received[$current_timestamp]['course-commission'] = $commissions_received[$current_timestamp]['course-commission'] + $course_commission_paid;
			$commissions_received[$current_timestamp]['class-commission'] = $commissions_received[$current_timestamp]['class-commission'] + $class_commission_paid;
			$commissions_received[$current_timestamp]['other-amounts'] = $commissions_received[$current_timestamp]['other-amounts'] + $other_amounts;
		} else {
			$commissions_received[$current_timestamp]['total-commission'] = $total_commission_paid;
			$commissions_received[$current_timestamp]['course-commission'] = $course_commission_paid;
			$commissions_received[$current_timestamp]['class-commission'] = $class_commission_paid;
			$commissions_received[$current_timestamp]['other-amounts'] = $other_amounts;
		}

		foreach($overall_commission_details as $overall_commission_detail) {	

			$overall_commission_detail_extracted = explode('-', $overall_commission_detail);

			if(get_post_type($overall_commission_detail_extracted[0]) == 'dtlms_classes') {
				if(isset($commissions_received[$current_timestamp]['classes'][$overall_commission_detail_extracted[0]])) {
					$overall_commission_detail_keys = array_keys($commissions_received[$current_timestamp]['classes'][$overall_commission_detail_extracted[0]]);
					if(in_array($overall_commission_detail_extracted[2], $overall_commission_detail_keys)) {
						$commissions_received[$current_timestamp]['classes'][$overall_commission_detail_extracted[0]][$overall_commission_detail_extracted[2]] = $commissions_received[$current_timestamp]['classes'][$overall_commission_detail_extracted[0]][$overall_commission_detail_extracted[2]]+$overall_commission_detail_extracted[1];	
					} else {
						$commissions_received[$current_timestamp]['classes'][$overall_commission_detail_extracted[0]][$overall_commission_detail_extracted[2]] = $overall_commission_detail_extracted[1];			
					}
				} else {
					$commissions_received[$current_timestamp]['classes'][$overall_commission_detail_extracted[0]][$overall_commission_detail_extracted[2]] = $overall_commission_detail_extracted[1];
				}
			} else {
				if(isset($commissions_received[$current_timestamp]['courses'][$overall_commission_detail_extracted[0]])) {
					$overall_commission_detail_keys = array_keys($commissions_received[$current_timestamp]['courses'][$overall_commission_detail_extracted[0]]);
					if(in_array($overall_commission_detail_extracted[2], $overall_commission_detail_keys)) {
						$commissions_received[$current_timestamp]['courses'][$overall_commission_detail_extracted[0]][$overall_commission_detail_extracted[2]] = $commissions_received[$current_timestamp]['courses'][$overall_commission_detail_extracted[0]][$overall_commission_detail_extracted[2]]+$overall_commission_detail_extracted[1];	
					} else {
						$commissions_received[$current_timestamp]['courses'][$overall_commission_detail_extracted[0]][$overall_commission_detail_extracted[2]] = $overall_commission_detail_extracted[1];			
					}
				} else {
					$commissions_received[$current_timestamp]['courses'][$overall_commission_detail_extracted[0]][$overall_commission_detail_extracted[2]] = $overall_commission_detail_extracted[1];
				}
			}

		}

		update_user_meta($instructor_id, 'commissions-received', $commissions_received);

		
		$title = sprintf(esc_html__('Commission payments on %s.', 'dtlms'), current_time(get_option('date_format')));
		$payment_post = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_type' => 'dtlms_payments',
		);
		
		$payment_post_id = wp_insert_post( $payment_post );
		
		update_post_meta ( $payment_post_id, 'instructor-id',  $instructor_id );
		update_post_meta ( $payment_post_id, 'startdate',  $startdate );		
		update_post_meta ( $payment_post_id, 'enddate',  $enddate );
		update_post_meta ( $payment_post_id, 'instructor-paypal-email',  $instructor_paypal_email );
		update_post_meta ( $payment_post_id, 'payment-datas',  $commission_paid_courses );
		update_post_meta ( $payment_post_id, 'commission-paid-courses',  $commission_paid_courses );
		update_post_meta ( $payment_post_id, 'commission-paid-classes',  $commission_paid_classes );
		update_post_meta ( $payment_post_id, 'total-commission-paid',  $total_commission_paid );
		update_post_meta ( $payment_post_id, 'other-amounts',  $other_amounts );

		$output .= 'success';

	} else {

		$output .= esc_html__('Something went wrong!', 'dtlms');

	}

	echo $output;

	die();

}

?>