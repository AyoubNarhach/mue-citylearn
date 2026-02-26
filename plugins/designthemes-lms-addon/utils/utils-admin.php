<?php

if( !function_exists( 'dtlms_adminpanel_image_preview' ) ){
	function dtlms_adminpanel_image_preview($src) {

		$default = plugins_url ('designthemes-lms-addon').'/images/backend/no-image.jpg';
		$src = !empty($src) ? $src : $default;
		
		$output = '';

		$output .= '<div class="dtlms-image-preview-holder">';
			$output .= '<a href="#" class="dtlms-image-preview" onclick="return false;">
							<img src="'.plugins_url ('designthemes-lms-addon').'/images/backend/image-preview.png" alt="'.esc_html__('Image Preview', 'dtlms').'" title="'.esc_html__('Image Preview', 'dtlms').'" />';
							$output .= '<div class="dtlms-image-preview-tooltip">';
								$output .= '<img src="'.$src.'" data-default="'.$default.'"  alt="'.esc_html__('Image Preview Tooltip', 'dtlms').'" title="'.esc_html__('Image Preview Tooltip', 'dtlms').'" />';
							$output .= '</div>';
			$output .= '</a>';
		$output .= '</div>';

		return $output;

	}
}

?>