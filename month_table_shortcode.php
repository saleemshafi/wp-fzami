<?php

add_shortcode('fzami_month_table', 'fzami_month_table');

function fzami_month_table($atts = array()) {
    ob_start();

    $now = current_time( 'timestamp' );
    $month = date("n", $now);
    $year = date("Y", $now);
    $time_format = null;
    $date_format = 'Y-m-d';
    if ($atts) {
        extract($atts);
    }
    $last_day = date("t", mktime(0,0,0,$month, 1, $year));
?>
    <table class="prayer_table">
        <thead>
            <tr>
                <th class="date">Date</th>
                <th class="prayer">Fajr</th>
                <th class="prayer">Shuruq</th>
                <th class="prayer">Zuhr</th>
                <th class="prayer">Asr</th>
                <th class="prayer">Maghrib</th>
                <th class="prayer">Isha</th>
            </tr>
        </thead>
        <tbody>
<?php
    $pto = new Fzami_PrayerTimes();
    for($day = 1; $day <= $last_day; $day++) {
        $date = mktime(0,0,1, $month, $day, $year);
        $times = $pto->getAzanAndIqamaTimes($date, $time_format);
?>
            <tr<?=fzami_is_today($date)?' class="today"':'' ?>>
                <td class="date"><?= date($date_format, $date) ?></td>
                <td class="prayer">
                    <span class="azan"><?= $times['azan']['fajr'] ?></span>
                    <span class="iqama"><?= $times['iqama']['fajr'] ?></span>
                </td>
                <td class="prayer"><?= $times['azan']['shuruq'] ?></td>
                <td class="prayer">
                    <span class="azan"><?= $times['azan']['zuhr'] ?></span>
                    <span class="iqama"><?= $times['iqama']['zuhr'] ?></span>
                </td>
                <td class="prayer">
                    <span class="azan"><?= $times['azan']['asr'] ?></span>
                    <span class="iqama"><?= $times['iqama']['asr'] ?></span>
                </td>
                <td class="prayer">
                    <span class="azan"><?= $times['azan']['maghrib'] ?></span>
                    <span class="iqama"><?= $times['iqama']['maghrib'] ?></span>
                </td>
                <td class="prayer">
                    <span class="azan"><?= $times['azan']['isha'] ?></span>
                    <span class="iqama"><?= $times['iqama']['isha'] ?></span>
                </td>
            </tr>
<?php } ?>
        </tbody>
    </table>
<?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

function fzami_is_today($date) {
    return date('Y-m-d', $date) == date('Y-m-d', current_time( 'timestamp' ));
}