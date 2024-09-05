<!DOCTYPE html>
<html>
<head>
    <title>Fidelity Card</title>
</head>
<body>
    <p>Dear {{ $client->user->nom }} {{ $client->user->prenom }},</p>
    <p>Attached is your fidelity card. Please keep it safe.</p>
    <p>Thank you for being with us!</p>
</body>
</html>
