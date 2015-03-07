<?php
namespace tests\codeception\unit\modules\redisman;


use Codeception\Util\Debug;
use insolita\redisman\models\RedisItem;

class PhpredisTest extends \Codeception\TestCase\Test
{
    /**
     * @var \tests\codeception\unit\UnitTester
     */
    protected $tester;

    /**
     * @var \insolita\redisman\Redisman $module
     **/
    private $module;

    protected function setUp()
    {
        parent::setUp();
        $this->module=\Yii::$app->getModule('redisman');
         $this->module->setConnection('localnat',0);
    }

    protected function tearDown(){
        parent::tearDown();
    }

    public function testFind()
    {
         $this->assertEquals('localnat',$this->module->getCurrentConn());
        $this->assertInstanceOf('insolita\redisman\components\NativeConnection', $this->module->getConnection());
        $res = RedisItem::find('tfx_string');
        $this->assertNotEmpty($res);
        $this->assertEquals($res->value, null);
        $this->assertEquals($res->formatvalue, null);
        $this->assertEquals($res->type, 'string');
        $this->assertEquals($res->ttl, -1);

        $res = RedisItem::find('tfx_string')->findValue();
        $this->assertNotEmpty($res);
        $this->assertEquals(13, $res->size);
        $this->assertEquals($res->value, 'somestringval');
        $this->assertEquals($res->formatvalue, 'somestringval');

        $res = RedisItem::find('tfx_list')->findValue();
        Debug::debug($res->getValue());

        Debug::debug($res->getAttributes());
        $this->assertNotEmpty($res);
        /*$this->assertEquals(3, $res->size);
        $this->assertEquals($res->value, ['someval1', 'someval2', 'someval3']);
        $this->assertEquals($res->formatvalue, "someval1\r\nsomeval2\r\nsomeval3");*/

        $res = RedisItem::find('tfx_set')->findValue();
        $this->assertNotEmpty($res);
        Debug::debug($res->getValue());
        Debug::debug($res->getAttributes());
 /*        $this->assertEquals(4, $res->size);
        $this->assertTrue(in_array('someval4', $res->value));
        $this->assertTrue(in_array('someval1', $res->value));
        $this->assertEquals($res->formatvalue, implode("\r\n", $res->value));
*/
        $res = RedisItem::find('tfx_hash')->findValue();
        Debug::debug($res->getValue());
      //  Debug::debug($res->getAttributes());
        /*$this->assertNotEmpty($res);
        $this->assertEquals(1, $res->size);
        $this->assertEquals(['hashfield' => 'hashval'], $res->value);
        $this->assertAttributeInstanceOf('\yii\data\ArrayDataProvider', 'formatvalue', $res);*/

        $res = RedisItem::find('tfx_zset')->findValue();
        Debug::debug($res->getValue());
       // Debug::debug($res->getAttributes());
        $this->assertNotEmpty($res);
       /* $this->assertEquals(3, $res->size);
        $this->assertEquals(['someval2' => 3, 'someval1' => 4, 'someval3' => 8], $res->value);
        $this->assertAttributeInstanceOf('\yii\data\ArrayDataProvider', 'formatvalue', $res);

*/
        $this->setExpectedException('yii\web\NotFoundHttpException');
        $res = RedisItem::find('iugigigi giu')->findValue();
    }


}