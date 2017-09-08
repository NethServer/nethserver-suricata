<?php

echo $view->header()->setAttribute('template', $T('IPS_header'));

echo $view->panel()
     ->insert($view->fieldset()->setAttribute('template',$T('status_label'))
        ->insert($view->fieldsetSwitch('status', 'enabled', $view::FIELDSETSWITCH_CHECKBOX)
            ->insert($view->selector('RuleCategories', $view::SELECTOR_MULTIPLE ))
        )->setAttribute('uncheckedValue', 'disabled')
     );

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);

