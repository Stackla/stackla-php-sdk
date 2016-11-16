<?php

namespace Stackla\Api;

interface TermInterface
{
    /**
     * Term type
     */
    const TYPE_PAGE = 'page';       // facebook
    const TYPE_HASHTAG = 'hashtag';    // twitter, instagram
    const TYPE_USER = 'user';       // twitter, gplus, youtube, flickr, instagram, pinterest
    const TYPE_SEARCH = 'search';     // twitter, gplus, youtube
    const TYPE_LOCATION = 'location';   // twitter, instagram
    const TYPE_GALLERY = 'gallery';    // flickr
    const TYPE_SET = 'set';        // flickr
    const TYPE_BOARD = 'page';       // pinterest
    const TYPE_BLOG = 'blog';       // tumblr
    const TYPE_RSS = 'page';       // rss
    const TYPE_ATOM = 'atom';       // rss
    const TYPE_DEFAULT = 'default';    // Ecal
    const TYPE_POST = 'post';       // sta_feed
    const TYPE_WEIBO_SHOW = 'weiboshow';
    const TYPE_WEIBO_PAGE = 'weibopage';
    const TYPE_WEIBO_TOPIC = 'weibotopic';

    /**
     * Moderation value
     */
    const MODERATION_PUBLISH = 'publish';
    const MODERATION_QUEUE = 'queue';
    const MODERATION_DISABLE = 'disable';
    const MODERATION_EXCLUDE = 'exclude';

    /**
     * This method will delete content from Stackla
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    function delete();
}
