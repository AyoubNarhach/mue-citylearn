<?php
global $post;
$post_id = $post->ID;

$instructor_id = get_post_meta($post_id, 'instructor-id', true);
$startdate = get_post_meta($post_id, 'startdate', true);
$enddate = get_post_meta($post_id, 'enddate', true);
$instructor_paypal_email = get_post_meta($post_id, 'instructor-paypal-email', true);
$payment_datas = get_post_meta($post_id, 'payment-datas', true);
$commission_paid_courses = get_post_meta($post_id, 'commission-paid-courses', true);
$commission_paid_classes = get_post_meta($post_id, 'commission-paid-classes', true);
$other_amounts = get_post_meta($post_id, 'other-amounts', true);
$total_commission_paid = get_post_meta($post_id, 'total-commission-paid', true);

$instructor_label = apply_filters( 'instructor_label', 'singular' );
?>

<div class="dtlms-custom-box">
    <div class="dtlms-column dtlms-one-sixth first">
        <strong><?php echo $instructor_label; ?></strong>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php
		$instructor_info = get_userdata($instructor_id);
		$instructor_name = $instructor_info->display_name;
		echo $instructor_name;
        ?>
    </div>
</div>

<div class="dtlms-custom-box">
    <div class="dtlms-column dtlms-one-sixth first">
        <strong><?php echo sprintf(esc_html__('%s PayPal Email', 'dtlms'), $instructor_label); ?></strong>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php echo $instructor_paypal_email; ?>
    </div>
</div>

<div class="dtlms-custom-box">
    <div class="dtlms-column dtlms-one-sixth first">
        <strong><?php echo esc_html__('Start Date', 'dtlms'); ?></strong>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php echo date(get_option('date_format'), $startdate); ?>
    </div>
</div>

<div class="dtlms-custom-box">
    <div class="dtlms-column dtlms-one-sixth first">
        <strong><?php echo esc_html__('End Date', 'dtlms'); ?></strong>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php echo date(get_option('date_format'), $enddate); ?>
    </div>
</div>


