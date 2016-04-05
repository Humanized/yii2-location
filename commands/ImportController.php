<?php

namespace humanized\location\commands;

use humanized\clihelpers\controllers\ImportController as Controller;
use humanized\location\models\location\City;
use humanized\location\models\location\Country;
use humanized\location\models\translation\CityTranslation;
use humanized\location\models\translation\CountryTranslation;
use humanized\location\models\location\Location;

/**
 * A CLI allowing Yii2 location managementS.
 * 
 * Supported commands:
 * 
 * > contact/import --<option>
 *  --path
 *  --delimiter
 *  --enclosure
 *  --terminator
 *  --table
 *  --autobuild
 * 
 * > contact/import/countries
 * 
 * 
 * 
 * @name Location Import CLI
 * @version 0.1
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-contact
 * 
 * 
 * 
 */
class ImportController extends Controller
{

    private $_languages = [
        'deu' => 'de', 'fra' => 'fr',
        'hrv' => 'hr', 'ita' => 'it',
        'jpn' => 'ja', 'nld' => 'nl',
        'por' => 'pt', 'rus' => 'ru',
        'spa' => 'es', 'fin' => 'fi',
        'cym' => 'cy',
    ];
    private $_without_pc = [
        "AO", "AG", "AW", "BS", "BZ", "BJ", "BW", "BF", "BI", "CM", "CF", "KM",
        "CG", "CD", "CK", "CI", "DJ", "DM", "GQ", "ER", "FJ", "TF", "GM", "GH",
        "GD", "GN", "GY", "HK", "IE", "JM", "KE", "KI", "MO", "MW", "ML", "MR",
        "MU", "MS", "NR", "AN", "NU", "KP", "PA", "QA", "RW", "KN", "LC", "ST",
        "SA", "SC", "SL", "SB", "SO", "ZA", "SR", "SY", "TZ", "TL", "TK", "TO",
        "TT", "TV", "UG", "AE", "VU", "YE", "ZW"
    ];

    public function actionNuts()
    {
        $folder = \Yii::getAlias('@data') . '/nuts/';
        $files = scandir($folder . 'code/');

        foreach ($files as $file) {
            if ($folder != '.' && $folder != '..') {
                
            }
        }
    }

    public function actionImportBe()
    {
        $fileName = \Yii::getAlias('@data') . '/location/be_nlfr.csv';
        $file = fopen($fileName, "r");
        while (!feof($file)) {
            $record = fgetcsv($file, 0, ';');
            if (isset($record[0])) {
                $lang = in_array((int) substr($record[1], 0, 1), [1, 2, 3, 8, 9]) ? 'NL' : 'FR';
                $city = new City(['language_id' => $lang]);
                $city->save();
                (new CityTranslation(['name' => $record[2], 'city_id' => $city->id, 'language_id' => 'fr']))->save();
                (new CityTranslation(['name' => $record[3], 'city_id' => $city->id, 'language_id' => 'nl']))->save();
                (new Location(['city_id' => $city->id, 'country_id' => 'BE', 'postcode' => $record[1]]))->save();
            } else {
                break;
            }
        }
    }

    public function actionBulk($path = NULL)
    {
        if (!isset($path)) {
            $path = \Yii::getAlias('@data') . "/location/XXX";
        }
        $folders = scandir($path);

        foreach ($folders as $folder) {

            if ($folder != '.' && $folder != '..') {
                $filePath = $path . '/' . $folder;
                $files = scandir($filePath);
                $this->stdout('Importing ' . $folder . "\n");

                $cityFile = NULL;
                $postcodeFile = NULL;
                foreach ($files as $file) {

                    switch (strlen($file)) {
                        case 6: {
                                $postcodeFile = $file;
                                break;
                            }
                        case 9: {
                                $cityFile = $file;
                                break;
                            }
                    }
                }
                $this->importDefault(substr($postcodeFile, 0, 2), $filePath . '/' . $cityFile, $filePath . '/' . $postcodeFile, substr($cityFile, 3, 2));
            }
        }
    }

