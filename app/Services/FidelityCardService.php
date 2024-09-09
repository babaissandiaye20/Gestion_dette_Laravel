<?php
namespace App\Services;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
class FidelityCardService
{
    public function generatePdf(string $view, array $data, string $filePath)
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->save($filePath);
    }

    public function generateFidelityCard($client, $qrCodePath, $photoPath): string
    {
        $data = [
            'client' => $client,
            'qrCodePath' => $qrCodePath,
            'photoPath' => $photoPath,
        ];

        $filePath = storage_path('app/public/fidelity_cards/client_' . $client->id . '.pdf');

        // Generate the PDF using the view and data
        $this->generatePdf('fidelity_card', $data, $filePath);

        // Return the path to the generated PDF
        return $filePath;
    }
    public function generateFidelityCardForClient(Client $client, string $qrCodePath): string
    {
        $user = $client->user;
        $photoUrl = $user->photo;
        $encodedPhoto = $this->encodePhotoToBase64($photoUrl);

        $fidelityCardPath = $this->generateFidelityCard($client, $qrCodePath, $encodedPhoto);

        return $fidelityCardPath;
    }
    protected function encodePhotoToBase64($photoUrl)
{
    if ($photoUrl) {
        try {
            $imageData = file_get_contents($photoUrl);
            $imageExtension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            return 'data:image/' . $imageExtension . ';base64,' . base64_encode($imageData);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'encodage de la photo en base64 : ' . $e->getMessage());
            return null;
        }
    }
    return null;
}


}

