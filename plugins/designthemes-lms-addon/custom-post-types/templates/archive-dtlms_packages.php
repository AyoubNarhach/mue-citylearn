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
                            
                                        'display-type' => 'grid',
                                        'post-per-page' => '-1',
                                        'columns' => 2,
                                        'apply-isotope' => '',  
                                        'type' => 'type1',      

                                        'enable-carousel' => '',
                                        'carousel-effect' => '',
                                        'carousel-autoplay' => 0,
                                        'carousel-slidesperview' => 2,
                                        'carousel-loopmode' => '',
                                        'carousel-mousewheelcontrol' => '',
                                        'carousel-bulletpagination' => 'true',
                                        'carousel-arrowpagination' => '',
                                        'carousel-spacebetween' => 0,                                        

                                );

                        echo dtlms_packages_listing_content($attrs);
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