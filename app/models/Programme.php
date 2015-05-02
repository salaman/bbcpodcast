<?php

class Programme extends Eloquent {

    public function entries()
    {
        return $this->hasMany('Entry');
    }

}