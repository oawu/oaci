<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */
class OaModel extends ActiveRecordModel {

  public function __construct ($attributes = array (), $guard_attributes = TRUE, $instantiating_via_find = FALSE, $new_record = TRUE) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public static function addConditions (&$conditions, $str) {
    $args = array_filter (func_get_args (), function ($t) {
      return $t !== null;
    });
    $args = array_splice ($args, !isset ($conditions) ? 1 : 2);

    if (!isset($conditions) || !array_filter($conditions))
      $conditions = array();

    if (!$conditions)
      $conditions[0] = '(' . $str . ')';
    else
      $conditions[0] .= ' AND (' . $str . ')';

    foreach ($args as $arg)
      if ($arg !== null)
        array_push ($conditions, $arg);
  }
  public function backup ($has = false) {
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var,
      ) : $var;
  }
  protected function getBackup () {
    $that = $this;
    return array_combine ($k = array_keys ($this->table ()->columns), array_map (function ($u) use ($that) {
      switch (gettype ($that->$u)) {
        case 'integer': case 'string': case 'double':
          return $that->$u; break;
        
        default:
          if ($that->$u instanceof OrmUploader) return (string) $that->$u;
          if ($that->$u instanceof ActiveRecord\DateTime) return (string) $that->$u->format ('Y-m-d H:i:s');
          if ($that->$u === null) return null;
          
          var_dump ($u, get_class ($that->$u));
          exit ();  
          break;
      }
    }, $k));
  }
  protected function subBackup ($model, $has = false, $foreignKey = null) {
    if (!$foreignKey) $foreignKey = ActiveRecord\Utils::singularize ($this::$table_name) . ($this->table ()->pk ? '_' . $this->table ()->pk[0] : '');
    if (!isset ($model::table ()->columns[$foreignKey])) return array ();
    return array_map (function ($t) use ($has) {
      return method_exists ($t, 'backup') ? $t->backup ($has) : $t::table ()->columns;
    }, $model::find ('all', array ('conditions' => array ($foreignKey . ' = ?', $this->id))));
  }
}