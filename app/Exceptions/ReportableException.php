<?php

namespace App\Exceptions;

use App\Models\AppException;
use App\Models\User;
use App\Notifications\AppExceptionNotification;
use Exception;
use Illuminate\Support\Facades\Notification;
use Throwable;

class ReportableException extends Exception
{
    //
    public $report;
    public $status;

    public function __construct($error = null, $status = null)
    {
        $this->error = $error;
        $this->status = $status ?? 500;
    }

    public function render()
    {
        $request = request();
        $reportees = User::whereEmail('favescsskr@gmail.com')->get();
        try {
            Notification::send($reportees, new AppExceptionNotification($this->error));
        } catch (Throwable $th) {
        }
        $exc = AppException::create([
            "message" => $this->error->getMessage(),
            "trace" => $this->error->getTrace(),
            "request" => ['url' => $request->fullUrl(), 'body' => $request->all(), 'header' => $request->header(), 'ip' => $request->ip()],
            'reported' => true,
            'report_recipients' => $reportees
        ]);
        return response()->json([
            "success" => false,
            "message" => $this->error->getMessage(),
            "trace" => $this->error->getTrace(),
            "reported" => true,
            "request" => ['url' => $request->fullUrl(), 'body' => $request->all(), 'header' => $request->header(), 'ip' => $request->ip()]
        ], $this->status);
    }
}
