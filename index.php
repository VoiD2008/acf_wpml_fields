<?php
/**
 * Plugin Name: Language option for ACF4+ Fields 
 Description: Adding language option for ACF fields. Yet working with Polylang plugin only.
 Version: 1.0.1
 Author: VoiD
 Author URI: http://spbdesigner.ru
 Text Domain: acfwpml
 Plugin URI: https://github.com/VoiD2008/acf_wpml_fields
 */

defined('ABSPATH') or die("No kidding please!");

$acfwpml_error;
$acfwpml_lang_plugin;

	function acfwpml_activate() {
		global $acfwpml_error,$acfwpml_lang_plugin;
		$acfwpml_error = '';
		$acfwpml_lang_plugin = '';

		if (!function_exists('icl_t')){
			$acfwpml_error .= 'No WPML Plugin installed/activated.<br/>';
		} else {
			$acfwpml_lang_plugin = 'WPML';			
		}

		if (!defined('POLYLANG_VERSION')){
			$acfwpml_error .= 'No Polylang Plugin installed/activated.<br/>';
		} else {
			$acfwpml_lang_plugin = 'POLYLANG';			
		}

		if ($acfwpml_lang_plugin) $acfwpml_error = '';

		if (!function_exists('acf_form')){
			$acfwpml_error .= 'No ACF Plugin installed/activated.<br/>';
		}
		
		if ($acfwpml_error){
        		add_action( 'admin_init', 'acfwpml_deactivate' );
          		add_action( 'admin_notices', 'acfwpml_admin_notice',$error );
		} else {
			acfwpml_register_actions();
		}
	}

 	function acfwpml_deactivate() {
              deactivate_plugins( plugin_basename( __FILE__ ) );
       }

       function acfwpml_getlangs() {
		global $acfwpml_lang_plugin;
		switch ($acfwpml_lang_plugin){
			case 'POLYLANG':
				return pll_languages_list();
				break;
		}
	}

       function acfwpml_admin_notice() {
		global $acfwpml_error;
       	echo '<div class="error">Plug-in <strong>ACF_WPML</strong> was <strong>deactivated</strong>.<br/>'.$acfwpml_error.'</div>';
		if ( isset( $_GET['activate'] ) )
		unset( $_GET['activate'] );
       }

	function acfwpml_addlangselect($field){
		global $post;
		if ($field['class']=='label') {?>

	</td>
</tr>
<tr class="field_option field_option_lang">
	<td class="label">
		<label><?php _e("Language code",'acfwpml'); ?></label>
		<p><?php _e("Language for the field",'acfwpml') ?></p>
	</td>
	<td>
		<?
			$langs =  acfwpml_getlangs();
			preg_match('/.*\\[(.*)\\]\\[.*/', $field['name'], $fieldname);
			$select = get_post_meta($post->ID,$fieldname[1]);
			do_action('acf/create_field', array(
				'type'	=>	'select',
				'name'	=>	str_replace('label','language',$field['name']),
				'choices'	=>	array_merge((array)'all',$langs),
				'value'	=>	$select[0]['language'],
				'class' => 'language',
			));

		}
		return $field;
	}

	function acfwpml_get_post_language(){
		global $acfwpml_lang_plugin,$post;
		switch ($acfwpml_lang_plugin){
			case 'POLYLANG':
				return pll_get_post_language($post->ID);
				break;
		}		
	}


	function acfwpml_acf_load_field($field){
		$post_lang = acfwpml_get_post_language();
		$langs =  acfwpml_getlangs();
		$lang_index = array_search($post_lang,array_merge((array)'',$langs));
		if ($field['language'])
			if ($field['language']>0&&$lang_index>0)
				if ($field['language']!=$lang_index)
					return;
		return $field;
	}


	function acfwpml_register_actions(){
		add_filter('acf/create_field','acfwpml_addlangselect',10,1);
		add_filter('acf/load_field', 'acfwpml_acf_load_field',10,1);
	}


add_action( 'plugins_loaded', 'acfwpml_activate' );


