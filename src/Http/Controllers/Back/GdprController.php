<?php

namespace MediactiveDigital\MedKit\Http\Controllers\Back;

use Soved\Laravel\Gdpr\Http\Controllers\GdprController as SovedGdprController;

use Soved\Laravel\Gdpr\Events\GdprDownloaded;
use Soved\Laravel\Gdpr\Http\Requests\GdprDownload;

class GdprController extends SovedGdprController {

    /**
     * Download the GDPR compliant data portability JSON file.
     *
     * @param \Soved\Laravel\Gdpr\Http\Requests\GdprDownload $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function download(GdprDownload $request) {

        if (!$this->validateRequest($request)) {

            return $this->sendFailedLoginResponse();
        }

        $user = $request->user();

        event(new GdprDownloaded($user));

        $data = $user->portable();
        $filename = 'user.csv';

        $headers = [
            'Content-type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {

            $file = fopen('php://output', 'w');

            // Fix UTF-8 BOM pour Excel.
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            fputcsv($file, array_keys($data), ',');
            fputcsv($file, array_values($data), ',');

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
