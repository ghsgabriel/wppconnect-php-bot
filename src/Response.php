<?php


namespace Bot;


class Response
{

    /**
     * Send response text
     * @param $msg
     */
    public static function sendText($msg, $session)
    {
        $request = Request::load();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $session->SERVER_URL . '/send-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "phone": "' . $request->getFrom() . '",
                "message": "' . $msg . '",
                "isGroup": false
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $session->SERVER_KEY
            ),
        ));

        usleep ( rand(0500000,2000000) );
        curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            error_log("Curl Error");
            error_log($error_msg);
        }
        curl_close($curl);
    }
}