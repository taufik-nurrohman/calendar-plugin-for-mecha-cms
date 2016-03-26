<?php

class Calendar extends Base {

    public static $config = array(
        'query' => 'calendar'
    );

    // output raw calendar data
    public static function __($id = 0, $year = null, $month = null) {
        $results = array();
        $speak = Config::speak();
        $months = (array) $speak->month_names;
        $results['current'] = array(
            'year' => (int) date('Y'),
            'month' => (int) date('m'),
            'day' => (int) date('d')
        );
        $c = $results['current'];
        $q = self::$config['query'];
        if(is_null($year)) $year = $c['year'];
        if(is_null($month)) $month = $c['month'];
        $time = mktime(0, 0, 0, $month, 1, $year);
        $results['year'] = $year;
        $results['month'] = $month;
        $results['id'] = $id;
        $results['title'] = $months[$month - 1] . ' ' . $year;
        $results['url'] = $results['description'] = null;
        $t = strtotime($year . '/' . $month . '/' . $c['day'] . ' 11:11:11');
        $t_c = strtotime($c['year'] . '/' . $c['month'] . '/' . $c['day'] . ' 11:11:11');
        $t_p = strtotime('previous month', $t);
        $t_n = strtotime('next month', $t);
        $d_p = explode('.', date('Y.m.d', $t_p));
        $d_n = explode('.', date('Y.m.d', $t_n));
        $results['prev'] = array(
            'year' => (int) $d_p[0],
            'month' => (int) $d_p[1],
            'day' => (int) $d_p[2],
            'url' => $t_c !== $t_p ? str_replace('&', '&amp;', HTTP::query(array(
                $q . '[' . $id . '][year]' => (int) $d_p[0],
                $q . '[' . $id . '][month]' => (int) $d_p[1]
            ))) : null
        );
        $results['next'] = array(
            'year' => (int) $d_n[0],
            'month' => (int) $d_n[1],
            'day' => (int) $d_n[2],
            'url' => $t_c !== $t_n ? str_replace('&', '&amp;', HTTP::query(array(
                $q . '[' . $id . '][year]' => (int) $d_n[0],
                $q . '[' . $id . '][month]' => (int) $d_n[1]
            ))) : null
        );
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

    public static function year($id = 0, $fallback = null) {
        return Request::get(self::$config['query'] . '.' . $id . '.year', $fallback);
    }

    public static function month($id = 0, $fallback = null) {
        return Request::get(self::$config['query'] . '.' . $id . '.month', $fallback);
    }

    public static function day($id = 0, $fallback = null) {
        return Request::get(self::$config['query'] . '.' . $id . '.day', $fallback);
    }

}