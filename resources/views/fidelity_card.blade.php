<!-- resources/views/fidelity_card.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Fidelity Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .card {
            border: 1px solid #000;
            padding: 20px;
            width: 300px;
            margin: auto;
        }
        .photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>{{ $client->user->nom }} {{ $client->user->prenom }}</h2>
      <img src="{{ $photoPath }}" class="photo" alt="Photo">
      <p><strong>Email:</strong> {{ $client->user->login }}</p>
        <p><strong>Téléphone:</strong> {{ $client->telephone }}</p>
        <img src="{{ public_path($qrCodePath) }}" alt="QR Code">
    </div>
</body>
</html>
