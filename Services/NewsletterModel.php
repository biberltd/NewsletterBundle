<?php

/**
 * NewsletterModel Class
 *
 * This class acts as a database proxy model for NewsletterBundle functionalities.
 *
 * @vendor      BiberLtd
 * @package		Core\Bundles\MemberManagemetBundle
 * @subpackage	Services
 * @name	    MemberManagemetModel
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 * @date        02.01.2014
 *
 * @use         Biberltd\Core\Services\Encryption
 *
 * =============================================================================================================
 * !! INSTRUCTIONS ON IMPORTANT ASPECTS OF MODEL METHODS !!!
 *
 * Each model function must return a $response ARRAY.
 * The array must contain the following keys and corresponding values.
 *
 * $response = array(
 *              'result'    =>   An array that contains the following keys:
 *                               'set'         Actual result set returned from ORM or null
 *                               'total_rows'  0 or number of total rows
 *                               'last_insert_id' The id of the item that is added last (if insert action)
 *              'error'     =>   true if there is an error; false if there is none.
 *              'code'      =>   null or a semantic and short English string that defines the error concanated
 *                               with dots, prefixed with err and the initials of the name of model class.
 *                               EXAMPLE: err.amm.action.not.found success messages have a prefix called scc..
 *
 *                               NOTE: DO NOT FORGET TO ADD AN ENTRY FOR ERROR CODE IN BUNDLE'S
 *                               RESOURCES/TRANSLATIONS FOLDER FOR EACH LANGUAGE.
 * =============================================================================================================
 * TODOs:
 * Do not forget to implement SITE, ORDER, AND PAGINATION RELATED FUNCTIONALITY
 *
 * Newsletter
 * ** v1.1.0
 * @todo v1.1.0         listNewslettersAddedAfter()
 * @todo v1.1.0         listNewslettersAddedBefore()
 * @todo v1.1.0         listNewslettersAddedBetween()
 * @todo v1.1.0         listNewslettersSentAfter()
 * @todo v1.1.0         listNewslettersSentBefore()
 * @todo v1.1.0         listNewslettersSentBetween()
 *
 * NewsletterCategory
 * ** v1.1.0
 * @todo v1.1.0         listNewsletterCategoriesWithMessagesBetween()
 * @todo v1.1.0         listNewsletterCategoriesWithMessagesLessThan()
 * @todo v1.1.0         listNewsletterCategoriesWithMessagesMoreThan()
 *
 */

namespace BiberLtd\Core\Bundles\NewsletterModel\Services;