    public function importDefault($countryCode, $cityFile, $postcodeFile, $languageCode)
    {
        $this->stdout('importing city file for: ' . $countryCode);
        $this->importCityFile($countryCode, $cityFile, $languageCode);
        $this->stdout('importing postcode file for: ' . $countryCode);
        $this->importPostcodeFile($countryCode, $postcodeFile, $languageCode);
    }

    private function importCityFile($countryCode, $cityFile, $languageCode)
    {
        $cityData = fopen($cityFile, "r");
        while (!feof($cityData)) {

            $record = fgetcsv($cityData, 0, ';');

            if (isset($record[0])) {

                $city = new City(['language_id' => $languageCode, 'uid' => $countryCode . '-' . $record[0], 'local_name' => $record[1]]);

                if (!$city->save()) {
                    \yii\helpers\VarDumper::dump($city->errors);
                }
            } else {
                break;
            }
        }
    }

    private function importPostcodeFile($countryCode, $postcodeFile, $languageCode)
    {
        $postData = fopen($postcodeFile, "r");
        while (!feof($postData)) {

            $record = fgetcsv($postData, 0, ';');

            if (isset($record[0])) {
                $uid = $countryCode . '-' . $record[0];
                echo $uid;
                $city = City::findOne(['uid' => $uid]);
                if (isset($city)) {
                    $location = new Location(['city_id' => $city->id, 'country_id' => $countryCode, 'postcode' => $record[1]]);
                    if (!$location->save()) {
                        \yii\helpers\VarDumper::dump($location->errors);
                    }
                }
            } else {
                break;
            }
        }
    }

    public function actionDefault($fn)
    {
        if (strlen($fn) != 5) {
            $this->stderror('filename like <country-code>_<language-code> (5 characters - ommit .csv extension)' . "\n");
        }
//if filename is not 5 characters long, throw an error and display filename model

        $fileName = \Yii::getAlias('@data') . "/location/$fn.csv";
        //resolve file url from /location/

        $file = fopen($fileName, "r");

        $countryCode = strtoupper(substr($fn, 0, 2));

        $languageCode = strtoupper(substr($fn, 3, 2));

        $this->stdout('Importing City Data for ' . $countryCode . ' in language ' . $languageCode . "\n");

//fgetcsv($file, 0);

        while (!feof($file)) {

            $record = fgetcsv($file, 0, ';');
            if (isset($record[0])) {
                $city = new City(['language_id' => $languageCode, 'local_name' => $record[1]]);
                if (!$city->save()) {
                    \yii\helpers\VarDumper::dump($city->errors);
                }
                //    (new CityTranslation(['name' => $record[1], 'city_id' => $city->id, 'language_id' => $languageCode]))->save();
                $location = new Location(['city_id' => $city->id, 'country_id' => $countryCode, 'postcode' => $record[0]]);
                if (!$location->save()) {
                    echo $location->city_id . '=' . $location->country_id . '=' . $location->uid . '=' . $location->postcode;
                    \yii\helpers\VarDumper::dump($location->errors);
                }
            } else {
                break;
            }
        }
    }

    /**
     * 
     * @param string $fn Absolute Path to Import Filename, if not set, the system will use a default import file loaded in the data folder in the root of the extension
     * @param type $delimiter
     * @return type
     */
    public function actionCountries($fn = NULL)
    {
        //Set filename to default location
        if (!isset($fn)) {
            $fn = \Yii::getAlias('@vendor') . '/humanized/yii2-location/data/countries/countries.csv';
        }
        $file = fopen($fn, "r");
        while (!feof($file)) {
            $record = fgetcsv($file, 0);
            if (!isset($record[0])) {
                break;
            }
            $country = (new Country(['iso_2' => $record[0], 'iso_3' => $record[1], 'iso_numerical' => $record[2], 'has_postcodes' => $record[3]]));
            $country->save();
        }
    }

    /**
     * 
     */
    public function actionCountryTranslations()
    {
        $fn = \Yii::getAlias('@vendor') . '/humanized/yii2-location/data/countries/countries.json';
        $json = file_get_contents($fn);
        $object = json_decode($json);
        foreach ($object as $record) {
            (new Country(['iso_2' => $record->cca2, 'iso_3' => $record->cca3, 'has_postcodes' => in_array($record->cca2, $this->_without_pc) ? 0 : 1, 'iso_numerical' => $record->ccn3]))->save();
            //English Translation
            (new CountryTranslation(['language_id' => 'en', 'country_id' => $record->cca2, 'official_name' => $record->name->official, 'common_name' => $record->name->common, 'demonym' => $record->demonym]))->save();
            $this->_translateCountryRecord($record);
        }
        return 0;
    }

