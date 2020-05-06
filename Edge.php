<?php
define('rounds_asl_URL', plugin_dir_url(__FILE__));
global $wpdb;

class Edge{
    public function __construct() {
        add_shortcode( 'rounds', array($this, 'add_shortcode_rounds_box'));
        add_action( 'wp_enqueue_scripts', array($this, 'add_js_rounds_footer'));
        add_action('init', array($this, 'add_type_rounds'));
        add_action('admin_menu', array($this, 'add_custom_field_metabox_rounds'));
        add_action('save_post', array($this, 'custom_field_save_rounds'));
        add_action('admin_footer', array($this, 'round_plag_get_rounds'));
        add_action( 'admin_enqueue_scripts', array($this, 'round_admin_js_footer'));
        add_filter( 'mce_external_plugins', array($this, 'round_plag_add_buttons' ));
        add_filter( 'mce_buttons_2', array($this, 'round_plag_register_buttons' ));
        add_filter( 'add_menu_classes',array($this, 'round_plag_show_pending_number'));
        add_action('wp_ajax_roundplagadd', array($this, 'round_plag_form_add_q'));
        add_action('wp_ajax_nopriv_roundplagadd', array($this, 'round_plag_form_add_q'));
    }

    public function add_type_rounds(){
        global $wpdb;
//        $wpdb->query("UPDATE wp_postsmeta SET post_content = REPLACE (post_content, 'http://test.truemisha.ru', 'https://misha.blog');")

        $labels_rounds = array(
            'name' 				=> __('Преимущества'),
            'singular_name'		=> __('Преимущество'),
            'add_new' 			=> __('Добавить'),
            'add_new_item' 		=> __('Добавить'),
            'edit_item' 		=> __('Редактировать'),
            'new_item' 			=> __('Новое преимущество'),
            'view_item' 		=> __('Просмотреть преимущество'),
            'menu_name'			=> __('Преимущества')
        );
        $post_type_rounds = array(
            'labels' 			=> $labels_rounds,
            'singular_label' 	=> 'rounds',
            'public' 			=> true,
            'exclude_from_search' => true,
            'show_ui' 			=> true,
            'menu_icon' => 'dashicons-megaphone',
            'publicly_queryable'=> false,
            'show_in_nav_menus'=> false,
            'query_var'			=> false,
            'capability_type' 	=> 'post',
            'has_archive' 		=> false,
            'hierarchical' 		=> false,
            'rewrite' 			=> array('slug' => 'rounds', 'with_front' => true ),
            'supports'          => array('title'),
        );
        register_post_type('rounds', $post_type_rounds);

    }
    public $custom_field_metabox_rounds = array(
        'id' => 'customfield_rounds',
        'title' =>'Настройки',
        'page' => array('rounds'),
        'context' => 'normal',
        'priority' => 'default',
        'fields' => array(
            array(
                'name' =>'Количество преимуществ:',
                'desc' => '',
                'id' => 'count',
                'class' => 'count',
                'type' => 'radio',
                'options' => array (  // Параметры, всплывающие данные
                    'one' => array (
                        'label' => '1',  // Название поля
                        'value' => '1'  // Значение
                    ),
                    'two' => array (
                        'label' => '2',  // Название поля
                        'value' => '2'  // Значение
                    ),
                    'three' => array (
                        'label' => '3',  // Название поля
                        'value' => '3'  // Значение
                    )
                )
            ),
            array(
                'name' => 'Цвет первого преимущества',
                'desc' => '',
                'id' => 'color_first',
                'class' => 'color_first',
                'type' => 'select',
                'max' => 0,
                'options' => array (  // Параметры, всплывающие данные
                    'one' => array (
                        'label' => 'Желтый',  // Название поля
                        'value' => 'orange'  // Значение
                    ),
                    'two' => array (
                        'label' => 'Зеленый',  // Название поля
                        'value' => 'green'  // Значение
                    ),
                    'three' => array (
                        'label' => 'Синий',  // Название поля
                        'value' => 'blue'  // Значение
                    )
                )

            ),
            array(
                'name' => 'Текст первого преимущества: ',
                'desc' => '',
                'id' => 'text_first',
                'class' => 'text_first',
                'type' => 'text',
                'placeholder' => '',
                'max' => 0
            ),
            array(
                'name' => 'Цвет второго преимущества',
                'desc' => '',
                'id' => 'color_second',
                'class' => 'color_second',
                'type' => 'select',
                'max' => 0,
                'options' => array (  // Параметры, всплывающие данные
                    'one' => array (
                        'label' => 'Желтый',  // Название поля
                        'value' => 'orange'  // Значение
                    ),
                    'two' => array (
                        'label' => 'Зеленый',  // Название поля
                        'value' => 'green'  // Значение
                    ),
                    'three' => array (
                        'label' => 'Синий',  // Название поля
                        'value' => 'blue'  // Значение
                    )
                )
            ),
            array(
                'name' => 'Текст второго преимущества: ',
                'desc' => '',
                'id' => 'text_second',
                'class' => 'text_second',
                'type' => 'text',
                'placeholder' => '',
                'max' => 0
            ),
            array(
                'name' => 'Цвет третьего преимущества',
                'desc' => '',
                'id' => 'color_third',
                'class' => 'color_third',
                'type' => 'select',
                'max' => 0,
                'options' => array (  // Параметры, всплывающие данные
                    'one' => array (
                        'label' => 'Желтый',  // Название поля
                        'value' => 'orange'  // Значение
                    ),
                    'two' => array (
                        'label' => 'Зеленый',  // Название поля
                        'value' => 'green'  // Значение
                    ),
                    'three' => array (
                        'label' => 'Синий',  // Название поля
                        'value' => 'blue'  // Значение
                    )
                )
            ),
            array(
                'name' => 'Текст третьего преимущества: ',
                'desc' => '',
                'id' => 'text_third',
                'class' => 'text_third',
                'type' => 'text',
                'placeholder' => '',
                'max' => 0
            ),

        )
    );
    public function add_custom_field_metabox_rounds() {

        foreach($this->custom_field_metabox_rounds['page'] as $page_portfolio) {
            add_meta_box($this->custom_field_metabox_rounds['id'], $this->custom_field_metabox_rounds['title'], array($this, 'show_custom_field_metabox_rounds'), $this->custom_field_metabox_rounds['page'], 'normal', 'high', $this->custom_field_metabox_rounds);
        }
    }
    public function show_custom_field_metabox_rounds()	{
        global $post;
        global $prefix;
        global $wp_version;
        echo '<input type="hidden" name="custom_field_metabox_rounds_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
        echo '<br><p><b>Шорткод для вывода</b> - [rounds id="'.$post->ID.'"][/rounds]</p><br><table class="form-table">';
        foreach ($this->custom_field_metabox_rounds['fields'] as $field) {
            $meta = stripslashes(get_post_meta($post->ID, $field['id'], true));
            $field_checked = get_post_meta($post->ID, 'count', true);

            if($field_checked == "1" ) { $field_id_checked = 'checked="checked"';}
            echo '<tr class="'.$field['class'].'">',
            '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                '<td class="field_type_' . str_replace(' ', '_', $field['type']) . '">';
            switch ($field['type']) {
                case 'text':
                    echo "<input type='text' placeholder='", $field["placeholder"], "' name='", $field["id"], "' id='", $field["id"], "' value='", $meta , "' size='30' style='width:97%' /><br/>", "", stripslashes($field["desc"]);
                    break;
                case 'radio':
                    foreach ($field['options'] as $option) {
                        echo '<span style="padding-right: 20px"><input type="radio" name="'.$field['id'].'" id="'.$field['id'].'" ', $meta == $option['value'] ? ' checked="checked"' : '', ' value="'.$option['value'].'">'.$option['label'].'</span>';
                    }
                    break;
                case 'select':
                    echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                    foreach ($field['options'] as $option) {
                        echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                    }
                    echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
            }
            echo    '<td>',
            '</tr>';
        }
        echo '</table>';


    }
    public function custom_field_save_rounds($post_id) {
        global $meta_fields;  // Массив с нашими полями
        global $post;
        // проверяем наш проверочный код
        if (!wp_verify_nonce($_POST['custom_field_metabox_rounds_nonce'], basename(__FILE__))) {
            return $post_id;
        }
        // Проверяем авто-сохранение
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        // Проверяем права доступа
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        // Если все отлично, прогоняем массив через foreach
        foreach ($this->custom_field_metabox_rounds['fields'] as $field) {
            $old = get_post_meta($post_id, $field['id'], false);

            $new = $_POST[$field['id']];
            if ($new && $new != $old) {  // Если данные новые
                update_post_meta($post_id, $field['id'], $new); // Обновляем данные
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }
        }
    }

