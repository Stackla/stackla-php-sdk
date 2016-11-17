<?php

namespace Stackla\Api;

use Stackla\Core\StacklaDateTime;
use Stackla\Core\StacklaModel;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Filter
 *
 * Stakla\Api
 *
 * @property-read integer $id
 * @property string $name
 * @property integer $enable
 * @property integer $orders
 * @property string $sort
 * @property string[] $networks
 * @property Tag[] $tags
 * @property string[] $media
 * @property string $geofence
 * @property string $filterByClaimed
 *
 * @return
 */
class Filter extends StacklaModel implements FilterInterface
{
    /**
     * Endpoints
     *
     * @var string
     */
    protected $endpoint = 'filters';

    /**
     * @var Tile[]
     */
    public $contents;

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

    /**
     * Unique identifier for the Filter.
     *
     * @var integer
     */
    protected $_id;

    /**
     * Filter name.
     *
     * @var string
     *
     */
    protected $_name;

    /**
     * Filter status to show in stackla hub.
     *
     * @var boolean
     */
    protected $_enabled;

    /**
     * Filter order to show in stackla hub.
     *
     * @var integer
     */
    protected $_orders;

    /**
     * Sorting method for filter
     *
     * @var string
     *
     * @Assert\Choice(choices={"source_created_at_desc", "score_desc", "votes_desc"})
     *
     */
    protected $_sort;

    /**
     * Netowrks
     *
     * @var string[]
     *
     * @Assert\All({
     *      @Assert\Type(type="string")
     * })
     */
    protected $_networks;

    /**
     * Tags
     *
     * @var Tag[]
     *
     * @todo add array validation to validate the value type but skip the value validation because
     *       the value is only a place holder
     *
     * -@Assert\All({
     *      -@Assert\Type(type="\Stackla\Api\Tag")
     * })
     */
    protected $_tags;

    /**
     * Media
     *
     * @var string[]
     *
     * @Assert\All({
     *      @Assert\Type(type="string")
     * })
     */
    protected $_media;

    /**
     * Filter name.
     *
     * @var string
     *
     * @Assert\Choice(choices={"no", "claimed_only", "unclaimed_only"})
     */
    protected $_filterByClaimed;

    /**
     * Media
     *
     * @param string[] $media
     *
     * @return $this
     */
    public function setMedia($media)
    {
        if (gettype($media) === 'string') {
            $this->media = explode(',', $media);
        } elseif (is_array($media)) {
            $this->media = $media;
        }

        return $this;
    }

    /**
     * Add network to networks list
     *
     * @param string $network
     *
     * @return $this
     */
    public function addNetwork($network)
    {
        if (!$this->networks) {
            $this->networks = array($network);
        } else {
            $this->networks = array_merge($this->networks, array($network));
        }

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

    /**
     * Add single media to media list
     *
     * @param string $media
     *
     * @return $this
     */
    public function addMedia($media)
    {
        if (!$this->media) {
            $this->media = array($media);
        } else {
            $this->media = array_merge($this->media, array($media));
        }

        return $this;
    }

    /**
     * Filter's contents/tiles
     *
     * @param Tile[] $contents
     *
     * @return $this
     */
    protected function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    public function getContents()
    {
        $args = func_get_args();

        // default value
        $limit = 25;
        $page = 1;
        $force = false;
        $options = array();

        $error = false;
        switch (count($args)) {
            // 1st option overloading
            case 1:
                $error = gettype($args[0]) !== 'boolean' ? array('index' => 1, 'type' => 'boolean') : false;
                if (!$error) {
                    $force = $args[0];
                }
                break;
            // 2nd or 3rd option overloading
            case 2:
                if (gettype($args[0]) === 'integer' && gettype($args[1]) === 'integer') {
                    $limit = $args[0];
                    $page = $args[1];
                } elseif (gettype($args[0]) === 'integer' && gettype($args[1]) === 'array') {
                    $limit = $args[0];
                    $options = $args[1];
                } elseif (gettype($args[0]) === 'integer' && gettype($args[1]) === 'boolean') {
                    $limit = $args[0];
                    $force = $args[1];
                } else {
                    $error = array(
                        'index' => '1 and 2',
                        'type' => 'integer and integer for limit and page or integer and array for limit and options or integer and boolean for limit and force option'
                    );
                }
                break;
            // 3rd option overloading
            case 3:
                if (gettype($args[0]) === 'integer' && gettype($args[1]) === 'integer' && gettype($args[2]) === 'array') {
                    $limit = $args[0];
                    $page = $args[1];
                    $options = $args[2];
                } elseif (gettype($args[0]) === 'integer' && gettype($args[1]) === 'integer' && gettype($args[2]) === 'boolean') {
                    $limit = $args[0];
                    $page = $args[1];
                    $force = $args[2];
                } else {
                    $error = array(
                        'index' => '1, 2 and 3',
                        'type' => 'integer, integer and array for limit, page and options or integer, integer and boolean for limit, page and force option'
                    );
                }
                break;
            case 4:
                if (gettype($args[0]) === 'integer' && gettype($args[1]) === 'integer' && gettype($args[2]) === 'array' && gettype($args[3]) === 'boolean') {
                    $limit = $args[0];
                    $page = $args[1];
                    $options = $args[2];
                    $force = $args[3];
                } else {
                    $error = array(
                        'index' => '1, 2 , 3 and 4',
                        'type' => 'integer, integer, array and boolean for limit, page, options and force option'
                    );
                }

        }

        if ($error) {
            $message = sprintf('Missing argument or invalid argument %s to %s::%s() must be %s.', $error['index'], get_class($this), __METHOD__, $error['type']);
            throw new \Exception($message);
        }

        $contents = false;
        if ($this->contents && !$force) {
            $contents = $this->contents;
        } else {
            $endpoint = sprintf("%s/%s/content", $this->endpoint, $this->id);

            $this->initRequest();
            $requestOptions = array(
                'page' => $page,
                'limit' => $limit
            );
            $requestOptions = array_merge($requestOptions, $options);
            $json = $this->request->sendGet($endpoint, $requestOptions);

            if ($json !== false) {
                $tiles = new Tile($this->configs, $json);
                if (count($tiles)) {
                    $contents = $tiles->getResults();
                    $this->setContents($contents);
                }
            }
        }

        return $contents;
    }

    public function delete()
    {
        return parent::delete();
    }
}
