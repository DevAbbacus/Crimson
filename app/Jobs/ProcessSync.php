<?php

namespace App\Jobs;

use App\Models\SyncStatus;
use App\Services\CRMSService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Throwable;

class ProcessSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $credentials = [];

    public function __construct($user)
    {
        $this->user = $user;
        $this->credentials = [
            'subdomain' => $user->sub_domain,
            'key' => $user->api_token
        ];
    }

    public function handle(CRMSService $crmss)
    {
        $status =  SyncStatus::byUser($this->user->id)->first();
        $working = SyncStatus::byUser($this->user->id)->isWorking()->exists();
        //if ($working) return;
        $lastSync = $status
            ? Carbon::createFromFormat('Y-m-d H:i:s', $status->last_sync)
            ->subDays(1)
            ->toISOString()
            : '';
        $status = SyncStatus::updateOrCreate(
            ['user_id' => $this->user->id],
            [
                'status' => 0,
                'last_sync' =>  Carbon::now()->toDateTime(),
                'user_id' => $this->user->id
            ]
        );

        $query = ['all' => true, 'q[updated_at_gt]' => $lastSync];

        // ? PRODUCT GROUPS SYNC
        $groups = $crmss->productGroups($this->credentials, $query);
        Log::info(count($groups));
        foreach ($groups as $group)
            ProcessProductGroup::dispatch($group, $this->user)->onQueue('high');

        // ? PRODUCTS SYNC
        $productQuery = ['all' => true];
        $products = $crmss->products($this->credentials, $productQuery);
            //echo "<pre>";print_r($products);exit();
        //        Log::info($products);
        Log::info("SU Product Count" . count($products));
        foreach ($products as $key => $product){
        

            //echo "<pre>";print_r($product);exit();
            //ProcessProduct::dispatch($product, $this->user)->onQueue('low');
            ProcessProduct::dispatch($product, $this->user)->delay($key * 2);
        }

        $status->update(['status' => 1]);
    }

    public function failed(Throwable $exception)
    {
        $status =  SyncStatus::byUser($this->user->id)->first();
        $status->update(['status' => 2]);
    }
}