        public function add_shortcode_rounds_box($atts){
            global $post;
            extract(shortcode_atts(array('id' => '0'), $atts));
            $count = get_post_meta($id, 'count', true);
            $color_first = get_post_meta($id, 'color_first', true);
            $text_first = get_post_meta($id, 'text_first', true);
            $color_second = get_post_meta($id, 'color_second', true);
            $text_second = get_post_meta($id, 'text_second', true);
            $color_third = get_post_meta($id, 'color_third', true);
            $text_third = get_post_meta($id, 'text_third', true);

            $out .= '<div class="b-stickers">';
            if($count == 1){
                $out .= '<span class="b-sticker sticker-'.$color_first.' first">';
                $out .= '<span class="wrap-stick">'. $text_first .'</span>';
                $out .= '</span>';
            }elseif ($count == 2){
                $out .= '<span class="b-sticker sticker-'.$color_first.' first">';
                $out .= '<span class="wrap-stick">'. $text_first .'</span>';
                $out .= '</span>';
                $out .= '<span class="b-sticker sticker-'.$color_second.'">';
                $out .= '<span class="wrap-stick">'.$text_second.'</span>';
                $out .= '</span>';
            }else if ($count ==3){
                $out .= '<span class="b-sticker sticker-'.$color_first.' first">';
                $out .= '<span class="wrap-stick">'. $text_first .'</span>';
                $out .= '</span>';
                $out .= '<span class="b-sticker sticker-'.$color_second.'">';
                $out .= '<span class="wrap-stick">'.$text_second.'</span>';
                $out .= '</span>';
                $out .= '<span class="b-sticker sticker-'.$color_third.' last-sticker">';
                $out .= ' <span class="wrap-stick">'.$text_third.'</span>';
                $out .= '</span>';
            }

            $out .= '</div>';


            return $out;

        }

