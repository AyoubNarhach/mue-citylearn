<?php
use DTPortfolioElementor\Widgets\DTPortfolioElementorWidgetBase;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class Elementor_Portfolio_Single_Sliders extends DTPortfolioElementorWidgetBase {
	public function get_name() {
		return 'dt-portfolio-single-sliders';
	}

	public function get_title() {
		return __( 'Portfolio Single - Sliders', 'dtportfolio' );
	}

	protected function _register_controls() {
		$this->content_tab();
	}

	# CONTENT TAB
	protected function content_tab() {

		# General
			$this->start_controls_section( 'dt_section_general', array(
				'label' => __( 'General', 'dtportfolio'),
			) );

				# Portfolio ID
					$this->add_control( 'portfolio_id', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Portfolio ID', 'dtportfolio'),
						'default' => '',
						'description' => __('Enter portfolio id here. If you are going to use this shortcode in portfolio single page no need to give portfolio id.', 'dtportfolio'),
					) );

				# Gallery ID(s)
					$this->add_control( 'gallery_ids', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Gallery ID(s)', 'dtportfolio'),
						'default' => '',
						'description' => __('Comma separated gallery item ids to display particular gallery items only.', 'dtportfolio'),
					) );

				# Include Feature Image
					$this->add_control( 'include_featured_image', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Include Feature Image', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'), 
							'true'  => esc_html__('True', 'dtportfolio') 
						)							
					) );

				# Include Feature Video
					$this->add_control( 'include_featured_video', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Include Feature Video', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'), 
							'true'  => esc_html__('True', 'dtportfolio') 
						)							
					) );

				# Class
					$this->add_control( 'class', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Class', 'dtportfolio'),
						'default' => '',
						'description' => __('If you wish you can add additional class name here.', 'dtportfolio'),
					) );

			$this->end_controls_section();	

		# Carousel

			$this->start_controls_section( 'dt_section_carousel', array(
				'label' => __( 'Carousel', 'dtportfolio'),
			) );

				# Effect
					$this->add_control( 'carousel_effect', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Effect', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''          => esc_html__('Default', 'dtportfolio'),
							'cube'      => esc_html__('Cube', 'dtportfolio'),
							'fade'      => esc_html__('Fade', 'dtportfolio'),
							'flip'      => esc_html__('Flip', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Cube and Fade effect.', 'dtportfolio' )
					) );

				# Auto Play
					$this->add_control( 'carousel_autoplay', array(
						'type'    => Controls_Manager::NUMBER,
						'label'   => __('Auto Play', 'dtportfolio'),
						'default' => '',
						'min'     => 1,
						'max'     => 50000,
						'step'    => 1,
					) );

				# Slides Per View
					$this->add_control( 'carousel_slidesperview', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Slides Per View', 'dtportfolio'),
						'default' => 1,
						'options' => array(
							1 => 1, 
							2 => 2, 
							3 => 3, 
							4 => 4, 
							5 => 5, 
							6 => 6, 
							7 => 7, 
							8 => 8, 
							9 => 9, 
							10 => 10
						),
						'description' => esc_html__( 'Number slides of to show in view port.', 'dtportfolio' )
					) );

				# Enable Loop Mode
					$this->add_control( 'carousel_loopmode', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Loop Mode', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false'          => esc_html__('False', 'dtportfolio'),
							'true'      => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'If you wish you can enable continous loop mode for your carousel. Thumbnail pagination wont work along with "Loop Mode".', 'dtportfolio' )
					) );

				# Enable Mousewheel Control
					$this->add_control( 'carousel_mousewheelcontrol', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Mousewheel Control', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false'          => esc_html__('False', 'dtportfolio'),
							'true'      => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtportfolio' )
					) );

				# Enable Vertical Direction
					$this->add_control( 'carousel_verticaldirection', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Vertical Direction', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false'          => esc_html__('False', 'dtportfolio'),
							'true'      => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To make your slides to navigate vertically.', 'dtportfolio' ),
					) );

				# Pagination Type
					$this->add_control( 'carousel_paginationtype', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Pagination Type', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''            => esc_html__('None', 'dtportfolio'),
							'bullets'     => esc_html__('Bullets', 'dtportfolio'),
							'fraction'    => esc_html__('Fraction', 'dtportfolio'),
							'progressbar' => esc_html__('Progress Bar', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose pagination type you like to use.', 'dtportfolio' ),
					) );

				# Enable Thumbnail Pagination
					$this->add_control( 'carousel_thumbnailpagination', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Thumbnail Pagination', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'),
							'true'  => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable thumbnail pagination.', 'dtportfolio' ),
					) );

				# Enable Arrow Pagination
					$this->add_control( 'carousel_arrowpagination', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Arrow Pagination', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'),
							'true'  => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable arrow pagination.', 'dtportfolio' ),
					) );

				# Arrow Type
					$this->add_control( 'carousel_arrowpagination_type', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Arrow Type', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''          => esc_html__('Default', 'dtportfolio'),
							'type2'      => esc_html__('Type 2', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose arrow pagination type for your carousel.', 'dtportfolio' )
					) );

				# Enable Scrollbar
					$this->add_control( 'carousel_scrollbar', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Scrollbar', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false'          => esc_html__('False', 'dtportfolio'),
							'true'      => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable scrollbar for your carousel.', 'dtportfolio' ),
					) );

				# Enable Arrow For Mouse Pointer
					$this->add_control( 'carousel_arrowformousepointer', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Arrow For Mouse Pointer', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'),
							'true'  => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable arrow for mouse pointer for your carousel.', 'dtportfolio' ),
					) );

				# Pagination Color Scheme
					$this->add_control( 'carousel_paginationcolorscheme', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Pagination Color Scheme', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''          => esc_html__('Light', 'dtportfolio'),
							'dark'      => esc_html__('Dark', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose pagination color scheme for your carousel.', 'dtportfolio' ),
					) );

				# Enable Play/Pause Button
					$this->add_control( 'portfolio-carousel-playpausebutton', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Play/Pause Button', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'),
							'true'  => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable play pause button for your carousel.', 'dtportfolio' ),
					) );

				# Space Between Sliders
					$this->add_control( 'carousel_spacebetween', array(
						'type'    => Controls_Manager::NUMBER,
						'label'   => __('Space Between Sliders', 'dtportfolio'),
						'description' => esc_html__( 'Space between sliders can be given here.', 'dtportfolio' ),
						'default' => '',
						'min'     => 1,
						'max'     => 100,
						'step'    => 1,
					) );

				# Overall Pagination Design Types
					$this->add_control( 'carousel_pagination_designtype', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Overall Pagination Design Types', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''          => esc_html__('Default', 'dtportfolio'),
							'type2'      => esc_html__('Type 2', 'dtportfolio'),
							'type3'      => esc_html__('Type 3', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose overall pagination design type for your carousel.', 'dtportfolio' )
					) );

			$this->end_controls_section();


	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$output = '';
	
		if($settings['portfolio_id'] == '' && is_singular('dt_portfolios')) {
			global $post;
			$settings['portfolio_id'] = $post->ID;
		}

		if($settings['portfolio_id'] != '') {

			$media_carousel_attributes = array ();

			$carousel_effect = isset($settings['carousel_effect']) ? $settings['carousel_effect'] : '';
			$carousel_autoplay = isset($settings['carousel_autoplay']) ? $settings['carousel_autoplay'] : '';
			$carousel_slidesperview = isset($settings['carousel_slidesperview']) ? $settings['carousel_slidesperview'] : '';
			$carousel_loopmode = isset($settings['carousel_loopmode']) ? $settings['carousel_loopmode'] : '';
			$carousel_mousewheelcontrol = isset($settings['carousel_mousewheelcontrol']) ? $settings['carousel_mousewheelcontrol'] : '';
			$carousel_verticaldirection = isset($settings['carousel_verticaldirection']) ? $settings['carousel_verticaldirection'] : '';
			$carousel_paginationtype = isset($settings['carousel_paginationtype']) ? $settings['carousel_paginationtype'] : '';
			$carousel_thumbnailpagination = isset($settings['carousel_thumbnailpagination']) ? $settings['carousel_thumbnailpagination'] : '';
			$carousel_arrowpagination = isset($settings['carousel_arrowpagination']) ? $settings['carousel_arrowpagination'] : '';
			$carousel_scrollbar = isset($settings['carousel_scrollbar']) ? $settings['carousel_scrollbar'] : '';
			$carousel_arrowformousepointer = isset($settings['carousel_arrowformousepointer']) ? $settings['carousel_arrowformousepointer'] : '';
			$carousel_playpausebutton = isset($settings['carousel_playpausebutton']) ? $settings['carousel_playpausebutton'] : '';
			$carousel_spacebetween = isset($settings['carousel_spacebetween']) ? $settings['carousel_spacebetween'] : '';

			$carousel_paginationcolorscheme = isset($settings['carousel_paginationcolorscheme']) ? $settings['carousel_paginationcolorscheme'] : '';
			$carousel_pagination_designtype = isset($settings['carousel_pagination_designtype']) ? $settings['carousel_pagination_designtype'] : '';
			$carousel_arrowpagination_type = isset($settings['carousel_arrowpagination_type']) ? $settings['carousel_arrowpagination_type'] : '';

			$background_image = isset($settings['background_image']) ? $settings['background_image'] : '';


			array_push($media_carousel_attributes, 'data-enablecarousel="true"');
			array_push($media_carousel_attributes, 'data-carouseleffect="'.esc_attr($carousel_effect).'"');
			array_push($media_carousel_attributes, 'data-carouselautoplay="'.esc_attr($carousel_autoplay).'"');
			array_push($media_carousel_attributes, 'data-carouselslidesperview="'.esc_attr($carousel_slidesperview).'"');
			array_push($media_carousel_attributes, 'data-carouselloopmode="'.esc_attr($carousel_loopmode).'"');
			array_push($media_carousel_attributes, 'data-carouselmousewheelcontrol="'.esc_attr($carousel_mousewheelcontrol).'"');
			array_push($media_carousel_attributes, 'data-carouselverticaldirection="'.esc_attr($carousel_verticaldirection).'"');
			array_push($media_carousel_attributes, 'data-carouselpaginationtype="'.esc_attr($carousel_paginationtype).'"');
			array_push($media_carousel_attributes, 'data-carouselthumbnailpagination="'.esc_attr($carousel_thumbnailpagination).'"');
			array_push($media_carousel_attributes, 'data-carouselarrowpagination="'.esc_attr($carousel_arrowpagination).'"');
			array_push($media_carousel_attributes, 'data-carouselscrollbar="'.esc_attr($carousel_scrollbar).'"');
			array_push($media_carousel_attributes, 'data-carouselarrowformousepointer="'.esc_attr($carousel_arrowformousepointer).'"');
			array_push($media_carousel_attributes, 'data-carouselplaypausebutton="'.esc_attr($carousel_playpausebutton).'"');
			array_push($media_carousel_attributes, 'data-carouselspacebetween="'.esc_attr($carousel_spacebetween).'"');

			if(!empty($media_carousel_attributes)) {
				$media_carousel_attributes_string = implode(' ', $media_carousel_attributes);
			}

			$portfolio_settings = get_post_meta($settings['portfolio_id'], '_portfolio_settings', true);
			$portfolio_settings = is_array($portfolio_settings) ? $portfolio_settings : array ();

			$portfolio_galleries = explode(',', $portfolio_settings['portfolio-gallery']);

			if(isset($settings['gallery_ids']) && $settings['gallery_ids'] != '') {
				$filter_gallery = true;
				$gallery_ids = explode(',', $settings['gallery_ids']);
			} else {
				$filter_gallery = false;
			}


		    $featured_image_id = get_post_thumbnail_id($settings['portfolio_id']);	
		    $featured_image_id = ($featured_image_id != '') ? $featured_image_id : -1;

			$output .= '<div class="dtportfolio-image-gallery-holder '.$settings['class'].'">';

				// Gallery Images
				$output .= '<div class="dtportfolio-image-gallery-container swiper-container">';
				    $output .= '<div class="dtportfolio-image-gallery swiper-wrapper" '.$media_carousel_attributes_string.'>';

				    				$gallery_thumb = array ();

				    				if($settings['include_featured_image'] == 'true') {

										$image_details = wp_get_attachment_image_src($featured_image_id, 'full');
										
			                           	$output .= '<div class="swiper-slide">';

				                           	if($background_image == 'true') {
				                           		$output .= '<div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div>';
				                           	} else {
				                           		$output .= '<img src="'.esc_url($image_details[0]).'" title="'.esc_html__('Featured Image', 'dtportfolio').'" alt="'.esc_html__('Featured Image', 'dtportfolio').'" />';
				                           	}

			                           	$output .= '</div>';

										array_push($gallery_thumb, '<div class="swiper-slide"><div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div></div>');

									}

				                    if(is_array($portfolio_galleries) && !empty($portfolio_galleries)) {
				                        foreach($portfolio_galleries as $portfolio_gallery) {

											if($filter_gallery) {

												if(in_array($portfolio_gallery, $gallery_ids)) {

					                                $image_details = wp_get_attachment_image_src($portfolio_gallery, 'full');
					                               	$output .= '<div class="swiper-slide">';
						                           		if($background_image == 'true') {
							                           		$output .= '<div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div>';
							                           	} else {
							                           		$output .= '<img src="'.esc_url($image_details[0]).'" title="'.esc_html__('Featured Image', 'dtportfolio').'" alt="'.esc_html__('Featured Image', 'dtportfolio').'" />';
							                           	}
					                               	$output .= '</div>';

					                               	array_push($gallery_thumb, '<div class="swiper-slide"><div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div></div>');

												}

											} else {

					                            $image_details = wp_get_attachment_image_src($portfolio_gallery, 'full');
					                           	$output .= '<div class="swiper-slide">';
					                           		if($background_image == 'true') {
						                           		$output .= '<div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div>';
						                           	} else {
						                           		$output .= '<img src="'.esc_url($image_details[0]).'" title="'.esc_html__('Featured Image', 'dtportfolio').'" alt="'.esc_html__('Featured Image', 'dtportfolio').'" />';
						                           	}
					                           	$output .= '</div>';

					                           	array_push($gallery_thumb, '<div class="swiper-slide"><div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div></div>');

							    			}

				                        }
				                    }

		    		$output .= '</div>';

					$output .= '<div class="dtportfolio-swiper-pagination-holder '.$carousel_paginationcolorscheme.' '.$carousel_pagination_designtype.'">';

						if($carousel_arrowformousepointer == 'true') {

							$output .= '<div class="dtportfolio-swiper-arrow-mouse-pointer">
											<div class="dtportfolio-swiper-arrow-left">
												<div class="dtportfolio-swiper-arrow-click left">
													<div class="dtportfolio-swiper-arrow"></div>
												</div>
											</div>
											<div class="dtportfolio-swiper-arrow-middle">
												<div class="dtportfolio-swiper-arrow-click middle">
													<div class="dtportfolio-swiper-arrow"></div>
												</div>
											</div>
											<div class="dtportfolio-swiper-arrow-right">
												<div class="dtportfolio-swiper-arrow-click right">
													<div class="dtportfolio-swiper-arrow"></div>
												</div>
											</div>
										</div>';

						}

						if($carousel_paginationtype == 'bullets') {
							$output .= '<div class="dtportfolio-swiper-bullet-pagination"></div>';	
						}

						if($carousel_paginationtype == 'progressbar') {
							$output .= '<div class="dtportfolio-swiper-progress-pagination"></div>';	
						}	

						if($carousel_scrollbar == 'true') {
							$output .= '<div class="dtportfolio-swiper-scrollbar"></div>';	
						}						

						if(in_array($carousel_pagination_designtype, array ('type2', 'type3'))) {
							$output .= '<div class="dtportfolio-swiper-pagination-wrapper">';
						}

							if($carousel_paginationtype == 'fraction') {
								$output .= '<div class="dtportfolio-swiper-fraction-pagination"></div>';
							}							

							if($carousel_arrowpagination == 'true') {
								$output .= '<div class="dtportfolio-swiper-arrow-pagination '.$carousel_arrowpagination_type.'">';
									$output .= '<a href="#" class="dtportfolio-swiper-arrow-prev">'.esc_html__('Prev', 'dtportfolio').'</a>';
									$output .= '<a href="#" class="dtportfolio-swiper-arrow-next">'.esc_html__('Next', 'dtportfolio').'</a>';
								$output .= '</div>';
							}

							if($carousel_playpausebutton == 'true') {
								if($carousel_autoplay != '' && $carousel_autoplay > 0) {
									$output .= '<a href="#" class="dtportfolio-swiper-playpause pause"><span class="fa fa-pause"></span></a>';
								} else {
									$output .= '<a href="#" class="dtportfolio-swiper-playpause play"><span class="fa fa-play"></span></a>';
								}
							}

						if(in_array($carousel_pagination_designtype, array ('type2', 'type3'))) {
							$output .= '</div>';
						}
					
					$output .= '</div>';

		   		$output .= '</div>';

		   		if($carousel_thumbnailpagination == 'true') {

			   		// Gallery Thumb
					$output .= '<div class="dtportfolio-image-gallery-thumb-container swiper-container">';
					    $output .= '<div class="dtportfolio-image-gallery-thumb swiper-wrapper">';

					    	$output .= implode('', $gallery_thumb);

			    		$output .= '</div>';
			    	$output .= '</div>';

			    }

		   	$output .= '</div>';
			
		}

		echo $output;

	}

	protected function _content_template() {



	}

}