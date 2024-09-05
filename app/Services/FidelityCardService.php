<?php
namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

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
}