    public function round_plag_add_buttons( $plugin_array ){
        $args = array( 'post_type'=> 'rounds','post_status'=> 'publish','posts_per_page' =>-1,);
        $list_tags = get_posts( $args );
        if($list_tags){
            $plugin_array['button_round_item'] =  rounds_asl_URL . 'js/script.js';
        }
        return $plugin_array;
    }
    public function round_plag_register_buttons( $buttons ){
        array_push( $buttons, 'button_round_item' );
        return $buttons;
    }
    public function round_plag_get_rounds(){
        $args = array( 'post_type'=> 'rounds','post_status'=> 'publish','posts_per_page' =>-1,);
        $list_tags = get_posts( $args );
        echo '<script>var postsValues_round_button = {};';
        $count = 0;
        foreach($list_tags as $p){
            $p_id = $p->ID;
            $p_title = get_the_title($p->ID);
            echo "postsValues_round_button[{$p_id}] = '{$p_title}';";
            $count++;
        }
        echo '</script>';
    }
    public function add_js_rounds_footer() {
        wp_enqueue_style( 'style-round_asl', rounds_asl_URL . 'css/rounds.css');
    }
    public function round_admin_js_footer(){
        wp_enqueue_script( 'script-round_asl', rounds_asl_URL . 'js/round.js', array('jquery'), '1.1', true );
    }

    public function round_plag_show_pending_number( $menu ) {
        $type = "post";
        $status = "draft";
        $num_posts = wp_count_posts( $type, 'readable' );
        $pending_count = 0;
        if ( !empty($num_posts->$status) )
            $pending_count = $num_posts->$status;
        if ($type == 'post') {
            $menu_str = 'edit.php';
        } else {
            $menu_str = 'edit.php?post_type=' . $type;
        }
        foreach( $menu as $menu_key => $menu_data ) {
            if( $menu_str != $menu_data[2] )
                continue;
            $menu[$menu_key][0] .= " <span class='update-plugins count-$pending_count'><span class='plugin-count'>+" . number_format_i18n($pending_count) . '</span></span>';
        }
        return $menu;
    }
    public function round_plag_form_add_q(){
        $editor = apply_filters('the_content', $_POST['editor']);
        $title = $_POST['title'];
        $cat = $_POST['cat'];
        $my_post = array();
        $my_post['post_title'] = $title;
        $my_post['post_status'] = 'draft';
        $my_post['post_content'] = $editor;
        $my_post['post_category'] = array($cat);
        $new_post_id = wp_insert_post( $my_post );
        return true;
    }
}
global $rounds_asl_Plugin;
$rounds_asl_Plugin = new Edge();