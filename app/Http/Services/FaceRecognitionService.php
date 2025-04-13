<?php

namespace App\Http\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FaceRecognitionService
{
    public static function recognize(UploadedFile $photo, Media $reference)
    {
        return Http::asMultipart()
            ->attach('photo', fopen($photo->getRealPath(), 'r'), $photo->getClientOriginalName())
            ->attach('references', fopen($reference->getPath(), 'r'))
            ->post(config('services.face_recognition.base_url') . '/verify-face');
    }

    public static function checkFacePresence(UploadedFile $photo)
    {
        return Http::asMultipart()
            ->attach('image', fopen($photo->getRealPath(), 'r'), $photo->getClientOriginalName())
            ->post(config('services.face_recognition.base_url') . '/check-face-presence');
    }
}
