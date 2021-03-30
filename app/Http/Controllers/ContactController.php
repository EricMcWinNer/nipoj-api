<?php

namespace App\Http\Controllers;

use App\Rules\OneWord;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactController extends Controller
{
    public function requestQuote(Request $request)
    {
        $name = $request->name;
        $phone = $request->phone;
        $email = $request->email;
        $service = $request->service;
        $message = $request->message;
        $subject = "New Quote Request on " . env('APP_NAME') . " from $name";
        // To send HTML mail, the Content-type header must be set
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $template = "
            <html lang='us'>
                <head>
                    <title>New Quote Request</title>
                </head>
                <body>
                    <p><i>A new quote request was created on the website by $name for $service. This was their message:</i></p>
                    <br />
                    <q>$message</q>
                    <br />
                    <p><i>The number they entered during the quote request is <b>$phone</b> and their email is <b>$email.</b></i></p>
                </body>
            </html>
        ";
        $emailsArray = explode(",", env('APP_EMAILS'));
        foreach ($emailsArray as $email)
            mail($email, $subject, $template, $headers);
        return response(['message' => 'Request for quote sent successfully'], 200);
    }

    public function sendContact(Request $request)
    {
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $phone = $request->phone;
        $message = $request->message;
        $subject = "New Contact Message on " . env('APP_NAME') . " from $first_name $last_name";

        // To send HTML mail, the Content-type header must be set
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $template = "
            <html lang='us'>
                <head>
                    <title>New Contact Message</title>
                </head>
                <body>
                    <p><i>A new contact message was sent on the website by $first_name $last_name. This was their message:</i></p>
                    <br />
                    <q>$message</q>
                    <br />
                    <p><i>The number they entered in the contact message is <b>$phone</b> and their email is <b>$email.</b></i></p>
                </body>
            </html>
        ";
        $emailsArray = explode(",", env('APP_EMAILS'));
        foreach ($emailsArray as $email)
            mail($email, $subject, $template, $headers);


    }

    public function sendNipojFarmContact(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $message = $request->message;
        $subject = "New Contact Message on " . env('APP_FARM_NAME') . " from $name";

        // To send HTML mail, the Content-type header must be set
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $template = "
            <html lang='us'>
                <head>
                    <title>New Contact Message</title>
                </head>
                <body>
                    <p><i>A new contact message was sent on the website by $name. This was their message:</i></p>
                    <br />
                    <q>$message</q>
                    <br />
                    <p><i>The email address they entered in the contact message is <b>$email.</b></i></p>
                </body>
            </html>
        ";
        $emailsArray = explode(",", env('APP_FARM_EMAILS'));
        foreach ($emailsArray as $email)
            mail($email, $subject, $template, $headers);
    }

    public function sendCareerEmail(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['bail', 'required', 'string', new OneWord()],
            'last_name' => ['bail', 'required', 'string', new OneWord()],
            'email' => ['bail', 'required', 'string', 'email'],
            'phone_number' => ['bail', 'required', 'string'],
            'desired_position' => ['bail', 'required', 'string'],
            'cv' => ['bail', 'required', 'file', 'mimes:pdf,doc,docs,rtf,ppt,pptx']
        ]);
        $name = $request->input('first_name') . " " . $request->input('$last_name');
        $email = $request->input('email');
        $phone_number = $request->input('phone_number');
        $desired_position = $request->input('desired_position');
        $mail = new PHPMailer(true);
        try {
            $mail->addAttachment($request->file('cv')->getPathname(), $request->file('cv')->getClientOriginalName());
            $mail->isHTML(true);
            $mail->setFrom("website@nipojglobal.com");
            $mail->Subject = "New Career Application for " . $desired_position . " on " . env('APP_NAME') . " website";
            $mail->Body = "
            <html lang='us'>
                <head>
                    <title>New Career Application</title>
                </head>
                <body>
                    <p><i>A new career application was submitted on the website by $name for the position of $desired_position.</i></p>
                    <br />
                    <p><i>The number they entered in the contact message is <b>$phone_number</b> and their email is <b>$email.</b></i></p>
                </body>
            </html>
        ";
            $emailsArray = explode(",", env('APP_EMAILS'));
            foreach ($emailsArray as $email)
                $mail->addAddress($email);
            $mail->send();
            return response(['message' => 'Application submitted successfully'], 200);
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }

    }
}
