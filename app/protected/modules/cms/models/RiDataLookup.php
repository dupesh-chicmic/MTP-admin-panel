<?php

class RiDataLookup extends CActiveRecord
{
    public $request_date; // Virtual attribute for formatted date
    public $total_requests; // Virtual attribute for count
    public $date_from; // For date range filter
    public $date_to;   // For date range filter

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ri_data_lookup';
    }

    /* public function rules()
    {
        return array(
            array('request_date, total_requests', 'safe', 'on' => 'search'), // Allow virtual attributes
        );
    } */
    public function rules()
    {
        return array(
            array('request_date, total_requests, date_from, date_to', 'safe', 'on' => 'search'), // Allow filtering attributes
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria();

        // Filtering by request_date range
        if (!empty($this->date_from) && !empty($this->date_to)) {
            $criteria->addCondition("request_date BETWEEN :date_from AND :date_to");
            $criteria->params[':date_from'] = $this->date_from;
            $criteria->params[':date_to'] = $this->date_to;
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10, // Adjust as needed
            ),
            'sort' => array(
                'defaultOrder' => 'request_date DESC',
            ),
        ));
    }
}
