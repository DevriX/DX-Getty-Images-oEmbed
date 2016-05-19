<?php

/**
 * Plugin Name: DX Getty Images oEmbed
 * Plugin URI: http://devrix.com/
 * Description: Just paste a link from Getty Images and it will embed the image in your content
 * Version: 1.0.0
 * Author: DevriX
 * Author URI: http://devrix.com/
 * Text Domain: dx-getty-images
 * Domain Path: /lang
 */
if ( ! class_exists( 'DX_Getty_Images_oEmbed' ) ) :
	
class DX_Getty_Images_oEmbed {
	
	public function __construct( ) {
		// filter for the content to parce the GettyImages from link in the post content
		add_filter( 'the_content', array( $this, 'parce_getty_images' ) );
	}
	
	/**
	 * Method for the_content hook to parce getty_images
	 *
	 * @param string $the_content
	 *        	Post content
	 * @return string The parced with getty_images post content
	 */
	public function parce_getty_images( $the_content ) {
		if ( empty( $the_content ) ) {
			return $the_content;
		}
		$content = strip_tags( $the_content );
		$regex = '/(https?:\/\/(www\.gettyimages\.com)?[a-z0-9\.:].*?(?=\s|$))/'; // all links from the gettyimages.com sepereted with the any white space or end of the string
		$matches = array();
		preg_match_all( $regex, $content, $matches, PREG_SET_ORDER );
		
		$caller = get_site_url();
		if ( ! empty( $matches ) ) {
			foreach ( $matches as $item ) {
				if ( ! empty( $item[1] ) ) {
					$url = $item[1];
					$responce = wp_remote_get( "http://embed.gettyimages.com/oembed?url=$url&caller=$caller" );
					if ( ! empty( $responce ) && ! is_wp_error( $responce ) ) {
						$data = json_decode( $responce['body'] );
						$the_content = str_replace( $url, $data->html, $the_content );
						$qwe = $data;
					}
				}
			}
		}
		
		return $the_content;
	}

}

add_action( 'plugins_loaded', function ( ) {
	new DX_Getty_Images_oEmbed();
} );

endif;
