<?php

namespace App\Http\Controllers;

trait SafeDataAccessTrait
{
    /**
     * Safely get the first item from a collection with null check
     */
    protected function safeFirst($collection, $fallback = null)
    {
        if (!$collection || !method_exists($collection, 'count') || $collection->count() === 0) {
            return $fallback;
        }
        return $collection->first();
    }

    /**
     * Safely get nested relationship data
     */
    protected function safeGet($object, $path, $fallback = 'N/A')
    {
        if (!$object) {
            return $fallback;
        }

        $keys = explode('.', $path);
        $current = $object;

        foreach ($keys as $key) {
            if (!$current || !isset($current->$key)) {
                return $fallback;
            }
            $current = $current->$key;
        }

        return $current ?? $fallback;
    }

    /**
     * Safely get accommodation name from rate relationship
     */
    protected function getAccommodationName($rate, $fallback = 'N/A')
    {
        if (!$rate || !$rate->accommodations || $rate->accommodations->count() === 0) {
            return $fallback;
        }
        
        $accommodation = $rate->accommodations->first();
        return $accommodation ? $accommodation->name : $fallback;
    }

    /**
     * Safely get room number
     */
    protected function getRoomNumber($room, $fallback = 'N/A')
    {
        return $room && isset($room->room) ? $room->room : $fallback;
    }

    /**
     * Safely get user full name
     */
    protected function getUserFullName($user, $fallback = 'Unknown User')
    {
        if (!$user) {
            return $fallback;
        }

        $firstName = $user->firstName ?? '';
        $lastName = $user->lastName ?? '';
        
        if (empty($firstName) && empty($lastName)) {
            return $fallback;
        }

        return trim($firstName . ' ' . $lastName);
    }

    /**
     * Safely format currency
     */
    protected function formatCurrency($amount, $currency = '₱')
    {
        if (!is_numeric($amount)) {
            return $currency . '0.00';
        }
        return $currency . number_format((float)$amount, 2);
    }

    /**
     * Safely format date
     */
    protected function formatDate($date, $format = 'Y-m-d H:i:s', $fallback = 'N/A')
    {
        if (!$date) {
            return $fallback;
        }

        try {
            return $date->format($format);
        } catch (\Exception $e) {
            \Log::warning('Date formatting error', [
                'date' => $date,
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            return $fallback;
        }
    }
}
