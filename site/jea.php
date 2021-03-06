<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('JeaUpload', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/upload.php');

$input = JFactory::getApplication()->input;

if ($input->getCmd('task') == '')
{
	// Set 'controllers/default.php' as default controller and 'display' as default method
	$input->set('task', 'default.display');
}

$controller = JControllerLegacy::getInstance('jea');
$controller->execute($input->getCmd('task'));
$controller->redirect();
