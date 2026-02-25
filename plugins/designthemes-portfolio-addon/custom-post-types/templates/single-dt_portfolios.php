<?php get_header('dtportfolio'); ?>

	<?php
	/**
	* dtportfolio_before_main_content hook.
	*/
	do_action( 'dtportfolio_before_main_content' );
	?>

		<?php
		/**
		* dtportfolio_before_content hook.
		*/
		do_action( 'dtportfolio_before_content' );
		?>

			<?php
			if( have_posts() ): 
				while( have_posts() ): 
				the_post();

					$portfolio_id = get_the_ID(); 
					$portfolio_title = get_the_title();
					$portfolio_permalink = get_permalink();


					$portfolio_settings = get_post_meta($portfolio_id, '_portfolio_settings', true);
					$portfolio_settings = is_array($portfolio_settings) ? $portfolio_settings : array ();

					$portfolio_layout = array_key_exists('portfolio-layout', $portfolio_settings) ? $portfolio_settings['portfolio-layout'] : '';
					$mediaontop_mediatype = array_key_exists('mediaontop-mediatype', $portfolio_settings) ? $portfolio_settings['mediaontop-mediatype'] : '';

					$gallery_position = $fullwidth_class = '';
					if($portfolio_layout == 'fixed-gallery' || $portfolio_layout == 'fixed-feature-image' || $portfolio_layout == 'gallery-list') {

						$gallery_position = ($portfolio_settings['gallery-position'] != '' ) ? $portfolio_settings['gallery-position'] : 'left';
						$fullwidth_class = 'dtportfolio-fullwidth-wrapper';

					}

					
					$class = 'dtportfolio-single-layout-container '.$portfolio_layout.' '.$fullwidth_class;

				    echo '<div class="'.esc_attr($class).' '.esc_attr($gallery_position).'">';

						
						// Using the_content() for elementor compatilble
						ob_start();
						do_shortcode(the_content());
						$the_content = ob_get_clean();


		               if($portfolio_layout == 'media-on-top') {

		               		echo $the_content;

						} else if($portfolio_layout == 'fixed-gallery') {

							if ( ! post_password_required() ) {

								$gallery_position = (isset($portfolio_settings['gallery-position']) && $portfolio_settings['gallery-position'] != '' ) ? $portfolio_settings['gallery-position'] : 'left';

								if($gallery_position == 'right') {
									echo '<div class="dtportfolio-column dtportfolio-one-half no-space first">';
										echo '<div class="dtportfolio-details">'; 
												echo $the_content;
										echo '</div>';
									echo '</div>';							
									echo '<div class="dtportfolio-column dtportfolio-one-half no-space">';
										echo '<div class="dtportfolio-fixed-content">';
												echo do_shortcode('[dtportfolio_slider background_image="true" carousel_paginationtype="bullets" carousel_slidesperview="1" /]');
										echo '</div>';
									echo '</div>';	
								} else {
									echo '<div class="dtportfolio-column dtportfolio-one-half no-space first">';
										echo '<div class="dtportfolio-fixed-content">';
												echo do_shortcode('[dtportfolio_slider background_image="true" carousel_paginationtype="bullets" carousel_slidesperview="1" /]');
										echo '</div>';
									echo '</div>';
									echo '<div class="dtportfolio-column dtportfolio-one-half no-space">';
										echo '<div class="dtportfolio-details">'; 
												echo $the_content;
										echo '</div>';
									echo '</div>';							
								}


							} else {

								echo '<div class="dtportfolio-details">'; 
										echo $the_content;
								echo '</div>';	
														
							}

						} else if($portfolio_layout == 'fixed-feature-image') {

							if ( ! post_password_required() ) {

								$gallery_position = ($portfolio_settings['gallery-position'] != '' ) ? $portfolio_settings['gallery-position'] : 'left';

								$image_src = wp_get_attachment_image_src(get_post_thumbnail_id( $portfolio_id ), 'full', false);
								$image_src = $image_src[0];

								if($gallery_position == 'right') {

									echo '<div class="dtportfolio-column dtportfolio-two-third no-space first">';
										echo '<div class="dtportfolio-details">'; 
												echo $the_content;
										echo '</div>';
									echo '</div>';
									echo '<div class="dtportfolio-column dtportfolio-one-third no-space">';
										echo '<div class="dtportfolio-fixed-content" style="background-image:url('.$image_src.');"></div>';
									echo '</div>';							

								} else {

									echo '<div class="dtportfolio-column dtportfolio-one-third no-space first">';
										echo '<div class="dtportfolio-fixed-content" style="background-image:url('.$image_src.');"></div>';
									echo '</div>';
									echo '<div class="dtportfolio-column dtportfolio-two-third no-space">';
										echo '<div class="dtportfolio-details">'; 
												echo $the_content;
										echo '</div>';
									echo '</div>';

								}

							} else {

								echo '<div class="dtportfolio-details">'; 
										echo $the_content;
								echo '</div>';	
														
							}						

						} else if($portfolio_layout == 'gallery-list') {

							if ( ! post_password_required() ) {

								$gallery_position = ($portfolio_settings['gallery-position'] != '' ) ? $portfolio_settings['gallery-position'] : 'left';

								if($gallery_position == 'right') {

									echo '<div class="dtportfolio-column dtportfolio-one-half no-space first with-content-left">';
										echo '<div class="dtportfolio-details dtportfolio-fixed-content">'; 

												echo $the_content;

										echo '</div>';
									echo '</div>';
									echo '<div class="dtportfolio-column dtportfolio-one-half no-space">';

											$gallery_settings['portfolio_id'] = $portfolio_id;
											$gallery_settings['portfolio-post-layout'] = 'dtportfolio-one-column';
											$gallery_settings['portfolio-grid-space'] = '';
											$gallery_settings['animationeffect'] = 'fadeInUp';
											$gallery_settings['animationdelay'] = '400';
											$gallery_settings['gallerytype'] = 'gallery-list';	

											echo dtportfolio_get_portfolio_gallery_listing($gallery_settings);

									echo '</div>';

								} else {

									echo '<div class="dtportfolio-column dtportfolio-one-half no-space first">';

											$gallery_settings['portfolio_id'] = $portfolio_id;
											$gallery_settings['portfolio-post-layout'] = 'dtportfolio-one-column';
											$gallery_settings['portfolio-grid-space'] = '';
											$gallery_settings['animationeffect'] = 'fadeInUp';
											$gallery_settings['animationdelay'] = '400';
											$gallery_settings['gallerytype'] = 'gallery-list';	

											echo dtportfolio_get_portfolio_gallery_listing($gallery_settings);						
									echo '</div>';
									echo '<div class="dtportfolio-column dtportfolio-one-half no-space with-content-right">';
										echo '<div class="dtportfolio-details dtportfolio-fixed-content">'; 

												echo $the_content;

										echo '</div>';
									echo '</div>';

								}

							} else {

								echo '<div class="dtportfolio-details">'; 
										echo $the_content;
								echo '</div>';	
														
							}						

		                } else {

							echo $the_content;

		                }

				    echo '</div>';

					echo '<div class="dtportfolio-fullwidth-wrapper-fix"></div>';

				endwhile; 
			endif;
			?>

		<?php
		/**
		* dtportfolio_after_content hook.
		*/
		do_action( 'dtportfolio_after_content' );
		?>

	<?php
	/**
	* dtportfolio_after_main_content hook.
	*/
	do_action( 'dtportfolio_after_main_content' );
	?>

<?php get_footer('dtportfolio'); ?>