<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class WpmfGalleryDivi
 */
class WpmfGalleryAddonDivi extends ET_Builder_Module
{

    /**
     * Module slug
     *
     * @var string
     */
    public $slug = 'wpmf_gallery_addon_divi';

    /**
     * Whether module support visual builder. e.g `on` or `off`.
     *
     * @var string
     */
    public $vb_support = 'on';

    /**
     * Credits of all custom modules.
     *
     * @var array
     */
    protected $module_credits = array(
        'module_uri' => 'https://www.joomunited.com/',
        'author' => 'Joomunited',
        'author_uri' => 'https://www.joomunited.com/',
    );

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->name = esc_html__('WPMF Gallery Addon', 'wpmf');
    }

    /**
     * Advanced Fields Config
     *
     * @return array
     */
    public function get_advanced_fields_config() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from ET_Builder_Module class
    {
        return array(
            'button'       => false,
            'link_options' => false
        );
    }

    /**
     * Get the settings fields data for this element.
     *
     * @return array
     */
    public function get_fields() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from ET_Builder_Module class
    {
        $settings = get_option('wpmf_gallery_settings');
        $galleries = get_categories(
            array(
                'hide_empty' => false,
                'taxonomy' => WPMF_GALLERY_ADDON_TAXO,
                'pll_get_terms_not_translated' => 1
            )
        );


        $galleries = wpmfParentSort($galleries);

        $term_ids = array();
        foreach ($galleries as $gallery) {
            $term_ids[] = (int)$gallery->term_id;
        }
        $galleries_types = array();
        if (file_exists(WPMF_GALLERY_ADDON_PLUGIN_DIR . 'admin/class/helper.php')) {
            require_once WPMF_GALLERY_ADDON_PLUGIN_DIR . 'admin/class/helper.php';
            $WpmfGlrAddonHelper = new WpmfGlrAddonHelper();
            $galleries_types = $WpmfGlrAddonHelper->getGalleriesType($term_ids);
        }

        $galleries_list = array();
        $galleries_list[0] = esc_html__('WP Media Folder Gallery', 'wpmf');
        $i = 0;
        foreach ($galleries as $gallery) {
            $gallery_type = isset($galleries_types[(int)$gallery->term_id])? $galleries_types[(int)$gallery->term_id] : '' ;
            if (!empty($gallery_type) && ($gallery_type === 'photographer' || $gallery_type === 'archive')) {
                continue;
            }
            $depth = $gallery->depth + 1;
            $galleries_list[$i . '-' . $gallery->term_id] = str_repeat('&nbsp;&nbsp;', $depth) . $gallery->name;
            $i++;
        }

        //List photographer galleries
        $root = get_term_by('slug', 'photographer-gallery', WPMF_GALLERY_ADDON_TAXO);
        foreach ($galleries as $gallery) {
            $gallery_type = isset($galleries_types[(int)$gallery->term_id])? $galleries_types[(int)$gallery->term_id] : '' ;
            if (!empty($gallery_type) && $gallery_type === 'archive') {
                continue;
            }
            if (!empty($gallery_type) && $gallery_type === 'photographer') {
                if ((int)$root->term_id === (int)$gallery->term_id) {
                    $galleries_list[$i . '-' . $gallery->term_id] = esc_html__('Photographer Gallery', 'wpmf');
                } else {
                    $depth = $gallery->depth;
                    $galleries_list[$i . '-' . $gallery->term_id] = str_repeat('&nbsp;&nbsp;', $depth) . $gallery->name;
                }
            }
            $i++;
        }

        return array(
            'gallery_id' => array(
                'label' => esc_html__('Choose a Gallery', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => $galleries_list,
                'default' => 0,
                'default_on_front' => 0
            ),
            'theme' => array(
                'label' => esc_html__('Theme', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'default' => esc_html__('Default', 'wpmf'),
                    'masonry' => esc_html__('Masonry', 'wpmf'),
                    'portfolio' => esc_html__('Portfolio', 'wpmf'),
                    'slider' => esc_html__('Slider', 'wpmf'),
                    'flowslide' => esc_html__('Flow slide', 'wpmf'),
                    'square_grid' => esc_html__('Square grid', 'wpmf'),
                    'material' => esc_html__('Material', 'wpmf'),
                    'custom_grid' => esc_html__('Custom grid', 'wpmf')
                ),
                'default' => 'masonry',
                'default_on_front' => 'masonry'
            ),
            'layout' => array(
                'label' => esc_html__('Layout', 'wpmf'),
                'description' => esc_html__('Layout for masonry and square grid theme', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'vertical' => esc_html__('Vertical', 'wpmf'),
                    'horizontal' => esc_html__('Horizontal', 'wpmf'),
                ),
                'default' => 'vertical',
                'default_on_front' => 'vertical'
            ),
            'row_height' => array(
                'label' => esc_html__('Row height', 'wpmf'),
                'description' => esc_html__('Layout for masonry and square grid theme', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => 200,
                'default_on_front' => 200,
                'validate_unit'    => false,
                'unitless'         => true,
                'range_settings' => array(
                    'min' => 50,
                    'max' => 500,
                    'step' => 1
                )
            ),
            'aspect_ratio' => array(
                'label' => esc_html__('Aspect ratio', 'wpmf'),
                'description' => esc_html__('Aspect ratio for default, material, slider and square grid theme', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'default' => esc_html__('Default', 'wpmf'),
                    '1_1' => '1:1',
                    '3_2' => '3:2',
                    '2_3' => '2:3',
                    '4_3' => '4:3',
                    '3_4' => '3:4',
                    '16_9' => '16:9',
                    '9_16' => '9:16',
                    '21_9' => '21:9',
                    '9_21' => '9:21'
                ),
                'default' => '1_1',
                'default_on_front' => '1_1',
                'show_if_not'         => array(
                    'theme' => array(
                        'masonry',
                        'flowslide',
                        'custom_grid'
                    )
                )
            ),
            'gallery_navigation' => array(
                'label' => esc_html__('Gallery Navigation', 'wpmf'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('On', 'wpmf'),
                    'off' => esc_html__('Off', 'wpmf'),
                ),
                'default' => 'off',
                'default_on_front' => 'off'
            ),
            'sub_galleries_listing' => array(
                'label' => esc_html__('Sub-galleries listing', 'wpmf'),
                'description' => esc_html__('This gallery will only list its sub-galleries, using the galleries covers as image.', 'wpmf'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('On', 'wpmf'),
                    'off' => esc_html__('Off', 'wpmf'),
                ),
                'default' => 'off',
                'default_on_front' => 'off'
            ),
            'gallery_image_tags' => array(
                'label' => esc_html__('Display Images Tags', 'wpmf'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('On', 'wpmf'),
                    'off' => esc_html__('Off', 'wpmf'),
                ),
                'default' => 'off',
                'default_on_front' => 'off'
            ),
            'download_all' => array(
                'label' => esc_html__('Download All Images', 'wpmf'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('On', 'wpmf'),
                    'off' => esc_html__('Off', 'wpmf'),
                ),
                'default' => 'off',
                'default_on_front' => 'off'
            ),
            'columns' => array(
                'label' => esc_html__('Columns', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => $settings['theme']['masonry_theme']['columns'],
                'default_on_front' => $settings['theme']['masonry_theme']['columns'],
                'validate_unit'    => false,
                'unitless'         => true,
                'range_settings' => array(
                    'min' => 1,
                    'max' => 8,
                    'step' => 1
                ),
                'show_if_not'         => array(
                    'theme' => array(
                        'flowslide',
                        'custom_grid'
                    )
                )
            ),
            'number_lines' => array(
                'label' => esc_html__('Slider number lines', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => 1,
                'default_on_front' => 1,
                'validate_unit'    => false,
                'unitless'         => true,
                'range_settings' => array(
                    'min' => 1,
                    'max' => 3,
                    'step' => 1
                )
            ),
            'size' => array(
                'label' => esc_html__('Image Size', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => apply_filters('image_size_names_choose', array(
                    'thumbnail' => __('Thumbnail', 'wpmf'),
                    'medium' => __('Medium', 'wpmf'),
                    'large' => __('Large', 'wpmf'),
                    'full' => __('Full Size', 'wpmf'),
                )),
                'default' => $settings['theme']['masonry_theme']['size'],
                'default_on_front' => $settings['theme']['masonry_theme']['size']
            ),
            'targetsize' => array(
                'label' => esc_html__('Lightbox Size', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => apply_filters('image_size_names_choose', array(
                    'thumbnail' => __('Thumbnail', 'wpmf'),
                    'medium' => __('Medium', 'wpmf'),
                    'large' => __('Large', 'wpmf'),
                    'full' => __('Full Size', 'wpmf'),
                )),
                'default' => $settings['theme']['masonry_theme']['targetsize'],
                'default_on_front' => $settings['theme']['masonry_theme']['targetsize']
            ),
            'action' => array(
                'label' => esc_html__('Action On Click', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'file' => esc_html__('Lightbox', 'wpmf'),
                    'post' => esc_html__('Attachment Page', 'wpmf'),
                    'none' => esc_html__('None', 'wpmf'),
                ),
                'default' => $settings['theme']['masonry_theme']['link'],
                'default_on_front' => $settings['theme']['masonry_theme']['link']
            ),
            'orderby' => array(
                'label' => esc_html__('Order by', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'post__in' => esc_html__('Custom', 'wpmf'),
                    'rand' => esc_html__('Random', 'wpmf'),
                    'title' => esc_html__('Title', 'wpmf'),
                    'date' => esc_html__('Date', 'wpmf')
                ),
                'default' => $settings['theme']['masonry_theme']['orderby'],
                'default_on_front' => $settings['theme']['masonry_theme']['orderby']
            ),
            'order' => array(
                'label' => esc_html__('Order', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'ASC' => esc_html__('Ascending', 'wpmf'),
                    'DESC' => esc_html__('Descending', 'wpmf')
                ),
                'default' => $settings['theme']['masonry_theme']['order'],
                'default_on_front' => $settings['theme']['masonry_theme']['order']
            ),
            'gutterwidth' => array(
                'label' => esc_html__('Gutter', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => 5,
                'default_on_front' => 5,
                'validate_unit'    => false,
                'unitless'         => true,
                'range_settings' => array(
                    'min' => 0,
                    'max' => 100,
                    'step' => 5
                )
            ),
            'border_radius' => array(
                'label' => esc_html__('Border Radius', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => 0,
                'default_on_front' => 0,
                'validate_unit'    => false,
                'unitless'         => true,
                'range_settings' => array(
                    'min' => 0,
                    'max' => 20,
                    'step' => 1
                )
            ),
            'border_style' => array(
                'label' => esc_html__('Border Type', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'solid' => esc_html__('Solid', 'wpmf'),
                    'double' => esc_html__('Double', 'wpmf'),
                    'dotted' => esc_html__('Dotted', 'wpmf'),
                    'dashed' => esc_html__('Dashed', 'wpmf'),
                    'groove' => esc_html__('Groove', 'wpmf')
                ),
                'default' => 'solid',
                'default_on_front' => 'solid'
            ),
            'border_width' => array(
                'label' => esc_html__('Border Width', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => 0,
                'default_on_front' => 0,
                'validate_unit'    => false,
                'unitless'         => true,
                'range_settings' => array(
                    'min' => 0,
                    'max' => 30,
                    'step' => 1
                )
            ),
            'border_color' => array(
                'label' => esc_html__('Border Color', 'wpmf'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#cccccc',
                'default_on_front' => '#cccccc'
            ),
            'enable_shadow' => array(
                'label' => esc_html__('Enable Shadow', 'wpmf'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('On', 'wpmf'),
                    'off' => esc_html__('Off', 'wpmf'),
                ),
                'default' => 'off',
                'default_on_front' => 'off'
            ),
            'shadow_color' => array(
                'label' => esc_html__('Shadow Color', 'wpmf'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#cccccc',
                'default_on_front' => '#cccccc'
            ),
            'shadow_horizontal' => array(
                'label' => esc_html__('Horizontal', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'validate_unit' => true,
                'default' => '0px',
                'default_unit' => 'px',
                'default_on_front' => '0px',
                'range_settings' => array(
                    'min' => '-50',
                    'max' => '50',
                    'step' => '1'
                )
            ),
            'shadow_vertical' => array(
                'label' => esc_html__('Vertical', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'validate_unit' => true,
                'default' => '0px',
                'default_unit' => 'px',
                'default_on_front' => '0px',
                'range_settings' => array(
                    'min' => '-50',
                    'max' => '50',
                    'step' => '1'
                )
            ),
            'shadow_blur' => array(
                'label' => esc_html__('Blur', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'validate_unit' => true,
                'default' => '0px',
                'default_unit' => 'px',
                'default_on_front' => '0px',
                'range_settings' => array(
                    'min' => '0',
                    'max' => '50',
                    'step' => '1'
                )
            ),
            'shadow_spread' => array(
                'label' => esc_html__('Spread', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'validate_unit' => true,
                'default' => '0px',
                'default_unit' => 'px',
                'default_on_front' => '0px',
                'range_settings' => array(
                    'min' => '0',
                    'max' => '50',
                    'step' => '1'
                )
            ),
            'disable_overlay' => array(
                'label' => esc_html__('Disable Overlay', 'wpmf'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('On', 'wpmf'),
                    'off' => esc_html__('Off', 'wpmf'),
                ),
                'default' => 'off',
                'default_on_front' => 'off'
            ),
            'hover_color' => array(
                'label' => esc_html__('Hover Color', 'wpmf'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#000',
                'default_on_front' => '#000'
            ),
            'hover_opacity' => array(
                'label' => esc_html__('Hover Opacity', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => 0.4,
                'default_on_front' => 0.4,
                'validate_unit'    => false,
                'unitless'         => true,
                'range_settings' => array(
                    'min' => 0,
                    'max' => 1,
                    'step' => 0.1
                )
            ),
            'hover_title_position' => array(
                'label' => esc_html__('Title Position', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'none' => esc_html__('None', 'wpmf'),
                    'top_left' => esc_html__('Top left', 'wpmf'),
                    'top_right' => esc_html__('Top right', 'wpmf'),
                    'top_center' => esc_html__('Top center', 'wpmf'),
                    'bottom_left' => esc_html__('Bottom left', 'wpmf'),
                    'bottom_right' => esc_html__('Bottom right', 'wpmf'),
                    'bottom_center' => esc_html__('Bottom center', 'wpmf'),
                    'center_center' => esc_html__('Center center', 'wpmf'),
                ),
                'default' => 'center_center',
                'default_on_front' => 'center_center'
            ),
            'hover_title_size' => array(
                'label' => esc_html__('Title Size', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => 16,
                'default_on_front' => 16,
                'validate_unit'    => false,
                'unitless'         => true,
                'range_settings' => array(
                    'min' => 0,
                    'max' => 150,
                    'step' => 1
                )
            ),
            'hover_title_color' => array(
                'label' => esc_html__('Title Color', 'wpmf'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#fff',
                'default_on_front' => '#fff'
            ),
            'hover_desc_position' => array(
                'label' => esc_html__('Description Position', 'wpmf'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'none' => esc_html__('None', 'wpmf'),
                    'top_left' => esc_html__('Top left', 'wpmf'),
                    'top_right' => esc_html__('Top right', 'wpmf'),
                    'top_center' => esc_html__('Top center', 'wpmf'),
                    'bottom_left' => esc_html__('Bottom left', 'wpmf'),
                    'bottom_right' => esc_html__('Bottom right', 'wpmf'),
                    'bottom_center' => esc_html__('Bottom center', 'wpmf'),
                    'center_center' => esc_html__('Center center', 'wpmf'),
                ),
                'default' => 'center_center',
                'default_on_front' => 'center_center'
            ),
            'hover_desc_size' => array(
                'label' => esc_html__('Description Size', 'wpmf'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => 14,
                'default_on_front' => 14,
                'validate_unit'    => false,
                'unitless'         => true,
                'range_settings' => array(
                    'min' => 0,
                    'max' => 150,
                    'step' => 1
                )
            ),
            'hover_desc_color' => array(
                'label' => esc_html__('Description Color', 'wpmf'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#fff',
                'default_on_front' => '#fff'
            ),
        );
    }

    /**
     * Render content
     *
     * @param array  $attrs       List of attributes.
     * @param string $content     Content being processed.
     * @param string $render_slug Slug of module that is used for rendering output.
     *
     * @return string
     */
    public function render($attrs, $content, $render_slug) // phpcs:ignore PEAR.Functions.ValidDefaultValue.NotAtEnd -- Method extends from ET_Builder_Module class
    {
        $gallery_navigation = (!empty($this->props['gallery_navigation']) && $this->props['gallery_navigation'] === 'on') ? 1 : 0;
        $sub_galleries_listing = (!empty($this->props['sub_galleries_listing']) && $this->props['sub_galleries_listing'] === 'on') ? 1 : 0;
        $gallery_image_tags = (!empty($this->props['gallery_image_tags']) && $this->props['gallery_image_tags'] === 'on') ? 1 : 0;
        $disable_overlay = (!empty($this->props['disable_overlay']) && $this->props['disable_overlay'] === 'on') ? 1 : 0;
        $download_all = (!empty($this->props['download_all']) && $this->props['download_all'] === 'on') ? 1 : 0;
        if (!empty($this->props['enable_shadow']) && $this->props['enable_shadow'] === 'on') {
            $img_shadow = $this->props['shadow_horizontal'] . ' ' . $this->props['shadow_vertical'] . ' ' . $this->props['shadow_blur'] . ' ' . $this->props['shadow_spread'] . ' ' . $this->props['shadow_color'];
        } else {
            $img_shadow = '';
        }
        if (empty($this->props['gallery_id'])) {
            $html = '<div class="wpmf-divi-container">
            <div id="divi-gallery-addon-placeholder" class="divi-gallery-addon-placeholder">
                        <span class="wpmf-divi-message">
                            ' . esc_html__('Please select a gallery to activate the preview', 'wpmf') . '
                        </span>
            </div>
          </div>';
            return $html;
        }

        $gallery_id_string = $this->props['gallery_id'];
        if (strpos($gallery_id_string, '-') !== false) {
            $gallery_ids = explode('-', $gallery_id_string);
            $gallery_id = $gallery_ids[1];
        } else {
            $gallery_id = $gallery_id_string;
        }
        return do_shortcode('[wpmfgallery gallery_id="'. (int)$gallery_id .'" display_tree="' . esc_attr($gallery_navigation) . '" sub_galleries_listing="' . esc_attr($sub_galleries_listing) . '" display_tag="' . esc_attr($gallery_image_tags) . '" download_all="' . esc_attr($download_all) . '" disable_overlay="' . esc_attr($disable_overlay) . '" display="' . esc_attr($this->props['theme']) . '" layout="' . esc_attr($this->props['layout']) . '" row_height="' . esc_attr($this->props['row_height']) . '" aspect_ratio="' . esc_attr($this->props['aspect_ratio']) . '" columns="' . esc_attr($this->props['columns']) . '" size="' . esc_attr($this->props['size']) . '" targetsize="' . esc_attr($this->props['targetsize']) . '" link="' . esc_attr($this->props['action']) . '" wpmf_orderby="' . esc_attr($this->props['orderby']) . '" wpmf_order="' . esc_attr($this->props['order']) . '" gutterwidth="' . esc_attr($this->props['gutterwidth']) . '" border_width="' . esc_attr($this->props['border_width']) . '" border_style="' . esc_attr($this->props['border_style']) . '" border_color="' . esc_attr($this->props['border_color']) . '" img_shadow="' . esc_attr($img_shadow) . '" img_border_radius="' . esc_attr($this->props['border_radius']) . '" number_lines="' . esc_attr($this->props['number_lines']) . '" hover_color="'. $this->props['hover_color'] .'" hover_opacity="'. $this->props['hover_opacity'] .'" hover_title_position="'. $this->props['hover_title_position'] .'" hover_title_size="'. $this->props['hover_title_size'] .'" hover_title_color="'. $this->props['hover_title_color'] .'" hover_desc_position="'. $this->props['hover_desc_position'] .'" hover_desc_size="'. $this->props['hover_desc_size'] .'" hover_desc_color="'. $this->props['hover_desc_color'] .'"]');
    }
}

new WpmfGalleryAddonDivi;
