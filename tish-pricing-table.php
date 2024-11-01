<?php
/*
Plugin Name: Tish Pricing Table
Plugin URI: https://tishonator.com/plugins/tish-pricing-table
Description: Effortlessly design and customize interactive pricing tables for your WordPress site with Tish Pricing Table.
Version: 1.0.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Author: tishonator
Author URI: http://tishonator.com/
Contributors: tishonator
Text Domain: tishonator
*/

if ( !class_exists('tishonator_pricingtbl_TishPricingTablePlugin') && !class_exists('tishonator_TishPricingTableProPlugin') ) :

class tishonator_pricingtbl_TishPricingTablePlugin {

    public function __construct() {
        add_action('admin_menu', array($this, 'tishonator_pricingtbl_register_settings_page'));
        add_action('admin_init', array($this, 'tishonator_pricingtbl_register_settings'));
        add_shortcode('tish_pricing_table', array($this, 'tishonator_pricingtbl_render_shortcode'));
    }

    public function tishonator_pricingtbl_register_settings_page() {
	    add_menu_page(
	        'Tish Pricing Table Settings', // page title
	        'Tish Pricing Table', // menu title
	        'manage_options', // capability
	        'tish-pricing-table-pro', // menu slug
	        array($this, 'tishonator_pricingtbl_render_settings_page'), // function to display the page
	        'dashicons-cart', // icon URL (optional)
	        20 // position (optional)
	    );
	}

    public function tishonator_pricingtbl_register_settings() {
	    // Register settings for each pricing plan
	    for ($i = 1; $i <= 3; $i++) {
	        register_setting('tish_pricing_table', 'tish_pricing_table_plan_' . esc_attr($i) . '_name');
	        register_setting('tish_pricing_table', 'tish_pricing_table_plan_' . esc_attr($i) . '_price');
	        register_setting('tish_pricing_table', 'tish_pricing_table_plan_' . esc_attr($i) . '_currency');
	        register_setting('tish_pricing_table', 'tish_pricing_table_plan_' . esc_attr($i) . '_period');
	        register_setting('tish_pricing_table', 'tish_pricing_table_plan_' . esc_attr($i) . '_btn_text');
	        register_setting('tish_pricing_table', 'tish_pricing_table_plan_' . esc_attr($i) . '_btn_url');
	        register_setting('tish_pricing_table', 'tish_pricing_table_plan_' . esc_attr($i) . '_popular');

	        // Register settings for each feature of the pricing plan
	        for ($j = 1; $j <= 5; $j++) {
	            register_setting('tish_pricing_table', 'tish_pricing_table_plan_' . esc_attr($i) . '_feature_' . esc_attr($j) );
	        }
	    }
	}

	public function tishonator_pricingtbl_render_settings_page() {
	    // Check if settings have been saved
	    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') { ?>
	        <div id="message" class="updated notice is-dismissible">
	        	<p>
	        		<?php echo esc_html(__('Settings saved successfully.', 'tishonator')); ?>
	        	</p>
	        </div>
<?php
	    }
	    ?>
	    <div class="wrap">
	        <h1><?php echo esc_html(__('Tish Pricing Table Settings', 'tishonator')); ?></h1>
	        <form method="post" action="options.php">
	            <?php
	            settings_fields('tish_pricing_table');
	            do_settings_sections('tish_pricing_table');
	            ?>
	            <h2 class="nav-tab-wrapper">
	                <?php for ($i = 1; $i <= 3; $i++) : ?>
	                    <?php $plan_name = get_option('tish_pricing_table_plan_' . $i . '_name'); ?>
	                    <a href="#tab-<?php echo esc_attr($i); ?>" class="nav-tab">
	                        <?php printf(esc_html__('Plan %d %s', 'tishonator'), $i, (!empty($plan_name) ? '(' . esc_html($plan_name) . ')' : '')); ?>

