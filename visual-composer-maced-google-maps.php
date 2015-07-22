<?php

/**
 * Plugin Name: Visual Composer Maced Google Maps
 * Plugin URI:
 * Version: 1.0.0
 * Author: macerier
 * Author URI:
 * Description: Simply creates google maps with Visual Composer or via shortcode;
 * License: GPL2
 */
class vcMacedGmap
{

    function vcMacedGmap()
    {
        // Plugin Details
        $this->plugin = new stdClass();
        $this->plugin->name = 'visual-composer-maced-google-maps'; // Plugin Folder
        $this->plugin->displayName = 'Visual Composer Maced Google Maps'; // Plugin Name
        $this->plugin->version = '0.0.1';
        $this->plugin->folder = WP_PLUGIN_DIR . '/' . $this->plugin->name; // Full Path to Plugin Folder
        $this->plugin->url = WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
        
        add_action('plugins_loaded', array(
            &$this,
            'loadLanguageFiles'
        ));
        add_shortcode('vcmacedgmap', array(
            &$this,
            'GmapShortcode'
        ));
        
        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => __('Maced Google Maps', $this->plugin->name),
                'base' => 'vcmacedgmap',
                "description" => __("Visual Composer Maced Google Maps", $this->plugin->name),
                'category' => __('Macerier Shortcodes', $this->plugin->name),
                "icon" => 'icon-wpb-map-pin',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'heading' => __('Google Maps Lat', $this->plugin->name),
                        'param_name' => 'lat',
                        'value' => '-33.87',
                        'description' => __('The map will appear only if this field is filled correctly.<br />Example: <b>-33.87</b>', $this->plugin->name)
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __('Google Maps Lng', $this->plugin->name),
                        'param_name' => 'lng',
                        'value' => '151.21',
                        'description' => __('The map will appear only if this field is filled correctly.<br />Example: <b>151.21</b>', $this->plugin->name)
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __('Zoom', $this->plugin->name),
                        'param_name' => 'zoom',
                        'value' => '13'
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __('Height', $this->plugin->name),
                        'param_name' => 'height',
                        'value' => '200'
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => __('Controls', $this->plugin->name),
                        'param_name' => 'controls',
                        'value' => array(
                            '' => __('Zoom', $this->plugin->name),
                            'mapType' => __('Map Type', $this->plugin->name),
                            'streetView' => __('Street View', $this->plugin->name),
                            'zoom mapType' => __('Zoom & Map Type', $this->plugin->name),
                            'zoom streetView' => __('Zoom & Street View', $this->plugin->name),
                            'mapType streetView' => __('Map Type & Street View', $this->plugin->name),
                            'zoom mapType streetView' => __('Zoom, Map Type & Street View', $this->plugin->name),
                            'hide' => __('Hide All', $this->plugin->name)
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => __('Border', $this->plugin->name),
                        'param_name' => 'border',
                        'value' => array(
                            0 => __('No', $this->plugin->name),
                            1 => __('Yes', $this->plugin->name)
                        ),
                        'description' => __('Show map border', $this->plugin->name)
                    ),
                    array(
                        'type' => 'attach_image',
                        'heading' => __('Marker Icon', 'js_composer'),
                        'param_name' => 'icn',
                        'value' => '',
                        'description' => __('Select image from media library. Use  .png for best results.', $this->plugin->name)
                    ),
                    array(
                        'type' => 'textarea',
                        'heading' => __('Styles', 'js_composer'),
                        'param_name' => 'styles',
                        'value' => '',
                        'description' => __('You can get predefined styles from <a target="_blank" href="http://snazzymaps.com/">snazzymaps.com</a> or generate your own <a target="_blank" href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html">here</a>', $this->plugin->name)
                    ),
                    array(
                        'type' => 'textarea',
                        'heading' => __('Additional Markers | Lat,Lng', $this->plugin->name),
                        'param_name' => 'text',
                        'value' => '',
                        'description' => __('Separate Lat,Lang with <b>coma</b> [ , ]<br />Separate multiple Markers with <b>semicolon</b> [ ; ]<br />Example: <b>-33.88,151.21;-33.89,151.22</b>', $this->plugin->name)
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __('Box | Title', $this->plugin->name),
                        'param_name' => 'titl',
                        'value' => ''
                    ),
                    array(
                        'type' => 'textarea',
                        'heading' => __('Box | Address', $this->plugin->name),
                        'param_name' => 'content',
                        'value' => '',
                        'description' => __('HTML and shortcodes tags allowed.', $this->plugin->name)
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __('Box | Telephone', $this->plugin->name),
                        'param_name' => 'telephone',
                        'value' => ''
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __('Box | Email', $this->plugin->name),
                        'param_name' => 'email',
                        'value' => ''
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __('Box | Website', $this->plugin->name),
                        'param_name' => 'www',
                        'value' => ''
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __('Custom | Classes', $this->plugin->name),
                        'param_name' => 'classes',
                        'value' => '',
                        'description' => __('Custom CSS Item Classes Names. Multiple classes should be separated with SPACE', $this->plugin->name)
                    )
                )
            ));
        }
    }

    function GmapShortcode($attr, $content = null)
    {
        extract(shortcode_atts(array(
            'lat' => '',
            'lng' => '',
            'zoom' => 13,
            'height' => 200,
            'controls' => '',
            'border' => '',
            'icn' => '',
            'styles' => '',
            'titl' => '',
            'telephone' => '',
            'email' => '',
            'www' => '',
            'latlng' => '',
            'uid' => uniqid()
        ), $attr));
        
        // border
        if ($border) {
            $class = 'has_border';
        } else {
            $class = 'no_border';
        }
        if ($icn) {
            $icn = wp_get_attachment_url($icn);
        }
        
        // controls
        $zoomControl = $mapTypeControl = $streetViewControl = 'false';
        if (! $controls)
            $zoomControl = 'true';
        if (strpos($controls, 'zoom') !== false)
            $zoomControl = 'true';
        if (strpos($controls, 'mapType') !== false)
            $mapTypeControl = 'true';
        if (strpos($controls, 'streetView') !== false)
            $streetViewControl = 'true';
        
        wp_enqueue_script('google-maps', 'https://maps.google.com/maps/api/js?sensor=false', false, $this->plugin->version, true);
        
        wp_enqueue_style($this->plugin->name . '-base-css', plugins_url($this->plugin->name . '/css/base.css', $this->plugin->name), false, $this->plugin->version);
        wp_enqueue_style($this->plugin->name . '-flaticon-css', plugins_url($this->plugin->name . '/css/flaticon.css', $this->plugin->name), false, $this->plugin->version);
        $output = '<script>';
        // <![CDATA[
        $output .= 'function google_maps_' . $uid . '(){';
        
        $output .= 'var latlng = new google.maps.LatLng(' . $lat . ',' . $lng . ');';
        
        $output .= 'var myOptions = {';
        $output .= 'zoom				: ' . intval($zoom) . ',';
        $output .= 'center				: latlng,';
        $output .= 'mapTypeId			: google.maps.MapTypeId.ROADMAP,';
        if ($styles)
            $output .= 'styles	: ' . $styles . ',';
        $output .= 'zoomControl			: ' . $zoomControl . ',';
        $output .= 'mapTypeControl		: ' . $mapTypeControl . ',';
        $output .= 'streetViewControl	: ' . $streetViewControl . ',';
        $output .= 'scrollwheel			: false';
        $output .= '};';
        
        $output .= 'var map = new google.maps.Map(document.getElementById("google-map-area-' . $uid . '"), myOptions);';
        
        $output .= 'var marker = new google.maps.Marker({';
        $output .= 'position			: latlng,';
        if ($icn)
            $output .= 'icon	: "' . $icn . '",';
        $output .= 'map					: map';
        $output .= '});';
        
        // additional markers
        if ($latlng) {
            
            // remove white spaces
            $latlng = str_replace(' ', '', $latlng);
            
            // explode array
            $latlng = explode(';', $latlng);
            
            foreach ($latlng as $k => $v) {
                
                $markerID = $k + 1;
                $markerID = 'marker' . $markerID;
                
                $output .= 'var ' . $markerID . ' = new google.maps.Marker({';
                $output .= 'position			: new google.maps.LatLng(' . $v . '),';
                if ($icn)
                    $output .= 'icon	: "' . $icn . '",';
                $output .= 'map					: map';
                $output .= '});';
            }
        }
        
        $output .= '}';
        
        $output .= 'jQuery(document).ready(function($){';
        $output .= 'google_maps_' . $uid . '();';
        $output .= '});';
        // ]]>
        $output .= '</script>' . "\n";
        
        $output .= '<div class="google-map-wrapper ' . $class . '">';
        
        if ($titl || $content) {
            $output .= '<div class="google-map-contact-wrapper">';
            $output .= '<div class="get_in_touch">';
            if ($titl)
                $output .= '<p class="sameH3">' . $titl . '</p>';
            $output .= '<div class="get_in_touch_wrapper">';
            $output .= '<ul>';
            if ($content) {
                $output .= '<li class="address">';
                $output .= '<span class="icon"><i class="flaticon-mg-pointer"></i></span>';
                $output .= '<span class="address_wrapper">' . do_shortcode($content) . '</span>';
                $output .= '</li>';
            }
            if ($telephone) {
                $output .= '<li class="phone">';
                $output .= '<span class="icon"><i class="flaticon-mg-telephone"></i></span>';
                $output .= '<p><a href="tel:' . str_replace(' ', '', $telephone) . '">' . $telephone . '</a></p>';
                $output .= '</li>';
            }
            if ($email) {
                $output .= '<li class="mail">';
                $output .= '<span class="icon"><i class="flaticon-mg-envelope"></i></span>';
                $output .= '<p><a href="mailto:' . $email . '">' . $email . '</a></p>';
                $output .= '</li>';
            }
            if ($www) {
                $output .= '<li class="www">';
                $output .= '<span class="icon"><i class="flaticon-mg-link"></i></span>';
                $output .= '<p><a target="_blank" href="http://' . $www . '">' . $www . '</a></p>';
                $output .= '</li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }
        
        $output .= '<div class="google-map" id="google-map-area-' . $uid . '" style="width:100%; height:' . intval($height) . 'px;">&nbsp;</div>';
        
        $output .= '</div>' . "\n";
        
        return $output;
    }

    /**
     * Loads plugin textdomain
     */
    function loadLanguageFiles()
    {
        load_plugin_textdomain($this->plugin->name, false, $this->plugin->name . '/languages/');
    }
}
$vcMacedGmap = new vcMacedGmap();
