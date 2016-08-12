<?php

Class Player extends Football{

    public static $init = false;

    public static function init(){
        if (!self::$init){
            self::init_hooks();
        }
    }

    public static function init_hooks () {
        add_action('save_post',array('Player','update_player_data'));
        add_action('add_attachment',array('Player','new_attachment'));
        add_action('before_delete_post', array('Player','delete_associated_media'));
    }

    public static function get_data($player,$name){
        return $player[self::$names[$name]];
    }

    public static function find_player_by_unique_id($player){
        global $wpdb;
        $players_unique_ids = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s" , 'player_unique_id'));
        $players_unique_ids = array_map(function($a){return $a->meta_value;},$players_unique_ids);
        return in_array(self::get_data($player,'player_unique_id'),$players_unique_ids);
    }

    public static function delete_player($player){
        wp_delete_post($player->ID,true);
    }

    public static function add_player($player){
        $players_post = array(
            'post_title' => self::get_data($player,'player_name'),
            'post_type' => 'players',
            'post_status' => 'publish');

        $post_id = wp_insert_post($players_post);

        $fields = ['player_unique_id','team_unique_id','position','input_order'];

        foreach ($fields as $field){
            add_post_meta($post_id,$field,self::get_data($player,$field));
        }
        self::add_featured_image($player,$post_id);
    }

    public static function add_featured_image($player,$post_id){
        $headshot_url = self::get_data($player,'headshot_url');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        media_sideload_image($headshot_url, $post_id);
    }

    public static function new_attachment($att_id){
        $p = get_post($att_id);
        if (get_post_type($p->post_parent)== 'players'){
            set_post_thumbnail($p->post_parent,$att_id);
        }
    }

    public static function update_player_data($post_id){

        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);

        if($is_autosave || $is_revision){return;}

        if(isset($_POST['team_unique_id'])){
            update_post_meta($post_id,'team_unique_id',sanitize_text_field($_POST['team_unique_id']));
        }
        if(isset($_POST['position'])){
            update_post_meta($post_id,'position',sanitize_text_field($_POST['position']));
        }
        if(isset($_POST['input_order'])){
            update_post_meta($post_id,'input_order',sanitize_text_field($_POST['input_order']));
        }
    }

    public static function delete_associated_media($id) {
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
}