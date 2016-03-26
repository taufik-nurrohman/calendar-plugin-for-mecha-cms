<?php

// Widget::calendar('foo');
Widget::add('calendar', function($id = 0) use($config) {
    $year = Calendar::year($id, null);
    $month = Calendar::month($id, null);
    $T1 = TAB;
    $T2 = str_repeat(TAB, 2);
    $T3 = str_repeat(TAB, 3);
    $T4 = str_repeat(TAB, 4);
    $cc = ' ' . Widget::$config['classes']['current'];
    $C = Calendar::__($id, $year, $month);
    $C = Filter::apply(array('calendar:' . $id, 'calendar'), $C, $C['year'], $C['month'], $id);
    $current = $C['year'] === $C['current']['year'] && $C['month'] === $C['current']['month'] ? $cc : "";
    $kind = $kind = isset($C['kind']) ? ' kind-' . implode(' kind-', (array) $C['kind']) : "";
    $html  = $T1 . '<table class="calendar calendar-' . $id . $current . $kind . '" id="calendar-' . $id . '">' . NL;
    $current = $C['month'] === $C['current']['month'] ? $cc : "";
    $html .= $T2 . '<caption class="month month-' . $C['month'] . $current . $kind . '">';
    $html .= '<a href="' . ($C['prev']['url'] ? $C['prev']['url'] : $config->url_current) . '" rel="prev">&#9666;</a>&nbsp;';
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
    $html .= '&nbsp;<a href="' . ($C['next']['url'] ? $C['next']['url'] : $config->url_current) . '" rel="next">&#9656;</a>';
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
            if(isset($hook['title'])) {
                if(is_callable($hook['title'])) {
                    $hook['title'] = call_user_func($hook['title'], $vv, $C['data']);
                } else {
                    $hook['title'] = sprintf($hook['title'], $vv['title']);
                }
            }
            $vv = array_merge($vv, $hook);
            unset($C[$h]);
            $s = $vv['title'] ? $vv['title'] : "";
            $current = $vv['current'] ? $cc : "";
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