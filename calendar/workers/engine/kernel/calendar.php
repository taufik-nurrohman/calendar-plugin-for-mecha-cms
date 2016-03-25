<?php

class Calendar extends Base {

    // output raw calendar data
    public static function raw($id = 0, $year = null, $month = null) {
        $results = array();
        $speak = Config::speak();
        $months = (array) $speak->month_names;
        $results['current'] = array(
            'year' => (int) date('Y'),
            'month' => (int) date('m'),
            'day' => (int) date('d')
        );
        $c = $results['current'];
        if(is_null($year)) $year = $c['year'];
        if(is_null($month)) $month = $c['month'];
        $time = mktime(0, 0, 0, $month, 1, $year);
        $results['year'] = $year;
        $results['month'] = $month;
        $results['id'] = $id;
        $results['title'] = $months[$month - 1] . ' ' . $year;
        $results['url'] = $results['description'] = null;
        $results['data'] = (array) $speak->day_names;
        for($i = 0, $ii = date('w', $time); $i < $ii; ++$i) {
            $results['data'][] = array(
                'title' => null,
                'current' => false
            );
        }
        for($i = 1, $ii = date('t', $time); $i <= $ii; ++$i) {
            $current = $i === $c['day'] && $month === $c['month'] && $year === $c['year'];
            $results['data'][] = array(
                'title' => $i,
                'current' => $current,
                'description' => $current ? $speak->today : null
            );
        }
        $results['data'] = array_chunk($results['data'], 7);
        $last = array_pop($results['data']);
        $last_count = count($last);
        if($last_count < 7) {
            for($i = 0; $i < 7 - $last_count; ++$i) {
                $last[] = array(
                    'title' => null,
                    'current' => false
                );
            }
        }
        $results['data'][] = $last;
        return $results;
    }

}