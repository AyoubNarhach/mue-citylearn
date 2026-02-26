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
        $portfolio_archive_pageid = $post->ID;

        $post_layout = cs_get_option('portfolio-archives-post-layout');
        $post_layout = isset( $post_layout ) ? $post_layout : 'dtportfolio-one-fourth-column';

        $hover_style = cs_get_option('portfolio-hover-style');
        $hover_style = isset( $hover_style ) ? $hover_style : '';

        $cursor_hover_style = cs_get_option('portfolio-cursor-hover-style');
        $cursor_hover_style = isset( $cursor_hover_style ) ? $cursor_hover_style : '';  

        $allow_space = cs_get_option('portfolio-grid-space');
        $allow_space = ($allow_space == 'true') ? 'true' : '';

        $allow_fullwidth = cs_get_option('portfolio-allow-full-width');
        $allow_fullwidth = ($allow_fullwidth == 'true') ? 'true' : '';

        $postperpage = cs_get_option('portfolio-post-per-page');
        $postperpage = ($postperpage != '') ? $postperpage : -1;

        $disable_item_options = cs_get_option('portfolio-disable-item-options');
        $disable_item_options = ($disable_item_options == 'true') ? true : false; 

        $tpl_default_settings = array();
        $tpl_default_settings['portfolio-post-layout'] = $post_layout;
        $tpl_default_settings['portfolio-hover-style'] = $hover_style;
        $tpl_default_settings['portfolio-cursor-hover-style'] = $cursor_hover_style;
        if($allow_space != '') {
          $tpl_default_settings['portfolio-grid-space'] = $allow_space;
        }
        if($allow_fullwidth != '') {
          $tpl_default_settings['enable-fullwidth'] = $allow_fullwidth;
        }        
        $tpl_default_settings['portfolio-post-per-page'] = $postperpage;
        if($disable_item_options) {
          $tpl_default_settings['portfolio-disable-item-options'] = 'true';
        }
        $tpl_default_settings['portfolio-pagination-type'] = 'numbered-pagination';

        echo dtportfolio_portfolio_lists($portfolio_archive_pageid, $tpl_default_settings, false); 
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