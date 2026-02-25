<?php

function dtlms_validate_timestamp($timestamps, $condition) {

	$current_month = date('m');
	$current_year = date('Y');
	$filtered_timestamps = array ();
	foreach ($timestamps as $timestamp) {
		if($condition == 'daily') {
			if(date('m', $timestamp) == $current_month && date('Y', $timestamp) == $current_year) {
				array_push($filtered_timestamps, $timestamp);
			}			
		}
		if($condition == 'monthly') {
			if(date('Y', $timestamp) == $current_year) {
				array_push($filtered_timestamps, $timestamp);
			}			
		}
	}

	return $filtered_timestamps;

}

function dtlms_generate_days_array() {

	$days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

	$days = array ();
	for($i = 01; $i <= $days_in_month; $i++) {
		$k = str_pad($i, 2, '0', STR_PAD_LEFT);
		$days[$k] = 0;
	}

	return $days;

}

function dtlms_generate_months_label() {

	$months = array ( 
				'January',
				'February',
				'March',
				'April',
				'May',
				'June',
				'July',
				'August',
				'September',
				'October',
				'November',
				'December'
			);

	return $months;

}

function dtlms_generate_months_array() {

	$months = array ( 
				'01' => 0,
				'02' => 0,
				'03' => 0,
				'04' => 0,
				'05' => 0,
				'06' => 0,
				'07' => 0,
				'08' => 0,
				'09' => 0,
				'10' => 0,
				'11' => 0,
				'12' => 0
			);

	return $months;

}

function dtlms_generate_years_label($timestamps) {

	$filtered_timestamps = array ();
	foreach ($timestamps as $timestamp) {
		$filtered_timestamps[date('Y', $timestamp)] = 0;
	}

	return $filtered_timestamps;

}

function dtlms_generate_random_number() {

	$random_number = array(rand(), rand(), rand(), rand());
	shuffle($random_number);
	$random_number = implode('', $random_number);

	return $random_number;
}


function dtlms_customize_datas_for_chart($overviewchartoption, $purchased_users_timestamp_final) {

	$chart_label_string = $chart_datas_string = $chart_string = '';

	$purchased_users_timestamp_final_keys = array_keys($purchased_users_timestamp_final);

	if($overviewchartoption == 'daily') {

		$purchased_users_timestamp_final_keys_filtered = dtlms_validate_timestamp($purchased_users_timestamp_final_keys, 'daily');

		$chart_datas = dtlms_generate_days_array();
		$chart_data_keys = array_keys($chart_datas);
		if(!empty($chart_data_keys)) {
			$chart_label_string = '['.implode(',', $chart_data_keys).']';
		}

		foreach ($purchased_users_timestamp_final_keys_filtered as $timestamp) {
			$chart_datas[date('d', $timestamp)] = $purchased_users_timestamp_final[$timestamp];
		}

		if(!empty($chart_datas)) {
			$chart_datas_string = '['.implode(',', $chart_datas).']';
		}

	}

	if($overviewchartoption == 'monthly') {

		$purchased_users_timestamp_final_keys_filtered = dtlms_validate_timestamp($purchased_users_timestamp_final_keys, 'monthly');

		$chart_label = dtlms_generate_months_label();
		if(!empty($chart_label)) {
			$chart_label_string = '["'.implode('","', $chart_label).'"]';
		}

		$chart_datas = dtlms_generate_months_array();
		foreach ($purchased_users_timestamp_final_keys_filtered as $timestamp) {
			$chart_datas[date('m', $timestamp)] = $chart_datas[date('m', $timestamp)] + $purchased_users_timestamp_final[$timestamp];
		}

		if(!empty($chart_datas)) {
			$chart_datas_string = '['.implode(',', $chart_datas).']';
		}	

	}

	if($overviewchartoption == 'alltime') {

		$purchased_users_timestamp_final_keys_filtered = $purchased_users_timestamp_final_keys;

		$chart_label = dtlms_generate_years_label($purchased_users_timestamp_final_keys);
		$chart_label_keys = array_keys($chart_label);
		if(!empty($chart_label_keys)) {
			$chart_label_string = '['.implode(',', $chart_label_keys).']';
		}

		$chart_datas = $chart_label;
		foreach ($purchased_users_timestamp_final_keys_filtered as $timestamp) {
			$chart_datas[date('Y', $timestamp)] = $chart_datas[date('Y', $timestamp)] + $purchased_users_timestamp_final[$timestamp];
		}

		if(!empty($chart_datas)) {
			$chart_datas_string = '['.implode(',', $chart_datas).']';
		}

	}	

	if($chart_label_string != '' && $chart_datas_string != '') {
		$chart_string = $chart_label_string.'||'.$chart_datas_string;
	}

	return $chart_string;

}

function dtlms_get_class_purchase_details($overviewchartoption, $instructor_id = -1) {

	$args = array ( 
				'posts_per_page' => -1, 
				'post_type'=> 'dtlms_classes'
			);
	if($instructor_id > 0) {
		$args['author'] = $instructor_id; 	
	}

	$classes = get_posts( $args );

	$purchased_users_timestamp_final = array ();
	if(is_array($classes) && !empty($classes)) {
		foreach ( $classes as $class ) {
			setup_postdata( $class );

			$class_id = $class->ID;

			$purchased_users_timestamp = get_post_meta($class_id, 'purchased_users_timestamp', true);
			$purchased_users = get_post_meta($class_id, 'purchased_users', true);

			if(is_array($purchased_users_timestamp) && !empty($purchased_users_timestamp)) {	
				foreach($purchased_users_timestamp as $purchased_users_timestamp_key => $purchased_users_timestamp_data) {
					if(array_key_exists($purchased_users_timestamp_key, $purchased_users_timestamp_final)) {
						$purchased_users_timestamp_final[$purchased_users_timestamp_key] = count($purchased_users_timestamp_data)+$purchased_users_timestamp_final[$purchased_users_timestamp_key];
					} else {
						$purchased_users_timestamp_final[$purchased_users_timestamp_key] = count($purchased_users_timestamp_data);
					}

					if(array_key_exists($class_id, $purchased_users_data_final[$purchased_users_timestamp_key])) {
						$purchased_users_data_final[$purchased_users_timestamp_key][$class_id] = count($purchased_users_timestamp_data)+$purchased_users_data_final[$purchased_users_timestamp_key][$class_id];
					} else {
						$purchased_users_data_final[$purchased_users_timestamp_key][$class_id] = count($purchased_users_timestamp_data);
					}					
				}
			}

		}
	}

	wp_reset_postdata();

	$class_plural_label = apply_filters( 'class_label', 'plural' );

	$output = dtlms_customize_datas_for_chart($overviewchartoption, $purchased_users_timestamp_final);
	$output .= '##'.dtlms_generate_purchase_overview_item_datas($overviewchartoption, sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label ), $purchased_users_data_final);

	return $output;

}

function dtlms_get_course_purchase_details($overviewchartoption, $instructor_id = -1) {

	$args = array ( 
				'posts_per_page' => -1, 
				'post_type'=> 'dtlms_courses'
			);
	if($instructor_id > 0) {
		$args['author'] = $instructor_id; 	
	}

	$courses = get_posts( $args );

	$purchased_users_timestamp_final = array ();
	if(is_array($courses) && !empty($courses)) {
		foreach ( $courses as $course ) {
			setup_postdata( $course );

			$course_id = $course->ID;

			$purchased_users_timestamp = get_post_meta($course_id, 'purchased_users_timestamp', true);
			$purchased_users = get_post_meta($course_id, 'purchased_users', true);

			if(is_array($purchased_users_timestamp) && !empty($purchased_users_timestamp)) {	
				foreach($purchased_users_timestamp as $purchased_users_timestamp_key => $purchased_users_timestamp_data) {
					if(array_key_exists($purchased_users_timestamp_key, $purchased_users_timestamp_final)) {
						$purchased_users_timestamp_final[$purchased_users_timestamp_key] = count($purchased_users_timestamp_data)+$purchased_users_timestamp_final[$purchased_users_timestamp_key];
					} else {
						$purchased_users_timestamp_final[$purchased_users_timestamp_key] = count($purchased_users_timestamp_data);
					}

					if(array_key_exists($course_id, $purchased_users_data_final[$purchased_users_timestamp_key])) {
						$purchased_users_data_final[$purchased_users_timestamp_key][$course_id] = count($purchased_users_timestamp_data)+$purchased_users_data_final[$purchased_users_timestamp_key][$course_id];
					} else {
						$purchased_users_data_final[$purchased_users_timestamp_key][$course_id] = count($purchased_users_timestamp_data);
					}					
				}
			}			

		}
	}

	wp_reset_postdata();

	$output = dtlms_customize_datas_for_chart($overviewchartoption, $purchased_users_timestamp_final);
	$output .= '##'.dtlms_generate_purchase_overview_item_datas($overviewchartoption, esc_html__('Courses', 'dtlms'), $purchased_users_data_final);

	return $output;

}

function dtlms_get_package_purchase_details($overviewchartoption, $instructor_id = -1) {

	$args = array ( 
				'posts_per_page' => -1, 
				'post_type'=> 'dtlms_packages'
			);
	if($instructor_id > 0) {
		$args['author'] = $instructor_id; 	
	}

	$packages = get_posts( $args );

	$purchased_users_timestamp_final = array ();
	if(is_array($packages) && !empty($packages)) {
		foreach ( $packages as $package ) {
			setup_postdata( $package );

			$package_id = $package->ID;

			$purchased_users_timestamp = get_post_meta($package_id, 'purchased_users_timestamp', true);
			$purchased_users = get_post_meta($package_id, 'purchased_users', true);

			if(is_array($purchased_users_timestamp) && !empty($purchased_users_timestamp)) {	
				foreach($purchased_users_timestamp as $purchased_users_timestamp_key => $purchased_users_timestamp_data) {
					if(array_key_exists($purchased_users_timestamp_key, $purchased_users_timestamp_final)) {
						$purchased_users_timestamp_final[$purchased_users_timestamp_key] = count($purchased_users_timestamp_data)+$purchased_users_timestamp_final[$purchased_users_timestamp_key];
					} else {
						$purchased_users_timestamp_final[$purchased_users_timestamp_key] = count($purchased_users_timestamp_data);
					}

					if(array_key_exists($package_id, $purchased_users_data_final[$purchased_users_timestamp_key])) {
						$purchased_users_data_final[$purchased_users_timestamp_key][$package_id] = count($purchased_users_timestamp_data)+$purchased_users_data_final[$purchased_users_timestamp_key][$package_id];
					} else {
						$purchased_users_data_final[$purchased_users_timestamp_key][$package_id] = count($purchased_users_timestamp_data);
					}					
				}
			}

		}
	}

	wp_reset_postdata();

	$output = dtlms_customize_datas_for_chart($overviewchartoption, $purchased_users_timestamp_final);
	$output .= '##'.dtlms_generate_purchase_overview_item_datas($overviewchartoption, esc_html__('Packages', 'dtlms'), $purchased_users_data_final);

	return $output;

}

function dtlms_generate_purchase_overview_item_datas($overviewchartoption, $item_title, $purchased_users_data_final) {

	$output = '';

	$purchased_users_data_final_keys = array_keys($purchased_users_data_final);

	if($overviewchartoption == 'daily') {
		$purchased_users_data_final_filtered = dtlms_validate_timestamp($purchased_users_data_final_keys, 'daily');
	}

	if($overviewchartoption == 'monthly') {
		$purchased_users_data_final_filtered = dtlms_validate_timestamp($purchased_users_data_final_keys, 'monthly');
	}

	if($overviewchartoption == 'alltime') {
		$purchased_users_data_final_filtered = $purchased_users_data_final_keys;
	}

	$purchased_users_data_final_output = array ();

	if(is_array($purchased_users_data_final_filtered) && !empty($purchased_users_data_final_filtered)) {

		foreach($purchased_users_data_final_filtered as $purchased_users_data_final_filtered_timestamp) {

			foreach($purchased_users_data_final[$purchased_users_data_final_filtered_timestamp] as $purchased_users_data_final_classid => $purchased_users_data_final_classid_data) {

				if(array_key_exists($purchased_users_data_final_classid, $purchased_users_data_final_output)) {
					$purchased_users_data_final_output[$purchased_users_data_final_classid] = $purchased_users_data_final_classid_data+$purchased_users_data_final_output[$purchased_users_data_final_classid];
				} else if($purchased_users_data_final_classid_data > 0) {
					$purchased_users_data_final_output[$purchased_users_data_final_classid] = $purchased_users_data_final_classid_data;
				}

			}

		}

	}

	$output .= '<div class="dtlms-custom-table-wrapper">';
		$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
						<thead>
							<tr>
								<th>'.esc_html__('#', 'dtlms').'</th>
								<th>'.esc_html__($item_title).'</th>
								<th>'.esc_html__('Purchases', 'dtlms').'</th>
							</tr>
						</thead>
						<tbody class="dtlms-custom-dashboard-table">';

						if(is_array($purchased_users_data_final_output) && !empty($purchased_users_data_final_output)) {
							$i = 1;
							foreach($purchased_users_data_final_output as $purchased_user_data_final_output_key => $purchased_user_data_final_output) {
								$output .= '<tr>
												<td>'.$i.'</td>
												<td>'.get_the_title($purchased_user_data_final_output_key).'</td>
												<td>'.$purchased_user_data_final_output.'</td>
											</tr>';
								$i++;
							}
						} else {
								$output .= '<tr>
												<td colspan="3">'.esc_html__('No Records Found!', 'dtlms').'</td>
											</tr>';
						}

		$output .= '</tbody></table>';
	$output .= '</div>';

	return $output;

}


