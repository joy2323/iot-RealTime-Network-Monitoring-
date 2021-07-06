<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Communication;
use App\Detail;
use App\Device;
use App\Site;
use App\SiteReport;
use App\SiteStatus;
use App\User;
use App\Location;
use Carbon\Carbon;
use App\Helpers\HelperClass;
class NotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email and SMS notification scheduler';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('Notification scan'); 
        $this-> getAlarms();
    }

    public function getAlarms(){

        $activeAlarms = SiteReport::where('status', 'Active')->where('alarm', '!=', 'SITE DOWN')->get();
        foreach ($activeAlarms as $alarms) {
            $getLabel = $alarms->alarm;
            $siteId = $alarms->site_id;
            $helperClass = new HelperClass();
            $getNotification = $helperClass->sendScheduleNotification($alarms);
        }


    }
}