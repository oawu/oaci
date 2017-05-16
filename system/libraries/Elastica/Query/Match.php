<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Elastica_Query_Match extends Elastica_Query_Abstract
{

    public function __construct(array $term = array())
    {
        $this->setRawTerm($term);
    }

    public function setRawTerm(array $term)
    {
        return $this->setParams($term);
    }

    public function setMatch($key, $value)
    {
        return $this->setRawTerm(array($key => $value));
    }
}
