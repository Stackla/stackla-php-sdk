<?php

namespace Stackla\Api;

use Stackla\Core\StacklaModel;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Widget
 * Stakla\Api
 *
 * @property-read integer $id
 * @property-read integer $stackla_id
 * @property-read string $guid
 * @property array $style
 * @property array $config
 * @property string $css
 * @property integer $filter_id
 * @property string $custom_css
 * @property string $custom_js
 * @property string $external_js
 * @property integer $parent_id
 * @property-read string $embed_code
 */
class Widget extends StacklaModel implements WidgetInterface
{
    /**
     * Widget type style group
     */
    public static $FLUID_TYPE_STYLES = array(
        "fluid",
        "horizontal-fluid",
        "base_waterfall",
        "base_carousel",
    );
    public static $STATIC_TYPE_STYLES = array(
        "carousel",
        "main",
        "slideshow",
        "auto",
        "base_feed",
        "base_billboard",
        "base_slideshow",
    );

    /**
     * Endpoints
     *
     * @var string
     */
    protected $endpoint = 'widgets';

    /**
     * Unique identifier for the widget.
     *
     * @var integer
     */
    protected $_id;

    /**
     * Stack id
     *
     * @var integer
     */
    protected $_stackId;

    /**
     * Widget guid
     *
     * @var string
     */
    protected $_guid;

    /**
     * Style
     *
     * @var array
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $_style;

    /**
     * Config
     *
     * @var array
     */
    protected $_config;

    /**
     * Css
     *
     * @var string
     * @Assert\Type(type="string")
     */
    protected $_css;

    /**
     * Filter id
     *
     * @var integer
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="integer")
     */
    protected $_filterId;

    /**
     * Status enabled
     *
     * @var integer
     */
    protected $_enabled;

    /**
     * Custom css
     *
     * @var string
     * @Assert\Type(type="string")
     */
    protected $_customCss;

    /**
     * Source css
     *
     * @var string
     * @Assert\Type(type="string")
     */
    protected $_sourceCss;

    /**
     * Custom js
     *
     * @var string
     * @Assert\Type(type="string")
     */
    protected $_customJs;

    /**
     * External js
     *
     * @var string
     * @Assert\Type(type="string")
     */
    protected $_externalJs;

    /**
     * Parent id
     *
     * @var integer
     * @Assert\Type(type="integer")
     */
    protected $_parentId;

    /**
     * Embed code
     *
     * @var string
     * @Assert\Type(type="string")
     */
    protected $_embedCode;

    /**
     * Widget name.
     *
     * @uses $style['name']
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     * @Assert\Length(min=3, max=255)
     */
    protected $_name;

    /**
     * Widget type.
     *
     * @uses $style['type']
     * @var string
     * @Assert\Choice(choices={"fluid", "fixed"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     */
    protected $_type;

    /**
     * Widget name.
     *
     * @uses $style['style']
     * @var string
     * @Assert\Choice(choices={"fluid", "horizontal-fluid", "carousel", "main", "slideshow", "auto", "base_waterfall",
     *                                  "base_carousel", "base_feed", "base_billboard", "base_slidehow"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     */
    protected $_typeStyle;

    /**
     * Widget name.
     *
     * @uses $style['max_tile_width']
     * @var integer
     */
    protected $_maxTileWidth;

    /**
     * Widget name.
     *
     * @uses $style['name']
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $style = $this->style;
        if (empty($style)) {
            $style = array();
        }
        $style['name'] = $name;
        $this->style = $style;

        return $this;
    }

    /**
     * Widget name.
     *
     * @return string
     */
    public function getName()
    {
        $style = $this->style;

        if (!isset($style['name'])) {
            return null;
        } else {
            return $style['name'];
        }
    }

    /**
     * Widget type.
     *
     * @uses $style['type']
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $style = $this->style;
        if (empty($style)) {
            $style = array();
        }
        $style['type'] = $type;
        $this->style = $style;

        return $this;
    }

    /**
     * Widget type.
     *
     * @return string
     */
    public function getType()
    {
        $style = $this->style;

        if (!isset($style['type'])) {
            return null;
        } else {
            return $style['type'];
        }
    }

    /**
     * Widget type style.
     *
     * @uses $style['style']
     *
     * @param string $typeStyle
     *
     * @return $this
     */
    public function setTypeStyle($type_style)
    {
        $style = $this->style;
        if (empty($style)) {
            $style = array();
        }
        $style['style'] = $type_style;
        $this->style = $style;

        if (in_array($type_style, self::$FLUID_TYPE_STYLES)) {
            $this->type = self::TYPE_FLUID;
        } elseif (in_array($type_style, self::$STATIC_TYPE_STYLES)) {
            $this->type = self::TYPE_STATIC;
        }

        return $this;
    }

