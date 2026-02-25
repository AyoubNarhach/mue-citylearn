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
			echo '<div class="dtlms-container">';
				echo '<h2>'.esc_html__('Direct access is not allowed!', 'dtlms').'</h2>';
			echo '</div>';
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