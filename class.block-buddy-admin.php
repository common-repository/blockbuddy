<?php

class Block_Buddy_Admin
{
    private static $initiated = false;
    private static $admin_page = 'block-buddy-settings';

    public static function block_buddy_admin_init()
    {
        if (!self::$initiated) {
            self::block_buddy_admin_init_hooks();
        }
    }

    public static function block_buddy_admin_init_hooks()
    {
        self::$initiated = true;

        //enqueue editor assets
        add_action('enqueue_block_editor_assets', array('Block_Buddy_Admin', 'block_buddy_admin_custom_block_editor_assets'));
    }

    public static function block_buddy_admin_custom_block_editor_assets()
    {
        wp_enqueue_script(
            'custom-query-block-js',
            plugin_dir_url(__FILE__) . 'dist/blocks.build.js',
            array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor')
        );

        wp_enqueue_style(
            'custom-query-block-editor',
            plugin_dir_url(__FILE__) . 'dist/blocks.editor.build.css',
            array('wp-edit-blocks')
        );
    }

    public static function block_buddy_admin_plugin_activation()
    {
    }

    public static function block_buddy_admin_plugin_deactivation()
    {
    }
}
