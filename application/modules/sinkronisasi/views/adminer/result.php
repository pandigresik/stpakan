<?php

if (!empty($data)) {
    echo '<table class="table table-bordered">';
    $index = 0;
    foreach ($data as $d) {
        if (!$index) {
            echo '<thead>';
            echo '<tr>';
            echo '<th><input type="checkbox" onclick="Adminer.checkAll(this)" /></th>';
            foreach ($d as $k => $v) {
                echo '<th>'.word_wrap($k, 25).'</th>';
            }
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
        }
        echo '<tr onclick="Adminer.pilihBaris(this)">';
        echo '<td><input type="checkbox" /></td>';
        foreach ($d as $k => $v) {
            echo '<td>'.word_wrap($v, 25).'</td>';
        }
        ++$index;
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<div class="alert alert-warning">Data tidak ditemukan</div>';
}
