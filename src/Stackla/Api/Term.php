<?php

namespace Stackla\Api;

use Stackla\Core\StacklaDateTime;
use Stackla\Core\StacklaModel;

/**
 * Class Details
 *
 * Term allow you to ingest social media contents.
 *
 * @package Stackla\Api
 *
 * @property-read integer $id
 * @property string $name
 * @property string $display_name
 * @property bool $active
 * @property string $type
 * @property string $network
 * @property string $term
 * @property string[] $filter
 * @property string[] $exclude_filter
 * @property string[] $fan_filter
 * @property string[] $fan_exclude_filter
 * @property integer $minimum_fallowers
 * @property string $moderate_text
 * @property string $moderate_image
 * @property string $moderate_video
 * @property bool $retweet_enable
 * @property bool $reply_enable
 * @property bool $reply_to_enable
 * @property bool $partial_match
 * @property bool $official
 * @property bool $own
 * @property bool $include_fan_content
 * @property bool $include_hashtag_in_comments
 * @property bool $include_official_content
 * @property bool $disable_badword
 * @property string $search_exact_phrase
 * @property integer $num_of_backfill
 * @property string $geoFence
 * @property string[] $whitelist_handles
 * @property string[] $blacklist_handles
 * @property string $avatar
 * @property integer $ecal_id
 * @property integer $access_key
 * @property string $source_user_id
 * @property string $attribute
 * @property string $selector
 * @property bool $subscribed_to_updates
 * @property string $page_type
 * @property Tag[] $tags
 * @property-read StacklaDateTime $created
 * @property-read StacklaDateTime $modified
 * @property-read StacklaDateTime $last_ingestion_post
 */
class Term extends StacklaModel implements TermInterface
{
    /**
     * Endpoints
     * @var string
     */
    protected $endpoint = 'terms';

    /**
     * Unique identifier for the Term.
     *
     * @var integer
     */
    protected $_id;

    /**
     * Name of the Term.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Type(type="string")
     */
    protected $_name;

    /**
     * Display name for the term.
     *
     * @var string
     */
    protected $_displayName;

    /**
     * This value specifies if the term is active or not.
     * Must be 1 for true or 0 for false.
     *
     * @var bool
     */
    protected $_active;

    /**
     * Type of the term.
     *
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $_type;

    /**
     * Network for the term
     *
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $_network;

    /**
     * The term
     *
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $_term;

    /**
     * Filter for the term.
     *
     * @var string[]
     *
     */
    protected $_filter;

    /**
     * Exclude filter for the term.
     *
     * @var string[]
     *
     */
    protected $_excludeFilter;

    /**
     * Fan filter for the term.
     *
     * @var string[]
     *
     */
    protected $_fanFilter;

    /**
     * Fan exclude filter for the term.
     *
     * @var string[]
     *
     */
    protected $_fanExcludeFilter;

    /**
     * Minimum followers of the term.
     *
     * @var integer
     *
     */
    protected $_minimumFollowers;

    /**
     * Option for text content moderation of the term.
     *
     * @var string
     *
     */
    protected $_moderateText;

    /**
     * Option for image content moderation of the term.
     *
     * @var string
     *
     */
    protected $_moderateImage;

    /**
     * Option for video content moderation of the term.
     *
     * @var string
     *
     */
    protected $_moderateVideo;

    /**
     * Include retweets that meet term criteria
     *
     * @var integer
     *
     */
    protected $_retweetEnable;

    /**
     * Include reply Tweets that meet Term criteria
     *
     * @var integer
     *
     */
    protected $_replyEnable;

    /**
     * Reply to enable tweets that meet Term criteria
     *
     * @var integer
     *
     */
    protected $_replyToEnable;

    /**
     * Include partial match that meet term criteria
     *
     * @var integer
     *
     */
    protected $_partialMatch;

    /**
     * Include official content that meet term criteria
     *
     * @var integer
     *
     */
    protected $_official;

