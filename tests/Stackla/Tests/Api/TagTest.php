<?php

namespace Stackla\Tests\Api;

use Stackla\Api\Tag;
use Stackla\Api\Stack;
use Stackla\Core\Credentials;
use Stackla\Core\StacklaDateTime;
use Stackla\Core\Request;

class TagTest extends \PHPUnit_Framework_TestCase
{
    private $credentials;
    private $stack;
    public function __construct()
    {
        $this->credentials = new Credentials(API_HOST, ACCESS_TOKEN, API_STACK);

        $this->stack = new \Stackla\Api\Stack($this->credentials, API_HOST, API_STACK);
    }

    public function testCreate()
    {
        $tag = $this->stack->instance('Tag');
        $tag->tag = 'Test tag';
        $tag->slug = 'Test tag slug';
        $tag->type = Tag::TYPE_CONTENT;
        $tag->custom_url = 'http://stackla.com/';
        $tag->publicly_visible = Tag::VISIBLE;

        $validations = $tag->validate();

        if (count($validations)) {
            foreach ($validations as $validation) {
                echo $validation['property'] . " -- " . $validation['message'] . "\n";
            }
            throw new \Exception("invalid properties");
        }

        $res = $tag->create();

        if ($res) {
            $this->assertGreaterThan(0, $tag->id, "Tag created without any ID");
            $this->assertNotEmpty($tag->created_at, "Tag created without any timestamp");
            $this->assertEquals(0, count($tag->errors), "Error: " . json_encode($tag->errors));
        }

        if ($tag->id) {
            $request = new Request($this->credentials, API_HOST, API_STACK);
            $jsonContent = $request->sendGet('tags/' .  $tag->id);

            $resTag = json_decode($jsonContent, true);

            if (count($resTag['errors'])) { //
                $this->assertEmpty($resTag['errors'], sprintf("Tag with ID '%s' is not exist", $tag->id));
            } else { // validating all fields
                $this->assertEquals($tag->type, $resTag['data']['type'], 'Tag\'s type is different from the post request value');
                $this->assertEquals($tag->tag, $resTag['data']['tag'], 'Tag\'s name is different from the post request value');
                $this->assertEquals($tag->slug, $resTag['data']['slug'], 'Tag\'s slug is different from the post request value');
                $this->assertEquals($tag->custom_url, $resTag['data']['custom_url'], 'Tag\'s custom url is different from the post request value');
            }

        }

        return $tag;
    }

    // /**
    //  * @depends testCreate
    //  */
    public function testFetch()
    {
        $tag = $this->stack->instance('Tag');
        $res = $tag->get();

        if ($res) {
            $this->assertGreaterThanOrEqual(1, count($tag));
            $this->assertEquals(0, count($tag->errors));
        }
    }

    /**
     * @depends testCreate
     */
    public function testFetchById(Tag $tagRes)
    {
        $tag = $this->stack->instance('tag', $tagRes->id);

        $this->assertGreaterThan(0, count($tag), 'Get request error');

        $this->assertEquals($tagRes->id, $tag->id, 'ID must be equal');
        $this->assertEquals(get_class(new StacklaDateTime()), get_class($tag->created_at), 'created_at must be DateTime object');
        $this->assertGreaterThanOrEqual(1, count($tag));
        $this->assertEquals(0, count($tag->errors));
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Tag $tagRes)
    {
        $tag = $this->stack->instance('tag', $tagRes->id);
        $newName = $tagRes->tag . ' - Edited';
        $tag->tag = $newName;
        $res = $tag->update();

        $this->assertNotFalse($res, 'Update request error');

        if ($res) {
            $tag->getById($tagRes->id);

            $this->assertEquals(0, count($tag->errors));
            $this->assertEquals($newName, $tag->tag);
        }
   }

    /**
     * @depends testCreate
     */
    public function testRemove(Tag $tagRes)
    {
        $res = $tagRes->delete();

        $this->assertNotFalse($res, 'Delete request Error');

        if ($res) {
            try {
                $tag = $this->stack->instance('tag');
                $tag->getById($tagRes->id);
            } catch(\Exception $e) {
                // exception's been throw because of the requested tag is not exist
            }
            // $tag should empty because the deletion
            $this->assertEquals(0, count($tag));
            $this->assertGreaterThan(0, count($tag->getErrors()));
            $this->assertEquals(0, count($tagRes->getErrors()));
        }
    }
}
