<?php
/**
 * @link              https://roadwarriorcreative.com
 * @since             1.0.0
 * @package           rwc_atx_found_pets
 *
 * @wordpress-plugin
 * Plugin Name:       Austin TX Found Pets Shortcode
 * Plugin URI:        https://roadwarriorcreative.com
 * Description:       This plugin creates a shortcode that displays all stray cats and dogs that are currently listed in Austin Animal Center's database. API integration example + help missing dogs and cats find their owners = good karma.  
 * Version:           1.0.0
 * Author:            Road Warrior Creative
 * Author URI:        https://roadwarriorcreative.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rwc-atx-found-pets
 * Domain Path:       /languages
 */

function rwc_atx_found_pets_shortcode( $attributes = array() ) {

	$attributes = shortcode_atts( array(
		'app_token' => '',
		'limit' => 6,
	), $attributes, 'found_pets' );


	$app_token = $attributes['app_token'];
	$limit = $attributes['limit'];
	$request_uri = 'https://data.austintexas.gov/resource/hye6-gvq2.json?$$app_token=' . $app_token . '&$limit=' . $limit;
	$request = wp_remote_get( $request_uri );
		if( is_wp_error( $request ) || '200' != wp_remote_retrieve_response_code( $request ) ) {
			return;
		}

		$pets = json_decode( wp_remote_retrieve_body( $request ) );
		if( empty( $pets ) ) {
			return;
		}
		ob_start();
		echo '<div class="found-pets">';
		foreach( $pets as $pet ) {
			$found_date = strtotime( $pet->intake_date );
			$imageurl = esc_url_raw( $pet->image->url);
			//$street = $pet->location->human_address->address;
			$type = $pet->type;
			$breed = $pet->looks_like;
			$sex = $pet->sex;
			$color = $pet->color;
			$age = $pet->age;

			?>
				<div class="pet" style="margin-bottom:20px;">
					<h3><?php echo $color . ' ' . $sex . ' ' . $type; ?></h3>
					<p><strong>Looks Like A:</strong> <?php echo $breed; ?></br>
					<strong>Estimated Age:</strong> <?php echo $age; ?></br>
					<strong>Found Date:</strong> <?php echo date( 'F j, Y', $found_date ); ?></p>
					<button href="<?php echo $imageurl; ?>" target="_blank" class="pet-photo">See Pet Photo</button>
				</div>
			<?php
		}
		echo '</div>';
		return ob_get_clean();
}
	
add_shortcode( 'found_pets', 'rwc_atx_found_pets_shortcode' );