<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Password;
use App\Utils\RandomFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class DTS extends Controller
{
    public function getAllPasswords()
    {
        $passwords = Password::all();
        return response(['data' => $passwords], 200);
    }

    public function setPassword(Request $request)
    {
        $data = $request->validate([
            'password' => ['bail', 'required', 'string', 'min:6']
        ]);
        $password = $request->input('password');
        $dtsPassword = new Password();
        $dtsPassword->email = $email;
        $dtsPassword->password = $password;
        $dtsPassword->save();
        return response(['data' => $dtsPassword], 200);
    }

    public function getPassword(Request $request)
    {
        $email = $request->input('email');
        $password = Password::where('email', $email)->first();
        return response(['data' => $password], 200);
    }


    public function uploadDocument(Request $request)
    {
        $data = $request->validate([
            'name' => ['bail', 'required', 'string'],
            'email' => ['bail', 'required', 'string', 'email'],
            'phone_number' => ['bail', 'required', 'string'],
            'affiliated_company' => ['bail', 'required', 'string'],
            'position' => ['bail', 'required', 'string'],
            'document_title' => ['bail', 'required', 'string'],
            'file' => ['bail', 'required', 'file', 'mimes:pdf,doc,docs,rtf,ppt,pptx']
        ]);
        $name = $request->input('name');
        $user_email = $request->input('email');
        $phone_number = $request->input('phone_number');
        $affiliated_company = $request->input('affiliated_company');
        $position = $request->input('position');
        $document_title = $request->input('document_title');
        $mail = new PHPMailer(true);
        try {
            // TODO CONVERT THIS TO THE LONE EMAIL
            $emailsArray = explode(",", env('APP_EMAILS'));
            foreach ($emailsArray as $email)
                $mail->addAddress($email);
            $mail->setFrom('website@nipojoglobal.com');
            $mail->addAttachment($request->file('file')->getPathname(), $request->file('file')->getClientOriginalName());
            $mail->isHTML(true);
            $mail->Subject = "New Document Upload ($document_title) on " . env('APP_NAME') . " by " . $name;
            $mail->Body = "
            <html lang='us'>
                <head>
                    <title>New Document Upload</title>
                </head>
                <body>
                    <p>A new document with title, <b>\"$document_title\"</b> was uploaded on the website by $name. It is attached to this email.</p>
                    <br />
                    <p>The number they entered in the request for password form is <b>$phone_number</b>,their email is <b>$user_email</b>, their position is <b>$position</b> and their affiliated company is <b>$affiliated_company</b></p>
                    <br />

                </body>
            </html>
        ";
            $mail->send();
            return response(['message' => 'Document Uploaded'], 200);
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    public function uploadPublicDocument(Request $request)
    {
        $request->validate([
            'title' => ['bail', 'required', 'string'],
            'description' => ['bail', 'required', 'string'],
            'file' => ['bail', 'required', 'file', 'mimes:pdf,doc,docs,rtf,ppt,pptx']
        ]);
        $document = new Document();
        $document->title = $request->input('title');
        $document->description = $request->input('description');
        $document->file = $request->file('file')->store('documents', 'local');
        $document->save();
        return response(['message' => 'Document uploaded'], 200);
    }

    public function deleteDocument(int $id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        return response(['message' => 'Document deleted'], 200);
    }

    public function renameDocument(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => ['bail', 'required', 'string'],
        ]);
        $document = Document::findOrFail($id);
        $document->name = $request->input('name');
        $document->save();
        return response(['message' => 'Document renamed'], 200);
    }

    public function sendPasswordRequest(Request $request)
    {
        $data = $request->validate([
            'name' => ['bail', 'required', 'string'],
            'email' => ['bail', 'required', 'string', 'email'],
            'phone_number' => ['bail', 'required', 'string'],
            'affiliated_company' => ['bail', 'required', 'string'],
            'position' => ['bail', 'required', 'string'],
            'reasons' => ['bail', 'required', 'string']
        ]);
        $name = $request->input('name');
        $email = $request->input('email');
        $phone_number = $request->input('phone_number');
        $affiliated_company = $request->input('affiliated_company');
        $position = $request->input('position');
        $reasons = $request->input('reasons');
        $appName = env('APP_NAME');

        $subject = "New Request for Password for Document Transfer System on $appName from $name";

        // To send HTML mail, the Content-type header must be set
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $otpLink = route('send_otp_mail', [
            'ppn' => env('BEARER_TOKEN'),
            'email' => $email
        ]);
        $downloadableOtpLink = route('send_otp_downloadable_mail', [
            'ppn' => env('BEARER_TOKEN'),
            'email' => $email
        ]);
        $template = "
            <html lang='us'>
                <head>
                    <title>New Request for Password</title>
                </head>
                <body>
                    <p><i>A new request for password was sent on the  website by $name. These are their details:</i></p>
                    <p><b>Name:</b> $name</p>
                    <p><b>Email:</b> $email</p>
                    <p><b>Phone Number:</b> $phone_number</p>
                    <p><b>Affiliated Company:</b> $affiliated_company</p>
                    <p><b>Position:</b> $position</p>
                    <br />
                    <br />
                    <p>This was their reason for requesting for a password:</p>
                    <q>$reasons</q>
                    <br />
                    <br />
                    <br />
                    <p>In order to automatically create and send a One Time Password (OTP) to this user's mail, please click any of the links below:</p>
                    <ul>
                        <li><a href='$otpLink'>Send View Only OTP</a></li>
                        <li><a href='$downloadableOtpLink'>Send View and Download OTP</a></li>
                    </ul>
                </body>
            </html>
        ";
        //TODO CONVERT THIS TO THE LONE MAIL
//        mail(env('APP_LONE_EMAIL'), $subject, $template, $headers);
        $emailsArray = explode(",", env('APP_EMAILS'));
        foreach ($emailsArray as $email)
            mail(env('APP_FARM_EMAILS'), $subject, $template, $headers);
        return response(['message' => 'Password Request sent successfully'], 200);

    }

    public function getAllDocuments(Request $request)
    {
        $documents = Document::all();
        return response(['data' => $documents], 200);
    }

    protected function checkIfPasswordExists(string $password)
    {
        $dtsPassword = Password::where('password', $password)->first();
        $currentTime = Carbon::now();
        if (!is_null($dtsPassword)) {
            if ($currentTime->diffInMinutes(Carbon::parse($dtsPassword->created_at)) < 30 && $dtsPassword->times_used <= 1 && is_null($dtsPassword->used_at)) return $dtsPassword;
            else return false;
        } else return false;

    }

    public function getDocument(Request $request)
    {
        $request->validate([
            'password' => ['bail', 'required', 'string',]
        ]);
        if ($passwordOBJ = $this->checkIfPasswordExists($request->query('password'))) {
            $document = Document::find($request->query('document_id'));
            if (is_null($document)) return response(['message' => "The file you're trying to access doesn't exist."], 400);
            $passwordOBJ->times_used = is_null($passwordOBJ->times_used) ? 0 : $passwordOBJ->times_used + 1;
            $passwordOBJ->save();
            return response()->file(Storage::disk('local')->path($document->file));
        } else return response(['message' => 'Unauthenticated'], 401);
    }

    public function checkPassword(Request $request)
    {
        $request->validate([
            'password' => ['bail', 'required', 'string',]
        ]);
        if ($passwordOBJ = $this->checkIfPasswordExists($request->input('password'))) return response(['message' => 'Password is valid', 'downloadable' => $passwordOBJ->allows_download]);
        else return response(['message' => 'Unauthenticated'], 401);
    }

    public function invalidatePasswordFromFrontEnd(Request $request) {
        $request->validate([
            'password' => ['bail', 'required', 'string',]
        ]);
        $passwordOBJ = Password::where('password', $request->input('password'))->first();
        $passwordOBJ->used_at = Carbon::now();
        $passwordOBJ->save();
        return response(['message' => "Password invalidated successfully"], 200);
    }


    public function generateOTP(Request $request)
    {
        if ($request->query('ppn') !== env('BEARER_TOKEN'))
            abort(404);
        $password = new Password();
        $password->password = RandomFunctions::generateRandomString(10, true);
        $password->save();
        return view('generate-password', ['password' => $password->password]);
    }

    public function sendOTPMail(Request $request) {
        $email = $request->query('email');
        if ($request->query('ppn') !== env('BEARER_TOKEN'))
            abort(404);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            abort(404);
        $password = new Password();
        $password->password = RandomFunctions::generateRandomString(10, true);
        date_default_timezone_set("UTC");
        $time = Carbon::now()->addMinutes(env('OTP_EXPIRY_MINUTES'))->setTimezone('Africa/Lagos')->format('h:i a');
        $password->save();
        $subject = "New One Time Password to View Document on Nipoj Website";

        // To send HTML mail, the Content-type header must be set
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $template = "
            <html lang='us'>
                <head>
                    <title>New OTP To View Document</title>
                </head>
                <body>
                    <p>A new OTP has been created for you to view your requested document on the NIPOJ website. Your password is {$password->password}</p>
                    <p>It only works once and will expire at $time, so please use it before then.</p>
                </body>
            </html>
        ";
        mail($email, $subject, $template, $headers);
        return view('generate-password', ['password' => $password->password, 'email' => urldecode($email), 'expiry' => $time]);
    }

    public function sendOTPDownloadableMail(Request $request) {
        $email = $request->query('email');
        if ($request->query('ppn') !== env('BEARER_TOKEN'))
            abort(404);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            abort(404);
        $password = new Password();
        $password->password = RandomFunctions::generateRandomString(10, true);
        $password->allows_download = true;
        date_default_timezone_set("UTC");
        $time = Carbon::now()->addMinutes(env('OTP_EXPIRY_MINUTES'))->setTimezone('Africa/Lagos')->format('h:i a');
        $password->save();
        $subject = "New OTP to View and Download Document on Nipoj Website";

        // To send HTML mail, the Content-type header must be set
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $template = "
            <html lang='us'>
                <head>
                    <title>New OTP To View Document</title>
                </head>
                <body>
                    <p>A new OTP has been created for you to view and download your requested document on the NIPOJ website. Your password is {$password->password}</p>
                    <p>It only works once and will expire at $time, so please use it before then.</p>
                </body>
            </html>
        ";
        mail($email, $subject, $template, $headers);
        return view('generate-downloadable-password', ['password' => $password->password, 'email' => urldecode($email), 'expiry' => $time]);
    }

    public function generateDownloadableOTP(Request $request) {
        if ($request->query('ppn') !== env('BEARER_TOKEN'))
            abort(404);
        $password = new Password();
        $password->password = RandomFunctions::generateRandomString(10, true);
        $password->allows_download = true;
        $password->save();
        return view('generate-downloadable-password', ['password' => $password->password]);
    }

}