/** CoreModel */
use BiberLtd\Core\CoreModel;
/** Entities to be used */
use BiberLtd\Core\Bundles\MemberManagementBundle\Entity as BundleEntity;
use BiberLtd\Core\Bundles\MultiLanguageSupportBundle\Entity as MLSEntity;
/** Helper Models */
use BiberLtd\Core\Bundles\SiteManagementBundle\Services as SMMService;
use BiberLtd\Core\Bundles\MultiLanguageSupportBundle\Services as MLSService;
/** Core Service */
use BiberLtd\Core\Services as CoreServices;
use BiberLtd\Core\Exceptions as CoreExceptions;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NewsletterModel extends CoreModel {

    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           object          $kernel
     * @param           string          $db_connection  Database connection key as set in app/config.yml
     * @param           string          $orm            ORM that is used.
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine') {
        parent::__construct($kernel, $db_connection, $orm);
        /**
         * Register entity names for easy reference.
         */
        $this->entity = array(
            'newsletter' => array('name' => 'NewsletterBundle:Newsletter', 'alias' => 'n'),
            'newsletter_category' => array('name' => 'NewsletterBundle:NewsletterCategory', 'alias' => 'nc'),
            'newsletter_category_localization' => array('name' => 'NewsletterBundle:NewsletterCategoryLocalization', 'alias' => 'ncl'),
            'newsletter_localization' => array('name' => 'NewsletterBundle:NewsletterLocalization', 'alias' => 'nl'),
            'newsletter_recipient' => array('name' => 'NewsletterBundle:NewsLetterRecipient', 'alias' => 'nr'),
            'language' => array('name' => 'MultiLanguageSupportBundle:Language', 'alias' => 'l'),
            'site' => array('name' => 'SiteManagementBundle:Site', 'alias' => 's'),
        );
    }

    /**
     * @name            __destruct()
     *                  Destructor.dumpGroupCodes
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __destruct() {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @name 		deleteNewsletter()
     * Deletes an existing item from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->deleteNewsletters()
     *
     * @param           mixed           $newsletter           Entity, id or url key of item
     * @param           string          $by
     *
     * @return          mixed           $response
     */
    public function deleteNewsletter($newsletter, $by = 'entity') {
        return $this->deleteNewsletters(array($newsletter), $by);
    }

    /**
     * @name            deleteNewsletters()
     * Deletes provided items from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection of Newsletter entities, ids, or codes or url keys
     * @param           string          $by             Accepts the following options: entity, id, code, url_key
     *
     * @return          array           $response
     */
    public function deleteNewsletters($collection, $by = 'entity') {
        $this->resetResponse();
        $by_opts = array('entity', 'id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', 'err.invalid.parameter.collection', implode(',', $by_opts));
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $entries = array();
        /** Loop through items and collect values. */
        $delete_count = 0;
        foreach ($collection as $newsletter) {
            $value = '';
            if (is_object($newsletter)) {
                if (!$newsletter instanceof BundleEntity\Newsletter) {
                    return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'BundleEntity\Newsletter');
                }
                $this->em->remove($newsletter);
                $delete_count++;
            } else if (is_numeric($newsletter) || is_string($newsletter)) {
                $value = $newsletter;
            } else {
                /** If array values are not numeric nor object */
                return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'integer, string, or Module entity');
            }
            if (!empty($value) && $value != '') {
                $entries[] = $value;
            }
        }
        /**
         * Control if there is any entity ids in collection.
         */
        if (count($entries) < 1) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $join_needed = false;
        /**
         * Prepare query string.
         */
        switch ($by) {
            case 'entity':
                /** Flush to delete all persisting objects */
                $this->em->flush();
                /**
                 * Prepare & Return Response
                 */
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => null,
                        'total_rows' => $delete_count,
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            case 'id':
                $values = implode(',', $entries);
                break;
            /** Requires JOIN */
            case 'url_key':
                $join_needed = true;
                $values = implode('\',\'', $entries);
                $values = '\'' . $values . '\'';
                break;
        }
        if ($join_needed) {
            $q_str = 'DELETE ' . $this->entity['newsletter']['alias']
                    . ' FROM ' . $this->entity['newsletter_localization']['name'] . ' ' . $this->entity['newsletter_localization']['alias']
                    . ' JOIN ' . $this->entity['newsletter_localization']['name'] . ' ' . $this->entity['newsletter_localization']['alias']
                    . ' WHERE ' . $this->entity['newsletter_localization']['alias'] . '.' . $by . ' IN(:values)';
        } else {
            $q_str = 'DELETE ' . $this->entity['form']['alias']
                    . ' FROM ' . $this->entity['form']['name'] . ' ' . $this->entity['form']['alias']
                    . ' WHERE ' . $this->entity['form']['alias'] . '.' . $by . ' IN(:values)';
        }
        /**
         * Create query object.
         */
        $query = $this->em->createQuery($q_str);
        $query->setParameter('values', $entries);
        /**
         * Free memory.
         */
        unset($values);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $entries,
                'total_rows' => count($entries),
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name            listNewsletters()
     * List items of a given collection.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->resetResponse()
     * @use             $this->createException()
     * @use             $this->prepare_where()
     * @use             $this->createQuery()
     * @use             $this->getResult()
     * 
     * @throws          InvalidSortOrderException
     * @throws          InvalidLimitException
     * 
     *
     * @param           mixed           $filter                Multi dimensional array
     * @param           array           $sortorder              Array
     *                                                              'column'    => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listNewsletters($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }

        /**
         * Add filter check to below to set join_needed to true
         */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';


        /**
         * Start creating the query
         *
         * Note that if no custom select query is provided we will use the below query as a start
         */
        $localizable = true;
        if (is_null($query_str)) {
            if ($localizable) {
                $query_str = 'SELECT ' . $this->entity['newsletter_localization']['alias']
                        . ' FROM ' . $this->entity['newsletter_localization']['name'] . ' ' . $this->entity['newsletter_localization']['alias']
                        . ' JOIN ' . $this->entity['newsletter_localization']['alias'] . '.COLUMN ' . $this->entity['newsletter']['alias'];
            } else {
                $query_str = 'SELECT ' . $this->entity['form']['alias']
                        . ' FROM ' . $this->entity['form']['name'] . ' ' . $this->entity['form']['alias'];
            }
        }
        /*
         * Prepare ORDER BY section of query
         */
        if (!is_null($sortorder)) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'name':
                    case 'url_key':
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /*
         * Prepare WHERE section of query
         */

        if (!is_null($filter)) {
            $filter_str = $this->prepare_where($filter);
            $where_str = ' WHERE ' . $filter_str;
        }



        $query_str .= $where_str . $group_str . $order_str;


        $query = $this->em->createQuery($query_str);

        /*
         * Prepare LIMIT section of query
         */

        if (!is_null($limit) && is_numeric($limit)) {
            /*
             * if limit is set
             */
            if (isset($limit['start']) && isset($limit['count'])) {
                $query = $this->addLimit($query, $limit);
            } else {
                $this->createException('InvalidLimitException', '', 'err.invalid.limit');
            }
        }
        //print_r($query->getSql()); die;
        /*
         * Prepare and Return Response
         */

        $files = $query->getResult();


        $total_rows = count($files);
        if ($total_rows < 1) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $files,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );

        return $this->response;
    }

    /**
     * @name 		getNewsletter()
     * Returns details of a gallery.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listNewsletters()
     *
     * @param           mixed           $newsletter               id, url_key
     * @param           string          $by                 entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getNewsletter($newsletter, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($newsletter) && !is_numeric($newsletter) && !is_string($newsletter)) {
            return $this->createException('InvalidParameterException', 'Newsletter', 'err.invalid.parameter');
        }
        if (is_object($newsletter)) {
            if (!$newsletter instanceof BundleEntity\Newsletter) {
                return $this->createException('InvalidParameterException', 'Newsletter', 'err.invalid.parameter');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $newsletter,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['newsletter_localization']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $newsletter),
                )
            )
        );

        $response = $this->listNewsletters($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 		doesNewsletterExist()
     * Checks if entry exists in database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->getNewsletter()
     *
     * @param           mixed           $newsletter           id, url_key
     * @param           string          $by             id, url_key
     *
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesNewsletterExist($newsletter, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getNewsletter($newsletter, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = $response['result']['set'];
            $error = false;
        } else {
            $exist = false;
            $error = true;
        }

        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 		insertNewsletter()
     * Inserts one or more item into database.
     *
     * @since		1.0.1
     * @version         1.0.3
     * @author          Said Imamoglu
     *
     * @use             $this->insertFiles()
     *
     * @param           array           $newsletter        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertNewsletter($newsletter, $by = 'post') {
        $this->resetResponse();
        return $this->insertNewsletters($newsletter);
    }

    /**
     * @name            insertNewsletters()
     * Inserts one or more items into database.
     *
     * @since           1.0.1
     * @version         1.0.3
     * @author          Said Imamoglu
     *
     * @use             $this->createException()
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array           $collection        Collection of entities or post data.
     * @param           string          $by                entity, post
     *
     * @return          array           $response
     */
    public function insertNewsletters($collection, $by = 'post') {
        /* Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'array() or Integer', 'err.invalid.parameter.collection');
        }

        if (!in_array($by, $this->by_opts)) {
            return $this->createException('InvalidParameterException', implode(',', $this->by_opts), 'err.invalid.parameter.by.collection');
        }

        if ($by == 'entity') {
            $sub_response = $this->insert_entities($collection, 'BiberLtd\\Core\\Bundles\\NewsletterBundle\\Entity\\Newsletter');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.insert.done.',
                );

                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        } elseif ($by == 'post') {

            $locCollection = array();
            foreach ($collection as $newsletter) {
                $localizations = array();
                if (isset($newsletter['localizations'])) {
                    $localizations = $newsletter['localizations'];
                    unset($newsletter['localizations']);
                }
                /** HANDLE FOREIGN DATA :: LOCALIZATIONS */
                if (count($localizations) > 0) {
                    $locCollection = $localizations;
                }
                $assayEntity = new BundleEntity\Newsletter();
                foreach ($newsletter['entity'] as $column => $value) {
                    $newsletterMethod = 'set' . $this->translateColumnName($column);
                    if (method_exists($assayEntity, $newsletterMethod)) {
                        $assayEntity->itemMethod($value);
                    } else {
                        return $this->createException('InvalidMethodException', 'method not found in entity', 'err.method.notfound');
                    }
                    //$this->em->persist($assayEntity);
                }


                //$this->em->flush();
                $this->insert_entities(array($assayEntity), 'BiberLtd\\Core\\Bundles\\NewsletterBundle\\Entity\\Newsletter');
                //echo 'Newsletter eklendi'; die;
                $entityLocalizationCollection = array();
                foreach ($locCollection as $localization) {
                    if ($localization instanceof BundleEntity\NewsletterLocalization) {
                        $entityLocalizationCollection[] = $localization;
                    } else {
                        $localizationEntity = new BundleEntity\NewsletterLocalization;
                        $localizationEntity->setNewsletter($assayEntity);
                        foreach ($localization as $key => $value) {
                            $localizationMethod = 'set' . $this->translateColumnName($key);
                            switch ($key) {
                                case 'language':
                                    $MLSModel = new MLSService\MultiLanguageSupportModel($this->kernel, $this->db_connection, $this->orm);

                                    $response = $MLSModel->getLanguage($value, 'id');
                                    if ($response['error']) {
                                        new CoreExceptions\InvalidLanguageException($this->kernel, $value);
                                        break;
                                    }
                                    $language = $response['result']['set'];
                                    $localizationEntity->setLanguage($language);
                                    unset($response, $MLSModel);
                                    break;
                                default:
                                    if (method_exists($localizationEntity, $localizationMethod)) {
                                        $localizationEntity->localizationMethod($value);
                                    } else {
                                        return $this->createException('InvalidMethodException', 'method not found in entity', 'err.method.notfound');
                                    }
                                    break;
                            }
                            $entityLocalizationCollection[] = $localizationEntity;
                        }
                    }
                }
                $this->insert_entities($entityLocalizationCollection, 'BiberLtd\\Core\\Bundles\\NewsletterBundle\\Entity\\NewsletterLocalization');
            }
            unset($localizationEntity);

            $this->em->flush();

            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $collection,
                    'total_rows' => count($collection),
                    'last_insert_id' => '', //LAST INSERT ID missing..
                ),
                'error' => false,
                'code' => 'scc.db.insert.done',
            );

            return $this->response;
        }
    }

    /*
     * @name            updateNewsletter()
     * Updates single item. The item must be either a post data (array) or an entity
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->resetResponse()
     * @use             $this->updateNewsletters()
     * 
     * @param           mixed   $newsletter     Entity or Entity id of a folder
     * 
     * @return          array   $response
     * 
     */

    public function updateNewsletter($newsletter) {
        $this->resetResponse();
        return $this->updateNewsletters(array($newsletter));
    }

    /*
     * @name            updateNewsletters()
     * Updates one or more item details in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listNewsletters()
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * @param           array   $by             entity or post
     * 
     * @return          array   $response
     * 
     */

    public function updateNewsletters($collection, $by = 'post') {
        if ($by == 'entity') {
            $sub_response = $this->update_entities($collection, 'BundleEntity\Newsletter');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        } elseif ($by == 'post') {
            if (!is_array($collection)) {
                return $this->createException('InvalidParameterException', 'expected an array', 'err.invalid.by');
            }

            $newslettersToUpdate = array();
            $newsletterId = array();
            $count = 0;

            foreach ($collection as $newsletter) {
                if (!isset($newsletter['id'])) {
                    unset($collection[$count]);
                }
                $newsletterId[] = $newsletter['id'];
                $newslettersToUpdate[$newsletter['id']] = $newsletter;
                $count++;
            }
            $filter = array(
                array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['assay']['alias'] . '.id', 'comparison' => 'in', 'value' => $newsletterId),
                        )
                    )
                )
            );
            $response = $this->listNewsletters($filter);
            if ($response['error']) {
                return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
            }

            $entities = $response['result']['set'];

            foreach ($entities as $entity) {
                $newsletterData = $newslettersToUpdate[$entity->getId()];
                foreach ($newsletterData as $column => $value) {
                    $newsletterMethodSet = 'set' . $this->translateColumnName($column);
                    $entity->itemMethodSet($value);
                }
                $this->em->persist($entity);
            }
            $this->em->flush();
        }
    }

    /**
     * @name 		deleteNewsletterCategory()
     * Deletes an existing item from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->deleteNewsletterCategories()
     *
     * @param           mixed           $form           Entity, id or url key of item
     * @param           string          $by
     *
     * @return          mixed           $response
     */
    public function deleteNewsletterCategory($form, $by = 'entity') {
        return $this->deleteNewsletterCategories(array($form), $by);
    }

    /**
     * @name            deleteNewsletterCategories()
     * Deletes provided items from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection of NewsletterCategory entities, ids, or codes or url keys
     * @param           string          $by             Accepts the following options: entity, id, code, url_key
     *
     * @return          array           $response
     */
    public function deleteNewsletterCategories($collection, $by = 'entity') {
        $this->resetResponse();
        $by_opts = array('entity', 'id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', 'err.invalid.parameter.collection', implode(',', $by_opts));
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $entries = array();
        /** Loop through items and collect values. */
        $delete_count = 0;
        foreach ($collection as $form) {
            $value = '';
            if (is_object($form)) {
                if (!$form instanceof BundleEntity\NewsletterCategory) {
                    return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'BundleEntity\NewsletterCategory');
                }
                $this->em->remove($form);
                $delete_count++;
            } else if (is_numeric($form) || is_string($form)) {
                $value = $form;
            } else {
                /** If array values are not numeric nor object */
                return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'integer, string, or Module entity');
            }
            if (!empty($value) && $value != '') {
                $entries[] = $value;
            }
        }
        /**
         * Control if there is any entity ids in collection.
         */
        if (count($entries) < 1) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $join_needed = false;
        /**
         * Prepare query string.
         */
        switch ($by) {
            case 'entity':
                /** Flush to delete all persisting objects */
                $this->em->flush();
                /**
                 * Prepare & Return Response
                 */
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => null,
                        'total_rows' => $delete_count,
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            case 'id':
                $values = implode(',', $entries);
                break;
            /** Requires JOIN */
            case 'url_key':
                $join_needed = true;
                $values = implode('\',\'', $entries);
                $values = '\'' . $values . '\'';
                break;
        }
        if ($join_needed) {
            $q_str = 'DELETE ' . $this->entity['form']['alias']
                    . ' FROM ' . $this->entity['form_localization']['name'] . ' ' . $this->entity['form_localization']['alias']
                    . ' JOIN ' . $this->entity['form_localization']['name'] . ' ' . $this->entity['form_localization']['alias']
                    . ' WHERE ' . $this->entity['form_localization']['alias'] . '.' . $by . ' IN(:values)';
        } else {
            $q_str = 'DELETE ' . $this->entity['form']['alias']
                    . ' FROM ' . $this->entity['form']['name'] . ' ' . $this->entity['form']['alias']
                    . ' WHERE ' . $this->entity['form']['alias'] . '.' . $by . ' IN(:values)';
        }
        /**
         * Create query object.
         */
        $query = $this->em->createQuery($q_str);
        $query->setParameter('values', $entries);
        /**
         * Free memory.
         */
        unset($values);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $entries,
                'total_rows' => count($entries),
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name            listNewsletterCategories()
     * List items of a given collection.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->resetResponse()
     * @use             $this->createException()
     * @use             $this->prepare_where()
     * @use             $this->createQuery()
     * @use             $this->getResult()
     * 
     * @throws          InvalidSortOrderException
     * @throws          InvalidLimitException
     * 
     *
     * @param           mixed           $filter                Multi dimensional array
     * @param           array           $sortorder              Array
     *                                                              'column'    => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listNewsletterCategories($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }

        /**
         * Add filter check to below to set join_needed to true
         */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';


        /**
         * Start creating the query
         *
         * Note that if no custom select query is provided we will use the below query as a start
         */
        $localizable = true;
        if (is_null($query_str)) {
            if ($localizable) {
                $query_str = 'SELECT ' . $this->entity['form_localization']['alias']
                        . ' FROM ' . $this->entity['form_localization']['name'] . ' ' . $this->entity['form_localization']['alias']
                        . ' JOIN ' . $this->entity['form_localization']['alias'] . '.COLUMN ' . $this->entity['form']['alias'];
            } else {
                $query_str = 'SELECT ' . $this->entity['form']['alias']
                        . ' FROM ' . $this->entity['form']['name'] . ' ' . $this->entity['form']['alias'];
            }
        }
        /*
         * Prepare ORDER BY section of query
         */
        if (!is_null($sortorder)) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'name':
                    case 'url_key':
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /*
         * Prepare WHERE section of query
         */

        if (!is_null($filter)) {
            $filter_str = $this->prepare_where($filter);
            $where_str = ' WHERE ' . $filter_str;
        }



        $query_str .= $where_str . $group_str . $order_str;


        $query = $this->em->createQuery($query_str);

        /*
         * Prepare LIMIT section of query
         */

        if (!is_null($limit) && is_numeric($limit)) {
            /*
             * if limit is set
             */
            if (isset($limit['start']) && isset($limit['count'])) {
                $query = $this->addLimit($query, $limit);
            } else {
                $this->createException('InvalidLimitException', '', 'err.invalid.limit');
            }
        }
        //print_r($query->getSql()); die;
        /*
         * Prepare and Return Response
         */

        $files = $query->getResult();


        $total_rows = count($files);
        if ($total_rows < 1) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $files,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );

        return $this->response;
    }

    /**
     * @name 		getNewsletterCategory()
     * Returns details of a gallery.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listNewsletterCategories()
     *
     * @param           mixed           $form               id, url_key
     * @param           string          $by                 entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getNewsletterCategory($form, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($form) && !is_numeric($form) && !is_string($form)) {
            return $this->createException('InvalidParameterException', 'NewsletterCategory', 'err.invalid.parameter');
        }
        if (is_object($form)) {
            if (!$form instanceof BundleEntity\NewsletterCategory) {
                return $this->createException('InvalidParameterException', 'NewsletterCategory', 'err.invalid.parameter');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $form,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['form_localization']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $form),
                )
            )
        );

        $response = $this->listNewsletterCategories($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 		doesNewsletterCategoryExist()
     * Checks if entry exists in database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->getNewsletterCategory()
     *
     * @param           mixed           $form           id, url_key
     * @param           string          $by             id, url_key
     *
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesNewsletterCategoryExist($form, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getNewsletterCategory($form, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = $response['result']['set'];
            $error = false;
        } else {
            $exist = false;
            $error = true;
        }

        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 		insertNewsletterCategory()
     * Inserts one or more item into database.
     *
     * @since		1.0.1
     * @version         1.0.3
     * @author          Said Imamoglu
     *
     * @use             $this->insertFiles()
     *
     * @param           array           $form        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertNewsletterCategory($form, $by = 'post') {
        $this->resetResponse();
        return $this->insertNewsletterCategories($form);
    }

    /**
     * @name            insertNewsletterCategories()
     * Inserts one or more items into database.
     *
     * @since           1.0.1
     * @version         1.0.3
     * @author          Said Imamoglu
     *
     * @use             $this->createException()
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array           $collection        Collection of entities or post data.
     * @param           string          $by                entity, post
     *
     * @return          array           $response
     */
    public function insertNewsletterCategories($collection, $by = 'post') {
        /* Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'array() or Integer', 'err.invalid.parameter.collection');
        }

        if (!in_array($by, $this->by_opts)) {
            return $this->createException('InvalidParameterException', implode(',', $this->by_opts), 'err.invalid.parameter.by.collection');
        }

        if ($by == 'entity') {
            $sub_response = $this->insert_entities($collection, 'BiberLtd\\Core\\Bundles\\NewsletterCategoryBundle\\Entity\\NewsletterCategory');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.insert.done.',
                );

                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        } elseif ($by == 'post') {

            $locCollection = array();
            foreach ($collection as $form) {
                $localizations = array();
                if (isset($form['localizations'])) {
                    $localizations = $form['localizations'];
                    unset($form['localizations']);
                }
                /** HANDLE FOREIGN DATA :: LOCALIZATIONS */
                if (count($localizations) > 0) {
                    $locCollection = $localizations;
                }
                $assayEntity = new BundleEntity\NewsletterCategory();
                foreach ($form['entity'] as $column => $value) {
                    $formMethod = 'set' . $this->translateColumnName($column);
                    if (method_exists($assayEntity, $formMethod)) {
                        $assayEntity->itemMethod($value);
                    } else {
                        return $this->createException('InvalidMethodException', 'method not found in entity', 'err.method.notfound');
                    }
                    //$this->em->persist($assayEntity);
                }


                //$this->em->flush();
                $this->insert_entities(array($assayEntity), 'BiberLtd\\Core\\Bundles\\NewsletterCategoryBundle\\Entity\\NewsletterCategory');
                //echo 'NewsletterCategory eklendi'; die;
                $entityLocalizationCollection = array();
                foreach ($locCollection as $localization) {
                    if ($localization instanceof BundleEntity\NewsletterCategoryLocalization) {
                        $entityLocalizationCollection[] = $localization;
                    } else {
                        $localizationEntity = new BundleEntity\NewsletterCategoryLocalization;
                        $localizationEntity->setNewsletterCategory($assayEntity);
                        foreach ($localization as $key => $value) {
                            $localizationMethod = 'set' . $this->translateColumnName($key);
                            switch ($key) {
                                case 'language':
                                    $MLSModel = new MLSService\MultiLanguageSupportModel($this->kernel, $this->db_connection, $this->orm);

                                    $response = $MLSModel->getLanguage($value, 'id');
                                    if ($response['error']) {
                                        new CoreExceptions\InvalidLanguageException($this->kernel, $value);
                                        break;
                                    }
                                    $language = $response['result']['set'];
                                    $localizationEntity->setLanguage($language);
                                    unset($response, $MLSModel);
                                    break;
                                default:
                                    if (method_exists($localizationEntity, $localizationMethod)) {
                                        $localizationEntity->localizationMethod($value);
                                    } else {
                                        return $this->createException('InvalidMethodException', 'method not found in entity', 'err.method.notfound');
                                    }
                                    break;
                            }
                            $entityLocalizationCollection[] = $localizationEntity;
                        }
                    }
                }
                //echo '<pre>'; print_r($entityLocalizationCollection); die;
                $this->insert_entities($entityLocalizationCollection, 'BiberLtd\\Core\\Bundles\\NewsletterCategoryBundle\\Entity\\NewsletterCategoryLocalization');
                //$this->em->persist($localizationEntity);
            }
            unset($localizationEntity);

            $this->em->flush();

            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $collection,
                    'total_rows' => count($collection),
                    'last_insert_id' => '', //LAST INSERT ID missing..
                ),
                'error' => false,
                'code' => 'scc.db.insert.done',
            );

            return $this->response;
        }
    }

    /*
     * @name            updateNewsletterCategory()
     * Updates single item. The item must be either a post data (array) or an entity
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->resetResponse()
     * @use             $this->updateNewsletterCategories()
     * 
     * @param           mixed   $form     Entity or Entity id of a folder
     * 
     * @return          array   $response
     * 
     */

    public function updateNewsletterCategory($form) {
        $this->resetResponse();
        return $this->updateNewsletterCategories(array($form));
    }

    /*
     * @name            updateNewsletterCategories()
     * Updates one or more item details in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listNewsletterCategories()
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * @param           array   $by             entity or post
     * 
     * @return          array   $response
     * 
     */

    public function updateNewsletterCategories($collection, $by = 'post') {
        if ($by == 'entity') {
            $sub_response = $this->update_entities($collection, 'BundleEntity\NewsletterCategory');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        } elseif ($by == 'post') {
            if (!is_array($collection)) {
                return $this->createException('InvalidParameterException', 'expected an array', 'err.invalid.by');
            }

            $formsToUpdate = array();
            $formId = array();
            $count = 0;

            foreach ($collection as $form) {
                if (!isset($form['id'])) {
                    unset($collection[$count]);
                }
                $formId[] = $form['id'];
                $formsToUpdate[$form['id']] = $form;
                $count++;
            }
            $filter = array(
                array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['assay']['alias'] . '.id', 'comparison' => 'in', 'value' => $formId),
                        )
                    )
                )
            );
            $response = $this->listNewsletterCategories($filter);
            if ($response['error']) {
                return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
            }

            $entities = $response['result']['set'];

            foreach ($entities as $entity) {
                $formData = $formsToUpdate[$entity->getId()];
                foreach ($formData as $column => $value) {
                    $formMethodSet = 'set' . $this->translateColumnName($column);
                    $entity->itemMethodSet($value);
                }
                $this->em->persist($entity);
            }
            $this->em->flush();
        }
    }

    /**
     * @name 		deleteNewsletterRecipient()
     * Deletes an existing item from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->deleteNewsletterRecipients()
     *
     * @param           mixed           $item           Entity, id or url key of item
     * @param           string          $by
     *
     * @return          mixed           $response
     */
    public function deleteNewsletterRecipient($item, $by = 'entity') {
        return $this->deleteNewsletterRecipients(array($item), $by);
    }

    /**
     * @name            deleteNewsletterRecipients()
     * Deletes provided items from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection of NewsletterRecipient entities, ids, or codes or url keys
     * @param           string          $by             Accepts the following options: entity, id, code, url_key
     *
     * @return          array           $response
     */
    public function deleteNewsletterRecipients($collection, $by = 'entity') {
        $this->resetResponse();
        $by_opts = array('entity', 'id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', 'err.invalid.parameter.collection', implode(',', $by_opts));
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $entries = array();
        /** Loop through items and collect values. */
        $delete_count = 0;
        foreach ($collection as $item) {
            $value = '';
            if (is_object($item)) {
                if (!$item instanceof BundleEntity\NewsletterRecipient) {
                    return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'BundleEntity\NewsletterRecipient');
                }
                $this->em->remove($item);
                $delete_count++;
            } else if (is_numeric($item) || is_string($item)) {
                $value = $item;
            } else {
                /** If array values are not numeric nor object */
                return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'integer, string, or Module entity');
            }
            if (!empty($value) && $value != '') {
                $entries[] = $value;
            }
        }
        /**
         * Control if there is any entity ids in collection.
         */
        if (count($entries) < 1) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $join_needed = false;
        /**
         * Prepare query string.
         */
        switch ($by) {
            case 'entity':
                /** Flush to delete all persisting objects */
                $this->em->flush();
                /**
                 * Prepare & Return Response
                 */
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => null,
                        'total_rows' => $delete_count,
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            case 'id':
                $values = implode(',', $entries);
                break;
            /** Requires JOIN */
            case 'url_key':
                $join_needed = false;
                $values = implode('\',\'', $entries);
                $values = '\'' . $values . '\'';
                break;
        }
        if ($join_needed) {
            $q_str = 'DELETE ' . $this->entity['newsletter_recipient']['alias']
                    . ' FROM ' . $this->entity['newsletter_recipient_localization']['name'] . ' ' . $this->entity['newsletter_recipient_localization']['alias']
                    . ' JOIN ' . $this->entity['newsletter_recipient_localization']['name'] . ' ' . $this->entity['newsletter_recipient_localization']['alias']
                    . ' WHERE ' . $this->entity['newsletter_recipient_localization']['alias'] . '.' . $by . ' IN(:values)';
        } else {
            $q_str = 'DELETE ' . $this->entity['newsletter_recipient']['alias']
                    . ' FROM ' . $this->entity['newsletter_recipient']['name'] . ' ' . $this->entity['newsletter_recipient']['alias']
                    . ' WHERE ' . $this->entity['newsletter_recipient']['alias'] . '.' . $by . ' IN(:values)';
        }
        /**
         * Create query object.
         */
        $query = $this->em->createQuery($q_str);
        $query->setParameter('values', $entries);
        /**
         * Free memory.
         */
        unset($values);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $entries,
                'total_rows' => count($entries),
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name            listNewsletterRecipients()
     * List items of a given collection.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->resetResponse()
     * @use             $this->createException()
     * @use             $this->prepare_where()
     * @use             $this->createQuery()
     * @use             $this->getResult()
     * 
     * @throws          InvalidSortOrderException
     * @throws          InvalidLimitException
     * 
     *
     * @param           mixed           $filter                Multi dimensional array
     * @param           array           $sortorder              Array
     *                                                              'column'    => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listNewsletterRecipients($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }

        /**
         * Add filter check to below to set join_needed to true
         */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';


        /**
         * Start creating the query
         *
         * Note that if no custom select query is provided we will use the below query as a start
         */
        $localizable = false;
        if (is_null($query_str)) {
            if ($localizable) {
                $query_str = 'SELECT ' . $this->entity['newsletter_recipient_localization']['alias']
                        . ' FROM ' . $this->entity['newsletter_recipient_localization']['name'] . ' ' . $this->entity['newsletter_recipient_localization']['alias']
                        . ' JOIN ' . $this->entity['newsletter_recipient_localization']['alias'] . '.COLUMN ' . $this->entity['newsletter_recipient']['alias'];
            } else {
                $query_str = 'SELECT ' . $this->entity['newsletter_recipient']['alias']
                        . ' FROM ' . $this->entity['newsletter_recipient']['name'] . ' ' . $this->entity['newsletter_recipient']['alias'];
            }
        }
        /*
         * Prepare ORDER BY section of query
         */
        if (!is_null($sortorder)) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'name':
                    case 'url_key':
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /*
         * Prepare WHERE section of query
         */

        if (!is_null($filter)) {
            $filter_str = $this->prepare_where($filter);
            $where_str = ' WHERE ' . $filter_str;
        }



        $query_str .= $where_str . $group_str . $order_str;


        $query = $this->em->createQuery($query_str);

        /*
         * Prepare LIMIT section of query
         */

        if (!is_null($limit) && is_numeric($limit)) {
            /*
             * if limit is set
             */
            if (isset($limit['start']) && isset($limit['count'])) {
                $query = $this->addLimit($query, $limit);
            } else {
                $this->createException('InvalidLimitException', '', 'err.invalid.limit');
            }
        }
        //print_r($query->getSql()); die;
        /*
         * Prepare and Return Response
         */

        $files = $query->getResult();


        $total_rows = count($files);
        if ($total_rows < 1) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $files,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );

        return $this->response;
    }

    /**
     * @name 		getNewsletterRecipient()
     * Returns details of a gallery.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listNewsletterRecipients()
     *
     * @param           mixed           $item               id, url_key
     * @param           string          $by                 entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getNewsletterRecipient($item, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($item) && !is_numeric($item) && !is_string($item)) {
            return $this->createException('InvalidParameterException', 'NewsletterRecipient', 'err.invalid.parameter');
        }
        if (is_object($item)) {
            if (!$item instanceof BundleEntity\NewsletterRecipient) {
                return $this->createException('InvalidParameterException', 'NewsletterRecipient', 'err.invalid.parameter');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $item,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['newsletter_recipient']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $item),
                )
            )
        );

        $response = $this->listNewsletterRecipients($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 		doesNewsletterRecipientExist()
     * Checks if entry exists in database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->getNewsletterRecipient()
     *
     * @param           mixed           $item           id, url_key
     * @param           string          $by             id, url_key
     *
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesNewsletterRecipientExist($item, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getNewsletterRecipient($item, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = $response['result']['set'];
            $error = false;
        } else {
            $exist = false;
            $error = true;
        }

        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 		insertNewsletterRecipient()
     * Inserts one or more item into database.
     *
     * @since		1.0.1
     * @version         1.0.3
     * @author          Said Imamoglu
     *
     * @use             $this->insertFiles()
     *
     * @param           array           $item        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertNewsletterRecipient($item, $by = 'post') {
        $this->resetResponse();
        return $this->insertNewsletterRecipients($item);
    }

    /**
     * @name            insertNewsletterRecipients()
     * Inserts one or more items into database.
     *
     * @since           1.0.1
     * @version         1.0.3
     * @author          Said Imamoglu
     *
     * @use             $this->createException()
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array           $collection        Collection of entities or post data.
     * @param           string          $by                entity, post
     *
     * @return          array           $response
     */
    public function insertNewsletterRecipients($collection, $by = 'post') {
        /* Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'array() or Integer', 'err.invalid.parameter.collection');
        }

        if (!in_array($by, $this->by_opts)) {
            return $this->createException('InvalidParameterException', implode(',', $this->by_opts), 'err.invalid.parameter.by.collection');
        }

        if ($by == 'entity') {
            $sub_response = $this->insert_entities($collection, 'BiberLtd\\Core\\Bundles\\NewsletterBundle\\Entity\\NewsletterRecipient');
        } elseif ($by == 'post') {

            foreach ($collection as $item) {
                $entity = new \BiberLtd\Core\Bundles\NewsletterBundle\Entity\NewsletterRecipient();
                foreach ($item['address'] as $column => $value) {
                    $itemMethod = 'set_' . $column;
                    if (method_exists($entity, $itemMethod)) {
                        $entity->itemMethod($value);
                    } else {
                        return $this->createException('InvalidMethodException', 'method not found in entity', 'err.method.notfound');
                    }
                }
                unset($item, $column, $value);
                $this->em->persist($entity);
            }
            $this->em->flush();
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $collection,
                    'total_rows' => count($collection),
                    'last_insert_id' => $entity->getId(),
                ),
                'error' => false,
                'code' => 'scc.db.insert.done',
            );

            return $this->response;
        }
    }

    /*
     * @name            updateNewsletterRecipient()
     * Updates single item. The item must be either a post data (array) or an entity
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->resetResponse()
     * @use             $this->updateNewsletterRecipients()
     * 
     * @param           mixed   $item     Entity or Entity id of a folder
     * 
     * @return          array   $response
     * 
     */

    public function updateNewsletterRecipient($item) {
        $this->resetResponse();
        return $this->updateNewsletterRecipients(array($item));
    }

    /*
     * @name            updateNewsletterRecipients()
     * Updates one or more item details in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listNewsletterRecipients()
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * @param           array   $by             entity or post
     * 
     * @return          array   $response
     * 
     */

    public function updateNewsletterRecipients($collection, $by = 'post', $type = 'all') {
        if ($by == 'entity') {
            $sub_response = $this->update_entities($collection, 'BundleEntity\NewsletterRecipient');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        } elseif ($by == 'post') {
            if (!is_array($collection)) {
                return $this->createException('InvalidParameterException', 'expected an array', 'err.invalid.by');
            }

            $itemsToUpdate = array();
            $itemId = array();
            $count = 0;

            foreach ($collection as $item) {
                if (!isset($item['id'])) {
                    unset($collection[$count]);
                }
                $itemId[] = $item['id'];
                $itemsToUpdate[$item['id']] = $item;
                $count++;
            }
            $filter = array(
                array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['newsletter_recipient']['alias'] . '.id', 'comparison' => 'in', 'value' => $itemId),
                        )
                    )
                )
            );
            $response = $this->listNewsletterRecipients($filter);
            if ($response['error']) {
                return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
            }

            $entities = $response['result']['set'];

            foreach ($entities as $entity) {
                $itemData = $itemsToUpdate[$entity->getId()];
                if ($type == 'all') {
                    foreach ($itemData as $column => $value) {
                        $itemMethodSet = 'set_' . $column;
                        $entity->$itemMethodSet($value);
                    }
                } else {
                    if ($type !== 'a' || $type !== 'i') {
                        return $this->createException('InvalidParameterException', 'Type of status(a or i)', 'err.invalid.parameter.collection');
                    }
                    $entity->setStatus($value);
                }

                $this->em->persist($entity);
            }
            $this->em->flush();
        }
    }

    /*
     * @name            activateNewsletterRecipient()
     * Activate one or more item details in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->update_entities()updateNewsletterRecipient
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * @param           array   $by             entity or post
     * 
     * @return          array   $response
     * 
     */

    public function activateNewsletterRecipient($collection, $by) {
        $this->updateNewsletterRecipients($collection, $by, 'a');
    }

    /*
     * @name            deactivateNewsletterRecipient()
     * Deactivate one or more item details in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->update_entities()updateNewsletterRecipient
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * @param           array   $by             entity or post
     * 
     * @return          array   $response
     * 
     */

    public function deactivateNewsletterRecipient($collection, $by) {
        $this->updateNewsletterRecipients($collection, $by, 'a');
    }

    /*
     * @name            listActivateNewsletterRecipients()
     * Lists active newsletter recpients in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->listNewsletterRecipients()
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * @param           array   $by             entity or post
     * 
     * @return          array   $response
     * 
     */

    public function listActivateNewsletterRecipients($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['newsletter_recipient']['alias'] . '.status', 'comparison' => '=', 'value' => 'a'),
                )
            )
        );
        $this->listNewsletterRecipients($filter, $sortorder, $limit, $query_str);
    }
    /*
     * @name            listNewsletterRecipientsOfRecipient()
     * Lists newsletter recipients of newsletter in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->listNewsletterRecipients()
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * @param           array   $by             entity or post
     * 
     * @return          array   $response
     * 
     */

    public function listNewsletterRecipientsOfRecipient($recipient,$filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['newsletter_recipient']['alias'] . '.recipient', 'comparison' => '=', 'value' => $recipient),
                )
            )
        );
        $this->listNewsletterRecipients($filter, $sortorder, $limit, $query_str);
    }

}

