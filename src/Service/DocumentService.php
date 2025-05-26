<?php

namespace GeoLibre\Service;

use GeoLibre\Model\Document;
use GeoLibre\Model\User;
use Psr\Http\Message\UploadedFileInterface;

class DocumentService
{
    private $uploadDir;
    private $document;
    private $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
        'text/csv'
    ];

    public function __construct(string $uploadDir, Document $document)
    {
        $this->uploadDir = $uploadDir;
        $this->document = $document;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    }

    public function createDocument(array $data, ?UploadedFileInterface $file, User $user): array
    {
        if (!$file) {
            throw new \Exception('No file uploaded');
        }

        if (!in_array($file->getClientMediaType(), $this->allowedTypes)) {
            throw new \Exception('File type not allowed');
        }

        $filename = $this->generateUniqueFilename($file);
        $file->moveTo($this->uploadDir . '/' . $filename);

        return [
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'file_path' => $filename,
            'file_type' => $file->getClientMediaType(),
            'file_size' => $file->getSize(),
            'is_public' => isset($data['is_public']),
            'user_id' => $user->id
        ];
    }

    public function updateDocument(int $id, array $data): array
    {
        return [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_public' => isset($data['is_public']) ? true : false
        ];
    }

    public function deleteDocument(int $id): bool
    {
        $document = $this->document->findById($id);
        if ($document) {
            $filePath = $this->uploadDir . '/' . $document['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return $this->document->delete($id);
        }
        return false;
    }

    private function generateUniqueFilename(UploadedFileInterface $file): string
    {
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        return uniqid() . '_' . time() . '.' . $extension;
    }

    public function getPublicDocuments(): array
    {
        return $this->document->getPublicDocuments();
    }
} 