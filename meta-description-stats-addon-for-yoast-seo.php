<?php
/*
Plugin Name: Meta Description Stats Addon for Yoast SEO
Plugin URI: http://www.netattingo.com/
Description: We have developed add-on for Yoast SEO plugin that enhances the current feature to next level.
Author: NetAttingo Technologies
Version: 1.0.0
Author URI: http://www.netattingo.com/
*/

define('WP_DEBUG',true);
//initilize constant
define('MKS_DIR', plugin_dir_path(__FILE__));
define('MKS_URL', plugin_dir_url(__FILE__));
define('MKS_PAGE_DIR', plugin_dir_path(__FILE__).'pages/');
define('MKS_INCLUDE_URL', plugin_dir_url(__FILE__).'includes/');

//Include menu and assign page
function mdsysa_plugin_menu() {
    $icon = MKS_URL. 'includes/icon.png';
	add_menu_page("Meta Description Stats", "Meta Description Stats", "administrator", "description-stats", "mdsysa_plugin_pages", $icon ,31);
	add_submenu_page("description-stats", "About Us", "About Us", "administrator", "about-us", "mdsysa_plugin_pages");
}
add_action("admin_menu", "mdsysa_plugin_menu");

function mdsysa_plugin_pages() {

   $itm = MKS_PAGE_DIR.$_GET["page"].'.php';
   include($itm);
}

//add admin css
function mdsysa_admin_css() {
  wp_register_style('mdsysa_admin_css', plugins_url('includes/mdsysa-admin-style.css',__FILE__ ));
  wp_enqueue_style('mdsysa_admin_css');
}
add_action( 'admin_init','mdsysa_admin_css');

//function for pagination
 function mdsysa_pagination($pages = '', $range = 4)
{ 
     $showitems = ($range * 2)+1; 
     //global $paged;
	 $paged = (sanitize_text_field( $_GET['paged'] )) ? sanitize_text_field( $_GET['paged'] ) : 1;
     if(empty($paged)) $paged = 1;
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }  
     if(1 != $pages)
     {
         echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;&lsaquo;</a>"; //previous
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">&rsaquo;&rsaquo;</a>"; //next
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}


//function for post cout by meta key
function mdsysa_get_post_count( $kword){
  
    $post_nct='';
	global $wpdb;
	ob_start();
	//all post types array
	$post_type_to_exclude=array('attachment','revision' , 'nav_menu_item');
	$post_type_to_include=array();
	$all_post_types = get_post_types( '', 'names' ); 
	foreach ( $all_post_types as $post_type ) {
		 if ( !in_array($post_type, $post_type_to_exclude)) {
			$post_type_to_include[]= $post_type;
		 }
	}
	
	$args = array(
		   'post_type' => $post_type_to_include,
		   'meta_key' => '_yoast_wpseo_metadesc',
		   'post_status'       => 'publish',
		   'posts_per_page' => -1,
		   'meta_query' => array(
			   array(
				   'key' => '_yoast_wpseo_metadesc',
				   'value' =>  $kword,
				   'compare' => '=',
			   )
		   )
		 );
	$the_query = new WP_Query( $args );	 
	$post_nct= $the_query->found_posts;
	return $post_nct; 
}

?>