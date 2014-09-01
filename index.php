<?php
/**
 * Plugin Name: Language option for ACF4+ Fields 
 Description: Adding language option for ACF fields. Yet working with Polylang plugin only.
 Version: 1.1.0
 Author: VoiD
 Author URI: http://spbdesigner.ru
 Text Domain: acfwpml
 */

defined('ABSPATH') or die("No kidding please!");

$acfwpml_error;
$acfwpml_lang_plugin;

	function acfwpml_activate() {
		global $acfwpml_error,$acfwpml_lang_plugin;
		$acfwpml_error = '';
		$acfwpml_lang_plugin = '';


		if (!defined('ICL_SITEPRESS_VERSION')){
			$acfwpml_error .=  __("No WPML Plugin installed/activated.",'acfwpml').'<br/>';
		} else {
			$acfwpml_lang_plugin = 'WPML';			
		}

		if (!defined('POLYLANG_VERSION')){
			$acfwpml_error .= __("No Polylang Plugin installed/activated.",'acfwpml').'<br/>';
		} else {
			$acfwpml_lang_plugin = 'POLYLANG';			
		}

		if (!defined('XILILANGUAGE_VER')){
			$acfwpml_error .= __("No xili-language Plugin installed/activated.",'acfwpml').'<br/>';
		} else {
			$acfwpml_lang_plugin = 'XILI';			
		}

		if ($acfwpml_lang_plugin) $acfwpml_error = '';

		if (!function_exists('acf_form')){
			$acfwpml_error .= __("No ACF Plugin installed/activated.",'acfwpml').'<br/>';
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
			case 'WPML':
				foreach (icl_get_languages() as $key => $val)
					$langs[] = $key;
				return $langs;
				break;
			case 'XILI':
				global $xili_language;
				foreach ($xili_language->xili_settings['lang_features'] as $key => $val)
					$langs[] = $key;
				return $langs;
		}
	}

	function acfwpml_get_post_language(){
		global $acfwpml_lang_plugin,$post;
		switch ($acfwpml_lang_plugin){
			case 'POLYLANG':
				return pll_get_post_language($post->ID);
			case 'WPML':
				return ICL_LANGUAGE_CODE;
			case 'XILI':
				global $post,$xili_language;
				return $xili_language->get_post_language($post->ID);
		}		
	}

       function acfwpml_admin_notice() {
		global $acfwpml_error;
       	echo __('<div class="error">Plug-in <strong>ACF_WPML</strong> was <strong>deactivated</strong>','acfwpml').'<br/>'.$acfwpml_error.'</div>';
		if ( isset( $_GET['activate'] ) )
		unset( $_GET['activate'] );
       }

	function acfwpml_addlangselect($field){
		global $post;
///// Field options language selector ////////
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
				'choices'	=>	array_merge((array)__('all','acfwpml'),$langs),
				'value'	=>	$select[0]['language'],
				'class' => 'language',
			));

		}
///// Field group options language selector ////////
		if ($field['name']=='menu_order') {?>
	</td>
</tr>
<tr class="field_option field_option_lang">
	<td class="label">
		<label><?php _e("Language code",'acfwpml'); ?></label>
		<p><?php _e("Language for the field group",'acfwpml') ?></p>
	</td>
	<td>
		<?
			$langs =  acfwpml_getlangs();
			$select = get_post_meta($post->ID,'acfwpml_language');
			do_action('acf/create_field', array(
				'type'	=>	'select',
				'name'	=>	'options[acfwpml_language]',
				'choices'	=>	array_merge((array)__('all','acfwpml'),$langs),
				'value'	=>	$select[0],
				'class' => 'acfwpml_language',
			));

		}

		return $field;
	}


	function acfwpml_acf_get_options($options){
		$options = array_merge((array)'acfwpml_language',$options);
		return $options;
	}

	function wpml_save_fieldgroup($post_id){
		if( isset($_POST['options']['acfwpml_language']))
			update_post_meta($post_id, 'acfwpml_language', $_POST['options']['acfwpml_language']);
	}

	function acfwpml_acf_load_field($field){
		global $post;
		if ($post->post_type=='acf')
			return $field;
		$post_lang = acfwpml_get_post_language();
		$langs =  acfwpml_getlangs();

		$lang_index = array_search($post_lang,array_merge((array)'',$langs));
		if ($field['language'])
			if ($field['language']>0&&$lang_index>0)
				if ($field['language']!=$lang_index)
					return;
		return $field;
	}

	function wpml_acf_get_field_groups($acfs){
		$post_lang = acfwpml_get_post_language();
		$langs =  acfwpml_getlangs();
		foreach ($acfs as $key => $acf){
			$lang = get_post_meta($acf['id'],'acfwpml_language');
			$lang_index = 0;
			$lang_index = array_search($post_lang,array_merge((array)'',$langs)); 
			if ($lang&&$lang_index>0&&$lang[0]>0)
				if ($lang[0]!=$lang_index) unset($acfs[$key]);	
		}
		return $acfs;
	}

	function acfwpml_register_actions(){
		add_filter('acf/create_field','acfwpml_addlangselect',10,1);
		add_filter('acf/load_field', 'acfwpml_acf_load_field',10,1);
		add_filter('acf/field_group/get_options','acfwpml_acf_get_options',10,1);
		add_action('save_post', 'wpml_save_fieldgroup',1,1);
		add_filter('acf/get_field_groups','wpml_acf_get_field_groups',100,1);
	}

	function acfwpml_load_langs_filter( $lang ) {
		return $lang;
	}

	function acfwpml_preload_langs(){
		add_filter('acfwpml_load_langs', 'acfwpml_load_langs_filter');
   		$lang = apply_filters( 'acfwpml_load_langs', get_locale() );
		load_textdomain('acfwpml', plugin_dir_path( __FILE__ ) . 'lang/acfwpml-' . $lang . '.mo');
	}

add_action( 'init','acfwpml_preload_langs',998);
add_action( 'init', 'acfwpml_activate',999 );


