<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private string $uploadDir,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileExtension = $file->guessExtension();

        if (!$fileExtension) {
            throw new \RuntimeException('Unable to guess the file extension.');
        }

        $fileName = $safeFilename . '-' . uniqid() . '.' . $fileExtension;

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw new FileException("Echec du téléchargement de l'image: {$e->getMessage()}", 0, $e);
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->uploadDir;
    }
}
