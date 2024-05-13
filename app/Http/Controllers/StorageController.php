<?php

namespace App\Http\Controllers;

use App\Models\AccreditationContent;
use App\Http\Resources\ErrorResource;
use Illuminate\Http\Request;
use Storage;

class StorageController extends Controller
{
    public function showAccreditationFile(Request $request, $path)
    {
        $fullPath = "accreditations/{$path}";
        $storage = Storage::disk('local');
        if ($storage->exists($fullPath)) {
            $content = AccreditationContent::with('accreditation.institution')->where('file', $fullPath)->first();
            if ($content) {
                $filename = $content->filename();
                return $storage->download($fullPath, $filename, ['Filename' => $filename]);
            } else {
                return $storage->download($fullPath);
            }
        }

        return new ErrorResource(__('errors.file_not_found'), 404);
    }

    public function showEvaluationFile(Request $request, $path)
    {
        $fullPath = "evaluations/{$path}";
        $storage = Storage::disk('local');
        if ($storage->exists($fullPath)) {
            return $storage->download($fullPath);
        }

        return new ErrorResource(__('errors.file_not_found'), 404);
    }

    public function showFile(Request $request, $path)
    {
        $storage = Storage::disk('local');
        if ($storage->exists($path)) {
            return $storage->download($path);
        }

        return new ErrorResource(__('errors.file_not_found'), 404);
    }

    public function showCertificateFile(Request $request, $path)
    {
        $fullPath = "certifications/{$path}";
        $storage = Storage::disk('local');
        if ($storage->exists($fullPath)) {
            return $storage->download($fullPath);
        }

        return new ErrorResource(__('errors.file_not_found'), 404);
    }
}
