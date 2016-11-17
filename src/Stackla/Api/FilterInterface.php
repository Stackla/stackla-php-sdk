<?php

namespace Stackla\Api;

interface FilterInterface
{
    /**
     * Media type options
     */
    const MEDIA_TEXT = 'text';
    const MEDIA_IMAGE = 'image';
    const MEDIA_VIDEO = 'video';
    const MEDIA_HTML = 'html';

    /**
     * Sort options
     */
    const SORT_LATEST = 'source_created_at_desc';
    const SORT_GREATEST = 'score_desc';
    const SORT_MOST_VOTES = 'votes_desc';

    /**
     * Visibility options
     */
    const ENABLE_HIDDEN = 0;
    const ENABLE_PANEL = 1;
    const ENABLE_BAR = 2;

    /**
     * Display claimed content's options
     */
    const FILTER_CLAIM_NO = 'no';
    const FILTER_CLAIM_CLAIMED = 'claimed_only';
    const FILTER_CLAIM_UNCLAIMMED = 'unclaimed_only';

    /** @noinspection PhpDocSignatureInspection */
    /**
     * Get contents of filter
     *
     * Overloading 1st option
     * @param bool $force Force to get latest content
     *
     * Overloading 2nd option
     * @param integer $limit Limit the return contents
     * @param bool $force Force to get latest content
     *
     * Overloading 2nd option
     * @param integer $limit Limit the return contents
     * @param integer $page Page number
     *
     * Overloading 2nd option
     * @param integer $limit Limit the return contents
     * @param array $options Additional parameter
     *
     * Overloading 3nd option
     * @param integer $limit Limit the return contents
     * @param integer $page Page number
     * @param bool $force Force to get latest content
     *
     * Overloading 3nd option
     * @param integer $limit Limit the return contents
     * @param integer $page Page number
     * @param array $options Additional parameter
     *
     * Overloading 4nd option
     * @param integer $limit Limit the return contents
     * @param integer $page Page number
     * @param array $options Additional parameter
     * @param bool $force Force to get latest content
     *
     * @return mixed
     */
    function getContents();

    /**
     * This method will delete content from Stackla
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    function delete();
}
