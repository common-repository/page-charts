<?php
/*
Plugin Name: Page Charts
Description: Javascript charts for wordpress.
Version: 1.0
Author: Sidov

*/

global $wpdb;
define( 'PAGCH_ROOT', plugin_dir_url( __FILE__ ) );
define ('PAGCH_CHART_TABLE',$wpdb->prefix .'hchart_names');
define ('PAGCH_CHARTS_JS_LIBRARY',$wpdb->prefix .'charts_js_library');
require plugin_dir_path( __FILE__ ) . 'db_config.php';
register_activation_hook( __FILE__, 'pagch_tab_install' );

add_action( 'admin_menu', 'pagch_chart_add_admin_page' );
function pagch_chart_add_admin_page() {
	add_plugins_page(
        'Charts For WordPress',
        'Page Charts',
        'administrator',
        'page-charts',
        'pagch_render_admin_charts_page'
    );
}
 
function pagch_render_admin_charts_page() {
	global $pagch_allPostsArr;
	global $wpdb;
	$chartLibraries = $wpdb->get_results( "SELECT * FROM ".PAGCH_CHARTS_JS_LIBRARY." WHERE id=1");
	if(is_array($chartLibraries)){
		foreach ($chartLibraries as $key =>$libraryFile){
			$PAGCH_libraryFile = $libraryFile->library;
			if(strlen($PAGCH_libraryFile)<1){
				$PAGCH_libraryFile =  "Please fill this field";
			}
			$selChart = '';
			$selHighCh = '';
			if($PAGCH_libraryFile == 'chart'){
				$selChart = 'selected';
			}elseif($PAGCH_libraryFile == 'highcharts'){
				$selHighCh = 'selected';
			}else{
				$defSelect = 'selected';
			}
?>
			<label for="submit_library" class = "submit_library">Choose a chart library:</label><select  name="submit_library" id="submit_library" ><option <?php esc_attr_e($defSelect);?> = value="" style="color:red" >Do not chosen</option><option <?php esc_attr_e($selChart);?> value="chart" style="color:black">Chart</option><option <?php esc_attr_e($selHighCh);?> value="highcharts" style="color:black">Highcharts</option></select>
<?php
		}
	}
	
	$allPages = $wpdb->get_results( "SELECT post_title,post_type FROM ".$wpdb->prefix .'posts'." WHERE (post_type = 'page' OR post_type = 'post')AND post_status = 'publish'");
	$chartPages = $wpdb->get_results( "SELECT * FROM ".PAGCH_CHART_TABLE." ORDER BY name ASC");
?>
	<div id = "div_table"><table id = "chart_table" root = "<?php esc_attr_e(PAGCH_ROOT);?>"><tr class = "tbHeader"><th><span >Names of posts or pages</span></th><th>PHP files</th><th>Javascript files</th><th></th><th></th><th></th></tr>
<?php
	if(is_array($chartPages)){
		foreach ($chartPages as $key =>$chartPage){
			$php_file = $chartPage->php_name;
			$js_file = $chartPage->js_name;
?>
			<tr id = "<?php esc_attr_e($chartPage->id);?>" ><td><span name = "<?php esc_attr_e($chartPage->name);?>" post_type = "<?php esc_attr_e($chartPage->post_type);?>" class = "post_names"> <?php esc_html_e($chartPage->name);?>(<?php esc_html_e($chartPage->post_type);?>) </span> </td><td><input type = "text" value = "<?php esc_attr_e($php_file)?>" chart_nm =" <?php esc_attr($chartPage->chart_nm); ?>" saved = "<?php esc_attr_e($php_file);?>"><input type="submit" value="change" id="submit_php<?php esc_attr_e($key);?>" name="submit_php<?php esc_attr_e($key); ?>" class = "save_post change"></td><td><input type = "text" value = "<?php esc_attr_e($js_file);?>" chart_nm =" <?php esc_attr($chartPage->chart_nm); ?>" class = "js_file"><input type="submit" value="change" id="submit_js<?php esc_attr_e($key);?>" name="submit_js<?php esc_attr_e($key);?>" class = "save_post change"></td> <td><input type="submit" value="duplicate" id="new_chart<?php esc_attr_e($key);?>" name="new_chart<?php esc_attr_e($key);?>" class = "save_post duplicate"></td><td><input type="submit" value="preview the chart" id="show_chart<?php esc_attr_e($chartPage->id);?>" name="show_chart " class = "show_chart"></td><td><input type="submit" value="delete the chart" id="delete_chart<?php esc_attr_e($key);?>" name="delete_chart<?php esc_attr_e($key);?>" class = "save_post delete"></td></tr>
<?php
		}
    }
	if(is_array($allPages)){
		foreach ($allPages as $page){ 
		$pagch_allPostsArr[]=  $page->post_title;
			$exists = 0;
			if(is_array($chartPages)){		
				foreach ($chartPages as $chartPage){	
						if($page->post_title === $chartPage->name){
							$exists = 1;
							break;
						}
				}
			}
			if($exists == 1) continue;
			else{
				if($page->post_title != null){
?>
					<tr><td><span name = "<?php esc_attr_e($page->post_title);?>" post_type = "<?php esc_attr_e($page->post_type);?>" class = "new_post"><?php esc_attr_e($page->post_title);?>(<?php esc_attr_e($page->post_type);?>)</span></td><td> <input type="submit" value="add new chart" class = "save_post add"></td><td></td><td></td><td></td></tr>
					<?php
				}
				}
			}
	}
?>
	<tr></tr></table><br>
	<div id = "chart_append" style = "display:none;">Id of the container's name was not found in the script  <br>Please put the name<br><input type = "text" value = ""  id="cont_name"><input type="button" value="Save" id="subnum" class ="subnum"  ></div>
<?php
	$container = '';
	if($PAGCH_libraryFile == "highcharts"){
		$container = '<div id = "container"  class = "container" style = "float:left; width:900px; height:500px;"></div>';
		?>
		<div id = "container"  class = "container" style = "float:left; width:900px; height:500px;"></div>
		<?php
	}elseif($PAGCH_libraryFile == "chart"){
		?>
		<div style="width:800px; height = 450px !important;"><canvas id="container"  class = "container" width="800" height="450"></canvas></div>
<?php
	}
}
	
