<?php

namespace App\Traits;

use App\Library\CustomORM\AppCollection;
use Illuminate\Support\Facades\Storage;
use \ZipArchive;
use File;
use App\Models\User;
use Mail;
use Carbon\Carbon;
use Auth;

trait UtilityTrait
{
    /**
     * Method get value by given key from array.
     *
     * @param string     $key     key name
     * @param array      $arr     array
     * @param mixed|null $default default flag
     *
     * @return mixed
     */
    public function arrayGet(string $key, array $arr, $default = null)
    {
        if (is_array($arr) && array_key_exists($key, $arr) && !empty($arr[$key])) {
            return $arr[$key];
        }
        return $default;
    }

    /**
     * This will send mail
     *
     * @param string $toEmail  ToEmail
     * @param string $mailFrom MailFrom
     * @param string $mailName MailName
     * @param string $body     Body
     * @param string $subject  Subject
     *
     * @return void
     */
    public function sendMail(
        string $toEmail,
        string $mailFrom,
        string $mailName,
        string $body,
        string $subject,
        string $fileName
    ) {
        Mail::send(
            $fileName,
            ['body' => $body],
            function ($message) use ($toEmail, $body, $mailFrom, $mailName, $subject) {
                $message->to($toEmail)->subject($subject);
                $message->from($mailFrom, $mailName);
            }
        );
    }

    /**
     * This will retrive table name
     *
     * @return string Table Name
     */
    public function getTableName() :string
    {
        return $this->table;
    }

    /**
     * Converts an object to an array
     *
     * @param object $d object to be converted
     *
     * @return array Array convertido
     */
    public function objectToArray($d)
    {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }
        return is_array($d) ? array_map(array($this, 'objectToArray'), $d) : $d;
    }

    /**
     * Passing model name for the pagination and sorting.
     *
     */
    // public function newCollection(array $models = [])
    // {
    //     return new AppCollection($models);
    // }


    /**
     * Verify if exit subarray
     *
     * @param array   $arr      -> array to verify if exits subarray
     * @param boolean $checkAll -> To check whether all the elements are array or not
     *
     * @return boolean     true if exist and false if not
     */
    public function existSubArray($arr, bool $checkAll = false): bool
    {
        $noOfArray =  0;
        foreach ($arr as $value) {
            if (is_array($value)) {
                if (false === $checkAll) {
                    return true;
                } else {
                    $noOfArray++;
                }
            }
        }

        if ($noOfArray === count($arr)) {
            return true;
        }

        return false;
    }
   
    /**
     * This will prepare Data for send email
     *
     * @param array $input input
     * @param string $password password
     * @param string $url url
     *
     * @return boolean
     */
    public function prepareSendMailData(array $input, string $password, string $url)
    {
        $body = array(
            'username' => $input['username'],
            'password' => $password,
            'firstName' => $input['first_name'],
            'lastName' => $input['last_name'],
            'email' => $input['email'],
            'login_url' => $url
        );
        $fromEmail = env('MAIL_FROM', 'care@optimind.com');
        $fromName = env('MAIL_NAME', 'optimind');
        $subject = config('constants.subjects.newAccount');
        $this->sendMail($input['email'], $fromEmail, $fromName, $body, $subject, 'verifyAccount');
    }

    /**
     * This will encrypt data
     *
     * @param int $data data
     *
     * @return void
     */
    public function encrypt($data)
    {
        $id = (double)$data * 253525.24;
        return base64_encode($id);
    }

    /**
     * This will decrypt data
     *
     * @param string $data data
     *
     * @return void
     */
    public function decrypt($data)
    {
        $urlId = base64_decode($data);
        $id = (double)$urlId / 253525.24;
        return $id;
    }

    /*
    * Generate SignedURL
    *
    * @param string $path Path
    * @param boolean $forceDownload Download
    *
    * @return string output
    */
    // public function generateSignedURL($path, $forceDownload = false)
    // {
    //     if (env('APP_ENV') == 'testing') {
    //         return '';
    //     }
    //     if (!Storage::disk('s3')->exists($path)) {
    //         return false;
    //     }
    //     $s3 = Storage::disk('s3');
    //     $client = $s3->getDriver()->getAdapter()->getClient();
    //     $expiry = "+7 days";
    //     $params = array(
    //         'Bucket' => config('app.bucket_name'),
    //         'Key' => $path,
    //     );
    //     // if ($forceDownload) {
    //     //     $params['ResponseContentType'] = 'application/octet-stream';
    //     //     $params['ResponseContentDisposition'] = 'attachment; filename="' . basename($path) . '"';
    //     // }
    //     $command = $client->getCommand('GetObject', $params);

    //     $request = $client->createPresignedRequest($command, $expiry);

    //     return (string)$request->getUri();
    // }

    /*
     * Generate SignedURL
     *
     * @param string $folderName folder Name
     * @param string $fileName file Name
     * @param string $path Path
     *
     * @return string filePath
     */
    // public function awsBucketStorage($folderName = null, $fileName = null, $path = null)
    // {
    //     if (env('APP_ENV') == 'testing') {
    //         return '';
    //     }
    //     Storage::disk('s3')->put($folderName . '/' . $fileName, file_get_contents($path));
    //     // $filePath = $this->generateSignedURL(
    //     //     $folderName . '/' . $fileName,
    //     //     true
    //     // );
    //     return true;
    // }

    public function random_strings($length_of_string) 
    { 
        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
        return substr(str_shuffle($str_result), 0, $length_of_string); 
    } 

    public function organizationSignInCheck()
    {
        $organizationSignInCheck = User::select('id','organization_login_id')->where('id',Auth::user()->id)->where('organization_login_id','!=',0)->first();
        return $organizationSignInCheck;
    }

    public function sendSingle($registration_ids, $message)
    {
        $fields = array(
            'to' => $registration_ids,
            'notification' => $message,
        );
        return $this->sendPushNotification($fields);
    }

    public function sendMultiple($registration_ids, $message)
    {
        $fields = array(
            'registration_ids' => $registration_ids,
            'notification' => $message,
        );
        return $this->sendPushNotification($fields);
    }

    public function sendPushNotification($fields)
    {
        $appKey = Setting::first();

        $headers = [
            'Authorization: key='.$appKey->firebase_server_key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        
        \Log::info("Cron is working 🤩".$result);

        curl_close($ch);
        return $result;
    }
}
