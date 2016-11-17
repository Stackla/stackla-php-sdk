<?php

namespace Stackla\Api;

use Stackla\Core\StacklaDateTime;
use Stackla\Core\StacklaModel;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Details
 *
 * Tags allow content to be categorised and filtered for better curation.
 * They are more like blog tags or product swing-tags and not to be confused
 * with hashtagsg
 *
 * @package Stackla\Api
 *
 * @property-read integer $id
 * @property-read integer $stack_id
 * @property string $tag
 * @property string $slug
 * @property bool $custom_slug
 * @property string $type
 * @property bool $publicly_visible
 * @property bool $vode_enabled
 * @property string $target
 * @property bool $system_tag
 * @property integer $priority
 * @property string $custom_url
 * @property string $price
 * @property string $ext_product_id
 * @property string $description
 * @property string $image_small_url
 * @property integer $image_small_width
 * @property integer $image_small_height
 * @property string $image_medium_url
 * @property integer $image_medium_width
 * @property integer $image_medium_height
 * @property-read StacklaDateTime $created_at
 */
class Tag extends StacklaModel implements TagInterface
{
    /**
     * Endpoints
     * @var string
     */
    protected $endpoint = 'tags';

    /**
     * Unique identifier for the Tag.
     *
     * @var int
     */
    protected $_id;

    /**
     * Name and display title on the Tag
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}")
     */
    protected $_tag;

    /**
     * Stack id for current tag
     *
     * @var int
     */
    protected $_stackId;

    /**
     * Simplified and class-name-fiendly Tag identifier, most often auto-generated.
     *
     * @var string
     *
     * @Assert\Type(type="string")
     */
    protected $_slug;

    /**
     * This value specifies if the slug field value is being auto-generated or overwritten
     * by the user. Must be 1 for true or 0 for false.
     *
     * @var bool
     */
    protected $_customSlug;

    /**
     * Specifies the type of the Tag, and must be one of: content | product | competition
     * Enumerated
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"content", "product", "competition", "system"})
     */
    protected $_type;

    /**
     * This value specifies if display renderers should display this Tag in display context.
     * Must be 1 for true or 0 for false.
     *
     * @var bool
     */
    protected $_publiclyVisible;

    /**
     * Must be 1 for enabled or 0 for disabled
     *
     * @var bool
     */
    protected $_voteEnabled;

    /**
     * When rendered as a link, this will indicate the target attribute for the anchor tag
     * when used with custom_url. Should be one of: _blank | _self | _parent | _top
     * Enumerated
     *
     * @var string
     *
     * @Assert\Choice(choices={"", "_blank", "_self", "_parent", "_top"})
     */
    protected $_target;

    /**
     * Indicates whether this Tag is a read-only tag created and managed by the system. Must be 1 for true or 0 for false.
     *
     * @var bool
     */
    protected $_systemTag;

    /**
     * Specifies the sequential sort order in which this Tag should be
     * displayed when being rendered for display. Values range from 1 (highest)
     * to 5 (lowest) with 3 being the default.
     *
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    protected $_priority;

    /**
     * URL that clicking on the Tag should take the user to. When type is product,
     * this is the URL that the product click-through should be linked to.
     *
     * @var string
     *
     * @Assert\Url()
     */
    protected $_customUrl;

    /**
     * User provided price for Tags of type product.
     *
     * @var string
     */
    protected $_price;

    /**
     * User provided reference to external product for Tags of type product.
     * Should be a continous string, best if URL-friendly. When querying for a product by ID,
     * this value can be prefixed with ext: to fetch by it.
     *
     * @var string
     */
    protected $_extProductId;

    /**
     * User provided description for Tags of type product. Maximum length: 512 characters.
     *
     * @var string
     */
    protected $_description;

    /**
     * URL of the small (optimised for 300px x 300px) image PNG/JPG/JPEG/GIF image to be displayed.
     * This should be a HTTPS URL to so that the Stack or widget can be served over HTTPS completely.
     *
     * @var string
     *
     * @Assert\Url()
     */
    protected $_imageSmallUrl;

    /**
     * Width of the small image being used as the image_small_url, in pixels.
     *
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    protected $_imageSmallWidth;

    /**
     * Height of the small image being used as the image_small_url, in pixels.
     *
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    protected $_imageSmallHeight;

    /**
     * URL of the medium (optimised for 600px x 600px) image PNG/JPG/JPEG/GIF image to be displayed.
     * This should be a HTTPS URL to so that the Stack or widget can be served over HTTPS completely.
     *
     * @var string
     *
     * @Assert\Url()
     */
    protected $_imageMediumUrl;

    /**
     * Width of the small image being used as the image_medium_url, in pixels.
     *
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    protected $_imageMediumWidth;

    /**
     * Height of the small image being used as the image_medium_url, in pixels.
     *
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    protected $_imageMediumHeight;

    /**
     * UTC timestamp of when this Tag was created.
     *
     * @var StacklaDateTime
     */
    protected $_createdAt;

    public function toArray($only_updated = false)
    {
        $properties = $this->_propMap;

        foreach ($properties as $k => $v) {
            if ($v instanceof StacklaDateTime) {
                $properties[$k] = $v->getTimestamp();
            }
        }

        return parent::_convertToArray($properties, $only_updated);
    }

    public function getByExtProductId($id)
    {
        return parent::getById('ext:' . $id);
    }

    public function delete()
    {
        return parent::delete();
    }
}
