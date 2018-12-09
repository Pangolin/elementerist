<?php
/**
 * Plugin Name: Elementor Pinterest Tag Addon
 * Plugin URI: https://www.cafevagrant.com
 * Description: A plugin to add pinterest tags to the Elementor image widget
 * Version: 1.0
 * Author: Cafe Vagrant
 * Author URI: https://www.cafevagrant.com
 */


add_action('elementor/element/before_section_end', 'pinterest_tags', 10, 3 );

function pinterest_tags( $widget, $section_id, $args ) {
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

add_filter( 'elementor/image_size/get_attachment_image_html', 'inject_pinterest_tags', 10, 2);
function inject_pinterest_tags($content, $settings) {

	
	$description = get_pinterest_description( $settings );
	echo $description;
	$content = str_replace( 
		array( '<img ' ),
		array( '<img ' . 'data-pin-description="' . $description . '" ' ),
		$content 
	);

	return $content;
}


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
	return get_post_meta($instance['image']['id'], 'wpcf-data-pin-description', true);
}

function add_pinterest_field_attachment_details( $form_fields, $post ) {
    $field_value = get_post_meta( $post->ID, 'wpcf-data-pin-description', true );
    $form_fields['wpcf-data-pin-description'] = array(
        'value' => $field_value ? $field_value : '',
        'label' => __( 'Pinterest Description' ),
        'helps' => __( 'This description will be added as an attribute to your image' ),
        'input'  => 'textarea'
    );
    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'add_pinterest_field_attachment_details', null, 2 );

// add_action( 'elementor/widget/before_render_content', 'pinterest_render_image' );

// function pinterest_render_image( $widget ) {

// 	if( 'image' === $widget->get_name() ) {

// 		$settings = $widget->get_settings();

// 		$pinterest_description = get_pinterest_description($settings);

// 		// Adding new Pinterest attributes
// 		if( $pinterest_description ) {
// 		  echo $pinterest_description;

// 		  $widget->add_render_attribute( 'img', [
// 		  	'data-pin-description' => $pinterest_description,
// 		  ], true );
// 		}
// 	}
// }

// function pinterest_render_image( $widget ) {

// 	if( 'image' === $widget->get_name() ) {

// 		$settings = $widget->get_settings();

// 		$pinterest_description = get_pinterest_description($settings);

// 		// Adding new Pinterest attributes
// 		if( $pinterest_description ) {
// 		  echo $pinterest_description;

// 		  $widget->add_render_attribute( 'img', [
// 		  	'data-pin-description' => $pinterest_description,
// 		  ], true );
// 		}
// 	}
// }







// add_filter('elementor/widget/render_content', function ($content, $widget) {

	
//   if ($widget->get_name() !== 'image') {
//     return $content;
//   }
//   var_dump($widget);
//   echo $content;

  // $dom = new \Zend\Dom\Query($content);
  // $buttons = $dom->execute('.elementor-cta__button-wrapper');

  // foreach ($buttons as $button) {
  //   $buttonTag = $button->ownerDocument->saveXML($button);
  //   $className = 'elementor-element elementor-button-' . $widget->get_settings('ntz_button_type');

  //   $content = str_replace(
  //     $buttonTag,
  //     sprintf('<div class="elementor-widget-button %s"><div class="elementor-button">%s</div></div>', $className, $buttonTag),
  //     $content
  //   );
  // }
  
//   return $content;
// }, 10, 2);





