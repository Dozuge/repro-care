<?php

namespace App\Services;

use App\Models\Cycle;
use Carbon\Carbon;

class CyclePredictionService
{
    /**
     * Predict the next period date based on cycle history
     *
     * @param int $userId
     * @return Carbon|null
     */
    public function predictNextPeriod(int $userId): ?Carbon
    {
        $cycles = $this->getRecentCycles($userId, 6);

        if ($cycles->isEmpty()) {
            return null;
        }

        $averageCycle = $this->calculateAverageCycleLength($cycles);
        $lastCycle = $cycles->first();

        return $lastCycle->period_start_date->copy()->addDays($averageCycle);
    }

    /**
     * Predict multiple future periods with fertility information
     *
     * @param int $userId
     * @param int $count
     * @return array
     */
    public function predictFuturePeriods(int $userId, int $count = 3): array
    {
        $cycles = $this->getRecentCycles($userId, 6);

        if ($cycles->isEmpty()) {
            return [];
        }

        $averageCycle = $this->calculateAverageCycleLength($cycles);
        $averagePeriod = $this->getAveragePeriodLength($userId);
        $nextPeriod = $this->predictNextPeriod($userId);

        if (!$nextPeriod) {
            return [];
        }

        $predictions = [];
        
        for ($i = 0; $i < $count; $i++) {
            $start = $nextPeriod->copy()->addDays($i * $averageCycle);
            $end = $start->copy()->addDays($averagePeriod - 1);
            
            // Calculate ovulation (14 days before next period)
            $ovulation = $start->copy()->addDays($averageCycle - 14);
            $fertileWindowStart = $ovulation->copy()->subDays(5);
            $fertileWindowEnd = $ovulation->copy()->addDays(1);
            
            $predictions[] = [
                'period_start' => $start,
                'period_end' => $end,
                'cycle_number' => $i + 1,
                'ovulation' => $ovulation,
                'fertile_start' => $fertileWindowStart,
                'fertile_end' => $fertileWindowEnd,
            ];
        }

        return $predictions;
    }

    /**
     * Calculate average cycle length from cycle history
     *
     * @param int $userId
     * @param int $maxCycles
     * @return int|null
     */
    public function getAverageCycleLength(int $userId, int $maxCycles = 6): ?int
    {
        $cycles = $this->getRecentCycles($userId, $maxCycles);

        if ($cycles->count() < 1) {
            return null;
        }

        $cycleLengths = $cycles->pluck('cycle_length')->filter();

        if ($cycleLengths->isEmpty()) {
            return 28; // Default
        }

        return (int) round($cycleLengths->avg());
    }

    /**
     * Calculate average period length
     *
     * @param int $userId
     * @param int $maxCycles
     * @return int
     */
    public function getAveragePeriodLength(int $userId, int $maxCycles = 6): int
    {
        $cycles = Cycle::where('user_id', $userId)
            ->whereNotNull('period_end_date')
            ->orderBy('period_start_date', 'desc')
            ->take($maxCycles)
            ->get();

        if ($cycles->isEmpty()) {
            return 5; // Default
        }

        $totalDays = $cycles->sum(function ($cycle) {
            return $cycle->period_length ?? 0;
        });

        return (int) round($totalDays / $cycles->count());
    }

    /**
     * Get cycle regularity status
     *
     * @param int $userId
     * @return string
     */
    public function getCycleRegularity(int $userId): string
    {
        $cycles = $this->getRecentCycles($userId, 6);

        if ($cycles->count() < 3) {
            return 'insufficient_data';
        }

        $cycleLengths = $cycles->pluck('cycle_length')->filter();

        if ($cycleLengths->count() < 3) {
            return 'insufficient_data';
        }

        $variance = $this->calculateVariance($cycleLengths->toArray());

        if ($variance <= 3) {
            return 'regular';
        } elseif ($variance <= 7) {
            return 'somewhat_irregular';
        } else {
            return 'irregular';
        }
    }

