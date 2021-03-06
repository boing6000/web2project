<?php

/**
 * Produces an INPUT Element of the TEXT type in edit mode. In view mode, it becomes a clickable email address.
 *
 * @package web2project\core
 */

class w2p_Core_CustomFieldEmail extends w2p_Core_CustomFieldText
{
    public function getHTML($mode) {
        switch ($mode) {
            case 'edit':
                $html = $this->field_description . ': </td><td><input type="text" class="text" name="' . $this->fieldName() . '" value="' . $this->charValue() . '" ' . $this->fieldExtraTags() . ' />';
                break;
            case 'view':
                $html = $this->field_description . ': </td><td class="hilite" width="100%">' . w2p_email($this->charValue());
                break;
        }
        return $html;
    }
}