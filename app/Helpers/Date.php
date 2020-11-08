<?php


namespace App\Helpers;


class Date
{
    /**
     *
     * @return array
     *
     * Возвращает массив месяцев.
     */
    public static function months() {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }


    /**
     *
     * @return string
     *
     * Возвращает время в метке Unix: 1544636288, принимает дату: 2017-09-01 00:00:00
     */
    public static function timestampToTime($date)
    {
        if ($date && $date != '0000-00-00 00:00:00') {
            return strtotime($date);
        }
        return '';
    }


    /**
     *
     * @return string
     *
     * Возвращает время в формате: 2017-09-01 00:00:00, принимает дату в метке Unix: 1544636288
     */
    public static function timeToTimestamp(int $date)
    {
        if ($date) {
            return date('Y-m-d H:i:s', $date);
        }
        return '';
    }


    /**
     *
     * @return bool
     *
     * Сравнивает время в промежутке часы минуты, возвращает true или false, например 8:45-17:45.
     * $startTime - время с (8:45).
     * $endTime - время до (17:45).
     * https://ru.stackoverflow.com/questions/615207/Сравнение-времени-php-hm
     */
    public static function timeComparison($startTime, $endTime)
    {
        if ($startTime && $endTime) {
            $start = strtotime(date('Y-m-d') . " {$startTime}");
            $end = strtotime(date('Y-m-d') . " {$endTime}");
            $time = time();
            return $time >= $start && $time <= $end;
        }
        return false;
    }


    /**
     *
     * @return int
     *
     * Возвращает время даты на конец месяца (формат метки Unix: 1544636288).
     * $month -  передать null, будет дата на конец недели.
     */
    public static function timeEndDay($month = true)
    {
        if ($month) {
            // Остаток времени до конца сегодняшнего дня
            $end_day = strtotime('tomorrow') - time();

            // Количество полных дней до конца месяца в time
            $end_month = (date('t') - date('j')) * 86400;

            return time() + $end_day + $end_month;

        } else {

            // Остаток времени до конца сегодняшнего дня
            $end_day = strtotime('tomorrow') - time();

            // Количество полных дней до конца недели в time
            $end_week = (7 - date('N')) * 86400;

            return time() + $end_day + $end_week;
        }
    }


    /**
     *
     * @return array
     *
     * Возвращает в массиве года, от минимального до текущего.
     * $min_year - от какого года начинать.
     * $max_year - необязательный параметр, каким годом заканчивать, по-умолчанию текущий.
     */
    public static function loopDate($min_year, $max_year = null)
    {
        if ($min_year) {
            $min_year = substr((int)$min_year, 0, 4);
            $max_year = $max_year ?: date('Y');
            $max_year = substr((int)$max_year, 0, 4);
            if ($min_year > $max_year) {
                return [];
            }

            while ($min_year <= $max_year) {
                $year[] = $min_year;
                $min_year++;
            }
            return array_reverse($year);
        }
        return [];
    }
}
