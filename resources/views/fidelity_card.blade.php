<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fidelity Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .card {
            border: 1px solid #000;
            padding: 60px; /* Further increased padding */
            width: 700px; /* Further increased width */
            margin: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .photo {
            width: 200px; /* Increased photo size */
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 40px;
        }
        .qr-code {
            margin-top: 40px;
            width: 250px; /* Increased QR code size */
            height: 250px;
        }
    </style>
</head>
<body>
    <div class="card">
        <!-- Displaying client name -->
        <h2>
            @if ($client->user)
                {{ $client->user->nom }} {{ $client->user->prenom }}
            @else
                <p>Client information unavailable</p>
            @endif
        </h2>

        <!-- Displaying profile photo -->
        @if($photoPath)
            <img src="{{ $photoPath }}" class="photo" alt="Photo de profil">
        @else
            <p>Photo non disponible</p>
        @endif

        <!-- Safely checking if user exists before displaying login -->
        @if ($client->user)
            <p><strong>Email:</strong> {{ $client->user->login }}</p>
        @else
            <p><strong>Email:</strong> Non disponible</p>
        @endif

        <p><strong>Téléphone:</strong> {{ $client->telephone }}</p>

        <!-- Displaying QR code -->
        @if($qrCodePath)
            <img src="{{ public_path($qrCodePath) }}" class="qr-code" alt="QR Code">
        @else
            <p>QR Code non disponible</p>
        @endif
    </div>
</body>
</html>
