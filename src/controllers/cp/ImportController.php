<?php

namespace futureactivities\customredirects\controllers\cp;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use craft\helpers\UrlHelper;
use futureactivities\customredirects\elements\Redirect;
use futureactivities\customredirects\Plugin;

class ImportController extends Controller
{
    /**
     * Quick and dirty CSV import.
     * Future development required to build into the CMS interface.
     */
    public function actionIndex()
    {
        if (!file_exists(Craft::$app->path->storagePath.'/redirect-import.csv'))
            throw new \Exception('Upload your CSV to storage/redirect-import.csv');
            
        $csv = array_map('str_getcsv', file(Craft::$app->path->storagePath.'/redirect-import.csv'));
        
        if (strtolower($csv[0][0]) !== 'from' && strtolower($csv[0][1]) !== 'to')
            throw new \Exception('Please ensure CSV is formatted: from, to, group, code');

        foreach($csv AS $row) {
            if ($row[0] == 'from') continue;
            
            $siteId = isset($row[4]) ? (int)$row[4] : 1;
            $site = Craft::$app->sites->getSiteById($siteId);
            
            $redirect = Redirect::find()->site($site)->from($row[0])->one();
            if (!$redirect) $redirect = new Redirect();
            
            $redirect->siteId = $siteId;
            $redirect->from = $row[0];
            $redirect->to = $row[1];
            $redirect->group = isset($row[2]) ? $row[2] : 'Redirections';
            $redirect->code = isset($row[3]) ? $row[3] : 301;
            
            Craft::$app->elements->saveElement($redirect);
        }
        
        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('customredirects'))->send();
        
        return;
    }
}