add_action( 'wp_ajax_dtlms_generate_purchases_overview_chart', 'dtlms_generate_purchases_overview_chart' );
add_action( 'wp_ajax_nopriv_dtlms_generate_purchases_overview_chart', 'dtlms_generate_purchases_overview_chart' );
function dtlms_generate_purchases_overview_chart() {

	$output = '';

	$chart_title = $_REQUEST['charttitle'];
	$includeclasspurchases = $_REQUEST['includeclasspurchases'];
	$includecoursepurchases = $_REQUEST['includecoursepurchases'];
	$includepackagepurchases = $_REQUEST['includepackagepurchases'];
	$includedata = $_REQUEST['includedata'];
	$overviewchartoption = $_REQUEST['overviewchartoption'];
	$firstcolor = $_REQUEST['firstcolor'];
	$secondcolor = $_REQUEST['secondcolor'];
	$thirdcolor = $_REQUEST['thirdcolor'];
	$instructor_id = isset($_REQUEST['instructorid']) ? $_REQUEST['instructorid'] : -1;

	if($overviewchartoption == 'daily') {
		$chart_xaxis_label = esc_html__('Days', 'dtlms');
	}

	if($overviewchartoption == 'monthly') {
		$chart_xaxis_label = esc_html__('Months', 'dtlms');
	}

	if($overviewchartoption == 'alltime') {
		$chart_xaxis_label = esc_html__('Years', 'dtlms');
	}

	$chart_class_data = $chart_course_data = $chart_package_data = $chart_label_data = $class_overall_datas = $course_overall_datas = $package_overall_datas = '';
	if($includeclasspurchases == 'true') {
		$class_datas = dtlms_get_class_purchase_details($overviewchartoption, $instructor_id);
		$class_datas = explode('##', $class_datas);
		$class_chart_datas = explode('||', $class_datas[0]);
		$chart_class_data = $class_chart_datas[1];
		$chart_label_data = $class_chart_datas[0];

		$class_overall_datas = explode('||', $class_datas[1]);
		$class_overall_datas = $class_overall_datas[0];
	}

	if($includecoursepurchases == 'true') {
		$course_datas = dtlms_get_course_purchase_details($overviewchartoption, $instructor_id);
		$course_datas = explode('##', $course_datas);
		$course_chart_datas = explode('||', $course_datas[0]);
		$chart_course_data = $course_chart_datas[1];
		$chart_label_data = $course_chart_datas[0];

		$course_overall_datas = explode('||', $course_datas[1]);
		$course_overall_datas = $course_overall_datas[0];
	}

	if($includepackagepurchases == 'true') {	
		$package_datas = dtlms_get_package_purchase_details($overviewchartoption, $instructor_id);
		$package_datas = explode('##', $package_datas);
		$package_chart_datas = explode('||', $package_datas[0]);
		$chart_package_data = $package_chart_datas[1];
		$chart_label_data = $package_chart_datas[0];

		$package_overall_datas = explode('||', $package_datas[1]);
		$package_overall_datas = $package_overall_datas[0];
	}	


	if($includedata == 'true') {
		$output .= '<div class="dtlms-column dtlms-one-half first">';
			$output .= $class_overall_datas.$course_overall_datas.$package_overall_datas;
		$output .= '</div>';
		$output .= '<div class="dtlms-column dtlms-one-half">';
	}

	$class_singular_label = apply_filters( 'class_label', 'singular' );

	$chart_class_dataset = $chart_course_dataset = $chart_package_dataset = '';

	if($chart_class_data != ''){
		$chart_class_dataset = '{
					                label: "'.sprintf( esc_html__( '%1$s Purchases', 'dtlms' ), $class_singular_label ).'",
					                backgroundColor: "'.$firstcolor.'",
					                borderWidth: 1,
					                data: '.$chart_class_data.',
					            },';
	}
	if($chart_course_data != ''){
		$chart_course_dataset = '{
					                label: "'.esc_html__('Course Purchases', 'dtlms').'",
					                backgroundColor: "'.$secondcolor.'",
					                borderWidth: 1,
					                data: '.$chart_course_data.',
					            },';
	}
	if($chart_package_data != ''){
		$chart_package_dataset = '{
					                label: "'.esc_html__('Package Purchases', 'dtlms').'",
					                backgroundColor: "'.$thirdcolor.'",
					                borderWidth: 1,
					                data: '.$chart_package_data.',
					            },';
	}	
	

	if($chart_label_data != '') {

		$legend_position = dtlms_option('chart', 'legend-position');
		$legend_position = ($legend_position != '') ? $legend_position : 'right';

		$chart_id = dtlms_generate_random_number();

		$output .= '<canvas id="dtlmsPurchasesOverviewChart-'.$chart_id.'"></canvas>';
		$output .= '<script>

						jQuery(document).ready(function() {

					        var dtlmsChartData = {
					            labels: '.$chart_label_data.',
					            datasets: ['.$chart_class_dataset.$chart_course_dataset.$chart_package_dataset.']
					        };

				            var ctx = document.getElementById("dtlmsPurchasesOverviewChart-'.$chart_id.'").getContext("2d");
				            window.dtlmsPurchasesOverviewChart = new Chart(ctx, {
				                type: "bar",
				                data: dtlmsChartData,
				                options: {
				                    elements: {
				                        rectangle: {
				                            borderWidth: 2,
				                        }
				                    },
				                    responsive: true,
				                    legend: {
				                        position: "'.$legend_position.'",
				                    },
				                    title: {
				                        display: true,
				                        text: "'.$chart_title.'"
				                    },
							        scales: {
							            xAxes: [{
										      scaleLabel: {
										        display: true,
										        labelString: "'.$chart_xaxis_label.'"
										      }
							            }],
							            yAxes: [{
							                ticks: {
							                    beginAtZero:true
							                }
							            }]			            
							        }                   
				                }
				            });

				        });

			        </script>';	

	} else {

		$output .= esc_html__('No records found!', 'dtlms');

	}  

	if($includedata == 'true') {
		$output .= '</div>';
	}

	echo $output;

	die();

}


function dtlms_get_instructor_commission_overperiod_details($overviewchartoption, $commission_type, $instructor_id = -1) {

	$commissions_received = get_user_meta($instructor_id, 'commissions-received', true);

	$commissions_received_keys = array_keys($commissions_received);

	if($overviewchartoption == 'daily') {

		$commissions_received_keys_filtered = dtlms_validate_timestamp($commissions_received_keys, 'daily');
		$chart_datas = dtlms_generate_days_array();

		$chart_data_keys = array_keys($chart_datas);
		$chart_label_string = '['.implode(',', $chart_data_keys).']';

		foreach ($commissions_received_keys_filtered as $timestamp) {
			$chart_datas[date('d', $timestamp)] = $commissions_received[$timestamp][$commission_type];
		}

		$chart_datas_string = '['.implode(',', $chart_datas).']';	

	}

	if($overviewchartoption == 'monthly') {

		$commissions_received_keys_filtered = dtlms_validate_timestamp($commissions_received_keys, 'monthly');

		$chart_label = dtlms_generate_months_label();
		$chart_label_string = '["'.implode('","', $chart_label).'"]';

		$chart_datas = dtlms_generate_months_array();
		foreach ($commissions_received_keys_filtered as $timestamp) {
			$chart_datas[date('m', $timestamp)] = $chart_datas[date('m', $timestamp)] + $commissions_received[$timestamp][$commission_type];
		}

		$chart_datas_string = '['.implode(',', $chart_datas).']';	

	}

	if($overviewchartoption == 'alltime') {

		$commissions_received_keys_filtered = $commissions_received_keys;

		$chart_label = dtlms_generate_years_label($commissions_received_keys);
		$chart_label_keys = array_keys($chart_label);
		$chart_label_string = '['.implode(',', $chart_label_keys).']';

		$chart_datas = $chart_label;
		foreach ($commissions_received_keys_filtered as $timestamp) {
			$chart_datas[date('Y', $timestamp)] = $chart_datas[date('Y', $timestamp)] + $commissions_received[$timestamp][$commission_type];
		}

		$chart_datas_string = '['.implode(',', $chart_datas).']';

	}

	return $chart_label_string.'||'.$chart_datas_string;

}

function dtlms_get_instructor_commission_overitem_details($overviewchartoption, $commission_type, $instructor_id = -1) {

	$commissions_details = array ();

	$commissions_received = get_user_meta($instructor_id, 'commissions-received', true);
	$commissions_received_keys = array_keys($commissions_received);

	if($overviewchartoption == 'daily') {
		$commissions_received_keys_filtered = dtlms_validate_timestamp($commissions_received_keys, 'daily');
	}

	if($overviewchartoption == 'monthly') {
		$commissions_received_keys_filtered = dtlms_validate_timestamp($commissions_received_keys, 'monthly');
	}

	if($overviewchartoption == 'alltime') {
		$commissions_received_keys_filtered = $commissions_received_keys;
	}

	if(in_array($commission_type, array('classes', 'courses'))) {
		foreach ($commissions_received_keys_filtered as $timestamp) {
			foreach ($commissions_received[$timestamp][$commission_type] as $item_id => $item_details) {

				$woo_price = 0;
				$product = dtlms_get_product_object($item_id);
				if($product->get_sale_price() != '') {
					$woo_price = $product->get_sale_price();
				} else {
					$woo_price = $product->get_regular_price();
				}

				$commission_amount = 0;
				foreach ($item_details as $item_detail_key => $item_detail_value) {
					$commission_amount = $commission_amount + (($item_detail_value*$woo_price)*$item_detail_key)/100;
				}

				if(in_array($item_id, array_keys($commissions_details))) {
					$commissions_details[$item_id] = $commissions_details[$item_id] + $commission_amount;
				} else {
					$commissions_details[$item_id] = $commission_amount;
				}

			}
		}
	}


	if($commission_type == 'other-amounts') {
		foreach ($commissions_received_keys_filtered as $timestamp) {

			if(isset($commissions_details['other-amounts'])) {
				$commissions_details['other-amounts'] = $commissions_details['other-amounts'] + $commissions_received[$timestamp]['other-amounts'];
			} else {
				$commissions_details['other-amounts'] = $commissions_received[$timestamp]['other-amounts'];
			}

		}
	}

	if($commission_type == 'total-commission') {
		foreach ($commissions_received_keys_filtered as $timestamp) {

			if(isset($commissions_details['total-commission'])) {
				$commissions_details['total-commission'] = $commissions_details['total-commission'] + $commissions_received[$timestamp]['total-commission'];
			} else {
				$commissions_details['total-commission'] = $commissions_received[$timestamp]['total-commission'];
			}

		}
	}	


	return $commissions_details;

}

