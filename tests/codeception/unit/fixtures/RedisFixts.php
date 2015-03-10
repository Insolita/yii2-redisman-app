<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 05.03.15
 * Time: 11:16
 */

namespace tests\codeception\unit\fixtures;



use yii\base\Component;

class RedisFixts extends Component
{
    /**
     * @var \insolita\redisman\Redisman $module
     **/
    private $module;


    public function createFixtures(){
        $this->module = \Yii::$app->getModule('redisman');
        $this->module->setConnection('localnat',1);
        $this->deleteFixtures();
        /** string **/
        $this->module->executeCommand('SET',['tfx_string','somestringval']);
        $this->module->executeCommand('SET',['tfx_stringlong','
        Lorem Ipsum - это текст-"рыба", часто используемый в печати и вэб-дизайне. Lorem Ipsum является стандартной "рыбой" для текстов на латинице с начала XVI века. В то время некий безымянный печатник создал большую коллекцию размеров и форм шрифтов, используя Lorem Ipsum для распечатки образцов. Lorem Ipsum не только успешно пережил без заметных изменений пять веков, но и перешагнул в электронный дизайн. Его популяризации в новое время послужили публикация листов Letraset с образцами Lorem Ipsum в 60-х годах и, в более недавнее время, программы электронной вёрстки типа Aldus PageMaker, в шаблонах которых используется Lorem Ipsum.
        ']);
        $this->module->executeCommand('SET',['tfx_stringexp','somestringvalwithexpiration']);
        $this->module->executeCommand('EXPIRE',['tfx_stringexp',100000]);
        $this->module->executeCommand('SET',['testfxt:1:string','somestringval1']);
        $this->module->executeCommand('SET',['testfxt:2:string','somestringval2']);
        $this->module->executeCommand('SET',['testfxt:3:string','somestringval3']);
        /** hash **/
        $this->module->executeCommand('HSET',['tfx_hash','hashfield','hashval']);
        $this->module->executeCommand('HMSET',['tfx_hashlong',
                'hashfield1','hashval1',
                'hashfield2','hashval2',
                'hashfield3','hashval3',
                'hashfield4','hashval4',
                'hashfield5','hashval5',
                'hashfield6','hashval6',
                'hashfield7','hashval7',
                'hashfield8','hashval8',
                'hashfield9','hashval9',
                'hashfield10','hashval10',
                'hashfield11','hashval11',
                'hashfield12','hashval12',
                'hashfield13','hashval13',
                'hashfield14','hashval14',
                'hashfield15','hashval15',
                'hashfield16','hashval16',
                'hashfield17','hashval17',
                'hashfield18','hashval18'
            ]);
        $this->module->executeCommand('HMSET',['testfxt:1:hash',
                'hashfierththld1','hasgkbjrfkjkhval1',
                '4543','hashvatgrl2',
            ]);
        $this->module->executeCommand('HMSET',['testfxt:2:hash',
                'hashfi8938945eld1','hashval1',
                'hashfield2','hashvrgfergkral2',
            ]);
        $this->module->executeCommand('HMSET',['testfxt:3:hash',
                'hashfield1','hashval1',
                'hashfield2','hashval2',
            ]);


        /** list **/

        $this->module->executeCommand('RPUSH',['tfx_list','someval1','someval2','someval3']);
        $this->module->executeCommand('RPUSH',['tfx_listlong','someval1','someval2','someval3',
                'someval4','someval5','someval6',
                'someval7','someval8','someval9',
                'someval10','someval11','someval12',
                'someval13','someval14','someval15',
                'someval16','someval17','someval18',
                'someval19','someval20','someval21',]);
        $this->module->executeCommand('RPUSH',['tfx_listexp','somestringvalwithexpiration','someval2','someval3']);
        $this->module->executeCommand('EXPIRE',['tfx_listexp',100000]);
        $this->module->executeCommand('RPUSH',['testfxt:1:list','reger','oiefyefewfe']);
        $this->module->executeCommand('RPUSH',['testfxt:2:list','i uerufgier','iueffef9weft8e9w9']);
        $this->module->executeCommand('RPUSH',['testfxt:3:list','349y3fh3498f43','ietf87efterfew']);


        /** set **/

        $this->module->executeCommand('SADD',['tfx_set','someval1','someval2','someval3','someval4']);
        $this->module->executeCommand('SADD',['tfx_setlong','someval1','someval2','someval3',
                'someval4','someval5','someval6',
                'someval7','someval8','someval9',
                'someval10','someval11','someval12',
                'someval13','someval14','someval15',
                'someval16','someval17','someval18',
                'someval19','someval20','someval21',]);
        $this->module->executeCommand('SADD',['testfxt:1:set','reger','oiefyefewfe']);
        $this->module->executeCommand('SADD',['testfxt:2:set','i uerufgier','iueffef9weft8e9w9']);
        $this->module->executeCommand('SADD',['testfxt:3:set','349y3fh3498f43','ietf87efterfew']);

        /** zzset **/
        $this->module->executeCommand('ZADD',['tfx_zset',4,'someval1',3,'someval2',8,'someval3']);
        $this->module->executeCommand('ZADD',['tfx_zsetlong',5,'someval1',0.3,'someval2',0.8,'someval3',
                3,'someval4',5,'someval5',11,'someval6',
                7,'someval7',6.439,'someval8',3,'someval9',
                2,'someval10',2.5,'someval11',4.33,'someval12',
                5,'someval13',6,'someval14',3.7,'someval15',
                2,'someval16',6,'someval17',4,'someval18']);
        $this->module->executeCommand('ZADD',['testfxt:1:zset',1,'reger',2,'oiefyefewfe']);
        $this->module->executeCommand('ZADD',['testfxt:2:zset',300,'i uerufgier',500,'iueffef9weft8e9w9']);
        $this->module->executeCommand('ZADD',['testfxt:3:zset',2011,'349y3fh3498f43',2015,'ietf87efterfew']);

        /**stress**/
        $this->module->executeCommand('SET',['stress:"','valofstresskey']);
        $this->module->executeCommand('SET',["stress:'",'valofstresskey']);
        $this->module->executeCommand('SET',['stress:qwe"rty"uio','valofstresskey']);
        $this->module->executeCommand('SET',['stress:q\'we"rty"ui\'o','valofstresskey']);
        $this->module->executeCommand('SET',["stress:q\'we\"rty\"ui\'o",'valofstresskey']);
        $this->module->executeCommand('SET',["stress:qw'e\"rt'y\"uio",'valofstresskey']);
        $this->module->executeCommand('SET',["stress:qwerty'uio'p",'valofstresskey']);
        $this->module->executeCommand('SET',['stress:qww\"er\"t','valofstresskey']);
        $this->module->executeCommand('SET',["stress:qww\'er\'t",'valofstresskey']);
        $this->module->executeCommand('SET',['stress:\\','valofstresskey']);
        $this->module->executeCommand('SET',['stress:<script>alert(document.cookie)</script>','valofstresskey']);
        $this->module->executeCommand('SET',['stress:<script>alert("hi");</script>','valofstresskey']);
        $this->module->executeCommand('SET',['stress:\\jjj\\\e\n','valofstresskey']);
        $this->module->executeCommand('SET',['stress:q1','"']);
        $this->module->executeCommand('SET',["stress:q2","'"]);
        $this->module->executeCommand('SET',['stress:q3','qwe"rty"uio']);
        $this->module->executeCommand('SET',["stress:q4","qwerty'uio'p"]);
        $this->module->executeCommand('SET',['stress:q5','qww\"er\"t']);
        $this->module->executeCommand('SET',["stress:q6",'qww\'er\'t']);
        $this->module->executeCommand('SET',['stress:q7','valofstre\\sskey']);
        $this->module->executeCommand('SET',['stress:q8','<script>alert(document.cookie)</script>']);
        $this->module->executeCommand('SET',['stress:q9','<script>alert("hi");</script>']);
        $this->module->executeCommand('SET',['stress:q10','<script>alert(\'hi\');</script>']);
        $this->module->executeCommand('SET',['stress:q11','\\jjj\\\e\n']);
        $this->module->executeCommand('HMSET',['stress:hash',
                '"','\\jjj\\\e\n',
                '"','jjj\\\e\n',
                '*','qwe"rty"uio',
                "qwerty'uio'p",'qww\"er\"t',
                'stre\\sskey','qww\'er\'t',
                '<script>alert(document.cookie)</script>','<script>alert(document.cookie)</script>',
                '<script>alert("hi");</script>','<script>alert("hi");</script>',
                '<script>alert(\'hi\');</script>','<script>alert(\'hi\');</script>',
            ]);



    }

    public function deleteFixtures(){
        $this->module = \Yii::$app->getModule('redisman');
        $this->module->setConnection('localnat',1);
        $this->module->executeCommand('FLUSHDB');
    }
} 