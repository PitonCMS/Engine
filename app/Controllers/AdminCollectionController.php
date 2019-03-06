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
     * Show Collections and Collection Pages
     *
     * Show all collection groups, collection templates, and collection pages
     */
    public function showCollections()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $collectionMapper = $mapper('CollectionMapper');
        $pageMapper = $mapper('PageMapper');

        // Fetch collection pages, and chuck pages into sub-array by collection ID with meta info
        $collectionPages = $pageMapper->findCollectionPages();
        foreach ($collectionPages as $col) {
            if (!isset($data['collectionPages'][$col->collection_id])) {
                $data['collectionPages'][$col->collection_id]['collection_id'] = $col->collection_id;
                $data['collectionPages'][$col->collection_id]['collection_title'] = $col->collection_title;
                $data['collectionPages'][$col->collection_id]['collection_slug'] = $col->collection_slug;
            }

            $data['collectionPages'][$col->collection_id]['pages'][] = $col;
        }

        // Get available templates and collection groups
        $data['templates'] = $this->getPageTemplates('collection');
        $data['collections'] = $collectionMapper->find();

        // Enrich collections array with matching description from templates array
        $templateArray = array_column($data['templates'], 'filename');
        array_walk($data['collections'], function (&$collect) use ($data, $templateArray) {
            // Find matching collection template key for reference in $templateArray
            $key = array_search($collect->definition, $templateArray);
            $collect->templateName = $data['templates'][$key]['name'];
            $collect->templateDescription = $data['templates'][$key]['description'];
        });

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
        if (is_numeric($args['id'])) {
            $collection = $collectionMapper->findById($args['id']);
        } elseif (is_string($args['id'])) {
            // Create new collection and set template JSON file
            $collection = $collectionMapper->make();
            $collection->definition = $args['id'];
        }

        return $this->render('editCollection.html', $collection);
    }

    /**
     * Save Collection
     *
     * Save collection group information
     */
    public function saveCollection()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $collectionMapper = $mapper('CollectionMapper');
        $toolbox = $this->container->toolbox;

        // Create collection object and populate
        $collection = $collectionMapper->make();
        $collection->id = $this->request->getParsedBodyParam('id');
        $collection->title = $this->request->getParsedBodyParam('title');
        $collection->slug = $toolbox->cleanUrl($this->request->getParsedBodyParam('slug'));
        $collection->definition = $this->request->getParsedBodyParam('definition');

        $collection = $collectionMapper->save($collection);

        return $this->redirect('showCollections');
    }

    /**
     * Confirm Delete Collection
     *
     * Shows all pages to be deleted
     */
    public function confirmDeleteCollection($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $collectionMapper = $mapper('CollectionMapper');
        $pageMapper = $mapper('PageMapper');

        $data = $collectionMapper->findById($args['id']);
        $data->pages = $pageMapper->findCollectionPagesById($args['id'], false);

        return $this->render('confirmDeleteCollection.html', $data);
    }

    /**
     * Delete Collection
     *
     * Delete collection group and pages
     */
    public function deleteCollection()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $collectionMapper = $mapper('CollectionMapper');
        $pageMapper = $mapper('PageMapper');
        $pageElementMapper = $mapper('PageElementMapper');
        $pageSettingMapper = $mapper('PageSettingMapper');

        if (null !== $collectionId = $this->request->getParsedBodyParam('id')) {
            // Get list of pages to delete
            $pages = $pageMapper->findCollectionPagesById($collectionId, false);

            foreach ($pages as $value) {
                $page = $pageMapper->make();
                $page->id = $value->id;
                $pageMapper->delete($page);

                $pageElementMapper->deleteElementsByPageId($value->id);
                $pageSettingMapper->deleteByPageId($value->id);
            }

            // Last, delete collection
            $collection = $collectionMapper->make();
            $collection->id = $collectionId;
            $collectionMapper->delete($collection);
        }

        // Redirect back to show collections
        return $this->redirect('showCollections');
    }
}
