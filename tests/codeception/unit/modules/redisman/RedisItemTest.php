<?php
namespace tests\codeception\unit\modules\redisman;

use Codeception\Util\Debug;
use insolita\redisman\models\RedisItem;
use tests\codeception\unit\fixtures\RedisFixts;
use yii\helpers\Html;

class RedisItemTest extends \Codeception\TestCase\Test
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

        $fixter = new RedisFixts();
        $fixter->createFixtures();

        $this->module = \Yii::$app->getModule('redisman');
        $this->module->setConnection('local', 1);

    }

    protected function tearDown()
    {
        $fixter = new RedisFixts();
        $fixter->deleteFixtures();
        parent::tearDown();
    }


    public function testGetValue()
    {
        $this->assertEquals('local',$this->module->getCurrentConn());
        $this->assertNotInstanceOf('insolita\redisman\components\PhpredisConnection', $this->module->getConnection());
        $model = new RedisItem();
        $model->setAttributes(['key' => 'tfx_string', 'type' => 'string']);
        $this->assertTrue($model->validate());
        $val = $model->getValue();
        $this->assertEquals('somestringval', $val);

        $model = new RedisItem();
        $model->setAttributes(['key' => 'tfx_list', 'type' => 'list']);
        $this->assertTrue($model->validate());
        $val = $model->getValue();
        $this->assertTrue(is_array($val));
        $this->assertEquals(['someval1', 'someval2', 'someval3'], $val);

        $model = new RedisItem();
        $model->setAttributes(['key' => 'tfx_set', 'type' => 'set']);
        $this->assertTrue($model->validate());
        $val = $model->getValue();
        $this->assertTrue(is_array($val));
        $this->assertTrue(in_array('someval4', $val));
        $this->assertTrue(in_array('someval1', $val));
        $this->assertTrue(in_array('someval2', $val));
        $this->assertTrue(in_array('someval3', $val));


        $model = new RedisItem();
        $model->setAttributes(['key' => 'testfxt:3:hash']);
        $this->assertTrue($model->validate());
        $val = $model->getValue();
        Debug::debug($model->getAttributes());
        Debug::debug($val);
        Debug::debug($model->findValue()->getAttributes());
        Debug::debug($this->module->executeCommand('HGETALL', ['testfxt:3:hash']));
        $this->assertTrue(is_array($val));
        $this->assertEquals($this->module->executeCommand('HGETALL', ['testfxt:3:hash']), $val);

        $model = new RedisItem();
        $model->setAttributes(['key' => 'tfx_zset', 'type' => 'zset']);
        $this->assertTrue($model->validate());
        $val = $model->getValue();
        $this->assertTrue(is_array($val));
        $this->assertEquals(['someval2', 3, 'someval1', 4, 'someval3', 8], $val);
    }


    /**
     * @depends testGetValue
     **/
    public function testValueDataProvider()
    {
        $model = new RedisItem();
        $model->setAttributes(['key' => 'testfxt:3:hash', 'type' => 'hash', 'value'=>['hashfield1' => 'hashval1', 'hashfield2' => 'hashval2']]);

        $dp=$model->valueDataProvider();
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $dp);
        $this->assertTrue($dp->getCount()==2);
        $this->assertTrue($dp->getTotalCount()==2);

        $model = new RedisItem();
        $model->setAttributes(['key' => 'testfxt:3:zset', 'type' => 'hash', 'value'=>['someval2' => 3, 'someval1' => 4, 'someval3' => 8]]);

        $dp=$model->valueDataProvider();
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $dp);
        $this->assertTrue($dp->getCount()==3);
        $this->assertTrue($dp->getTotalCount()==3);

    }
    /**
     * @depends testGetValue
     **/
    public function testFind()
    {
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
        $this->assertNotEmpty($res);
        $this->assertEquals(3, $res->size);
        $this->assertEquals($res->value, ['someval1', 'someval2', 'someval3']);
        $this->assertEquals($res->formatvalue, "someval1\r\nsomeval2\r\nsomeval3");

        $res = RedisItem::find('tfx_set')->findValue();
        $this->assertNotEmpty($res);
        $this->assertEquals(4, $res->size);
        $this->assertTrue(in_array('someval4', $res->value));
        $this->assertTrue(in_array('someval1', $res->value));
        $this->assertEquals($res->formatvalue, implode("\r\n", $res->value));

        $res = RedisItem::find('tfx_hash')->findValue();
        $this->assertNotEmpty($res);
        $this->assertEquals(1, $res->size);
        $this->assertEquals(['hashfield' => 'hashval'], $res->value);
        $this->assertAttributeInstanceOf('\yii\data\ArrayDataProvider', 'formatvalue', $res);

        $res = RedisItem::find('tfx_zset')->findValue();
        $this->assertNotEmpty($res);
        $this->assertEquals(3, $res->size);
        $this->assertEquals(['someval2' => 3, 'someval1' => 4, 'someval3' => 8], $res->value);
        $this->assertAttributeInstanceOf('\yii\data\ArrayDataProvider', 'formatvalue', $res);


        $this->setExpectedException('yii\web\NotFoundHttpException');
        $res = RedisItem::find('iugigigi giu')->findValue();
    }

    public function testMoveScenario()
    {
        $model = new RedisItem();
        $model->scenario = 'move';
        $model->setAttributes(['key' => 'tfx_string', 'db' => 2]);
        $this->assertTrue($model->validate());

        $model->setAttributes(['key' => 'tfx_string', 'db' => 1]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('db'));

        $model->setAttributes(['key' => 'T^R$%&^R^VTYFFt', 'db' => 1]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('key'));

        $model->setAttributes(['key' => 'tfx_string', 'db' => 999]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('db'));

        $model->setAttributes(['key' => 'tfx_string', 'db' => null]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('db'));

        $model->setAttributes(['key' => null, 'db' => 4]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('key'));
    }

    public function testPersistScenario()
    {
        $model = new RedisItem();
        $model->scenario = 'persist';
        $model->setAttributes(['key' => 'tfx_string', 'ttl' => 100]);
        $this->assertTrue($model->validate());

        $model->setAttributes(['key' => 'tfx_string', 'ttl' => 100500100500]);
        $this->assertTrue($model->validate());

        $model->setAttributes(['key' => 'tfx_string', 'ttl' => 100500100500100500]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('ttl'));

        $model->setAttributes(['key' => 'tfx_string', 'ttl' => -9]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('ttl'));

        $model->setAttributes(['key' => 'T^R$%&^R^VTYFFt', 'ttl' => 1]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('key'));

        $model->setAttributes(['key' => 'tfx_string', 'ttl' => null]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('ttl'));


        $model->setAttributes(['key' => null, 'ttl' => 100]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('key'));
    }

    public function testCreateScenario()
    {
        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'ttl' => 100, 'type'=>'string','formatvalue'=>'ffhirifrihfirhfihri']);
        $this->assertTrue($model->validate());

        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'ttl' => 100,'formatvalue'=>'ffhirifrihfirhfihri']);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('type'));


        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'type'=>'set','formatvalue'=>"hhr \r\n jgjrj \r\n", 'ttl' => 100500100500]);
        $this->assertTrue($model->validate());



        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string555', 'ttl' => 100, 'type'=>'string','formatvalue'=>['ffhirifrih','firhfihri']]);
         $this->assertFalse($model->validate());
        Debug::debug($model->getErrors());
        $this->assertTrue($model->hasErrors('formatvalue'));

        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'type'=>'list','formatvalue'=>['ffhirifrihfirhfihri','frfrfrfrjjr','frfrfrfrjjr']]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('formatvalue'));

        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'type'=>'string','formatvalue'=>['ffhirifrihfirhfihri','frfrfrfrjjr','frfrfrfrjjr']]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('formatvalue'));

        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'type'=>'hash','formatvalue'=>['ffhirifrihfirhfihri','frfrfrfrjjr','frfrfrfrjjr'], 'ttl' => 100500100500]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('formatvalue'));


        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'type'=>'hash','formatvalue'=>[['field'=>'frfrf','value'=>'frfr'],['field'=>'44r','value'=>'frfr4rr']]]);
        $this->assertTrue($model->validate());

        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'type'=>'zset','formatvalue'=>[['field'=>'frfrf','score'=>'434'],['field'=>'44r','score'=>'33']]]);
        $this->assertTrue($model->validate());

        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'type'=>'hash','formatvalue'=>[['field'=>'frfrf','score'=>'frfr'],['field'=>'44r','value'=>'frfr4rr']]]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('formatvalue'));

        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'type'=>'zset','formatvalue'=>[['field'=>'frfrf','score'=>'frfr'],['field'=>'44r','value'=>'frfr4rr']]]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('formatvalue'));

        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'ttl' => 100500100500100500, 'type'=>'list','formatvalue'=>'uiugi uiuuu']);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('ttl'));


        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'ttl' => -9, 'type'=>'list','formatvalue'=>'uiugi uiuuu']);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('ttl'));
        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string', 'ttl' => 1, 'type'=>'set','formatvalue'=>'uiugi uiuuu']);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('key'));

        $model = new RedisItem();
        $model->scenario = 'create';
        $model->setAttributes(['key' => 'tfx_string999', 'ttl' => null,  'type'=>'list','formatvalue'=>'uiugi uiuuu']);
        $this->assertTrue($model->validate());

    }

    public function testDeleteScenario()
    {
        $model = new RedisItem();
        $model->scenario = 'delete';
        $model->setAttributes(['key' => 'tfx_string']);
        $this->assertTrue($model->validate());

        $model->setAttributes(['key' => 'T^R$%&^R^VTYFFt']);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('key'));

        $model->setAttributes(['db' => 1, 'ttl' => 500, 'key' => null]);
        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('key'));

    }

    public function testPersist()
    {
        $model = RedisItem::find('tfx_string');
        $this->assertEquals(-1, $model->ttl);
        $this->assertEquals(-1, $this->module->executeCommand('TTL', ['tfx_string']));
        $model->scenario = 'persist';
        $model->setAttributes(['ttl' => '100500']);
        $this->assertTrue($model->validate());
        $model->persist();
        $this->assertNotEquals(-1, $this->module->executeCommand('TTL', ['tfx_string']));
        $this->assertLessThanOrEqual(100500, $this->module->executeCommand('TTL', ['tfx_string']));
        $model->setAttributes(['ttl' => '-1']);
        $this->assertTrue($model->validate());
        $model->persist();
        $this->assertEquals(-1, $model->ttl);
        $this->assertEquals(-1, $this->module->executeCommand('TTL', ['tfx_string']));
    }

    public function testDelete()
    {
        $model = new RedisItem();
        $model->setAttributes(['key' => 'tfx_string']);
        $model->scenario = 'delete';
        $this->assertTrue($model->validate());
        $model->delete();
        $this->assertEquals(0, $this->module->executeCommand('EXISTS', ['tfx_string']));
    }

    public function testMove()
    {
        $this->module->setConnection('local', 3);
        $this->assertEquals(0, $this->module->executeCommand('EXISTS', ['tfx_string']));
        $this->module->setConnection('local', 1);
        $model = new RedisItem();
        $model->setAttributes(['key' => 'tfx_string', 'db' => 3]);
        $model->scenario = 'move';
        $this->assertTrue($model->validate());
        $model->move();
        $this->assertEquals(0, $this->module->executeCommand('EXISTS', ['tfx_string']));
        $this->module->setConnection('local', 3);
        $this->assertEquals(1, $this->module->executeCommand('EXISTS', ['tfx_string']));
        $this->module->executeCommand('DEL', ['tfx_string']);
    }

    public function testStressMove()
    {
        $this->module->setConnection('local', 1);
        $keys=$this->module->executeCommand('KEYS', ['stress:*']);

        $this->module->setConnection('local', 3);
        foreach($keys as $key){
            $this->assertEquals(0, $this->module->executeCommand('EXISTS', [$key]));
        }

        $this->module->setConnection('local', 1);

        foreach($keys as $key){
            $model = new RedisItem();
            $model->setAttributes(['key' => $key, 'db' => 3]);
            $model->scenario = 'move';
            $this->assertTrue($model->validate());
            $model->move();
            $this->assertEquals(0, $this->module->executeCommand('EXISTS', [$key]));
        }


        $this->module->setConnection('local', 3);
        foreach($keys as $key){
            $this->assertEquals(1, $this->module->executeCommand('EXISTS', [$key]));
            $this->module->executeCommand('DEL', [$key]);
        }

    }

    public function testRemfield(){
        $model = new RedisItem();
        $model->scenario = 'remfield';
        $model->setAttributes(['key' => 'tfx_hashlong']);
        $this->assertFalse($model->validate());

        $model = new RedisItem();
        $model->setAttributes(['key' => 'tfx_abyrvalg','field'=>'pararam']);
        $this->assertFalse($model->validate());

        $model = new RedisItem();
        $model->scenario = 'remfield';
        $model->setAttributes(['key' => 'tfx_hashlong','field'=>'pararam']);
        $this->assertTrue($model->validate());
        $model->remfield();

        $model = new RedisItem();
        $model->scenario = 'remfield';
        $model->setAttributes(['key' => urlencode('tfx_hashlong'),'field'=>Html::encode('pararam')]);
        $this->assertTrue($model->validate());
        $model->remfield();

        $model = new RedisItem();
        $model->scenario = 'remfield';
        $model->setAttributes(['key' => 'tfx_hashlong','field'=>'hashfield3']);
        $this->assertTrue($model->validate());
        $model->remfield();
        $this->assertEquals(0, $this->module->executeCommand('HEXISTS', ['tfx_hashlong','hashfield3']));

        $stress=$this->module->executeCommand('HKEYS', ['stress:hash']);
        Debug::debug($this->module->executeCommand('HGETALL', ['stress:hash']));
        foreach($stress as $sfield){
            Debug::debug($sfield);
            $model = new RedisItem();
            $model->scenario = 'remfield';
            $model->setAttributes(['key' => urlencode('stress:hash'),'field'=>Html::encode($sfield)]);
            $this->assertTrue($model->validate());
            $model->remfield();
            $this->assertEquals(0, $this->module->executeCommand('HEXISTS', ['stress:hash',$sfield]));
        }
        Debug::debug($this->module->executeCommand('HGETALL', ['stress:hash']));

        $this->assertEquals(3,$this->module->executeCommand('ZCARD', ['tfx_zset']));
        $model = new RedisItem();
        $model->scenario = 'remfield';
        $model->setAttributes(['key' => urlencode('tfx_zset'),'field'=>Html::encode('someval2')]);
        $this->assertTrue($model->validate());
        $model->remfield();
        $this->assertEquals(2,$this->module->executeCommand('ZCARD', ['tfx_zset']));

    }
    public function testUpdate(){
        $this->module->setConnection('local', 1);
        $keys=$this->module->executeCommand('KEYS', ['*']);

        foreach($keys as $key){
            $model = RedisItem::find($key)->findValue();
            $model->scenario='update';
            if($model->type=='string'){
                $model->setAttributes(['formatvalue'=>$model->formatvalue.' and new appendix']);
                $this->assertTrue($model->validate());
                $model->update();
                $val=$this->module->executeCommand('GET',[$model->key]);
                $this->assertContains('new appendix', $val);
                Debug::debug($val);
            }elseif($model->type=='list' || $model->type=='set'){
                $model->setAttributes(['formatvalue'=>$model->formatvalue."\r\n newitem1\r\n Other new Item \r\n AbyrValg"]);
                $this->assertTrue($model->validate());
                $model->update();
                $val=$model->getValue();
                $this->assertTrue(in_array('newitem1',$val));
                $this->assertTrue(in_array('AbyrValg',$val));
                $this->assertTrue(in_array('Other new Item',$val));
                Debug::debug($val);
            }elseif($model->type=='hash'){
                $change=[];
                 foreach($model->value as $k=>$v){
                    $change[]=['field'=>$k,'value'=>strrev($v).'_upd'];
                }
                $model->setAttributes(['formatvalue'=>$change]);
                $this->assertTrue($model->validate());
                $model->update();
                $model->findValue();
                foreach($model->value as $k=>$v){
                    $this->assertTrue(strpos($v,'_upd')!==0);
                }
             }elseif($model->type=='zset'){
                $change=[];
                foreach($model->value as  $k=>$v){
                    $change[]=['field'=>$k,'score'=>4];
                }
                $model->setAttributes(['formatvalue'=>$change]);
                $this->assertTrue($model->validate());
                $model->update();
                $model->findValue();
                foreach($model->value as $k=>$v){
                    $this->assertTrue($v==4);
                }
             }

        }
    }

    public function testCreate(){
        $model=new RedisItem();
        $model->scenario='create';
        $model->setAttributes(['key'=>'newstringkey','ttl'=>2000,'formatvalue'=>'h ehfif ierireh ei','type'=>'string']);
        $this->assertTrue($model->validate());
        $model->create();
        $this->assertEquals(1,$this->module->executeCommand('EXISTS',['newstringkey']));
        $tmodel=RedisItem::find('newstringkey')->findValue();
        $this->assertLessThanOrEqual(2000,$tmodel->ttl);
        $this->assertNotEquals(-1, $tmodel->ttl);
    }

    protected function getProtected($name)
    {
        $class = new \ReflectionClass('insolita\redisman\models\RedisItem');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
    /**
     * public function testAddGetkey()
     * {
     * $conn=$this->module->getConnection();
     * $model = new RedisItem();
     * $model->save(Redisman::REDIS_STRING, 'test1','test1val');
     * $type=$conn->type('test1');
     * Debug::debug($type);
     * $this->assertTrue($type==Redisman::REDIS_STRING);
     * $val=$model->getValue('test1');
     * $this->assertTrue($val=='test1val');
     * Debug::debug($this->module->i18nType('test1'));
     * $this->assertTrue($this->module->i18nType('test1')=='Строка');
     * $conn->del('test1');
     *
     * $model->save(Redisman::REDIS_LIST, 'test1',['test1val']);
     * $val=$model->getValue('test1');
     * $this->assertTrue($val==['test1val']);
     * $this->assertTrue($this->module->i18nType('test1')=='Список');
     * $conn->del('test1');
     *
     * $model->save(Redisman::REDIS_LIST, 'test1',['test1val','test2val','test3val']);
     * $val=$model->getValue('test1');
     * Debug::debug($val);
     * $this->assertTrue($val==['test1val','test2val','test3val']);
     * $this->assertTrue($this->module->i18nType('test1')=='Список');
     * $conn->del('test1');
     *
     * $model->save(Redisman::REDIS_HASH, 'test1',['test1field','test1val']);
     * $val=$model->getValue('test1');
     * Debug::debug($val);
     * $this->assertTrue($val==['test1field','test1val']);
     * $this->assertTrue($this->module->i18nType('test1')=='Хеш');
     * $conn->del('test1');
     *
     * $model->save(Redisman::REDIS_HASH, 'test1',['test1field','test1val','test2field','test2val']);
     * $val=$model->getValue('test1');
     * Debug::debug($val);
     * $this->assertTrue($val==['test1field','test1val','test2field','test2val']);
     * $this->assertTrue($this->module->i18nType('test1')=='Хеш');
     * $conn->del('test1');
     *
     * $model->save(Redisman::REDIS_SET, 'test1',['test1val']);
     * $val=$model->getValue('test1');
     * Debug::debug($val);
     * $this->assertTrue($val==['test1val']);
     * $this->assertTrue($this->module->i18nType('test1')=='Набор');
     * $conn->del('test1');
     *
     * $model->save(Redisman::REDIS_SET, 'test1',['test1val','test2val']);
     * $val=$model->getValue('test1');
     * Debug::debug($val);
     * $this->assertTrue($val==['test2val', 'test1val']);
     * $this->assertTrue($this->module->i18nType('test1')=='Набор');
     * $conn->del('test1');
     *
     * $model->save(Redisman::REDIS_ZSET, 'test1',[1,'test1val']);
     * $val=$model->getValue('test1');
     * Debug::debug($val);
     * $this->assertTrue($val==['test1val',1]);
     * $this->assertTrue($this->module->i18nType('test1')=='Множество');
     * $conn->del('test1');
     *
     * $model->save(Redisman::REDIS_ZSET, 'test2',[1,'test1val', 2,'iufri', 5, 'ifurirf']);
     * $val=$model->getValue('test2');
     * Debug::debug($val);
     * $this->assertTrue(count($val)==6);
     * $this->assertTrue($this->module->i18nType('test2')=='Множество');
     * $conn->del('test1');
     *
     * }
     */


}