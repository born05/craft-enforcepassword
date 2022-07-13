<?php

namespace born05\enforcepassword;

use born05\enforcepassword\models\Settings;
use born05\enforcepassword\services\History;
use born05\enforcepassword\services\Security;

use Craft;
use craft\base\Plugin as CraftPlugin;
use craft\elements\User as UserElement;
use craft\web\Application as WebApplication;

use yii\base\Event;
use yii\base\ModelEvent;

class Plugin extends CraftPlugin
{
    public string $schemaVersion = '1.0.0';

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Plugin::$plugin
     *
     * @var Plugin
     */
    public static Plugin $plugin;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (!$this->isInstalled) {
            return;
        }

        // Register Components (Services)
        $this->setComponents(
            [
                'history' => History::class,
                'security' => Security::class,
            ]
        );

        Event::on(WebApplication::class, WebApplication::EVENT_INIT, function () {
            // Only try to trigger jobs from the admin.
            if (Craft::$app->getRequest()->getIsCpRequest()) {
                $this->history->queuePasswordResets();
            }
        });

        Event::on(
            UserElement::class,
            UserElement::EVENT_BEFORE_VALIDATE,
            function (ModelEvent $event) {
                /** @var UserElement $user */
                $user = $event->sender;

                // Check if a new password was set on the user
                if ($user->newPassword) {
                    $event->isValid = $this->security->validatePassword($user, $user->newPassword);
                }
            }
        );

        Event::on(
            UserElement::class,
            UserElement::EVENT_AFTER_SAVE,
            function (ModelEvent $event) {
                /** @var UserElement $user */
                $user = $event->sender;

                $this->history->updateHistoryByUser($user);
            }
        );
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }
}