function dtlms_generate_commission_item_datas($item_datas, $item_type) {

	$output = '';

	
	if(in_array($item_type, array('classes', 'courses'))) {

		if(is_array($item_datas) && !empty($item_datas)) {

			$output .= '<div class="dtlms-custom-table-wrapper">';

				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
								<thead>
									<tr>
										<th>'.esc_html__('#', 'dtlms').'</th>
										<th>'.ucfirst($item_type).'</th>
										<th>'.esc_html__('Commissions', 'dtlms').' ('.get_woocommerce_currency_symbol().')</th>
									</tr>
								</thead>
								<tbody class="dtlms-custom-dashboard-table">';

								$i = 1;
								$total_amount = 0;
								foreach($item_datas as $item_data_key => $item_data) {
									$output .= '<tr>
													<td>'.$i.'</td>
													<td>'.get_the_title($item_data_key).'</td>
													<td>'.$item_data.'</td>
												</tr>';
									$total_amount = $total_amount + $item_data;
									$i++;
								}

								$output .= '<tr>
												<td colspan="3"></td>
											</tr>';	

								$output .= '<tr>
												<td colspan="2">'.ucfirst($item_type).' '.esc_html__('Total Commission', 'dtlms').' ('.get_woocommerce_currency_symbol().')</td>
												<td>'.$total_amount.'</td>
											</tr>';

				$output .= '</tbody></table>';

			$output .= '</div>';

		}

	}

	if($item_type == 'other-amounts') {

		if(isset($item_datas['other-amounts'])) {

			$output .= '<div class="dtlms-custom-table-wrapper">';

				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">';	

								$output .= '<tr>
												<td colspan="2">'.esc_html__('Other Amounts', 'dtlms').' ('.get_woocommerce_currency_symbol().')</td>
												<td>'.$item_datas['other-amounts'].'</td>
											</tr>';

				$output .= '</table>';

			$output .= '</div>';

		}

	}

	if($item_type == 'total-commission') {

		if(isset($item_datas['total-commission'])) {

			$output .= '<div class="dtlms-custom-table-wrapper">';

				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">';	

								$output .= '<tr>
												<td colspan="2">'.esc_html__('Total Commission', 'dtlms').' ('.get_woocommerce_currency_symbol().')</td>
												<td>'.$item_datas['total-commission'].'</td>
											</tr>';

				$output .= '</table>';

			$output .= '</div>';

		}

	}

	return $output;

}


