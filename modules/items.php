<?php
class Items
{

    protected $db, $itemsTable;
    protected $fields;
    protected $newFieldPrefix;
    protected $editFieldPrefix;

    public function __construct($db)
    {
        $this->db = $db;
        $this->itemsTable = 'items';
        $this->fields = array(
            'name' => array(
                'type' => 'simple-text',
                'maxlength' => '20'
            ),
            'file' => array(
                'type' => 'file',
                'accept' => 'image/*',
                'uploadPath'=> './uploads/'
            )
        );
        $this->newFieldPrefix = 'new-';
        $this->editFieldPrefix = 'edit-';
    }
    public function getAll($id = false)
    {
        if (!$id) {
            return $this->db->query('SELECT * FROM ' . $this->itemsTable)->fetchAll();
        } elseif (!is_int($id)) {
            return array(
                'error' => 'wrong-parameter',
                'message' => 'Paramter should be an integer or left empty'
            );
        } else {
            return $this->db->query('SELECT * FROM ' . $this->itemsTable . ' WHERE id=?', $id)->fetchArray();
        }
    }
    public function add($post ,$files = false)
    {

        $keys = array_keys($post);
        $query = 'INSERT INTO ' . $this->itemsTable . '(';
        $values = ' VALUES(';
        $valuesActual = array();
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            if (strpos($key, $this->newFieldPrefix) === false) {
                return array(
                    'error' => 'wrong-fields-format',
                    'message' => 'Some fields do not contain the correct newFieldPrefix'
                );
            } else {
                    $query .= explode($this->newFieldPrefix, $key)[1] . ',';
                    $values .= '?,';
                    $valuesActual[] = $post[$key];
                }
            
        }
        if($files !== false){
            for ($i=0; $i < count($files); $i++) { 
                $file = $files[array_keys($files)[$i]];
                if(move_uploaded_file($file['tmp_name'],
                $this->fields[explode($this->newFieldPrefix, array_keys($files)[$i])[1]]['uploadPath'] . $file['name'])){

                    $query .= explode($this->newFieldPrefix, array_keys($files)[$i])[1] . ',';
                    $values .= '?,';
                    $valuesActual[] = $file['name'];
                }
            }
        }
        $query = rtrim($query, ' ,') . ')';
        $values = rtrim($values, ' ,') . ')';
        $query = $query . $values;

