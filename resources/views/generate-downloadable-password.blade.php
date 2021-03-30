<!DOCTYPE html>
<html>
<head>
    <title>Nipoj Password Generator</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500&display=swap"
        rel="stylesheet"
    />
    <style>
        * {
            box-sizing: border-box;
            font-family: "Kanit", sans-serif;
        }
        body {
            background: #cecece;
        }
        h2 {
            color: #1A0D7C;
            font-weight: 500;
        }
        .wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            padding-top: 8vh;
            align-items: flex-start;
            justify-content: center;
        }
        .card {
            background: white;
            border-radius: 7px;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            min-width: 320px;

        }
        img {
            height: 20px
        }
        code {
            display: inline-block;
            border-radius: 4px;
            background: #00215B30;
            padding: 10px;
            letter-spacing: 2px;
            font-size: 2rem;
            font-weight: 600;
            color: #00215B;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <img src="{{asset('logo.png')}}" alt="" />
        <h2>Generated Downloadable Document OTP</h2>
        <p>An OTP was successfully generated and sent by email to {{$email}} and it will expire at {{$expiry}}, the OTP is shown below:</p>
        <code>{{$password}}</code>
    </div>
</div>

</body>
</html>
