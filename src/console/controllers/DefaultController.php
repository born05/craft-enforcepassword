<?php

namespace born05\enforcepassword\console\controllers;

use born05\enforcepassword\Plugin as EnforcePassword;

use yii\console\Controller;

/**
 * Enforces password resets.
 */
class DefaultController extends Controller
{
    /**
     * Queue's the password reset task.
     */
    public function actionIndex()
    {
        echo "Queue password resets.\n";
        
        EnforcePassword::$plugin->history->queuePasswordResets();

        return "Done queueing password resets.";
    }
}