    /**
     * Get complete cycle statistics
     *
     * @param int $userId
     * @return array
     */
    public function getCycleStatistics(int $userId): array
    {
        $cycles = $this->getRecentCycles($userId, 12);
        $nextPeriod = $this->predictNextPeriod($userId);
        $futurePeriods = $this->predictFuturePeriods($userId, 3);

        return [
            'total_cycles_recorded' => Cycle::where('user_id', $userId)->count(),
            'average_cycle_length' => $this->getAverageCycleLength($userId),
            'average_period_length' => $this->getAveragePeriodLength($userId),
            'shortest_cycle' => $cycles->min('cycle_length'),
            'longest_cycle' => $cycles->max('cycle_length'),
            'regularity' => $this->getCycleRegularity($userId),
            'next_predicted_period' => $nextPeriod?->format('Y-m-d'),
            'days_until_next' => $nextPeriod ? round(Carbon::now()->diffInDays($nextPeriod, false)) : null,
            'future_predictions' => $futurePeriods,
            'recent_cycles' => $cycles->take(6),
        ];
    }

    /**
     * Get calendar data with periods and predictions
     *
     * @param int $userId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getCalendarData(int $userId, int $year, int $month): array
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Get cycles overlapping with this month
        $cycles = Cycle::where('user_id', $userId)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('period_start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('period_end_date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('period_start_date', '<=', $startOfMonth)
                            ->where('period_end_date', '>=', $endOfMonth);
                    });
            })
            ->orderBy('period_start_date', 'asc')
            ->get();

        // Get predictions for future periods
        $predictions = $this->predictFuturePeriods($userId, 3);

        // Add current cycle ovulation and fertile window if cycle exists in this month
        $currentCyclePrediction = null;
        if ($cycles->isNotEmpty()) {
            $lastCycle = $cycles->last();
            $averageCycle = $this->getAverageCycleLength($userId);
            if ($averageCycle && $lastCycle->period_end_date) {
                // Simple ovulation calculation (14 days before next period)
                $ovulation = $lastCycle->period_start_date->copy()->addDays($averageCycle - 14);
                $fertileWindowStart = $ovulation->copy()->subDays(5);
                $fertileWindowEnd = $ovulation->copy()->addDays(1);
                
                $currentCyclePrediction = [
                    'period_start' => $lastCycle->period_start_date,
                    'period_end' => $lastCycle->period_end_date,
                    'ovulation' => $ovulation,
                    'fertile_start' => $fertileWindowStart,
                    'fertile_end' => $fertileWindowEnd,
                ];
            }
        }

        // Generate calendar days
        $calendarDays = $this->generateCalendarDays($cycles, $predictions, $year, $month, $currentCyclePrediction);

        return [
            'cycles' => $cycles,
            'predictions' => $predictions,
            'current_cycle_prediction' => $currentCyclePrediction,
            'calendar_days' => $calendarDays,
            'average_cycle_length' => $this->getAverageCycleLength($userId),
            'average_period_length' => $this->getAveragePeriodLength($userId),
        ];
    }

    /**
     * Generate calendar days with period and prediction data
     *
     * @param \Illuminate\Database\Eloquent\Collection $cycles
     * @param array $predictions
     * @param int $year
     * @param int $month
     * @param array|null $currentCyclePrediction
     * @return array
     */
    private function generateCalendarDays($cycles, array $predictions, int $year, int $month, $currentCyclePrediction = null): array
    {
        $calendarDays = [];
        $firstDayOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        $current = $firstDayOfMonth->copy()->subDay($firstDayOfMonth->dayOfWeek);
        
        while ($current <= $lastDayOfMonth->copy()->addDay(6 - $lastDayOfMonth->dayOfWeek)) {
            $dayData = [
                'date' => $current->copy(),
                'is_current_month' => $current->month == $month,
                'is_today' => $current->isToday(),
                'period' => false,
                'predicted_period' => false,
                'ovulation' => false,
                'fertile' => false,
                'non_fertile' => false,
            ];
            
            // Check if this day is in a recorded period
            foreach ($cycles as $cycle) {
                $periodEnd = $cycle->period_end_date ?? $cycle->period_start_date;
                if ($current->between($cycle->period_start_date, $periodEnd)) {
                    $dayData['period'] = true;
                    break;
                }
            }
            
            // Check if this day is in a predicted period or fertile window
            foreach ($predictions as $prediction) {
                // Check predicted period
                if (isset($prediction['period_start']) && isset($prediction['period_end'])) {
                    if ($current->between($prediction['period_start'], $prediction['period_end'])) {
                        $dayData['predicted_period'] = true;
                        break;
                    }
                }
                
                // Check ovulation
                if (isset($prediction['ovulation']) && $current->equalTo($prediction['ovulation'])) {
                    $dayData['ovulation'] = true;
                }
                
                // Check fertile window
                if (isset($prediction['fertile_start']) && isset($prediction['fertile_end'])) {
                    if ($current->between($prediction['fertile_start'], $prediction['fertile_end'])) {
                        $dayData['fertile'] = true;
                    }
                }
                
                // Check non-fertile windows in predictions
                if (isset($prediction['non_fertile_windows'])) {
                    foreach ($prediction['non_fertile_windows'] as $nonFertileWindow) {
                        if (isset($nonFertileWindow['start']) && isset($nonFertileWindow['end'])) {
                            if ($current->between($nonFertileWindow['start'], $nonFertileWindow['end'])) {
                                $dayData['non_fertile'] = true;
                                break;
                            }
                        }
                    }
                }
            }
            
            // Check current cycle ovulation and fertile window
            if ($currentCyclePrediction) {
                // Check ovulation
                if (isset($currentCyclePrediction['ovulation']) && $current->equalTo($currentCyclePrediction['ovulation'])) {
                    $dayData['ovulation'] = true;
                }
                
                // Check fertile window
                if (isset($currentCyclePrediction['fertile_start']) && isset($currentCyclePrediction['fertile_end'])) {
                    if ($current->between($currentCyclePrediction['fertile_start'], $currentCyclePrediction['fertile_end'])) {
                        $dayData['fertile'] = true;
                    }
                }
                
            }
            
            $calendarDays[] = $dayData;
            $current->addDay();
        }
        
        return $calendarDays;
    }

