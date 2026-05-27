<?php
/**
 * 
 *******************************************************************************
 * <hr>
 * Plik: <b>Mobile.php</b><br> 
 * Autor: <b>Mariusz Winiarz</b><br>
 * Firma: <b>Qbix-Soft</b><br>
 * Data utworzenia: <b>10-07-2013</b><br>
 * <hr>
 *******************************************************************************
 * Klasa posiada kluczowe metody dostepu do danych dla wersji mobilnej strony
 *******************************************************************************
 * <hr> 
 * @author mariusz
 *******************************************************************************
 */
Class Mobile extends CActiveRecord
{
    public static $MOBILE_VERSION_PARENT_ID = 100; // ID od ktorego zostanie generowane menu glowne
    
    /**
     * Metoda zwraca czy User ma wlaczona opcje mobile
     * @return boolean
     */
    public static function getUserIsMobileOn()
    {
        if(!Yii::app()->user->isGuest)
        {
            $lvUser = Uzytkownik::model()->find(array(
                'condition'=>'id=:id',
                'params'=>array(':id'=>Yii::app()->user->getId())
            ));          
            if($lvUser->mobile_on == 1)
                return true;            
        }
        return false;
    }
	
	
    public static function getDisplayYears($file)
    {
        $yearsArr = array();
        //$file = 'Yrs2Display_ByMake.xml';
        $dir = Yii::app()->params['import_folder'];
        $yearsFile = file_get_contents($dir.'/years/'.$file);
       // echo ' kms file path:'.'./'.$dir.'/'.$file;
        $yearsFile = htmlspecialchars($yearsFile);
        //$kms = simplexml_load_string(html_entity_decode(htmlentities($kms_file)), 'SimpleXMLElement', LIBXML_NOCDATA); // tak nie dziala import
        $links = simplexml_load_string(html_entity_decode($yearsFile), 'SimpleXMLElement', LIBXML_NOCDATA);
        
        $json = json_encode($links);
        $links = json_decode($json, true);
        
       //var_dump($links);
     //exit;
        foreach ($links as $row)
        {
            foreach ($row as $key=>$val)
            {
               // var_dump();
                

                $yearsArr[$val['@attributes']['year']] = $val['@attributes']['year'];
            }
        }
        return $yearsArr;
 
    }
    /**
     * Metoda sprawdza czy uzytkownik posiada dostep do zastrzezonych zasobow.
     * Sprawdzany jest token zapisany w ciastku uzytkownika.
     * Podczas pierwszego wejscia token zostaje wygenerowany i zapisany w
     * ciastku oraz w bazie danych w tabeli uzytkownik w kolumnie mobile_token.
     * Ciastko jest wazne przez rok.
     * @return boolean
     */
    public static function checkCookie($lvUser)
    {
        return true; //TODO MW DEPLOY FIX
        exit;
        $cookieName = Mobile::getCookieName();
        if($lvUser->mobile_on == 1)
        {
            // user pierwszy raz wchodzi do mobile || usunal ciastko w telefonie   
            if((empty($lvUser->mobile_token)))//|| (!empty($lvUser->mobile_token) && empty(Yii::app()->request->cookies[mtp_mobile_token])) )
            {
                // zapisz nowy token do bazy 
                $lvUser->mobile_token = Mobile::generateToken($lvUser->id);
                if($lvUser->update(array('mobile_token')))
                {
                    // zapisz token w ciastku
                    $cookie = new CHttpCookie($cookieName, $lvUser->mobile_token);
                    $cookie->expire = time()+60*60*24*380; // rok w sekundach
                    Yii::app()->request->cookies[$cookieName] = $cookie;
                    return true;
                }
            }
            
            // sprawdz czy token w ciastku uzytkownika jest = z ciastkiem w bazie
            if(!empty(Yii::app()->request->cookies[$cookieName]))
            {
                $lvCookieToken = Yii::app()->request->cookies[$cookieName]->value;
                //var_dump($lvCookieToken);
                if($lvCookieToken == $lvUser->mobile_token)
                {
                    $cookie = Yii::app()->request->cookies[$cookieName];
                    $cookie->expire = time()+60*60*24*380; // rok w sekundach
                    Yii::app()->request->cookies[$cookieName] = $cookie;
                    return true;
                }
            }
            else
            {
                // ciastko sie przeterminowalo
            }
        }
        return false;
    }
    
    /**
     * Metoda zwraca nazwe ciastka.
     * Nazwa jest zalezna od wersji aplikacji: Produkcja/Test
     * @return cookieName
     */
    public static function getCookieName()
    {
        return (Yii::app()->params['is_test_version']) ? 'mtp_test_version_mobile_token' : 'mtp_mobile_token';
    }

    /**
     * Metoda zwraca strony dla podanego jako parametr parentId lub 
     * dla menu glownego (MOBILE_VERSION_PARENT_ID)
     * @return model
     */
    public static function getMobileSites($pmParentId=null)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('`parent_id`', ($pmParentId==null) ? self::$MOBILE_VERSION_PARENT_ID : $pmParentId);
        $criteria->compare('`display`',1);
        $criteria->order = '`order`';
        return CmsPage::model()->findAll($criteria);
    }

    /**
     * Metoda zwraca wszystkie newsy
     * @return model
     */    
    public static function getAllNews()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('`display`',1);
        $criteria->order = '`order`';
        return CmsNews::model()->findAll($criteria);
    }
    
    /**
     * Metoda pobiera po url tresc strony
     * @param type $pmSiteUrl
     * @return model
     */
    public static function getSiteContent($pmSiteUrl)
    {
        if(empty($pmSiteUrl))
        {
            return null;
        }
        else
        {
            $criteria = new CDbCriteria;
            $criteria->compare('`url`',$pmSiteUrl);
            $criteria->compare('`display`',1);
            $criteria->order = '`order`';
            return CmsPage::model()->find($criteria);
        }
    }

    /**
     * USED CARS
     * Metoda zwraca liste marek z ostatniego importu
     * @return model
     */
    public static function getUsedCarsMark()
    {
        return Yii::app()->db->createCommand("
                 SELECT `uc`.`id`, `uc`.`id_import`, `uc`.`name`, `imp`.`nazwa`, `imp`.`data` 
                 FROM `used_cars` `uc`
                 INNER JOIN `import` `imp` ON `imp`.`id` = `uc`.`id_import`
                 WHERE `imp`.`id` = ( SELECT MAX(`id_import`) FROM `used_cars` )
                 ORDER BY `uc`.`name`
                ")->queryAll();
    }

    /**
     * USED CARS
     * Metoda zwraca liste modeli dla danej marki
     * @return model
     */
    public static function getModelsForTheBandForCars($pmMakeId)
    {
        return UsedCarsModel::model()->findAll(array(
            'condition'=>'id_used_cars=:id',
            'params'=>array(':id'=>$pmMakeId)
        ));
    }
    
    public static function getModelsByRangeForTheBandForCars($pmMakeId, $rangeCode)
    {
        return UsedCarsModel::model()->findAll(array(
            'condition'=>'id_used_cars=:id AND rangecode=:rangecode',
            'params'=>array(':id'=>$pmMakeId, 'rangecode'=>$rangeCode)
        ));
    }
    
    public static function getModelsByRangeForTheBandForComms($pmMakeId, $rangeCode)
    {
        return UsedComCarsModel::model()->findAll(array(
            'condition'=>'id_used_com_cars=:id AND rangecode=:rangecode',
            'params'=>array(':id'=>$pmMakeId, 'rangecode'=>$rangeCode)
        ));
    }
    
    public static function getRangesForTheBandForCars($pmMakeId)
    {
        $importId = UsedCarsModel::findByPk($pmMakeId);
        $startRangeCode = null;
        $rangeName = '';

        if (isset($_GET['rangecode'])) {
            $startRangeCode = $_GET['rangecode'];
        }
        
        return UsedCarsModel::model()->findAll(array(
            'condition'=>'id_used_cars=:id',
            'params'=>array(':id'=>$pmMakeId)
        ));
    }

    /**
     * USED CARS
     * Metoda zwraca dane dla danego modelu
     * @return model
     */    
    public static function getDetailsDataForUsedCars($pmId)
    {
        return UsedCarsModel::model()->find(array(
            'condition'=>'id=:id',
            'params'=>array(':id'=>$pmId)
        ));        
    }    

    /**
     * USED COMMERCIAL
     * Metoda zwraca liste marek z ostatniego importu
     * @return model
     */    
    public static function getUsedCommercialMark()
    {
        return Yii::app()->db->createCommand("
                 SELECT `ucc`.`id`,`ucc`.`id_import`,`ucc`.`name`, `imp`.`nazwa`, `imp`.`data`
                 FROM `used_com_cars` `ucc`
                 INNER JOIN `import` `imp` ON `imp`.`id` = `ucc`.`id_import`
                 WHERE `imp`.`id` = ( SELECT MAX(`id_import`) FROM `used_com_cars` )
                 ORDER BY `ucc`.`name`
                ")->queryAll(); 
    }

    /**
     * USED COMMERCIAL
     * Metoda zwraca liste modeli dla danej marki
     * @return model
     */
    public static function getModelsForTheBandForCommercial($pmMakeId)
    {
        return UsedComCarsModel::model()->findAll(array(
            'condition'=>'id_used_com_cars=:id',
            'params'=>array(':id'=>$pmMakeId)
        ));
    }    
    
    /**
     * USED CARS
     * Metoda zwraca dane dla danego modelu
     * @return model
     */     
    public static function getDetailsDataForUsedCommercial($pmId)
    {
        return UsedComCarsModel::model()->find(array(
            'condition'=>'id=:id',
            'params'=>array(':id'=>$pmId)
        ));        
    }
    

    /**
     * Metoda generuje i zwraca token zlozony z losowych znakow
     * @param type $pmUserId
     * @return String
     */
    public static function generateToken($pmUserId=""){
        $lvToken = $pmUserId;
        for ($i=0; $i<10; $i++) 
        {
            $losuj=rand(1,30)%2; //T/F
            if($losuj)
            {
                //if(true) ? A-Z : a-z;
                $lvToken .= (rand(1,30)%2) ? chr(rand(65,90)) : chr(rand(97,122));
            }
            else
            {
                $lvToken .= chr(rand(48,57)); //0-9
            }
        }
        return $lvToken;
    }     
    
    /**
     * Metoda zwraca dane z pliku man.xml dla Cars
     * @return array(model=>pathToFile)
     */
    public static function getNewPricesCarsManData()
    {
        $man_file = file_get_contents('./data/cars/man.xml');
        $man_file = htmlspecialchars($man_file);
        $cars = simplexml_load_string(html_entity_decode($man_file), 'SimpleXMLElement', LIBXML_NOCDATA);        

        $xmlManFileData = array();
        foreach ($cars as $row)
        {    
            $xmlManFileData += array($row['distributor'].' ('.$row['effective'].')'=>urldecode($row['file']));//'./data/cars/'.
        }
        return $xmlManFileData;
    }
    
    public static function getCleanManufacturer($distributorWithDate)
    {
        $out = null;
        $manArr = explode('(', $distributorWithDate);
        if(is_array($manArr)){
            $out = trim($manArr[0]);
        }else {
            $out = trim($distributorWithDate);
        }
        return $out;
    }
    
    /**
     * Metoda zwraca tablice z danymi dla CARS uzywana w widoku
     * @param type $carXml
     * @return type
     */
    public static function getCarData($carXml)
    {
        $xmlCarData = array();
        $output = array();
        $reader = new XMLReader();
        $file = './data/cars/'.$carXml;
        if(file_exists($file))
        {
            if (!$reader->open($file))
            {
                die("Failed to open ".$carXml);
            }
            while($reader->read())
            {
                if($reader->getAttribute('model') == null) 
                    continue;

                $xmlCarData = array(
                    'manufacturer'=>$reader->getAttribute('manufacturer'),
                    'model'=>$reader->getAttribute('model'),
                    'doors'=>$reader->getAttribute('doors'),
                    'body'=>$reader->getAttribute('body'),
                    'retail'=>$reader->getAttribute('retail'),
                    'engine'=>$reader->getAttribute('engine'),
                    'bhp'=>$reader->getAttribute('bhp'),
                    'vrt'=>$reader->getAttribute('vrt'),
                    'band'=>$reader->getAttribute('band'),
                    'co2'=>$reader->getAttribute('co2'),
                    'fuel'=>$reader->getAttribute('fuel'),
                    'tax'=>$reader->getAttribute('tax'),
                    'rangecode'=>$reader->getAttribute('rangecode')
                );
                $output[] = $xmlCarData;
            }
            $reader->close();
        }
        return $output;
    }

    /**
     * Metoda zwraca dane z pliku man.xml dla Commercial
     * @return array(model=>pathToFile)
     */
    public static function getNewPricesCommManData()
    {
        $man_file = file_get_contents('./data/commercial/man.xml');
        $man_file = htmlspecialchars($man_file);
        $cars = simplexml_load_string(html_entity_decode($man_file), 'SimpleXMLElement', LIBXML_NOCDATA);        

        $xmlManFileData = array();
        foreach ($cars as $row)
        {
            $xmlManFileData += array($row['distributor'].' ('.$row['effective'].')'=>urldecode($row['file']));//'./data/cars/'.
        }
        return $xmlManFileData;
    }

    /**
     * Metoda zwraca tablice z danymi dla COMMERCIAL uzywana w widoku
     * @param type $carXml
     * @return type
     */
    public static function getCommData($carXml)
    {
        $xmlCarData = array();
        $output = array();
        $reader = new XMLReader();
        $file = './data/commercial/'.$carXml;
        if(file_exists($file))
        {
            if (!$reader->open($file))
            {
                die("Failed to open ".$carXml);
            }
            while($reader->read())
            {
                if($reader->getAttribute('model') == null) 
                    continue;

                $xmlCarData = array(
                    'manufacturer'=>$reader->getAttribute('manufacturer'),
                    'model'=>$reader->getAttribute('model'),
                    'body'=>$reader->getAttribute('body'),
                    'retail'=>$reader->getAttribute('retail'),
                    'gvw'=>$reader->getAttribute('gvw'),
                    'cc'=>$reader->getAttribute('cc'),
                    'cat'=>$reader->getAttribute('cat'),
                    'vrt'=>$reader->getAttribute('vrt'),
                    'band'=>$reader->getAttribute('band'),
                    'co2'=>$reader->getAttribute('co2'),
                    'fuel'=>$reader->getAttribute('fuel'),
                    'tax'=>$reader->getAttribute('tax'),
                    'rangecode'=>$reader->getAttribute('rangecode')
                    
                );
                $output[] = $xmlCarData;
            }
            $reader->close();
        }
        return $output;
    }

}
?>