add_action( 'wp_ajax_dtlms_generate_commissions_overview_chart', 'dtlms_generate_commissions_overview_chart' );
add_action( 'wp_ajax_nopriv_dtlms_generate_commissions_overview_chart', 'dtlms_generate_commissions_overview_chart' );
function dtlms_generate_commissions_overview_chart() {

	$output = '';

	$overviewchartoption = $_REQUEST['overviewchartoption'];
	$charttitle = $_REQUEST['charttitle'];
	$instructorearnings = $_REQUEST['instructorearnings'];
	$contentfilter = $_REQUEST['contentfilter'];
	$charttype = $_REQUEST['charttype'];
	$timelinefilter = $_REQUEST['timelinefilter'];
	$includecoursecommission = $_REQUEST['includecoursecommission'];
	$includeclasscommission = $_REQUEST['includeclasscommission'];
	$includeothercommission = $_REQUEST['includeothercommission'];
	$includetotalcommission = $_REQUEST['includetotalcommission'];
	$instructorid = isset($_REQUEST['instructorid']) ? $_REQUEST['instructorid'] : -1;

	if($charttitle == '') {
		if($instructorearnings == 'over-period') {
			$charttitle = esc_html('Commissions Over Period', 'dtlms');
		} else if($instructorearnings == 'over-item') {
			$charttitle = esc_html('Commissions Over Item', 'dtlms');
		}
	}

	if($instructorid > 0) {

		$legend_position = dtlms_option('chart', 'legend-position');
		$legend_position = ($legend_position != '') ? $legend_position : 'right';


		if($overviewchartoption == 'daily') {
			$chart_xaxis_label = esc_html__('Days', 'dtlms');
		}

		if($overviewchartoption == 'monthly') {
			$chart_xaxis_label = esc_html__('Months', 'dtlms');
		}

		if($overviewchartoption == 'alltime') {
			$chart_xaxis_label = esc_html__('Years', 'dtlms');
		}

		$overperiod_chart_label = '';
		$overperiod_coursecommission_chart_dataset = $overperiod_classcommission_chart_dataset = $overperiod_othercommission_chart_dataset = $overperiod_totalcommission_chart_dataset = '';
		$overperiod_coursecommission_datas = $overperiod_classcommission_datas = $overperiod_othercommission_datas = $overperiod_totalcommission_datas = '';


		$overitem_coursecommission_chart_label = $overitem_classcommission_chart_label = $overitem_othercommission_chart_label = $overitem_totalcommission_chart_label = '';
		$overitem_coursecommission_chart_dataset = $overitem_classcommission_chart_dataset = $overitem_othercommission_chart_dataset = $overitem_totalcommission_chart_dataset = '';
		$overitem_coursecommission_datas = $overitem_classcommission_datas = $overitem_othercommission_datas = $overitem_totalcommission_datas = '';

		$overitem_coursecommission_totalitems = $overitem_classcommission_totalitems = $overitem_othercommission_totalitems = $overitem_totalcommission_totalitems = 6;

		if($instructorearnings == 'over-period') {
			if($contentfilter == 'chart' || $contentfilter == 'both') {
				$chart_colors = dtlms_option('chart', 'chart-colors');
				if(dtlms_option('chart', 'shuffle-colors') == 'true') {
					shuffle($chart_colors);
				}
				$firstcolor = $chart_colors[0];
				$secondcolor = $chart_colors[1];
				$thirdcolor = $chart_colors[2];
				$fourthcolor = $chart_colors[3];	
			}
		}


		if($includecoursecommission == 'true') {

			if($instructorearnings == 'over-period') {

				if($contentfilter == 'chart' || $contentfilter == 'both') {

					$commission_datas = dtlms_get_instructor_commission_overperiod_details($overviewchartoption, $instructorid, 'course-commission');
					$commission_datas = explode('||', $commission_datas);
					$chart_coursecommission_data = $commission_datas[1];
					$overperiod_chart_label = $commission_datas[0];

					$overperiod_coursecommission_chart_dataset = '{
										                label: "'.esc_html__('Course Commissions', 'dtlms').'",
										                borderColor: "'.$firstcolor.'",
										                backgroundColor: "'.$firstcolor.'",
										                borderWidth: 1,
										                data: '.$chart_coursecommission_data.',
										                fill: false,
										            },';					

				}

				if($contentfilter == 'data' || $contentfilter == 'both') {

					$coursecommission_datas = dtlms_get_instructor_commission_overitem_details($overviewchartoption, $instructorid, 'courses');
					$overperiod_coursecommission_datas = dtlms_generate_commission_item_datas($coursecommission_datas, 'courses');

				}				

			} else if($instructorearnings == 'over-item') {

				$item_coursecommission_datas = dtlms_get_instructor_commission_overitem_details($overviewchartoption, $instructorid, 'courses');

				if($contentfilter == 'chart' || $contentfilter == 'both') {

					$chart_label_string = '["';
					$chart_data_string = '[';
					foreach($item_coursecommission_datas as $item_coursecommission_data_key => $item_coursecommission_data) {
						$chart_label_string .= get_the_title($item_coursecommission_data_key).'","';
						$chart_data_string .= $item_coursecommission_data.',';
					}
					$chart_label_string = rtrim($chart_label_string, '","').'"]';
					$chart_data_string = rtrim($chart_data_string, ',').']';

					if($chart_label_string == '["]') {
						$chart_label_string = '""';
					}
					if($chart_data_string == '[]') {
						$chart_data_string = '""';
					}

					$overitem_coursecommission_chart_label = $chart_label_string;
					$overitem_coursecommission_chart_dataset = $chart_data_string;

				}

				if($contentfilter == 'data' || $contentfilter == 'both') {

					$overitem_coursecommission_totalitems = count($item_coursecommission_datas);
					$overitem_coursecommission_datas = dtlms_generate_commission_item_datas($item_coursecommission_datas, 'courses');

				}

			}

		}

		if($includeclasscommission == 'true') {

			if($instructorearnings == 'over-period') {

				if($contentfilter == 'chart' || $contentfilter == 'both') {

					$class_singular_label = apply_filters( 'class_label', 'singular' );

					$commission_datas = dtlms_get_instructor_commission_overperiod_details($overviewchartoption, $instructorid, 'class-commission');
					$commission_datas = explode('||', $commission_datas);
					$chart_classcommission_data = $commission_datas[1];
					$overperiod_chart_label = $commission_datas[0];

					$overperiod_classcommission_chart_dataset = '{
										                label: "'.sprintf( esc_html__( '%1$s Commissions', 'dtlms' ), $class_singular_label ).'",
										                borderColor: "'.$secondcolor.'",
										                backgroundColor: "'.$secondcolor.'",
										                borderWidth: 1,
										                data: '.$chart_classcommission_data.',
										                fill: false,
										            },';					

				}

				if($contentfilter == 'data' || $contentfilter == 'both') {

					$classcommission_datas = dtlms_get_instructor_commission_overitem_details($overviewchartoption, $instructorid, 'classes');
					$overperiod_classcommission_datas = dtlms_generate_commission_item_datas($classcommission_datas, 'classes');

				}				

			} else if($instructorearnings == 'over-item') {

				$item_classcommission_datas = dtlms_get_instructor_commission_overitem_details($overviewchartoption, $instructorid, 'classes');

				if($contentfilter == 'chart' || $contentfilter == 'both') {

					$chart_label_string = '["';
					$chart_data_string = '[';
					foreach($item_classcommission_datas as $item_classcommission_data_key => $item_classcommission_data) {
						$chart_label_string .= get_the_title($item_classcommission_data_key).'","';
						$chart_data_string .= $item_classcommission_data.',';
					}
					$chart_label_string = rtrim($chart_label_string, '","').'"]';
					$chart_data_string = rtrim($chart_data_string, ',').']';

					if($chart_label_string == '["]') {
						$chart_label_string = '""';
					}
					if($chart_data_string == '[]') {
						$chart_data_string = '""';
					}

					$overitem_classcommission_chart_label = $chart_label_string;
					$overitem_classcommission_chart_dataset = $chart_data_string;

				}

				if($contentfilter == 'data' || $contentfilter == 'both') {

					$overitem_classcommission_totalitems = count($item_classcommission_datas);
					$overitem_classcommission_datas = dtlms_generate_commission_item_datas($item_classcommission_datas, 'classes');

				}

			}

		}

		if($includeothercommission == 'true') {

			if($instructorearnings == 'over-period') {

				if($contentfilter == 'chart' || $contentfilter == 'both') {

					$commission_datas = dtlms_get_instructor_commission_overperiod_details($overviewchartoption, $instructorid, 'other-amounts');
					$commission_datas = explode('||', $commission_datas);
					$chart_othercommission_data = $commission_datas[1];
					$overperiod_chart_label = $commission_datas[0];

					$overperiod_othercommission_chart_dataset = '{
										                label: "'.esc_html__('Other Amounts', 'dtlms').'",
										                borderColor: "'.$thirdcolor.'",
										                backgroundColor: "'.$thirdcolor.'",
										                borderWidth: 1,
										                data: '.$chart_othercommission_data.',
										                fill: false,
										            },';					

				}

				if($contentfilter == 'data' || $contentfilter == 'both') {

					$othercommission_datas = dtlms_get_instructor_commission_overitem_details($overviewchartoption, $instructorid, 'other-amounts');
					$overperiod_othercommission_datas = dtlms_generate_commission_item_datas($othercommission_datas, 'other-amounts');

				}				

			} else if($instructorearnings == 'over-item') {

				$item_othercommission_datas = dtlms_get_instructor_commission_overitem_details($overviewchartoption, $instructorid, 'other-amounts');

				if($contentfilter == 'chart' || $contentfilter == 'both') {

					$chart_label_string = '["';
					$chart_data_string = '[';
					foreach($item_othercommission_datas as $item_othercommission_data_key => $item_othercommission_data) {
						$chart_label_string .= esc_html__('Other Amounts', 'dtlms').'","';
						$chart_data_string .= $item_othercommission_data.',';
					}
					$chart_label_string = rtrim($chart_label_string, '","').'"]';
					$chart_data_string = rtrim($chart_data_string, ',').']';

					if($chart_label_string == '["]') {
						$chart_label_string = '""';
					}
					if($chart_data_string == '[]') {
						$chart_data_string = '""';
					}

					$overitem_othercommission_chart_label = $chart_label_string;
					$overitem_othercommission_chart_dataset = $chart_data_string;

				}

				if($contentfilter == 'data' || $contentfilter == 'both') {

					$overitem_othercommission_totalitems = count($item_othercommission_datas);
					$overitem_othercommission_datas = dtlms_generate_commission_item_datas($item_othercommission_datas, 'other-amounts');

				}

			}

		}

		if($includetotalcommission == 'true') {

			if($instructorearnings == 'over-period') {

				if($contentfilter == 'chart' || $contentfilter == 'both') {

					$commission_datas = dtlms_get_instructor_commission_overperiod_details($overviewchartoption, $instructorid, 'total-commission');
					$commission_datas = explode('||', $commission_datas);
					$chart_totalcommission_data = $commission_datas[1];
					$overperiod_chart_label = $commission_datas[0];

					$overperiod_totalcommission_chart_dataset = '{
										                label: "'.esc_html__('Total Commissions', 'dtlms').'",
										                borderColor: "'.$fourthcolor.'",
										                backgroundColor: "'.$fourthcolor.'",
										                borderWidth: 1,
										                data: '.$chart_totalcommission_data.',
										                fill: false,
										            },';				

				}

				if($contentfilter == 'data' || $contentfilter == 'both') {

					$totalcommission_datas = dtlms_get_instructor_commission_overitem_details($overviewchartoption, $instructorid, 'total-commission');
					$overperiod_totalcommission_datas = dtlms_generate_commission_item_datas($totalcommission_datas, 'total-commission');

				}				

			} else if($instructorearnings == 'over-item') {

				$item_totalcommission_datas = dtlms_get_instructor_commission_overitem_details($overviewchartoption, $instructorid, 'total-commission');

				if($contentfilter == 'chart' || $contentfilter == 'both') {

					$chart_label_string = '["';
					$chart_data_string = '[';
					foreach($item_totalcommission_datas as $item_totalcommission_data_key => $item_totalcommission_data) {
						$chart_label_string .= esc_html__('Total Commission', 'dtlms').'","';
						$chart_data_string .= $item_totalcommission_data.',';
					}
					$chart_label_string = rtrim($chart_label_string, '","').'"]';
					$chart_data_string = rtrim($chart_data_string, ',').']';

					if($chart_label_string == '["]') {
						$chart_label_string = '""';
					}
					if($chart_data_string == '[]') {
						$chart_data_string = '""';
					}

					$overitem_totalcommission_chart_label = $chart_label_string;
					$overitem_totalcommission_chart_dataset = $chart_data_string;

				}

				if($contentfilter == 'data' || $contentfilter == 'both') {

					$overitem_totalcommission_totalitems = count($item_totalcommission_datas);
					$overitem_totalcommission_datas = dtlms_generate_commission_item_datas($item_totalcommission_datas, 'total-commission');

				}

			}

		}


		if($instructorid > 0) {
			$output .= '<p>'.sprintf(esc_html__('Commission details of %s', 'dtlms'), '<strong>'.get_the_author_meta('display_name', $instructorid).'</strong>').'</p>';
		}

		// Over Item

		if($instructorearnings == 'over-item') {

			if($contentfilter == 'both') {
				$output .= '<div class="dtlms-column dtlms-one-half first">';
			}

			if($contentfilter == 'data' || $contentfilter == 'both') {

				$output .= $overitem_coursecommission_datas.$overitem_classcommission_datas.$overitem_othercommission_datas.$overitem_totalcommission_datas;

			}

			if($contentfilter == 'both') {
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-one-half">';
			}

			$chart_scale_option = '';
			if($charttype == 'bar' || $charttype == 'line') {
				$chart_scale_option = 'scales: {
									            xAxes: [{
												    ticks: {
									                    beginAtZero:true
									                }
									            }],
									            yAxes: [{
									                ticks: {
									                    beginAtZero:true
									                }
									            }]			            
									        }';
			}

			$label = esc_html__('Commission Amount', 'dtlms');

			if($contentfilter == 'chart' || $contentfilter == 'both') {

				$chart_colors = dtlms_option('chart', 'chart-colors');

				if($overitem_coursecommission_chart_dataset != '') {

					if(dtlms_option('chart', 'shuffle-colors') == 'true') {
						shuffle($chart_colors);
					}

					if($charttype == 'pie') {
						$coursecommission_chart_colors = array_slice($chart_colors, 0, $overitem_coursecommission_totalitems);
						$coursecommission_chart_colors = implode('","', $coursecommission_chart_colors);
						$coursecommission_chart_colors = '["'.$coursecommission_chart_colors.'"]';
					} else {
						$coursecommission_chart_colors = '"'.$chart_colors[0].'"';						
					}

					$chart_id = dtlms_generate_random_number();

					$output .= '<canvas id="dtlmsCommissionsOverItemChart-'.$chart_id.'"></canvas>';
					$output .= '<script>

							        var dtlmsCommissionsOverItemChartData = {
							            labels: '.$overitem_coursecommission_chart_label.',
							            datasets: [{
							            	label: "'.$label.'",
							                backgroundColor: '.$coursecommission_chart_colors.',
							                data: '.$overitem_coursecommission_chart_dataset.',
							            }]
							        };

								    var ctx = document.getElementById("dtlmsCommissionsOverItemChart-'.$chart_id.'").getContext("2d");
								    window.dtlmsCommissionsOverItemChart = new Chart(ctx, {
						                type: "'.$charttype.'",
						                data: dtlmsCommissionsOverItemChartData,
						                options: {
						                    responsive: true,
						                    legend: {
						                    	display: true,
						                        position: "'.$legend_position.'",
						                    },
						                    title: {
						                        display: true,
						                        text: "'.$charttitle.'"
						                    },
									        '.$chart_scale_option.'								        
						                }
						            });

						        </script>';	
						        				
				}

				if($overitem_classcommission_chart_dataset != '') {

					if(dtlms_option('chart', 'shuffle-colors') == 'true') {
						shuffle($chart_colors);
					}

					if($charttype == 'pie') {
						$classcommission_chart_colors = array_slice($chart_colors, 0, $overitem_classcommission_totalitems);
						$classcommission_chart_colors = implode('","', $classcommission_chart_colors);
						$classcommission_chart_colors = '["'.$classcommission_chart_colors.'"]';
					} else {
						$classcommission_chart_colors = '"'.$chart_colors[0].'"';						
					}

					$chart_id = dtlms_generate_random_number();

					$output .= '<canvas id="dtlmsCommissionsOverItemChart-'.$chart_id.'"></canvas>';
					$output .= '<script>

							        var dtlmsCommissionsOverItemChartData = {
							            labels: '.$overitem_classcommission_chart_label.',
							            datasets: [{
							                label: "'.$label.'",
							                backgroundColor: '.$classcommission_chart_colors.',
							                data: '.$overitem_classcommission_chart_dataset.',
							            }]
							        };

								    var ctx = document.getElementById("dtlmsCommissionsOverItemChart-'.$chart_id.'").getContext("2d");
								    window.dtlmsCommissionsOverItemChart = new Chart(ctx, {
						                type: "'.$charttype.'",
						                data: dtlmsCommissionsOverItemChartData,
						                options: {
						                    responsive: true,
						                    legend: {
						                        position: "'.$legend_position.'",
						                    },
						                    title: {
						                        display: true,
						                        text: "'.$charttitle.'"
						                    },
									        '.$chart_scale_option.' 
						                }
						            });

						        </script>';	
						        				
				}

				if($overitem_othercommission_chart_dataset != '') {

					if(dtlms_option('chart', 'shuffle-colors') == 'true') {
						shuffle($chart_colors);
					}

					if($charttype == 'pie') {
						$othercommission_chart_colors = array_slice($chart_colors, 0, $overitem_othercommission_totalitems);
						$othercommission_chart_colors = implode('","', $othercommission_chart_colors);
						$othercommission_chart_colors = '["'.$othercommission_chart_colors.'"]';
					} else {
						$othercommission_chart_colors = '"'.$chart_colors[0].'"';						
					}

					$chart_id = dtlms_generate_random_number();

					$output .= '<canvas id="dtlmsCommissionsOverItemChart-'.$chart_id.'"></canvas>';
					$output .= '<script>

							        var dtlmsCommissionsOverItemChartData = {
							            labels: '.$overitem_othercommission_chart_label.',
							            datasets: [{
							                label: "'.$label.'",
							                backgroundColor: '.$othercommission_chart_colors.',
							                data: '.$overitem_othercommission_chart_dataset.',
							            }]
							        };

								    var ctx = document.getElementById("dtlmsCommissionsOverItemChart-'.$chart_id.'").getContext("2d");
								    window.dtlmsCommissionsOverItemChart = new Chart(ctx, {
						                type: "'.$charttype.'",
						                data: dtlmsCommissionsOverItemChartData,
						                options: {
						                    responsive: true,
						                    legend: {
						                        position: "'.$legend_position.'",
						                    },
						                    title: {
						                        display: true,
						                        text: "'.$charttitle.'"
						                    },
									        '.$chart_scale_option.'  
						                }
						            });

						        </script>';	
						        				
				}	

				if($overitem_totalcommission_chart_dataset != '') {

					if(dtlms_option('chart', 'shuffle-colors') == 'true') {
						shuffle($chart_colors);
					}

					if($charttype == 'pie') {
						$totalcommission_chart_colors = array_slice($chart_colors, 0, $overitem_totalcommission_totalitems);
						$totalcommission_chart_colors = implode('","', $totalcommission_chart_colors);
						$totalcommission_chart_colors = '["'.$totalcommission_chart_colors.'"]';
					} else {
						$totalcommission_chart_colors = '"'.$chart_colors[0].'"';						
					}

					$chart_id = dtlms_generate_random_number();

					$output .= '<canvas id="dtlmsCommissionsOverItemChart-'.$chart_id.'"></canvas>';
					$output .= '<script>

							        var dtlmsCommissionsOverItemChartData = {
							            labels: '.$overitem_totalcommission_chart_label.',
							            datasets: [{
							                label: "'.$label.'",
							                backgroundColor: '.$totalcommission_chart_colors.',
							                data: '.$overitem_totalcommission_chart_dataset.',
							            }]
							        };

								    var ctx = document.getElementById("dtlmsCommissionsOverItemChart-'.$chart_id.'").getContext("2d");
								    window.dtlmsCommissionsOverItemChart = new Chart(ctx, {
						                type: "'.$charttype.'",
						                data: dtlmsCommissionsOverItemChartData,
						                options: {
						                    responsive: true,
						                    legend: {
						                        position: "'.$legend_position.'",
						                    },
						                    title: {
						                        display: true,
						                        text: "'.$charttitle.'"
						                    },
									        '.$chart_scale_option.'  
						                }
						            });

						        </script>';	
						        				
				}							

		    }

			if($contentfilter == 'both') {
				$output .= '</div>';
			}

		}

		// Over Period

		if($instructorearnings == 'over-period') {

			if($charttype == 'pie') {
				$charttype = 'bar';
			}

			if($contentfilter == 'both') {
				$output .= '<div class="dtlms-column dtlms-one-half first">';
			}			

			if($contentfilter == 'data' || $contentfilter == 'both') {

				$output .= $overperiod_coursecommission_datas.$overperiod_classcommission_datas.$overperiod_othercommission_datas.$overperiod_totalcommission_datas;

			}

			if($contentfilter == 'both') {
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-one-half">';
			}			

			if($contentfilter == 'chart' || $contentfilter == 'both') {

				$chart_id = dtlms_generate_random_number();

				$output .= '<canvas id="dtlmsCommissionsOverPeriodChart-'.$chart_id.'"></canvas>';
				$output .= '<script>

						        var dtlmsCommissionsOverPeriodChartData = {
						            labels: '.$overperiod_chart_label.',
						            datasets: ['.$overperiod_classcommission_chart_dataset.$overperiod_coursecommission_chart_dataset.$overperiod_othercommission_chart_dataset.$overperiod_totalcommission_chart_dataset.']
						        };					        

							    var ctx = document.getElementById("dtlmsCommissionsOverPeriodChart-'.$chart_id.'").getContext("2d");
							    window.dtlmsCommissionsOverPeriodChart = new Chart(ctx, {
					                type: "'.$charttype.'",
					                data: dtlmsCommissionsOverPeriodChartData,
					                options: {
					                    responsive: true,
					                    legend: {
					                        position: "'.$legend_position.'",
					                    },
					                    title: {
					                        display: true,
					                        text: "'.$charttitle.'"
					                    }                
					                }
					            });

					        </script>';

		    }

			if($contentfilter == 'both') {
				$output .= '</div>';
			}		    

		}

	} else {

		$instructor_singular_label = apply_filters( 'instructor_label', 'singular' );
		$output .= sprintf( esc_html__( 'Please make sure %1$s is selected', 'dtlms' ), $instructor_singular_label );

	}

	echo $output;

	die();

}