<div class="dtlms-custom-box">
	<?php

	$output = '';

	if(!empty($commission_paid_courses)) {

		$output .= '<h2><strong>'.esc_html__('Courses', 'dtlms').'</strong></h2>';

		$commission_settings = get_option('dtlms-commission-settings');

		$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
						<thead>
							<tr>
								<th scope="col">'.esc_html__('#', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Course', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Subscriptions', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Commision Percentage ( % )', 'dtlms').'</th>
								<th scope="col">'.sprintf(esc_html__('Amount Paid (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</th>
							</tr>
						</thead>
						<tbody>';

						$i = 1;
						foreach($commission_paid_courses as $payment_data_key => $payment_data) {

							$course_id = $payment_data_key;

							$product = dtlms_get_product_object($course_id);
							$woo_price_html = dtlms_get_item_price_html($product);

							if($product->get_sale_price() != '') {
								$woo_price = $product->get_sale_price();
							} else {
								$woo_price = $product->get_regular_price();
							}

							$subscription_detail_html = '';
							$total_subscriptions = $commission_amount = 0;
							
							foreach($payment_data as $payment_data_key => $payment_data_detail) {
								$subscription_detail_html .= '<div class="dtlms-subscriber-details">';
									$subscription_detail_html .= '<label>'.date(get_option('date_format'), $payment_data_key).'</label>';
									$subscription_detail_html .= '<ul>';
										foreach($payment_data_detail['users'] as $user_id) {
											$user_info = get_userdata($user_id);
											$user_name = $user_info->display_name;
											$subscription_detail_html .= '<li>'.$user_name.'</li>';
											$total_subscriptions++;
										}
									$subscription_detail_html .= '</ul>';
								$subscription_detail_html .= '</div>';

								$commission = $payment_data_detail['commission'];

								$commission_amount = $commission_amount + ((count($payment_data_detail['users']) * $woo_price * $commission)/100);

							}
							
							$commission_percentage = (isset($commission_settings[$instructor_id][$course_id]) && $commission_settings[$instructor_id][$course_id] > 0) ? $commission_settings[$instructor_id][$course_id] : '';

							$output .= '<tr>
											<td>'.$i.'</td>
											<td>'.get_the_title($course_id).'</td>
											<td>'.$woo_price_html.'</td>
											<td><p class="dtlms-total_subscriptions"><span>'.$total_subscriptions.'</span></p>';
											$output .= '<div class="dtlms-subscriber-tooltip">';
												$output .= '<i class="fa fa-eye"></i>';
												$output .= '<div class="dtlms-subscription-detail-holder">'.$subscription_detail_html.'</div>';
											$output .= '</div>';
									$output .= '</td>
											<td>'.$commission_percentage.'</td>
											<td>'.$commission_amount.'</td>'; 
							$output .= '</tr>';

							$i++;

						}

		$output .= '</tbody></table>';	

	}	

	if(!empty($commission_paid_classes)) {

		$class_singular_label = apply_filters( 'class_label', 'singular' );
		$class_plural_label = apply_filters( 'class_label', 'plural' );

		$output .= '<h2><strong>'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label ).'</strong></h2>';

		$commission_settings = get_option('dtlms-commission-settings');

		$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
						<thead>
							<tr>
								<th scope="col">'.esc_html__('#', 'dtlms').'</th>
								<th scope="col">'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ).'</th>
								<th scope="col">'.esc_html__('Price', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Subscriptions', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Commision Percentage ( % )', 'dtlms').'</th>
								<th scope="col">'.sprintf(esc_html__('Amount Paid (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</th>
							</tr>
						</thead>
						<tbody>';

						$i = 1;
						foreach($commission_paid_classes as $payment_data_key => $payment_data) {

							$class_id = $payment_data_key;

							$product = dtlms_get_product_object($class_id);
							$woo_price_html = dtlms_get_item_price_html($product);

							if($product->get_sale_price() != '') {
								$woo_price = $product->get_sale_price();
							} else {
								$woo_price = $product->get_regular_price();
							}

							$subscription_detail_html = '';
							$total_subscriptions = $commission_amount = 0;
							
							foreach($payment_data as $payment_data_key => $payment_data_detail) {
								$subscription_detail_html .= '<div class="dtlms-subscriber-details">';
									$subscription_detail_html .= '<label>'.date(get_option('date_format'), $payment_data_key).'</label>';
									$subscription_detail_html .= '<ul>';
										foreach($payment_data_detail['users'] as $user_id) {
											$user_info = get_userdata($user_id);
											$user_name = $user_info->display_name;
											$subscription_detail_html .= '<li>'.$user_name.'</li>';
											$total_subscriptions++;
										}
									$subscription_detail_html .= '</ul>';
								$subscription_detail_html .= '</div>';

								$commission = $payment_data_detail['commission'];

								$commission_amount = $commission_amount + ((count($payment_data_detail['users']) * $woo_price * $commission)/100);

							}
							
							$commission_percentage = (isset($commission_settings[$instructor_id][$class_id]) && $commission_settings[$instructor_id][$class_id] > 0) ? $commission_settings[$instructor_id][$class_id] : '';

							$output .= '<tr>
											<td>'.$i.'</td>
											<td>'.get_the_title($class_id).'</td>
											<td>'.$woo_price_html.'</td>
											<td><p class="dtlms-total_subscriptions"><span>'.$total_subscriptions.'</span></p>';
											$output .= '<div class="dtlms-subscriber-tooltip">';
												$output .= '<i class="fa fa-eye"></i>';
												$output .= '<div class="dtlms-subscription-detail-holder">'.$subscription_detail_html.'</div>';
											$output .= '</div>';
									$output .= '</td>
											<td>'.$commission_percentage.'</td>
											<td>'.$commission_amount.'</td>'; 
							$output .= '</tr>';

							$i++;

						}

		$output .= '</tbody></table>';	

	}	

	$output .= '<div class="dtlms-hr-invisible"></div>';

	$output .= '<div class="dtlms-column dtlms-two-third first">';
	$output .= '</div>';
	$output .= '<div class="dtlms-column dtlms-one-third">';
		$output .= '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="20">
						<tbody>
							<tr>
								<td>'.sprintf(esc_html__('Other Amounts (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</td>
								<td>'.$other_amounts.'</td>
							</tr>							
							<tr>
								<td>'.sprintf(esc_html__('Total Amount Paid (%s)', 'dtlms'), get_woocommerce_currency_symbol()).'</td>
								<td>'.$total_commission_paid.'</td>
							</tr>								
						</tbody>
					</table>';	
	$output .= '</div>';	

	echo $output;

    ?>
</div>