    /**
     * Include own content that meet term criteria
     *
     * @var integer
     *
     */
    protected $_own;

    /**
     * Include fan content that meet term criteria
     *
     * @var integer
     *
     */
    protected $_includeFanContent;

    /**
     * Include hashtag in comments that meet term criteria
     *
     * @var integer
     *
     */
    protected $_includeHashtagInComments;

    /**
     * Include official content that meet term criteria
     *
     * @var integer
     *
     */
    protected $_includeOfficialContent;

    /**
     * Disable badword filtering in content that meet term criteria
     *
     * @var integer
     *
     */
    protected $_disableBadword;

    /**
     * Search exact phrase in content that meet term criteria
     *
     * @var string
     *
     */
    protected $_searchExactPhrase;

    /**
     * Whitelist handles
     *
     * @var string[]
     *
     */
    protected $_whitelistHandles;

    /**
     * Blacklist Handles
     *
     * @var string[]
     *
     */
    protected $_blacklistHandles;

    /**
     * Avatar
     *
     * @var string
     *
     */
    protected $_avatar;

    /**
     * Ecal id
     *
     * @var integer
     *
     */
    protected $_ecalId;

    /**
     * Access key
     *
     * @var integer
     *
     */
    protected $_accessKey;

    /**
     * Source user id
     *
     * @var string
     *
     */
    protected $_sourceUserId;

    /**
     * Attribute
     *
     * @var string
     *
     */
    protected $_attribute;

    /**
     * Selector
     *
     * @var string
     *
     */
    protected $_selector;

    /**
     * Subscribed to updates
     *
     * @var integer
     *
     */
    protected $_subscribedToUpdates;

    /**
     * Page type
     *
     * @var string
     *
     */
    protected $_pageType;

    /**
     * Indicate number of backfill for ingestion
     *
     * @var integer
     *
     */
    protected $_numOfBackfill;

    /**
     * Tags for the term.
     *
     * @var Tag[]
     *
     */
    protected $_tags;

    /**
     * Creation date.
     *
     * @var StacklaDateTime
     *
     */
    protected $_created;

    /**
     * Modified date.
     *
     * @var StacklaDateTime
     *
     */
    protected $_modified;

    /**
     * Last ingestion post date
     *
     * @var StacklaDateTime
     *
     */
    protected $_lastIngestionPost;

    public function setFilter($filter)
    {
        if (gettype($filter) === 'string') {
            $this->filter = explode(',', $filter);
        } else {
            $this->filter = $filter;
        }

        return $this;
    }

    /**
     * Filter for the term.
     *
     * @return string[]
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Filter for the term.
     *
     * @param string $filter
     *
     * @return $this
     */
    public function addFilter($filter)
    {
        if (!$this->getFilter()) {
            $this->setFilter(array($filter));
        } else {
            $this->setFilter(array_merge($this->getFilter(), array($filter)));
        }

        return $this;
    }

    /**
     * Exclude filter for the term.
     *
     * @param string $filter
     *
     * @return $this
     */
    public function setExcludeFilter($filter)
    {
        if (gettype($filter) === 'string') {
            $this->exclude_filter = explode(',', $filter);
        } else {
            $this->exclude_filter = $filter;
        }

        return $this;
    }

    /**
     * Exclude filter for the term.
     *
     * @return string[]
     */
    public function getExcludeFilter()
    {
        return $this->exclude_filter;
    }

    /**
     * Exclude filter for the term.
     *
     * @param string $filter
     *
     * @return $this
     */
    public function addExcludeFilter($filter)
    {
        if (!$this->getExcludeFilter()) {
            $this->setExcludeFilter(array($filter));
        } else {
            $this->setExcludeFilter(array_merge($this->getExcludeFilter(), array($filter)));
        }

        return $this;
    }

