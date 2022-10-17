<?php

namespace App\Jobs;

use App\LeaveCredits;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class UpdateEmployeeCredits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $obj = DB::select("
            SELECT 
                *
            FROM
                employee_info
            WHERE
                status = 1 AND deleted_at IS NULL
                    AND eid LIKE 'ESCC-%';
        ");
        
        foreach($obj as $e):
            $id = $e->id;
            $det = DB::select("
                SELECT 
                    id
                FROM
                    leave_credits
                WHERE
                    employee_id = $id AND type = 1
                        AND month = MONTH(NOW())
                        AND year = YEAR(NOW());
            ");
            if(count($det) == 0){
                $div = 0;
                switch($e->employee_category):
                    case 1: $div = 20; break;
                    case 2: $div = 14; break;
                    case 3: $div = 10; break;
                    case 4: $div = 10; break;
                endswitch;
                $credit = new LeaveCredits();
                $credit->employee_id = $id;
                $credit->credit = $div / 12;
                $credit->type = 1;
                $credit->month = date("n");
                $credit->year = date("Y");
                $credit->save();
            }
        endforeach;
    }
}
