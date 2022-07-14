<?php

namespace App;

use Carbon\Carbon;
use App\Models\Rota;

class SingleManning
{
    /**
     * @var $shopId
     */
    private $shopId;

    /**
     * @var $dateCommencing
     */
    private $dateCommencing;

    /**
     * SingleManning Class
     * @param int $shopId
     * @param Carbon $dateCommencing
     */
    public function __construct(int $shopId, Carbon $dateCommencing)
    {
        $this->shopId = $shopId;
        $this->dateCommencing = $dateCommencing;
    }

    /**
     * singleManning
     * @param Rota $rota
     * @return array
     */
    public function singleManning()
    {
        $shifts = $this->prepareShifts();

        //calculate the singleManningTime
        $getTotalMinutesForWeek = $this->getTotalMinutesForWeek($shifts);

        return $getTotalMinutesForWeek;
    }

    public function prepareShifts()
    {
        $shifts = $this->shifts()->shifts;
        $shifts = $this->shiftsSortedByDay($shifts);
        $shifts = $this->shiftsGroupByDay($shifts);

        return $shifts;
    }

    public function getTotalMinutesForWeek($shifts)
    {
        $totalMinutesArray = [];

        foreach($shifts as $date => $shift)
        {
            $shiftsCount = $shift->count();

            if($shiftsCount == 1){
                $startTime = Carbon::parse($shift[0]['start_time']);
                $endTime = Carbon::parse($shift[0]['end_time']);
                $totalMinutes = $endTime->diffInMinutes($startTime);
                $totalMinutesArray[$date]= $totalMinutes;
                continue;
            }
            info('**************');
            info($date);

            $allShifts= $shift->toArray();
            $startOfRange = min(array_column($allShifts, 'start_time'));
            $startOfRange = Carbon::parse($startOfRange);

            $endOfRange = max(array_column($allShifts, 'end_time'));
            $endOfRange = Carbon::parse($endOfRange);

            $numberOfMinsInRange = $endOfRange->diffInMinutes($startOfRange);

            info($numberOfMinsInRange);
            $totalMinutes = 0;
            //loop through a days time range
            for ($minute = 0; $minute <= $numberOfMinsInRange; $minute++) {

                info('');
info('$$$$$$$$$$$ current minute - '.$minute.'  $$$$$$$$$$$');
                $startOfTimeUnit = $startOfRange->copy()->addMinutes($minute);
                $endOfTimeUnit = Carbon::parse($startOfTimeUnit)->copy()->addMinute();

                $overLapCountPerMinute = 0;

                foreach($allShifts as $key => $eachShift)
                {
                    $e = $key+1;
                    info('--------person---'.$e.'-------------');
                    $shiftStartTime = Carbon::parse($eachShift['start_time']);
                    $shiftEndTime = Carbon::parse($eachShift['end_time']);
                /*    info('$startOfTimeUnit');
                    info($startOfTimeUnit);
                    info('$endOfTimeUnit');
                    info($endOfTimeUnit);
                    info('$shiftStartTime');
                    info($shiftStartTime);
                    info('$shiftEndTime');
                    info($shiftEndTime);*/

                    $overlap = $this->isShiftOverlappingWithinThisMinute($shiftStartTime,$shiftEndTime,$startOfTimeUnit ,$endOfTimeUnit);
                    //info($overLapCountPerMinute);
                    if($overlap) {
                        //info('overlaps');
                        $overLapCountPerMinute ++;
                    }
                    info('$overLapCountPerMinute a -'.$overLapCountPerMinute);
                    //if more than 1 shift overlaps then this minute will not be counted
                    if($overLapCountPerMinute > 1) {
                        info('$overLapCountPerMinute b -'.$overLapCountPerMinute);
                        //donot add min
                        info('breaking');
                        break;
                    }
                }

                info('$overLapCountPerMinute c -'.$overLapCountPerMinute);
                // if only one shift is overlapping this minute then add it to the total for that day
                if($overLapCountPerMinute == 1) {
                    $totalMinutes = $totalMinutes + 1;
                }
                info('$totalMinutes - '.$totalMinutes);
            }
            $totalMinutesArray[$date]= $totalMinutes;
        }
        return $totalMinutesArray;
    }

