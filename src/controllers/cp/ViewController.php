<?php

namespace futureactivities\customredirects\controllers\cp;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use craft\helpers\UrlHelper;
use futureactivities\customredirects\elements\Redirect;
use futureactivities\customredirects\Plugin;

class ViewController extends Controller
{
    public function actionIndex(int $redirectId = null): Response
    {
        $variables = [];
        
        // Load message
        $variables['data'] = $redirectId ? Redirect::find()->id($redirectId)->one() : null;
        
        // Breadcrumbs
        $variables['crumbs'] = [
            [
                'label' => Craft::t('customredirects', 'Redirects'),
                'url' => UrlHelper::url('customredirects')
            ]
        ];
        
        // Set the base CP edit URL
        $variables['baseCpEditUrl'] = 'customredirects/{id}';
        
        return $this->renderTemplate('customredirects/_view', $variables);
    }
    
    public function actionSave()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        
        $redirectId = Craft::$app->getRequest()->getBodyParam('redirectId');

        if ($redirectId) {
            $redirect = Redirect::find()->id($redirectId)->one();
            
            if (!$redirect) {
                throw new NotFoundHttpException('Redirect not found');
            }
        } else {
            $redirect = new Redirect();
        }
        
        $redirect->siteId = Craft::$app->getRequest()->getBodyParam('siteId');
        $redirect->from = Craft::$app->getRequest()->getBodyParam('from');
        $redirect->to = Craft::$app->getRequest()->getBodyParam('to');
        $redirect->group = Craft::$app->getRequest()->getBodyParam('group');
        $redirect->code = Craft::$app->getRequest()->getBodyParam('code');
        
        Craft::$app->elements->saveElement($redirect);
         
        return $this->redirectToPostedUrl();
    }
    
    public function actionDelete()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        
        $redirectId = Craft::$app->getRequest()->getBodyParam('redirectId');
        $redirect = $redirectId ? Redirect::find()->id($redirectId)->one() : null;
        
        if ($redirect)
            Craft::$app->elements->deleteElement($redirect);
         
        return $this->redirectToPostedUrl();
    }
}