    /**
     * Widget type style.
     *
     * @return string
     */
    public function getTypeStyle()
    {
        $style = $this->style;

        if (!isset($style['style'])) {
            return null;
        } else {
            return $style['style'];
        }
    }

    /**
     * Widget max tile width.
     *
     * @uses $style['max_tile_width']
     *
     * @param integer $width
     *
     * @return $this
     */
    public function setMaxTileWidth($width)
    {
        $style = $this->style;
        if (empty($style)) {
            $style = array();
        }
        $style['max_tile_width'] = $width;
        $this->style = $style;

        return $this;
    }

    /**
     * Widget max tile width.
     *
     * @return integer
     */
    public function getMaxTileWidth()
    {
        $style = $this->style;

        if (!isset($style['max_tile_width'])) {
            return null;
        } else {
            return $style['max_tile_width'];
        }
    }

    public function setDefaultValue()
    {
        $defaultConfig = array(
            'tile_options' => array(
                'show_tags'      => '0',
                'show_votes'     => '0',
                'show_likes'     => '0',
                'show_dislikes'  => '0',
                'show_comments'  => '0',
                'show_shopspots' => '0',
            ),
            'lightbox'     => array(
                'layout'               => 'portrait',
                'show_additional_info' => '1',
                'show_sharing'         => '0',
                'sharing_text'         => '',
                'sharing_title'        => '',
                'show_comments'        => '0',
                'post_comments'        => '0',
                'show_products'        => '0',
                'show_shopspots'       => '0',
            ),
        );
        $style = $this->style;
        $config = $this->config;
        if (empty($style)) {
            $style = array();
        }
        if (empty($config)) {
            $config = array('tile_options' => array(), 'lightbox' => array());
        }
        switch ($this->type_style) {
            case self::STYLE_BASE_WATERFALL:
            case self::STYLE_VERTICAL_FLUID:
                $defaultStyle = array(
                    'enable_custom_tiles_per_page'     => '',
                    'tiles_per_page'                   => 15,
                    'click_through_url'                => '%5BEXPAND%5D',
                    'polling_frequency'                => '30',
                    'parent_page_secret_key'           => '',
                    'enable_scrolling'                 => '0',
                    'text_tile_background'             => 'ffffff',
                    'text_tile_font_color'             => '666666',
                    'text_tile_font_size'              => 24,
                    'text_tile_user_name_font_color'   => '333333',
                    'text_tile_user_name_font_size'    => 18,
                    'text_tile_user_handle_font_color' => '333333',
                    'text_tile_user_handle_font_size'  => 18,
                    'margin'                           => 0,
                    'max_tile_width'                   => 365,
                    'widget_background'                => "",
                    'widget_loading_image'             => "/media/images/widget/loading.gif",
                );
                $style = $style + $defaultStyle;
                $config['tile_options'] = array_merge($defaultConfig['tile_options'], $config['tile_options']);
                $config['lightbox'] = array_merge($defaultConfig['lightbox'], $config['lightbox']);
                break;
            case self::STYLE_BASE_CAROUSEL:
            case self::STYLE_HORIZONTAL_FUILD:
                $defaultStyle = array(
                    'enable_custom_tiles_per_page' => '',
                    'tiles_per_page'               => 15,
                    'click_through_url'            => '%5BEXPAND%5D',
                    'polling_frequency'            => '30',
                    'parent_page_secret_key'       => '',
                    'widget_background'            => "",
                    'text_tile_background'         => '',
                    'text_tile_font_color'         => '666666',
                    'text_tile_source_color'       => '333333',
                    'margin'                       => 7,
                    'widget_height'                => 300,
                    'widget_loading_image'         => "/media/images/widget/loading.gif",
                );
                $style = $style + $defaultStyle;
                $config['tile_options'] = array_merge($defaultConfig['tile_options'], $config['tile_options']);
                $config['lightbox'] = array_merge($defaultConfig['lightbox'], $config['lightbox']);
                break;
            case self::STYLE_BASE_BILLBOARD:
            case self::STYLE_CAROUSEL:
                $defaultStyle = array(
                    'navigation'                       => 'click',
                    'click_through_url'                => '%5BORIGINAL_URL%5D',
                    'polling_frequency'                => '30',
                    'widget_background'                => "",
                    'arrow_color'                      => "333333",
                    'text_tile_user_name_font_color'   => '666666',
                    'text_tile_user_name_font_size'    => 16,
                    'text_tile_user_handle_font_color' => '666666',
                    'text_tile_user_handle_font_size'  => 13,
                    'text_tile_background'             => 'ffffff',
                    'text_tile_font_color'             => '666666',
                    'text_tile_font_size'              => 17,
                    'image_tile_background'            => 'ffffff',
                    'image_tile_font_color'            => 'ffffff',
                    'image_tile_font_size'             => 17,
                    'width'                            => 970,
                    'height'                           => 300,
                    'margin'                           => 7,
                    'rows'                             => 10,
                    'columns'                          => 2,
                    'tileWidth'                        => 300,
                    'tileHeight'                       => 300,
                    'tiles_per_page'                   => 9,
                    'widget_loading_image'             => "/media/images/widget/loading.gif",
                );
                $style = $style + $defaultStyle;
                break;
            case self::STYLE_BASE_FEED:
            case self::STYLE_SCROLL:
                $defaultStyle = array(
                    'navigation'                       => 'click',
                    'click_through_url'                => '%5BORIGINAL_URL%5D',
                    'polling_frequency'                => '30',
                    'widget_background'                => "ffffff",
                    'arrow_color'                      => "000000",
                    'text_tile_user_name_font_color'   => '666666',
                    'text_tile_user_name_font_size'    => 16,
                    'text_tile_user_handle_font_color' => '666666',
                    'text_tile_user_handle_font_size'  => 13,
                    'text_tile_background'             => 'ffffff',
                    'text_tile_font_color'             => '666666',
                    'text_tile_font_size'              => 18,
                    'image_tile_background'            => 'ffffff',
                    'image_tile_font_color'            => '666666',
                    'image_tile_font_size'             => 18,
                    'width'                            => 970,
                    'height'                           => 600,
                    'margin'                           => 15,
                    'rows'                             => 3,
                    'columns'                          => 3,
                    'tileWidth'                        => 253,
                    'tileHeight'                       => 253,
                    'tiles_per_page'                   => 9,
                    'widget_loading_image'             => "/media/images/widget/loading.gif",
                );
                $style = $style + $defaultStyle;
                break;
            case self::STYLE_BASE_SLIDESHOW:
            case self::STYLE_SLIDESHOW:
                $defaultStyle = array(
                    'click_through_url' => '%5BORIGINAL_URL%5D',
                    'polling_frequency' => '30',
                    'widget_background' => "ffffff",
                    'arrow_color' => "333333",
                    'text_tile_user_name_font_color' => '666666',
                    'text_tile_user_name_font_size' => 24,
                    'text_tile_user_handle_font_color' => '666666',
                    'text_tile_user_handle_font_size'  => 20,
                    'text_tile_background'             => 'ffffff',
                    'text_tile_font_color'             => '666666',
                    'text_tile_font_size'              => 24,
                    'image_tile_background'            => 'ffffff',
                    'image_tile_font_color'            => 'ffffff',
                    'image_tile_font_size'             => 24,
                    'width'                            => 970,
                    'height'                           => 600,
                    'margin'                           => 0,
                    'rows'                             => 1,
                    'columns'                          => 1,
                    'tileWidth'                        => 970,
                    'tileHeight'                       => 600,
                    'tiles_per_page'                   => 15,
                    'widget_loading_image'             => "/media/images/widget/loading.gif",
                );
                $style = $style + $defaultStyle;
                break;
        }
        $this->style = $style;
        $this->config = $config;
    }

