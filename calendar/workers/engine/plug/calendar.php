<?php

// Calendar::hook('foo', function($lot) { ... });
Calendar::plug('hook', function($id = 0, $fn = 10, $stack = 10) {
    if(is_callable($id)) {
        $stack = $fn;
        $fn = $id;
        $id = "";
    } else {
        $id = ':' . $id;
    }
    Filter::add('calendar' . $id, $fn, $stack);
});

// Calendar::date('2016/4/21', array( ... ), 'foo');
// Calendar::date('2016/4', array( ... ), 'foo');
// Calendar::date('2016', array( ... ), 'foo');
Calendar::plug('date', function($date, $data, $id = null) {
    if(is_null($id)) {
        $id = "";
    } else {
        $id = ':' . $id;
    }
    Filter::add('calendar' . $id, function($lot, $year, $month) use($date, $data) {
        $s = explode('/', $date);
        if(count($s) === 1) {
            if($year === (int) $s[0]) {
                $lot = array_merge($lot, (array) $data);
            }
        } else if(count($s) === 2) {
            if($year === (int) $s[0] && $month === (int) $s[1]) {
                $lot = array_merge($lot, (array) $data);
            }
        } else {
            $lot[$s[0] . '/' . $s[1] . '/' . $s[2]] = $data;
        }
        return $lot;
    }, 1);
});