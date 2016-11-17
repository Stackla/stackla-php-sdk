<?php

namespace Stackla\Tests\Api;

use Stackla\Api\Widget;
use Stackla\Api\Stack;
use Stackla\Core\Credentials;
use Stackla\Core\StacklaDateTime;
use Stackla\Core\Request;

class WidgetTest extends \PHPUnit_Framework_TestCase
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

        $widget = $this->stack->instance('Widget');
        $widget->name = 'Test widget ' . $this->uniqId;
        $widget->type_style = Widget::STYLE_BASE_WATERFALL;
        $widget->filter_id = (int) DEFAULT_FILTER_ID;

        $validations = $widget->validate();
        if (count($validations) > 0) {
            foreach ($validations as $val) {
                echo $val['property'] . " -- " . $val['message'];
                echo ";\n";
            }
        }

        $res = $widget->create();

        if ($res) {
            $this->assertGreaterThan(0, $widget->id, "Widget created without any ID");
            $this->assertEquals(0, count($widget->errors), "Error: " . json_encode($widget->errors));
        }

        if ($widget->id) {
            $request = new Request($this->credentials, API_HOST, API_STACK);
            $jsonContent = $request->sendGet('widgets/' .  $widget->id);

            $resWidget = json_decode($jsonContent, true);

            if (count($resWidget['errors'])) { //
                $this->assertEmpty($resWidget['errors'], sprintf("Widget with ID '%s' is not exist", $widget->id));
            } else { // validating all fields
                $this->assertEquals($widget->type, $resWidget['data']['style']['type'], 'Widget\'s type is different from the post request value ');
                $this->assertEquals($widget->type_style, $resWidget['data']['style']['style'], 'Widget\'s type style is different from the post request value');
                $this->assertEquals($widget->name, $resWidget['data']['style']['name'], 'Widget\'s name is different from the post request value');
                $this->assertEquals($widget->filter_id, $resWidget['data']['filter_id'], 'Widget\'s filter id is different from the post request value');
            }

        }

        return $widget;
    }

    /**
     * @depends testCreate
     */
    public function testClone(Widget $widgetRes)
    {
        $widget = $widgetRes->duplicate();

        $this->assertNotEquals($widget->id, $widgetRes->id, "The clone ID is same to the source ID");
        $this->assertEquals($widget->name, $widgetRes->name . " (Clone)", "The cloned widget name should exactly the same with the source widget name");

        if ($widget) {
            $widget->delete();
        }
    }

    /**
     * @depends testCreate
     */
    public function testDerive(Widget $widgetRes)
    {
        $deriveName = "DERIVED:Test Widget";
        $widget = $widgetRes->derive(DEFAULT_FILTER_ID, $deriveName);

        $this->assertNotEquals($widget->id, $widgetRes->id, "The derived ID is same to the source ID");
        $this->assertEquals($widget->name, $deriveName, "The derived widget name should exactly the same with the source widget name");
        $this->assertEquals($widget->parent_id, $widgetRes->id, "The derived parent ID should be the source widget ID");

        if ($widget) {
            $widget->delete();
        }
    }

    public function testFetch()
    {
        $widget = $this->stack->instance('Widget');
        $res = $widget->get();

        if ($res) {
            $this->assertGreaterThanOrEqual(1, count($widget));
            $this->assertEquals(0, count($widget->errors));
        }
    }

    /**
     * @depends testCreate
     */
    public function testFetchById(Widget $widgetRes)
    {
        $widget = $this->stack->instance('widget', $widgetRes->id);

        $this->assertGreaterThan(0, count($widget), 'Get request error');

        $this->assertEquals($widgetRes->id, $widget->id, 'ID must be equal');
        $this->assertGreaterThanOrEqual(1, count($widget));
        $this->assertEquals(0, count($widget->errors));
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Widget $widgetRes)
    {
        $widget = $this->stack->instance('widget', $widgetRes->id);
        $newName = $widgetRes->name . ' - Edited';
        $widget->name = $newName;
        $res = $widget->update();

        $this->assertNotFalse($res, 'Update request error');

        if ($res) {
            $widget->getById($widgetRes->id);

            $this->assertEquals(0, count($widget->errors));
            $this->assertEquals($newName, $widget->name);
        }
   }

    /**
     * @depends testCreate
     */
    public function testRemove(Widget $widgetRes)
    {
        $res = $widgetRes->delete();

        $this->assertNotFalse($res, 'Delete request Error');

        if ($res) {
            try {
                $widget = $this->stack->instance('widget');
                $widget->getById($widgetRes->id);
            } catch(\Exception $e) {
                // exception's been throw because of the requested tag is not exist
            }
            // $tag should empty because the deletion
            $this->assertEquals(0, count($widget));
            $this->assertGreaterThan(0, count($widget->getErrors()));
            $this->assertEquals(0, count($widgetRes->getErrors()));
        }
    }
}
