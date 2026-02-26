<?php

// Comment Form Fields
if(!function_exists('dtportfolio_comment_form_fields')) {

	function dtportfolio_comment_form_fields( $fields ) {

		if(is_singular('dt_portfolios')) {

			global $post;

			unset($fields['author']);
			unset($fields['email']);
			unset($fields['url']);

			$dtportfolio_media_in_commentform = get_post_meta($post->ID, 'dtportfolio_media_in_commentform', true);
			$dtportfolio_ratings_in_commentform = get_post_meta($post->ID, 'dtportfolio_ratings_in_commentform', true);
	

			$fields['author'] = '<div class="dtportfolio-column dtportfolio-one-half first">
									<input id="author" name="author" type="text" placeholder="'.esc_html__('Name', 'dtportfolio').'" required />
								</div>';
						
			$fields['email']  = '<div class="dtportfolio-column dtportfolio-one-half">
									<input id="email" name="email" type="text" placeholder="'.esc_html__('Email', 'dtportfolio').'" required />
								</div>';

		}

		return $fields;

	}

	add_filter( 'comment_form_default_fields', 'dtportfolio_comment_form_fields' );

}

// Move comment field to bottom
if(!function_exists('dtportfolio_move_comment_field_to_bottom')) {

	function dtportfolio_move_comment_field_to_bottom( $fields ) {

		if(is_singular('dt_portfolios')) {
			$comment_field = $fields['comment'];
			unset( $fields['comment'] );
			$fields['comment'] = $comment_field;
		}
		return $fields;

	}

	add_filter( 'comment_form_fields', 'dtportfolio_move_comment_field_to_bottom' );

}

// Modifying comments template
function dtportfolio_modifying_comment_template( $comment_template ) {
     global $post;
     if ( !( is_singular('dt_portfolios') && ( have_comments() || 'open' == $post->comment_status ) ) ) {
        return;
     }

     return dirname(__FILE__) . '/comments.php';
}
add_filter('comments_template', 'dtportfolio_modifying_comment_template');


// Modifying comments list
function dtportfolio_comment_style( $comment, $args, $depth ) {

    $dtdirectory_title = get_comment_meta( $comment->comment_ID, 'dtdirectory_title', true );

	$GLOBALS['comment'] = $comment;
	switch ($comment->comment_type ) :
		case 'pingback':
		case 'trackback':
			echo '<li class="post pingback">';
			echo '<p>';
			esc_html_e('Pingback:', 'dtdirectory');
			comment_author_link();
			edit_comment_link(esc_html__('Edit', 'dtdirectory'), ' ', '');
			echo '</p>';
		break;

		default:
		case '':
			echo "<li ";
			comment_class();
			echo ' id="li-comment-';
			comment_ID();
			echo '">';
			echo '<article class="comment" id="comment-';
			comment_ID();
			echo '">';

			echo '<header class="comment-author">'.get_avatar($comment, 450).'</header>';

			echo '<section class="comment-details">';
			echo '	<div class="author-name">';
			echo 		get_comment_author_link();
			echo '		<span class="commentmetadata">'.get_the_date ( get_option('date_format') ).'</span>';
			echo '	</div>';
			echo '  <div class="comment-body">';
						if($dtdirectory_title != '') {
							echo esc_html($dtdirectory_title);
						}
						comment_text();
						if ($comment->comment_approved == '0') :
							esc_html_e('Your comment is awaiting moderation.', 'dtdirectory');
						endif;
						edit_comment_link(esc_html__('Edit', 'dtdirectory'));
			echo '	</div>';
			if(isset($args['disable_reply']) && $args['disable_reply'] != 'true') {
				echo '	<div class="reply">';
				echo 		comment_reply_link(array_merge($args, array('reply_text' => esc_html__('Reply', 'dtdirectory'), 'depth' => $depth, 'max_depth' => $args['max_depth'])));
				echo '	</div>';
			}
			echo '</section>';
			echo '</article><!-- .comment-ID -->';
		break;
	endswitch;

}

