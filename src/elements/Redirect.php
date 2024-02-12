<?php
namespace futureactivities\customredirects\elements;

use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use futureactivities\customredirects\elements\db\RedirectQuery;
use craft\helpers\UrlHelper;
use craft\elements\User;

class Redirect extends Element
{
    /**
     * @var integer Site ID
     */
    public ?int $siteId;
    
    /**
     * @var string From
     */
    public $from;
    
    /**
     * @var string To
     */
    public $to;
    
    /**
     * @var string Group
     */
    public $group;
    
    /**
     * @var string Code
     */
    public $code;

    /**
     * @var int hitcount
     */
    public $hitcount;
    
    public static function refHandle(): ?string
    {
        return 'customredirects';
    }
    
    public function afterSave(bool $isNew): void
    {
        if ($isNew) {
            \Craft::$app->db->createCommand()
                ->insert('{{%custom_redirects}}', [
                    'id' => $this->id,
                    'siteId' => $this->siteId,
                    'from' => $this->from,
                    'to' => $this->to,
                    'group' => $this->group,
                    'code' => $this->code,
                    'hitcount' => $this->hitcount
                ])
                ->execute();
        } else {
            \Craft::$app->db->createCommand()
                ->update('{{%custom_redirects}}', [
                    'siteId' => $this->siteId,
                    'from' => $this->from,
                    'to' => $this->to,
                    'group' => $this->group,
                    'code' => $this->code,
                    'hitcount' => $this->hitcount
                ], ['id' => $this->id])
                ->execute();
        }
    
        parent::afterSave($isNew);
    }
    
    public static function find(): ElementQueryInterface
    {
        return new RedirectQuery(static::class);
    }
    
    protected static function defineTableAttributes(): array
    {
        return [
            'from' => 'From',
            'to' => 'To',
            'code' => 'Status Code',
            'hitcount' => 'Hit Count',
            'dateCreated' => 'Date Submitted'
        ];
    }
    
    protected static function defineSearchableAttributes(): array
    {
        return [
            'from',
            'to'
        ];
    }
    
    protected static function defineSources(string $context = null): array
    {
        $sources = [
            [
                'key' => '*',
                'label' => 'All Redirects'
            ]
        ];
        
        $redirects = \Craft::$app->db->createCommand('SELECT `group` FROM {{%custom_redirects}} GROUP BY(`group`)')->queryAll();
        foreach($redirects AS $redirect) {
            $sources[] = [
                'key' => $redirect['group'],
                'label' => $redirect['group'],
                'criteria' => [
                    'group' => $redirect['group']   
                ]
            ];
        }
        
        return $sources;
    }
    
    public function getCpEditUrl(): ?string
    {
        return UrlHelper::cpUrl('customredirects/'.$this->id);
    }
    
    public function canView(User $user):bool 
    {
        return true;
    }
    
    public static function isLocalized(): bool
    {
        return true;
    }
    
    public function getSupportedSites(): array
    {
        return [$this->siteId];
    }
}