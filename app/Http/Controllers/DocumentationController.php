<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class DocumentationController extends Controller
{
    /**
     * Download the feature & access permission guide PDF.
     */
    public function downloadPdf()
    {
        if (! auth()->user() || (! auth()->user()->isSuperAdmin() && ! auth()->user()->isIt())) {
            abort(403, 'Akses dokumen panduan fitur hanya diperuntukkan bagi Super Admin dan IT.');
        }

        $pdf = Pdf::loadView('docs.system_guide_pdf')
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        // Also ensure a static copy exists in public/docs
        $docDir = public_path('docs');
        if (! File::exists($docDir)) {
            File::makeDirectory($docDir, 0755, true);
        }
        $staticPath = $docDir.DIRECTORY_SEPARATOR.'Panduan_Fitur_Master_Raport_Al_Athifah.pdf';
        file_put_contents($staticPath, $pdf->output());

        return $pdf->download('Panduan_Fitur_dan_Akses_Master_Raport_Al_Athifa.pdf');
    }

    /**
     * Generate static PDF file if needed.
     */
    public static function generateStaticFile(): string
    {
        $pdf = Pdf::loadView('docs.system_guide_pdf')
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        $docDir = public_path('docs');
        if (! File::exists($docDir)) {
            File::makeDirectory($docDir, 0755, true);
        }
        $staticPath = $docDir.DIRECTORY_SEPARATOR.'Panduan_Fitur_Master_Raport_Al_Athifah.pdf';
        file_put_contents($staticPath, $pdf->output());

        return $staticPath;
    }
}
