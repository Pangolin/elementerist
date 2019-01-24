<?php
/**
 * Plugin Name: Elementerist
 * Plugin URI: https://pangolin.ai
 * Description: A plugin to add pinterest tags to the Elementor image widget created by Tanner Chung
 * Version: 1.0
 * Author: Pangolin
 * Author URI: https://pangolin.ai
 */

/* Adds pinterest fields to the Elementor's Image widget */
add_action('elementor/element/before_section_end', 'add_elementor_pinterest_tags', 10, 3 );
function add_elementor_pinterest_tags( $widget, $section_id, $args ) {
	if( $widget->get_name() == 'image' && $section_id == 'section_image' ){
		// we are at the end of the "section_image" area of the "image"
		$widget->add_control(
			'pinterest_description_source' ,
			[
				'label'        => __('Pinterest Description', 'elementor' ),
				'type'         => Elementor\Controls_Manager::SELECT,
				'default'      => 'none',
				'options'      => [
					'none' => __( 'None', 'elementor' ),
					'attachment' => __('Attachment Pinterest Description', 'elementor'),
					'custom' => __( 'Custom Description', 'elementor' ),
				],
				'label_block'  => true,
			]
		);

		$widget->add_control(
			'pinterest_description',
			[
				'label' => __( 'Custom Description', 'elementor' ),
				'type' => Elementor\Controls_Manager::TEXTAREA,
				'rows' => 3,
				'placeholder' => __( 'Type your description here', 'elementor'),
				'condition' => [
						'pinterest_description_source' => 'custom',
				],
				'dynamic' => [
					'active' => true,
				],
				
			]
		);
	}
}

/* Adds pinterest fields grabbed from media attachment/widget to the image render */
add_filter( 'elementor/image_size/get_attachment_image_html', 'inject_elementor_pinterest_tags', 10, 2);
function inject_elementor_pinterest_tags($content, $settings) {

	
	$description = get_pinterest_description( $settings );
	if ($description){
		$content = str_replace( 
			array( '<img ' ),
			array( '<img ' . 'data-pin-description="' . $description . '" ' ),
			$content 
		);
	}
	return $content;
}

/* Abstracts the retrieval of the pinterest description from the tag fields */
function get_pinterest_description( $instance ) {
	if ( 'none' === $instance['pinterest_description_source'] ) {
		return false;
	}

	if ( 'custom' === $instance['pinterest_description_source'] ) {
		if ( empty( $instance['pinterest_description'] ) ) {
			return false;
		}
		return $instance['pinterest_description'];
	}
	return get_post_meta($instance['image']['id'], 'data-pin-description', true);
}



/* Adds pinterest description in case user decides to use wordpress' default editor */
add_filter( 'image_send_to_editor', 'add_pinterest_to_image', 10, 2 );

function add_pinterest_to_image( $html, $attachment_id ) 
{
    if ($attachment_id)
    {
        //check if there is data-pin-description
        $data_pin_description = get_post_meta($attachment_id, 'data-pin-description', true);

        //if there is a data-pin-description set for the image, add data-pin-description
        if ($data_pin_description)
        {
            $document = new DOMDocument();
            $document->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            $imgs = $document->getElementsByTagName('img');

            foreach ($imgs as $img)
            {

                //add the data attribute
                $img->setAttribute('data-pin-description', $data_pin_description);
            }

            $html = $document->saveHTML();
        }
    }

    return $html;
}


/* Disable Pods shortcode button, storing here for the time being */
add_action( 'admin_init', 'remove_pods_shortcode_button', 14 );

function remove_pods_shortcode_button () {
    remove_action( 'media_buttons', array( PodsInit::$admin, 'media_button' ), 12 );
}