	                    </a>
	                <?php endfor; ?>
	            </h2>
	            <?php for ($i = 1; $i <= 3; $i++) : ?>
	                <div id="tab-<?php echo esc_attr($i); ?>" class="tab-content">
	                    <table class="form-table">
	                        <tr valign="top">
	                            <th scope="row"><?php echo esc_html(__('Plan', 'tishonator')); ?> <?php echo esc_attr($i); ?> <?php echo esc_html(__('Name', 'tishonator')); ?></th>
	                            <td>
	                                <input type="text" name="tish_pricing_table_plan_<?php echo esc_attr($i); ?>_name" value="<?php echo esc_attr(get_option('tish_pricing_table_plan_' . esc_attr($i) . '_name')); ?>"  />
	                            </td>
	                        </tr>
	                        <tr valign="top">
	                            <th scope="row">
	                            	<?php echo esc_html(__('Plan', 'tishonator')); ?> <?php echo esc_attr($i); ?> <?php echo esc_html(__('Price', 'tishonator')); ?></th>
	                            <td>
	                                <input type="text" name="tish_pricing_table_plan_<?php echo esc_attr($i); ?>_price" value="<?php echo esc_attr(get_option('tish_pricing_table_plan_' . esc_attr($i) . '_price')); ?>"  />
	                            </td>
	                        </tr>
	                        <tr valign="top">
	                            <th scope="row"><?php echo esc_html(__('Currency', 'tishonator')); ?></th>
	                            <td>
	                                <input type="text" name="tish_pricing_table_plan_<?php echo esc_attr($i); ?>_currency" placeholder="$" value="<?php echo esc_attr( get_option('tish_pricing_table_plan_' . esc_attr($i) . '_currency')); ?>"  />
	                            </td>
	                        </tr>
	                        <tr valign="top">
	                            <th scope="row"><?php echo esc_html(__('Pricing Period', 'tishonator')); ?></th>
	                            <td>
	                                <input type="text" name="tish_pricing_table_plan_<?php echo esc_attr($i); ?>_period" placeholder="year" value="<?php echo esc_attr(get_option('tish_pricing_table_plan_' . esc_attr($i) . '_period')); ?>"  />
	                            </td>
	                        </tr>
	                        <?php for ($j = 1; $j <= 5; $j++) : ?>
	                            <tr valign="top">
							        <th scope="row"><?php echo esc_html(__('Feature', 'tishonator')); ?> <?php echo esc_attr($j); ?></th>
							        <td>
							        	<label for="tish_pricing_table_plan_<?php echo esc_attr($i); ?>_feature_<?php echo esc_attr($j); ?>"><?php echo esc_html(__('Feature Name', 'tishonator')); ?>:</label>
							            <br>
							            <input type="text" name="tish_pricing_table_plan_<?php echo esc_attr($i); ?>_feature_<?php echo esc_attr($j); ?>"
							            value="<?php echo esc_attr(get_option('tish_pricing_table_plan_' . esc_attr($i) . '_feature_' . esc_attr($j) )); ?>" />
							        </td>
							    </tr>
	                        <?php endfor; ?>
	                        <tr valign="top">
	                            <th scope="row"><?php echo esc_html(__('Purchase Button Text', 'tishonator')); ?></th>
	                            <td>
	                                <input type="text" name="tish_pricing_table_plan_<?php echo esc_attr($i); ?>_btn_text" placeholder="Purchase" value="<?php echo esc_attr(get_option('tish_pricing_table_plan_' . esc_attr($i) .'_btn_text')); ?>" />
	                            </td>
	                        </tr>
	                        <tr valign="top">
	                            <th scope="row"><?php echo esc_html(__('Purchase Button URL', 'tishonator')); ?></th>
	                            <td>
	                                <input type="text" name="tish_pricing_table_plan_<?php echo esc_attr($i); ?>_btn_url" value="<?php echo esc_attr(get_option('tish_pricing_table_plan_' . esc_attr($i) . '_btn_url')); ?>" />
	                            </td>
	                        </tr>
	                        <tr valign="top">
	                            <th scope="row"><?php echo esc_html(__('Mark as Most Popular Pricing Plan', 'tishonator')); ?></th>
	                            <td>
	                                <input type="checkbox" name="tish_pricing_table_plan_<?php echo esc_attr($i); ?>_popular" <?php checked(get_option('tish_pricing_table_plan_' . esc_attr($i) . '_popular'), 'on'); ?> />
	                            </td>
	                        </tr>
	                    </table>
	                </div>
	            <?php endfor; ?>