add_action( 'admin_enqueue_scripts', 'pagch_chart_register_scripts' );
function pagch_chart_register_scripts() {
	global $wpdb;
	$chartLibraries = $wpdb->get_results( "SELECT * FROM ".PAGCH_CHARTS_JS_LIBRARY." WHERE id=1");
	if(is_array($chartLibraries)){
		foreach ($chartLibraries as $key =>$libraryFile){
			$PAGCH_libraryFile = $libraryFile->library;
		}
	}
	$allLibraries = array('highcharts','chart');
	if(in_array($PAGCH_libraryFile,$allLibraries)){
	
		wp_register_script(
			'highCharts',
			PAGCH_ROOT .'js/'.$PAGCH_libraryFile.'.js',
	        array( 'jquery' ),
			'3.0',	
			true
		);
	    add_action( 'admin_enqueue_scripts', 'pagch_enqueue_library' );	
	}	
	if(in_array($PAGCH_libraryFile,$allLibraries)){$libArr = array( 'highCharts' );}else $libArr = null;
		wp_register_script(
			'pageCharts',
			PAGCH_ROOT .'js/page_charts.js',
			$libArr,
			'1.0',
			true
		);
		wp_register_style(
			'ChartsStyles',
			PAGCH_ROOT .'css/chart.css'
		);
}

function pagch_enqueue_library(  ) {
	wp_enqueue_script( 'highCharts' );
}	
add_action( 'admin_enqueue_scripts', 'pagch_enqueue_scripts' );
function pagch_enqueue_scripts(  ) {
	wp_enqueue_style( 'ChartsStyles' );
    wp_enqueue_script( 'pageCharts' );
}	

