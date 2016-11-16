<?php

namespace Stackla\Api;

interface WidgetInterface
{
    /**
     * Widget type options
     */
    const TYPE_FLUID = 'fluid';
    const TYPE_STATIC = 'fixed';

    /**
     * Widget type style
     */
    const STYLE_VERTICAL_FLUID = 'fluid'; // fluid type only
    const STYLE_HORIZONTAL_FUILD = 'horizontal-fluid'; // fluid type only
    const STYLE_CAROUSEL = 'carousel'; // static type only
    const STYLE_SCROLL = 'main'; // static type only
    const STYLE_SLIDESHOW = 'slideshow'; // static type only
    const STYLE_AUTO = 'auto'; // static type only
    const STYLE_BASE_WATERFALL = 'base_waterfall'; // fluid type only
    const STYLE_BASE_CAROUSEL = 'base_carousel'; // fluid type only
    const STYLE_BASE_FEED = 'base_feed'; // fixed type only
    const STYLE_BASE_BILLBOARD = 'base_billboard'; // fixed type only
    const STYLE_BASE_SLIDESHOW = 'base_slideshow'; // fixed type only

    /**
     * This method will create a new widget that is cloned from current Widget
     *
     * @return boolean|mixed        This method will return a new widget object or False if failed to clone
     */
    function duplicate();

    /**
     * This method will create a widget with inherited style from parent widget.
     * the only change able fields are filter_id and name of the widget
     *
     * @param integer $filter_id Filter id for the inherited widget
     * @param string $name Widget name for inherited widget
     *
     * @return boolean|mixed        This method will return a widget object or false if failed to create inherited widget
     */
    function derive($filter_id, $name);

    /**
     * This method will delete content from Stackla
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    function delete();
}
