<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}
/**
 * Unsubscribe Form Shortcode
 *
 * 
 * @author 		SendPress
 * @category 	Shortcodes
 * @version     0.9.9.4
 */
class SendPress_SC_Recent_Posts extends SendPress_SC_Base {

	public static function title(){
		return __('Get Recent Posts', 'sendpress');
	}

	public static function options(){
		return 	array(
			 'posts' => 1,
			 'uid' => 0,
			 'imgalign' => 'left',
			 'alternate' => false
			);
	}

	public static function html(){
		return __('You can provide a Title. This is added before the post loop begins.','sendpress');
	}
	/**
	 * Output the form
	 *
	 * @param array $atts
	 */
	public static function output( $atts , $content = null ) {
		global $post , $wp;
		$old_post = $post;
		extract( shortcode_atts( self::options() , $atts ) );

		$args = array('orderby' => 'date', 'order' => 'DESC' , 'showposts' => $posts, 'post_status' => 'publish');

		if($uid > 0){
			$args['author'] = $uid;
		}

		if(strlen($readmoretext) === 0){
			$readmoretext = 'Read More';
		}

		if(strlen($imgalign) === 0){
			$imgalign = 'left';
		}

		$return_string = '';
	   	if($content){
	      	$return_string = $content;
	  	}

	  	//$margin = ($alternate && strtolower($imgalign) === 'left') ? '0px 10px 10px 0px' : '0px 0px 10px 10px';

	  	//$return_string .= '<div>';
	   	//query_posts($args);

	  	$template = SendPress_Data::post_text_only();

	   	$query = new WP_Query($args);
		if($query->have_posts()){
			while($query->have_posts()){
				$query->the_post();

				if(has_post_thumbnail()){
					//reset the template because we have an image
					$template = (strtolower($imgalign) === 'left') ? SendPress_Data::post_img_left() : SendPress_Data::post_img_right();
					$img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'single-post-thumbnail' );
					$template = str_replace('{sp-post-image}',$img[0],$template);
				}

				$template = str_replace('{sp-post-link}',get_permalink(),$template);
				$template = str_replace('{sp-post-title}',get_the_title(),$template);
				$template = str_replace('{sp-post-excerpt}',get_the_excerpt(),$template);
				$template = str_replace('{sp-post-readmore}',$readmoretext,$template);

	          	$imgalign = ($alternate && strtolower($imgalign) === 'left') ? 'right' : 'left';




	          	$return_string .= $template;
			}
		}
		wp_reset_postdata();

	   	//$return_string .= '</div>';
	   	wp_reset_query();
	   	$post = $old_post;
	   	return $return_string;

	}

	public static function docs(){
		return __('This shortcode creates a listing of Posts in emails or on pages.  Use the following options to customize the output: <br><br><b>posts</b> - number of poists to display. (defaults to 1)<br><b>uid</b> - the user id of the author you would like to see.<br><b>imgalign</b> - Align images left or right. (defaults to left)<br><b>alternate</b> - when writing posts, alternate the thumbnail images. (defaults to false)<br><b>readmoretext</b> - the text for the readmore link (defaults to Read More)', 'sendpress');
	}


}
