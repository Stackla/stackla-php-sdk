<?php

namespace Stackla\Api;

use Stackla\Core\StacklaModel;
use Stackla\Api\Tag;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class Widget
 *
 * @package Stakla\Api
 *
 * @property-read integer           $id
 * @property-read integer           $stackla_id
 * @property-read string            $guid
 * @property array                  $style
 * @property array                  $config
 * @property string                 $css
 * @property integer                $filter_id
 * @property string                 $custom_css
 * @property string                 $custom_js
 * @property string                 $external_js
 * @property integer                $parent_id
 * @property-read string            $embed_code
 */
class Widget extends StacklaModel implements WidgetInterface
{
    /**
     * Widget type style group
     */
    public static $FLUID_TYPE_STYLES = array(
        "fluid", "horizontal-fluid", "base_waterfall", "base_carousel"
    );
    public static $STATIC_TYPE_STYLES = array(
        "carousel", "main", "slideshow", "auto", "base_feed", "base_billboard", "base_slideshow"
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
     *
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
     *
     * @Assert\Type(type="string")
     */
    protected $_css;

    /**
     * Filter id
     *
     * @var integer
     *
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
     *
     * @Assert\Type(type="string")
     */
    protected $_customCss;

    /**
     * Source css
     *
     * @var string
     *
     * @Assert\Type(type="string")
     */
    protected $_sourceCss;

    /**
     * Custom js
     *
     * @var string
     *
     * @Assert\Type(type="string")
     */
    protected $_customJs;

    /**
     * External js
     *
     * @var string
     *
     * @Assert\Type(type="string")
     */
    protected $_externalJs;

    /**
     * Parent id
     *
     * @var integer
     *
     * @Assert\Type(type="integer")
     */
    protected $_parentId;

    /**
     * Embed code
     *
     * @var string
     *
     * @Assert\Type(type="string")
     */
    protected $_embedCode;

    /**
     * Widget name.
     *
     * @uses $style['name']
     *
     * @var string
     *
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
     *
     * @var string
     *
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
     *
     * @var string
     *
     * @Assert\Choice(choices={"fluid", "horizontal-fluid", "carousel", "main", "slideshow", "auto", "base_waterfall", "base_carousel", "base_feed", "base_billboard", "base_slidehow"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     */
    protected $_typeStyle;

    /**
     * Widget name.
     *
     * @uses $style['max_tile_width']
     *
     * @var integer
     */
    protected $_maxTileWidth;

    /**
     * Widget name.
     *
     * @uses $style['name']
     *
     * @param string    $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $style = $this->style;
        if (empty($style)) $style = array();
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

        if (!isset($style['name'])) return null;
        else return $style['name'];
    }

    /**
     * Widget type.
     *
     * @uses $style['type']
     *
     * @param string    $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $style = $this->style;
        if (empty($style)) $style = array();
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

        if (!isset($style['type'])) return null;
        else return $style['type'];
    }

    /**
     * Widget type style.
     *
     * @uses $style['style']
     *
     * @param string    $typeStyle
     *
     * @return $this
     */
    public function setTypeStyle($type_style)
    {
        $style = $this->style;
        if (empty($style)) $style = array();
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

        if (!isset($style['style'])) return null;
        else return $style['style'];
    }

    /**
     * Widget max tile width.
     *
     * @uses $style['max_tile_width']
     *
     * @param integer    $width
     *
     * @return $this
     */
    public function setMaxTileWidth($width)
    {
        $style = $this->style;
        if (empty($style)) $style = array();
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

        if (!isset($style['max_tile_width'])) return null;
        else return $style['max_tile_width'];
    }

    public function setDefaultValue()
    {
        $defaultConfig = array(
            'tile_options' => array(
                'show_tags' => '0',
                'show_votes' => '0',
                'show_likes' => '0',
                'show_dislikes' => '0',
                'show_comments' => '0',
                'show_shopspots' => '0'
            ),
            'lightbox' => array(
                'layout' => 'portrait',
                'show_additional_info' => '1',
                'show_sharing' => '0',
                'sharing_text' => '',
                'sharing_title' => '',
                'show_comments' => '0',
                'post_comments' => '0',
                'show_products' => '0',
                'show_shopspots' => '0'
            )
        );
        $style = $this->style;
        $config = $this->config;
        if (empty($style)) $style = array();
        if (empty($config)) $config = array('tile_options' => array(), 'lightbox' => array());
        switch ($this->type_style) {
            case self::STYLE_BASE_WATERFALL:
            case self::STYLE_VERTICAL_FLUID:
                $style['max_tile_width'] = 365;
                $config['tile_options'] = array_merge($defaultConfig['tile_options'], $config['tile_options']);
                $config['lightbox'] = array_merge($defaultConfig['lightbox'], $config['lightbox']);
                break;
            case self::STYLE_BASE_CAROUSEL:
            case self::STYLE_HORIZONTAL_FUILD:
                $style['tiles_per_page'] = 15;
                $style['widget_height'] = 300;
                $config['tile_options'] = array_merge($defaultConfig['tile_options'], $config['tile_options']);
                $config['lightbox'] = array_merge($defaultConfig['lightbox'], $config['lightbox']);
                break;
            case self::STYLE_BASE_BILLBOARD:
            case self::STYLE_CAROUSEL:
                $style['width'] = 970;
                $style['height'] = 300;
                $style['margin'] = 7;
                $style['rows'] = 10;
                $style['columns'] = 2;
                $style['tileWidth'] = 300;
                $style['tileHeight'] = 300;
                $style['tiles_per_page'] = 9;
                break;
            case self::STYLE_BASE_FEED:
            case self::STYLE_SCROLL:
                $style['width'] = 970;
                $style['height'] = 600;
                $style['margin'] = 15;
                $style['rows'] = 3;
                $style['columns'] = 3;
                $style['tileWidth'] = 251;
                $style['tileHeight'] = 251;
                $style['tiles_per_page'] = 9;
                break;
            case self::STYLE_BASE_SLIDESHOW:
            case self::STYLE_SLIDESHOW:
                $style['auto_scroll'] = 1;
                $style['width'] = 970;
                $style['height'] = 600;
                $style['margin'] = 0;
                $style['rows'] = 1;
                $style['columns'] = 1;
                $style['tileWidth'] = 970;
                $style['tileHeight'] = 600;
                $style['tiles_per_page'] = 15;
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

        $json = $this->request->sendPost($endpoint, array(), array(
            'content-type' => 'application/json',
            'body' => json_encode($data)
        ));

        $this->fromJson($json);

        return $json === false ? false : $this;
    }

    public function duplicate()
    {
        $endpoint = sprintf("%s/%s?action=clone", $this->endpoint, $this->id);

        $this->initRequest();

        $data = $this->toArray(true);

        $json = $this->request->sendPost($endpoint, array(), array(
            'content-type' => 'application/json',
            'body' => json_encode($data)
        ));

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
            'style' => array(
                'name' => $name
            )
        );

        $json = $this->request->sendPost($endpoint, array(), array(
            'content-type' => 'application/json',
            'body' => json_encode($data)
        ));

        $class = get_class($this);
        $widget = new $class($this->configs, $json);

        return $json === false ? false : $widget;
    }

    public function update($force = false)
    {
        if ($this->isPlaceholder && !$force) {
            throw new \Exception("This is placeholder object, it doesn't have a uptodate data. If you still want to update this object with provided property(ies), you can pass 'true' value to the first parameter of this method");
        }

        $endpoint = sprintf("%s/%s", $this->endpoint, $this->id);

        $this->initRequest();

        $this->setDefaultValue();

        $data = $this->toArray(true);

        $json = $this->request->sendPut($endpoint, array(), array(
            'content-type' => 'application/json',
            'body' => json_encode($data)
        ));

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
