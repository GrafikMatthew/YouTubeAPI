<?php
/**
 * @package YouTube API
 */
/*
Plugin Name: YouTube API
Description: A set of shortcode functions that retrieve information from YouTube using the Data API v3.
Version: 1.0.0
Author: GRAFIK
Author URI: http://www.grafik.com/
License: NFR
Text Domain: youtube-api
*/

	if( !defined( 'ABSPATH' ) ) exit;

	/*

	.o88o. 8888o. 888888o. 88 8888o.    888888o. 888888 8888o. 88  88
	88  88 88  88 88 88 88 88 88  88    88 88 88 88     88  88 88  88
	888888 88  88 88 88 88 88 88  88    88 88 88 8888   88  88 88  88
	88  88 88  88 88 88 88 88 88  88    88 88 88 88     88  88 88  88
	88  88 8888Y' 88 88 88 88 88  88    88 88 88 888888 88  88 'Y88Y'

	*/

	add_action( 'admin_menu', function() {

		// Create Top-Level Menu
		add_menu_page( 'YouTube API', 'YouTube API', 'administrator', __FILE__, function() {

			if( isset( $_POST[ 'YouTube_API_Nonce' ] ) && wp_verify_nonce( $_POST[ 'YouTube_API_Nonce' ], 'YouTube_API_Nonce' ) ) {
				$cb_option = sanitize_text_field( $_POST[ 'api-key' ] );
				update_option( 'YouTube_API_Options', $cb_option );
			}
			$cb_option = get_option( 'YouTube_API_Options', '' );

			echo
			'<h2>YouTube API</h2>'.
			'<hr/>'.
			'<form method="POST">'.
				wp_nonce_field( 'YouTube_API_Nonce', 'YouTube_API_Nonce', true, false ).
				'<p><label for="api-key">API Key:</label><input type="text" name="api-key" id="api-key" value="'.esc_html( $cb_option ).'" /></p>'.
				'<p><button type="submit">Save Changes</button></p>'.
			'</form>'.
			'<hr/>'.
			'<table style="border-collapse:separate;border-spacing:20px;">'.
				'<tr>'.
					'<td style="width:50%;vertical-align:top;">'.
						'<h3>YouTube Snippet</h3>'.
						'<p><strong>Intent:</strong><br/>Retrieve any single part of data from a YouTube video.</p>'.
						'<p><strong>Usage:</strong><br/><pre>[YTAPI_Snippet video_id="(your_video_id)" asset="(desired_asset)"]</pre></p>'.
						'<br/>'.
						'<h3>YouTube Code Block</h3>'.
						'<p><strong>Intent:</strong><br/>Retrieve multiple parts of data from a YouTube video, injected into placeholder tokens in valid HTML markup.</p>'.
						'<p><strong>Usage:</strong><br/><pre>[YTAPI_Block video_id="(your_video_id)"]<br/>&lt;p&gt;{{ title }}&lt;/p&gt;<br/>[/YTAPI_Block]</pre></p>'.
						'<br/>'.
						'<h3>YouTube Embed</h3>'.
						'<p><strong>Intent:</strong><br/>Output a responsive embeded YouTube video that shrinks and grows with the page layout.</p>'.
						'<p><strong>Usage:</strong><br/><pre>[YTAPI_Embed video_id="(your_video_id)"]</pre></p>'.
					'</td>'.
					'<td style="width:50%;vertical-align:top;">'.
						'<h4>Available Asset Values:</h4>'.
						'<dl>'.
							'<dt>publishedAt:</dt>'.
							'<dd>Returns a string representing the time the video was published.<br/><em>Example Output: 2015-03-24T17:18:28.000Z</em></dd>'.
							'<dt>title:</dt>'.
							'<dd>Returns a string representing the name of the video.</dd>'.
							'<dt>description:</dt>'.
							'<dd>Returns a string representing the description of the video.</dd>'.
							'<dt>thumbnail_default:</dt>'.
							'<dd>Returns an IMG entity populated with the URL of the default thumbnail, its width and height values, and an alt attribute containing the title of the video.</dd>'.
							'<dt>thumbnail_medium:</dt>'.
							'<dd>Identical to <em>thumbnail</em>, though twice as tall.</dd>'.
							'<dt>thumbnail_high:</dt>'.
							'<dd>Identical to <em>thumbnail_medium</em>, though twice as tall.</dd>'.
							'<dt>channelTitle:</dt>'.
							'<dd>Returns a string representing the name of the channel the video belongs to.</dd>'.
							'<dt>tags:</dt>'.
							'<dd>Return a UL entity populated with LI entities representing the tags placed on the video.</dd>'.
						'</dl>'.
					'</td>'.
				'</tr>'.
			'</table>';

		}, plugins_url( '/icon.png', __FILE__ ) );

		// Register Plugin Settings
		add_action( 'admin_init', function() {
			register_setting( 'youtube-api-settings', 'api-key' );
		} );

	} );

	/*

	.o8888 88  88 .o88o. 8888o. 888888 .o8888 .o88o. 8888o. 888888 .o8888
	88     88  88 88  88 88  88   88   88     88  88 88  88 88     88    
	'Y88o. 888888 88  88 8888Y'   88   88     88  88 88  88 8888   'Y88o.
	    88 88  88 88  88 88  88   88   88     88  88 88  88 88         88
	8888Y' 88  88 'Y88Y' 88  88   88   'Y8888 'Y88Y' 8888Y' 888888 8888Y'

	*/

	add_shortcode( 'YTAPI_Snippet', function( $atts ) {
		$a = shortcode_atts( array( 'video_id' => '', 'asset' => '', 'class' => '', 'id' => '', 'debug' => 0 ), $atts );
		return ( $a[ 'debug' ] == 1  ? '<!-- DEBUG: YTAPI_Snippet '.print_r( $a, true ).' -->' : '' ).YTAPI_Snippet( $a );
	} );

	add_shortcode( 'YTAPI_Block', function( $atts, $content = null ) {
		$a = shortcode_atts( array( 'video_id' => '', 'class' => '', 'id' => '', 'debug' => 0 ), $atts );
		return ( $a[ 'debug' ] == 1  ? '<!-- DEBUG: YTAPI_Block '.print_r( $a, true ).' -->' : '' ).YTAPI_Block( $a, $content );
	} );

	add_shortcode( 'YTAPI_Embed', function( $atts, $content = null ) {
		$a = shortcode_atts( array( 'video_id' => '', 'class' => '', 'id' => '', 'debug' => 0 ), $atts );
		return ( $a[ 'debug' ] == 1  ? '<!-- DEBUG: YTAPI_Embed '.print_r( $a, true ).' -->' : '' ).YTAPI_Embed( $a );
	} );

	/*

	.o8888 .o88o. 88     88     8888o. .o88o. .o8888 88  88 .o8888
	88     88  88 88     88     88  88 88  88 88     88 .8' 88    
	88     888888 88     88     8888Y' 888888 88     8888'  'Y88o.
	88     88  88 88     88     88  88 88  88 88     88 '8.     88
	'Y8888 88  88 888888 888888 8888Y' 88  88 'Y8888 88  88 8888Y'

	*/

	function YTAPI_Snippet( $a ) {

		$Atts = YTAPI_GetAtts( $a, 'ytapi ytapi-snippet ytapi-snippet-'.$a[ 'asset' ] );
		$Cached = YTAPI_GetCached( $a );
		$Snippet = $Cached[ 'items' ][ 0 ][ 'snippet' ];

		$HTML = '';

		switch( $a[ 'asset' ] ) {
			case 'publishedAt':
				$HTML .= '<span'.$Atts.'>'.$Snippet[ $a[ 'asset' ] ].'</span>';
				break;
			case 'title':
				$HTML .= '<span'.$Atts.'>'.$Snippet[ $a[ 'asset' ] ].'</span>';
				break;
			case 'description':
				$HTML .= '<span'.$Atts.'>'.$Snippet[ $a[ 'asset' ] ].'</span>';
				break;
			case 'channelTitle':
				$HTML .= '<span'.$Atts.'>'.$Snippet[ $a[ 'asset' ] ].'</span>';
				break;
			case 'thumbnail_default':
				$TN_Default = $Snippet[ 'thumbnails' ][ 'default' ];
				$HTML .= '<img'.$Atts.' src="'.$TN_Default[ 'url' ].'" width="'.$TN_Default[ 'width' ].'" height="'.$TN_Default[ 'height' ].'" alt="'.$Snippet[ 'title' ].'" />';
				break;
			case 'thumbnail_medium':
				$TN_Medium = $Snippet[ 'thumbnails' ][ 'medium' ];
				$HTML .= '<img'.$Atts.' src="'.$TN_Medium[ 'url' ].'" width="'.$TN_Medium[ 'width' ].'" height="'.$TN_Medium[ 'height' ].'" alt="'.$Snippet[ 'title' ].'" />';
				break;
			case 'thumbnail_high':
				$TN_High = $Snippet[ 'thumbnails' ][ 'high' ];
				$HTML .= '<img'.$Atts.' src="'.$TN_High[ 'url' ].'" width="'.$TN_High[ 'width' ].'" height="'.$TN_High[ 'height' ].'" alt="'.$Snippet[ 'title' ].'" />';
				break;
			case 'tags':
				$Tags = '';
				foreach( $Snippet[ $a[ 'asset' ] ] as $tag ) $Tags .= '<li>'.$tag.'</li>';
				$HTML .= '<ul'.$Atts.'>'.$Tags.'</ul>';
				break;
		}

		return $HTML;

	}

	function YTAPI_Block( $a, $content ) {

		$Atts = YTAPI_GetAtts( $a, 'ytapi ytapi-content' );
		$Cached = YTAPI_GetCached( $a );
		$Snippet = $Cached[ 'items' ][ 0 ][ 'snippet' ];

		$Tags = '';
		foreach( $Snippet[ 'tags' ] as $tag ) $Tags .= '<li>'.$tag.'</li>';

		$CurlyCode_Search = array(
			'{{ publishedAt }}',
			'{{ title }}',
			'{{ description }}',
			'{{ channelTitle }}',
			'{{ thumbnail_default }}',
			'{{ thumbnail_medium }}',
			'{{ thumbnail_high }}',
			'{{ tags }}'
		);
		$CurlyCode_Replace = array(
			'<span'.$Atts.'>'.$Snippet[ 'publishedAt' ].'</span>',
			'<span'.$Atts.'>'.$Snippet[ 'title' ].'</span>',
			'<span'.$Atts.'>'.$Snippet[ 'description' ].'</span>',
			'<span'.$Atts.'>'.$Snippet[ 'channelTitle' ].'</span>',
			'<img'.$Atts.' src="'.$Snippet[ 'thumbnails' ][ 'default' ][ 'url' ].'" width="'.$Snippet[ 'thumbnails' ][ 'default' ][ 'width' ].'" height="'.$Snippet[ 'thumbnails' ][ 'default' ][ 'height' ].'" alt="'.$Snippet[ 'title' ].'" />',
			'<img'.$Atts.' src="'.$Snippet[ 'thumbnails' ][ 'medium' ][ 'url' ].'" width="'.$Snippet[ 'thumbnails' ][ 'medium' ][ 'width' ].'" height="'.$Snippet[ 'thumbnails' ][ 'medium' ][ 'height' ].'" alt="'.$Snippet[ 'title' ].'" />',
			'<img'.$Atts.' src="'.$Snippet[ 'thumbnails' ][ 'high' ][ 'url' ].'" width="'.$Snippet[ 'thumbnails' ][ 'high' ][ 'width' ].'" height="'.$Snippet[ 'thumbnails' ][ 'high' ][ 'height' ].'" alt="'.$Snippet[ 'title' ].'" />',
			'<ul'.$Atts.'>'.$Tags.'</ul>'
		);

		return str_replace( $CurlyCode_Search, $CurlyCode_Replace, $content );

	}

	function YTAPI_Embed( $a ) {

		$Atts = YTAPI_GetAtts( $a, 'ytapi ytapi-embed' );
		$Cached = YTAPI_GetCached( $a );
		$Snippet = $Cached[ 'items' ][ 0 ][ 'snippet' ];

		$Percent = ( (int)$Snippet[ 'thumbnails' ][ 'high' ][ 'height' ] / (int)$Snippet[ 'thumbnails' ][ 'high' ][ 'width' ] ) * 100;

		return
		'<div class="embed-container" style="position:relative;padding-bottom:'.$Percent.'%;height:0;overflow:hidden;max-width:100%;">'.
			'<iframe src="http://www.youtube.com/embed/'.$a[ 'video_id' ].'" frameborder="0" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>'.
		'</div>';

	}

	/*

	88  88 888888 88     8888o. 888888 8888o. .o8888
	88  88 88     88     88  88 88     88  88 88    
	888888 8888   88     8888Y' 8888   8888Y' 'Y88o.
	88  88 88     88     88     88     88  88     88
	88  88 888888 888888 88     888888 88  88 8888Y'

	*/

	function YTAPI_GetAtts( $a, $classes ) {

		$Atts_ID = $a[ 'id' ];
		$Atts_Classes = trim( trim( $a[ 'class' ] ).' '.trim( $classes ) );

		return
		( !empty( $Atts_ID ) ? ' id="'.$Atts_ID.'"' : '' ).
		( !empty( $Atts_Classes ) ? ' class="'.$Atts_Classes.'"' : '' );

	}

	function YTAPI_GetCached( $a ) {

		$cb_options = get_option( 'YouTube_API_Options', '' );
		$curl_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id='.$a[ 'video_id' ].'&key='.$cb_options;

		if( !defined( 'YTAPI_'.$a[ 'video_id' ] ) ) {
			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_URL, $curl_url );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			$curl_out = curl_exec( $curl );
			curl_close( $curl );
			define( 'YTAPI_'.$a[ 'video_id' ], $curl_out );
		}
		return json_decode( constant( 'YTAPI_'.$a[ 'video_id' ] ), true );

	}

?>