<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_t3rest_util_ServiceRegistry');


t3lib_extMgm::addService($_EXTKEY,  't3rest' /* sv type */,  'tx_t3rest_srv_Member' /* sv key */,
  array(
    'title' => 'T3rest logging service', 'description' => 'Access to logging data', 'subtype' => 'logs',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => t3lib_extMgm::extPath($_EXTKEY).'srv/class.tx_t3rest_srv_Logs.php',
    'className' => 'tx_t3rest_srv_Logs',
  )
);

