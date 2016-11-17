<?php

namespace Stackla\Api;

use Stackla\Core\StacklaDateTime;
use Stackla\Core\StacklaModel;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tile's properties
 *
 * @package Stackla\Api
 *
 * @property integer $term_id
 * @property-read string $_id
 * @property-read string $id
 * @property-read string $sta_feed_id
 * @property string $guid
 * @property string $name
 * @property string $avatar
 * @property string $title
 * @property string $share_text
 * @property string $media
 * @property string $video_url
 * @property-read string $source_user_id
 * @property string $width_ratio
 * @property string $height_ratio
 * @property string $image
 * @property string $image_url
 * @property string $image_small_url
 * @property string $image_medium_url
 * @property string $image_large_url
 * @property integer $image_width
 * @property integer $image_height
 * @property integer $image_small_width
 * @property integer $image_small_height
 * @property integer $image_medium_width
 * @property integer $image_medium_height
 * @property integer $image_large_width
 * @property integer $image_large_height
 * @property string $message
 * @property-read string $original_url
 * @property string $html
 * @property Tag[] $tags
 * @property-read string $source
 * @property string $status
 * @property string $longitude
 * @property string $latitude
 * @property string[] $disabled_reason
 * @property-read bool $disabled
 * @property-read bool $claimed
 * @property-read bool $anonymous
 * @property-read integer $score
 * @property-read integer $numVotes
 * @property-read integer $numUps
 * @property-read integer $numDowns
 * @property-read integer $numComments
 * @property-read StacklaDateTime $created_at
 * @property-read StacklaDateTime $updated_at
 * @property-read StacklaDateTime $source_created_at
 */
class Tile extends StacklaModel implements TileInterface
{
    /**
     * Endpoints
     *
     * @var string
     */
    protected $endpoint = 'tiles';

    /**
     * setter Stackla Term's Id
     *
     * @var integer
     */
    protected $_termId;

    /**
     * Unique identifier for the Tile, in the Stack. This is an object containing a "$id" property, which will expose
     * the ID as a 24-byte string.
     *
     * @var string
     */
    protected $_id;

    /**
     * Globally unique ID to be used for this post (often referencing external ID).
     * Tiles that have this value set can also be retrived using the guid: operator (see _id).
     * Note
     * When a tile is created, its _id is not known immediately, and hence won't be returned
     * when the Tile is created. When a guid is specified, it can be used to retrieve the
     * specific tile at a later time without knowing the _id.
     *
     * @var string
     */
    protected $_staFeedId;

    /**
     * Globally unique ID to be used for this post (often referencing external ID).
     * Tiles that have this value set can also be retrived using the guid: operator (see _id).
     * Note
     * When a tile is created, its _id is not known immediately, and hence won't be returned
     * when the Tile is created. When a guid is specified, it can be used to retrieve the
     * specific tile at a later time without knowing the _id.
     *
     * @var string
     */
    protected $_guid;

    /**
     * Display name to be used for the author.
     *
     * @var string
     */
    protected $_name;

    /**
     * User handle / username of the network.
     *
     * @var string
     */
    protected $_user;

    /**
     * URL to be used as the post author's avatar.
     *
     * @var string
     * @Assert\Url()
     */
    protected $_avatar;

    /**
     * Tile title to accompany the message, often used for video tiles.
     *
     * @var string
     */
    protected $_title;

    /**
     * Accompanying text to be used when tile is shared on a Social network
     * (e.g. Twitter, Instagram, Facebook, etc.)
     *
     * @var string
     */
    protected $_shareText;

    /**
     * The media type of the post. Must be one of: text, image, video or html.
     *
     * @var string
     * @Assert\Choice(choices={"text", "image", "video", "html"})
     */
    protected $_media;

    /**
     * URL of the video file. Required when media type is "video").
     *
     * @var string
     * @Assert\Url()
     */
    protected $_videoUrl;

