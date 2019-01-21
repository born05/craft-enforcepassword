<?php

namespace born05\enforcepassword\console\controllers;

use born05\enforcepassword\Plugin as EnforcePassword;

use Craft;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Default Command
 * 
 * ./craft enforce-password/default
 */
class DefaultController extends Controller
{
    public function actionIndex()
    {
        echo "Queue password resets.\n";
        
        EnforcePassword::$plugin->history->queuePasswordResets();

        return "Done queueing password resets.";
    }
}