add_action( 'wp_ajax_dtlms_load_instructorwise_courses', 'dtlms_load_instructorwise_courses' );
add_action( 'wp_ajax_nopriv_dtlms_load_instructorwise_courses', 'dtlms_load_instructorwise_courses' );
function dtlms_load_instructorwise_courses( $dashboard_function_call, $dashboard_output_div, $instructor_dashboard_id = -1) {

	$output = '';

	if($instructor_dashboard_id > 0) {
		$instructor_id = $instructor_dashboard_id;
	} else {
		$instructor_id = isset($_REQUEST['instructor_id']) ? $_REQUEST['instructor_id'] : -1;
	}

	// Pagination script Start
	$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
	$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
	$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

	if($dashboard_function_call != '') {
		$function_call = $dashboard_function_call;
	} else {
		$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : '';
	}

	if($dashboard_output_div != '') {
		$output_div = $dashboard_output_div;
	} else {
		$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : '';
	}
	// Pagination script End

	$class_singular_label = apply_filters( 'class_label', 'singular' );

	$args = array ( 
					'offset' => $offset,
					'paged' => $current_page,
					'posts_per_page' => $post_per_page, 
					'post_type'=> 'dtlms_courses'
				);

	if($instructor_id > 0) {
		$args['author'] = $instructor_id; 	
	}

	$courses = get_posts( $args );

	$output .= '<div class="dtlms-custom-table-wrapper">';

		$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
						<thead>
							<tr>
								<th>'.esc_html__('#', 'dtlms').'</th>
								<th>'.esc_html__('Course', 'dtlms').'</th>
								<th>'.esc_html__('# Students - Direct Purchase', 'dtlms').'</th>
								<th>'.esc_html__('# Students - Assigned', 'dtlms').'</th>
								<th>'.esc_html__('# Students - Package Purchase', 'dtlms').'</th>
								<th>'.sprintf( esc_html__( '# Students - %1$s Purchase', 'dtlms' ), $class_singular_label ).'</th>
								<th>'.esc_html__('# Under Progress', 'dtlms').'</th>
								<th>'.esc_html__('# Under Evaluation', 'dtlms').'</th>
								<th>'.esc_html__('# Completed', 'dtlms').'</th>
								<th>'.esc_html__('# Badges', 'dtlms').'</th>
								<th>'.esc_html__('# Certificates', 'dtlms').'</th>
							</tr>
						</thead>
						<tbody class="dtlms-custom-dashboard-table">';

		if(is_array($courses) && !empty($courses)) {

			$i = $offset+1;
			foreach ( $courses as $course ) {
				setup_postdata( $course );

				$course_id = $course->ID;
				$course_title = get_the_title($course_id);
				$author_id = $course->post_author;
				
				$purchased_users = get_post_meta($course_id, 'purchased_users', true);
				$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array ();

				$assigned_users = dtlms_get_course_assigned_users($course_id);
				$assigned_users = (is_array($assigned_users) && !empty($assigned_users)) ? $assigned_users : array ();	

				$package_purchased_users = dtlms_get_course_package_purchased_users($course_id);
				$package_purchased_users = (is_array($package_purchased_users) && !empty($package_purchased_users)) ? $package_purchased_users : array ();

				$class_purchased_users = dtlms_get_course_class_purchased_users($course_id);
				$class_purchased_users = (is_array($class_purchased_users) && !empty($class_purchased_users)) ? $class_purchased_users : array ();	

				$started_users = get_post_meta($course_id, 'started_users', true);
				$started_users = (is_array($started_users) && !empty($started_users)) ? $started_users : array ();

				$submitted_users = get_post_meta($course_id, 'submitted_users', true);
				$submitted_users = (is_array($submitted_users) && !empty($submitted_users)) ? $submitted_users : array ();

				$completed_users = get_post_meta($course_id, 'completed_users', true);
				$completed_users = (is_array($completed_users) && !empty($completed_users)) ? $completed_users : array ();

				$users_undergoing = array_diff($started_users, $submitted_users);
				$users_underevaluation = array_diff($submitted_users, $completed_users);

				$badge_achieved_cnt = $certificate_achieved_cnt = 0;
				if(is_array($completed_users) && !empty($completed_users)) {
					foreach($completed_users as $completed_user) {

						$curriculum_details = get_user_meta($completed_user, $course_id, true);
						$completed_course_grade_id = isset($curriculum_details['grade-post-id']) ? $curriculum_details['grade-post-id'] : -1;

			            $badge_achieved = get_post_meta($completed_course_grade_id, 'badge-achieved', true);
			            if($badge_achieved == 'true') {
			            	$badge_achieved_cnt++;
			            }

						$certificate_achieved = get_post_meta($completed_course_grade_id, 'certificate-achieved', true);
						if($certificate_achieved == 'true') {
							$certificate_achieved_cnt++;
						}

					}							
				}
				
				$purchased_users_html = '';
				if(is_array($purchased_users) && !empty($purchased_users)) {
					$purchased_users_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$purchased_users_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					foreach($purchased_users as $purchased_user) {
						$purchased_users_html .= '<li><a href="#" class="dtlms-button filled small"  data-courseid="'.$course_id.'" data-userid="'.$purchased_user.'">'.get_the_author_meta('display_name', $purchased_user).'</a></li>';
					}
					$purchased_users_html .= '</ul></div></div>';
				}

				$assigned_users_html = '';
				if(is_array($assigned_users) && !empty($assigned_users)) {
					$assigned_users_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$assigned_users_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					foreach($assigned_users as $assigned_user) {
						$assigned_users_html .= '<li><a href="#" class="dtlms-button filled small"  data-courseid="'.$course_id.'" data-userid="'.$assigned_user.'">'.get_the_author_meta('display_name', $assigned_user).'</a></li>';
					}
					$assigned_users_html .= '</ul></div></div>';
				}

				$package_purchased_users_html = '';
				if(is_array($package_purchased_users) && !empty($package_purchased_users)) {
					$package_purchased_users_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$package_purchased_users_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					foreach($package_purchased_users as $package_purchased_user) {
						$package_purchased_users_html .= '<li><a href="#" class="dtlms-button filled small"  data-courseid="'.$course_id.'" data-userid="'.$package_purchased_user.'">'.get_the_author_meta('display_name', $package_purchased_user).'</a></li>';
					}
					$package_purchased_users_html .= '</ul></div></div>';
				}

				$class_purchased_users_html = '';
				if(is_array($class_purchased_users) && !empty($class_purchased_users)) {
					$class_purchased_users_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$class_purchased_users_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					foreach($class_purchased_users as $class_purchased_user) {
						$class_purchased_users_html .= '<li><a href="#" class="dtlms-button filled small"  data-courseid="'.$course_id.'" data-userid="'.$class_purchased_user.'">'.get_the_author_meta('display_name', $class_purchased_user).'</a></li>';
					}
					$class_purchased_users_html .= '</ul></div></div>';
				}			

				$users_undergoing_html = '';
				if(is_array($users_undergoing) && !empty($users_undergoing)) {
					$users_undergoing_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$users_undergoing_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					foreach($users_undergoing as $user_undergoing) {
						$users_undergoing_html .= '<li>'.get_the_author_meta('display_name', $user_undergoing).'</li>';
					}
					$users_undergoing_html .= '</ul></div></div>';
				}

				$users_underevaluation_html = '';
				if(is_array($users_underevaluation) && !empty($users_underevaluation)) {
					$users_underevaluation_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$users_underevaluation_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					foreach($users_underevaluation as $user_underevaluation) {
						$users_underevaluation_html .= '<li>'.get_the_author_meta('display_name', $user_underevaluation).'</li>';
					}
					$users_underevaluation_html .= '</ul></div></div>';
				}

				$completed_users_html = '';
				if(is_array($completed_users) && !empty($completed_users)) {
					$completed_users_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$completed_users_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					foreach($completed_users as $completed_user) {
						$completed_users_html .= '<li>'.get_the_author_meta('display_name', $completed_user).'</li>';
					}
					$completed_users_html .= '</ul></div></div>';
				}														

				$badge_users_list = $certificate_users_list = '';
				if(is_array($completed_users) && !empty($completed_users)) {										
					foreach($completed_users as $completed_user) {

						$curriculum_details = get_user_meta($completed_user, $course_id, true);
						$completed_course_grade_id = isset($curriculum_details['grade-post-id']) ? $curriculum_details['grade-post-id'] : -1;

			            $badge_achieved = get_post_meta($completed_course_grade_id, 'badge-achieved', true);
			            if($badge_achieved == 'true') {
			            	$badge_users_list .= '<li>'.get_the_author_meta('display_name', $completed_user).'</li>';
			            }

						$certificate_achieved = get_post_meta($completed_course_grade_id, 'certificate-achieved', true);
						if($certificate_achieved == 'true') {
							$certificate_users_list .= '<li>'.get_the_author_meta('display_name', $completed_user).'</li>';
						}

					}		
				}

				$badge_users_html = $certificate_users_html = '';

				if($badge_users_list != '') {
					$badge_users_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$badge_users_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					$badge_users_html .= $badge_users_list;
					$badge_users_html .= '</ul></div></div>';
				}

				if($certificate_users_list != '') {
					$certificate_users_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$certificate_users_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					$certificate_users_html .= $certificate_users_list;
					$certificate_users_html .= '</ul></div></div>';
				}

				$output .= '<tr>
								<td>'.$i.'</td>
								<td>'.$course_title.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count($purchased_users).'</span></p>'.$purchased_users_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count($assigned_users).'</span></p>'.$assigned_users_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count($package_purchased_users).'</span></p>'.$package_purchased_users_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count($class_purchased_users).'</span></p>'.$class_purchased_users_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count($users_undergoing).'</span></p>'.$users_undergoing_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count($users_underevaluation).'</span></p>'.$users_underevaluation_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count($completed_users).'</span></p>'.$completed_users_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$badge_achieved_cnt.'</span></p>'.$badge_users_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$certificate_achieved_cnt.'</span></p>'.$certificate_users_html.'</td>
							</tr>';

				$i++;
			}

		} else {

			$output .= '<tr>
							<td colspan="11">'.esc_html__('No records found!', 'dtlms').'</td>
						</tr>';

		}

		$output .= '</tbody></table>';

	$output .= '</div>';

	wp_reset_postdata();


	// Pagination script Start
	$total_post_args = array ( 
					'posts_per_page' => -1, 
					'post_type'=> 'dtlms_courses'
				);
	if($instructor_id > 0) {
		$total_post_args['author'] = $instructor_id; 	
	}	
	$total_post_courses = get_posts( $total_post_args );
	wp_reset_postdata();

	$courses_post_count = count($total_post_courses);
	$max_num_pages = ceil($courses_post_count / $post_per_page);

	$item_ids['instructor_id'] = $instructor_id;

	$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, $item_ids);
	// Pagination script End


	if($ajax_call) {

		echo $output;
		die();

	} else {

		return $output;

	}

}

add_action( 'wp_ajax_dtlms_load_instructorwise_classes', 'dtlms_load_instructorwise_classes' );
add_action( 'wp_ajax_nopriv_dtlms_load_instructorwise_classes', 'dtlms_load_instructorwise_classes' );
function dtlms_load_instructorwise_classes($dashboard_function_call, $dashboard_output_div, $instructor_dashboard_id = -1) {

	$output = '';

	if($instructor_dashboard_id > 0) {
		$instructor_id = $instructor_dashboard_id;
	} else {
		$instructor_id = isset($_REQUEST['instructor_id']) ? $_REQUEST['instructor_id'] : -1;
	}

	// Pagination script Start
	$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
	$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
	$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

	if($dashboard_function_call != '') {
		$function_call = $dashboard_function_call;
	} else {
		$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : '';
	}

	if($dashboard_output_div != '') {
		$output_div = $dashboard_output_div;
	} else {
		$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : '';
	}
	// Pagination script End

	$class_singular_label = apply_filters( 'class_label', 'singular' );

	$args = array ( 
				'offset' => $offset,
				'paged' => $current_page,
				'posts_per_page' => $post_per_page, 
				'post_type'=> 'dtlms_classes'
			);
	if($instructor_id > 0) {
		$args['author'] = $instructor_id; 	
	}	

	$classes = get_posts( $args );

	$output .= '<div class="dtlms-custom-table-wrapper">';

		$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
						<thead>
							<tr>
								<th>'.esc_html__('#', 'dtlms').'</th>
								<th>'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ).'</th>
								<th>'.esc_html__('Courses Included', 'dtlms').'</th>
								<th>'.esc_html__('# Students', 'dtlms').'</th>
							</tr>
						</thead>
						<tbody class="dtlms-custom-dashboard-table">';	
				
		if(is_array($classes) && !empty($classes)) {

			$i = $offset+1;
			foreach ( $classes as $class ) {
				setup_postdata( $class );

				$class_id = $class->ID;
				$class_title = get_the_title($class_id);
				$author_id = $class->post_author;

				$courses_included = get_post_meta($class_id, 'dtlms-class-courses', true);
				$courses_included_html = '';
				if(is_array($courses_included) && !empty($courses_included)) {
					$courses_included_html .= '<ul>';
					foreach($courses_included as $course_included) {
						$courses_included_html .= '<li><a href="'.get_permalink($course_included).'">'.get_the_title($course_included).'</a></li>';
					}
					$courses_included_html .= '</ul>';
				}			


				$purchased_users = get_post_meta($class_id, 'purchased_users', true);
				$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array ();

				$purchased_users_html = '';
				if(is_array($purchased_users) && !empty($purchased_users)) {
					$purchased_users_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$purchased_users_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					foreach($purchased_users as $purchased_user) {
						$purchased_users_html .= '<li><a href="#" class="dtlms-button filled small" data-classid="'.$class_id.'" data-userid="'.$purchased_user.'">'.get_the_author_meta('display_name', $purchased_user).'</a></li>';
					}
					$purchased_users_html .= '</ul></div></div>';
				}

				$output .= '<tr>
								<td>'.$i.'</td>
								<td>'.$class_title.'</td>
								<td>'.$courses_included_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count($purchased_users).'</span></p>'.$purchased_users_html.'</td>
							</tr>';

				$i++;
			}

		} else {

			$output .= '<tr>
							<td colspan="4">'.esc_html__('No records found!', 'dtlms').'</td>
						</tr>';

		}

		$output .= '</tbody></table>';

	$output .= '</div>';

	wp_reset_postdata();


	// Pagination script Start
	$total_post_args = array ( 
					'posts_per_page' => -1, 
					'post_type'=> 'dtlms_classes'
				);
	if($instructor_id > 0) {
		$total_post_args['author'] = $instructor_id; 	
	}	
	$total_post_classes = get_posts( $total_post_args );
	wp_reset_postdata();

	$classes_post_count = count($total_post_classes);
	$max_num_pages = ceil($classes_post_count / $post_per_page);

	$item_ids['instructor_id'] = $instructor_id;

	$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, $item_ids);
	// Pagination script End


	if($ajax_call) {

		echo $output;
		die();

	} else {

		return $output;

	}

}