// Portfolio gallery listing
function dtportfolio_get_portfolio_gallery_listing($gallery_settings) {

	$output = '';

	if(!is_array($gallery_settings) && empty($gallery_settings)) {
		$gallery_settings = array ();
	}

	$portfolio_id = isset( $gallery_settings['portfolio_id'] ) ? $gallery_settings['portfolio_id'] : '';

	if(isset($gallery_settings['gallery_ids']) && $gallery_settings['gallery_ids'] != '') {
		$filter_gallery = true;
		$gallery_ids = explode(',', $gallery_settings['gallery_ids']);
	} else {
		$filter_gallery = false;
	}

	$portfolio_settings = get_post_meta ( $portfolio_id, '_portfolio_settings', TRUE );
	$portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();

	$portfolio_layout_class = '';
	$portfolio_layout = array_key_exists("portfolio-layout",$portfolio_settings) ? $portfolio_settings['portfolio-layout'] : ''; 
	if($portfolio_layout != '') {
		$portfolio_layout_class = 'dtportfolio-container-'.$portfolio_layout;
	}

	$hover_class = (isset($gallery_settings['portfolio-hover-design']) && $gallery_settings['portfolio-hover-design'] == 'true') ? 'dtportfolio-hover-overlay' : 'hover-none';

	$column_layout = isset( $gallery_settings['portfolio-post-layout'] ) ? $gallery_settings['portfolio-post-layout'] : 'dtportfolio-one-half-column';
	if($column_layout == 'dtportfolio-one-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-column';
		$column = 1;	
	} else if($column_layout == 'dtportfolio-one-half-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-half';
		$column = 2;
	} else if($column_layout == 'dtportfolio-one-third-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-third';
		$column = 3;
	} else if($column_layout == 'dtportfolio-one-fourth-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-fourth';
		$column = 4;
	} else if($column_layout == 'dtportfolio-one-fifth-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-fifth';
		$column = 5;
	} else if($column_layout == 'dtportfolio-one-sixth-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-sixth';
		$column = 6;
	} else if($column_layout == 'dtportfolio-one-seventh-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-seventh';
		$column = 7;
	} else if($column_layout == 'dtportfolio-one-eight-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-eight';
		$column = 8;
	} else if($column_layout == 'dtportfolio-one-nineth-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-nineth';
		$column = 9;
	} else if($column_layout == 'dtportfolio-one-tenth-column') {
		$column_class = 'dtportfolio-column dtportfolio-one-tenth';
		$column = 10;			
	}

	/*if(isset($gallery_settings['gallerytype']) && $gallery_settings['gallerytype'] == 'gallery-list') {
		$image_size = 'dtportfolio-960x740';
	} else */
	if($column > 6) {
		$image_size = 'dtportfolio-420x420';
	} else {
		$image_size = 'full';
	}

	$space_class = (isset($gallery_settings['portfolio-grid-space']) && $gallery_settings['portfolio-grid-space'] == 'true') ? 'with-space' : 'no-space';

	$animation_class = $animation_effect = $animation_delay = '';
	$animationeffect = isset( $gallery_settings['animationeffect'] ) ? $gallery_settings['animationeffect'] : '';
	$animationdelay = isset( $gallery_settings['animationdelay'] ) ? $gallery_settings['animationdelay'] : '';
	if($animationeffect != '') {
		$animation_class = 'animate';
		$animation_effect = 'data-animationeffect="'.$animationeffect.'"';
		$animation_delay = 'data-animationdelay="'.$animationdelay.'"';
	}

	$repeat_animation_class = (isset($gallery_settings['repeat-animation']) && $gallery_settings['repeat-animation'] == 'true' ) ? 'repeat-animation' : '';

	$output .= '<div class="dtportfolio-container-wrapper">';

		$output .= '<div class="dtportfolio-container dtportfolio-single-container '.$space_class.' '.$repeat_animation_class.' '.$portfolio_layout_class.'">';

			$grid_sizer_class = str_replace('dtportfolio-column ', '', $column_class);
			$output .= '<div class="dtportfolio-grid-sizer '.$grid_sizer_class.'"></div>';

			if( array_key_exists('portfolio-gallery', $portfolio_settings) ) {

				$i = 1;
				
				$items = explode(',', $portfolio_settings['portfolio-gallery']);
				foreach( $items as $item ){

					if($filter_gallery) {

						if(in_array($item, $gallery_ids)) {

	    					$image_src = wp_get_attachment_image_src($item, $image_size, false);
							$image_src = $image_src[0];

							if($image_src != '') {

								if($i == 1) $first_class = 'first'; else $first_class = '';
								if($i == $column) $i = 1; else $i = $i + 1;

								$output .= '<div class="dtportfolio-item '.esc_attr($column_class).' '.esc_attr($hover_class).' '.esc_attr($space_class).' '.esc_attr($first_class).' '.esc_attr($animation_class).'" '.$animation_effect.' '.$animation_delay.'> 
										<figure>
											<img src="'.esc_url($image_src).'" alt="portfolio-image" title="portfolio-image" />
											<div class="dtportfolio-image-overlay">
												<div class="links">
			                                		<a title="'.get_the_title($portfolio_id).'" data-gal="prettyPhoto[gallery-listing]" href="'.esc_url($image_src).'"><span class="fas fa-search"> </span></a>
		                                		</div>
		                                    </div>
	                                    </figure>
									</div>';

							}

						}

					} else {

						$image_src = wp_get_attachment_image_src($item, $image_size, false);
						$image_src = $image_src[0];

						if($image_src != '') {

							if($i == 1) $first_class = 'first'; else $first_class = '';
							if($i == $column) $i = 1; else $i = $i + 1;

							$output .= '<div class="dtportfolio-item '.esc_attr($column_class).' '.esc_attr($hover_class).' '.esc_attr($space_class).' '.esc_attr($first_class).' '.esc_attr($animation_class).'" '.$animation_effect.' '.$animation_delay.'> 
									<figure>
										<img src="'.esc_url($image_src).'" alt="portfolio-image" title="portfolio-image" />
										<div class="dtportfolio-image-overlay">
											<div class="links">
		                                		<a title="'.get_the_title($portfolio_id).'" data-gal="prettyPhoto[gallery-listing]" href="'.esc_url($image_src).'"><span class="fas fa-search"> </span></a>
	                                		</div>
	                                    </div>
	                                </figure>
								</div>';

						}

	    			}

				}

	    	}

		$output .= '</div>';

	$output .= '</div>';

	return $output;

}