	            <h3>
                  <?php echo esc_html(__('Usage', 'tishonator')); ?>
                </h3>
                <p>
                    <?php echo esc_html(__('Use the shortcode', 'tishonator')); ?> <code>[tish_pricing_table]</code> <?php echo esc_html(__( 'to display your Pricing table to any page or post.', 'tishonator' )); ?>
                </p>

	            <?php submit_button(); ?>
	        </form>
	    </div>
	    <script>
	        jQuery(document).ready(function($) {
	            $('.tab-content').hide();
	            $('h2.nav-tab-wrapper a:first').addClass('nav-tab-active');
	            $('.tab-content:first').show();
	            $('h2.nav-tab-wrapper a').click(function(e) {
	                e.preventDefault();
	                $('h2.nav-tab-wrapper a').removeClass('nav-tab-active');
	                $(this).addClass('nav-tab-active');
	                $('.tab-content').hide();
	                $($(this).attr('href')).show();
	            });
	        });
	    </script>
	    <?php
	}

    public function tishonator_pricingtbl_render_shortcode() {
        ob_start();
        $this->tishonator_pricingtbl_render_pricing_table();
        return ob_get_clean();
    }

    public function tishonator_pricingtbl_render_pricing_table() { ?>
	    <div class="tish-pricing-table-pro" id="tish-pricing-table-pro">
	        <?php for ($i = 1; $i <= 3; $i++) : ?>
	            <?php 
	            $plan_name = get_option('tish_pricing_table_plan_' . $i . '_name');
	            $plan_price = get_option('tish_pricing_table_plan_' . $i . '_price');
	            $currency = get_option('tish_pricing_table_plan_' . $i . '_currency');
	            $period = get_option('tish_pricing_table_plan_' . $i . '_period');
	            $btn_url = get_option('tish_pricing_table_plan_' . $i . '_btn_url');
	            $btn_text = get_option('tish_pricing_table_plan_' . $i . '_btn_text');
	            $popular = get_option('tish_pricing_table_plan_' . $i . '_popular') == 'on' ? 'popular' : '';

	            if (!empty($plan_name) && !empty($plan_price)): ?>

	                <div class="plan plan-<?php echo esc_attr($i); ?> <?php echo esc_attr($popular); ?>">
	                    <h2 class="plan-title"><?php echo esc_html($plan_name); ?></h2>
	                    <div class="plan-price"><span class="price-currency"><?php echo esc_attr($currency); ?></span> <?php echo esc_attr($plan_price); ?> <span class="price-period">/ <?php echo esc_attr($period); ?></span></div>
	                    <?php for ($j = 1; $j <= 5; $j++) : ?>
	                        <?php 
	                        $feature = get_option('tish_pricing_table_plan_' . $i . '_feature_' . $j);
    
	                        if (!empty($feature)): ?>
	                            <div class="plan-features">
	                            	<?php echo esc_html($feature); ?>
	                            </div>
	                        <?php endif; ?>
	                    <?php endfor; ?>

	                    <?php if (!empty($btn_url) && !empty($btn_text)) : ?>
	                        <a href="<?php echo esc_url($btn_url); ?>" class="plan-purchase-btn"><?php echo esc_html($btn_text); ?></a>
	                    <?php endif; ?>
	                </div>
	            <?php endif; ?>
	        <?php endfor; ?>
	    </div>
	    <?php

	    // CSS
	    wp_register_style('tish-pricing-styles', plugins_url('css/tish-pricing-styles.css', __FILE__), true);
	    wp_enqueue_style('tish-pricing-styles', plugins_url('css/tish-pricing-styles.css', __FILE__), array());
	}
}

function tishonator_pricingtbl_pricing_table() {
    return new tishonator_pricingtbl_TishPricingTablePlugin();
}

// Initialize the plugin
tishonator_pricingtbl_pricing_table();

endif;
