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
            padding: 20px;
            width: 300px;
            margin: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .qr-code {
            margin-top: 20px;
            width: 150px;
            height: 150px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>{{ $client->user->nom }} {{ $client->user->prenom }}</h2>

        <!-- Affichage de la photo à partir de l'URL de Cloudinary ou autre -->
        @if($photoPath)
            <img src="{{ $photoPath }}" class="photo" alt="Photo de profil">
        @else
            <p>Photo non disponible</p>
        @endif

        <p><strong>Email:</strong> {{ $client->user->login }}</p>
        <p><strong>Téléphone:</strong> {{ $client->telephone }}</p>

        <!-- Affichage du QR code -->
        @if($qrCodePath)
        <img src="{{ public_path($qrCodePath) }}" alt="QR Code">

@else
    <p>QR Code non disponible</p>
@endif
    </div>
</body>
</html>