// Portfolio navigation links
function dtportfolio_get_portfolio_navigation_links($type) {

	$output = '';

	if($type == 'type4') {

		$output .= '<div class="post-nav-container '.$type.'">';
			$output .= '<div class="post-prev-link">';
					$output .= get_previous_post_link('%link', '<i class="fas fa-chevron-left"> </i>'.esc_html__('Prev Entry','dtportfolio') );
			$output .= '</div>';		
			$output .= '<div class="post-next-link">';
					$output .= get_next_post_link('%link', esc_html__('Next Entry','dtportfolio').'<i class="fas fa-chevron-right"> </i>');
			$output .= '</div>';
		$output .= '</div>';

	} else if($type == 'type3') {

		$prev_post = get_previous_post();
		$next_post = get_next_post();

		$output .= '<div class="post-nav-container '.$type.'">';
			$output .= '<div class="post-prev-link">';
				if(isset($prev_post) && !empty($prev_post)) {
					$output .= '<i class="fas fa-chevron-left"> </i>';
					$output .= get_previous_post_link('%link', esc_html__('Prev Entry','dtportfolio') );
					$output .= '<a href="'.get_permalink($prev_post->ID).'">'.$prev_post->post_title.'</a>';
				}
			$output .= '</div>';
			$output .= '<div class="post-archive-link-wrapper">';
					$output .= '<a class="post-archive-link" href="'.get_post_type_archive_link('dt_portfolios').'"></a>';
			$output .= '</div>';	
			$output .= '<div class="post-next-link">';
				if(isset($next_post) && !empty($next_post)) {
					$output .= '<i class="fas fa-chevron-right"> </i>';
					$output .= get_next_post_link('%link', esc_html__('Next Entry','dtportfolio'));
					$output .= '<a href="'.get_permalink($next_post->ID).'">'.$next_post->post_title.'</a>';
				}
			$output .= '</div>';
		$output .= '</div>';

	} else if($type == 'type2') {

		$output .= '<div class="post-nav-container '.$type.'">';
			$output .= '<div class="post-prev-link">';
					$output .= get_previous_post_link('%link', '<i class="fas fa-chevron-left"> </i>' );
			$output .= '</div>';
			$output .= '<div class="post-archive-link-wrapper">';
					$output .= '<a class="post-archive-link" href="'.get_post_type_archive_link('dt_portfolios').'"></a>';
			$output .= '</div>';
			$output .= '<div class="post-next-link">';
					$output .= get_next_post_link('%link', '<i class="fas fa-chevron-right"> </i>');
			$output .= '</div>';
		$output .= '</div>';

	} else {

		$output .= '<div class="post-nav-container type1">';
			$output .= '<div class="post-prev-link">';
					$output .= get_previous_post_link('%link', '<i class="fas fa-chevron-left"> </i>'.esc_html__('Prev Entry','dtportfolio') );
			$output .= '</div>';
			$output .= '<div class="post-archive-link-wrapper">';
					$output .= '<a class="post-archive-link" href="'.get_post_type_archive_link('dt_portfolios').'"></a>';
			$output .= '</div>';			
			$output .= '<div class="post-next-link">';
					$output .= get_next_post_link('%link', esc_html__('Next Entry','dtportfolio').'<i class="fas fa-chevron-right"> </i>');
			$output .= '</div>';
		$output .= '</div>';

	}

	return $output;

}

