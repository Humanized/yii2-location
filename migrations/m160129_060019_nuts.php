<?php

use yii\db\Migration;

class m160129_060019_nuts extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    public function safeUp()
    {

        $this->createTable('nuts_code', [
            'id' => $this->string(10),
            'description' => $this->string(255),
            'country_id' => $this->string(2),
                ], $this->tableOptions);
        $this->addPrimaryKey('pk_nuts', 'nuts_code', 'id');
        $this->addForeignKey('fk_nuts_country', 'nuts', 'country_id', 'nuts', 'code', 'CASCADE', 'CASCADE');


        $this->createTable('nuts_hierarchy', [
            'parent_id' => $this->string(20)->notNull(),
            'child_id' => $this->string(20)->notNull(),
            'is_offspring' => $this->boolean(0)->notNull(),
            'depth' => $this->integer()->notNull()
                ], $this->tableOptions);
        $this->addForeignKey('fk_nuts_parent', 'nuts_hierarchy', 'parent_id', 'nuts_code', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_nuts_child', 'nuts_hierarchy', 'child_id', 'nuts_code', 'id', 'CASCADE', 'CASCADE');
        $this->addPrimaryKey('pk_nuts_hierarchy', 'nuts_hierarchy', ['parent', 'child']);

        $this->createTable('nuts_location', [
            'id' => $this->primaryKey(),
            'nuts_code_id' => $this->string(20)->notNull(),
            'postcode' => $this->string(20)->notNull(),
            'country_id' => $this->integer()->notNull(),
            'location_id' => $this->integer()
                ], $this->tableOptions);
        $this->addForeignKey('fk_nuts_country', 'nuts', 'cp', 'nuts', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_nuts_location_nuts', 'nuts_location', 'nuts_id', 'nuts_code', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_nuts_location_location_optional', 'nuts_location', 'location_id', 'location', 'id', 'CASCADE', 'CASCADE');
        //   $this->addPrimaryKey('pk_nuts_location', 'nuts_location', ['nuts_id', 'location_id']);
    }

    public function safeDown()
    {
        echo "m160129_060019_nuts cannot be reverted.\n";

        return false;
    }

}
