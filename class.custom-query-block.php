<?php

class Custom_Query_Block
{
    private static $initiated = false;
    protected static $instance = null;

    protected function __construct()
    {
    }

    public static function cqb_get_instance()
    {
        null === self::$instance and self::$instance = new self;
        return self::$instance;
    }

    public static function cqb_init()
    {
        if (!self::$initiated) {
            self::cqb_init_hooks();
        }
    }

    /*
     * Init all hooks
     */
    private static function cqb_init_hooks()
    {
        self::$initiated = true;

        add_action('enqueue_block_assets', array('Custom_Query_Block', 'cqb_custom_block_assets'));
        //add_action('wp_enqueue_scripts', array('Custom_Query_Block', 'cqb_custom_block_frontend_assets'));
        add_action('rest_api_init', array('Custom_Query_Block', 'cqb_wp_rest_endpoints'));

        if(function_exists('register_block_type')) {
            register_block_type('cqb/block-custom-query-block',
                array(
                    'render_callback' => array('Custom_Query_Block', 'cqb_block_callback')
                )
            );
        }

    }

    public static function cqb_locate_template($template_name, $template_path = '', $default_path = '')
    {
        //set variable to search in cqb-templates folder of theme.
        if (!$template_path) {
            $template_path = 'template-parts/blocks/';
        }

        //set default plugin templates path.
        if (!$default_path) {
            $default_path = plugin_dir_path(__FILE__) . 'templates/';
        }

        //search template file in theme folder.
        $template = locate_template(array(
            $template_path . $template_name,
            $template_name
        ));

        //get plugins template file.
        if (!$template) {
            $template = $default_path . $template_name;
        }

        return apply_filters('cqb_locate_template', $template, $template_name, $template_path, $default_path);
    }

    public static function cqb_get_template($template_name, $args = array(), $tempate_path = '', $default_path = '')
    {
        if (is_array($args) && isset($args)) {
            extract($args);
        }

        $template_file = self::cqb_locate_template($template_name, $tempate_path, $default_path);

        if (!file_exists($template_file)) {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
            return;
        }

        ob_start();

        include $template_file;

        $output = ob_get_clean();

        return $output;
    }

    public static function cqb_block_callback($attributes)
    {

        //get data based on attribute selection
        $post_query = Custom_Query_Block::cqb_get_custom_query_posts($attributes);

          // set a fallback incase theuser doesn't set a template.
          if ( array_key_exists( 'cqb_template', $attributes) ) {
            $template_to_use = $attributes['cqb_template'];
          } else {
            $template_to_use = 'four-column.php';
          }

          $output = self::cqb_get_template(
            $template_to_use,
            array(
                'attributes' => $attributes,
                'post_query' => $post_query
            )
        );

        return $output;
    }

    public static function cqb_get_custom_query_posts($attributes)
    {
        if (!is_admin() && $attributes) {

            $args = array();

            if (array_key_exists('post_type', $attributes)) {
                $args['post_type'] = $attributes['post_type'];
            }

            if (array_key_exists('order_by', $attributes)) {
                $order_data = explode('/', $attributes['order_by']);
                $args['orderby'] = $order_data[0];
                $args['order'] = $order_data[1];
            }

            if (array_key_exists('display_number', $attributes)) {
                $args['posts_per_page'] = $attributes['display_number'];
            }

            $query = new WP_Query($args);
        } else {
            $query = null;
        }

        return $query;
    }

    public static function cqb_custom_block_assets()
    {
        wp_enqueue_style(
            'block-buddy-frontend',
            plugins_url('blockbuddy/dist/blocks.style.build.css', dirname(__FILE__))
        );
    }

    public static function cqb_wp_rest_endpoints()
    {
        register_rest_route(CUSTOM_QUERY_BLOCK_NAMESPACE, '/get-post-types', array(
            'methods' => 'GET',
            'callback' => 'Custom_Query_Block::cqb_get_post_types'
        ));

        register_rest_route(CUSTOM_QUERY_BLOCK_NAMESPACE, '/get-taxonomies', array(
            'methods' => 'GET',
            'callback' => 'Custom_Query_Block::cqb_get_taxonomies'
        ));

        register_rest_route(CUSTOM_QUERY_BLOCK_NAMESPACE, '/get-taxonomy-terms', array(
            'methods' => 'GET',
            'callback' => 'Custom_Query_Block::cqb_get_taxonomy_terms'
        ));

        register_rest_route(CUSTOM_QUERY_BLOCK_NAMESPACE, '/get-templates', array(
            'methods' => 'GET',
            'callback' => 'Custom_Query_Block::cqb_get_templates'
        ));
    }

    public static function cqb_get_post_types()
    {
        $post_types = get_post_types(
            array(
                'public' => true
            )
        );

        // add any to the list
        array_unshift($post_types, 'any');

        // remove attachments
        unset($post_types['attachment']);

        return Custom_Query_Block::cqb_prepare_options_json_response($post_types);
    }

    public static function cqb_get_taxonomies()
    {
        $taxonomies = get_taxonomies(
            array(
                'public' => true
            )
        );

        array_unshift($taxonomies, '');

        return Custom_Query_Block::cqb_prepare_options_json_response($taxonomies);
    }

    public static function cqb_get_taxonomy_terms($data)
    {
        $return = array();

        $taxonomy = $data['taxonomy'];

        if ($taxonomy != '') {
            $terms = get_terms(
                array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false
                )
            );

            if ($terms) {
                foreach ($terms as $term) {
                    $return[] = array(
                        'value' => $term->term_id,
                        'label' => $term->name
                    );
                }
            }
        }

        return rest_ensure_response($return);
    }

    public static function cqb_get_templates()
    {
        //get default available templates in the plugins template dir
        //scandirs and ignore .. and . returned in linux env dirs
        $default_path = plugin_dir_path(__FILE__) . 'templates/';
        $default_templates = array_diff(scandir($default_path), array('..', '.'));

        //get any custom templates that might exist in theme dir under themedir/template-parts/blocks/
        $theme_template_path = get_theme_file_path() . '/template-parts/blocks/';
        $theme_templates = array();
        if (file_exists($theme_template_path)) {
            $theme_templates = array_diff(scandir($theme_template_path), array('..', '.'));
        }

        //merge theme and default template for a unique list of available templates to use
        $available_templates = array_unique(array_merge($default_templates, $theme_templates));

        return Custom_Query_Block::cqb_prepare_options_json_response($available_templates);
    }

    public static function cqb_prepare_options_json_response($array)
    {
        foreach ($array as $item) {
            $return[] = array(
                'value' => $item,
                'label' => ucfirst($item)
            );
        }
        return rest_ensure_response($return);
    }
}
