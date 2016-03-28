<?php

use yii\db\Migration;

class m130524_201440_init extends Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        /**
         * Create Country Table
         * http://www.nationsonline.org/oneworld/country_code_list.htm
         * 
         * @todo Select PK through module initialisation
         */
        $this->createTable('country', [
            'iso_2' => $this->string(2)->notNull(), //ISO-2 Code is considered ID
            'iso_3' => $this->string(3)->notNull(), //ISOALPHA-3 Code is stored
            'iso_numerical' => $this->integer()->notNull(), //ISO Numerical Code UN M49 Numerical Code
            'has_postcodes' => $this->boolean()->notNull(),
            'city_count' => $this->integer()->defaultValue(0)->notNull(),
            'postcode_count' => $this->integer()->defaultValue(0)->notNull(),
        ]);

        $this->addPrimaryKey('pk_country', 'country', 'iso_2');
        $this->createTable('country_translation', [
            'country_id' => $this->string(3)->notNull(), //ISO-2 Code is considered ID, but you will be able to set it to any of the country codes in a later version
            'language_id' => $this->string(5)->notNull(),
            'official_name' => $this->string(255)->notNull(),
            'common_name' => $this->string(255)->notNull(),
            'adjectival' => $this->string(255),
            'demonym' => $this->string(255),
        ]);


        $this->addPrimaryKey('pk_country_translation', 'country_translation', ['country_id', 'language_id']);
        $this->addForeignKey('fk_country_tanslation_language', 'country_translation', 'language_id', 'language', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_country_tanslation_country', 'country_translation', 'country_id', 'country', 'iso_2', 'CASCADE', 'CASCADE');

        /*
         * Create City Table
         * Essentially Considered as no more than a lookup table containing 
         * translatable entries
         */
        $this->createTable('city', [
            'id' => $this->primaryKey(), //Auto-Incremented ID
            'uid' => $this->string(23),
            'language_id' => $this->string(5)->notNull(), //The default language for fallback purposes
        ]);

        $this->addForeignKey('fk_city_language', 'city', 'language_id', 'language', 'code', 'CASCADE', 'CASCADE');
        $this->createTable('city_translation', [
            'language_id' => $this->string(2)->notNull(),
            'city_id' => $this->integer()->notNull(), //ISO-2 Code is considered ID
            'name' => $this->string(255)->notNull(),
        ]);
        $this->addPrimaryKey('pk_city_translation', 'city_translation', ['city_id', 'language_id']);
        $this->addForeignKey('fk_city_translation_language', 'city_translation', 'language_id', 'language', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_city_translation_city', 'city_translation', 'city_id', 'city', 'id', 'CASCADE', 'CASCADE');


        /**
         * Create Location Table
         * Primary Key is composed out of a postal code 
         */
        $this->createTable('location', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(23),
            'postcode' => $this->string(20), //Postal Code (Null values allowed),
            'country_id' => $this->string(2)->notNull(), //Foreign Key to country,
            'city_id' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk_location_city', 'location', 'city_id', 'city', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_location_country', 'location', 'country_id', 'country', 'iso_2', 'CASCADE', 'CASCADE');



        return true;
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }

}
