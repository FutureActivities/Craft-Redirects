<?php 
namespace futureactivities\customredirects\controllers\v1;

use Craft;
use craft\web\Controller;
use yii\rest\ActiveController;
use futureactivities\customredirects\elements\Redirect;
use futureactivities\customredirects\Plugin;

class RedirectController extends Controller
{
    protected array|int|bool $allowAnonymous = true;
    public $enableCsrfValidation = false;
    
    public function actionIndex()
    {
        $request = Craft::$app->getRequest();
        $uri = $request->getParam('uri');
        if (!$uri) throw new \Exception('Missing uri body param');
        
        // Look for exact match
        $redirect = Redirect::find()->from($uri)->one();
        if ($redirect) {
            $this->hit($redirect);
            return $this->asJson($this->format($redirect, $uri));
        }
        
        // Regex match
        $query = Redirect::find()->orderBy('from DESC')->all();
        foreach($query AS $redirect) {
            $from = rtrim($redirect->from, '/');
            preg_match('/^'.str_replace('/', '\/', $from).'/', $uri, $matches);
            if (count($matches)) {
                $this->hit($redirect);
                return $this->asJson($this->format($redirect, $uri, true));
            }
        }
        
        return $this->asJson(null);
    }
    
    protected function hit($redirect)
    {
        $redirect->hitcount++;
        Craft::$app->elements->saveElement($redirect);
    }
    
    protected function format($redirect, $uri, $regex = true)
    {
        $to = $redirect->to;
        $from = rtrim($redirect->from, '/');
        
        // If regex, replace any strings if required
        if ($regex) {
            $to = preg_replace('/'.str_replace('/', '\/', $from).'/', $redirect->to, $uri);
        }
        
        return [
            'from' => $redirect->from,
            'to' => $to,
            'code' => $redirect->code
        ];
    }
}