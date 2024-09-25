<!DOCTYPE html>
<html>
<head>
    <title>Email send by - {{ $name }}</title>
</head>
<body>
    <h1>Contact from {{ $name }}</h1>
    <p>{{ $body }}</p><br />
    <br />
    From the: {{ $email }}
    with the phone number: {{ $phone }}
</body>
</html>
