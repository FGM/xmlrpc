<?php

/**
 * @file
 * Contains \Drupal\xmlrpc_example\Form\XmlRpcExampleServerForm.
 *
 * User interface for the XML-RPC Server part.
 *
 * A server does not require an interface at all. In this implementation we use
 * a server configuration form to set the limits available for the addition and
 * subtraction operations.
 */

namespace Drupal\xmlrpc_example\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Configuration form of the XML-RPC service.
 *
 * In this case the maximum and minimum values for any of the operations (add
 * or subtraction).
 */
class XmlRpcExampleServerForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      'xmlrpc_example.server',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xmlrpc_example_server';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('xmlrpc_example.server');

    $form['explanation'] = array(
      '#markup' => '<div>' . t('This is the XML-RPC server configuration page.<br />Here you may define the maximum and minimum values for the addition or subtraction exposed services.<br />') . '</div>',
    );
    $form['min'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter the minimum value returned by the subtraction or addition methods'),
      '#description' => t('If the result of the operation is lower than this value, a custom XML-RPC error will be returned: 10002.'),
      '#default_value' => $config->get('min'),
      '#size' => 5,
      '#required' => TRUE,
    );
    $form['max'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter the maximum value returned by sub or add methods'),
      '#description' => t('if the result of the operation is bigger than this value, a custom XML-RPC error will be returned: 10001.'),
      '#default_value' => $config->get('max'),
      '#size' => 5,
      '#required' => TRUE,
    );
    $form['info'] = array(
      '#type' => 'markup',
      '#markup' => '<div>' . t('Use the <a href="!link">XML-RPC Client example form</a> to experiment.', array(
        '!link' => $this->url('xmlrpc_example.client'),
      )),
    );

    if ($config->get('alter_enabled')) {
      $form['overridden'] = array(
        '#type' => 'markup',
        '#markup' => '<div><strong>' . t('Just a note of warning: The <a href="!link">alter form</a> has been used to disable the limits, so you may want to turn that off if you do not want it.', array(
          '!link' => $this->url('xmlrpc_example.alter'),
        )) . '</strong></div>',
      );
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('xmlrpc_example.server')
      ->set('min', $form_state->getValue('min'))
      ->set('max', $form_state->getValue('max'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