    /**
     * Tile width ratio to be used as a ratio vs height ratio as width to height.
     * Positive numeric value. Required when media type is "html".
     *
     * @var integer
     */
    protected $_widthRatio;

    /**
     * Tile width ratio to be used as a ratio vs height_ratio as width to height.
     * Positive numeric value. Required when media type is "html".
     *
     * @var integer
     */
    protected $_heightRatio;

    /**
     * Source user id
     *
     * @var string
     */
    protected $_sourceUserId;

    /**
     * Full-sized image URL. Should always be deliverd via HTTPS.
     *
     * @var string
     * @Assert\Url()
     */
    protected $_image;

    /**
     * Full-sized image URL. Should always be deliverd via HTTPS.
     *
     * @var string
     * @Assert\Url()
     */
    protected $_imageUrl;

    /**
     * Small image URL (ideally under to 300x300px, or 600x600px for retina). Should always be deliverd via HTTPS.
     *
     * @var string
     * @Assert\Url()
     */
    protected $_imageSmallUrl;

    /**
     * Medium image URL (ideally under to 600x600px, or 1200x1200px for retina). Should always be deliverd via HTTPS.
     *
     * @var string
     * @Assert\Url()
     */
    protected $_imageMediumUrl;

    /**
     * Large image URL (ideally under to 600x600px, or 1200x1200px for retina). Should always be deliverd via HTTPS.
     *
     * @var string
     * @Assert\Url()
     */
    protected $_imageLargeUrl;

    /**
     * Full-sized image width.
     *
     * @var integer
     */
    protected $_imageWidth;

    /**
     * Full-sized image height.
     *
     * @var integer
     */
    protected $_imageHeight;

    /**
     * Small-sized image width.
     *
     * @var integer
     */
    protected $_imageSmallWidth;

    /**
     * Small-sized image height.
     *
     * @var integer
     */
    protected $_imageSmallHeight;

    /**
     * Medium-sized image width.
     *
     * @var integer
     */
    protected $_imageMediumWidth;

    /**
     * Medium-sized image height.
     *
     * @var integer
     */
    protected $_imageMediumHeight;

    /**
     * Large-sized image width.
     *
     * @var integer
     */
    protected $_imageLargeWidth;

    /**
     * Large-sized image height.
     *
     * @var integer
     */
    protected $_imageLargeHeight;

    /**
     * Message body, normalised from the content source. Will be the Tweet text, Facebook status, Instagram caption,
     * etc. Maximum 32k characters.
     *
     * @var string
     * @Assert\Type(type="string")
     */
    protected $_message;

    /**
     * Original source url for the content.
     *
     * @var string
     * @Assert\Type(type="string")
     */
    protected $_originalUrl;

    /**
     * HTML body, up to 32k characters. This field is mandatory for "html" media type.
     *
     * @var string
     * @Assert\Type(type="string")
     */
    protected $_html;

    /**
     * Tile's tags
     *
     * @var Tag[]
     */
    protected $_tags;

    /**
     * The source of the post, often a social network. This field is also referred
     * to as "network" in the filter context.
     *
     * @var string
     * @Assert\Choice(choices={"twitter", "facebook", "instagram", "flickr", "pinterest", "ecal", "gplus", "rss",
     *                                    "stackla", "stackla_internal", "sta_feed", "tumblr", "youtube", "weibo"})
     */
    protected $_source;

    /**
     * Tile status
     *
     * @var string
     * @Assert\Choice(choices={"published", "queued", "disabled"})
     */
    protected $_status;

    /**
     * Tile's location - Latitude value
     *
     * @var string
     */
    protected $_latitude;

    /**
     * Tile's location - Longitude value
     *
     * @var string
     */
    protected $_longitude;

    /**
     * Disabled reason of tile
     *
     * @var string
     */
    protected $_disabledReason;

    /**
     * Disabled status of tile
     *
     * @var integer
     */
    protected $_disabled;