    /**
     * Calculate cycle length for a given cycle
     *
     * @param Cycle $cycle
     * @return int|null
     */
    public function calculateCycleLength(Cycle $cycle): ?int
    {
        $previousCycle = Cycle::where('user_id', $cycle->user_id)
            ->where('period_start_date', '<', $cycle->period_start_date)
            ->orderBy('period_start_date', 'desc')
            ->first();

        if (!$previousCycle) {
            return null;
        }

        return $previousCycle->period_start_date->diffInDays($cycle->period_start_date);
    }

    /**
     * Recalculate all cycle lengths for a user
     *
     * @param int $userId
     * @return void
     */
    public function recalculateCycleLengths(int $userId): void
    {
        $cycles = Cycle::where('user_id', $userId)
            ->orderBy('period_start_date', 'asc')
            ->get();

        foreach ($cycles as $cycle) {
            $cycleLength = $this->calculateCycleLength($cycle);
            if ($cycleLength !== null) {
                $cycle->cycle_length = $cycleLength;
                $cycle->saveQuietly();
            }
        }
    }

    /**
     * Get recent cycles for a user
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentCycles(int $userId, int $limit = 6)
    {
        return Cycle::where('user_id', $userId)
            ->orderBy('period_start_date', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Calculate average from cycle collection
     *
     * @param \Illuminate\Database\Eloquent\Collection $cycles
     * @return int
     */
    private function calculateAverageCycleLength($cycles): int
    {
        $validCycles = $cycles->filter(function ($cycle) {
            return $cycle->cycle_length !== null;
        });

        if ($validCycles->isEmpty()) {
            return 28; // Default cycle length
        }

        return (int) round($validCycles->avg('cycle_length'));
    }

    /**
     * Calculate variance for cycle regularity
     *
     * @param array $values
     * @return float
     */
    private function calculateVariance(array $values): float
    {
        if (count($values) < 2) {
            return 0;
        }

        $mean = array_sum($values) / count($values);
        $squaredDiffs = array_map(function ($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $values);

        return sqrt(array_sum($squaredDiffs) / count($squaredDiffs));
    }
}
