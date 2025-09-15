<?php

namespace App\Services;

use App\Models\SmsMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OsonSms {

    private $login;
    private $hash;
    private $sender;
    private $server;
    private $trxPrefix;

    public function __construct($login, $hash, $sender, $server, $trxPrefix)
    {
        $this->login = $login;
        $this->hash = $hash;
        $this->sender = $sender;
        $this->server = $server;
        $this->trxPrefix = $trxPrefix;
    }

    public function send($phoneNumber, $message, $payload = null): bool
    {
        $smsMessage = new SmsMessage();
        $smsMessage->company_id = is_null($payload) ? Auth::user()->company_id : $payload['company_id'];
        $smsMessage->phone_number = $phoneNumber;
        $smsMessage->message = $message;

        if($smsMessage->save() === false) {
            abort(422, 'Не удалось сохранить СМС');
        }

        $trxId = $this->trxPrefix . $smsMessage->id;
        $separator = ';';

        $strHash = hash('sha256',$trxId . $separator . $this->login . $separator . $this->sender . $separator . $phoneNumber . $separator . $this->hash);

        $queries = [
            'from'          => $this->sender,
            'phone_number'  => $phoneNumber,
            'msg'           => $message,
            'str_hash'      => $strHash,
            'txn_id'        => $trxId,
            'login'         => $this->login,
        ];

        try {
            if(empty($this->server) || !preg_match('/^https?:\/\//i', $this->server)) {
                // Skip sending if server is not configured properly
                $smsMessage->response = 'SKIPPED: invalid OSONSMS_SERVER';
            } else {
                // Let HTTP client append the query string correctly
                $response = Http::timeout(5)->connectTimeout(3)->retry(1, 200)->get($this->server, $queries);
                $smsMessage->response = (string)$response->body();
            }
        } catch (\Throwable $e) {
            // Do not break business flow if SMS provider fails
            $smsMessage->response = 'ERROR: ' . $e->getMessage();
        }

        if($smsMessage->save() === false) {
            abort(422, 'Не удалось обновить СМС');
        }

        return true;
    }
}
