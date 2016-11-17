<?php

namespace Stackla\Tests\Service;

use Stackla\Api\Filter;
use Stackla\Api\Stack;
use Stackla\Api\Stackla;
use Stackla\Core\Credentials;
use Stackla\Core\Request;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    private $credentials;
    private $stack;
    private $uniqId;
    public function __construct()
    {
        $this->credentials = new Credentials(API_HOST, ACCESS_TOKEN, API_STACK);

        $this->stack = new \Stackla\Api\Stack($this->credentials, API_HOST, API_STACK);
        $this->uniqId = uniqid();
    }

    public function testCreate()
    {
        // Abort if we use a read only token
        if (!ACCESS_TOKEN && ACCESS_TOKEN_PERMISSION == 'r') {
            $this->markTestSkipped(
              'This test need an ACCESS_TOKEN_PERMISSION with read and write permission.'
            );
        }

        $tag = $this->stack->instance('tag', DEFAULT_TAG_ID, false);

        $filter = $this->stack->instance('filter');
        $filter->name = 'Test filter ' . $this->uniqId;
        $filter->enabled = Filter::ENABLE_HIDDEN;
        $filter->sort = Filter::SORT_LATEST;
        $filter->orders = 11; // random number
        $filter->addTag($tag);
        $filter->addMedia(Filter::MEDIA_IMAGE);
        $filter->addMedia(Filter::MEDIA_TEXT);
        $filter->addNetwork(Stackla::NETWORK_TWITTER);
        $filter->addNetwork(Stackla::NETWORK_INSTAGRAM);
        $filter->addNetwork(Stackla::NETWORK_STACKLA);

        $validations = $filter->validate();

        if (count($validations)) {
            foreach ($validations as $validation) {
                echo $validation['property']." -- ".$validation['message']."\n";
            }
            throw new \Exception("invalid properties");
        }

        $res = $filter->create();

        if ($res) {
            $this->assertGreaterThan(0, $filter->id, "Filter created without any ID");
            $this->assertEquals(0, count($filter->errors), "Error: ".json_encode($filter->errors));
        }

        if ($filter->id) {
            $request = new Request($this->credentials, API_HOST, API_STACK);
            $jsonContent = $request->sendGet('filters/'.$filter->id);

            $resFilter = json_decode($jsonContent, true);

            if (count($resFilter['errors'])) { //
                $this->assertEmpty($resFilter['errors'], sprintf("Filter with ID '%s' is not exist", $filter->id));
            } else { // validating all fields
                $this->assertEquals($filter->name, $resFilter['data']['name'], 'Filter\'s name is different from the post request value');
                $this->assertEquals($filter->sort, $resFilter['data']['sort'], 'Filter\'s sorting is different from the post request value');
                $this->assertEquals($filter->orders, $resFilter['data']['orders'], 'Filter\'s orders is different from the post request value');
                $this->assertEquals($filter->enabled, $resFilter['data']['enabled'], 'Filter\'s enabled is different from the post request value');
                $this->assertEquals($filter->media, $resFilter['data']['media'], 'Filter\'s media are different from the post request value');
                $this->assertEquals($filter->networks, $resFilter['data']['networks'], 'Filter\'s network are different from the post request value');
            }
        }

        return $filter;
    }

    public function testFetch()
    {
        $filter = $this->stack->instance('filter');
        $res = $filter->get();
        if ($res) {
            $this->assertGreaterThanOrEqual(1, count($filter));
            $this->assertEquals(0, count($filter->errors));
        } else {
            $this->assertFalse($res);
        }
    }

    public function testFetchContents()
    {
        $filter = $this->stack->instance('filter', DEFAULT_FILTER_ID);
        $tiles = $filter->getContents();
        $this->assertGreaterThan(0, count($tiles));

        $tileClass = get_class(new \Stackla\Api\Tile);
        foreach ($tiles as $index => $tile) {
            $this->assertEquals($tileClass, get_class($tile), sprintf("Record %d is not Tile instance", $index));
        }

        return $filter;
    }

    /**
     * @depends testFetchContents
     */
    public function testFetchById(Filter $filterRes)
    {
        $filter = $this->stack->instance('filter', $filterRes->id);

        $this->assertNotNull($filter->id, 'Get request error');

        if ($filter->id) {
            $this->assertEquals($filterRes->id, $filter->id, 'ID must be equal');
            $this->assertEquals($filterRes->name, $filter->name, 'Name must be equal');
            $this->assertGreaterThanOrEqual(1, count($filter));
            $this->assertEquals(0, count($filter->errors));
        }
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Filter $filterRes)
    {
        $newName = $filterRes->name.' - Edited';
        $filter = $this->stack->instance('filter', $filterRes->id);
        $filter->name = $newName;
        $filter->update();

        $filter2 = $this->stack->instance('filter', $filterRes->id);

        $this->assertEquals(0, count($filter2->errors));
        $this->assertEquals($newName, $filter2->name);
    }

    /**
     * @depends testCreate
     */
    public function testRemove(Filter $filterRes)
    {
        $res = $filterRes->delete();

        $this->assertNotFalse($res, 'Delete request Error');

        if ($res) {
            try {
                $filter = $this->stack->instance('filter');
                $filter->getById($filterRes->id);
            } catch (\Exception $e) {
                // Requested filter not exists
            }
            // $filter should be empty because of the deletion
            $this->assertEquals(0, count($filter));
            $this->assertGreaterThan(0, count($filter->getErrors()));
            $this->assertEquals(0, count($filterRes->getErrors()));
        }
    }

}
