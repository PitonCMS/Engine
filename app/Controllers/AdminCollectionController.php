<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Controllers;

/**
 * Piton Admin Collection Controller
 */
class AdminCollectionController extends AdminBaseController
{
    /**
     * Show Collections
     *
     */
    public function showCollections()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $CollectionMapper = $mapper('CollectionMapper');

        // Fetch collections
        $collections = $CollectionMapper->find();

        return $this->render('collections.html', ['collections' => $collections]);
    }

    /**
     * Edit Collection Group
     *
     * Create new collection group, or edit collection group
     */
    public function editCollection($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $CollectionMapper = $mapper('CollectionMapper');

        // Fetch collection group, or create new collection group
        if (is_numeric($args['id'])) {
            $collection = $CollectionMapper->findById($args['id']);
        } else {
            // New collection
            $collection = $CollectionMapper->make();
        }

        return $this->render('editCollection.html', $collection);
    }

    /**
     * Save Collection
     *
     * Create new collection, or update existing collection
     */
    public function saveCollection()
    {
        // // Get dependencies
        $mapper = $this->container->dataMapper;
        $CollectionMapper = $mapper('CollectionMapper');
        $Markdown = $this->container->markdownParser;

        // // Create collection object and populate with POST data
        $collection = $CollectionMapper->make();
        $collection->id = $this->request->getParsedBodyParam('id');
        $collection->title = $this->request->getParsedBodyParam('title');
        $collection->content_raw = $this->request->getParsedBodyParam('content_raw');
        $collection->content = $Markdown->text($this->request->getParsedBodyParam('content_raw'));
        // $collection->expansion = $this->request->getParsedBodyParam('expansion');
        $collection = $CollectionMapper->save($collection);

        return $this->redirect('showCollections');
    }

    /**
     * Delete Collection
     *
     * Delete collection and collection details
     */
    public function deleteCollection()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $CollectionMapper = $mapper('CollectionMapper');
        $CollectionDetailMapper = $mapper('CollectionDetailMapper');

        if ($this->request->getParsedBodyParam('button') === 'delete' && $this->request->getParsedBodyParam('id')) {
            // Delete collection
            $collection = $CollectionMapper->make();
            $collection->id = $this->request->getParsedBodyParam('id');
            $collection = $CollectionMapper->delete($collection);

            // Delete collection details
            $CollectionDetailMapper->deleteByCollectionId($collection->id);
        } else {
            throw new Exception('Invalid collection delete request.');
        }

        // Redirect back to show collections
        return $this->redirect('showCollections');
    }

    /**
     * Edit Collection Detail
     *
     * @param array $args 'collection' Collection ID
     *                    'id' Collection Detail ID
     */
    public function editCollectionDetail($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $CollectionMapper = $mapper('CollectionMapper');
        $CollectionDetailMapper = $mapper('CollectionDetailMapper');

        // Get collection detail and collection summary data
        if (isset($args['id']) && is_numeric($args['id'])) {
            $data = $CollectionDetailMapper->findById($args['id']);
        } else {
            $data = $CollectionDetailMapper->make();
        }

        // Get collection summary information
        $data->collection = $CollectionMapper->findById($args['collection']);

        return $this->render('editCollectionDetail.html', $data);
    }
}