    /**
     * getTotalMinutesForWeek
     * @param Collection $shifts
     * @return array
     */
    public function oldgetTotalMinutesForWeek($shifts)
    {
        $dayOfWeek = [0,1,2,3,4,5,6];
        $totalMinutesArray = [];

        foreach($dayOfWeek as $day)
        {
            info($day);

            $shiftsForDay = $shifts[$day];
            info(print_r($shiftsForDay,true));
            if(count($shiftsForDay) == 1){
                $startTime = $shiftsForDay[0]['start_time'];
                $endTime = $shiftsForDay[0]['end_time'];
                $totalMinutes = $endTime->diffInMinutes($startTime);
                $totalMinutesArray[$day]= $totalMinutes;
                continue;
            }

            $startOfRange = min(array_column($shiftsForDay, 'start_time'));
            $endOfRange = max(array_column($shiftsForDay, 'end_time'));
            $numberOfMinsInRange = $endOfRange->diffInMinutes($startOfRange);
            $totalMinutes = 0;

            //loop through a days time range
            for ($minute = 0; $minute <= $numberOfMinsInRange; $minute++) {

                //create a range of mins between the min start time and max end time for the day so
                //we can see if any shifts overlapping for each minute of the range
                $startOfTimeUnit = $startOfRange->addMinutes($minute);
                $endOfTimeUnit = $startOfTimeUnit->addMinute();
                $overLapCountPerMinute = 0;
                foreach($shiftsForDay as $shift)
                {
                    $overlap = $this->isShiftOverlappingWithinThisMinute($shift['start_time'],$shift['end_time'],$startOfTimeUnit ,$endOfTimeUnit);
                    if($overlap) {
                        $overLapCountPerMinute ++;
                    }

                    //if more than 1 shift overlaps then this minute will not be counted
                    if($overLapCountPerMinute > 1) {
                        //donot add min
                        break;
                    }
                }

                // if only one shift is overlapping this minute then add it to the total for that day
                if($overLapCountPerMinute == 1) {
                    $totalMinutes = $totalMinutes + 1;
                }
            }

            //add totalMinutes for Day to array
            $totalMinutesArray[$day]= $totalMinutes;
        }

        return $totalMinutesArray;
    }

    /**
     * Check if staff time is overlapping the unit time
     * @param Carbon $staffStart
     * @param Carbon $staffEnd
     * @param Carbon $unitTimeStart
     * @param Carbon $unitTimeEnd
     * @return bool
     */
    public function isShiftOverlappingWithinThisMinute($shiftStartTime,$shiftEndTime,$startOfTimeUnit ,$endOfTimeUnit) :bool
    {
     info('($startOfTimeUnit) '.$startOfTimeUnit.' >= '.$shiftStartTime.' ($shiftStartTime)');
        info($startOfTimeUnit >= $shiftStartTime);
        info('($endOfTimeUnit) '.$endOfTimeUnit.' <= '.$shiftEndTime.' ($shiftEndTime)');
        info($endOfTimeUnit <= $shiftEndTime);
        return (($startOfTimeUnit >= $shiftStartTime) && ($endOfTimeUnit <= $shiftEndTime)) ? true : false;
    }

    /**
     * getShifts
     * @param Rota $rota
     * @return mixed
     */
    public function shifts()
    {
        return (new Rota())->rotaWithShifts($this->shopId,$this->dateCommencing)->first();
    }

    public function getShopId()
    {
        return $this->shopId;
    }

    public function getDateCommencing()
    {
        return $this->dateCommencing->format('Y-m-d');
    }

    /**
     * shiftsSortedByDay
     * @param Collection $shifts
     * @return mixed
     */
    public function shiftsSortedByDay($shifts)
    {
        $shifts = $shifts->sortBy(function ($shift){
            return Carbon::parse($shift->start_time)->format('Y-m-d h:i:s');
        });
        return $shifts;
    }

    /**
     * shiftsGroupByDay
     * @param $shifts
     * @return mixed
     */
    public function shiftsGroupByDay($shifts)
    {
        $shifts = $shifts->groupBy(function ($shift){
            return Carbon::parse($shift->start_time)->format('Y-m-d');
        });
        return $shifts;
    }
}

