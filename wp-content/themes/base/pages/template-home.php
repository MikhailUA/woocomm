<?php
/*
* Template Name: Home Page
*
*/
?>
<?php get_header(); ?>
<?php  while (have_posts()):the_post(); ?>
    <div id="main">
        <div id="content">
<!--        <h1>Choose Package</h1>
            <?php
/*            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 3,
                'order' => 'ASC'
            );

            $query = new WP_Query( $args );

            if ($query->have_posts()) :
                while ($query->have_posts()) :
                    $query->the_post();
                    $product = wc_get_product( $query->post->ID );*/?>

                <div class="column">

                    <a class="img-holder" href="<?php /*echo get_the_permalink($product->ID)*/?>">
                        <h3><?php /*the_title();*/?></h3>
                    </a>
                </div>
            <?php /*endwhile;*/?>

            --><?php /*endif;

            wp_reset_query(); // Remember to reset
            */?>


            <form method="post">

                <textarea name="text">;asdfksa;dlkf222222</textarea>
                <input type="submit">

            </form>

            <?php

                $text = $_POST['text'];//echo $text;
                $text = str_split($text);
                $symbols = array();
                $symbol = array('name' => '','freq' => 0);

                function search($symbols, $elem){
                    foreach($symbols as $key=>$s){
                        if ($s['name'] == $elem){
                            return $key;
                        }
                    }
                    return null;
                }
            // заполнение массива символов (символ -> частота)
                foreach ($text as $s) {
                    $key=search($symbols,$s);
                    if (!is_null($key)){
                        $symbols[$key]['freq']++;
                    }else{
                        $symbol['name'] = $s;
                        $symbol['freq']=1;

                        $symbols[] = $symbol;
                    }
                }

            // сортировка массива по частоте
                //var_dump($symbols);

                function bubble_sort($arr){
                    $len = count($arr)-1;
                    for ( $i = 0;  $i<$len; $i++ ){
                        for( $k =0; $k<$len; $k++ ){
                            if( $arr[$k+1]['freq'] > $arr[$k]['freq'] ){
                                $t = $arr[$k];
                                $arr[$k] = $arr[$k+1];
                                $arr[$k+1] = $t;
                            }
                        }
                    }

                    return $arr;
                }


                $symbols = bubble_sort($symbols);

            // создание дерева

                $tree = $symbols;

                function create_node($left, $right){
                    $node = array(
                         'name' => $right['name'].$left['name'],
                         'right' => '',
                         'left' => '',
                         'freq' => 0
                    );

                    $node['right'] = $right;
                    $node['left'] = $left;
                    $node['freq'] = $left['freq'] + $right['freq'];

                    return $node;
                }


                function create_tree($tree){

                    $len = count($tree)-1;
                    $n = create_node($tree[$len-1],$tree[$len]);

                    $tree[$len-1] = $n;
                    array_pop($tree);

                    $tree = bubble_sort($tree);

                    if($len-1 == 0){
                        return $tree;
                    }else{
                        return create_tree($tree);
                    }
                }

                $tree = create_tree($tree);

                $tree = $tree[0];

                var_dump($tree);

            // обход дерева

                function parse_tree($tree){

                    $t = $tree;

                    if (!empty($t['left'])){
                        return parse_tree($tree['left']);
                    }

                    if (!empty($t['right'])){
                        return parse_tree($tree['right']);
                    }

                }


            parse_tree($tree);


            // кодирвка символов


            // вывод кодировки


            // вывод дерева


            ?>



        </div>
    </div>
<?php endwhile;?>
<?php get_footer(); ?>
