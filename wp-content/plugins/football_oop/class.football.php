<?php

Class Football{

    public static $init = false;
    public static $players;
    public static $names;

    public static function init(){
        if (!self::$init){
            self::init_hooks();
        }
    }

    public static function init_hooks () {
        self::$init = true;
        add_action('init',array('Football','setup_post_type'),11);
        add_action('add_meta_boxes',array('Football','football_metaboxes'));
        add_action('admin_menu',array('Football','football_admin_subpage'));
        add_action('admin_post_nopriv_import', array('Football','add_players_from_file') );
        add_action('admin_post_import', array('Football','add_players_from_file' ));
        add_action('init',array('Football','delete_all_players'),12);
        add_action( 'admin_enqueue_scripts', array('Football','load_wp_media_files' ));
    }

    public static function load_wp_media_files() {
        wp_enqueue_script('upload',plugins_url('upload.js',__FILE__),array('jquery'));
        wp_enqueue_media();
    }

    public static function setup_post_type(){

        $labels = array(
            'name' => 'Players'
        );

        $supports = array('title','thumbnail');

        $args = array(
            'public' => true,
            'label' => 'Players',
            'labels' => $labels,
            'supports' => $supports
        );

        register_post_type('players',$args);
    }

    public static function football_metaboxes() {
        add_meta_box('players_data','Players data',array('Football','view_metabox'));
    }

    public static function view_metabox($post){
        require_once(plugin_dir_path(__FILE__).'view-metabox.php');
    }

    public static function football_admin_subpage(){
        add_submenu_page('edit.php?post_type=players','Upload','Upload','edit_posts','upload',array('Football','view_football_admin_subpage'));
    }

    public static function view_football_admin_subpage(){
        require_once(plugin_dir_path(__FILE__).'view-admin-subpage.php');
    }

    /*public static function add_players_from_file1(){
        if(isset($_POST) && isset($_POST['btn_submit']) && isset($_FILES['file'])){
            if ($_FILES['file']['error']==4){
                wp_redirect (admin_url('edit.php?post_type=players&page=upload'));
                add_notice('file is not chosen','error');
            }else{
                $players = self::Upload($_FILES['file']);
                $count=0;
                foreach ($players as $player){
                    if (!Player::find_player_by_unique_id($player)){
                        Player::add_player($player);
                        $count++;
                    }
                }
                wp_redirect (admin_url('edit.php?post_type=players&page=upload'));
                if ($count){
                    add_notice($count.' were uploaded','update');
                }else{
                    add_notice('nothing to upload, any new players','error');
                }
            }
        }
    }*/

    public static function add_players_from_file(){
        if(isset($_POST) && isset($_POST['btn_submit'])){
            if (empty($_POST['file-id'])){
                wp_redirect (admin_url('edit.php?post_type=players&page=upload'));
                add_notice('file is not chosen','error');
            }else{
                $file = get_attached_file($_POST['file-id']);

                $valid_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                $mime = wp_check_filetype($file);

                if ($mime['type']==$valid_type){
                    self::Upload($file);


                    if (!self::$players){
                        wp_redirect (admin_url('edit.php?post_type=players&page=upload'));
                        add_notice('excel file has invalid data','error');
                    }else{
                        $count=0;
                        foreach (self::$players as $player){
                            if (!Player::find_player_by_unique_id($player)){
                                Player::add_player($player);
                                $count++;
                            }
                        }
                        wp_redirect (admin_url('edit.php?post_type=players&page=upload'));
                        if ($count){
                            add_notice($count.' were uploaded','update');
                        }else{
                            add_notice('nothing to upload, any new players','error');
                        }
                    }
                }else{
                    wp_redirect (admin_url('edit.php?post_type=players&page=upload'));
                    add_notice('choosen file is not Excel2007 (.xslx)','error');
                }
            }
        }
    }

    public static function delete_all_players(){
        if ($_GET && isset($_GET['delete'])){
            $args = array('post_type' => 'players','posts_per_page' => -1);
            $players = new WP_Query($args);
            $players = $players->get_posts();
            foreach ($players as $player){
                Player::delete_player($player);
            }

            if ($c = count($players)){
                add_notice($c.' were deleted','update');
            }else{
                add_notice('nothing to delete','error');
            }
        }
    }

    private static function Upload($file){
/*        $upload_dir = wp_upload_dir();
        $filename = basename($file['name']);

        // Check folder permission and define file location

        if( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
            move_uploaded_file($file["tmp_name"],$file);
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
            move_uploaded_file($file["tmp_name"],$file);
        }*/
        $obj = PHPExcel_IOFactory::load($file);

        $data = $obj->getActiveSheet()->toArray(null,true,true,true);

        $names = array_diff($data[1], [null]);
        self::$names = array_flip($names);

        if (array_key_exists('player_unique_id',self::$names)){
            unset($data[1]);
            self::$players = $data;
            return self::$players;
        }else{
            return false;
        }
    }
}