add_action( 'wp_ajax_dtlms_load_instructorwise_commissions', 'dtlms_load_instructorwise_commissions' );
add_action( 'wp_ajax_nopriv_dtlms_load_instructorwise_commissions', 'dtlms_load_instructorwise_commissions' );
function dtlms_load_instructorwise_commissions($dashboard_function_call, $dashboard_output_div, $commissioncontent = 'course', $instructor_dashboard_id = -1) {

	$output = '';

	if($instructor_dashboard_id > 0) {
		$instructor_id = $instructor_dashboard_id;
	} else {
		$instructor_id = isset($_REQUEST['instructor_id']) ? $_REQUEST['instructor_id'] : -1;
	}

	// Pagination script Start
	$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
	$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
	$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

	if($dashboard_function_call != '') {
		$function_call = $dashboard_function_call;
	} else {
		$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : 'dtlms_load_instructorwise_commissions';
	}

	if($dashboard_output_div != '') {
		$output_div = $dashboard_output_div;
	} else {
		$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : 'dtlms-instructor-commissions-container';
	}

	$commissioncontent = isset($_REQUEST['commission_content']) ? $_REQUEST['commission_content'] : $commissioncontent;

	// Pagination script End	

	if($instructor_id > 0) {

		if(!($instructor_dashboard_id > 0)) {
			$output .= '<p>'.sprintf(esc_html__('Commission details of %s', 'dtlms'), '<strong>'.get_the_author_meta('display_name', $instructor_id).'</strong>').'</p>';
		}

		// Courses

		if($commissioncontent == 'course') {

			$courses_subscribed = get_user_meta($instructor_id, 'courses-subscribed', true);

			$pay_commission_courses = array ();
			foreach($courses_subscribed as $courses_subscribed_key => $course_subscribed) {
				foreach($course_subscribed as $course_key => $course_datas) {
					foreach($course_datas['users'] as $course_data_user) {
						$pay_commission_courses[$course_key][$courses_subscribed_key]['users'][] = $course_data_user;
					}
					$status = (isset($course_datas['status']) && $course_datas['status'] != '') ? $course_datas['status'] : '';
					$commission = (isset($course_datas['commission']) && $course_datas['commission'] != '') ? $course_datas['commission'] : '';
					$pay_commission_courses[$course_key][$courses_subscribed_key]['status'] = $status;
					$pay_commission_courses[$course_key][$courses_subscribed_key]['commission'] = $commission;	
				}
			}

			if(is_array($pay_commission_courses) && !empty($pay_commission_courses)) {

				$commission_settings = get_option('dtlms-commission-settings');

				$output .= '<div class="dtlms-custom-table-wrapper">';

					$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
									<thead>
										<tr>
											<th scope="col">'.esc_html__('#', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Course', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Total Subscriptions', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Paid Subscriptions', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Unpaid Subscriptions', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Commision Percentage ( % )', 'dtlms').'</th>
											<th scope="col">'.sprintf(esc_html__('Commission Amount Paid (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</th>
											<th scope="col">'.sprintf(esc_html__('Commission Amount Unpaid (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</th>
										</tr>
									</thead>
									<tbody class="dtlms-custom-dashboard-table">';

									$i = $offset+1;
									
									$pay_commission_courses_filtered = array_slice($pay_commission_courses, $offset, $post_per_page, true);
									foreach($pay_commission_courses_filtered as $pay_commission_course_key => $pay_commission_course) {

										$course_id = $pay_commission_course_key;
										$course_title = get_the_title($course_id);

										if($course_title != '') {

											$product = dtlms_get_product_object($course_id);

											$woo_price_html = dtlms_get_item_price_html($product);

											if($product->get_sale_price() != '') {
												$woo_price = $product->get_sale_price();
											} else {
												$woo_price = $product->get_regular_price();
											}

											$commission_percentage = (isset($commission_settings[$instructor_id][$course_id]) && $commission_settings[$instructor_id][$course_id] > 0) ? $commission_settings[$instructor_id][$course_id] : '';


											$subscription_detail_html = '';
											$total_subscriptions = $total_subscriptions_paid = $total_subscriptions_unpaid = 0;
											$commissions_paid = $commissions_unpaid = 0;
											foreach($pay_commission_course as $subscription_detail_key => $subscription_detail) {

												$subscription_detail_html .= '<div class="dtlms-subscriber-details">';
													$subscription_detail_html .= '<label>'.date(get_option('date_format'), $subscription_detail_key).'</label>';
													$subscription_detail_html .= '<ul>';
													if(isset($subscription_detail['users'])) {
														foreach($subscription_detail['users'] as $user_id) {
															$subscription_detail_html .= '<li>'.get_the_author_meta('display_name', $user_id).'</li>';
														}
													}
													$subscription_detail_html .= '</ul>';
												$subscription_detail_html .= '</div>';

												$users_count = isset($subscription_detail['users']) ? count($subscription_detail['users']) : 0;

												$total_subscriptions = $total_subscriptions + $users_count;

												if($subscription_detail['status'] == 'paid') {
													$total_subscriptions_paid = $total_subscriptions_paid + $users_count;
													$commissions_paid = $commissions_paid + ($woo_price * $users_count * $subscription_detail['commission'])/100;
												}
												if($subscription_detail['status'] == 'unpaid') {
													$total_subscriptions_unpaid = $total_subscriptions_unpaid + $users_count;
													$commissions_unpaid = $commissions_unpaid + ($woo_price * $users_count * $commission_percentage)/100;
												}

											}

											$output .= '<tr>
															<td>'.$i.'</td>
															<td>'.get_the_title($course_id).'</td>
															<td>'.$woo_price_html.'</td>
															<td><p class="dtlms-total_subscriptions"><span>'.$total_subscriptions.'</span></p>';

															$output .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
															$output .= '<div class="dtlms-subscription-detail-holder">'.$subscription_detail_html.'</div></div>';

												$output .= '</td>
															<td><p class="dtlms-total_subscriptions"><span>'.$total_subscriptions_paid.'</span></p>
															<td><p class="dtlms-total_subscriptions"><span>'.$total_subscriptions_unpaid.'</span></p>
															<td>'.$commission_percentage.'</td>
															<td>'.$commissions_paid.'</td>
															<td>'.$commissions_unpaid.'</td>'; 
											$output .= '</tr>';

											$i++;

										}

									}

					$output .= '</tbody></table>';

				$output .= '</div>';


				// Pagination script Start
				$pay_commission_courses_count = count($pay_commission_courses);
				$max_num_pages = ceil($pay_commission_courses_count / $post_per_page);

				$item_ids['instructor_id'] = $instructor_id;
				$item_ids['commission_content'] = $commissioncontent;

				$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, $item_ids);
				// Pagination script End


			} else {
				$output .= esc_html__('No records found!', 'dtlms');
			}

		}

		// Classes

		if($commissioncontent == 'class') {

			$courses_subscribed = get_user_meta($instructor_id, 'classes-subscribed', true);

			$pay_commission_classes = array ();
			foreach($courses_subscribed as $classes_subscribed_key => $class_subscribed) {
				foreach($class_subscribed as $class_key => $class_datas) {
					foreach($class_datas['users'] as $class_data_user) {
						$pay_commission_classes[$class_key][$classes_subscribed_key]['users'][] = $class_data_user;
					}
					if(isset($class_datas['status'])) {
						$pay_commission_classes[$class_key][$classes_subscribed_key]['status'] = $class_datas['status'];
					}
					if(isset($class_datas['commission'])) {
						$pay_commission_classes[$class_key][$classes_subscribed_key]['commission'] = $class_datas['commission'];	
					}
				}
			}

			if(is_array($pay_commission_classes) && !empty($pay_commission_classes)) {

				$class_singular_label = apply_filters( 'class_label', 'singular' );

				$commission_settings = get_option('dtlms-commission-settings');

				$output .= '<div class="dtlms-custom-table-wrapper">';

					$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
									<thead>
										<tr>
											<th scope="col">'.esc_html__('#', 'dtlms').'</th>
											<th scope="col">'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ).'</th>
											<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Total Subscriptions', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Paid Subscriptions', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Unpaid Subscriptions', 'dtlms').'</th>
											<th scope="col">'.esc_html__('Commision Percentage ( % )', 'dtlms').'</th>
											<th scope="col">'.sprintf(esc_html__('Commission Amount Paid (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</th>
											<th scope="col">'.sprintf(esc_html__('Commission Amount Unpaid (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</th>
										</tr>
									</thead>
									<tbody class="dtlms-custom-dashboard-table">';

									$i = $offset+1;
									
									$pay_commission_classes_filtered = array_slice($pay_commission_classes, $offset, $post_per_page, true);
									foreach($pay_commission_classes_filtered as $pay_commission_class_key => $pay_commission_class) {

										$class_id = $pay_commission_class_key;

										$class_title = get_the_title($class_id);

										if($class_title != '') {

											$product = dtlms_get_product_object($class_id);
											$woo_price_html = dtlms_get_item_price_html($product);

											if($product->get_sale_price() != '') {
												$woo_price = $product->get_sale_price();
											} else {
												$woo_price = $product->get_regular_price();
											}

											$commission_percentage = (isset($commission_settings[$instructor_id][$class_id]) && $commission_settings[$instructor_id][$class_id] > 0) ? $commission_settings[$instructor_id][$class_id] : '';


											$subscription_detail_html = '';
											$total_subscriptions = $total_subscriptions_paid = $total_subscriptions_unpaid = 0;
											$commissions_paid = $commissions_unpaid = 0;
											foreach($pay_commission_class as $subscription_detail_key => $subscription_detail) {

												if(!isset($subscription_detail['users'])) {
													$subscription_detail['users'] = array ();
												}

												$subscription_detail_html .= '<div class="dtlms-subscriber-details">';
													$subscription_detail_html .= '<label>'.date(get_option('date_format'), $subscription_detail_key).'</label>';
													$subscription_detail_html .= '<ul>';
													if(isset($subscription_detail['users']) && !empty($subscription_detail['users'])) {
														foreach($subscription_detail['users'] as $user_id) {
															$subscription_detail_html .= '<li>'.get_the_author_meta('display_name', $user_id).'</li>';
														}
													}
													$subscription_detail_html .= '</ul>';
												$subscription_detail_html .= '</div>';

												$total_subscriptions = $total_subscriptions + count($subscription_detail['users']);

												if($subscription_detail['status'] == 'paid') {
													$total_subscriptions_paid = $total_subscriptions_paid + count($subscription_detail['users']);
													$commissions_paid = $commissions_paid + ($woo_price * count($subscription_detail['users']) * $subscription_detail['commission'])/100;
												}
												if($subscription_detail['status'] == 'unpaid') {
													$total_subscriptions_unpaid = $total_subscriptions_unpaid + count($subscription_detail['users']);
													$commissions_unpaid = $commissions_unpaid + ($woo_price *count($subscription_detail['users']) * $commission_percentage)/100;
												}

											}

											$output .= '<tr>
															<td>'.$i.'</td>
															<td>'.get_the_title($class_id).'</td>
															<td>'.$woo_price_html.'</td>
															<td><p class="dtlms-total_subscriptions"><span>'.$total_subscriptions.'</span></p>';

															$output .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
															$output .= '<div class="dtlms-subscription-detail-holder">'.$subscription_detail_html.'</div></div>';

												$output .= '</td>
															<td><p class="dtlms-total_subscriptions"><span>'.$total_subscriptions_paid.'</span></p>
															<td><p class="dtlms-total_subscriptions"><span>'.$total_subscriptions_unpaid.'</span></p>
															<td>'.$commission_percentage.'</td>
															<td>'.$commissions_paid.'</td>
															<td>'.$commissions_unpaid.'</td>'; 
											$output .= '</tr>';

											$i++;

										}

									}

					$output .= '</tbody></table>';

				$output .= '</div>';


				// Pagination script Start
				$pay_commission_classes_count = count($pay_commission_classes);
				$max_num_pages = ceil($pay_commission_classes_count / $post_per_page);

				$item_ids['instructor_id'] = $instructor_id;
				$item_ids['commission_content'] = $commissioncontent;

				$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, $item_ids);
				// Pagination script End


			} else {
				$output .= esc_html__('No records found!', 'dtlms');
			}	

		}	

	}

	if($ajax_call) {

		echo $output;
		die();

	} else {

		return $output;

	}

}

add_action( 'wp_ajax_dtlms_load_statistics_instructor_content', 'dtlms_load_statistics_instructor_content' );
add_action( 'wp_ajax_nopriv_dtlms_load_statistics_instructor_content', 'dtlms_load_statistics_instructor_content' );
function dtlms_load_statistics_instructor_content() {

	$output = '';

	// Pagination script Start
	$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
	$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
	$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

	$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : 'dtlms_load_statistics_instructor_content';
	$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : 'dtlms-instructor-statistics-container';
	// Pagination script End


	$instructor_label = apply_filters( 'instructor_label', 'singular' );

	$output .= '<div class="dtlms-custom-table-wrapper">';

		$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
						<thead>
							<tr>
								<th scope="col">'.esc_html__('#', 'dtlms').'</th>
								<th scope="col">'.sprintf(esc_html__('%s', 'dtlms'), $instructor_label).'</th>
								<th scope="col">'.esc_html__('# Courses', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Classes', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Packages', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Students', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Evaluated', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Under Evaluation', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Badges', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Certificates', 'dtlms').'</th>
							</tr>
						</thead>
						<tbody class="dtlms-custom-dashboard-table">';

		$instructors = get_users ( 
							array (
								'offset' => $offset,
								'paged' => $current_page,
								'number' => $post_per_page, 							
								'role' => 'instructor', 
							) 
						);				
		if(is_array($instructors) && !empty($instructors)) {

			$i = $offset+1;
			foreach ( $instructors as $instructor ) {

				$instructor_id = $instructor->data->ID;

				// Courses
				$purchased_users_cnt = 0;
				$instructor_courses_html = '';
				$courses_args = array (
									'post_type'        => 'dtlms_courses',
									'author'	   	   => $instructor_id,
									'post_status'      => 'publish'
								);

				$courses = get_posts( $courses_args );
				if(is_array($courses) && !empty($courses)) {
					$instructor_courses_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$instructor_courses_html .= '<div class="dtlms-subscription-detail-holder"><ul>';			
					foreach ( $courses as $course ) {
						setup_postdata( $course ); 
						$course_id = $course->ID;

						$purchased_users = get_post_meta($course_id, 'purchased_users', true);
						if(is_array($purchased_users) && !empty($purchased_users)) {
							$purchased_users_cnt = $purchased_users_cnt + count($purchased_users);
						}

						$instructor_courses_html .= '<li>'.get_the_title($course_id).'</li>';
					}
					$instructor_courses_html .= '</ul></div></div>';
				}
				wp_reset_postdata();	

				// Classes
				$instructor_classes_html = '';
				$classes_args = array (
									'post_type'        => 'dtlms_classes',
									'author'	   	   => $instructor_id,
									'post_status'      => 'publish'
								);

				$classes = get_posts( $classes_args );
				if(is_array($classes) && !empty($classes)) {
					$instructor_classes_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$instructor_classes_html .= '<div class="dtlms-subscription-detail-holder"><ul>';					
					foreach ( $classes as $class ) {
						setup_postdata( $class ); 
						$class_id = $class->ID;
						$instructor_classes_html .= '<li>'.get_the_title($class_id).'</li>';
					}
					$instructor_classes_html .= '</ul></div></div>';
				}
				wp_reset_postdata();

				// Packages
				$instructor_packages_html = '';
				$packages_args = array (
									'post_type'        => 'dtlms_packages',
									'author'	   	   => $instructor_id,
									'post_status'      => 'publish'
								);

				$packages = get_posts( $packages_args );
				if(is_array($packages) && !empty($packages)) {
					$instructor_packages_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$instructor_packages_html .= '<div class="dtlms-subscription-detail-holder"><ul>';				
					foreach ( $packages as $package ) {
						setup_postdata( $package ); 
						$package_id = $package->ID;
						$instructor_packages_html .= '<li>'.get_the_title($package_id).'</li>';
					}
					$instructor_packages_html .= '</ul></div></div>';
				}
				wp_reset_postdata();


				$badges_args = array (
								'meta_key'         => 'badge-achieved',
								'meta_value'       => 'true',
								'post_type'        => 'dtlms_gradings',
								'author'	   	   => $instructor_id,
								'post_status'      => 'publish',
							);
				$badges = new WP_Query( $badges_args );
				$badges_count = $badges->found_posts;
				wp_reset_postdata();

				$certificates_args = array (
										'meta_key'         => 'certificate-achieved',
										'meta_value'       => 'true',
										'post_type'        => 'dtlms_gradings',
										'author'	   	   => $instructor_id,
										'post_status'      => 'publish',
									);
				$certificates = new WP_Query( $certificates_args );
				$certificates_count = $certificates->found_posts; 
				wp_reset_postdata();	


				$gradings_graded_args = array ( 
									'author' => $instructor_id, 
									'post_type' => 'dtlms_gradings',
									'meta_query'=>array(), 
								);	
				$gradings_graded_args['meta_query'][] = array (
											'key'     => 'grade-type',
											'value'   => 'course',
											'compare' => '=='
										);																
				$gradings_graded_args['meta_query'][] = array (
											'key'     => 'graded',
											'value'   => 'true',
											'compare' => '=='
										);
				$gradings_graded = new WP_Query( $gradings_graded_args );
				$gradings_graded_count = $gradings_graded->found_posts; 
				wp_reset_postdata();

				$under_gradings_args = array ( 
									'author' => $instructor_id, 
									'post_type' => 'dtlms_gradings',
									'meta_query'=>array(), 
								);	
				$under_gradings_args['meta_query'][] = array (
											'key'     => 'grade-type',
											'value'   => 'course',
											'compare' => '=='
										);																	
				$under_gradings_args['meta_query'][] = array (
											'key'     => 'graded',
											'compare' => 'NOT EXISTS'
										);
				$under_gradings_args['meta_query'][] = array (
											'key'     => 'submitted',
											'value'   => '1',
											'compare' => '=='
										);					
				$under_gradings = new WP_Query( $under_gradings_args );
				$under_gradings_count = $under_gradings->found_posts; 
				wp_reset_postdata();									

				$output .= '<tr>
								<td>'.$i.'</td>
								<td>'.get_the_author_meta('display_name', $instructor_id).'</td>
								<td><p class="dtlms-statistics-count"><span>'.count_user_posts( $instructor_id , 'dtlms_courses' ).'</span></p>'.$instructor_courses_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count_user_posts( $instructor_id , 'dtlms_classes' ).'</span></p>'.$instructor_classes_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count_user_posts( $instructor_id , 'dtlms_packages' ).'</span></p>'.$instructor_packages_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$purchased_users_cnt.'</span></p></td>
								<td><p class="dtlms-statistics-count"><span>'.$gradings_graded_count.'</span></p></td>
								<td><p class="dtlms-statistics-count"><span>'.$under_gradings_count.'</span></p></td>
								<td><p class="dtlms-statistics-count"><span>'.$badges_count.'</span></p></td>
								<td><p class="dtlms-statistics-count"><span>'.$certificates_count.'</span></p></td>
							</tr>';

				$i++;
			}

		} else {

			$output .= '<tr>
							<td colspan="11" class="section">'.esc_html__('No Records Found!', 'dtlms').'</td>
						</tr>';

		}

		$output .= '</tbody></table>';

	$output .= '</div>';


	// Pagination script Start
	$total_users_args = array (							
							'role' => 'instructor', 
						);
	$total_users = get_users( $total_users_args );

	$total_users_count = count($total_users);
	$max_num_pages = ceil($total_users_count / $post_per_page);

	$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, array ());
	// Pagination script End


	if($ajax_call) {

		echo $output;
		die();

	} else {

		return $output;

	}	

}

add_action( 'wp_ajax_dtlms_load_statistics_students_content', 'dtlms_load_statistics_students_content' );
add_action( 'wp_ajax_nopriv_dtlms_load_statistics_students_content', 'dtlms_load_statistics_students_content' );
function dtlms_load_statistics_students_content() {
	
	$output = '';

	// Pagination script Start
	$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
	$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
	$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

	$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : 'dtlms_load_statistics_students_content';
	$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : 'dtlms-students-statistics-container';
	// Pagination script End

	$class_plural_label = apply_filters( 'class_label', 'plural' );


	$output .= '<div class="dtlms-custom-table-wrapper">';

		$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
						<thead>
							<tr>
								<th scope="col">'.esc_html__('#', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Student', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Courses Purchased', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Courses Assigned', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Packages Purchased', 'dtlms').'</th>
								<th scope="col">'.sprintf( esc_html__( '%1$s Purchased', 'dtlms' ), $class_plural_label ).'</th>
								<th scope="col">'.esc_html__('# Courses - Under Progress', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Courses - Under Evaluation', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Courses - Completed', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Courses - Badges', 'dtlms').'</th>
								<th scope="col">'.esc_html__('# Courses - Certificates', 'dtlms').'</th>
							</tr>
						</thead>
						<tbody class="dtlms-custom-dashboard-table">';

		$students = get_users ( 
							array (
								'offset' => $offset,
								'paged' => $current_page,
								'number' => $post_per_page, 							
								'role' => 'student', 
							) 
						);	
		if(is_array($students) && !empty($students)) {

			$i = $offset+1;
			
			foreach ( $students as $student ) {
				setup_postdata( $student );

					$student_id = $student->data->ID;

					$purchased_courses = get_user_meta($student_id, 'purchased_courses', true);
					if(is_array($purchased_courses) && !empty($purchased_courses)) {
						$purchased_courses_cnt = count($purchased_courses);						
					} else {
						$purchased_courses = array ();
						$purchased_courses_cnt = 0;
					}

					$assigned_courses = get_user_meta($student_id, 'assigned_courses', true);
					if(is_array($assigned_courses) && !empty($assigned_courses)) {
						$assigned_courses_cnt = count($assigned_courses);						
					} else {
						$assigned_courses = array ();
						$assigned_courses_cnt = 0;
					}				

					$purchased_packages = get_user_meta($student_id, 'purchased_packages', true);
					if(is_array($purchased_packages) && !empty($purchased_packages)) {
						$purchased_packages = array_keys($purchased_packages);
						$purchased_packages_cnt = count($purchased_packages);						
					} else {
						$purchased_packages = array ();
						$purchased_packages_cnt = 0;
					}	

					$purchased_classes = get_user_meta($student_id, 'purchased_classes', true);
					if(is_array($purchased_classes) && !empty($purchased_classes)) {
						$purchased_classes_cnt = count($purchased_classes);						
					} else {
						$purchased_classes = array ();
						$purchased_classes_cnt = 0;
					}									

					$started_courses = get_user_meta($student_id, 'started_courses', true);
					if(is_array($started_courses) && !empty($started_courses)) {
						$started_courses_cnt = count($started_courses);
					} else {
						$started_courses = array ();
						$started_courses_cnt = 0;							
					}

					$submitted_courses = get_user_meta($student_id, 'submitted_courses', true);
					if(is_array($submitted_courses) && !empty($submitted_courses)) {
						$submitted_courses_cnt = count($submitted_courses);
					} else {
						$submitted_courses = array ();
						$submitted_courses_cnt = 0;						
					}

					$badge_courses_list = $certificate_courses_list = '';
					$completed_courses = get_user_meta($student_id, 'completed_courses', true);
					if(is_array($completed_courses) && !empty($completed_courses)) {
						$completed_courses_cnt = count($completed_courses);
						$badge_achieved_cnt = $certificate_achieved_cnt = 0;
												
						foreach($completed_courses as $completed_course) {

							$curriculum_details = get_user_meta($student_id, $completed_course, true);
							$completed_course_grade_id = isset($curriculum_details['grade-post-id']) ? $curriculum_details['grade-post-id'] : -1;

				            $badge_achieved = get_post_meta($completed_course_grade_id, 'badge-achieved', true);
				            if($badge_achieved == 'true') {
				            	$badge_achieved_cnt++;
				            	$badge_courses_list .= '<li>'.get_the_title($completed_course).'</li>';
				            }

							$certificate_achieved = get_post_meta($completed_course_grade_id, 'certificate-achieved', true);
							if($certificate_achieved == 'true') {
								$certificate_achieved_cnt++;
								$certificate_courses_list .= '<li>'.get_the_title($completed_course).'</li>';
							}

						}	

					} else {
						$completed_courses = array ();	
						$completed_courses_cnt = $badge_achieved_cnt = $certificate_achieved_cnt = 0;							
					}

					$badge_courses_html = $certificate_courses_html = '';

					if($badge_courses_list != '') {
						$badge_courses_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
						$badge_courses_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
						$badge_courses_html .= $badge_courses_list;
						$badge_courses_html .= '</ul></div></div>';	
					}

					if($certificate_courses_list != '') {
						$certificate_courses_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
						$certificate_courses_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
						$certificate_courses_html .= $certificate_courses_list;
						$certificate_courses_html .= '</ul></div></div>';	
					}

					$courses_undergoing_cnt = $courses_underevaluation_cnt = 0;
					$courses_undergoing = array_diff($started_courses, $submitted_courses);
					if(is_array($courses_undergoing) && !empty($courses_undergoing)) {
						$courses_undergoing_cnt = count($courses_undergoing);
					}
					$courses_underevaluation = array_diff($submitted_courses, $completed_courses);
					if(is_array($courses_underevaluation) && !empty($courses_underevaluation)) {
						$courses_underevaluation_cnt = count($courses_underevaluation);
					}

					$purchased_courses_html = '';
					if(is_array($purchased_courses) && !empty($purchased_courses)) {
						$purchased_courses_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
						$purchased_courses_html .= '<div class="dtlms-subscription-detail-holder"><ul>';		
						foreach($purchased_courses as $purchased_course) {
							$purchased_courses_html .= '<li><a href="#" class="dtlms-button filled small"  data-courseid="'.$purchased_course.'" data-userid="'.$student_id.'">'.get_the_title($purchased_course).'</a></li>';
						}
						$purchased_courses_html .= '</ul></div></div>';
					}

					$assigned_courses_html = '';
					if(is_array($assigned_courses) && !empty($assigned_courses)) {
						$assigned_courses_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
						$assigned_courses_html .= '<div class="dtlms-subscription-detail-holder"><ul>';		
						foreach($assigned_courses as $assigned_course) {
							$assigned_courses_html .= '<li><a href="#" class="dtlms-button filled small"  data-courseid="'.$assigned_course.'" data-userid="'.$student_id.'">'.get_the_title($assigned_course).'</a></li>';
						}
						$assigned_courses_html .= '</ul></div></div>';
					}				

					$purchased_packages_html = '';
					if(is_array($purchased_packages) && !empty($purchased_packages)) {
						$purchased_packages_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
						$purchased_packages_html .= '<div class="dtlms-subscription-detail-holder"><ul>';		
						foreach($purchased_packages as $purchased_package) {
							$purchased_packages_html .= '<li>'.get_the_title($purchased_package).'</li>';
						}
						$purchased_packages_html .= '</ul></div></div>';
					}

					$purchased_classes_html = '';
					if(is_array($purchased_classes) && !empty($purchased_classes)) {
						$purchased_classes_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
						$purchased_classes_html .= '<div class="dtlms-subscription-detail-holder"><ul>';		
						foreach($purchased_classes as $purchased_class) {
							$purchased_classes_html .= '<li><a href="#" class="dtlms-button filled small" data-classid="'.$purchased_class.'" data-userid="'.$student_id.'">'.get_the_title($purchased_class).'</a></li>';
						}
						$purchased_classes_html .= '</ul></div></div>';
					}										

					$courses_undergoing_html = '';
					if(is_array($courses_undergoing) && !empty($courses_undergoing)) {
						$courses_undergoing_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
						$courses_undergoing_html .= '<div class="dtlms-subscription-detail-holder"><ul>';		
						foreach($courses_undergoing as $course_undergoing) {
							$courses_undergoing_html .= '<li>'.get_the_title($course_undergoing).'</li>';
						}
						$courses_undergoing_html .= '</ul></div></div>';
					}

					$courses_underevaluation_html = '';
					if(is_array($courses_underevaluation) && !empty($courses_underevaluation)) {
						$courses_underevaluation_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
						$courses_underevaluation_html .= '<div class="dtlms-subscription-detail-holder"><ul>';		
						foreach($courses_underevaluation as $course_underevaluation) {
							$courses_underevaluation_html .= '<li>'.get_the_title($course_underevaluation).'</li>';
						}
						$courses_underevaluation_html .= '</ul></div></div>';
					}

					$completed_courses_html = '';
					if(is_array($completed_courses) && !empty($completed_courses)) {
						$completed_courses_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
						$completed_courses_html .= '<div class="dtlms-subscription-detail-holder"><ul>';		
						foreach($completed_courses as $completed_course) {
							$completed_courses_html .= '<li>'.get_the_title($completed_course).'</li>';
						}
						$completed_courses_html .= '</ul></div></div>';
					}														


				$output .= '<tr>
								<td>'.$i.'</td>
								<td>'.get_the_author_meta('display_name', $student_id).'</td>
								<td><p class="dtlms-statistics-count"><span>'.$purchased_courses_cnt.'</span></p>'.$purchased_courses_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$assigned_courses_cnt.'</span></p>'.$assigned_courses_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$purchased_packages_cnt.'</span></p>'.$purchased_packages_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$purchased_classes_cnt.'</span></p>'.$purchased_classes_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$courses_undergoing_cnt.'</span></p>'.$courses_undergoing_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$courses_underevaluation_cnt.'</span></p>'.$courses_underevaluation_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$completed_courses_cnt.'</span></p>'.$completed_courses_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$badge_achieved_cnt.'</span></p>'.$badge_courses_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.$certificate_achieved_cnt.'</span></p>'.$certificate_courses_html.'</td>
							</tr>';

				$i++;
			}

		} else {

			$output .= '<tr>
							<td colspan="9" class="section">'.esc_html__( 'No records found!', 'dtlms' ).'</td>
						</tr>';

		}

		$output .= '</tbody></table>';

	$output .= '</div>';

	wp_reset_postdata();


	// Pagination script Start
	$total_users_args = array (							
							'role' => 'student', 
						);
	$total_users = get_users( $total_users_args );

	$total_users_count = count($total_users);
	$max_num_pages = ceil($total_users_count / $post_per_page);

	$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, array ());
	// Pagination script End


	if($ajax_call) {

		echo $output;
		die();

	} else {

		return $output;

	}

}


