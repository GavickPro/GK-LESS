<?php

/*
Plugin Name: GK LESS
Plugin URI: http://www.gavick.com/
Description: LESS files parser
Version: 1.0
Author: GavickPro
Author URI: http://www.gavick.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*

Copyright 2013-2013 GavickPro (info@gavick.com)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

if ( !defined( 'WPINC' ) ) {
    die;
}

/**
 * i18n
 */
load_plugin_textdomain( 'gk_less', false, dirname( dirname( plugin_basename( __FILE__) ) ).'/languages' );

/**
 * Hooks
 */
function gk_less_activate() {
  if ( ! current_user_can( 'activate_plugins' ) ) {
    return;
  }

  add_option( 'gk_less_state', 'on');
  add_option( 'gk_less_adminbar', 'on' );
  add_option( 'gk_less_comments', 'off' );
  add_option( 'gk_less_input_dir', '' );
  add_option( 'gk_less_input_files', '' );
  add_option( 'gk_less_output_dir', '' );
  add_option( 'gk_less_compile_on_demand', 'off' );
}

function gk_less_deactivate() {
  if ( ! current_user_can( 'activate_plugins' ) ) {
    return;
  }

  delete_option( 'gk_less_state' );
  delete_option( 'gk_less_adminbar' );
  delete_option( 'gk_less_comments' );
  delete_option( 'gk_less_input_dir' );
  delete_option( 'gk_less_input_files' );
  delete_option( 'gk_less_output_dir' );
  delete_option( 'gk_less_compile_on_demand' );
}

function gk_less_init() {
  register_setting( 'gk_less_options', 'gk_less_state' );
  register_setting( 'gk_less_options', 'gk_less_adminbar' );
  register_setting( 'gk_less_options', 'gk_less_comments' );
  register_setting( 'gk_less_options', 'gk_less_input_dir' );
  register_setting( 'gk_less_options', 'gk_less_input_files' );
  register_setting( 'gk_less_options', 'gk_less_output_dir' );
  register_setting( 'gk_less_options', 'gk_less_compile_on_demand' );
}

/**
 * install & uninstall
 */
register_activation_hook  ( __FILE__, 'gk_less_activate'   );
register_deactivation_hook( __FILE__, 'gk_less_deactivate' );

/**
 * Main class of the plugin
 */
class GK_LESS {
   // helper variables
   private $dir;
   private $capability;
   /**
    * Initialize
    */  
   public function __construct() {
      // basic class fields
      $this->dir = basename( dirname( __FILE__ ) );
      $this->capability = apply_filters( 'gk_less_capability', 'manage_options' );
      // actions
      add_action( 'admin_init', 'gk_less_init' );
      add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
      // enable the adminbar button if enabled
      if(get_option('gk_less_adminbar') == 'on') {
        add_action( 'admin_bar_menu', array( &$this, 'adminbar_switcher' ), 999 );
      }
      // get state
      $this->get_state();
      // if the parser is enabled
      if(get_option('gk_less_state') == 'on') {
        $this->parse();
      }
   }

   /**
    * Add page to the settings menu
    */    
   public function admin_menu() {
      $page = add_options_page(
          __( 'GK LESS', 'gk_less' ),
          __( 'GK LESS', 'gk_less' ),
          $this->capability,
          $this->dir.'/back-end/index.php'
      );
   }

   /**
    * Add admin bar button to enable/disable the LESS parser
    */
   public function adminbar_switcher() {
    global $wp_admin_bar;

    if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
      return;
    }

