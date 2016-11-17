<?php

namespace Stackla\Tests\Service;

use Stackla\Api\Term;
use Stackla\Api\Stack;
use Stackla\Api\Stackla;
use Stackla\Api\Tag;
use Stackla\Core\Credentials;
use Stackla\Core\StacklaDateTime;
use Stackla\Core\Request;

class TermTest extends \PHPUnit_Framework_TestCase
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

        // $term = new Term($this->credentials);
        $term = $this->stack->instance('term');
        $term->name = 'Test term ' . $this->uniqId;
        $term->display_name = 'Test term ' . $this->uniqId;
        $term->active = 1;
        $term->num_of_backfill = 0;
        $term->term = 'stacklalife';
        $term->type = Term::TYPE_HASHTAG;
        $term->filter = '';
        $term->network = Stackla::NETWORK_TWITTER;

        $res = $term->create();

        if ($res) {
            $this->assertGreaterThan(0, $term->id, "Term created without any ID");
            // $this->assertNotEmpty($term->created, "Term created without any create time");
            $this->assertEquals(0, count($term->errors), "Error: ".json_encode($term->errors));
        }

        if ($term->id) {
            $request = new Request($this->credentials, API_HOST, API_STACK);

            $jsonContent = $request->sendGet('terms/'.$term->id);

            if ($jsonContent === false) {
                $response = $request->getResponse();
                $jsonContent = $response->getBody(true);
            }

            $resTerm = json_decode($jsonContent, true);

            if (!count($resTerm['errors'])) { //
                $this->assertEmpty($resTerm['errors'], sprintf("Term with ID '%s' is not exist", $term->id));
            } else { // validating all fields
                $this->assertEquals($term->term, $resTerm['data']['term'], 'Term\'s term is different from the post request value');
                $this->assertEquals($term->network, $resTerm['data']['network'], 'Term\'s network is different from the post request value');
                $this->assertEquals($term->filter, $resTerm['data']['filter'], 'Term\'s filter is different from the post request value');
                $this->assertEquals($term->display_name, $resTerm['data']['displat_name'], 'Term\'s display name is different from the post request value');
                $this->assertEquals($term->type, $resTerm['data']['type'], 'Term\'s type is different from the post request value');
                $this->assertEquals($term->name, $resTerm['data']['name'], 'Term\'s name is different from the post request value');
            }

        }

        return $term;
    }

    public function testCreateTwitterTerm()
    {
        // Abort if we use a read only token
        if (!ACCESS_TOKEN && ACCESS_TOKEN_PERMISSION == 'r') {
            $this->markTestSkipped(
              'This test need an ACCESS_TOKEN_PERMISSION with read and write permission.'
            );
        }

        $term = $this->stack->instance('term');
        $term->name = 'Test user type term';
        $term->display_name = 'Test user term';
        $term->active = 1;
        $term->num_of_backfill = 0;
        $term->term = 'stacklatest';
        $term->type = Term::TYPE_USER;
        $term->filter = '';
        $term->network = Stackla::NETWORK_TWITTER;

        $res = $term->create();

        if ($res) {
            $this->assertGreaterThan(0, $term->id, "Term created without any ID");
            // $this->assertNotEmpty($term->created, "Term created without any create time");
            $this->assertEquals(0, count($term->errors), "Error: ".json_encode($term->errors));
        }

        if ($term->id) {
            $request = new Request($this->credentials, API_HOST, API_STACK);

            $jsonContent = $request->sendGet('terms/'.$term->id);

            if ($jsonContent === false) {
                $response = $request->getResponse();
                $jsonContent = $response->getBody(true);
            }

            $resTerm = json_decode($jsonContent, true);

            if (!count($resTerm['errors'])) { //
                $this->assertEmpty($resTerm['errors'], sprintf("Term with ID '%s' is not exist", $term->id));
            } else { // validating all fields
                $this->assertEquals($term->source_user_id, $resTerm['data']['source_user_id'], 'Term\'s source_user_id is different from the post request value');
                $this->assertEquals($term->term, $resTerm['data']['term'], 'Term\'s term is different from the post request value');
                $this->assertEquals($term->network, $resTerm['data']['network'], 'Term\'s network is different from the post request value');
                $this->assertEquals($term->filter, $resTerm['data']['filter'], 'Term\'s filter is different from the post request value');
                $this->assertEquals($term->display_name, $resTerm['data']['displat_name'], 'Term\'s display name is different from the post request value');
                $this->assertEquals($term->type, $resTerm['data']['type'], 'Term\'s type is different from the post request value');
                $this->assertEquals($term->name, $resTerm['data']['name'], 'Term\'s name is different from the post request value');
            }

            $term->delete();
        }
    }

    public function testCreateInstagramTerm()
    {
        // Abort if we use a read only token
        if (!ACCESS_TOKEN && ACCESS_TOKEN_PERMISSION == 'r') {
            $this->markTestSkipped(
              'This test need an ACCESS_TOKEN_PERMISSION with read and write permission.'
            );
        }

        $term = $this->stack->instance('term');
        $term->name = 'Test user type term';
        $term->display_name = 'Test user term';
        $term->active = 1;
        $term->num_of_backfill = 0;
        $term->term = 'stackdev';
        $term->type = Term::TYPE_USER;
        $term->filter = '';
        $term->network = Stackla::NETWORK_INSTAGRAM;

        $res = $term->create();

        if ($res) {
            $this->assertGreaterThan(0, $term->id, "Term created without any ID");
            // $this->assertNotEmpty($term->created, "Term created without any create time");
            $this->assertEquals(0, count($term->errors), "Error: ".json_encode($term->errors));
        }

        if ($term->id) {
            $request = new Request($this->credentials, API_HOST, API_STACK);

            $jsonContent = $request->sendGet('terms/'.$term->id);

            if ($jsonContent === false) {
                $response = $request->getResponse();
                $jsonContent = $response->getBody(true);
            }

            $resTerm = json_decode($jsonContent, true);

            if (!count($resTerm['errors'])) { //
                $this->assertEmpty($resTerm['errors'], sprintf("Term with ID '%s' is not exist", $term->id));
            } else { // validating all fields
                $this->assertEquals($term->source_user_id, $resTerm['data']['source_user_id'], 'Term\'s source_user_id is different from the post request value');
                $this->assertEquals($term->term, $resTerm['data']['term'], 'Term\'s term is different from the post request value');
                $this->assertEquals($term->network, $resTerm['data']['network'], 'Term\'s network is different from the post request value');
                $this->assertEquals($term->filter, $resTerm['data']['filter'], 'Term\'s filter is different from the post request value');
                $this->assertEquals($term->display_name, $resTerm['data']['displat_name'], 'Term\'s display name is different from the post request value');
                $this->assertEquals($term->type, $resTerm['data']['type'], 'Term\'s type is different from the post request value');
                $this->assertEquals($term->name, $resTerm['data']['name'], 'Term\'s name is different from the post request value');
            }

            $term->delete();
        }
    }

    public function testCreateFacebookTerm()
    {
        // Abort if we use a read only token
        if (!ACCESS_TOKEN && ACCESS_TOKEN_PERMISSION == 'r') {
            $this->markTestSkipped(
              'This test need an ACCESS_TOKEN_PERMISSION with read and write permission.'
            );
        }

        $term = $this->stack->instance('term');
        $term->name = 'Test user type term';
        $term->display_name = 'Test user term';
        $term->active = 1;
        $term->num_of_backfill = 0;
        $term->term = 'CNCASYum';
        $term->type = Term::TYPE_USER;
        $term->filter = '';
        $term->network = Stackla::NETWORK_FACEBOOK;

        $res = $term->create();

        if ($res) {
            $this->assertGreaterThan(0, $term->id, "Term created without any ID");
            // $this->assertNotEmpty($term->created, "Term created without any create time");
            $this->assertEquals(0, count($term->errors), "Error: ".json_encode($term->errors));
        }

        if ($term->id) {
            $request = new Request($this->credentials, API_HOST, API_STACK);

            $jsonContent = $request->sendGet('terms/'.$term->id);

            if ($jsonContent === false) {
                $response = $request->getResponse();
                $jsonContent = $response->getBody(true);
            }

            $resTerm = json_decode($jsonContent, true);

            if (!count($resTerm['errors'])) { //
                $this->assertEmpty($resTerm['errors'], sprintf("Term with ID '%s' is not exist", $term->id));
            } else { // validating all fields
                $this->assertEquals($term->source_user_id, $resTerm['data']['source_user_id'], 'Term\'s source_user_id is different from the post request value');
                $this->assertEquals($term->term, $resTerm['data']['term'], 'Term\'s term is different from the post request value');
                $this->assertEquals($term->network, $resTerm['data']['network'], 'Term\'s network is different from the post request value');
                $this->assertEquals($term->filter, $resTerm['data']['filter'], 'Term\'s filter is different from the post request value');
                $this->assertEquals($term->display_name, $resTerm['data']['displat_name'], 'Term\'s display name is different from the post request value');
                $this->assertEquals($term->type, $resTerm['data']['type'], 'Term\'s type is different from the post request value');
                $this->assertEquals($term->name, $resTerm['data']['name'], 'Term\'s name is different from the post request value');
            }

            $term->delete();
        }
    }

    public function testFetch()
    {
        $term = $this->stack->instance('term');
        $res = $term->get();

        $this->assertNotFalse($res, 'Get request error');

        if ($res) {
            $this->assertGreaterThanOrEqual(1, count($term));
            $this->assertEquals(0, count($term->errors));
        }
    }

    public function testTermsTag()
    {
        $termid = STACKLA_POST_TERM_ID;
        $term = $this->stack->instance('term');
        $term->getById($termid);

        $tags = $term->tags;

        $tagClass = get_class(new Tag());
        foreach ($tags as $tag) {
            $this->assertEquals($tagClass, get_class($tag), 'Tag is not using '.$tagClass.' class; '.json_encode($tag));
        }

        $this->assertEquals($termid, $term->id, 'ID must be equal');
        $this->assertEquals(get_class(new StacklaDateTime()), get_class($term->created), 'created_on must be DateTime object');
        $this->assertGreaterThanOrEqual(1, count($term));
        $this->assertEquals(0, count($term->errors));
    }

    public function testFetchById()
    {
        $term = $this->stack->instance('term');
        $term->getById(STACKLA_POST_TERM_ID);

        $tags = $term->tags;
        if ($tags) {
            $tagClass = get_class(new Tag());
            foreach ($tags as $tag) {
                $this->assertEquals($tagClass, get_class($tag), 'Tag is not using '.$tagClass.' class; '.json_encode($tag));
            }
        }

        $this->assertEquals(STACKLA_POST_TERM_ID, $term->id, 'ID must be equal');
        $this->assertEquals(get_class(new StacklaDateTime()), get_class($term->created), 'created_on must be DateTime object');
        $this->assertGreaterThanOrEqual(1, count($term));
        $this->assertEquals(0, count($term->errors));
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Term $termRes)
    {
        // $tag = new Tag($this->credentials);
        // $tag->get();
        $term = $this->stack->instance('term');
        $term->getById($termRes->id);
        $newName = $termRes->name.' - Edited';
        $term->name = $newName;
        $term->display_name = $newName;
        // $term->tags = $tag->getResults();

        // echo "\n". $term->toJSON() ."\n";
        $term->update();
        $term->getById($termRes->id);

        $this->assertEquals(0, count($term->errors));
        $this->assertEquals($newName, $term->display_name);
        $this->assertEquals($newName, $term->name);
    }

    /**
     * @depends testCreate
     */
    public function testAddTag(Term $termRes)
    {
        $tag = $this->stack->instance('tag', DEFAULT_TAG_ID);
        $termRes->addTag($tag);
        $termRes->update();

        $term = $this->stack->instance('term', $termRes->id);

        $termTags = $term->tags;

        $this->assertGreaterThan(0, count($term->tags), 'No tag associated to this term');
        $this->assertEquals((int)DEFAULT_TAG_ID, $termTags[0]->id, 'Tag ID is not the same');
    }

    /**
     * @depends testCreate
     */
    public function testDeleteTag(Term $termRes)
    {
        $tag = $this->stack->instance('tag', DEFAULT_TAG_ID);
        $termRes->deleteTag($tag);
        $termRes->update();

        $term = $this->stack->instance('term', $termRes->id);

        $this->assertEquals(0, count($term->tags), 'No tag associated to this term');
    }

    /**
     * @depends testCreate
     */
    public function testRemove(Term $termRes)
    {
        $termRes->delete();

        try {
            $term = $this->stack->instance('term');
            $term->getById($termRes->id);

        } catch (\Exception $e) {
            // exception's been throw because of the requested term is not exist
        }
        // $term should be empty because the deletion
        $this->assertEquals(0, count($term));
        $this->assertGreaterThan(0, count($term->getErrors()));
        $this->assertEquals(0, count($termRes->getErrors()));
    }
}
