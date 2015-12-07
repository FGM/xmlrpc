<?php

/**
 * @file
 * Contains \Drupal\xmlrpc_test\Tests\XmlRpcValidatorTest.
 */

namespace Drupal\xmlrpc_test\Tests;

/**
 * Tests large messages and method alterations.
 *
 * @group xmlrpc
 */
class XmlRpcMessagesTest extends XmlRpcTestBase {
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name'  => 'XML-RPC message and alteration',
      'description' => 'Test large messages and method alterations.',
      'group' => 'XML-RPC',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp('xmlrpc_test');
  }

  /**
   * Make sure that XML-RPC can transfer large messages.
   */
  public function testSizedMessages() {
    // These tests can produce up to 128 x 160 words in the XML-RPC message
    // (see xmlrpc_test_message_sized_in_kb()) with 4 tags used to represent
    // each. Set a large enough tag limit to allow this to be tested.
    variable_set('xmlrpc_message_maximum_tag_count', 100000);

    $sizes = array(8, 80, 160);
    foreach ($sizes as $size) {
      $xml_message_l = xmlrpc_test_message_sized_in_kb($size);
      $xml_message_r = $this->xmlRpcGet(array('messages.messageSizedInKB' => array($size)));

      $this->assertEqual($xml_message_l, $xml_message_r, format_string('XML-RPC messages.messageSizedInKB of %s Kb size received', array('%s' => $size)));
    }
  }

  /**
   * Ensure that hook_xmlrpc_alter() can hide even builtin methods.
   */
  public function testAlterListMethods() {
    // Ensure xmlrpc_test_xmlrpc_alter() is disabled and retrieve regular list
    // of methods.
    variable_set('xmlrpc_test_xmlrpc_alter', FALSE);
    $methods1 = $this->xmlRpcGet(array('system.listMethods' => array()));

    // Enable the alter hook and retrieve the list of methods again.
    variable_set('xmlrpc_test_xmlrpc_alter', TRUE);
    $methods2 = $this->xmlRpcGet(array('system.listMethods' => array()));

    $diff = array_diff($methods1, $methods2);
    $this->assertTrue(is_array($diff) && !empty($diff), 'Method list is altered by hook_xmlrpc_alter');
    $removed = reset($diff);
    $this->assertEqual($removed, 'system.methodSignature', 'Hiding builtin system.methodSignature with hook_xmlrpc_alter works');
  }

  /**
   * Ensure that XML-RPC client sets correct encoding in request http headers.
   */
  public function testRequestContentTypeDefinition() {
    $headers = xmlrpc($this->getEndpoint(), array('test.headerEcho' => array()));
    $this->assertIdentical($headers['Content-Type'], 'text/xml; charset=utf-8');
  }

  /**
   * Check XML-RPC client and server encoding information.
   *
   * Ensure that XML-RPC client sets correct processing instructions for XML
   * documents.
   *
   * Ensure that XML-RPC server sets correct encoding in response http headers
   * and processing instructions for XML documents.
   */
  public function testRequestAndResponseEncodingDefinitions() {
    $url = $this->getEndpoint();

    // We can't use the [_]xmlrpc() function here, because we have to access the
    // full drupal_http_request response.
    require_once DRUPAL_ROOT . '/includes/xmlrpc.inc';
    $xmlrpc_request = xmlrpc_request('system.listMethods', array());

    $headers = array('Content-Type' => 'text/xml; charset=utf-8');
    $options = array(
      'header' => $headers,
      'method' => 'POST',
      'data' => $xmlrpc_request->xml,
    );
    $result = drupal_http_request($url, $options);
    $status = intval($result->code);
    $this->assertEqual($status, 200, 'Request transport succeeded');

    $message = xmlrpc_message($result->data);
    $this->assertTrue(xmlrpc_message_parse($message), 'Message is well formed');

    $this->assertNotEqual($message->messagetype === 'fault', 'Message is not a fault');

    $headers = $result->headers;
    $content_type = $headers['content-type'];

    // The request string starts with the XML processing instruction.
    $this->assertIdentical(0, strpos($xmlrpc_request->xml, '<?xml version="1.0" encoding="utf-8" ?>'), 'Request Processing Instruction is "&lt;?xml version="1.0" encoding="utf-8" ?&gt;"');

    // The response body has to start with the xml processing instruction.
    $this->assertIdentical(strpos($result->data, '<?xml version="1.0" encoding="utf-8" ?>'), 0, 'Response Processing Instruction is "&lt;?xml version="1.0" encoding="utf-8" ?&gt;"');
    $this->assertIdentical($content_type, 'text/xml; charset=utf-8');
  }

}
