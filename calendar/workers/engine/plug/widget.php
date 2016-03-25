<?php

// Widget::calendar('foo');
Widget::add('calendar', function($id = 0) use($config) {
    $year = Request::get('calendar.' . $id . '.year', null);
    $month = Request::get('calendar.' . $id . '.month', null);
    $T1 = TAB;
    $T2 = str_repeat(TAB, 2);
    $T3 = str_repeat(TAB, 3);
    $T4 = str_repeat(TAB, 4);
    $C = Calendar::raw($id, $year, $month);
    $C = Filter::apply(array('calendar:' . $id, 'calendar'), $C, $C['year'], $C['month'], $id);
    $html  = $T1 . '<table class="calendar calendar-' . $id . ($C['year'] === $C['current']['year'] && $C['month'] === $C['current']['month'] ? ' current' : "") . '" id="calendar-' . $id . '">' . NL;
    $html .= $T2 . '<caption class="month month-' . $C['month'] . ($C['month'] === $C['current']['month'] ? ' current' : "") . '">';
    $time = strtotime($C['year'] . '/' . $C['month'] . '/1 11:11:11');
    $time_c = strtotime($C['current']['year'] . '/' . $C['current']['month'] . '/1 11:11:11');
    $time_p = strtotime('previous month', $time);
    $time_n = strtotime('next month', $time);
    $url_prev = $time_p !== $time_c ? str_replace('&', '&amp;', HTTP::query(array('calendar[' . $id . '][year]' => (int) date('Y', $time_p), 'calendar[' . $id . '][month]' => (int) date('m', $time_p)))) : $config->url_current;
    $url_next = $time_n !== $time_c ? str_replace('&', '&amp;', HTTP::query(array('calendar[' . $id . '][year]' => (int) date('Y', $time_n), 'calendar[' . $id . '][month]' => (int) date('m', $time_n)))) : $config->url_current;
    $html .= '<a href="' . $url_prev . '">&#9666;</a>&nbsp;';
    $html .= '<strong>';
    if(isset($C[$C['year'] . '/' . $C['month']])) {
        $C = array_merge($C, (array) $C[$C['year'] . '/' . $C['month']]);
        unset($C[$C['year'] . '/' . $C['month']]);
    } else if(isset($C[$C['year']])) {
        $C = array_merge($C, (array) $C[$C['year']]);
        unset($C[$C['year']]);
    }
    if(isset($C['url'])) {
        $html .= '<a href="' . $C['url'] . '"' . (isset($C['description']) ? ' title="' . Text::parse($C['description'], '->text') . '"' : "") . '>' . $C['title'] . '</a>';
    } else if(isset($C['description'])) {
        $html .= '<span class="a"' . ($C['description'] ? ' title="' . Text::parse($C['description'], '->text') . '"' : "") . '>' . $C['title'] . '</span>';
    } else {
        $html .= $C['title'];
    }
    $html .= '</strong>';
    $html .= '&nbsp;<a href="' . $url_next . '">&#9656;</a>';
    $html .= '</caption>' . NL;
    $html .= $T2 . '<thead>' . NL;
    $html .= $T3 . '<tr class="week week-0">' . NL;
    foreach($C['data'][0] as $k => $v) {
        $html .= $T3 . '<th class="day day-' . ($k + 1) . '" title="' . $v . '">' . substr(strtoupper($v), 0, 1) . '</th>' . NL;
    }
    $html .= $T3 . '</tr>' . NL;
    $html .= $T2 . '</thead>' . NL;
    $html .= $T2 . '<tbody>' . NL;
    unset($C['data'][0]);
    foreach($C['data'] as $k => $v) {
        $html .= $T3 . '<tr class="week week-' . $k . '">' . NL;
        foreach($v as $kk => $vv) {
            $h = $C['year'] . '/' . $C['month'] . '/' . $vv['title'];
            $hook = isset($C[$h]) ? $C[$h] : array();
            $vv = array_merge($vv, $hook);
            unset($C[$h]);
            $s = $vv['title'] ? $vv['title'] : "";
            $current = $vv['current'] ? ' current' : "";
            $active = isset($vv['url']) || isset($vv['description']) && $vv['description'] !== $config->speak->today ? ' active' : "";
            $kind = isset($vv['kind']) ? ' kind-' . implode(' kind-', (array) $vv['kind']) : "";
            $html .= $T4 . '<td class="date date-' . ($s ? $s : 0) . ' day-' . ($kk + 1) . $current . $active . $kind . '">';
            if(isset($vv['url'])) {
                $html .= '<a href="' . $vv['url'] . '"' . (isset($vv['description']) ? ' title="' . Text::parse($vv['description'], '->text') . '"' : "") . '>' . $s . '</a>';
            } else if(isset($vv['description'])) {
                    $html .= '<span class="a"' . ($vv['description'] ? ' title="' . Text::parse($vv['description'], '->text') . '"' : "") . '>' . $s . '</span>';
            } else {
                $html .= $s;
            }
            $html .= '</td>' . NL;
        }
        $html .= $T3 . '</tr>' . NL;
    }
    $html .= $T2 . '</tbody>' . NL;
    return $html . $T1 . '</table>';
}, true);