# Stackla PHP SDK #
[![Build Status](https://travis-ci.org/Stackla/stackla-php-sdk.svg?branch=master)](https://travis-ci.org/Stackla/stackla-php-sdk)

Welcome to the Stackla PHP SDK GitHub repository!

This SDK is intended for PHP developers to enable them to integrate Stackla RESTful APIs into their applications with greater ease. You can find more information on working with Stackla APIs on the [Stackla Developer Portal](http://developer.stackla.com/).

We are continuously working on improving both the SDK and the documentation around it, so please feel free to give us feedback and any reports of issues to [platform@stackla.com](mailto:platform@stackla.com).

## System requirements ##
* PHP >= 5.4
* [Composer](https://getcomposer.org/)

## PHPUnit ##

Required `env` variable:
* ACCESS_TOKEN
* API_STACK => Stackla stack short_name
* API_HOST => `https://api.stackla.com/api/`
* DEFAULT_FILTER_ID
* DEFAULT TAG_ID
* STACKLA_POST_TERM_ID

```
$ php ./vendor/bin/phpunit ---configuration phpunit.xml.dist
```

## Great. How do I get this thing going? ##

#### Dependencies ####
Include this package in your composer.json:
```
#!json
{
    "require": {
        "stackla/stackla-php-sdk": "1.0.0"
    }
}
```

And then:
```
#!bs
$ php composer.phar install
```

### Instantiating a Stack ###

There are 2 ways to connect to Stackla: _OAuth2_ or the previous _api_key_ method The _api_key_ method is being deprecated over time in favour of the _OAuth2_ method.

#### OAuth2 (access_token) ####
This is the preferred method and is being rolled out to all customers at present.

```
#!php

// Stackla stack name
$stack = "foo";

// Stackla Credentials details
$host = "https://api.stackla.com/api/";
$accessToken = "1234567890"; // OAuth2 access_token
$credentials = new \Stackla\Core\Credentials($host, $accessToken, $stack);

// Stackla API configs
$stack = new \Stackla\Api\Stack($credentials, $host, $stack);
```

##### Generate OAuth2 access token #####
If you are developing a custom application that will have a client ID, users will need to authenticate and authorise the use their account in order for you to obtain a token. If you are not developing a custom app, may not need to perform this part.

To generate the token, you can generate the OAuth2 callback endpoint for your application as per the following.
###### link.php ######
```#!php

$stack = "foo";
$host  = "https://api.stackla.com/api/";
$client_id = '1234567890';
$client_secret = '0987654321';
$callback = 'https://test.com/callback.php';

$credentials = new Stackla\Core\Credentials($host, null, $stack);
$access_uri = $credentials->getAccessUri($client_id, $client_secret, $callback);

printf('<a href="%s">Generate Access Token</a>', $access_uri);
```

###### callback.php ######
```#!php

$stack = "foo";
$host  = "https://api.stackla.com/api/";
$client_id = '1234567890';
$client_secret = '0987654321';
$callback = 'https://test.com/callback.php';

$access_code = $_GET['code'];

$credentials = new Stackla\Core\Credentials($host, null, $stack);
$response = $credentials->generateToken($client_id, $client_secret, $access_code, $callback);

if ($response === false) {
    echo "Failed to create the access token.\n";
} else {
    echo "Your access token is '{$credentials->token}'\n";
}

```

[More details](tests/callback)


#### API Key (api_key) ####
*This method is being deprecated. Please contact your customer representative if you are using or intend to use this method.*

```
#!php

// Stackla stack name
$stack = "foo";

// Stackla Credentials details
$host = "https://api.stackla.com/api/";
$apiKey = "1234567890"; // Stackla api key
$credentials = new \Stackla\Core\Credentials($host, $apiKey, $stack, \Stackla\Core\Credentials::TYPE_APIKEY);

// Stackla API configs
$stack = new \Stackla\Api\Stack($credentials, $host, $stack);
```

## Object Reference ##
### Term ###


##### Properties #####

Property | type | value | Post | Put | Definition
---|---|---|---|---|---
id | ```integer``` | | ✘ | ✘ | unique id for term
name | ```string``` | | ✔ | ✔ | Name this new term
display_name | ```string``` | | ✔ | ✔ | string | Display name
active | ```bool``` | | ✔ | ✔ | Flag - 1 or 0
type** | ```enum``` | ```Term::TYPE_PAGE``` <br /> ```Term::TYPE_HASHTAG``` <br /> ```Term::TYPE_USER``` <br /> ```Term::TYPE_SEARCH``` <br /> ```Term::TYPE_LOCATION``` <br /> ```Term::TYPE_GALLERY``` <br /> ```Term::TYPE_SET``` <br /> ```Term::TYPE_BOARD``` <br /> ```Term::TYPE_BLOG``` <br /> ```Term::TYPE_RSS``` <br /> ```Term::TYPE_ATOM``` <br /> ```Term::TYPE_DEFAULT``` <br /> ```Term::TYPE_POST``` <br /> ```Term::TYPE_WEIBO_SHOW``` <br /> ```Term::TYPE_WEIBO_PAGE``` <br /> ```Term::TYPE_WEIBO_TOPIC``` | ✔ | ✘ |
network** | ```enum``` | ```Stackla::NETWORK_TWITTER``` <br /> ```Stackla::NETWORK_FACEBOOK``` <br /> ```Stackla::NETWORK_INSTAGRAM``` <br /> ```Stackla::NETWORK_YOUTUBE``` <br /> ```Stackla::NETWORK_GPLUS``` <br /> ```Stackla::NETWORK_FLICKR``` <br /> ```Stackla::NETWORK_PINTEREST``` <br /> ```Stackla::NETWORK_TUMBLR``` <br /> ```Stackla::NETWORK_RSS``` <br /> ```Stackla::NETWORK_ECAL``` <br /> ```Stackla::NETWORK_STACKLA``` <br /> ```Stackla::NETWORK_WEIBO``` | ✔ | ✘ |
term* | ```string``` | | ✔ | ✔ | keyword for the term
filter | ```string[]``` | | ✔ | ✔ | Filter CSV list
exclude_filter | ```string[]``` | | ✔ | ✔ | Exclude-filter CSV list
fan_filter | ```string[]``` | | ✔ | ✔ | Fan-filter CSV list
fan_exclude_filter | ```string[]``` | | ✔ | ✔ | Fan-exclude-filter CSV list
minimum_fallowers | ```integer``` | | ✔ | ✔ | Threshorld for minimum followers, available only for twitter - search and hashtag terms
moderate_text | ```enum``` | ```Term::MODERATION_PUBLISH``` <br /> ```Term::MODERATION_QUEUE``` <br /> ```Term::MODERATION_DISABLE``` <br /> ```Term::MODERATION_EXCLUDE``` | ✔ | ✔ | value wil be either
moderate_image | ```enum``` | | ✔ | ✔ | same as moderate_text value
moderate_video | ```enum``` | | ✔ | ✔ | same as moderate_text value
retweet_enable | ```bool``` | | ✔ | ✔ |
reply_enable | ```bool``` | | ✔ | ✔ |
reply_to_enable | ```bool``` | | ✔ | ✔ |
partial_match | ```bool``` | | ✔ | ✔ |
include_fan_content | ```bool``` | | ✔ | ✔ |
include_hashtag_in_comments | ```bool``` | | ✔ | ✔ |
include_official_content | ```bool``` | | ✔ | ✔ |
search_exact_phrase | ```string``` | | ✔ | ✔ |
num_of_backfill | ```integer``` | | ✔ | ✘ |
whitelist_handles | ```string[]``` | | ✔ | ✔ |
blacklist_handles | ```string[]``` | | ✔ | ✔ |
avatar | ```string``` | | ✔ | ✔ |
source_user_id | ```string``` | | ✔ | ✔ |
tags | ```Tag[]``` | | ✔ | ✔ | listing of tag
created | ```StacklaDateTime``` | | ✘ | ✘ | Term's creation time
modified | ```StacklaDateTime``` | | ✘ | ✘ | Term's modification time
last_ingestion_post | ```StacklaDateTime``` | | ✘ | ✘ | Term's last ingestion post time

##### Methods #####

Method | argument | type | default | return
---|---|:---|:---|---
create | - | - | - | self or false
update | ```$force``` | ```bool``` | false | self or false
get | ```$limit``` <br /> ```$page``` <br /> ```$options``` | ```integer``` <br /> ```integer``` <br /> ```array``` | ```25``` <br /> ```1``` <br /> ```[]``` | Term objects / self or false
getById | ```$id``` | ```integer``` | - | Term object / self or false
delete | - | - | - | self or false
addTag*** | ```$tag``` | ```Tag``` | - | Term / it self
deleteTag*** | ```$tag``` | ```Tag``` | - | Term / it self
associateTag**** | ```$tag``` | ```Tag``` | - | Term / it self
disassociateTag**** | ```$tag``` | ```Tag``` | - | Term / it self

#### Notes ####
* When creating a new ***facebook*** term or ***twitter user*** term or ***instagram user*** term or ***pinterest user*** term or ***gplus user*** term or ***flickr user*** term, this method will validate the user first.
* After term creation, the ```$network``` and ```$type``` cannot be changed
and if it passes, the term will be created. Alternatively, it will return an error when the the user validation has failed.
* These methods are only adding/deleting tag from the properties. You still need to call **update**.
* *Remember to call this method after the term is created*. These methods will send a request to RESTful API, there is **NO** need to call **update**.


##### Examples #####

Create a new instance of term object:
```#!php
// Create new instance of term object
$term = $stack->instance('term');

// Create new instance of term object with term id and pull from API
$term = $stack->instance('term', 1234);

// Create new instance of term object with term id in array format and pull from API
$term = $stack->instance('term', array('id' => 1234));

// Create new instance of term object with term id only (without pulling the data from API)
$term = $stack->instance('term', 1234, false);
```

Create a new term:
```#!php
// Create new instance of term object
$term = $stack->instance('term');

// Populating term properties
$term->name = 'My awesome term';
$term->display_name = 'My awesome term';
$term->active = 1;
$term->num_of_backfill = 0;
$term->term = 'stacklalife';
$term->type = Term::TYPE_HASHTAG;
$term->filter = '';
$term->network = Stackla::NETWORK_TWITTER;

// Create the term
$term->create();
```

Get term:
```#!php
// Create new instance of term object with term id and pull from API
$term = $stack->instance('term', 1234);

// Create a new instance of term
$term2 = $stack->instance('term');
// get specific term by id
$term2->getById(1234);

// Create a new instance of term
$term3 = $stack->instance('term');
// get 25 terms from RESTful API
$term3->get();

// Create new instance of term
$term4 = $stack->instance('term');
// get 5 terms from page 3
$term4->get(5, 3);
```
Looping through array of terms:
```#!php
// Create new instance of term
$term = $stack->instance('term');
// get 25 terms from RESTful API
$term->get();

// Work with each term
foreach ($term as $iterm) {
    echo $iterm->id . " - " . $iterm->name . " - " . " - " . $iterm->network;
}
```

Update the term:
```#!php
// Create a new instance of term object with term id and pull from API
$term = $stack->instance('term', 1234);

$term->name = "my awesome term name - edited";

// Update
$term->update();

// Create new instance of term object with term id but not pulling from server
$term = $stack->instance('term', 1234, false);

$term->name = "my awesome term name - edited";

// Force update with provided data
$term->update(true);
```

Add and delete tags:
```#!php
// Create new instance of term object with term id and pull from API
$term = $stack->instance('term', 1234);

// Get existing tag without pulling data from API
$tag = $stack->instance('tag', 321, false);

// Add tag to term
$term->addTag($tag);
$term->update();

// Add tag to term
$term->associateTag($tag);

// Delete tag from term
$term->deleteTag($tag);
$term->update();

// Delete tag from term
$term->disassociateTag($tag);

```

Delete Term:
```#!php
// Create new instance of term object with term id and pull from API
$term = $stack->instance('term', 1234);

// Delete term
$term->delete();
```

Catch errors:
```#!php
$term = $stack->instance('term');
try {
    $term->update();
} catch (\Exception $e) {
    echo $e->getMessage();
}
```

--------------

### Filter ###


##### Properties #####

Property | type | value | Post | Put | Definition
---|---|---|---|---|---
id | ```integer``` | | ✘ | ✘ | unique id for filter
name | ```string``` | | ✔ | ✔ | filter name
enable | ```enum``` | ```Filter::ENABLE_HIDDEN``` <br /> ```Filter::ENABLE_PANEL``` <br /> ```Filter::ENABLE_BAR``` | ✔ | ✔ | status to show filter in Social hub
order | ```integer``` | | ✔ | ✔ | order of filter in Social hub
sort | ```enum``` | ```Filter::SORT_LATEST``` <br /> ```Filter::SORT_GREATEST``` <br /> ```Filter::SORT_MOST_VOTES``` | ✔ | ✔ | filter sorting type
networks | ```enum[]``` | ```Stackla::NETWORK_TWITTER``` <br /> ```Stackla::NETWORK_FACEBOOK``` <br /> ```Stackla::NETWORK_INSTAGRAM``` <br /> ```Stackla::NETWORK_YOUTUBE``` <br /> ```Stackla::NETWORK_GPLUS``` <br /> ```Stackla::NETWORK_FLICKR``` <br /> ```Stackla::NETWORK_PINTEREST``` <br /> ```Stackla::NETWORK_TUMBLR``` <br /> ```Stackla::NETWORK_RSS``` <br /> ```Stackla::NETWORK_ECAL``` <br /> ```Stackla::NETWORK_STACKLA``` <br /> ```Stackla::NETWORK_WEIBO``` | ✔ | ✔ | filtering network(s)
tags | ```Tag[]``` | | ✔ | ✔ | filtering tag(s)
media | ```enum[]``` | ```Filter::MEDIA_TEXT``` <br /> ```Filter::MEDIA_IMAGE``` <br /> ```Filter::MEDIA_VIDEO``` <br /> ```Filter::MEDIA_HTML``` | ✔ | ✔ | filtering media(s)


##### Methods #####

Method | argument | type | default | return
---|---|:---|:---|---
create* | - | - | - | self or false
update | ```$force``` | ```bool``` | ```false``` | self or false
get | ```$limit``` <br /> ```$page``` <br /> ```$options``` | ```integer``` <br /> ```integer``` <br /> ```array``` | ```25``` <br /> ```1``` <br /> ```[]``` |
getById | ```$id``` | ```integer``` | - | Filter[] or false
getContents | ```$force``` | ```boolean``` | ```false``` | false or Tile[]
getContents | ```$limit``` <br /> ```$force``` | ```integer``` <br /> ```boolean``` | ```25``` <br /> ```false``` | false or Tile[]
getContents | ```$limit``` <br /> ```$options``` <br /> ```$force``` | ```integer``` <br /> ```array``` <br /> ```boolean``` | ```25``` <br /> ```[]``` <br /> ```false``` | false or Tile[]
getContents | ```$limit``` <br /> ```$page``` <br /> ```$force``` | ```integer``` <br /> ```integer``` <br /> ```boolean``` | ```25``` <br /> ```1``` <br /> ```false``` | false or Tile[]
getContents | ```$limit``` <br /> ```$page``` <br /> ```$options``` <br /> ```$force``` | ```integer``` <br /> ```integer``` <br /> ```array``` <br /> ```boolean``` | ```25``` <br /> ```1``` <br /> ```[]``` <br /> ```false``` | false or Tile[]
delete | - | - | - | self or false


--------------

### Tile ###


##### Properties #####

Property | type | value | Post | Put | Definition
---|---|---|---|---|---
id | ```string``` |  | ✘ | ✘ | Unique identifier for the Tile, in the Stack. This is an object containing a "$id" property, which will expose the ID as a 24-byte string
sta_feed_id | ```string``` | | ✔ | ✘ | Globally unique ID to be used for this post (often referencing external ID)
guid | ```string``` | | ✔ | ✘ | Globally unique ID to be used for this post (often referencing external ID)
term_id | ```integer``` | | ✔ | ✘ | Associated term (optional)
name | ```string``` | | ✔ | ✘ | Display name to be used for the author
avatar | ```string``` | | ✔ | ✘ | URL to be used as the post author's avatar
title | ```string``` | | ✔ | ✘ | Tile title to accompany the message, often used for video tiles
share_text | ```string``` | | ✔ | ✘ | Accompanying text to be used when tile is shared on a Social network (e.g. Twitter, Instagram, Facebook, etc.)
media | ```enum``` | ```Filter::MEDIA_TEXT``` <br /> ```Filter:MEDIA_IMAGE``` <br /> ```Filter::MEDIA_VIDEO``` <br /> ```Filter::MEDIA_HTML``` | ✔ | ✘ | The media type of the post
video_url | ```string``` | | ✔ | ✘ | URL of the video file. Required when media type is "video")
source_user_id | ```string``` | | ✔ | ✘ |
width_ratio | ```string``` | | ✔ | ✘ | Tile width ratio to be used as a ratio vs width_ratio as width to height. Positive numeric value. Required when media type is "html"
height_ratio | ```string``` | | ✔ | ✘ | Tile width ratio to be used as a ratio vs height_ratio as width to height. Positive numeric value. Required when media type is "html"
image | ```string``` | | ✔ | ✘ | Full-sized image URL
image_url | ```string``` | | ✔ | ✘ | Full-sized image URL
image_small_url | ```string``` | | ✔ | ✘ | Small image URL (ideally under to 300x300px, or 600x600px for retina)
image_medium_url | ```string``` | | ✔ | ✘ | Medium image URL (ideally under to 600x600px, or 1200x1200px for retina)
image_large_url | ```string``` | | ✔ | ✘ | Large image URL
image_width | ```integer``` | | ✔ | ✘ |
image_height | ```integer``` | | ✔ | ✘ |
image_small_width | ```integer``` | | ✔ | ✘ |
image_small_height | ```integer``` | | ✔ | ✘ |
image_medium_width | ```integer``` | | ✔ | ✘ |
image_medium_height | ```integer``` | | ✔ | ✘ |
image_large_width | ```integer``` | | ✔ | ✘ |
image_large_height | ```integer``` | | ✔ | ✘ |
message | ```string``` | ```Tile::STATUS_ENABLED``` <br /> ```Tile::STATUS_QUEUE``` <br /> ```Tile::STATUS_DISABLED``` | ✔ | ✔ | Message body, normalised from the content source. Will be the Tweet text, Facebook status, Instagram caption, etc. Maximum 32k characters
original_url | ```string``` | | | | Source content url
html | ```string``` | | ✔ | ✔ | HTML body, up to 32k characters. This field is mandatory for "html" media type
tags | ```Tag[]``` | | ✔ | ✔ | Array of Tag IDs associated with the Tile. Note: May be returned as array of Integers or Strings
source | ```string``` | ```Stackla::NETWORK_TWITTER``` <br /> ```Stackla::NETWORK_FACEBOOK``` <br /> ```Stackla::NETWORK_INSTAGRAM``` <br /> ```Stackla::NETWORK_YOUTUBE``` <br /> ```Stackla::NETWORK_GPLUS``` <br /> ```Stackla::NETWORK_FLICKR``` <br /> ```Stackla::NETWORK_PINTEREST``` <br /> ```Stackla::NETWORK_TUMBLR``` <br /> ```Stackla::NETWORK_RSS``` <br /> ```Stackla::NETWORK_ECAL``` <br /> ```Stackla::NETWORK_STACKLA``` <br /> ```Stackla::NETWORK_WEIBO``` | ✘ | ✘ | The source of the post, often a social network. This field is also referred to as "network" in the filter context
status | ```string``` | | ✔ | ✔ | Tile moderation status
longitude | ```string``` | | ✔ | ✔ | GPS longitude co-ordinate for geo-location
latitude | ```string``` | | ✔ | ✔ | GPS latitude co-ordinate for geo-location
disabled_reason | ```string``` | | ✘ | ✘ | The reason for disabling the tile
disabled | ```bool``` | | ✘ | ✘ | Status either the tile is disabled or not
claimed | ```bool``` | | ✘ | ✘ | Status either the tile is claimed or not
anonymous | ```bool``` | | ✘ | ✘ | Tiles that originate from networks that allow anonymous posts (including Stackla) will have this field set to true. Renderers should not show any attributions the creator of this Tile
score | ```integer``` | | ✘ | ✘ | Accumulated score when the tile being voted
created_at | ```StacklaDateTime``` | | ✘ | ✘ | Creation time
updated_at | ```StacklaDateTime``` | | ✘ | ✘ | Updated time
source_created_at | ```StacklaDateTime``` | | ✘ | ✘ | Original source creation time


##### Methods #####

Method | argument | type | default | return
---|---|:---|:---|---
create* | - | - | - | self or false
update** | ```$force``` | ```bool``` | ```false``` | self or false
get | ```$limit``` <br /> ```$page``` <br /> ```$options``` | ```integer``` <br /> ```integer``` <br /> ```array``` | ```25``` <br /> ```1``` <br /> ```[]``` |
getById | ```$id``` | ```integer``` | - | Tile / it self or false
getByGuid | ```$guid``` | ```string``` | - | Tile / it self or false
getByStaFeedId | ```$sta_feed_id``` | ```string``` | - | Tile / it self or false
addTag*** | ```$tag``` | ```Tag``` | - | Tile / it self
deleteTag*** | ```$tag``` | ```Tag``` | - | Tile / it self

#### Notes ####
* _create_ method will only create sta_feed network data.
* _update_ method will only update ```$status``` and ```$tag``` fields for all network and for **sta_feed** network the update method will able to update ```$message```, ```$html```, ```$longitude```, ```$latitude```.
* _addTag_ and _deleteTag_ will only add/delete tags from the properties, you still need to call _update_.


### Tag ###


##### Properties #####

Property | type | value | Post | Put | Definition
---|---|---|---|---|---
id | ```integer``` | | ✘ | ✘ | Unique identifier for the Tag
tag | ```string``` | | ✔ | ✔ | Name and display title on the Tag
slug | ```string``` | | ✔ | ✔ | Simplified and class-name-fiendly Tag identifier, most often auto-generated. On output this value is often used to set the class
custom_slug | ```bool``` | | ✔ | ✔ | This value specifies if the slug field value is being auto-generated or overwritten by the user. Must be 1 for true or 0 for false
type | ```enum``` | ```Tag:TYPE_CONTENT``` <br /> ```Tag::TYPE_PRODUCT``` <br /> ```Tag::TYPE_COMPETITION``` | ✔ | ✔ | Specifies the type of the Tag
publicly_visible | ```enum``` | ```Tag::VISIBLE``` <br /> ```Tag::NOT_VISIBLE``` | ✔ | ✔ | This value specifies if display renderers should display this Tag in display context
target | ```enum``` | ```Tag::TARGET_BLANK``` <br /> ```Tag::TARGET_SELF``` <br /> ```Tag::TARGET_PARENT``` <br /> ```Tag::TARGET_TOP``` | ✔ | ✔ | When rendered as a link, this will indicate the target attribute for the anchor tag when used with custom_url
system_tag | ```bool``` | | ✘ | ✘ | Indicates whether this Tag is a read-only tag created and managed by the system. Must be 1 for true or 0 for false
priority | ```integer``` | | ✔ | ✔ | Specifies the sequential sort order in which this Tag should be displayed when being rendered for display. Values range from 1 (highest) to 5 (lowest) with 3 being the default
custom_url | ```string``` | | ✔ | ✔ | URL that clicking on the Tag should take the user to. When type is product, this is the URL that the product click-through should be linked to
price | ```string``` | | ✔ | ✔ | User provided price for Tags of type product
ext_product_id | ```string``` | | ✔ | ✔ | User provided reference to external product for Tags of type product. Should be a continous string, best if URL-friendly. When querying for a product by ID, this value can be prefixed with ext: to fetch by it
description | ```string``` | | ✔ | ✔ | User provided description for Tags of type product. Maximum length: 512 characters
image_small_url | ```string``` | | ✔ | ✔ | URL of the small (optimised for 300px x 300px) image PNG/JPG/JPEG/GIF image to be displayed. This should be a HTTPS URL to so that the Stack or widget can be served over HTTPS completely
image_small_width | ```integer``` | | ✔ | ✔ | Width of the small image being used as the image_small_url, in pixels
image_small_height | ```integer``` | | ✔ | ✔ | Height of the small image being used as the image_small_url, in pixels
image_medium_url | ```string``` | | ✔ | ✔ | URL of the medium (optimised for 600px x 600px) image PNG/JPG/JPEG/GIF image to be displayed. This should be a HTTPS URL to so that the Stack or widget can be served over HTTPS completely
image_medium_width | ```integer``` | | ✔ | ✔ | Width of the small image being used as the image_medium_url, in pixels
image_medium_height | ```integer``` | | ✔ | ✔ | Height of the small image being used as the image_medium_url, in pixels
created_at | ```StacklaDateTime``` | | ✘ | ✘ | UTC timestamp of when this Tag was created


##### Methods #####

Method | argument | type | default | return
---|---|:---|:---|---
create | - | - | - | self or false
update | ```$force``` | ```bool``` | ```false``` | self or false
get | ```$limit``` <br /> ```$page``` <br /> ```$options``` | ```integer``` <br /> ```integer``` <br /> ```array``` | ```25``` <br /> ```1``` <br /> ```[]``` | self or false
getById | ```$id``` | ```integer``` | - | self or false
getByExtProductId | ```$id``` | ```string``` | - | self or false
delete | - | - | - | self or false


----------------

### Widget ###


##### Properties #####

Property | type | value | Post | Put | Definition
---|---|---|---|---|---
id | ```integer``` | | ✘ | ✘ | Unique identifier for the Tag
name | ```string``` | | ✔ | ✔ | Name and display title on the Tag
type | ```enum``` | ```Widget::TYPE_FLUID``` <br /> ```Widget::TYPE_STATIC``` | ✔ | ✘ | Specifies the type of the Tag
type_style | ```enum``` | ```Widget::STYLE_VERTICAL_FLUID``` (for fluid only) <br /> ```Widget::STYLE_HORIZONTAL_FLUID``` (for fluid only) <br /> ```Widget::STYLE_CAROUSEL``` <br /> ```Widget::STYLE_SCROLL``` <br /> ```Widget::STYLE_SLIDESHOW``` <br /> ```Widget::STYLE_AUTO``` | ✔ | ✔ | This value is depend on the type.
filter_id | ```integer``` | | ✔ | ✔ | default filter for the widget
parent_id | ```integer``` | | ✘ | ✘ | This field will be filled using source filter_id if the widget is derived from other widget
embed_code | ```string``` | | ✘ | ✘ | The embed code (html) for website


##### Methods #####

Method | argument | type | default | return
---|---|:---|:---|---
create | - | - | - | self or false
update | ```$force``` | ```bool``` | ```false``` | self or false
get | ```$limit``` <br /> ```$page``` <br /> ```$options``` | ```integer``` <br /> ```integer``` <br /> ```array``` | ```25``` <br /> ```1``` <br /> ```[]``` | Term objects / self or false
getById | ```$id``` | ```integer``` | - | widget object / self or false
duplicate* | - | - | - | this method will clone current loaded widget. return false if failed or widget object if success
derive** | ```$filter_id``` <br> ```$name``` | ```integer``` <br> ```string``` | - <br> - | This method will inherit current loaded widget to new widget with provided filter id and name. return false if failed or widget object if success
delete*** | - | - | - | widget object / self or false

#### Notes ####
* _clone_ or _duplicate_ will copy all CSS and JavaScript from the source.
* _derive_ widget cannot be changed. The only fields that can be changed are ```$filter_id``` and ```$name```.
* _delete_ will not work if the widget has child widget.

----------------

[Error Handling](doc/error_handling.md)