    /**
     * Fan filter for the term.
     *
     * @param string $filter
     *
     * @return $this
     */
    public function setFanFilter($filter)
    {
        if (gettype($filter) === 'string') {
            $this->fan_filter = explode(',', $filter);
        } else {
            $this->fan_filter = $filter;
        }

        return $this;
    }

    /**
     * Fan filter for the term.
     *
     * @return mixed
     */
    public function getFanFilter()
    {
        return $this->fan_filter;
    }

    /**
     * Fan filter for the term.
     *
     * @param string $filter
     *
     * @return $this
     */
    public function addFanFilter($filter)
    {
        if (!$this->getFanFilter()) {
            $this->setFanFilter(array($filter));
        } else {
            $this->setFanFilter(array_merge($this->getFanFilter(), array($filter)));
        }

        return $this;
    }

    /**
     * Fan exclude filter for the term.
     *
     * @param string $filter
     *
     * @return $this
     */
    public function setFanExcludeFilter($filter)
    {
        if (gettype($filter) === 'string') {
            $this->fan_exclude_filter = explode(',', $filter);
        } else {
            $this->fan_exclude_filter = $filter;
        }

        return $this;
    }

    /**
     * Fan exclude filter for the term.
     *
     * @return string
     */
    public function getFanExcludeFilter()
    {
        return $this->fan_exclude_filter;
    }

    /**
     * Fan exclude filter for the term.
     *
     * @param string $filter
     *
     * @return $this
     */
    public function addFanExcludeFilter($filter)
    {
        if (!$this->getFanExcludeFilter()) {
            $this->setFanExcludeFilter(array($filter));
        } else {
            $this->setFanExcludeFilter(array_merge($this->getFanExcludeFilter(), array($filter)));
        }

        return $this;
    }

    /**
     * Associate single tag to term
     *
     * @param Tag $tag
     *
     * @return $this
     * @throws \Exception
     */
    public function associateTag(Tag $tag)
    {
        if (empty($tag->id)) {
            return $this;
        }

        $endpoint = sprintf("%s/%s/tags/%s", $this->endpoint, $this->id, $tag->id);

        $this->initRequest();
        $json = $this->request->sendPost($endpoint);

        $response = json_decode($json, true);
        if (count($response['errors'])) {
            throw new \Exception("Error happening while trying to Associate the tag to term.\n" . json_encode($response['errors']));
        }

        if (!$this->tags) {
            $this->tags = array($tag);
        } else {
            $this->tags = array_merge($this->tags, array($tag));
        }

        return $this;
    }

    /**
     * Disassosiate single tag from term
     *
     * @param Tag $tag
     *
     * @return $this
     * @throws \Exception
     */
    public function disassosiateTag(Tag $tag)
    {
        if (empty($tag->id)) {
            return $this;
        }

        // get all assosiated tags
        $tags = $this->tags;

        $newTags = array();
        $foundTag = false;
        if (count($tags)) {
            foreach ($tags as $_tag) {
                if ($_tag->id == $tag->id) {
                    $foundTag = true;
                } else {
                    $newTags[] = $_tag;
                }
            }
        }

        if (!$foundTag) {
            return $this;
        }

        $endpoint = sprintf("%s/%s/tags/%s", $this->endpoint, $this->id, $tag->id);

        $this->initRequest();
        $json = $this->request->sendDelete($endpoint);

        $response = json_decode($json, true);
        if (count($response['errors'])) {
            throw new \Exception("Error happening while trying to Associate the tag to term.\n" . json_encode($response['errors']));
        }

        $this->tags = $newTags;

        return $this;
    }

