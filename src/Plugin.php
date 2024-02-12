<?php
namespace futureactivities\customredirects;

use Craft;
use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use craft\elements\Asset;
use craft\helpers\Html;

class Plugin extends \craft\base\Plugin
{
    public bool $hasCpSettings = false;
    public bool $hasCpSection = true;
    public string $schemaVersion = '1.0.0';
    
    public function init()
    {
        parent::init();
        
        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['rest/v1/redirect'] = 'customredirects/v1/redirect';
            }
        );
        
        // Register a custom CP route
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['customredirects/<redirectId:\d+>'] = 'customredirects/cp/view';
            $event->rules['customredirects/new'] = 'customredirects/cp/view';
            $event->rules['customredirects/export'] = 'customredirects/cp/export';
            $event->rules['customredirects/import'] = 'customredirects/cp/import';
        });
    }
    
    public function getCpNavItem(): ?array
    {
        $item = parent::getCpNavItem();
        $item['label'] = 'Redirects';
        $item['icon'] = '@futureactivities/customredirects/icon.svg';
        return $item;
    }
    
    public function registerCpUrlRules(RegisterUrlRulesEvent $event)
    {
        $rules = [
            'customredirects/<redirectId:\d+>' => 'customredirects/cp/view'
        ];
        $event->rules = array_merge($event->rules, $rules);
    }
}