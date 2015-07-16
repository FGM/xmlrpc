<?php
/**
 * @file
 * XmlRpcControllerBase.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\xmlrpc_example;

use Drupal\Core\Url;

trait XmlRpcExampleTrait {
  /**
   * The XML-RPC server endpoint.
   *
   * @var string
   */
  protected $endPoint;

  /**
   * Returns the URI of the server XML-RPC endpoint.
   *
   * @return string
   *   The server endpoint URI.
   */
  public function getEndpoint() {
    if (!isset($this->endPoint)) {
      $uri = Url::fromUri($GLOBALS['base_url'] . '/xmlrpc.php');
      $this->endPoint = $uri->toUriString();
    }

    return $this->endPoint;
  }

}
