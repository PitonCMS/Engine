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
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');
        $pageMapper = ($this->container->dataMapper)('PageMapper');

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

        return $this->render('collections/collections.html', $data);
    }

    /**
     * Edit Collection Group
     *
     * Create new collection group, or edit collection group
     */
    public function editCollection($args)
    {
        // Get dependencies
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');
        $json = $this->container->json;
        $toolbox = $this->container->toolbox;

        // Fetch collection group, or create new collection group
        if (isset($args['id']) && is_numeric($args['id'])) {
            $collection = $collectionMapper->findById($args['id']);
        } else {
            $definionParam = $this->request->getQueryParam('definition');

            // Validate that we have a proper definition file name
            if (null === $definionParam || 1 !== preg_match('/^[a-zA-Z0-9]+\.json$/', $definionParam)) {
                throw new Exception("PitonCMS: Invalid query parameter for 'definition': $definionParam");
            }

            // Create new collection and set template JSON file
            $collection = $collectionMapper->make();
            $collection->definition = $definionParam;
        }

        return $this->render('collections/editCollection.html', $collection);
    }

    /**
     * Save Collection
     *
     * Save collection group information
     */
    public function saveCollection()
    {
        // Get dependencies
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');
        $toolbox = $this->container->toolbox;

        // Create collection object and populate
        $collection = $collectionMapper->make();
        $collection->id = $this->request->getParsedBodyParam('id');
        $collection->title = $this->request->getParsedBodyParam('title');
        $collection->slug = $toolbox->cleanUrl($this->request->getParsedBodyParam('slug'));
        $collection->definition = $this->request->getParsedBodyParam('definition');

        $collection = $collectionMapper->save($collection);

        return $this->redirect('adminCollections');
    }

    /**
     * Confirm Delete Collection
     *
     * Shows all pages to be deleted
     */
    public function confirmDeleteCollection($args)
    {
        // Get dependencies
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');
        $pageMapper = ($this->container->dataMapper)('PageMapper');

        $data = $collectionMapper->findById($args['id']);
        $data->pages = $pageMapper->findCollectionPagesById($args['id'], false);

        return $this->render('collections/confirmDeleteCollection.html', $data);
    }

    /**
     * Delete Collection
     *
     * Delete collection group and pages
     */
    public function deleteCollection()
    {
        // Get dependencies
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $pageElementMapper = ($this->container->dataMapper)('PageElementMapper');
        $pageSettingMapper = ($this->container->dataMapper)('PageSettingMapper');

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
        return $this->redirect('adminCollections');
    }
}
