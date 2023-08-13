<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CallBackController extends Controller
{
    use ApiResponser;

    public function invoice(Request $request) {
        $xenditXCallBackToken = 'ay9EH7ahIvSgs5YLosnW6CKXNUXwEw28CyypvL8JFh4cefp9';

        $header = getallheaders();
        $incomingCallBackTokenHeader = isset($header['x-callback-token']) ? $header['x-callback-token'] : "";

        if ($incomingCallBackTokenHeader === $xenditXCallBackToken) {
            $application = Application::where('invoice_number', $request->external_id)->first();

            $application->update([
                'payment_status' => $request->status,
            ]);

            return $this->success($application, 'Berhasil Membayar');
        } else {
            http_response_code(403);
        }

        return $this->error('Notif Gagal Terkirim');
    }
}