/**
 * Change Log
 * **************************************
 * v1.0.0                      Said İmamoğlu
 * 23.01.2014
 * **************************************
 * A deleteNewsletter()
 * A deleteNewsletters()
 * A doesNewsletterExist()
 * A getNewsletter()
 * A listNewsletters()
 * A listNewslettersInCategory()
 * A insertNewsletter()
 * A insertNewsletters()
 * A updateNewsletter()
 * A updateNewsletters()
 * A deleteNewsletterCategory()
 * A deleteNewsletterCategories()
 * A doesNewsletterCategoryExist()
 * A getNewsletterCategory()
 * A listNewsletterCategories()
 * A insertNewsletterCategory()
 * A insertNewsletterCategories()
 * A updateNewsletterCategory()
 * A updateNewsletterCategories()
 * A activateNewsletterRecipient()
 * A deActivateNewsletterRecipient()
 * A deleteNewsletterRecipient()
 * A deleteNewsletterRecipients()
 * A doesNewsletterRecipientExist()
 * A getNewsletterRecipient()
 * A listActivatedNewsletterRecipients()
 * A listActivatedNewsletterRecipientsOfCategory()
 * A listNewsletterRecipients()
 * A listNewsletterRecipientsOfCategory()
 * A insertNewsletterRecipient()
 * A insertNewsletterRecipients()
 * A updateNewsletterRecipient()
 * A updateNewsletterRecipients()
 * 
 * **************************************
 * v1.0.0                      Can Berkol
 * 02.01.2014
 * **************************************
 * A __construct()
 * A __destruct()
 *
 */