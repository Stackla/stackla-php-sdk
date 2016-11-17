<?php

namespace Stackla\Tests\Service;

use Stackla\Api\Tile;
use Stackla\Api\Tag;
use Stackla\Api\Stack;
use Stackla\Core\Credentials;
use Stackla\Core\StacklaDateTime;
use Stackla\Core\Request;

class TileTest extends \PHPUnit_Framework_TestCase
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
        // Abort if we use a read only token
        if (!ACCESS_TOKEN && ACCESS_TOKEN_PERMISSION == 'r') {
            $this->markTestSkipped(
              'This test need an ACCESS_TOKEN_PERMISSION with read and write permission.'
            );
        }

        $tile = $this->stack->instance('tile');
        $tile->name = 'Test tile\'s name';
        $tile->message = 'Test tile\'s message #testtile '.date('Y-m-d h:i:s');
        $tile->term_id = STACKLA_POST_TERM_ID;
        $tile->image_url = 'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/e35/14374061_652837251542826_5176014892573917184_n.jpg';

        $validations = $tile->validate();

        if (count($validations)) {
            foreach ($validations as $validation) {
                echo $validation['property']." -- ".$validation['message'].";\n";
            }
            throw new \Exception("Invalid data for Tile");
        }

        $res = $tile->create();

        if ($res) {
            $this->assertEquals(200, $tile->getResponseCode(), "Need to be 200");
            $this->assertNotEmpty($tile->sta_feed_id, "Tile created without any ID");
            $this->assertNotEmpty($tile->created_at, "Tile created without any create time");
            $this->assertEquals(0, count($tile->errors), "Error: ".json_encode($tile->errors));
        }


        // wait 15 seconds for content to be ingested
        sleep(15);

        if ($tile->sta_feed_id) {
            $request = new Request($this->credentials, API_HOST, API_STACK);

            $jsonContent = $request->sendGet('tiles/guid:'.$tile->sta_feed_id);

            if ($jsonContent === false) {
                $response = $request->getResponse();
                $jsonContent = $response->getBody(true);
            }

            $resTile = json_decode($jsonContent, true);

            if (!count($resTile['errors'])) { //
                $this->assertEmpty($resTile['errors'], sprintf("Tile with ID '%s' is not exist", $tile->sta_feed_id));
            } else { // validating all fields
                $this->assertEquals($tile->name, $resTile['data']['name'], 'Tile\'s name is different from the post request value');
                $this->assertEquals($tile->message, $resTile['data']['message'], 'Tile\'s message is different from the post request value');
                $this->assertEquals($tile->image_url, $resTile['data']['image_url'], 'Tile\'s image_url is different from the post request value');
            }

        }

        return $tile;
    }

    public function testFetch()
    {
        $tile = $this->stack->instance('tile');
        $tile->get(DEFAULT_FILTER_ID);

        $this->assertGreaterThan(0, count($tile));

        $tileClass = get_class(new \Stackla\Api\Tile);
        foreach ($tile as $index => $item) {
            $this->assertEquals($tileClass, get_class($item), sprintf("Record %d is not Tile instance", $index));
        }
    }

    /**
     * @depends testCreate
     */
    public function testFetchByStacklaFeedId(Tile $tileRes = null)
    {
        $sta_feed_id = $tileRes->sta_feed_id;
        /** @var Tile $tile */
        $tile = $this->stack->instance('tile');
        $tile->getByStacklaFeedId($sta_feed_id);

        $tiles = $tile->getResults();
        if (count($tiles)) {
            $tileClass = get_class(new Tile());
            foreach ($tiles as $tile) {
                $this->assertEquals($tileClass, get_class($tile), 'Tile is not using '.$tileClass.' class; '.$tile->toJSON());
            }
        }

        $this->assertEquals($sta_feed_id, $tile->sta_feed_id, 'ID must be equal');
        $this->assertEquals(get_class(new StacklaDateTime()), get_class($tile->created_at), 'created_on must be DateTime object');
        $this->assertGreaterThanOrEqual(1, count($tile));
        $this->assertEquals(0, count($tile->errors));

        return $tile;
    }

    /**
     * @depends testFetchByStacklaFeedId
     */
    public function testFetchById(Tile $tileRes = null)
    {
        $tile = $this->stack->instance('tile');
        $tile->getById($tileRes->getId());

        $tiles = $tile->getResults();
        if (count($tiles)) {
            $tileClass = get_class(new Tile());
            foreach ($tiles as $tile) {
                $this->assertEquals($tileClass, get_class($tile), 'Tile is not using '.$tileClass.' class; '.$tile->toJSON());
            }
        }

        $this->assertEquals($tileRes->getId(), $tile->id, 'ID must be equal');
        $this->assertEquals(get_class(new StacklaDateTime()), get_class($tile->created_at), 'created_on must be DateTime object');
        $this->assertGreaterThanOrEqual(1, count($tile));
        $this->assertEquals(0, count($tile->errors));
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Tile $tileRes)
    {
        //$tile = new Tile($this->credentials);
        $tile = $this->stack->instance('tile');
        $tile->getByGuid($tileRes->sta_feed_id);
        $newName = $tileRes->name.' - Edited';
        $newMessage = $tileRes->message.' - Edited';
        $tile->name = $newName;
        $tile->message = $newMessage;

        $tile->update();

        $tile->getByGuid($tileRes->sta_feed_id);

        $this->assertEquals(0, count($tile->errors));
        $this->assertEquals($newMessage, $tile->message);
        $this->assertEquals($newName, $tile->name);

        return $tileRes;
    }

    /**
     * @depends testCreate
     */
    public function testAddTag(Tile $tileRes)
    {
        $sta_feed_id = $tileRes->sta_feed_id;
        $tag = $this->stack->instance('tag', DEFAULT_TAG_ID);
        $tile = $this->stack->instance('tile');
        $tile->getByGuid($sta_feed_id);
        $tile->addTag($tag);
        $tile->update();

        $tile2 = $this->stack->instance('tile');
        $tile2->getByGuid($sta_feed_id);

        $tileTags = $tile2->tags;

        $this->assertGreaterThan(0, count($tile2->tags), 'No tag associated to this tile');
        $this->assertEquals((int)DEFAULT_TAG_ID, $tileTags[count($tileTags) - 1]->id, 'Tag ID is not the same');
    }

    /**
     * @depends testCreate
     */
    public function testDeleteTag(Tile $tileRes)
    {
        $sta_feed_id = $tileRes->sta_feed_id;
        $tagCount = count($tileRes->tags);
        $tag = $this->stack->instance('tag', DEFAULT_TAG_ID);
        /** @var Tile $tile */
        $tile = $this->stack->instance('tile');
        $tile->getByGuid($sta_feed_id);
        // TODO: fix this test, seems to occure server side...
        $tile->deleteTag($tag);
        $tile->update();

        $tile2 = $this->stack->instance('tile');
        $tile2->getByGuid($sta_feed_id);

        $this->assertEquals($tagCount, count($tile2->tags), 'Tag associated to this tile has not been deleted');
    }

    /**
     * @depends testUpdate
     */
    public function testDisabled(Tile $tileRes)
    {
        $tile = $this->stack->instance('tile');
        $tile->getByGuid($tileRes->sta_feed_id);
        $tile->status = Tile::STATUS_DISABLED;
        $tile->update();

        $tile2 = $this->stack->instance('tile');
        $tile2->getByGuid($tileRes->sta_feed_id);

        $this->assertEquals($tile2->status, Tile::STATUS_DISABLED);
        $this->assertEquals(0, count($tileRes->getErrors()));
    }
}
