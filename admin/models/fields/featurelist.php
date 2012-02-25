<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

/**
 * Form Field class for the Joomla Platform.
 * Provides a list of features
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.3
 */
class JFormFieldFeatureList extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.3
     */
    public $type = 'featureList';

    /**
     * Method to get the list of features.
     *
     * @return  string  The field input markup.
     * @see JHtmlFeatures
     */
    protected function getInput()
    {
        $subtype = $this->element['subtype'];

        $params = array(
		    'id' => $this->id,
		    'class' => (string) $this->element['class'],
        );

        // Verify if some fields have relashionship
        switch ($subtype) {
            case 'departments':
                if ($this->_hasRelationShip()) {
                    $this->_ajaxUpdateList ('department_id', 'town_id', 'get_towns');
                }
                break;
                 
            case 'towns':
                if ($this->_hasRelationShip()) {
                    $this->_ajaxUpdateList ('town_id', 'area_id', 'get_areas');
                    return JHtml::_('features.towns', $this->value, $this->name, $params, $this->form->getValue('department_id'));
                }
            case 'areas':
                if ($this->_hasRelationShip()) {
                    return JHtml::_('features.areas', $this->value, $this->name, $params, $this->form->getValue('town_id'));
                }
        }

        return JHtml::_('features.'.$subtype , $this->value, $this->name, $params );
    }

    /**
     * Verify relationship component parameter
     * @return boolean
     */
    private function _hasRelationShip()
    {
        $params = JComponentHelper::getParams('com_jea');
        return (bool) $params->get('relationship_dpts_towns_area', 0);
    }

    private function _ajaxUpdateList ($fromId, $toId, $task)
    {
        $fieldTo = $this->form->getField($toId);

        if (!empty($fieldTo->id)) {
            JFactory::getDocument()->addScriptDeclaration("
                window.addEvent('domready', function() {
                    document.id('{$this->id}').addEvent('change', function() {
                        var jSonRequest = new Request.JSON({
                            url: 'index.php',
                            onSuccess: function(response) {
                                var first = document.id('{$fieldTo->id}').getFirst().clone();
                                document.id('{$fieldTo->id}').empty();
                                document.id('{$fieldTo->id}').adopt(first);
                                if (response) {
                                    response.each(function(item) {
                                        var option  = new Element('option', {'value' : item.id});
                                        option.appendText(item.value);
                                        document.id('{$fieldTo->id}').adopt(option);
                                    });
                                }
                            }
                         });
                         jSonRequest.get({
                             'option' : 'com_jea',
                             'format' : 'json',
                             'task' : 'ajax.{$task}',
                             {$fromId} : this.value
                         });
                     });
                });"
            );
        }
    }
}