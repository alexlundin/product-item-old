<?php
/**
 * Fired during plugin activation
 *
 * @link       https://alexlundin.com
 * @since      1.0.0
 *
 * @package    Product_Item
 * @subpackage Product_Item/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Product_Item
 * @subpackage Product_Item/includes
 * @author     Александр Лундин <aslundin@ya.ru>
 */
class Product_Item_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::create_database_table();
	}

	/**
	 * Create Table for datatable which will hold the primary info of a table
	 *
	 * @since    1.0.0
	 */
	public static function create_database_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();$table_name = $wpdb->prefix . products_db_table_name();
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$sql
				= /**
					 * Structure tables
					 *
					 * @lang sql
			 	*/
				"CREATE TABLE $table_name (
    			id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    			lang varchar(10) NOT NULL,
    			product_id int(11) NOT NULL,
    			owner_id int(11),
    			attribute varchar(255) NOT NULL,
    			content longtext,
    			created_at timestamp NULL,
    			updated_at timestamp NULL 
				) $charset_collate;";

			require_once( ABSPATH . "wp-admin/includes/upgrade.php" );
			dbDelta( $sql );
		}
	}

}
