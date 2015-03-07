<?php
namespace tests\codeception\unit\modules\redisman;

use Codeception\Util\Debug;
use insolita\redisman\models\PartialDataProvider;
use insolita\redisman\models\RedisModel;
use tests\codeception\unit\fixtures\RedisFixts;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

class RedisModelTest extends \Codeception\TestCase\Test
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
        $this->module->setConnection('local',1);
        $fixter=new RedisFixts();
        $fixter->createFixtures();
    }

    protected function tearDown(){
        $fixter=new RedisFixts();
        $fixter->deleteFixtures();
        parent::tearDown();
    }

    public function testValidation(){
          
         $valids=$this->validFixts();
         $invalids=$this->invalidFixts();
         foreach($valids as $data){
             $model=new RedisModel();
             $model->load($data);
             //$model->setAttributes($data);
             $check= $model->validate();
             Debug::debug($model->getAttributes());

             $this->assertTrue($check);

         }
        foreach($invalids as $data){
            $model=new RedisModel();
            $model->setAttributes($data);
            $check= $model->validate();
            Debug::debug($model->getErrors());
            Debug::debug($model->getAttributes());
            $this->assertFalse($check);
        }
    }

    public function testTypeCondBuiler(){
        $model=new RedisModel();
        $fixts=$this->typeFixts();
        foreach($fixts as $fixt){
            $model->setAttributes(['type'=>$fixt['types']]);
            $this->assertTrue($model->validate());
            $typecond=$this->getProtected('typeCondBuilder');
            $this->assertEquals($fixt['expects'], $typecond->invoke($model));
        }
    }



    public function testSearch(){
        $this->module->greedySearch=false;
        $this->module->searchMethod='SCAN';
        $model=new RedisModel();
        $model->setAttributes( ['pattern'=>'testfxt:*','perpage'=>10]);

        $this->assertTrue($model->storeFilter());
        $dp=$model->search(['page'=>1]);
        $this->assertTrue($dp instanceof PartialDataProvider);
        $models1=$dp->getModels();
        $this->assertNotEmpty($models1);
        $this->assertTrue($dp->getCount()==10);
        $this->assertTrue($dp->getCount()==count($models1));
        $total1=$dp->getTotalCount();
        $this->assertTrue($total1==15);
        Debug::debug($dp->getCount().'/'.$dp->getTotalCount());
        Debug::debug($models1);

        $dp=$model->search(['page'=>2]);
        $models2=$dp->getModels();
        $total2=$dp->getTotalCount();
        $this->assertTrue($dp instanceof PartialDataProvider);
        $this->assertNotEmpty($models2);
        $this->assertTrue($dp->getCount()==5);
        $this->assertTrue($dp->getCount()==count($models2));
        $this->assertEquals($total1, $total2);
        $this->assertNotEquals($models1, $models2);


        Debug::debug($dp->getCount().'/'.$dp->getTotalCount());
        Debug::debug($models2);

        $this->module->searchMethod='KEYS';
        $model=new RedisModel();
        $model->setAttributes( ['pattern'=>'testfxt:*','perpage'=>10]);

        $this->assertTrue($model->storeFilter());
        $dp=$model->search(['page'=>1]);
        $models1=$dp->getModels();
        $this->assertTrue($dp instanceof PartialDataProvider);
        $this->assertNotEmpty($models1);
        $this->assertTrue($dp->getCount()==10);
        $this->assertTrue($dp->getCount()==count($models1));
        $total1=$dp->getTotalCount();
        Debug::debug($dp->getCount().'/'.$dp->getTotalCount());
        Debug::debug($models1);

        $dp=$model->search(['page'=>2]);
        $models2=$dp->getModels();
        $total2=$dp->getTotalCount();
        $this->assertNotEmpty($models2);
        $this->assertTrue($dp->getCount()==5);
        $this->assertTrue($dp->getCount()==count($models2));
        $this->assertEquals($total1, $total2);
        $this->assertNotEquals($models1, $models2);


        Debug::debug($dp->getCount().'/'.$dp->getTotalCount());
        Debug::debug($models2);

    }

    public function testGreedySearch(){
        $this->module->greedySearch=true;
        $this->module->searchMethod='SCAN';
        $model=new RedisModel();
        $model->setAttributes( ['pattern'=>'testfxt:*','perpage'=>10]);

        $this->assertTrue($model->storeFilter());
        $dp=$model->search(['page'=>1]);
        $this->assertTrue($dp instanceof ArrayDataProvider);
        $models1=$dp->getModels();
        $this->assertNotEmpty($models1);
        $this->assertTrue($dp->getCount()==10);
        $this->assertTrue($dp->getCount()==count($models1));
        $total1=$dp->getTotalCount();
        Debug::debug($dp->getCount().'/'.$dp->getTotalCount());


        $dp=$model->search(['page'=>2]);
        $this->assertTrue($dp instanceof ArrayDataProvider);
        $models2=$dp->getModels();
        $total2=$dp->getTotalCount();
         $this->assertTrue($dp->getCount()==10);
        $this->assertTrue($dp->getCount()==count($models2));
        $this->assertEquals($total1, $total2);
        $this->assertNotEquals($models1, $models2);


        Debug::debug($dp->getCount().'/'.$dp->getTotalCount());
        Debug::debug($models2);

        $this->module->searchMethod='KEYS';

        $model=new RedisModel();
        $model->setAttributes( ['pattern'=>'testfxt:*','perpage'=>10]);

        $this->assertTrue($model->storeFilter());
        $dp=$model->search(['page'=>1]);
        $this->assertTrue($dp instanceof ArrayDataProvider);
        $models1=$dp->getModels();
        $this->assertNotEmpty($models1);
        $this->assertTrue($dp->getCount()==10);
        $this->assertTrue($dp->getCount()==count($models1));
        $total1=$dp->getTotalCount();
        Debug::debug($dp->getCount().'/'.$dp->getTotalCount());
        Debug::debug($models1);

        $dp=$model->search(['page'=>2]);
        $this->assertTrue($dp instanceof ArrayDataProvider);
        $models2=$dp->getModels();
        $total2=$dp->getTotalCount();
        $this->assertNotEmpty($models2);
        $this->assertTrue($dp->getCount()==10);
        $this->assertTrue($dp->getCount()==count($models2));
        $this->assertEquals($total1, $total2);
        $this->assertNotEquals($models1, $models2);


        Debug::debug($dp->getCount().'/'.$dp->getTotalCount());
        Debug::debug($models2);

    }

    public function testSearchByTypes(){
        $this->module->greedySearch=true;
        $this->module->searchMethod='SCAN';
        $model=new RedisModel();

        $model->setAttributes( ['pattern'=>'testfxt:*','type'=>['string'],'perpage'=>10]);
        $dp=$model->search(['page'=>1]);
        $models=$dp->getModels();
        $this->assertTrue(count($models)==3);
        foreach($models as $m){
            $this->assertTrue($m['type']=='string');
        }
        $model->setAttributes( ['pattern'=>'testfxt:*','type'=>['list'],'perpage'=>10]);
        $dp=$model->search(['page'=>1]);
        $models=$dp->getModels();
        $this->assertTrue(count($models)==3);
        foreach($models as $m){
            $this->assertTrue($m['type']=='list');
        }

        $model->setAttributes( ['pattern'=>'testfxt:*','type'=>['hash'],'perpage'=>10]);
        $dp=$model->search(['page'=>1]);
        $models=$dp->getModels();
        $this->assertTrue(count($models)==3);
        foreach($models as $m){
            $this->assertTrue($m['type']=='hash');
        }

        $model->setAttributes( ['pattern'=>'testfxt:*','type'=>['set'],'perpage'=>10]);
        $dp=$model->search(['page'=>1]);
        $models=$dp->getModels();
        $this->assertTrue(count($models)==3);
        foreach($models as $m){
            $this->assertTrue($m['type']=='set');
        }

        $model->setAttributes( ['pattern'=>'testfxt:*','type'=>['zset'],'perpage'=>10]);
        $dp=$model->search(['page'=>1]);
        $models=$dp->getModels();
        $this->assertTrue(count($models)==3);
        foreach($models as $m){
            $this->assertTrue($m['type']=='zset');
        }

        $model->setAttributes( ['pattern'=>'testfxt:*','type'=>['zset','set'],'perpage'=>10]);
        $dp=$model->search(['page'=>1]);
        $models=$dp->getModels();
        $this->assertTrue(count($models)==6);

        $model->setAttributes( ['pattern'=>'testfxt:*','type'=>['zset','set','list','hash'],'perpage'=>15]);
        $dp=$model->search(['page'=>1]);
        $models=$dp->getModels();
        $this->assertTrue(count($models)==12);
        foreach($models as $m){
            $this->assertTrue($m['type']!='string');
        }
    }

    public function testSearchId(){
        $this->module->greedySearch=false;
        $this->module->setConnection('local',1);
        $model=new RedisModel();
        $model->setAttributes( ['pattern'=>'testfxt:*','type'=>['string','list'],'perpage'=>30]);
        $model->validate();
        $func=$this->getProtected('getSearchId');
        $searchId=$func->invoke($model);
        Debug::debug($searchId);
        $expect='testfxt:*:stringlist:30:local:1:';
        $this->assertEquals($expect, $searchId);

        $this->module->greedySearch=true;
        $model->setAttributes( ['pattern'=>'*:*','type'=>['set','zset'],'perpage'=>20]);
        $model->validate();
        $searchId=$func->invoke($model);
        Debug::debug($searchId);
        $expect='*:*:setzset:20:local:1:1';
        $this->assertEquals($expect, $searchId);

        $this->module->setConnection('remote1',0);
        $model=new RedisModel();
        $model->setAttributes( ['pattern'=>'*fxt*','perpage'=>30]);
        $model->validate();
        $func=$this->getProtected('getSearchId');
        $searchId=$func->invoke($model);
        Debug::debug($searchId);
        $expect='*fxt*:stringsetlistzsethash:30:remote1:0:1';
        $this->assertEquals($expect, $searchId);
    }



    protected function typeFixts(){
        return [
             ['types'=>['string'],'expects'=>'tp == "string"'],
             ['types'=>['hash','string'],'expects'=>'tp == "hash" or tp == "string"'],
            ['types'=>['hash','string','list'],'expects'=>'tp == "hash" or tp == "string" or tp == "list"'],
            ['types'=>['list','set','zset'],'expects'=>'tp == "list" or tp == "set" or tp == "zset"'],
            ['types'=>['list','set','zset','hash'],'expects'=>'tp == "list" or tp == "set" or tp == "zset" or tp == "hash"'],
            ['types'=>['list','set','zset','hash','string'],'expects'=>'1==1'],
        ];
    }
    protected function validFixts(){
        return [
            ['pattern'=>'*:*','type'=>['list','hash']],
            ['pattern'=>'*'],
            ['pattern'=>'~','perpage'=>'','type'=>''],
            ['pattern'=>'~','perpage'=>'','type'=>'','encache'=>true],
            ['pattern'=>'~','perpage'=>'','type'=>'','encache'=>false],
            ['pattern'=>'$R%^BT&*','perpage'=>100,'type'=>['string']],
            ['pattern'=>'<script>alert(hi)</script>','perpage'=>100,'type'=>['string']],
            ['perpage'=>10],
            ['pattern'=>'мама мыла раму','perpage'=>50,'type'=>['string']],
            ['pattern'=>'"','perpage'=>90,'type'=>['string']],
            ['pattern'=>"'",'perpage'=>657,'type'=>['string']],
            ['pattern'=>'*.*','type'=>['list','hash']],
            [],
            ['pattern'=>'Mail','perpage'=>34,'type'=>['zset']]

        ];
    }

    protected function invalidFixts(){
        return [
            ['pattern'=>'~','perpage'=>'','type'=>'','encache'=>'boo'],
            ['pattern'=>'*.*','perpage'=>222220,'type'=>['string']],
            ['pattern'=>'*.*','type'=>'string'],
            ['type'=>['boo']],
            ['type'=>['list','string','set','hset','foo']],
            ['type'=>['list','string','set','hash','zset','hash','zset']],
        ];
    }

    protected  function getProtected($name) {
        $class = new \ReflectionClass('insolita\redisman\models\RedisModel');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}