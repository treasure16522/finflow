<?php
namespace app\helpers;

class DateHelper
{
    /**
     * Convert month to database format (YYYY-MM-DD)
     */
    public static function toDbFormat($month)
    {
        if (empty($month)) {
            return date('Y-m-01');
        }
        
        // If already in YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $month)) {
            return $month;
        }
        
        // Handle MM-YYYY format
        if (preg_match('/^(\d{1,2})-(\d{4})$/', $month, $matches)) {
            return $matches[2] . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT) . '-01';
        }
        
        // Handle YYYY-MM format
        if (preg_match('/^(\d{4})-(\d{1,2})$/', $month, $matches)) {
            return $matches[1] . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . '-01';
        }
        
        // Try to parse
        $timestamp = strtotime($month);
        return $timestamp ? date('Y-m-01', $timestamp) : date('Y-m-01');
    }
    
    /**
     * Convert month to display format (YYYY-MM)
     */
    public static function toDisplayFormat($month)
    {
        if (empty($month)) {
            return date('Y-m');
        }
        
        // If it's a full date
        if (preg_match('/^(\d{4}-\d{2})-\d{2}$/', $month, $matches)) {
            return $matches[1];
        }
        
        // If already in YYYY-MM format
        if (preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $month;
        }
        
        // Handle MM-YYYY format
        if (preg_match('/^(\d{1,2})-(\d{4})$/', $month, $matches)) {
            return $matches[2] . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        }
        
        // Try to parse
        $timestamp = strtotime($month);
        return $timestamp ? date('Y-m', $timestamp) : date('Y-m');
    }
    
    /**
     * Get month name from date
     */
    public static function getMonthName($date)
    {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return $timestamp ? date('F', $timestamp) : '';
    }
    
    /**
     * Get year from date
     */
    public static function getYear($date)
    {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return $timestamp ? date('Y', $timestamp) : '';
    }
}