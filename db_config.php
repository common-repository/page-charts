<?php
global $tab_db_version;
$tab_db_version = "1.0";

function pagch_tab_install () {
    global $wpdb;
    global $tab_db_version;

    $table_name = $wpdb->prefix . "hchart_names";
    $charset_collate = $wpdb->get_charset_collate();

    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE ".DB_NAME.".".$table_name." (
              id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              name varchar(50) DEFAULT NULL,
              chart_nm int(11) DEFAULT NULL,
              js_name varchar(255) DEFAULT NULL,
              php_name varchar(255) DEFAULT NULL,
              post_type varchar(255) DEFAULT NULL,
              PRIMARY KEY  (id)
            )
            $charset_collate;";
        $wpdb->query($sql);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $resdb = dbDelta($sql);
        $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
        add_option("tab_db_version", $tab_db_version);
    }
	
	 $library_name = $wpdb->prefix . "charts_js_library";
	
	if($wpdb->get_var("show tables like '$library_name'") != $library_name) {
        $sql = "CREATE TABLE ".DB_NAME.".".$library_name." (
               id int(11) NOT NULL DEFAULT 1,
               library varchar(255),
               PRIMARY KEY (id)
            )
            $charset_collate;";
        $wpdb->query($sql);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $resdb = dbDelta($sql);
        $rows_affected = $wpdb->insert( $library_name, array( 'library' => ''));
 	}
}
?>