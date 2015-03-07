<?php
namespace tests\codeception\unit\modules\redisman;
use tests\codeception\unit\fixtures\RedisFixts;
use Codeception\Util\Debug;


class AddGetTest extends \Codeception\TestCase\Test
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
        $this->module->setConnection('local',0);
    }

    protected function tearDown(){
        parent::tearDown();
    }


    public function testRedisKeysWork(){
        $conn=$this->module->getConnection();
        $conn->set('stest1','stest1val');
        $val=$conn->get('stest1');
        Debug::debug($val);
        $this->assertTrue($val=='stest1val');
        $conn->del('stest1');
        $val=$conn->get('stest1');
        $this->assertEmpty($val);


        $conn->executeCommand('RPUSH',['slist1','A']);
        $conn->rpush('slist1','B');
        $conn->rpush('slist1','C','D','E');
        $val=$conn->lrange('slist1',0,-1);
        $this->assertTrue(is_array($val));
        $this->assertTrue(count($val)==5);
        $this->assertTrue(in_array('B',$val));
        $conn->del('slist1');

        $vals=['k1','k2','k3'];
        $key='slist1';
        array_unshift($vals, $key);
        $conn->executeCommand('RPUSH',$vals);
        $val=$conn->lrange('slist1',0,-1);
        Debug::debug($val);
        $this->assertEquals($val,['k1','k2','k3']);
        $conn->del('slist1');


        $conn->hset('shash1','Gr1', 'd1');
        $conn->hset('shash1','Gr2', 'd2');
        $conn->hmset('shash1','Gr3', 'd3','Gr4', 'd4','Gr5', 'd5');

        $val=$conn->hgetall('shash1');
        Debug::debug($val);
        $this->assertTrue(in_array('d3',$val));
        $val=$conn->hget('shash1', 'Gr3');
        $this->assertTrue($val=='d3');
        Debug::debug($val);
        $conn->del('shash1');

        $conn->sadd('setik1','Gr1');
        $conn->executeCommand('SADD', ['setik1','Gr2']);
        $conn->sadd('setik1','Gr3','Gr4', 'Gr2');
        $conn->executeCommand('SADD', ['setik1','Gr6','Gr7','Gr5']);
        $val=$conn->smembers('setik1');
        Debug::debug($val);
        $this->assertTrue(is_array($val));
        $this->assertTrue(count($val)==7);
        $conn->del('setik1');

        $conn->zadd('zsetik1',1,'Gr1');
        $conn->zadd('zsetik1',4,'Gr2');
        $conn->zadd('zsetik1',2,'Gr3', 3, 'Gr4', 1,'Gr5', 2, 'Gr2');
        $val=$conn->zrange('zsetik1', 0,-1,'withscores');
        $conn->del('zsetik1');
        Debug::debug($val);

        $val=$conn->zrange('zaddik', 0,-1,'withscores');
        Debug::debug($val);

    }


     public function testFixturizer(){

         $fixter=new RedisFixts();
         $fixter->createFixtures();
         $this->module->setConnection('local',1);

         $this->assertEquals(1,$this->module->executeCommand('EXISTS',['tfx_string']));
         $this->assertEquals(1,$this->module->executeCommand('EXISTS',['tfx_hash']));
         $this->assertEquals(1,$this->module->executeCommand('EXISTS',['tfx_list']));
         $this->assertEquals(1,$this->module->executeCommand('EXISTS',['tfx_set']));
         $this->assertEquals(1,$this->module->executeCommand('EXISTS',['tfx_zset']));
         $this->assertEquals(-1,$this->module->executeCommand('TTL',['tfx_hash']));
         $this->assertNotEquals(-1,$this->module->executeCommand('TTL',['tfx_stringexp']));
         $this->assertNotEquals(-1,$this->module->executeCommand('TTL',['tfx_listexp']));


          $fixter->deleteFixtures();


         $this->assertEquals(0,$this->module->executeCommand('EXISTS',['tfx_string']));
         $this->assertEquals(0,$this->module->executeCommand('EXISTS',['tfx_hash']));
         $this->assertEquals(0,$this->module->executeCommand('EXISTS',['tfx_list']));
         $this->assertEquals(0,$this->module->executeCommand('EXISTS',['tfx_set']));
         $this->assertEquals(0,$this->module->executeCommand('EXISTS',['tfx_zset']));
     }

     public function testDiffer(){
         $arr1=['item1','item2','item3','item4'];
         $arr2=['item6','item2','item4','item5','item7','item8'];
         $arr3=['item6','item2','item8'];
         Debug::debug(array_diff_assoc($arr1,$arr2));
         Debug::debug(array_diff_assoc($arr2,$arr1));
         Debug::debug(array_diff_assoc($arr2,$arr3));
         Debug::debug(array_diff_assoc($arr3,$arr2));
     }
}