<?php

namespace WPGO_Plugins\SVG_Flags;

/*
 *    Register blocks
 */
class Register_Blocks
{
    protected  $module_roots ;
    /* Main class constructor. */
    public function __construct( $module_roots )
    {
        $this->module_roots = $module_roots;
        add_filter(
            'block_categories',
            array( &$this, 'add_block_category' ),
            10,
            2
        );
        add_action( 'plugins_loaded', array( &$this, 'register_dynamic_block' ) );
    }
    
    /**
     * Add custom block category.
     */
    public function add_block_category( $categories, $post )
    {
        return array_merge( $categories, [ [
            'slug'  => 'svg-flags',
            'title' => __( 'SVG Flags', 'svg-flags' ),
        ] ] );
    }
    
    /**
     * Register the dynamic block.
     *
     * @since 2.1.0
     *
     * @return void
     */
    public function register_dynamic_block()
    {
        $attr_arr = [
            'gutenberg_block' => [
            'type'    => 'boolean',
            'default' => true,
        ],
            'flag'            => [
            'type'    => 'string',
            'default' => '{"value":"GB","label":"United Kingdom"}',
        ],
            'size'            => [
            'type'    => 'string',
            'default' => '5',
        ],
            'size_unit'       => [
            'type'    => 'string',
            'default' => 'em',
        ],
            'width'           => [
            'type'    => 'string',
            'default' => '5em',
        ],
            'height'          => [
            'type'    => 'string',
            'default' => '5em',
        ],
            'square'          => [
            'type'    => 'boolean',
            'default' => false,
        ],
            'caption'         => [
            'type'    => 'boolean',
            'default' => false,
        ],
            'inline'          => [
            'type'    => 'boolean',
            'default' => false,
        ],
            'inline_valign'   => [
            'type'    => 'string',
            'default' => 'middle',
        ],
            'random'          => [
            'type'    => 'boolean',
            'default' => false,
        ],
        ];
        // register the block
        register_block_type( 'svg-flags/svg-flag', [
            'render_callback' => array( SVG_Flag_Shortcode::get_instance(), 'render_svg_flag' ),
            'attributes'      => $attr_arr,
        ] );
    }

}
/* End class definition */