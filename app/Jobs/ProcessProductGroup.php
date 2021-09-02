<?php

namespace App\Jobs;

use App\Models\ProductGroup;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class ProcessProductGroup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    private $user;

    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    public function handle()
    {
        try {
            ProductGroup::updateOrCreate(
                [
                    'user_id' => $this->user->id,
                    'crms_id' => $this->data['id']
                ],
                $this->parsed()
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function parsed()
    {
        return [
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'user_id' => $this->user->id,
            'crms_id' => $this->data['id']
        ];
    }
}
