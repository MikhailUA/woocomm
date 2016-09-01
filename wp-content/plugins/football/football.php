<?php
/*
Plugin Name: Football
Plugin URI:  http://URI_Of_Page_Describing_Plugin_and_Updates
Description: This describes my plugin in a short sentence
Version:     1
Author:
Author URI:  http://URI_Of_The_Plugin_Author
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain:
*/

// Exit if accessed directly
if (!defined('ABSPATH')){
    exit;
}

require_once(plugin_dir_path(__FILE__).'Classes/PHPExcel.php');
require_once(plugin_dir_path(__FILE__).'Classes/PHPExcel/IOFactory.php');
require_once(plugin_dir_path(__FILE__).'admin-notice-helper/admin-notice-helper.php');

add_action( 'admin_post_nopriv_import', 'fileprocessing' );
add_action( 'admin_post_import', 'fileprocessing' );

register_activation_hook(__FILE__,'football_install');
function football_install(){
    // football_setup_post_type(); not works here, action init is needed
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__,'football_uninstall');
function football_uninstall(){
    flush_rewrite_rules();
}
// ---------------------------------------------------------------------

function football_setup_post_type(){
    register_post_type('players',
        array(
            'public' => true,
            'labels' => array(
                'name' => 'Players'
            ),
            'supports' => array(
                'title','thumbnail'
            )
        )
    );
}
add_action('init','football_setup_post_type');

function addstyles(){
    wp_register_style( 'football', plugins_url('football.css',__FILE__));
    wp_enqueue_style('football');

    wp_enqueue_script('quick_tag',plugins_url('quick_tags.js',__FILE__),array('quicktags',),true);

}
add_action('admin_enqueue_scripts','addstyles');

// ---------------------------------------------------------------------
// data uploading
function upload_page(){
    add_submenu_page('edit.php?post_type=players', 'Upload Players', 'Upload Players', 'edit_posts', basename(__FILE__), 'playersuploadform');
}
add_action('admin_menu' , 'upload_page');

function playersuploadform(){?>
    <div id="uploadplayers">
        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="import"/>
            <input id="imported_file" type="file" name="file"/>
            <input class="button" type="submit" name="btn_submit" value="Upload File"/>
        </form>
    </div>

    <div>
        <a href="<?php echo add_query_arg('delete','all');?>">Delete All Players</a>
    </div>
<?php
}

function fileprocessing(){
    if(isset($_POST) && isset($_POST['btn_submit']) && isset($_FILES['file']))
    {
        $upload_dir = wp_upload_dir();
        $filename = basename($_FILES['file']['name']);

        // Check folder permission and define file location

        if( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
            move_uploaded_file($_FILES["file"]["tmp_name"],$file);
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
            move_uploaded_file($_FILES["file"]["tmp_name"],$file);
        }

        $obj = PHPExcel_IOFactory::load($file);

        $data = $obj->getActiveSheet()->toArray(null,true,true,true);

        $columns = ['player_unique_id','player_name','headshot_url','team_unique_id','position','input_order'];

        foreach($data as $key=>$row){
            if ($key!=1){
                $players[] = $row['A'];
            }
        }

        $args = array('post_type' => 'players','posts_per_page' => -1);
        $playersIn = new WP_Query($args);
        $playersIn = $playersIn->get_posts();
        $playerInTemp=[];
        foreach ($playersIn as $playerIn){
            $playerInPostID = $playerIn->ID;
            $playerInTemp[$playerInPostID] = get_post_meta($playerInPostID,'player_unique_id',true);
        }
        $playersIn = $playerInTemp;

        foreach($data as $key=>$player){
            if ($key!=1 && !is_null($player['A']) && !in_array ($player['A'],$playersIn)){
                $players_post = array(
                    'post_title' => $player['B'],
                    'post_type' => 'players',
                    'post_status' => 'publish',
                );
                $id = wp_insert_post($players_post);

                $headshot_url = $player['C'];
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                media_sideload_image($headshot_url, $id);

                add_post_meta($id,'player_unique_id',$player['A']);
                add_post_meta($id,'team_unique_id',$player['D']);
                add_post_meta($id,'position',$player['E']);
            }
        }
    }
    wp_redirect (admin_url('edit.php?post_type=players&page=football.php'));
    add_notice('uploaded');
}
// ---------------------------------------------------------------------
// Metabox: players data
add_action('add_meta_boxes','players_meta_data');
function players_meta_data(){
    add_meta_box('players_meta_data','Players data','players_meta_box','players');
}

function players_meta_box($post) {
    ?>
    <div>
        <div>
            <label>team_unique_id</label><input type="text" name='team_unique_id' value="<?php echo get_post_meta(($post->ID),'team_unique_id',true)?>"/>
        </div>
        <div>
            <label>Position</label><input type="text" name='position' value="<?php echo get_post_meta(($post->ID),'position',true)?>"/>
        </div>
    </div>
<?php
}

function update_player_data($post_id){

    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);

    if($is_autosave || $is_revision){return;}

    if(isset($_POST['team_unique_id'])){
        update_post_meta($post_id,'team_unique_id',sanitize_text_field($_POST['team_unique_id']));
    }
    if(isset($_POST['position'])){
        update_post_meta($post_id,'position',sanitize_text_field($_POST['position']));
    }
}
add_action('save_post','update_player_data');

function delete_all_players(){
    if ($_GET && isset($_GET['delete'])){
        $args = array('post_type' => 'players','posts_per_page' => -1);
        $playersIn = new WP_Query($args);
        $playersIn = $playersIn->get_posts();
        foreach ($playersIn as $playerIn){
            wp_delete_post($playerIn->ID,true);
        }
        add_notice('deleted');
    }
}
add_action('init','delete_all_players');

function new_attachment($att_id){
    $p = get_post($att_id);
    if (get_post_type($p->post_parent)== 'players'){
        set_post_thumbnail($p->post_parent,$att_id);
    }
}
add_action('add_attachment','new_attachment');

function delete_associated_media($id) {
    // check if page
    if ('players' !== get_post_type($id)) return;

    $media = get_children(array(
        'post_parent' => $id,
        'post_type' => 'attachment'
    ));
    if (empty($media)) return;

    foreach ($media as $file) {
        // pick what you want to do
        wp_delete_attachment($file->ID);
        //unlink(get_attached_file($file->ID));
    }
}
add_action('before_delete_post', 'delete_associated_media');