    // Get opposite direction for button text
    $state = get_option('gk_less_state', 'on') == 'on';
    // add the LESS switcher
    $wp_admin_bar->add_menu(
      array(
        'id'    => 'GK-LESS-main',
        'title' => sprintf( __( '%s LESS parser', 'gk_less' ), $state ? __('Disable', 'gk_less') : __('Enable', 'gk_less') ),
        'href'  => add_query_arg( array( 'less_state' => $state ? 0 : 1 ) )
      )
    );
    // add option of the recompilation on demand
    if(
        get_option('gk_less_state') == 'off' && 
        get_option('gk_less_compile_on_demand') == 'on'
    ) {
      $wp_admin_bar->add_menu(
        array(
          'parent' => 'GK-LESS-main',
          'id'    => 'GK-LESS-recompile',
          'title' => __( 'Recompile the LESS files', 'gk_less' ),
          'href'  => add_query_arg( array( 'recompile' => 'true' ) )
        )
      );
    }
    // add option of the error log
    $wp_admin_bar->add_menu(
      array(
        'parent' => 'GK-LESS-main',
        'id'    => 'GK-LESS-errors',
        'title' => __( 'GK LESS errorlog', 'gk_less' ),
        'meta' => array(
            'target' => '_blank'
        ),
        'href'  => plugin_dir_url(__FILE__) . 'errorlog.lesserrors'
      )
    );
  }

  /**
   * Get state
   */
  private function get_state() {
    if(isset($_GET['less_state']) && $_GET['less_state'] === '1') {
      update_option('gk_less_state', 'on');
    } else if(isset($_GET['less_state']) && $_GET['less_state'] === '0') {
      update_option('gk_less_state', 'off');
    }
    // recompilation od demand works only if the proper variable is placed in the URL,
    // the compiler is disabled and the compilation on demand is enabled
    if(
        isset($_GET['recompile']) &&
        $_GET['recompile'] == 'true' && 
        get_option('gk_less_state') == 'off' && 
        get_option('gk_less_compile_on_demand') == 'on'
    ) {
      $this->parse();
    }
  }
   
   /**
    * Parsing less files
    */
   public function parse() {
    // prepare the list of files
    $input_dir = ABSPATH . rtrim(get_option('gk_less_input_dir'), '/');
    $output_dir = ABSPATH . rtrim(get_option('gk_less_output_dir'), '/');
    $input_files = get_option('gk_less_input_files');
    $files = array();

    if(trim($input_files) != '') {
      $files = preg_split('/$\R?^/m', get_option('gk_less_input_files'));
      $len = count($files);
      for($i = 0; $i < $len; $i++) {
        $files[$i] = '/' . trim(ltrim($files[$i], '/'));
      }
    } else {
      $files = $this->directoryToArray($input_dir, true);

      $len = count($files);
      for($i = 0; $i < $len; $i++) {
        $files[$i] = str_replace($input_dir, '', $files[$i]);
      }
    }
    // prepare the error log
    $errorlog = '';

    // prepare the LESS compiler
    require('phpless.php');
    $less = new lessc;

    if(get_option('gk_less_comments') == 'on') {
      $less->setPreserveComments(true);
    }
    
    for($i = 0; $i < $len; $i++) {
      // remove the filename from the path
      $file_path = $output_dir . preg_replace('@/([^/]*?)\.([^\./]+?)$@mis', '', $files[$i]);
      // check if the specific dir exists
      if(!is_dir($file_path)) {
        // create the unexisting dirs
        if(mkdir($file_path, 0755, true)) {
          // check if the file exists
          if(file_exists($input_dir . $files[$i])) {
            // the compilation will run only if the output file is older than the input file
            try {
              $less->checkedCompile($input_dir . $files[$i], $output_dir . str_replace('.less', '.css', $files[$i])); 
            } catch (Exception $ex) {
              $errorlog .= $ex->getMessage() . "\n\n";
            }
          }
        } else {
          $errorlog .= __('Directory wasn\'t created: ', 'gk_less') . $file_path . "\n\n";
        }
      } else { // if the directory exists
        // check if the file exists
        if(file_exists($input_dir . $files[$i])) {
          // the compilation will run only if the output file is older than the input file
          try {
            $less->checkedCompile($input_dir . $files[$i], $output_dir . str_replace('.less', '.css', $files[$i])); 
          } catch (Exception $ex) {
            $errorlog .= $ex->getMessage() . "\n\n";
          }
        }
      }
    }

    // save the errorlog
    file_put_contents( plugin_dir_path(__FILE__) .' errorlog.lesserrors', $errorlog == '' ? __('Last LESS recompilation was successful', 'gk_less') : $errorlog);
   }
   /**
    * Parsing directory to array
    *
    * @param $directory - current directory to parse
    * @param $recursive - we will go deeper?
    *
    * @return array of files in the directory
    */
   private function directoryToArray($directory, $recursive) {
      $array_items = array();
      if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && stripos($file, 'index.html') === FALSE) {
                    if (is_dir($directory. "/" . $file)) {
                        if($recursive) {
                            $array_items = array_merge($array_items, $this->directoryToArray($directory. "/" . $file, $recursive));
                        }
                        if(stripos($file, '.less') !== FALSE) {
                          $file = $directory . "/" . $file;
                          $array_items[] = preg_replace("/\/\//si", "/", $file);
                        }
                    } else {
                        if(stripos($file, '.less') !== FALSE) {
                          $file = $directory . "/" . $file;
                          $array_items[] = preg_replace("/\/\//si", "/", $file);
                        }
                    }
                }
            }
            closedir($handle);
      }

      return $array_items;
   }   
}
// Run the parser
$parser = new GK_LESS();

// EOF
