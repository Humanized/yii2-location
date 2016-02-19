<?php

use yii\db\Migration;

class m160129_060019_nuts extends Migration {

    public function safeUp()
    {

        $this->createTable('nuts', [
            'code' => $this->string(20),
            'description' => $this->string(255)
        ]);
        $this->addPrimaryKey('pk_nuts', 'nuts', 'code');
     

        $this->createTable('nuts_hierarchy', [
            'parent' => $this->string(20)->notNull(),
            'child' => $this->string(20)->notNull(),
            'is_offspring' => $this->boolean(0)->notNull(),
            'depth' => $this->integer()->notNull()
        ]);
        $this->addForeignKey('fk_nuts_parent', 'nuts_hierarchy', 'parent', 'nuts', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_nuts_child', 'nuts_hierarchy', 'child', 'nuts', 'code', 'CASCADE', 'CASCADE');
        $this->addPrimaryKey('pk_nuts_hierarchy', 'nuts_hierarchy', ['parent', 'child']);

        $this->createTable('nuts_location', [
            'nuts_id' => $this->string(20)->notNull(),
            'location_id' => $this->integer()->notNull()
        ]);
        $this->addForeignKey('fk_nuts_location_nuts', 'nuts_location', 'nuts_id', 'nuts', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_nuts_location_location', 'nuts_location', 'location_id', 'location', 'id', 'CASCADE', 'CASCADE');
        $this->addPrimaryKey('pk_nuts_location', 'nuts_location', ['nuts_id', 'location_id']);
    }

    public function safeDown()
    {
        echo "m160129_060019_nuts cannot be reverted.\n";

        return false;
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
