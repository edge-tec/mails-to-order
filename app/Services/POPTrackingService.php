<?php

namespace App\Services;

use Exception;

class POPTrackingService {

    public static function getUsage(int $serverId, int $userId, int $dailyLimit, int $monthlyLimit): array {
        $today = date('Y-m-d');
        $firstOfMonth = date('Y-m-01');

        // Check if today's usage row exists
        $dailyRow = Database::fetch(
            "SELECT daily_pop_used FROM server_usage WHERE server_id = ? AND user_id = ? AND usage_date = ?",
            [$serverId, $userId, $today]
        );
        $dailyUsed = $dailyRow ? (int)$dailyRow['daily_pop_used'] : 0;

        // Sum monthly usage for current month
        $monthlyRow = Database::fetch(
            "SELECT SUM(daily_pop_used) as total_monthly FROM server_usage WHERE server_id = ? AND user_id = ? AND usage_date >= ?",
            [$serverId, $userId, $firstOfMonth]
        );
        $monthlyUsed = $monthlyRow && $monthlyRow['total_monthly'] ? (int)$monthlyRow['total_monthly'] : 0;

        $remainingDaily = max(0, $dailyLimit - $dailyUsed);
        $remainingMonthly = max(0, $monthlyLimit - $monthlyUsed);

        return [
            'daily_limit' => $dailyLimit,
            'daily_used' => $dailyUsed,
            'daily_remaining' => $remainingDaily,
            'monthly_limit' => $monthlyLimit,
            'monthly_used' => $monthlyUsed,
            'monthly_remaining' => $remainingMonthly,
            'usage_date' => $today,
        ];
    }

    public static function recordUsage(int $serverId, int $userId, int $popAmount): bool {
        $today = date('Y-m-d');
        $existing = Database::fetch(
            "SELECT id, daily_pop_used FROM server_usage WHERE server_id = ? AND user_id = ? AND usage_date = ?",
            [$serverId, $userId, $today]
        );

        if ($existing) {
            Database::execute(
                "UPDATE server_usage SET daily_pop_used = daily_pop_used + ?, updated_at = NOW() WHERE id = ?",
                [$popAmount, $existing['id']]
            );
        } else {
            Database::insert(
                "INSERT INTO server_usage (server_id, user_id, usage_date, daily_pop_used, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())",
                [$serverId, $userId, $today, $popAmount]
            );
        }
        return true;
    }

    public static function resetDailyUsage(): int {
        // Daily counters reset naturally because usage is queried per usage_date = TODAY.
        // We can log execution for statistics.
        return 1;
    }

    public static function resetMonthlyUsage(): int {
        // Monthly counters reset naturally because usage is queried per usage_date >= FIRST OF MONTH.
        return 1;
    }
}