function dtlms_get_course_package_purchased_users($course_id) {

	$purchased_users_list = array ();

	$packages_list = dtlms_get_course_packages($course_id);
	if(is_array($packages_list) && !empty($packages_list)) {
		foreach ($packages_list as $package) {
			$purchased_users = get_post_meta($package, 'purchased_users', true);
			$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array ();
			$purchased_user_keys = array_keys($purchased_users);
			$purchased_users_list = array_merge_recursive($purchased_users_list, $purchased_user_keys);			
		}
	}

	return array_unique($purchased_users_list);

}

function dtlms_get_course_class_purchased_users($course_id) {

	$purchased_users_list = array ();

	$classes_list = dtlms_get_course_classes_lists($course_id);

	if(is_array($classes_list) && !empty($classes_list)) {
		foreach ($classes_list as $class) {
			$purchased_users = get_post_meta($class, 'purchased_users', true);
			$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array ();
			$purchased_users_list = array_merge_recursive($purchased_users_list, $purchased_users);			
		}
	}
	
	return array_unique($purchased_users_list);

}

function dtlms_get_course_assigned_users($course_id) {

	$assigned_users_list = array ();

	$assigned_users = get_post_meta($course_id, 'assigned_users', true);
	$assigned_users_list = (is_array($assigned_users) && !empty($assigned_users)) ? $assigned_users : array ();

	return array_unique($assigned_users_list);

}

