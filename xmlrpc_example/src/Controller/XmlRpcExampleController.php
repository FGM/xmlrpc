<?php

/**
 * @file
 * Contains \Drupal\xmlrpc_example\Controller\XmlRpcExampleController.
 */

namespace Drupal\xmlrpc_example\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\xmlrpc_example\XmlRpcExampleTrait;

/**
 * Controller methods for basic documentation pages in this module.
 */
class XmlRpcExampleController extends ControllerBase {

  use XmlRpcExampleTrait;

  /**
   * Constructs a page with info about the XML-RPC example.
   *
   * Our router maps this method to the path 'examples/xmlrpc'.
   */
  public function info() {
    // Make the XML-RPC request.
    $server = $this->getEndpoint();
    $options = array('system.listMethods' => array());
    $supported_methods = xmlrpc($server, $options);

    // Tell the user if there was an error.
    if ($supported_methods === FALSE) {
      drupal_set_message($this->t('Error return from xmlrpc(): Error: @errno, Message: @message', array(
        '@errno' => xmlrpc_errno(),
        '@message' => xmlrpc_error_msg(),
      )));
    }

    // Process the results.
    $build = [
      'basic' => [
        '#theme' => 'item_list',
        '#title' => $this->t('This XML-RPC example presents code that shows'),
        '#items' => [
          $this->l($this->t('XML-RPC server code'), Url::fromRoute('xmlrpc_example.server')),
          $this->l($this->t('XML-RPC client code'), Url::fromRoute('xmlrpc_example.client')),
          $this->l($this->t('An example hook_xmlrpc_alter() call'), Url::fromRoute('xmlrpc_example.alter')),
        ],
      ],
      'method_array' => [
        '#theme' => 'item_list',
        '#title' => $this->t('These methods are supported by :url', [
          ':url' => UrlHelper::stripDangerousProtocols($server),
        ]),
        '#list_type' => 'ul',
        '#items' => $supported_methods,
      ],
    ];

    return $build;
  }

}
