<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://alexlundin.com
 * @since      1.0.0
 *
 * @package    Product_Item
 * @subpackage Product_Item/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Product_Item
 * @subpackage Product_Item/admin
 * @author     Александр Лундин <aslundin@ya.ru>
 */
class Product_Item_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

    /**
     * Custom Post Type Name
     *
     * @since    1.0.0
     * @access   private
     * @var      string $cpt_name .
     */
    private $cpt_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->cpt_name = 'wares';

	}

    /**
     * Register form post types
     *
     * @return void
     */
    public function register_post_type()
    {
        $args = array(
            'label'               => __('Product Items', 'product-item'),
            'public'              => false,
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'query_var'           => false,
            'supports'            => array('title', 'editor'),
            'labels'              => array(
                'name'               => __('Product Items', 'product-item'),
                'singular_name'      => __('Product', 'product-item'),
                'menu_name'          => __('Product Items', 'product-item'),
                'add_new'            => __('Add Product', 'product-item'),
                'add_new_item'       => __('Add New Product', 'product-item'),
                'edit'               => __('Edit', 'product-item'),
                'edit_item'          => __('Edit Product', 'product-item'),
                'new_item'           => __('New Product', 'product-item'),
                'view'               => __('View Product', 'product-item'),
                'view_item'          => __('View Product', 'product-item'),
                'search_items'       => __('Search Product', 'product-item'),
                'not_found'          => __('No Product Found', 'product-item'),
                'not_found_in_trash' => __('No Product Found in Trash', 'product-item'),
                'parent'             => __('Parent Product', 'product-item'),
            ),
        );
        register_post_type($this->cpt_name, $args);
    }


    /**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Product_Item_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Product_Item_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/product-item-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Product_Item_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Product_Item_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/product-item-admin.js', array( 'jquery' ), $this->version, false );

	}

}