add_action( 'wp_ajax_dtlms_load_statistics_packages_content', 'dtlms_load_statistics_packages_content' );
add_action( 'wp_ajax_nopriv_dtlms_load_statistics_packages_content', 'dtlms_load_statistics_packages_content' );
function dtlms_load_statistics_packages_content() {

	$output = '';

	// Pagination script Start
	$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
	$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
	$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

	$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : 'dtlms_load_statistics_packages_content';
	$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : 'dtlms-package-statistics-container';
	// Pagination script End

	$class_plural_label = apply_filters( 'class_label', 'plural' );

	$args = array ( 
				'offset' => $offset,
				'paged' => $current_page,
				'posts_per_page' => $post_per_page, 
				'post_type'=> 'dtlms_packages'
			);

	$packages = get_posts( $args );

	$output .= '<div class="dtlms-custom-table-wrapper">';

		$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
						<thead>
							<tr>
								<th>'.esc_html__('#', 'dtlms').'</th>
								<th>'.esc_html__('Package', 'dtlms').'</th>
								<th>'.esc_html__('Courses Included', 'dtlms').'</th>
								<th>'.sprintf( esc_html__( '%1$s Included', 'dtlms' ), $class_plural_label ).'</th>
								<th>'.esc_html__('# Students', 'dtlms').'</th>
							</tr>
						</thead>
						<tbody class="dtlms-custom-dashboard-table">';

		if(is_array($packages) && !empty($packages)) {

			$i = $offset+1;
			foreach ( $packages as $package ) {
				setup_postdata( $package );

				$package_id = $package->ID;
				$package_title = get_the_title($package_id);
				$author_id = $package->post_author;

				$courses_included = get_post_meta($package_id, 'courses-included', true);
				$courses_included_html = '';
				if(is_array($courses_included) && !empty($courses_included)) {
					$courses_included_html .= '<ul>';
					foreach($courses_included as $course_included) {
						$courses_included_html .= '<li><a href="'.get_permalink($course_included).'">'.get_the_title($course_included).'</a></li>';
					}
					$courses_included_html .= '</ul>';
				}

				$classes_included = get_post_meta($package_id, 'classes-included', true);
				$classes_included_html = '';
				if(is_array($classes_included) && !empty($classes_included)) {
					$classes_included_html .= '<ul>';
					foreach($classes_included as $classe_included) {
						$classes_included_html .= '<li><a href="'.get_permalink($classe_included).'">'.get_the_title($classe_included).'</a></li>';
					}
					$classes_included_html .= '</ul>';
				}				


				$purchased_users = get_post_meta($package_id, 'purchased_users', true);
				$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array ();

				$purchased_users_html = '';
				if(is_array($purchased_users) && !empty($purchased_users)) {
					$purchased_users_html .= '<div class="dtlms-subscriber-tooltip"><i class="fa fa-eye"></i>';
					$purchased_users_html .= '<div class="dtlms-subscription-detail-holder"><ul>';
					foreach($purchased_users as $purchased_user_key => $purchased_user) {
						$purchased_users_html .= '<li>'.get_the_author_meta('display_name', $purchased_user_key).'</li>';
					}
					$purchased_users_html .= '</ul></div></div>';
				}

				$output .= '<tr>
								<td>'.$i.'</td>
								<td>'.$package_title.'</td>
								<td>'.$courses_included_html.'</td>
								<td>'.$classes_included_html.'</td>
								<td><p class="dtlms-statistics-count"><span>'.count($purchased_users).'</span></p>'.$purchased_users_html.'</td>
							</tr>';

				$i++;
			}

		} else {

			$output .= '<tr>
							<td colspan="4">'.esc_html__('No records found!', 'dtlms').'</td>
						</tr>';

		}

		$output .= '</tbody></table>';

	$output .= '</div>';

	wp_reset_postdata();


	// Pagination script Start
	$total_post_args = array ( 
					'posts_per_page' => -1, 
					'post_type'=> 'dtlms_packages'
				);
	$total_post_packages = get_posts( $total_post_args );
	wp_reset_postdata();

	$packages_post_count = count($total_post_packages);
	$max_num_pages = ceil($packages_post_count / $post_per_page);

	$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, array ());
	// Pagination script End


	if($ajax_call) {

		echo $output;
		die();

	} else {

		return $output;

	}

}

?>