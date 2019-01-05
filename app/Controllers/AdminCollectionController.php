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
        $CollectionDetailMapper = $mapper('CollectionDetailMapper');

        // Fetch collections
        $data['collections'] = $CollectionMapper->find();

        // Fetch collection details
        $data['collectionDetails'] = $CollectionDetailMapper->find();

        return $this->render('collections.html', $data);
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
        if (isset($args['id']) && is_numeric($args['id'])) {
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

        // Create collection object and populate with POST data
        $collection = $CollectionMapper->make();
        $collection->id = $this->request->getParsedBodyParam('id');
        $collection->title = $this->request->getParsedBodyParam('title');
        // $collection->expansion = $this->request->getParsedBodyParam('expansion');

        // Prep URL Slug
        $collection->slug = strtolower(trim($this->request->getParsedBodyParam('slug')));
        $collection->slug = preg_replace('/[^a-z0-9\s-]/', '', $collection->slug);
        $collection->slug = preg_replace('/[\s-]+/', ' ', $collection->slug);
        $collection->slug = preg_replace('/[\s]/', '-', $collection->slug);

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

    /**
     * Save Collection Detail
     *
     * Create new collection detail, or update collection detail
     */
    public function saveCollectionDetail()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $CollectionDetailMapper = $mapper('CollectionDetailMapper');
        $Markdown = $this->container->markdownParser;
        $Toolbox = $this->container->toolbox;

        // Create collection detail object and populate POST data
        $collectionDetail = $CollectionDetailMapper->make();
        $collectionDetail->id = $this->request->getParsedBodyParam('id');
        $collectionDetail->collection_id = $this->request->getParsedBodyParam('collection_id');
        $collectionDetail->title = $this->request->getParsedBodyParam('title');
        $collectionDetail->sub_title = $this->request->getParsedBodyParam('sub_title');
        $collectionDetail->content_raw = $this->request->getParsedBodyParam('content_raw');
        $collectionDetail->content = $Markdown->text($this->request->getParsedBodyParam('content_raw'));
        $collectionDetail->summary_image_path = $this->request->getParsedBodyParam('summary_image_path');
        $collectionDetail->detail_image_path = $this->request->getParsedBodyParam('detail_image_path');

        // Process published date
        $collectionDetail->published_date = ($this->request->getParsedBodyParam('published_date')) ?: '';
        if (!empty($collectionDetail->published_date)) {
        /*
        @link: http://php.net/strtotime
        Dates in the m/d/y or d-m-y formats are disambiguated by looking at the separator between the various
        components: if the separator is a slash (/), then the American m/d/y is assumed; whereas if the separator
        is a dash (-) or a dot (.), then the European d-m-y format is assumed.
        */
            $publishedDate = strtotime($collectionDetail->published_date);
            $collectionDetail->published_date = date('Y-m-d', $publishedDate);
        }

        // Prep URL Slug
        $collectionDetail->slug = strtolower(trim($this->request->getParsedBodyParam('slug')));
        $collectionDetail->slug = preg_replace('/[^a-z0-9\s-]/', '', $collectionDetail->slug);
        $collectionDetail->slug = preg_replace('/[\s-]+/', ' ', $collectionDetail->slug);
        $collectionDetail->slug = preg_replace('/[\s]/', '-', $collectionDetail->slug);

        // Save Page and get ID
        $collectionDetail = $CollectionDetailMapper->save($collectionDetail);

        // Redirect back to show collections
        return $this->redirect('showCollections');
    }

    /**
     * Delete Collection Detail
     *
     */
    public function deleteCollectionDetail()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $CollectionDetailMapper = $mapper('CollectionDetailMapper');

        if ($this->request->getParsedBodyParam('button') === 'delete' && $this->request->getParsedBodyParam('id')) {
            // Delete collection
            $collectionDetail = $CollectionDetailMapper->make();
            $collectionDetail->id = $this->request->getParsedBodyParam('id');
            $collectionDetail = $CollectionDetailMapper->delete($collectionDetail);
        } else {
            throw new Exception('Invalid page delete request.');
        }

        // Redirect back to show collections
        return $this->redirect('showCollections');
    }
}