// Porfolio terms
function dtportfolio_get_portfolio_terms($portfolio_id, $with_icon) {

	if($portfolio_id != '' && $portfolio_id > 0) {

	} else {
		global $post;
		$portfolio_id = $post->ID;		
	}

	if($with_icon) {
		$output = get_the_term_list($portfolio_id, 'portfolio_entries', '<p class="dtportfolio-categories"> <i class="fas fa-tags"> </i> ', ', ', '</p> ');
	} else {
		$output = get_the_term_list($portfolio_id, 'portfolio_entries', '<p class="dtportfolio-categories"> ', ', ', '</p> ');
	}

	return $output;

}

// Portfolio title
function dtportfolio_get_portfolio_lists($portfolio_id) {

	if($portfolio_id != '' && $portfolio_id > 0) {

	} else {
		global $post;
		$portfolio_id = $post->ID;		
	}

	$output = '<h2>'.get_the_title($portfolio_id).'</h2>';

	return $output;

}

// Alter password protected post form
function dtportfolio_portfolio_password_form() {

	if(is_singular('dt_portfolios')) {

		global $post;

		$label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
		$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
		<p>' . esc_html__( 'Enter your password below:', 'dtportfolio' ) . '</p>
		<p><label for="' . $label . '">' . ' <input name="post_password" placeholder="'.esc_html__( 'Password:', 'dtportfolio' ) .'" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'dtportfolio' ) . '" /></p></form>';
	    		
	} else {

		global $post;

		$label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
		$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
		<p>' . __( 'This content is password protected. To view it please enter your password below:' ) . '</p>
		<p><label for="' . $label . '">' . __( 'Password:' ) . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form' ) . '" /></p></form>';

	}

	return $output;

}
add_filter( 'the_password_form', 'dtportfolio_portfolio_password_form' );


function dtportfolio_mediaontop_hook($portfolio_id) {

	if($portfolio_id != '' && $portfolio_id > 0) {

	} else {
		global $post;
		$portfolio_id = $post->ID;		
	}

	$output = '';

	$portfolio_settings = get_post_meta ( $portfolio_id, '_portfolio_settings', TRUE );
	$portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();

	$portfolio_layout = array_key_exists("portfolio-layout",$portfolio_settings) ? $portfolio_settings['portfolio-layout'] : ''; 

	if($portfolio_layout == 'media-on-top') {

		$output .= '<section class="dtportfolio-single-mediaontop-section-holder">';

			$portfolio_featured_video = get_post_meta($portfolio_id, '_portfolio_featured_video', TRUE);
			$portfolio_featured_video = is_array($portfolio_featured_video) ? $portfolio_featured_video  : array();

			$featured_video =  isset($portfolio_featured_video['featured_video']) ? $portfolio_featured_video['featured_video'] : '';

			if($featured_video != '') {

				$output .= '<div class="dtportfolio-single-mediaontop-section">
								<video poster="'.plugins_url('designthemes-portfolio-addon').'/images/video-image.png" id="portfolio-featured-video" autoplay="autoplay" loop="loop" class="pause">
									<source src="'.$featured_video.'" type="video/mp4">
									<source src="'.$featured_video.'" type="video/webm">
									<source src="'.$featured_video.'" type="video/ogg">
								</video>
							</div>';

			} else if( has_post_thumbnail($portfolio_id) ){

				$bgimage = wp_get_attachment_image_src(get_post_thumbnail_id($portfolio_id), 'full', false);

				$output .= '<div class="dtportfolio-single-mediaontop-section">';
		    		$output .= '<div class="dtportfolio-single-mediaontop-item" style="background-image:url('.esc_url($bgimage[0]).')"></div>';
		    	$output .= '</div>';

		    } 

		$output .= '</section>';
		$output .= '<div class="dtportfolio-fullwidth-wrapper-fix"></div>';


	}

	echo $output;

}
add_action( 'dtportfolio_mediaontop_hook', 'dtportfolio_mediaontop_hook' );

?>