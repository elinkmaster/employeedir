<?php

namespace App\Http\Controllers;

use App\LeaveCreditArchive;
use App\LeaveCredits;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function archiveLeaveCredits(Request $request)
    {
        $fromYear = $request->fromYear ?? now()->format('Y');
        $toYear = $request->toYear ?? $fromYear;
        $fromMonth = $request->fromMonth ?? now()->format('m');
        $toMonth = $request->toMonth ?? $fromMonth;
        $employeeId = $request->id;

        $leaveCredits = LeaveCredits::where('year', '<=',(integer) $fromYear)->where('year', '>=',(integer) $toYear)
                                            ->where('month', '<=', $fromMonth)->where('month', '>=', $toMonth)
                                            ->get();

        if($employeeId){
            $leaveCredits = LeaveCredits::where('year', '<=',(integer) $fromYear)->where('year', '>=',(integer) $toYear)
                                            ->where('month', '<=', $fromMonth)->where('month', '>=', $toMonth)
                                            ->where('employee_id', $employeeId)->get();
        }

        ini_set('max_execution_time', 1200);
        foreach($leaveCredits as $credit)
        {
            LeaveCreditArchive::create([
                'employee_id' => $credit->employee_id,
                'credit' => $credit->credit,
                'type' => $credit->type,
                'month' => $credit->month,
                'year' => $credit->year,
                'leave_id' => $credit->leave_id,
                'status' => $credit->status
            ]);

            $credit->delete();
        }

        ini_set('max_execution_time', 60);
        return 'All credits are move to archived';
    }

public function unArchiveLeaveCredits(Request $request){
        $fromYear = $request->fromYear ?? now()->format('Y');
        $toYear = $request->toYear ?? $fromYear;
        $fromMonth = $request->fromMonth ?? now()->format('m');
        $toMonth = $request->toMonth ?? $fromMonth;
        $employeeId = $request->id;

        $leaveCredits = LeaveCreditArchive::where('year', '<=',(integer) $fromYear)->where('year', '>=',(integer) $toYear)
                                            ->where('month', '<=', $fromMonth)->where('month', '>=', $toMonth)
                                            ->get();

        if($employeeId){
            $leaveCredits = LeaveCreditArchive::where('year', '<=',(integer) $fromYear)->where('year', '>=',(integer) $toYear)
                                            ->where('month', '<=', $fromMonth)->where('month', '>=', $toMonth)
                                            ->where('employee_id', $employeeId)->get();
        }

        ini_set('max_execution_time', 1200);
        foreach($leaveCredits as $credit)
        {
            LeaveCredits::create([
                'employee_id' => $credit->employee_id,
                'credit' => $credit->credit,
                'type' => $credit->type,
                'month' => $credit->month,
                'year' => $credit->year,
                'leave_id' => $credit->leave_id,
                'status' => $credit->status
            ]);

            $credit->delete();
        }

        ini_set('max_execution_time', 60);
        return 'All archives are remove';
    }
}
