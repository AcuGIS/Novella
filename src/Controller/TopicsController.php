<?php

namespace GeoLibre\Controller;

use GeoLibre\Model\Topic;
use GeoLibre\Model\Dataset;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TopicsController
{
    private $topic;
    private $dataset;
    private $view;

    public function __construct(Topic $topic, Dataset $dataset, Twig $view)
    {
        $this->topic = $topic;
        $this->dataset = $dataset;
        $this->view = $view;
    }

    public function index(Request $request, Response $response)
    {
        $topics = $this->topic->with('datasets')->get();
        return $this->view->render($response, 'topics/index.twig', [
            'topics' => $topics
        ]);
    }

    public function create(Request $request, Response $response)
    {
        return $this->view->render($response, 'topics/edit.twig', [
            'topic' => $this->topic,
            'datasets' => $this->dataset->all(),
            'selected_datasets' => []
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        
        // Extract datasets from the data
        $datasets = $data['datasets'] ?? [];
        unset($data['datasets']); // Remove datasets from the main data array
        
        // Add user information
        $data['created_by'] = $_SESSION['user_id'];
        $data['updated_by'] = $_SESSION['user_id'];

        // Create the topic
        $topicId = $this->topic->create($data);

        // Associate datasets with the topic
        if (!empty($datasets) && $topicId) {
            $topic = Topic::createWithDb($this->topic->getDb(), $topicId);
            $topic->datasets()->sync($datasets);
        }

        return $response->withHeader('Location', '/topics')->withStatus(302);
    }

    public function edit(Request $request, Response $response, array $args)
    {
        $topicData = $this->topic->findOrFail($args['id']);
        $topic = Topic::createWithDb($this->topic->getDb(), $topicData['id']);
        foreach ($topicData as $key => $value) {
            $topic->$key = $value;
        }
        
        $datasets = $this->dataset->all();
        $selected_datasets = $topic->datasets()->getIds();
        
        return $this->view->render($response, 'topics/edit.twig', [
            'topic' => $topic,
            'datasets' => $datasets,
            'selected_datasets' => $selected_datasets
        ]);
    }

    public function update(Request $request, Response $response, array $args)
    {
        $topicData = $this->topic->findOrFail($args['id']);
        $topic = Topic::createWithDb($this->topic->getDb(), $topicData['id']);
        foreach ($topicData as $key => $value) {
            $topic->$key = $value;
        }

        $data = $request->getParsedBody();
        
        // Extract datasets from the data
        $datasets = $data['datasets'] ?? [];
        unset($data['datasets']); // Remove datasets from the main data array
        
        $data['updated_by'] = $_SESSION['user_id'];

        $topic->update($topic->id, $data);

        // Update dataset associations
        if (!empty($datasets)) {
            $topic->datasets()->sync($datasets);
        } else {
            $topic->datasets()->detach();
        }

        return $response->withHeader('Location', '/topics')->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args)
    {
        $topicData = $this->topic->findOrFail($args['id']);
        $topic = Topic::createWithDb($this->topic->getDb(), $topicData['id']);
        foreach ($topicData as $key => $value) {
            $topic->$key = $value;
        }
        
        $topic->delete($topic->id);

        return $response->withHeader('Location', '/topics')->withStatus(302);
    }
} 