    /**
     * Add tag to tags list
     *
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag)
    {
        if (!$this->tags) {
            $this->tags = array($tag);
        } else {
            // avoid duplication
            $tagExist = false;
            foreach ($this->tags as $_tag) {
                if ($_tag->id == $tag->id) {
                    $tagExist = true;
                    break;
                }
            }
            if (!$tagExist) {
                $this->tags = array_merge($this->tags, array($tag));
            }
        }

        return $this;
    }

    /**
     * Disassosiate single tag from term
     *
     * @param Tag $tag
     *
     * @return $this
     */
    public function deleteTag(Tag $tag)
    {
        if ($this->tags) {
            $tagExist = false;
            foreach ($this->tags as $index => $_tag) {
                if ($_tag->id == $tag->id) {
                    $tagExist = $index;
                    break;
                }
            }
            if ($tagExist !== false) {
                $tags = $this->tags;
                array_splice($tags, $tagExist, 1);
                $this->tags = $tags;
            }
        }

        return $this;
    }

    /**
     * Validate user term if term type is TYPE_USER, TYPE_SET or TYPE_GALLERY
     *
     * @return mixed    return array of user information of False if invalid user term
     * @throws \Exception
     */
    protected function validateUser()
    {
        switch ($this->type) {
            case Term::TYPE_USER:       // twitter, gplus, youtube, flickr, instagram, pinterest
            case Term::TYPE_SET:        // flickr
            case Term::TYPE_GALLERY:    // flickr
            case Term::TYPE_PAGE:       // facebook, pinterest, rss
            case Term::TYPE_ATOM:       // rss
                $endpoint = sprintf("%s/user", $this->endpoint);

                $this->initRequest();

                $options = array(
                    'network' => $this->network,
                    'name' => $this->term,
                );

                switch ($this->network) {
                    case Stackla::NETWORK_FACEBOOK:
                        if (!$this->include_fan_content) {
                            $this->include_fan_content = 0;
                        }
                        break;
                    case Stackla::NETWORK_RSS:
                        return uniqid();
                        break;
                    case Stackla::NETWORK_TWITTER:
                    case Stackla::NETWORK_YOUTUBE:
                    case Stackla::NETWORK_INSTAGRAM:
                    case Stackla::NETWORK_GPLUS:
                    case Stackla::NETWORK_PINTEREST:
                        break;
                    case Stackla::NETWORK_FLICKR:
                        $options['type'] = $this->type;
                        break;

                }

                try {
                    $json = $this->request->sendGet($endpoint, $options);
                    $response = json_decode($json, true);

                    if ($json === false || count($response['errors'])) {
                        return false;
                    }

                    $this->source_user_id = $response['data']['id'];

                    return true;
                } catch (\Exception $e) {
                    $message = sprintf("User for this term is not valid.");
                    $this->errors = array($message);

                    throw new \Exception($message);
                }
                break;
            default:
                return true;
                break;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function toArray($only_updated = false)
    {
        $properties = $this->_propMap;

        foreach ($properties as $k => $v) {
            if ($v instanceof StacklaDateTime) {
                $properties[$k] = $v->getTimestamp();
            } elseif ($k === 'tags') {
                $tags = array();
                if (is_array($v)) {
                    foreach ($v as $tag) {
                        if (is_object($tag) && get_class($tag) == get_class(new Tag())) {
                            $tags[] = $tag->id;
                        } else {
                            $tags[] = $tag;
                        }
                    }
                }
                $properties[$k] = $tags;
            }
        }

        return parent::_convertToArray($properties, $only_updated);
    }

    public function create()
    {
        $this->validateUser();

        // Add default value
        $this->disable_badword || $this->disable_badword = 0;
        // enable the term by default
        $this->active || $this->active = 1;
        // enable the term by default
        $this->num_of_backfill || $this->num_of_backfill = 0;
        // publish content by default
        $this->moderate_text || $this->moderate_text = Term::MODERATION_PUBLISH;
        $this->moderate_image || $this->moderate_image = Term::MODERATION_PUBLISH;
        $this->moderate_video || $this->moderate_video = Term::MODERATION_PUBLISH;

        return parent::create();
    }

    public function update($force = false)
    {
        $changes = $this->toArray(true);

        if (isset($changes['term'])) {
            $this->validateUser();
        }
        return parent::update($force);
    }

    public function delete()
    {
        return parent::delete();
    }
}
