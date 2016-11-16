<?php

namespace Stackla\Api;

interface TileInterface
{
    /**
     * Visibility options
     */
    const STATUS_ENABLED = 'published';
    const STATUS_QUEUE = 'queued'; // deprecated
    const STATUS_QUEUED = 'queued';
    const STATUS_DISABLED = 'disabled';

    /**
     * This method will return content by provided valid ID
     *
     * @param integer $id Content Guid
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    function getByGuid($id);

    /**
     * This method will return content by provided valid ID
     *
     * @param integer $id Content sta_feed_id
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    function getByStacklaFeedId($id);

}
