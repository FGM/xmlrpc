<?php
/**
 * @file
 * Contains \Drupal\xmlrpc_test\Tests\XmlRpcTestBase.
 */

namespace Drupal\xmlrpc_test\Tests;

/**
 * A base class simplifying xmlrpc() calls testing.
 */
abstract class XmlRpcTestBase extends \DrupalWebTestCase {
  /**
   * The XML-RPC server endpoint.
   *
   * @var string
   */
  protected $endPoint;

  /**
   * Are verbose results enabled ?
   *
   * @var bool
   */
  protected $verbose;

  /**
   * Provides detailed response information if verbose is enabled.
   *
   * @param mixed $result
   *   A XML-RPC result.
   */
  protected function verboseResult($result) {
    // Skip evaluating the response information if it is not needed.
    if (!$this->verbose) {
      return;
    }

    if ($result === FALSE) {
      $this->verbose(format_string('Result: <pre>@result</pre><br />Errno: @errno<br />Message: @message', array(
        '@result' => var_export($result, TRUE),
        '@errno' => xmlrpc_errno(),
        '@message' => xmlrpc_error_msg(),
      )));
    }
    else {
      $this->verbose('<pre>' . var_export($result, TRUE) . '</pre>');
    }
  }

  /**
   * Invokes xmlrpc method.
   *
   * @param array $args
   *   An associative array whose keys are the methods to call and whose values
   *   are the arguments to pass to the respective method. If multiple methods
   *   are specified, a system.multicall is performed.
   * @param array $headers
   *   (optional) An array of headers to pass along.
   *
   * @return mixed
   *   The result of xmlrpc() function call.
   *
   * @see xmlrpc()
   */
  protected function xmlRpcGet(array $args, array $headers = array()) {
    $url = $this->getEndpoint();
    $result = xmlrpc($url, $args, $headers);
    $this->verboseResult($result);
    return $result;
  }

  /**
   * Returns the URI of the server XML-RPC endpoint.
   *
   * @return string
   *   The server endpoint URI.
   */
  public function getEndpoint() {
    if (!isset($this->endPoint)) {
      $uri = url(NULL, array('absolute' => TRUE)) . 'xmlrpc.php';
      $this->endPoint = $uri;
    }

    return $this->endPoint;
  }

}