    public function create()
    {
        $endpoint = sprintf("%s", $this->endpoint);

        $this->initRequest();

        $this->setDefaultValue();

        $data = $this->toArray();

        $json = $this->request->sendPost($endpoint, [], ['body' => json_encode($data)]);

        $this->fromJson($json);

        return $json === false ? false : $this;
    }

    public function duplicate()
    {
        $endpoint = sprintf("%s/%s?action=clone", $this->endpoint, $this->id);

        $this->initRequest();

        $data = $this->toArray(true);

        $json = $this->request->sendPost($endpoint, [], ['body' => json_encode($data)]);

        $class = get_class($this);
        $widget = new $class($this->configs, $json);

        return $json === false ? false : $widget;
    }

    public function derive($filter_id, $name)
    {
        $endpoint = sprintf("%s/%s?action=derive", $this->endpoint, $this->id);

        $this->initRequest();

        // $data = $this->toArray(true);
        $data = array(
            'filter_id' => $filter_id,
            'style'     => array(
                'name' => $name,
            ),
        );

        $json = $this->request->sendPost($endpoint, [], ['body' => json_encode($data)]);

        $class = get_class($this);
        $widget = new $class($this->configs, $json);

        return $json === false ? false : $widget;
    }

    public function update($force = false)
    {
        if ($this->isPlaceholder && !$force) {
            throw new \Exception(
                "This is placeholder object, it doesn't have a uptodate data. If you still want to update this object with provided property(ies), you can pass 'true' value to the first parameter of this method"
            );
        }

        $endpoint = sprintf("%s/%s", $this->endpoint, $this->id);

        $this->initRequest();

        $this->setDefaultValue();

        $data = $this->toArray(true);

        $json = $this->request->sendPut($endpoint, [], ['body' => json_encode($data)]);

        $this->fromJson($json);

        return $json === false ? false : $this;
    }

    /**
     * This method will delete the widget, but it will failed if the current widget is act as a parent Widget
     */
    public function delete()
    {
        return parent::delete();
    }
}
