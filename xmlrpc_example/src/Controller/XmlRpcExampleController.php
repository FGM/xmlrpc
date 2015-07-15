<?php

/**
 * @file
 * Contains \Drupal\xmlrpc_example\Controller\XmlRpcExampleController.
 */

namespace Drupal\xmlrpc_example\Controller;

use Drupal\Core\Controller\ControllerBase;
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
    // Make the xmlrpc request.
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
    $build = array(
      'basic' => array(
        '#markup' => $this->t('This XML-RPC example presents code that shows <ul><li><a href="!server">XML-RPC server code</a></li><li><a href="!client">XML-RPC client code</a></li><li>and <a href="!alter">an example hook_xmlrpc_alter() call</a></li></ul>',
          array(
            '!server' => $this->url('xmlrpc_example.server'),
            '!client' => $this->url('xmlrpc_example.client'),
            '!alter' => $this->url('xmlrpc_example.alter'),
          )
        ),
      ),
      'method_array' => array(
        '#theme' => 'item_list',
        '#title' => $this->t('These methods are supported by !server', array('!server' => check_url($server))),
        '#list_type' => 'ul',
        '#items' => $supported_methods,
      ),
    );

    return $build;
  }

}
