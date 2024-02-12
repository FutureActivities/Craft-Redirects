<?php
namespace futureactivities\customredirects\migrations;

use craft\db\Migration;

class Install extends Migration
{
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%custom_redirects}}')) {
            // create the products table
            $this->createTable('{{%custom_redirects}}', [
                'id' => $this->integer()->notNull(),
                'siteId' => $this->integer()->null(),
                'from' => $this->text()->notNull(),
                'to' => $this->text()->notNull(),
                'code' => $this->char(255)->notNull(),
                'hitcount' => $this->integer()->defaultValue(0),
                'group' => $this->char(255)->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'PRIMARY KEY(id)',
            ]);
            
            // give it a FK to the elements table
            $this->addForeignKey(
                $this->db->getForeignKeyName('{{%custom_redirects}}', 'id'),
                '{{%custom_redirects}}', 'id', '{{%elements}}', 'id', 'CASCADE', null);
        }
    }

    public function safeDown()
    {
        // ...
    }
}