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
        $collectionMapper = $mapper('CollectionMapper');
        $pageMapper = $mapper('PageMapper');

        // Fetch collections and page details
        $data['collections'] = $collectionMapper->find();
        $data['collectionDetails'] = $pageMapper->findCollectionPages(false);

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
        $collectionMapper = $mapper('CollectionMapper');
        $json = $this->container->json;
        $toolbox = $this->container->toolbox;

        // Fetch collection group, or create new collection group
        if (isset($args['id']) && is_numeric($args['id'])) {
            $collection = $collectionMapper->findById($args['id']);
        } else {
            // Create new collection
            $collection = $collectionMapper->make();
        }

        // Get custom collection schemas
        $theme = $this->container->get('settings')['site']['theme'];
        $path = ROOT_DIR . "themes/{$theme}/templates/collection/";
        $collection->custom = $toolbox->getDirectoryFiles($path, ['^_.+','\.html']);

        foreach ($collection->custom as $key => $file) {
            if (null === $collection->custom[$key]['json'] = $json->getCollectionDefinition($path . $file['filename'])) {
                $this->setAlert('danger', 'Custom Collection Error', $json->getErrorMessages());
                break;
            }
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
        $collectionMapper = $mapper('CollectionMapper');
        $toolbox = $this->container->toolbox;

        // Create collection object and populate with POST data
        $collection = $collectionMapper->make();
        $collection->id = $this->request->getParsedBodyParam('id');
        $collection->title = $this->request->getParsedBodyParam('title');
        $collection->slug = $toolbox->cleanUrl($this->request->getParsedBodyParam('slug'));
        $collection->kind = $this->request->getParsedBodyParam('kind');

        $collection = $collectionMapper->save($collection);

        return $this->redirect('showCollections');
    }

    /**
     * Delete Collection
     *
     * Delete collection and details
     */
    public function deleteCollection()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $collectionMapper = $mapper('CollectionMapper');

        if ($this->request->getParsedBodyParam('button') === 'delete' && $this->request->getParsedBodyParam('id')) {
            // Delete collection
            $collection = $collectionMapper->make();
            $collection->id = $this->request->getParsedBodyParam('id');
            $collectionMapper->delete($collection);
        } else {
            throw new Exception('Invalid collection delete request.');
        }

        // Redirect back to show collections
        return $this->redirect('showCollections');
    }
}
