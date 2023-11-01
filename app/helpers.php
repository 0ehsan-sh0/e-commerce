<?php

use Carbon\Carbon;

function generateFileName($image){
    $timestamp = Carbon::now()->timestamp;
    $microSecond = Carbon::now()->microsecond;
    $fileNameImagePrimary = $timestamp . '_' . $microSecond . '_' . $image->getClientOriginalName();
    return $fileNameImagePrimary;
}

function ShamsiToGregorian($shamsiDateTime) {
    // checking if the timestamp is null
    if (!$shamsiDateTime) {
        return null;
    }
    // spliting the year, month and day and time into an array
    $shamsiDate = preg_split("/[-\s]/", $shamsiDateTime);
    // turning into gregorian
    $gregorianDate = verta()->jalaliToGregorian($shamsiDate[0], $shamsiDate[1], $shamsiDate[2]);
    // adding the time
    return implode("-", $gregorianDate) . " " . $shamsiDate[3];
}