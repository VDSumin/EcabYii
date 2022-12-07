<?php

/**
 * Parent for Filters
 */
class FilterForm extends CFormModel
{
    /**
     * @var array filters, key => filter string
     */
    public $filters = array();

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->filters)) {
            $this->filters[$name] = '';
        }
        return $this->filters[$name];
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        $this->filters[$name] = $value;
    }

    /**
     * Filter input array by key value pairs
     * @param array $data rawData
     * @return array filtered data array
     * @throws CException
     */
    public function arrayFilter(array &$data)
    {
        foreach ($data as $rowIndex => &$row) {
            foreach ($this->filters as $key => &$searchValue) {
                if (!is_null($searchValue) && $searchValue !== '') {
                    $compareValue = null;

                    if (!strstr($key, '.')) {
                        if ($row instanceof CModel) {
                            if (!isset($row->$key)) {
                                throw new CException("Property " . get_class($row) . "::{$key} does not exist!");
                            }
                            $compareValue = $row->$key;
                        } elseif (is_array($row)) {
                            if (!array_key_exists($key, $row)) {
                                throw new CException("Key {$key} does not exist in array!");
                            }
                            $compareValue = $row[$key];
                        } else {
                            throw new CException("Data in CArrayDataProvider must be an array of arrays or an array of CModels!");
                        }

                        if (mb_stripos($compareValue, $searchValue, 0, Yii::app()->charset) === false) {
                            unset($data[$rowIndex]);
                            break;
                        }
                    }
                }
            }
        }
        return array_merge($data);
    }

}
