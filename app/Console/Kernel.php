<?php

namespace App\Console;

use App\Models\Importer;
use App\Models\ImporterData;
use App\Models\Property;
use App\Models\Reminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        set_time_limit(0);
        // $schedule->command('inspire')
        //          ->hourly();

        // Importer
        $schedule->call(function () {
            include app_path('Libs/DataImport/simple_html_dom.php');

            $data = Importer::where('status','pending')->first();
            if(!$data){
                return false;
            }

            $data->update([
                'status'=> 'proccess'
            ]);

            $importer = new \App\Libs\DataImport\Importer($data->connector);
            $importer->setImporterModal($data);
            switch ($data->connector){
                case 'OLX':
                    if($data->area->olx_id) $importer->setArea($data->area->olx_id);
                    if($data->property_type->olx_id) $importer->setType($data->property_type->olx_id);
                    if($data->purpose->olx_id) $importer->setPurpose($data->purpose->olx_id);
                    if($data->space_from || $data->space_to) $importer->setSpace($data->space_from,$data->space_to);
                    if($data->price_from || $data->price_to) $importer->setPrice($data->price_from,$data->price_to);
                    break;
            }

            $importer->getList($data->page_start,$data->page_end)->getProperties();

/*
            if($properties){
                foreach ($properties as $key => $value){
                    $insertImporterData = ImporterData::create([
                        'importer_id'=> $data->id,
                        'connector_id'=> $value['id'],
                        'name'=> $value['name'],
                        'price'=> $value['price'],
                        'description'=> $value['description'],
                        'space'=> $value['space'],
                        'bed_rooms'=> $value['bedRooms'],
                        'bath_room'=> $value['bathRoom'],
                        'mobile'=> $value['mobile'],
                        'owner_name'=> $value['ownerName']
                    ]);

                    // --- Notification
                    $numRequests = Property::requestsForImport($data->property_type_id,$data->purpose_id,$data->area_id,$value['space'],$value['price'])
                        ->count();
                    if($numRequests){
                        $allStaffToNotify = array_column(
                            App\Models\Staff::get(['id'])->toArray(),
                            'id'
                        );
                        notifyStaff(
                            [
                                'type'  => 'staff',
                                'ids'   => $allStaffToNotify
                            ],
                            __('There are :number requests related to Importer',['number'=> $numRequests]),
                            __('There are :number requests related to Importer',['number'=> $numRequests]),
                            route('system.importer.show',$data->id).'?id='.$insertImporterData->id
                        );
                    }
                    // --- Notification
                }
            }

            $data->update([
                'status'=> 'done'
            ]);*/

        })->everyMinute();


        // Reminder
        $schedule->call(function () {
            $reminder = Reminder::where('is_notified','no')
                ->where('date_time','=',date('Y-m-d H:i:s'))
                ->get();

            if($reminder->isNotEmpty()){
                foreach ($reminder as $key => $value){

                    switch ($value->sign_type){
                        case 'App\Models\Property':
                            $url = route('system.property.show',$value->sign_id);
                            break;

                        case 'App\Models\Request':
                            $url = route('system.request.show',$value->sign_id);
                            break;

                        case 'App\Models\Client':
                            $url = route('system.client.show',$value->sign_id);
                            break;

                        default:
                            $url = route('system.dashboard');
                            break;
                    }

                    notifyStaff(
                        [
                            'type'=> 'staff',
                            'ids' => [$value->staff_id]
                        ],
                        __('Calendar Notification'),
                        $value->comment,
                        $url
                    );
                }
            }

        })->everyMinute();




    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