    /**
     * Status of claimed
     *
     * @var integer
     */
    protected $_claimed;

    /**
     * Tiles that originate from networks that allow anonymous posts (including
     * Stackla) will have this field set to true. Renderers should not show any
     * attributions the creator of this Tile.
     *
     * @var integer
     */
    protected $_anonymouse;

    /**
     * Tile's score
     *
     * @var integer
     */
    protected $_score;

    /**
     * Tile's votes number
     *
     * @var integer
     */
    protected $_numVotes;

    /**
     * Tile's like counter
     *
     * @var integer
     */
    protected $_numUps;

    /**
     * Tile's dislike counter
     *
     * @var integer
     */
    protected $_numDowns;

    /**
     * Tile's comment counter
     *
     * @var integer
     */
    protected $_numComments;

    /**
     * Tile's creation date
     *
     * @var StacklaDateTime
     */
    protected $_createdAt;

    /**
     * Tile's updated date
     *
     * @var StacklaDateTime
     */
    protected $_updatedAt;

    /**
     * Source created time
     *
     * @var StacklaDateTime
     */
    protected $_sourceCreatedAt;

    /**
     * Unique identifier for the Tile, in the Stack. This is an object containing a "$id" property, which will expose
     * the ID as a 24-byte string.
     *
     * @param string|mixed $_id
     *
     * @return $this
     */
    protected function setId($_id)
    {
        if (gettype($_id) === 'string') {
            $this->id = $_id;
        } elseif (gettype($_id) == 'array') {
            $this->id = $_id['$id'];
        }

        return $this;
    }

    /**
     * Unique identifier for the Tile, in the Stack. This is an object containing a "$id" property, which will expose
     * the ID as a 24-byte string.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Tags - add tag to tile
     *
     * @param \Stackla\Api\Tag
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
     * Delete single tag from term
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

    public function toArray($only_updated = false)
    {
        $properties = $this->_propMap;

        foreach ($properties as $k => $v) {
            if ($k === 'tags') {
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
                $properties[$k] = implode(',', $tags);
            }
        }

        return parent::_convertToArray($properties, $only_updated);
    }

    public function create()
    {
        $endpoint = sprintf("%s?", $this->endpoint);
        if (isset($this->term_id) && !is_null($this->term_id)) {
            $endpoint = sprintf("%s?term_id=%s", $this->endpoint, $this->term_id);
        }
        $data = $this->toArray(true);
        unset($data['term_id']);

        $this->initRequest();
        $json = $this->request->sendPost($endpoint, $data);

        $this->fromJson($json);

        return $json === false ? false : $this;
    }

    /**
     * Get tiles will require filter_id
     * {@inheritdoc} Will need to pass filter_id in the array of options
     *
     * @param integer $filter_id filter id
     * @param integer $limit default value is 25
     * @param integer $page default value is 1
     * @param array $options optional data
     * @param bool $force force to
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    public function get()
    {
        $arg_list = func_get_args();

        if (!isset($arg_list[0]) || empty($arg_list[0])) {
            $message = sprintf('Missing argument 1 to %s::%s() must be an integer.', get_class($this), __METHOD__);
            throw new \Exception($message);
        }

        $filter = new Filter($this->configs, $arg_list[0], false);

        $limit = isset($arg_list[1]) ? $arg_list[1] : 25;
        $page = isset($arg_list[2]) ? $arg_list[2] : 1;
        $options = isset($arg_list[3]) ? $arg_list[3] : array();
        $force = isset($arg_list[4]) ? $arg_list[4] : false;

        $tiles = $filter->getContents($limit, $page, $options, $force);
        $this->records = $tiles;

        return $this;
    }

    public function getByGuid($id)
    {
        return parent::getById('guid:' . $id);
    }

    public function getByStacklaFeedId($id)
    {
        return parent::getById('sta_feed_id:' . $id);
    }

}
