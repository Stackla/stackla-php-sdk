<?php

namespace Stackla\Api;

interface TagInterface
{
    /**
     * Tag type
     */
    const TYPE_CONTENT = 'content';
    const TYPE_PRODUCT = 'product';
    const TYPE_COMPETITION = 'competition';

    /**
     * Custom URL target
     */
    const TARGET_BLANK = '_blank';
    const TARGET_SELF = '_self';
    const TARGET_PARENT = '_parent';
    const TARGET_TOP = '_top';

    /**
     * Public visibility
     */
    const VISIBLE = true;
    const NOT_VISIBLE = false;

    /**
     * Get tag by Ext product id
     *
     * @param string $id External product id
     *
     * @return mixed    $this or false
     */
    function getByExtProductId($id);

    /**
     * This method will delete content from Stackla
     *
     * @param integer $id Content id
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    function delete();
}
