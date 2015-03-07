<?php

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->assertTrue(Yii::$app->user->isGuest);
$I->wantTo('ensure that home page works');
$I->amOnPage('/');
$I->dontSee('Logout');
$I->see('Main');
$I->see('Login');
$I->click('Main');
$I->dontSee('Logout');
$I->see('Main');
$I->see('Login');


$user=\app\models\User::findByUsername('admin');
Yii::$app->user->login($user);
$I->assertFalse(Yii::$app->user->isGuest);

$I->amOnPage('/');
$I->see('I`m index');
$I->see('About');
$I->see('Logout');
$I->click('About');
$I->see('Information');