add_action( 'wp_ajax_save_chart', 'pagch_save_chart_callback' );
function pagch_save_chart_callback(){
	global $wpdb;
	$allPages = $wpdb->get_results( "SELECT post_title,post_type FROM ".$wpdb->prefix .'posts'." WHERE (post_type = 'page' OR post_type = 'post')AND post_status = 'publish'");
	$allPostsArr = array();
	if(is_array($allPages)){
		foreach ($allPages as $page){ 
			$allPostsArr[]=  $page->post_title;
		}
	}
	$pType = array('page','post');
	global $wpdb;
	$name =  sanitize_text_field($_POST['page_name']);
	$fileName = sanitize_text_field($_POST['file_name']);
	$chart_id = (int)$_POST['chart_id'];
	$post_type =  sanitize_text_field($_POST['post_type']);
	$file_type =  sanitize_text_field($_POST['file_type']);
	
	switch ($fileName){
	case  'add new chart':
	    if(!in_array($post_type,$pType))wp_die();
		if(!in_array($name,$allPostsArr))wp_die();
		$data = array('name'=>$name,'chart_nm'=>1,'post_type'=>$post_type);
		$res = $wpdb->insert(PAGCH_CHART_TABLE, $data);
		if ($res === false){
			$res = 'New chart was not included';
		}
		break;
	case  'delete the chart':
		$data = array('id'=>$chart_id);
		$wpdb->delete( PAGCH_CHART_TABLE, $data, [ '%d' ] );
		break;
	case  'duplicate':
	    if(!in_array($name,$allPostsArr))wp_die();
	    if(!in_array($post_type,$pType))wp_die();
	    $countQuery = "SELECT count(*) FROM ".PAGCH_CHART_TABLE." WHERE name = '".$name."'" ;
	    $count = $wpdb->get_var($countQuery);
		++$count;
		$data = array('name'=>$name,'chart_nm'=>$count,'post_type'=>$post_type);
		$res = $wpdb->insert(PAGCH_CHART_TABLE, $data);
		if (!$res){
			$res = 'New chart was not duplicated';
		}
		break;
	case  'show_chart':
		$chart_id =  (int)$_POST['chart_id'];
		$js_name =  sanitize_text_field($_POST['js_name']);
		$js_name = pagch_validate($js_name);
		if($js_name == null||$js_name ==''){
	        wp_die();
		}
		$php_name =  sanitize_text_field($_POST['php_name']);
		$php_name = pagch_validate($php_name);
		if(file_exists( plugin_dir_path(__FILE__) .'js/'.$js_name.'.js') == false){
			$data['plagin_error'] = 'file js/'.$js_name.'.js does not exist';
			wp_send_json($data); 
	        wp_die();
		}
		
		wp_register_script(
            'highCharts'.$chart_id,
            PAGCH_ROOT .'js/'.$js_name.'.js',
            array('highCharts'),
            '1.0',
            true
        );
		wp_enqueue_script( 'highCharts'.$chart_id );
		if(file_exists( plugin_dir_path(__FILE__) .'php/'. $php_name.'.php') == false){
		    $data['plagin_error'] = 'file php/'. $php_name.'.php does not exist';
			wp_send_json($data); 
	        wp_die();
		}
		
		include plugin_dir_path( __FILE__ ) .'php/'. $php_name.'.php';
		$data = pagch_sanitize_text_field($data);
		
		wp_localize_script( 'highCharts'.$chart_id, 'data', $data );
		wp_send_json($data); 
	    wp_die();
		break;	
	default :
	$fileName = pagch_validate($fileName);
		if(substr($file_type,0,7) == 'submit_'){
			$fType = substr($file_type,7,2);
			switch($fType){
				case 'ph':
				$data = array('php_name'=>$fileName);
				break;
				case 'js':
				$data = array('js_name'=>$fileName);
				break;
			}
			$format = array('id'=>$chart_id);
			$wpdb->update(PAGCH_CHART_TABLE, $data, $format);
		}
		break;
	}
}

add_action( 'wp_ajax_save_library', 'pagch_save_library_callback' );
function pagch_save_library_callback(){
	global $wpdb;
	$allLibraries = array('highcharts','chart','');
	$libraryName =  sanitize_text_field($_POST['library_name']);
	if(!in_array($libraryName,$allLibraries))wp_die();
	$libraryId = 1;
	$data = array('library'=>$libraryName);
	$format = array('id'=>$libraryId);
	$wpdb->update(PAGCH_CHARTS_JS_LIBRARY, $data, $format);
}

add_action( 'wp_enqueue_scripts', 'pagch_register_scripts' );
function pagch_register_scripts() {
	global $wpdb;
	$chartLibraries = $wpdb->get_results( "SELECT * FROM ".PAGCH_CHARTS_JS_LIBRARY." WHERE id=1");
	if(is_array($chartLibraries)){
		foreach ($chartLibraries as $key =>$libraryFile){
			$PAGCH_libraryFile = $libraryFile->library;
		}
	}
	
    wp_register_script(
        'highChartsP',
        PAGCH_ROOT .'js/'.$PAGCH_libraryFile.'.js',
        array( 'jquery' ),
        '3.0',
        true
    );
}

add_action('wp_head','pagch_render_chart');
function pagch_render_chart(){
	global $post,$wpdb;
	wp_enqueue_script( 'highChartsP' );
    $chartPages = $wpdb->get_results( "SELECT * FROM ".PAGCH_CHART_TABLE );
	foreach($chartPages as $key=>$dName)
	{
	    if($post->post_title == $dName->name){
			if($dName->js_name == null) continue;
		    wp_register_script(
               'highCharts'.$key,
               PAGCH_ROOT .'js/'.$dName->js_name.'.js',
               array('highChartsP'),
               '1.0',
               true
            );
		    wp_enqueue_script( 'highCharts'.$key );
			if($dName->php_name == null) continue;
		    include plugin_dir_path( __FILE__ ) .'php/'. $dName->php_name.'.php';
			$data = pagch_sanitize_text_field($data);
			wp_localize_script( 'highCharts'.$key, 'data', $data );
	    }	 
    }
}
function pagch_validate($text){
	$del = array(' ','{','}','[',']','(',')','"',"'",'+','$','^',':',';','~','/');
	$text = str_replace($del,'',$text);
	$text = substr($text,0,15);
	return $text;
}

function pagch_sanitize_text_field($array) {
    foreach ( $array as $key => &$value ) {
        if ( is_array( $value ) ) {
            $value = pagch_sanitize_text_field($value);
        }
        else {
			if(is_int($value) ||is_float($value)){}
            else $value = sanitize_text_field( $value );
        }
    }
    return $array;
}
?>