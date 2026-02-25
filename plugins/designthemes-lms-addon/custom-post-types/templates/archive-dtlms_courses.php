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
            $attrs = array (
                

                            'disable-all-filters'            => '',

                            'enable-search-filter'           => 'true',
                            'enable-display-filter'          => 'true',
                            'enable-orderby-filter'          => 'true',
                            'enable-category-filter'         => 'true',
                            'enable-instructor-filter'       => 'true',
                            'enable-cost-filter'             => 'true',
                            'enable-date-filter'             => 'true',

                            'listing-output-page'            => '',

                            'default-filter'                 => '',
                            'default-display-type'           => 'grid',
                            'course-item-ids'                => '',
                            'course-category-ids'            => '',
                            'instructor-ids'                 => '',

                            'apply-isotope'                  => '',
                            'enable-category-isotope-filter' => '',

                            'show-author-details'            => 'true',
                            
                            'post-per-page'                  => '6',
                            'columns'                        => 2,

                            'enable-fullwidth'               => '',
                            'type'                           => 'type6',
                            'show-description'               => '',
                            'hide-labels'                    => '',
                            'hide-category'                  => '',
                            'hide-addtocartbutton'           => '',
                            
                            'class'                          => '',

                            'enable-carousel'                => '',
                            'carousel-effect'                => '',
                            'carousel-autoplay'              => 0,
                            'carousel-slidesperview'         => 2,
                            'carousel-loopmode'              => '',
                            'carousel-mousewheelcontrol'     => '',
                            'carousel-bulletpagination'      => 'true',
                            'carousel-arrowpagination'       => '',
                            'carousel-spacebetween'          => 0,

            );

            echo dtlms_courses_listing_content($attrs);
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