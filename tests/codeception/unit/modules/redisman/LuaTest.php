<?php
namespace tests\codeception\unit\modules\redisman;

use Codeception\Util\Debug;
use insolita\redisman\Redisman;

class LuaTest extends \Codeception\TestCase\Test
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
    public function testEscapeQuotes(){
        $keys=$this->module->executeCommand('KEYS',['stress:*']);
        foreach($keys as $key){
            $qkey=Redisman::quoteValue($key);
            Debug::debug($key.'  - '.$qkey);

            $res = $this->module->executeCommand('EVAL', [$this->buildExistScript($qkey),0]);
            $this->assertEquals(1,$res);
            $res = $this->module->executeCommand('EXISTS', [$key]);
            $this->assertEquals(1,$res);
        }

    }
    /**
     * @depends testEscapeQuotes
    **/
     public function testLuaPaginated()
     {
         $conn = $this->module->setConnection('remote1', 1);
         $start = microtime(false);
         $typecond='tp=="string" or tp=="hash" or tp=="list"';
         $res = $conn->executeCommand('EVAL', [$this->scriptBuilder('Queue:*',$typecond, 3,6), 0]);
         $end = microtime(false) - $start;
         Debug::debug($res);
         Debug::debug('time for 15- ' . $end);
     }
     /**
      * @depends testLuaPaginated
     **/

    public function testLuaScan(){
        $conn=$this->module->setConnection('remote1',1);
        $start=microtime(false);
      $res = $conn->executeCommand('EVAL', [$this->buildScanDpScript('Queue:*', 15),0]);
        $end=microtime(false)-$start;
      Debug::debug($res);
        Debug::debug('time for 15- '.$end);

        $start=microtime(false);
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpScript('*:*', 40),0]);
        $end=microtime(false)-$start;
         Debug::debug('time for 40- '.$end);

        $start=microtime(false);
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpScript('*:*', 100),0]);
        $end=microtime(false)-$start;
         Debug::debug('time for 100- '.$end);

        $start=microtime(false);
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpScript('*:*', 500),0]);
        $end=microtime(false)-$start;
         Debug::debug('time for 500- '.$end);

        $start=microtime(false);
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpScript('*:*', 1000),0]);
        $end=microtime(false)-$start;
         Debug::debug('time for 1000 - '.$end);

        $start=microtime(false);
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpScript('*:*', 5000),0]);
        $end=microtime(false)-$start;
        Debug::debug('time for 5000 - '.$end);
    }
     /**
      * @depends testLuaScan
      **/

    public function testLuaScan2(){
        $conn=$this->module->setConnection('remote1',1);
        $start=microtime(false);
        $typecond='1==1';
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpCntScript('*:*', 15,$typecond),0]);
        $end=microtime(false)-$start;
        Debug::debug($res);
        Debug::debug('time for 15- '.$end);

         $start=microtime(false);
        $typecond='tp=="string"';
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpCntScript('*:*', 15,$typecond),0]);
        $end=microtime(false)-$start;
        Debug::debug($res);
        Debug::debug('time for 15- '.$end);

         $start=microtime(false);
        $typecond='tp=="zset" or tp=="hash"';
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpCntScript('*:*', 15,$typecond),0]);
        $end=microtime(false)-$start;
        Debug::debug($res);
        Debug::debug('time for 15- '.$end);

         $start=microtime(false);
        $typecond='tp=="zset" or tp=="set"';
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpCntScript('*:*', 15,$typecond),0]);
        $end=microtime(false)-$start;
        Debug::debug($res);
        Debug::debug('time for 15- '.$end);

         $start=microtime(false);
        $typecond='tp=="zset" or tp=="set" or tp=="list"';
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpCntScript('*:*', 15,$typecond),0]);
        $end=microtime(false)-$start;
        Debug::debug($res);
        Debug::debug('time for 15- '.$end);

         $start=microtime(false);
        $typecond='tp=="list" or tp=="set"';
        $res = $conn->executeCommand('EVAL', [$this->buildScanDpCntScript('*:*', 15,$typecond),0]);
        $end=microtime(false)-$start;
        Debug::debug($res);
        Debug::debug('time for 15- '.$end);


    }
     /**
      * @depends testLuaScan2
      **/
     public function testKeyCnt(){
        $start=microtime(false);
        $conn=$this->module->setConnection('remote1',1);
        $res = $conn->executeCommand('EVAL', [$this->buildKeyCntScript('Queue:*'),0]);
        $end=microtime(false)-$start;
        Debug::debug($res);
        Debug::debug('time - '.$end);
    }
     /**
      * @depends testLuaScan2
      **/
     public function testScanCnt(){
        $start=microtime(false);
        $conn=$this->module->setConnection('remote1',1);
        $res = $conn->executeCommand('EVAL', [$this->buildScanCntScript('Queue:*'),0]);
        $end=microtime(false)-$start;
        Debug::debug($res);
        Debug::debug('time - '.$end);
    }
    /**
     * @depends testEscapeQuotes
     **/
     public function testInfoScript(){
         $start=microtime(false);
         $conn=$this->module->setConnection('remote1',1);
         $res = $conn->executeCommand('EVAL', [$this->buildInfoScript('Queue:YProxy'),0]);
         $end=microtime(false)-$start;
         Debug::debug($res);
         Debug::debug('time - '.$end);
     }


    public function buildExistScript($key){
        $script
            = <<<EOF
local tp=redis.call("EXISTS", $key)
return tp;
EOF;
        return $script;
    }
     public function buildInfoScript($key){
         $script
             = <<<EOF
local tp=redis.call("TYPE", "$key")["ok"]
local size=9999
if tp == "string" then
    size=redis.call("STRLEN", "$key")
elseif tp == "hash" then
    size=redis.call("HLEN", "$key")
elseif tp == "list" then
    size=redis.call("LLEN", "$key")
elseif tp == "set" then
    size=redis.call("SCARD", "$key")
elseif tp == "zset" then
    size=redis.call("ZCARD", "$key")
else
    size=9999
end
local info={tp, size, redis.call("TTL", "$key"),
            redis.call("OBJECT","REFCOUNT", "$key"),redis.call("OBJECT","IDLETIME", "$key"),
            redis.call("OBJECT", "ENCODING", "$key"),redis.call("TTL", "$key")};
return info;
EOF;
         return $script;
     }

    public function buildScanDpCntScript($pattern, $limit, $typecond){
        $script=<<<EOF
local all_keys = {};
local keys = {};
local done = false;
local cursor = "0"
local count=0;
local size=0
local tp
repeat
    local result = redis.call("SCAN", cursor, "match", "$pattern", "count", 50)
    cursor = result[1];
    keys = result[2];
    for i, key in ipairs(keys) do
        tp=redis.call("TYPE", key)["ok"]
        if #all_keys<$limit then
           if $typecond then
               if tp == "string" then
                   size=redis.call("STRLEN", key)
                elseif tp == "hash" then
                    size=redis.call("HLEN", key)
                elseif tp == "list" then
                    size=redis.call("LLEN", key)
                elseif tp == "set" then
                    size=redis.call("SCARD", key)
                elseif tp == "zset" then
                    size=redis.call("ZCARD", key)
                else
                    size=9999
                end
               all_keys[#all_keys+1] = {key, tp, size, redis.call("TTL", key)};
           end
        end
        if $typecond then
           count=count+1
        end
    end
    if cursor == "0" then
        done = true;
    end
until done
all_keys[#all_keys+1]=count;
return all_keys;
EOF;
        return $script;
    }

    public function buildScanDpScript($pattern, $limit){
        $script=<<<EOF
local all_keys = {};
local keys = {};
local done = false;
local cursor = "0";
repeat
    local result = redis.call("SCAN", cursor, "match", "$pattern", "count", 50)
    cursor = result[1];
    keys = result[2];
    for i, key in ipairs(keys) do
        all_keys[#all_keys+1] = {key, redis.call("TYPE", key), redis.call("TTL", key)};
    end
    if cursor == "0" or #all_keys>=$limit then
        done = true;
    end
until done
return all_keys;
EOF;
        return $script;
    }

    public function buildKeyCntScript($pattern){
        $script=<<<EOF
local count=0;
local result = redis.call("KEYS", "$pattern")
    for i, key in ipairs(result) do
        count=count+1
    end
 return count;
EOF;
        return $script;
    }

    public function buildScanCntScript($pattern){
        $script=<<<EOF
local count=0;
local keys = {};
local done = false;
local cursor = "0";
repeat
    local result = redis.call("SCAN", cursor, "match", "$pattern", "count", 50)
    cursor = result[1];
    keys = result[2];
    for i, key in ipairs(keys) do
        count=count+1
    end
    if cursor == "0"  then
        done = true;
    end
until done
return count;
EOF;
        return $script;
    }


     protected function scriptBuilder($pattern, $typecond,$start, $end)
     {
         $script=<<<EOF
local all_keys = {};
local keys = {};
local done = false;
local cursor = "0"
local count=0;
local size=0
local tp
repeat
    local result = redis.call("SCAN", cursor, "match", "$pattern", "count", 50)
    cursor = result[1];
    keys = result[2];
    for i, key in ipairs(keys) do
        tp=redis.call("TYPE", key)["ok"]
        if count>=$start and count<$end then
           if $typecond then
               if tp == "string" then
                   size=redis.call("STRLEN", key)
                elseif tp == "hash" then
                    size=redis.call("HLEN", key)
                elseif tp == "list" then
                    size=redis.call("LLEN", key)
                elseif tp == "set" then
                    size=redis.call("SCARD", key)
                elseif tp == "zset" then
                    size=redis.call("ZCARD", key)
                else
                    size=9999
                end
               all_keys[#all_keys+1] = {key, tp, size, redis.call("TTL", key)};
           end
        end
        if $typecond then
           count=count+1
        end
    end
    if cursor == "0" then
        done = true;
    end
until done
all_keys[#all_keys+1]=count;
return all_keys;
EOF;
         return $script;
     }

     protected function scriptBuilderGreedy($pattern, $typecond)
     {
          $script=<<<EOF
local all_keys = {};
local keys = {};
local done = false;
local cursor = "0"
local count=0;
local size=0
local tp
repeat
    local result = redis.call("SCAN", cursor, "match", "$pattern", "count", 50)
    cursor = result[1];
    keys = result[2];
    for i, key in ipairs(keys) do
        tp=redis.call("TYPE", key)["ok"]
           if $typecond then
               if tp == "string" then
                   size=redis.call("STRLEN", key)
                elseif tp == "hash" then
                    size=redis.call("HLEN", key)
                elseif tp == "list" then
                    size=redis.call("LLEN", key)
                elseif tp == "set" then
                    size=redis.call("SCARD", key)
                elseif tp == "zset" then
                    size=redis.call("ZCARD", key)
                else
                    size=9999
                end
               all_keys[#all_keys+1] = {key, tp, size, redis.call("TTL", key)};
           end
        if $typecond then
           count=count+1
        end
    end
    if cursor == "0" then
        done = true;
    end
until done
all_keys[#all_keys+1]=count;
return all_keys;
EOF;
         return $script;
     }
}

