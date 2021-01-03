<?php
define('path_plugin', plugin_dir_url(__FILE__));
global $wpdb;
global $post;


$link_table = $wpdb->query("SELECT `meta_key` FROM `wp_postmeta` WHERE meta_key='link'");
if ($link_table != 0) {
    $wpdb->query("UPDATE wp_postmeta SET meta_key  = REPLACE(meta_key, 'link', 'link_wares') WHERE meta_key = 'link'");
}
foreach (get_option('option_name') as $key => $value) {
    switch ($key) {
        case 'checkbox':
            if ($value == 1) {
                $wpdb->query("UPDATE wp_postmeta SET meta_value='on' WHERE meta_key='wares_ajax'");
            } else {
                $wpdb->query("UPDATE wp_postmeta SET meta_value='' WHERE meta_key='wares_ajax'");
            }
    }
}
$posts = query_posts(['post_type' => 'wares', 'posts_per_page' => -1, 'post_status' => 'any']);
foreach ($posts as $post) {
    if (count(get_post_meta($post->ID, 'wares_ajax')) == 0) {
        update_post_meta($post->ID, 'wares_ajax', '');
    }
}
add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=wares', 'Настройки продуктов', 'Настройки', 'manage_options', 'wares-setting', 'wares_settings');
});
function wares_settings()
{
    $count = array();
    $posts = query_posts(['post_type' => 'wares', 'posts_per_page' => -1, 'post_status' => 'any']);
    $published_posts = wp_count_posts('wares')->publish;

    foreach ($posts as $post) {
        if (get_post_meta($post->ID, 'wares_ajax', true) == 'on')
            array_push($count, get_post_meta($post->ID, 'wares_ajax', true));
    }

    ?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title() ?></h2>
        <p>Всего продуктов <?php echo $published_posts; ?></p>
        <p>Количество продуктов с AJAX загрузкой <?php echo count($count); ?></p>
        <form action="options.php" method="POST">
            <?php
            settings_fields('wares_group');     // скрытые защитные поля
            do_settings_sections('wares-setting'); // секции с настройками (опциями). У нас она всего одна 'section_id'
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Регистрируем настройки.
 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
 */
add_action('admin_init', 'wares_plugin_settings');
function wares_plugin_settings()
{
    // параметры: $wares_group, $option_name, $sanitize_callback
    register_setting('wares_group', 'option_name', 'wares_sanitize_callback');

    // параметры: $id, $title, $callback, $page
    add_settings_section('wares_section_id', 'Основные настройки', '', 'wares-setting');

    // параметры: $id, $title, $callback, $page, $section, $args
    add_settings_field('wares_all_checkbox', 'Скрыть все продукты?', 'fill_primer_field2', 'wares-setting', 'wares_section_id');
}


## Заполняем опцию 2
function fill_primer_field2()
{
    $val = get_option('option_name');
    $val = $val ? $val['checkbox'] : $val[''];
    ?>
    <label><input type="checkbox" name="option_name[checkbox]" value="1" <?php checked(1, $val) ?> /></label>
    <?php
}

## Очистка данных
function wares_sanitize_callback($options)
{
    $options['checkbox'] = intval($options['checkbox']);

//    die(print_r( $options['checkbox'] )); // Array ( [input] => aaaa [checkbox] => 1 )

    return $options;
}

//print_r(get_option('option_name'));



$wares_metabox = [
    'id' => 'field_wares',
    'title' => 'Настройки',
    'page' => ['wares'],
    'context' => 'normal',
    'priority' => 'default',
    'fields' => [
        [
            'name' => 'Выбор шаблона:',
            'id' => 'skin',
            'type' => 'radio',
            'options' => [
                'one' => [
                    'label' => 'Чёрный',
                    'value' => 'new-count'
                ],
                'two' => [
                    'label' => 'Красный',
                    'value' => 'old-count'
                ],
                'three' => [
                    'label' => 'Серый',
                    'value' => 'old'
                ]
            ]
        ],
        [
            'name' => 'Номер:',
            'desc' => 'Будет показан если поле заполнено',
            'id' => 'count',
            'type' => 'text'
        ],
        [
            'name' => 'Ссылка:',
            'id' => 'link_wares',
            'type' => 'text'
        ],
        [
            'name' => 'Текст на верхней плашке:',
            'id' => 'text_left',
            'type' => 'text'
        ],
        [
            'name' => 'Заголовок в описании:',
            'id' => 'text_link',
            'type' => 'text',
        ],
        [
            'name' => 'Текст кнопки:',
            'id' => 'btn_text',
            'type' => 'text'
        ],
        [
            'name' => 'Скрыть от поиска:',
            'id' => 'wares_ajax',
            'type' => 'checkbox'
        ],
        [
            'name' => 'Стрелка возле кнопки:',
            'id' => 'arrow_btn',
            'type' => 'checkbox'
        ],
        [
            'name' => 'Пульсация кнопки:',
            'id' => 'animation_btn',
            'type' => 'checkbox'
        ],
        [
            'name' => 'Увеличение кнопки',
            'id' => 'zoom_btn',
            'type' => 'checkbox'
        ]
    ]
];

add_action('created_term', function ($term_id, $tt_id, $taxonomy) {
    if ($taxonomy == 'category') {
        $term = get_term($term_id, $taxonomy);
        $args = array('slug' => 'cat-' . $term->slug);
        wp_update_term($term_id, $taxonomy, $args);
    }
}, 10, 3);
add_shortcode('ware_item', function ($atts, $content = null) {
    extract(shortcode_atts(['id' => '0'], $atts));
    $wares_link = get_post_meta($id, 'link_wares', true);
    $count = get_post_meta($id, 'count', true);
    $text_left = get_post_meta($id, 'text_left', true);
    $text_link = get_post_meta($id, 'text_link', true);
    $btn_text = get_post_meta($id, 'btn_text', true);
    $arrow_btn = get_post_meta($id, 'arrow_btn', true);
    $ajax = get_post_meta($id, 'wares_ajax', true);
    $animation_btn = get_post_meta($id, 'animation_btn', true);
    $zoom_btn = get_post_meta($id, 'zoom_btn', true);
    $post_thumb = get_the_post_thumbnail($id, 'product_thumb');
    $post_title = get_the_title($id);
    $post_content = get_post_field('post_content', $id);
    $skin = get_post_meta($id, 'skin', true);
    $out = get_post_meta($id, 'ajax', true);
    if ($ajax == '') {
        switch ($skin) {
            case 'old' :
                $out .= '<div class="product_wrap old-count">';
                $out .= '<div class="product_block">';
                if ($count != '') {
                    $out .= '<div class="count-o">' . $count . '</div>';
                }
                $out .= '<div class="label-wrap"><div class="label">' . $text_left . '</div></div>';

                $out .= ' <div class="product_block-wrapper">';
                $out .= '<div class="product_block-inner">';
                $out .= '<div class="group">';
                $out .= '<div class="product-image"><a href="' . $wares_link . '">';
                $out .= $post_thumb;
                $out .= '</a></div>';
                $out .= '<div class="meta">';
                $out .= '<div class="product-name"><a href="' . $wares_link . '">' . $post_title . '</a></div>';
                $out .= '<div class="title"><a href="' . $wares_link . '">' . $text_link . '</a></div>';
                $out .= '<span class="description">' . $post_content . '</span>';
                $out .= '<div class="product-pricebox-sources">';
                if ($arrow_btn != "") {
                    $out .= '<p><img data-flat-attr="yes" src="' . path_plugin . 'i/arrow-flash-small.gif"></p>';
                }
                if ($zoom_btn != "") {
                    $out .= '<div class="btn-box btn-zoom">';
                } else {
                    $out .= '<div class="btn-box">';
                }

                if ($animation_btn != "") {

                    $out .= '<a href="' . $wares_link . '" class="btn btn-size-normal pulses" style="color:#ffffff; background-color:#fa5d49;" >' . $btn_text . '</a>';
                } else {
                    $out .= '<a href="' . $wares_link . '" class="btn btn-size-normal" style="color:#ffffff; background-color:#fa5d49;" >' . $btn_text . '</a>';

                }
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                break;
            case 'old-count' :
                $out .= '<div class="product_wrap product_wrap-oc" >';
                $out .= '<div class="product_block color">';
                $out .= '<div class="label">' . $text_left . '</div>';
                $out .= ' <div class="product_block-wrapper">';
                if ($count != '') {
                    $out .= '<div class="product_block-count color">' . $count . '</div>';
                }
                $out .= '<div class="product_block-inner">';
                $out .= '<div class="group">';
                $out .= '<div class="product-image"><a href="' . $wares_link . '">';
                $out .= $post_thumb;
                $out .= '</a></div>';
                $out .= '<div class="meta">';
                $out .= '<div class="product-name"><a href="' . $wares_link . '">' . $post_title . '</a></div>';
                $out .= '<div class="title"><a href="' . $wares_link . '">' . $text_link . '</a></div>';
                $out .= '<span class="description">' . $post_content . '</span>';
                $out .= '<div class="product-pricebox-sources">';
                if ($arrow_btn != "") {
                    $out .= '<p><img data-flat-attr="yes" src="' . path_plugin . 'i/arrow-flash-small.gif"></p>';
                }
                if ($zoom_btn != "") {
                    $out .= '<div class="btn-box btn-zoom">';
                } else {
                    $out .= '<div class="btn-box">';
                }

                if ($animation_btn != "") {

                    $out .= '<a href="' . $wares_link . '" class="btn btn-size-normal pulses" style="color:#ffffff; background-color:#fa5d49;" >' . $btn_text . '</a>';
                } else {
                    $out .= '<a href="' . $wares_link . '" class="btn btn-size-normal" style="color:#ffffff; background-color:#fa5d49;" >' . $btn_text . '</a>';

                }
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                break;
            case 'new-count' :
                $out .= '<div class="product_wrap-2 product_wrap-2-nc">';
                $out .= '<div class="product-flex">';
                $out .= '<div class="product_block-2">';
                $out .= '<div class="product_block-2__label-wrap">';
                $out .= '<div class="product_block-2__label">' . $text_left . '</div>';
                $out .= '</div>';
                $out .= '<div class="product_block-2__column-image">';
                if ($count != '') {
                    $out .= '<div class="product_block-new__count">' . $count . '</div>';
                }
                $out .= '<div class="product-image-container"><a href="' . $wares_link . '">';
                $out .= $post_thumb;
                $out .= '</a></div>';
                if ($zoom_btn != "") {
                    $out .= '<div class="btn-box btn-zoom">';
                } else {
                    $out .= '<div class="btn-box">';
                }
                if ($animation_btn != "") {
                    $out .= '<a href="' . $wares_link . '" class="btn_item__button pulses">' . $btn_text . '</a>';
                } else {
                    $out .= '<a href="' . $wares_link . '" class="btn_item__button" >' . $btn_text . '</a>';

                }
                $out .= '</div>';
                $out .= '</div>';
                $out .= '<div class="product_block-2__column-content">';
                $out .= '<div class="product_block-2__bar"></div>';
                $out .= '<div class="product_block-2__title"><a href="' . $wares_link . '">' . $post_title . '</a></div>';
                $out .= '<div class="title"><a href="' . $wares_link . '">' . $text_link . '</a></div>';
                $out .= '<span class="description">' . $post_content . '</span>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                break;
            default :
                $out .= '<div class="product_wrap-2 product_wrap-2-nc">';
                $out .= '<div class="product-flex">';
                $out .= '<div class="product_block-2">';
                $out .= '<div class="product_block-2__label-wrap">';
                $out .= '<div class="product_block-2__label">' . $text_left . '</div>';
                $out .= '</div>';
                $out .= '<div class="product_block-2__column-image">';
                if ($count != '') {
                    $out .= '<div class="product_block-new__count">' . $count . '</div>';
                }
                $out .= '<div class="product-image-container"><a href="' . $wares_link . '">';
                $out .= $post_thumb;
                $out .= '</a></div>';
                if ($zoom_btn != "") {
                    $out .= '<div class="btn-box btn-zoom">';
                } else {
                    $out .= '<div class="btn-box">';
                }
                if ($animation_btn != "") {
                    $out .= '<a href="' . $wares_link . '" class="btn_item__button pulses">' . $btn_text . '</a>';
                } else {
                    $out .= '<a href="' . $wares_link . '" class="btn_item__button" >' . $btn_text . '</a>';

                }
                $out .= '</div>';
                $out .= '</div>';
                $out .= '<div class="product_block-2__column-content">';
                $out .= '<div class="product_block-2__bar"></div>';
                $out .= '<div class="product_block-2__title"><a href="' . $wares_link . '">' . $post_title . '</a></div>';
                $out .= '<div class="title"><a href="' . $wares_link . '">' . $text_link . '</a></div>';
                $out .= '<span class="description">' . $post_content . '</span>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
        }
    } else {
        $out = '<div class="ajax-content" data-id="' . $id . '" ></div>';
    }
    return $out;
});
add_action('init', function () {
    $labels = [
        'name' => __('Товары'),
        'singular_name' => __('Товар'),
        'add_new' => __('Добавить новый'),
        'add_new_item' => __('Добавить новый товар'),
        'edit_item' => __('Редактировать товар'),
        'new_item' => __('Новый товар'),
        'view_item' => __('Просмотреть товар'),
        'search_items' => __('Найти товар'),
        'not_found' => __('Товар не найден'),
        'not_found_in_trash' => __('Товар не был найден в корзине'),
        'all_items' => __('Все товары'),
        'insert_into_item' => __('Вставить в товар'),
        'uploaded_to_this_item' => __('Загружено для этого товара'),
        'featured_image' => __('Миниатюра товара'),
        'set_featured_image' => __('Установить миниатюру товара'),
        'remove_featured_image' => __('Удалить миниатюру товара'),
        'use_featured_image' => __('Использовать как миниатюру товара'),
        'attributes' => __('Аттрибуты товара'),
        'item_updated' => __('Товар обновлен'),
        'item_published' => __('Товар опубликован'),
        'item_published_privately' => __('Товар опубликован приватно'),
        'menu_name' => __('Товары')
    ];
    $wares_post = [
        'labels' => $labels,
        'singular_label' => 'wares',
        'public' => false,
        'exclude_from_search' => true,
        'query_var' => 'wares',
        'publicly_queryable' => false,
        'has_archive' => false,
        'hierarchical' => false,
        'can_export' => true,
        'rewrite' => ['slug' => 'wares', 'with_front' => true],
        'supports' => ['title', 'editor', 'thumbnail',],
        'menu_icon' => 'dashicons-cart',
        'taxonomies' => ['category'],
    ];
    register_taxonomy_for_object_type('category', 'wares');
    register_post_type('wares', $wares_post);
});

add_action( 'template_redirect', function() {
    $post_type = get_post_type( );

    if( is_post_type_archive('wares')|| $post_type == 'wares' ){
        wp_redirect( get_site_url(), 301 );
        exit;
    }
} );

add_action('admin_menu', function () use ($wares_metabox) {
    foreach ($wares_metabox['page'] as $meta) {
        add_meta_box($wares_metabox['id'], $wares_metabox['title'], 'show_metabox', $wares_metabox['page'], 'normal', 'high', $wares_metabox);
    }

});
function show_metabox()
{
    global $post;
    global $wares_metabox;
    echo '<input type="hidden" name="wares_metabox_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    /**
     * Вывод HTML с подсказками
     */
    echo '<div style="background:#def9e5;padding: 10px 5px"><b style="font-size: 18px;">Подсказка с классами:</b><br>
                <p><b>Класс заголовка:</b> <pre style="color: #000;"> &lt;div class="product_block-2__specs"&gt;&lt;/div&gt;</pre></p>
                <p><b>Класс черты:</b> <pre style="color: #000;"> &lt;div class="product_block-2__bar"&gt;&lt;/div&gt;</pre></p>
                <p><b>Класс черты (Розовая):</b> <pre style="color: #000;"> &lt;div class="product_block-2__bar color"&gt;&lt;/div&gt;</pre></p>
              </div>
              <br>
              <hr>
              <p><b>Шорткод для вывода</b> - [ware_item id="' . $post->ID . '"]</p>   
              <table class="form-table">';
    /**
     * Формирование полей и вывод их в админке
     */
    foreach ($wares_metabox['fields'] as $field) {
        $meta = stripslashes(get_post_meta($post->ID, $field['id'], true));
        echo '<tr>',
        '<th style="width:20%;"><label for="', $field["id"], '">', $field["name"], '</label></th>',
            '<td class="field_type_' . str_replace(' ', '_', $field['type']) . '">';
        switch ($field['type']) {
            case 'text':
                echo "<input type='text' name='", $field["id"], "' value='" . $meta . "' size='30', style='width:97%'/><br/>";
                break;
            case 'radio':
                foreach ($field['options'] as $option) {
                    echo '<span style="padding-right: 20px;"><input type="radio" name="' . $field['id'] . '" id="' . $field['id'] . '" ', $meta == $option['value'] ? 'checked="checked"' : '', 'value="' . $option['value'] . '">' . $option['label'] . '</span>';
                }
                break;
            case 'checkbox':
                echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" ', $meta ? 'checked="checked"' : ' ' . '>';
                break;
        }
        echo '<td>',
        '</tr>';
    }
    echo '</table>';
}

add_action('save_post', function ($post_id) use ($wares_metabox) {

    if (!wp_verify_nonce($_POST['wares_metabox_nonce'], basename(__FILE__))) {
        return $post_id;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_page', $post_id)) {
        return $post_id;
    }
    foreach ($wares_metabox['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if (($new || $new === '0') && $new !== $old) {
            if ($field['type'] == 'checkbox') {
                if (isset($new)) {
                    update_post_meta($post_id, $field['id'], $new);
                } else {
                    delete_post_meta($post_id, $field['id']);
                }
            } else {
                if (is_string($new)) {
                    $new = $new;
                }
                update_post_meta($post_id, $field['id'], $new);
            }
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
});
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('wares-style', path_plugin . 'css/products.css');
    wp_enqueue_script('wares-script', path_plugin . 'js/main-prod.js', null, null, true);
    wp_enqueue_script('wares-async', path_plugin . 'js/wares-script.js', null, null, true);
});
add_action('admin_footer', function () {
    wp_enqueue_script('magnific', path_plugin . 'assets/magnific/jquery.magnific-popup.js', array('jquery'), '1.0', true);
    wp_enqueue_style('magnific-style', path_plugin . 'assets/magnific/magnific-popup.css');
    wp_enqueue_style('popup-style', path_plugin . 'css/window_popup.css');

    $args = array('post_type' => 'wares', 'post_status' => 'publish', 'posts_per_page' => -1,);
    $list_tags = get_posts($args);

    ob_start(); ?>
    <div id="product_wrap" style="display:none">
        <div id="popup">
            <div class="wares_search_wrap">
                <input type="text" id="wares_search" placeholder="Введите название товара" autofocus>
                <div class="small-text">
                    <div class="small-left">Набирать текст только в нижнем регистре</div>
                    <div class="small-right"></div>
                </div>
            </div>
            <table>
                <thead>
                <tr class="fixed">
                    <th class="tb_first">ID</th>
                    <th class="tb_second">Название товара</th>
                    <th class="tb_last">Категория</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $count = 0;
                foreach ($list_tags as $p) {
                    $p_name = get_the_category($p->ID);

                    echo '
                        <tr class="select_wares" id=' . $p->ID . '>
                            <td class="tb_first">' . $p->ID . '</td>
                            <td class="tb_second">' . get_the_title($p->ID) . '</td>';
                    if (!empty($p_name)) {
                        foreach ($p_name as $p_n) {
                            $cat_n = $p_n->name;

                            echo '<td class="tb_last">' . $cat_n . '</td>';
                            $count++;

                        }
                    } else {
                        echo '<td class="tb_last">Без категории</td>';
                    }

                    echo '</tr>';
                } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    echo $output;
});
add_filter('add_menu_classes', function ($menu) {
    $type = "post";
    $status = "draft";
    $num_posts = wp_count_posts($type, 'readable');
    $pending_count = 0;
    if (!empty($num_posts->$status))
        $pending_count = $num_posts->$status;
    if ($type == 'post') {
        $menu_str = 'edit.php';
    } else {
        $menu_str = 'edit.php?post_type=' . $type;
    }
    foreach ($menu as $menu_key => $menu_data) {
        if ($menu_str != $menu_data[2])
            continue;
        $menu[$menu_key][0] .= " <span class='update-plugins count-$pending_count'><span class='plugin-count'>+" . number_format_i18n($pending_count) . '</span></span>';
    }
    return $menu;
});
add_filter('mce_external_plugins', function ($plugin_array) {
    $args = array('post_type' => 'wares', 'post_status' => 'publish', 'posts_per_page' => -1,);
    $list_tags = get_posts($args);
    if ($list_tags) {
        $plugin_array['button_ware_asl'] = path_plugin . 'js/button_prod.js';
    }
    return $plugin_array;
});
add_filter('mce_buttons_2', function ($buttons) {
    array_push($buttons, 'button_ware_asl');
    return $buttons;
});

add_action('wp_ajax_wares', 'wares_ajax');
add_action('wp_ajax_nopriv_wares', 'wares_ajax');
add_action('wp_enqueue_scripts', 'wares_data');

function wares_data()
{
    wp_localize_script('wares-async', 'wares_ajax', array(
        'wares_url' => admin_url('admin-ajax.php')
    ));
}

function wares_ajax()
{
    $id = sanitize_text_field($_POST['id']);
    $wares_link = get_post_meta($id, 'link_wares', true);
    $count = get_post_meta($id, 'count', true);
    $text_left = get_post_meta($id, 'text_left', true);
    $text_link = get_post_meta($id, 'text_link', true);
    $btn_text = get_post_meta($id, 'btn_text', true);
    $arrow_btn = get_post_meta($id, 'arrow_btn', true);
    $animation_btn = get_post_meta($id, 'animation_btn', true);
    $zoom_btn = get_post_meta($id, 'zoom_btn', true);
    $post_thumb = get_the_post_thumbnail($id, 'product_thumb');
    $post_title = get_the_title($id);
    $post_content = get_post_field('post_content', $id);
    $skin = get_post_meta($id, 'skin', true);
    switch ($skin) {
        case 'old' :
            $out = '<div class="product_wrap old-count">';
            $out .= '<div class="product_block">';
            if ($count != '') {
                $out .= '<div class="count-o">' . $count . '</div>';
            }
            $out .= '<div class="label-wrap"><div class="label">' . $text_left . '</div></div>';

            $out .= ' <div class="product_block-wrapper">';
            $out .= '<div class="product_block-inner">';
            $out .= '<div class="group">';
            $out .= '<div class="product-image"><a href="' . $wares_link . '">';
            $out .= $post_thumb;
            $out .= '</a></div>';
            $out .= '<div class="meta">';
            $out .= '<div class="product-name"><a href="' . $wares_link . '">' . $post_title . '</a></div>';
            $out .= '<div class="title"><a href="' . $wares_link . '">' . $text_link . '</a></div>';
            $out .= '<span class="description">' . $post_content . '</span>';
            $out .= '<div class="product-pricebox-sources">';
            if ($arrow_btn != "") {
                $out .= '<p><img data-flat-attr="yes" src="' . path_plugin . 'i/arrow-flash-small.gif"></p>';
            }
            if ($zoom_btn != "") {
                $out .= '<div class="btn-box btn-zoom">';
            } else {
                $out .= '<div class="btn-box">';
            }

            if ($animation_btn != "") {

                $out .= '<a href="' . $wares_link . '" class="btn btn-size-normal pulses" style="color:#ffffff; background-color:#fa5d49;" >' . $btn_text . '</a>';
            } else {
                $out .= '<a href="' . $wares_link . '" class="btn btn-size-normal" style="color:#ffffff; background-color:#fa5d49;" >' . $btn_text . '</a>';

            }
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            break;
        case 'old-count' :
            $out .= '<div class="product_wrap product_wrap-oc" >';
            $out .= '<div class="product_block color">';
            $out .= '<div class="label">' . $text_left . '</div>';
            $out .= ' <div class="product_block-wrapper">';
            if ($count != '') {
                $out .= '<div class="product_block-count color">' . $count . '</div>';
            }
            $out .= '<div class="product_block-inner">';
            $out .= '<div class="group">';
            $out .= '<div class="product-image"><a href="' . $wares_link . '">';
            $out .= $post_thumb;
            $out .= '</a></div>';
            $out .= '<div class="meta">';
            $out .= '<div class="product-name"><a href="' . $wares_link . '">' . $post_title . '</a></div>';
            $out .= '<div class="title"><a href="' . $wares_link . '">' . $text_link . '</a></div>';
            $out .= '<span class="description">' . $post_content . '</span>';
            $out .= '<div class="product-pricebox-sources">';
            if ($arrow_btn != "") {
                $out .= '<p><img data-flat-attr="yes" src="' . path_plugin . 'i/arrow-flash-small.gif"></p>';
            }
            if ($zoom_btn != "") {
                $out .= '<div class="btn-box btn-zoom">';
            } else {
                $out .= '<div class="btn-box">';
            }

            if ($animation_btn != "") {

                $out .= '<a href="' . $wares_link . '" class="btn btn-size-normal pulses" style="color:#ffffff; background-color:#fa5d49;" >' . $btn_text . '</a>';
            } else {
                $out .= '<a href="' . $wares_link . '" class="btn btn-size-normal" style="color:#ffffff; background-color:#fa5d49;" >' . $btn_text . '</a>';

            }
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            break;
        case 'new-count' :
            $out = '<div class="product_wrap-2 product_wrap-2-nc">';
            $out .= '<div class="product-flex">';
            $out .= '<div class="product_block-2">';
            $out .= '<div class="product_block-2__label-wrap">';
            $out .= '<div class="product_block-2__label">' . $text_left . '</div>';
            $out .= '</div>';
            $out .= '<div class="product_block-2__column-image">';
            if ($count != '') {
                $out .= '<div class="product_block-new__count">' . $count . '</div>';
            }
            $out .= '<div class="product-image-container"><a href="' . $wares_link . '">';
            $out .= $post_thumb;
            $out .= '</a></div>';
            if ($zoom_btn != "") {
                $out .= '<div class="btn-box btn-zoom">';
            } else {
                $out .= '<div class="btn-box">';
            }
            if ($animation_btn != "") {
                $out .= '<a href="' . $wares_link . '" class="btn_item__button pulses">' . $btn_text . '</a>';
            } else {
                $out .= '<a href="' . $wares_link . '" class="btn_item__button" >' . $btn_text . '</a>';

            }
            $out .= '</div>';
            $out .= '</div>';
            $out .= '<div class="product_block-2__column-content">';
            $out .= '<div class="product_block-2__bar"></div>';
            $out .= '<div class="product_block-2__title"><a href="' . $wares_link . '">' . $post_title . '</a></div>';
            $out .= '<div class="title"><a href="' . $wares_link . '">' . $text_link . '</a></div>';
            $out .= '<span class="description">' . $post_content . '</span>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            break;
        default :
            $out = '<div class="product_wrap-2 product_wrap-2-nc">';
            $out .= '<div class="product-flex">';
            $out .= '<div class="product_block-2">';
            $out .= '<div class="product_block-2__label-wrap">';
            $out .= '<div class="product_block-2__label">' . $text_left . '</div>';
            $out .= '</div>';
            $out .= '<div class="product_block-2__column-image">';
            if ($count != '') {
                $out .= '<div class="product_block-new__count">' . $count . '</div>';
            }
            $out .= '<div class="product-image-container"><a href="' . $wares_link . '">';
            $out .= $post_thumb;
            $out .= '</a></div>';
            if ($zoom_btn != "") {
                $out .= '<div class="btn-box btn-zoom">';
            } else {
                $out .= '<div class="btn-box">';
            }
            if ($animation_btn != "") {
                $out .= '<a href="' . $wares_link . '" class="btn_item__button pulses">' . $btn_text . '</a>';
            } else {
                $out .= '<a href="' . $wares_link . '" class="btn_item__button" >' . $btn_text . '</a>';

            }
            $out .= '</div>';
            $out .= '</div>';
            $out .= '<div class="product_block-2__column-content">';
            $out .= '<div class="product_block-2__bar"></div>';
            $out .= '<div class="product_block-2__title"><a href="' . $wares_link . '">' . $post_title . '</a></div>';
            $out .= '<div class="title"><a href="' . $wares_link . '">' . $text_link . '</a></div>';
            $out .= '<span class="description">' . $post_content . '</span>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
    }
    echo $out;
    wp_die();
}