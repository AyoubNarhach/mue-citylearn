<?php
namespace DTPortfolioElementor\Widgets;

use Elementor\Widget_Base;

abstract class DTPortfolioElementorWidgetBase extends Widget_Base {

	/**
	 * Get categories
	 */
	public function get_categories() {
		return [ 'dt-portfolio-widgets' ];
	}
}