        $this->db->query($query, $valuesActual);
        return true;
    }

    public function getNewField($fieldIdentifier, $placeholder = false)
    {
        if (is_int($fieldIdentifier)) {
            $fieldName = array_keys($this->fields)[$fieldIdentifier];
        } else {
            if (!isset($this->fields[$fieldIdentifier])) {
                return array(
                    'error' => 'no-field',
                    'message' => 'No such field exists'
                );
            } else {
                $fieldName = $fieldIdentifier;
            }
        }
        $field = $this->fields[$fieldName];
        $output = '';
        switch ($field['type']) {
            case 'simple-text':
                $maxlength = (isset($field['maxlength'])) ? 'maxlength="' . $field['maxlength'] . '"' : '';
                $output = '<input type="text" name="' . $this->newFieldPrefix . $fieldName . '"id="' . $this->newFieldPrefix . $fieldName . '" placeholder="' . (($placeholder !== false) ? $placeholder : '') . '" ' . $maxlength . ' >';
                break;


            case 'simple-number':
                $output = '<input type="number" name="' . $this->newFieldPrefix . $fieldName . '"id="' . $this->newFieldPrefix . $fieldName . '" placeholder="' . (($placeholder !== false) ? $placeholder : '') . '">';
                break;


            case 'textarea':
                $output = '<textarea name="' . $this->newFieldPrefix . $fieldName . '"id="' . $this->newFieldPrefix . $fieldName . '" placeholder="' . (($placeholder !== false) ? $placeholder : '') . '" ></textarea>';
                break;


            case 'ckeditor':
                $output = '<textarea class="ckeditor" name="' . $this->newFieldPrefix . $fieldName . '"id="' . $this->newFieldPrefix . $fieldName . '" placeholder="' . (($placeholder !== false) ? $placeholder : '') . '" ></textarea>';
                break;


            case 'select':
                $output = '<select name="' . $this->newFieldPrefix . $fieldName . '"id="' . $this->newFieldPrefix . $fieldName . '"  >';
                for ($i = 0; $i < count($field['options']); $i++) {
                    $option = $field['options'][$i];
                    $output .= '<option value="' . $option . '">' . $option . '</option>';
                }
                $output .= '</select>';
                break;


            case 'file':
                $accept  = (isset($field['accept'])) ? 'accept="' . $field['accept'] . '"' : '';
                $output = '<input type="file" name="' . $this->newFieldPrefix . $fieldName . '"id="' . $this->newFieldPrefix . $fieldName . '" ' . $accept . '>';
                break;


            default:
                # code...
                break;
        }
        return $output;
    }

    public function getEditField($id,$fieldIdentifier, $placeholder = false, $value = false)
        {
            if (is_int($fieldIdentifier)) {
                $fieldName = array_keys($this->fields)[$fieldIdentifier];
            } else {
                if (!isset($this->fields[$fieldIdentifier])) {
                    return array(
                        'error' => 'no-field',
                        'message' => 'No such field exists'
                    );
                } else {
                    $fieldName = $fieldIdentifier;
                }
            }
            $field = $this->fields[$fieldName];
            $output = '';
            switch ($field['type']) {
                case 'simple-text':
                    $maxlength = (isset($field['maxlength'])) ? 'maxlength="' . $field['maxlength'] . '"' : '';
                    $output = '<input type="text" name="' . $this->editFieldPrefix . $fieldName . '"id="' . $this->editFieldPrefix . $fieldName . '" placeholder="' . (($placeholder !== false) ? $placeholder : '') . '" ' . $maxlength . '  value="' . (($value !== false) ? $value : '') . '">';
                    break;
    
    
                case 'simple-number':
                    $output = '<input type="number" name="' . $this->editFieldPrefix . $fieldName . '"id="' . $this->editFieldPrefix . $fieldName . '" placeholder="' . (($placeholder !== false) ? $placeholder : '') . '" value="' . (($value !== false) ? $value : '') . '">';
                    break;
    
    
                case 'textarea':
                    $output = '<textarea name="' . $this->editFieldPrefix . $fieldName . '"id="' . $this->editFieldPrefix . $fieldName . '" placeholder="' . (($placeholder !== false) ? $placeholder : '') . '" value="' . (($value !== false) ? $value : '') . '"></textarea>';
                    break;
    
    
                case 'ckeditor':
                    $output = '<textarea class="ckeditor" name="' . $this->editFieldPrefix . $fieldName . '"id="' . $this->editFieldPrefix . $fieldName . '" placeholder="' . (($placeholder !== false) ? $placeholder : '') . '"  value="' . (($value !== false) ? $value : '') . '"></textarea>';
                    break;
    
    
                case 'select':
                    $output = '<select name="' . $this->editFieldPrefix . $fieldName . '"id="' . $this->editFieldPrefix . $fieldName . '"  value="' . (($value !== false) ? $value : '') . '">';
                    for ($i = 0; $i < count($field['options']); $i++) {
                        $option = $field['options'][$i];
                        $output .= '<option value="' . $option . '">' . $option . '</option>';
                    }
                    $output .= '</select>';
                    break;
    
    
                case 'file':
                    $accept  = (isset($field['accept'])) ? 'accept="' . $field['accept'] . '"' : '';
                    $output = '<input type="file" name="' . $this->editFieldPrefix . $fieldName . '"id="' . $this->editFieldPrefix . $fieldName . '" ' . $accept . '>';
                    break;
    
    
                default:
                    # code...
                    break;
            }
            return $output;
        }
}
