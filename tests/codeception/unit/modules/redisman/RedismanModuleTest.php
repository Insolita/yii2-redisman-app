<?php
namespace tests\codeception\unit\modules\redisman;


use app\models\User;
use yii\codeception\TestCase;
use tests\codeception\unit\UnitTester;
use yii\redis\Connection;
use Codeception\Util\Debug;

class RedismanModuleTest extends TestCase
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function setUp()
    {
        parent::setUp();
        $user=User::findByUsername('admin');
        \Yii::$app->user->login($user);
    }

    protected function tearDown(){
        parent::tearDown();
    }

    // tests
    public function testConnectionCreate()
    {
        $this->tester->wantTo('Test creating redis connection with different config');
       $conn=\Yii::createObject([
               'class' => 'yii\redis\Connection',
               'hostname' => '82.146.35.148',
               'password'=>'sollypolly123wpw03RLQwa_Qwa',
               'port' => 6379,
               'database' => 0,
           ]);

        $this->assertTrue($conn instanceof Connection);
        $info=$conn->info();
        $this->assertTrue(!empty($info));
        $this->assertTrue($conn->isActive);
        $bind=$conn->executeCommand('CONFIG',['GET', 'bind']);
        $this->assertTrue($bind[1]=='0.0.0.0');
        $conn->close();

        $conn=\Yii::createObject([
                'class' => 'yii\redis\Connection'
            ]);

        $this->assertTrue($conn instanceof Connection);
        $info=$conn->info();
        $this->assertTrue(!empty($info));
        $this->assertTrue($conn->isActive);
        $bind=$conn->executeCommand('CONFIG',['GET', 'bind']);
        $this->assertTrue($bind[1]=='127.0.0.1');
        $conn->close();


        $conn=\Yii::createObject([
                'class' => 'yii\redis\Connection',
                'port' => 6379,
                'database' => 0,
            ]);

        $this->assertTrue($conn instanceof Connection);
        $info=$conn->info();
        $this->assertTrue(!empty($info));
        $this->assertTrue($conn->isActive);
        $bind=$conn->executeCommand('CONFIG',['GET', 'bind']);
        $this->assertTrue($bind[1]=='127.0.0.1');
        $conn->close();

        $this->setExpectedException('\yii\db\Exception');
        $conn=\Yii::createObject([
                'class' => 'yii\redis\Connection',
                'port' => 6379,
                'database' => 324,
            ]);
        $info=$conn->info();

        $this->setExpectedException('\yii\db\Exception');
        $conn=\Yii::createObject([
                'class' => 'yii\redis\Connection',
                'hostname' => '82.146.35.148',
                'port' => 6379,
                'database' => 0,
            ]);
        $info=$conn->info();

    }

    /**
     * @depends testConnectionCreate
     **/
    public function testConnection()
    {

        $this->tester->wantTo('Test creating redis connection by Module');
        /**
         * @var \insolita\redisman\Redisman $module
        **/
        $module=\Yii::$app->getModule('redisman');
        $conn=$module->getConnection();
        $this->assertTrue($conn instanceof Connection);

        $info=$conn->info();

        $this->assertTrue(!empty($info));
        $this->assertTrue($conn->isActive);
        $bind=$conn->executeCommand('CONFIG',['GET', 'bind']);
        $this->assertTrue($bind[1]=='127.0.0.1');
    }

    /**
     * @depends testConnection
    **/
    public function testConnectionSwitch()
    {
        $this->tester->wantTo('Test switching connections');
        /**
         * @var \insolita\redisman\Redisman $module
         **/
        $sescon_before=\Yii::$app->session->get('RedisManager_conCurrent');
        $sesdb_before=\Yii::$app->session->get('RedisManager_dbCurrent');
        $this->assertEmpty($sesdb_before);
        $this->assertEmpty($sescon_before);
        Debug::debug($_SESSION);
        $module=\Yii::$app->getModule('redisman');
        $conn=$module->getConnection();
        $this->assertTrue($conn instanceof Connection);
        $this->assertTrue($conn->database==0);

        $sescon=\Yii::$app->session->get('RedisManager_conCurrent');
        $sesdb=\Yii::$app->session->get('RedisManager_dbCurrent');
        $this->assertEmpty($sescon);
        $this->assertEmpty($sesdb);
        Debug::debug($_SESSION);

        $this->assertTrue($module->getCurrentDb()==0);
        $this->assertTrue($module->getCurrentConn()==$module->defRedis);

        $bind=$conn->executeCommand('CONFIG',['GET', 'bind']);
        $this->assertTrue($bind[1]=='127.0.0.1');

        $conn=$module->setConnection('remote1', 1);
        $this->assertTrue($conn instanceof Connection);
        $bind=$conn->executeCommand('CONFIG',['GET', 'bind']);
        $this->assertTrue($bind[1]=='0.0.0.0');
        $this->assertTrue($conn->isActive);
        $this->assertTrue($module->getCurrentDb()==1);

        $sescon_after=\Yii::$app->session->get('RedisManager_conCurrent');
        $sesdb_after=\Yii::$app->session->get('RedisManager_dbCurrent');
        $this->assertTrue($sesdb_after==$module->getCurrentDb());
        $this->assertTrue($sescon_after==$module->getCurrentConn());
        Debug::debug($_SESSION);

        $conn=$module->setConnection('local', 99);
        $this->assertTrue($conn instanceof Connection);
        $this->assertTrue($module->getCurrentConn()=='local');
        $this->assertTrue($module->getCurrentDb()==0);
        $sesdb=\Yii::$app->session->get('RedisManager_dbCurrent');
        $this->assertTrue($sesdb==0);

        $conn=$module->setConnection('local', 3);
        $this->assertTrue($conn instanceof Connection);
        $this->assertTrue($module->getCurrentConn()=='local');
        $this->assertTrue($module->getCurrentDb()==3);
        $sesdb=\Yii::$app->session->get('RedisManager_dbCurrent');
        $this->assertTrue($sesdb==3);
     }


    /**
     * @depends testConnection
     **/
    public function testInfo(){
        /**
         * @var \insolita\redisman\Redisman $module
         **/
        $module=\Yii::$app->getModule('redisman');
        $module->setConnection('remote1', 1);
        $infoex=$module->dbInfo();

        $this->assertNotEmpty($infoex);
        $this->assertArrayHasKey('CPU',$infoex);
        $this->assertNotEmpty($infoex['CPU']);
        $this->assertArrayHasKey('Memory',$infoex);
        $this->assertNotEmpty($infoex['Memory']);
        $this->assertArrayHasKey('Server',$infoex);
        $this->assertNotEmpty($infoex['Server']);
        $this->assertArrayHasKey('Clients',$infoex);
        $this->assertNotEmpty($infoex['Clients']);
        $this->assertArrayHasKey('Persistence',$infoex);
        $this->assertNotEmpty($infoex['Persistence']);
        $this->assertArrayHasKey('Stats',$infoex);
        $this->assertNotEmpty($infoex['Stats']);
        //Debug::debug($infoex);

    }

    /**
     * @depends testConnection
     **/
    public function testDbcount(){
        /**
         * @var \insolita\redisman\Redisman $module
         **/

        $module=\Yii::$app->getModule('redisman');
        $conn=$module->setConnection('local');

        $sestotal=\Yii::$app->session->get('RedisManager_totalDbItem');
        $this->assertNotEmpty($sestotal);

        $dbcount=$module->getDbCount();
         $this->assertTrue($dbcount==$conn->executeCommand('CONFIG',['GET', 'databases'])[1]);
        $this->assertTrue($dbcount==5);
        $this->assertTrue($dbcount==$sestotal['local']);

        $conn=$module->setConnection('remote1');
        $dbcount=$module->getDbCount();
         $this->assertTrue($dbcount==$conn->executeCommand('CONFIG',['GET', 'databases'])[1]);
        $this->assertTrue($dbcount==4);
        $this->assertTrue($dbcount==$sestotal['remote1']);
    }





  /*  public function testDiffInfo(){
         **
         * @var \insolita\redisman\Redisman $module
         **
        $module=\Yii::$app->getModule('redisman');
       $conn=$module->setConnection('remote1', 1);
        $info1=explode("\r\n", $conn->info());
        $info2=explode("\r\n", $conn->info('default'));
        $info3=explode("\r\n", $conn->info('all'));

        Debug::debug([1=>count($info1), 2=>count($info2), 3=>count($info3)]);

        Debug::debug(array_diff($info1, $info3));
    }
   */

}