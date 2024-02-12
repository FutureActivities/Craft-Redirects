<?php
namespace futureactivities\customredirects\elements\db;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use futureactivities\customredirects\elements\Redirect;

class RedirectQuery extends ElementQuery
{
    public mixed $siteId = 1;
    public $from;
    public $to;
    public $group;
    public $code;
    public $hitcount;
    
    public function siteId($value): ElementQuery
    {
        $this->siteId = $value;

        return $this;
    }
    
    public function from($value)
    {
        $this->from = $value;

        return $this;
    }
    
    public function to($value)
    {
        $this->to = $value;

        return $this;
    }

    public function group($value)
    {
        $this->group = $value;

        return $this;
    }

    public function code($value)
    {
        $this->code = $value;

        return $this;
    }
    
    public function hitcount($value)
    {
        $this->hitcount = $value;

        return $this;
    }
    
    protected function beforePrepare(): bool
    {
        // join in the products table
        $this->joinElementTable('custom_redirects');

        // select the price column
        $this->query->select([
            'custom_redirects.siteId',
            'custom_redirects.from',
            'custom_redirects.to',
            'custom_redirects.group',
            'custom_redirects.code',
            'custom_redirects.hitcount',
        ]);
        
        if ($this->siteId) {
            $this->subQuery->andWhere(Db::parseParam('custom_redirects.siteId', $this->siteId));
        }
        
        if ($this->from) {
            $this->subQuery->andWhere(Db::parseParam('custom_redirects.from', $this->from));
        }
        
        if ($this->to) {
            $this->subQuery->andWhere(Db::parseParam('custom_redirects.to', $this->to));
        }
        
        if ($this->group) {
            $this->subQuery->andWhere(Db::parseParam('custom_redirects.group', $this->group));
        }

        if ($this->code) {
            $this->subQuery->andWhere(Db::parseParam('custom_redirects.code', $this->code));
        }
        
        if ($this->hitcount) {
            $this->subQuery->andWhere(Db::parseParam('custom_redirects.hitcount', $this->hitcount));
        }
    
        return parent::beforePrepare();
    }
}