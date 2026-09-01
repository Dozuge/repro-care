<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Checkup;
use App\Services\RiskAnalysisService;
use App\Services\PeriodNotificationService;

class RunScheduledTasks extends Command
{
    protected $signature = 'alerts:run 
                            {--checkups : Mark overdue checkups as missed}
                            {--risk : Re-evaluate risk for missed checkups}
                            {--periods : Check and send period notifications}
                            {--all : Run all scheduled tasks}
                            {--list : List all scheduled tasks}';

    protected $description = 'Run automated analysis and alert tasks manually for testing';

    public function handle()
    {
        if ($this->option('list')) {
            $this->listTasks();
            return 0;
        }

        $ranAny = false;

        // 1. Mark overdue checkups
        if ($this->option('checkups') || $this->option('all')) {
            $this->info('⏳ Checking for overdue checkups...');
            
            $overdueCount = Checkup::overdue()->count();
            if ($overdueCount === 0) {
                $this->warn('  No overdue checkups found.');
            } else {
                $this->info("  Found {$overdueCount} overdue checkup(s).");
                Checkup::markOverdueCheckups();
                $this->info('  ✓ Overdue checkups marked as Missed. Notifications sent.');
            }
            $ranAny = true;
        }

        // 2. Re-evaluate risk for missed checkups
        if ($this->option('risk') || $this->option('all')) {
            $this->info('⏳ Re-evaluating risk for patients with missed checkups...');
            
            $missedUsers = Checkup::missed()
                ->distinct('patient_id')
                ->pluck('patient_id');
            
            if ($missedUsers->isEmpty()) {
                $this->warn('  No patients with missed checkups found.');
            } else {
                $this->info("  Found {$missedUsers->count()} patient(s) with missed checkups.");
                
                foreach ($missedUsers as $userId) {
                    $riskService = new RiskAnalysisService();
                    $riskLevel = $riskService->evaluate($userId);
                    $this->info("    User {$userId}: Risk level = {$riskLevel}");
                }
            }
            $ranAny = true;
        }

        // 3. Period notifications
        if ($this->option('periods') || $this->option('all')) {
            $this->info('⏳ Checking period tracking notifications...');
            
            $service = new PeriodNotificationService();
            $notified = $service->checkAndNotify();
            
            if (empty($notified)) {
                $this->warn('  No period notifications to send.');
            } else {
                $this->info('  ✓ Sent ' . count($notified) . ' period notification(s):');
                foreach ($notified as $n) {
                    $this->info("    - User {$n['user_id']}: {$n['type']} ({$n['date']})");
                }
            }
            $ranAny = true;
        }

        if (!$ranAny) {
            $this->error('No task specified. Use --checkups, --risk, --periods, or --all');
            $this->line('');
            $this->listTasks();
            return 1;
        }

        $this->line('');
        $this->info('✓ Tasks completed!');
        return 0;
    }

    private function listTasks()
    {
        $this->info('Scheduled Automated Tasks:');
        $this->line('  1. Mark overdue checkups → daily at 8:00 AM');
        $this->line('  2. Risk re-evaluation → daily at 8:30 AM');
        $this->line('  3. Period notifications → daily at 9:00 AM');
        $this->line('  4. High-risk patient review → weekly Sundays at 10:00 AM');
        $this->line('');
        $this->info('Manual Run Commands:');
        $this->line('  php artisan alerts:run --checkups    # Mark overdue checkups');
        $this->line('  php artisan alerts:run --risk        # Re-evaluate risk');
        $this->line('  php artisan alerts:run --periods     # Send period reminders');
        $this->line('  php artisan alerts:run --all         # Run all tasks');
    }
}
