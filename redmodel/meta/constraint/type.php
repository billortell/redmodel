<?php
/**
    'Type' Constraint 
    
    constrains a field to a particular 'type.'
*/
class RedModel_Meta_Constraint_Type extends RedModel_Meta_Constraint {
  
  /**
     Check the field's validity.
     @return true if valid, false if not.
  */
  public function check () {
    $cval = $this->value;         // constraint value
    $fval = $this->field->value;  // field value
    $modelName = $this->field->model->name;
    $fieldName = $this->field->name;
    
    // if the first character is uppercased, it's a class name
    if ($cval{0} === strtoupper($cval{0})) {
      $cval = 'int';
    }
    
    switch ($cval) {
      case 'numeric': case 'number': case 'decimal': case 'real': case 'float':  case 'double': 
        return $this->dispatch(is_numeric($fval),
          "{$this->field->title} must be numeric");
      case 'int': case 'integer': case 'integral':
        return $this->dispatch(is_numeric($fval) && ($fval == round($fval)),
          "{$this->field->title} must be a whole number");
      case 'date':
        $d=date_parse($fval);
        $ok = $d['year'] && $d['month'];

        if ($ok) { 
          $date = date_create("{$d['year']}-{$d['month']}-{$d['day']}");
          $date = date_format($date, 'Y-m-d H:i:s');
        }
        $this->field->model->bean->$fieldName = $date;
        
        // print_r($this->field->model->bean->$fieldName);
        return $this->dispatch($ok, "{$this->field->title} must be a valid date");
      case 'time':
        $d=date_parse($fval);
        return $this->dispatch($d['hour'],
          "{$this->field->title} must be a valid time");
      case 'string': case 'text':
        return $this->dispatch($fval === "$fval",
          "{$this->field->title} must be a string");
      default:
        throw new RedModel_Exception("Unknown type constraint '$cval'.");
    }
  }
}