    private function _translateCountryRecord($record)
    {
        $code = $record->cca2;
        foreach ($record->translations as $key => $translation) {
            (new CountryTranslation(['country_id' => $code, 'language_id' => strtoupper($this->_languages[$key]), 'official_name' => $translation->official, 'common_name' => $translation->common,]))->save();
        }
    }

    /**
     * 
     * Import Filename CSV must be provided in the following format:
     * 
     * [0]=> City Name
     * 
     * [1]=> Postal Code (Empty values will be threated as NULL)
     * 
     * [2]=> Country (iso-2 country code)
     * 
     * @param string $fn Absolute Path to Import Filename, if not set, the system will use a default import file loaded in the data folder in the root of the extension
     * @param type $delimiter
     * @return type
     */
    public function actionLocations($fn, $delimiter = ',')
    {
        $attributes = [
            0 => 'city',
            1 => 'postcode',
            2 => 'country_id'];
        $config = [
            'path' => $fn,
            'delimeter' => $delimiter,
            'saveModel' => Location::className(),
            'attributeMap' => $attributes
        ];
        return $this->importCSV($config);
    }

    public function actionCopyFromSource()
    {
        $chunkSize = 500;
        $chunkPointer = 1;
        $recordCount = \Yii::$app->db->createCommand('SELECT COUNT(*) FROM source')->queryScalar();
        $unit = ($recordCount / 100);

        $this->_msg = 'Processing ' . $recordCount . ' values';
        $base = 'SELECT * FROM source';

        $continue = true;

        while ($continue) {
            $qry = "$base LIMIT $chunkSize OFFSET $chunkPointer";
            echo $qry;

            $records = \Yii::$app->db->createCommand($qry)->queryAll();
            foreach ($records as $record) {

                if ($record['Country'] != 'BE' && $record['Country'] != 'NL') {
                    $location = new Location([
                        'country_id' => strtoupper($record['Country']),
                        'postcode' => $record['Postcode'],
                        'cityName' => $record['City'],
                        'cityLanguage' => $record['Language']
                    ]);

                    echo $location->country_id . '::' . $location->postcode . '::' . $location->cityName . '::' . $location->cityLanguage . "\n";

                    $location->save();
                }
            }
            $this->_msg = ($chunkPointer / $chunkSize) . ' Percent Completed';
            $this->printStatus();
            $chunkPointer += $chunkSize;
            if ($chunkPointer > $recordCount) {
                $continue = false;
            }
        }
        return 0;
    }

    public function actionMatchNuts()
    {
        Location::find()->all();
    }

    public function actionDummyData()
    {
        $countries = \humanized\location\models\location\Country::find()->select('iso_2')->asArray()->all();

        foreach ($countries as $country) {
            $countryId = $country['iso_2'];
            $this->_dummyLocation($countryId, '!UNSET', '0');
            $this->_dummyLocation($countryId, '!UNKNOWN', '-1');
        }
    }

    private function _dummyLocation($countryId, $name, $postCode)
    {
        //CityTranslation::find()->where(['language_id' => 'EN', 'name' => $name])->queryScalar();


        $dummyCity = new \humanized\location\models\location\City(['language_id' => 'EN']);
        try {
            $dummyCity->save();


            try {
                $dummyCityTranslation = new \humanized\location\models\translation\CityTranslation(['language_id' => 'EN', 'city_id' => $dummyCity->id, 'name' => $name]);
                $dummyCityTranslation->save();

                try {
                    $dummyLocation = new \humanized\location\models\location\Location(['postcode' => $postCode, 'city_id' => $dummyCity->id, 'country_id' => $countryId]);
                    $dummyLocation->save();
                } catch (Exception $ex) {
                    
                }
            } catch (Exception $ex) {
                
            }
        } catch (\Exception $ex) {
            
        }
    }

}
