<?php

namespace futureactivities\customredirects\controllers\cp;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use craft\helpers\UrlHelper;
use futureactivities\customredirects\elements\Redirect;
use futureactivities\customredirects\Plugin;

class ExportController extends Controller
{
    public function actionIndex(): Response
    {
        $data = $this->getRedirects();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=redirects.csv');
        $output = fopen('php://output', 'w');
        
        fputcsv($output, $data['headings']);
        
        foreach ($data['redirects'] AS $redirect) {
            $row = [];
            foreach($data['headings'] AS $key)
                $row[] = isset($redirect[$key]) ? $redirect[$key] : '';

            fputcsv($output, $row);
        }
        
        die();
    }
    
    protected function getRedirects()
    {
        $headings = ['SiteId','ID','from','to','group','code','hitcount','dateCreated'];
        $redirects = [];
        
        $saved = Redirect::find()->site('*')->all();
        foreach($saved AS $redirect) {
             $redirects[] = [
                'SiteId' => $redirect->siteId,
                'ID' => $redirect->id,
                'from' => $redirect->from,
                'to' => $redirect->to,
                'group' => $redirect->group,
                'code' => $redirect->code,
                'hitcount' => $redirect->hitcount,
                'dateCreated' => $redirect->dateCreated->format('Y-m-d H:i:s'),
            ];
        }
        
        return [
            'headings' => $headings,
            'redirects' => $redirects
        ];
        
    }
}