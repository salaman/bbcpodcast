<?php

class Entry extends Eloquent {

    public function programme()
    {
        return $this->belongsTo('Programme');
    }

} 