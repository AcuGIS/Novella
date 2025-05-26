<?php

declare(strict_types=1);

namespace GeoLibre\Controller;

use GeoLibre\Model\Document;
use GeoLibre\Model\User;
use GeoLibre\Service\DocumentService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

class DocumentController
{
    private Document $document;
    private DocumentService $documentService;
    private Twig $twig;
    private LoggerInterface $logger;
    private User $userModel;

    public function __construct(
        Document $document,
        DocumentService $documentService,
        Twig $twig,
        LoggerInterface $logger,
        User $userModel
    ) {
        $this->document = $document;
        $this->documentService = $documentService;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->userModel = $userModel;
    }

    public function index(Request $request, Response $response): Response
    {
        $user = $this->getCurrentUser();
        $documents = $this->document->findByUserId($user->id);
        return $this->twig->render($response, 'documents/index.twig', [
            'documents' => $documents
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'documents/create.twig');
    }

    public function store(Request $request, Response $response): Response
    {
        $user = $this->getCurrentUser();
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $file = $files['document'] ?? null;

        try {
            $documentData = $this->documentService->createDocument($data, $file, $user);
            $this->document->create($documentData);
            $this->flashMessage('success', 'Document uploaded successfully');
        } catch (\Exception $e) {
            $this->logger->error('Document upload failed: ' . $e->getMessage());
            $this->flashMessage('error', 'Failed to upload document: ' . $e->getMessage());
        }

        return $response->withHeader('Location', '/documents')->withStatus(302);
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $document = $this->document->findById((int)$args['id']);
        if (!$document) {
            throw new \Slim\Exception\HttpNotFoundException($request);
        }

        return $this->twig->render($response, 'documents/edit.twig', [
            'document' => $document
        ]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $document = $this->document->findById((int)$args['id']);
        if (!$document) {
            throw new \Slim\Exception\HttpNotFoundException($request);
        }

        $data = $request->getParsedBody();
        $updateData = $this->documentService->updateDocument((int)$args['id'], $data);
        
        if ($this->document->update((int)$args['id'], $updateData)) {
            $this->flashMessage('success', 'Document updated successfully');
        } else {
            $this->flashMessage('error', 'Failed to update document');
        }

        return $response->withHeader('Location', '/documents')->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if ($this->documentService->deleteDocument((int)$args['id'])) {
            $this->flashMessage('success', 'Document deleted successfully');
        } else {
            $this->flashMessage('error', 'Failed to delete document');
        }

        return $response->withHeader('Location', '/documents')->withStatus(302);
    }

    public function togglePublic(Request $request, Response $response, array $args): Response
    {
        if ($this->document->togglePublic((int)$args['id'])) {
            $this->flashMessage('success', 'Document visibility updated successfully');
        } else {
            $this->flashMessage('error', 'Failed to update document visibility');
        }

        return $response->withHeader('Location', '/documents')->withStatus(302);
    }

    public function download(Request $request, Response $response, array $args): Response
    {
        $filename = basename($args['filename']); // Prevent directory traversal
        $filePath = __DIR__ . '/../../storage/documents/' . $filename;

        if (!file_exists($filePath)) {
            throw new \Slim\Exception\HttpNotFoundException($request);
        }

        $stream = new \Slim\Psr7\Stream(fopen($filePath, 'rb'));
        return $response
            ->withHeader('Content-Type', mime_content_type($filePath))
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withBody($stream);
    }

    private function getCurrentUser(): User
    {
        if (!isset($_SESSION['user']['id'])) {
            throw new \Exception('User not authenticated');
        }
        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->findById((int)$userId);
        if (!$user) {
            throw new \Exception('User not found');
        }
        return $user;
    }

    private function flashMessage(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
} 