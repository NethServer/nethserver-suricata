<?php

echo $view->header()->setAttribute('template', $T('IPS_header'));

echo $view->panel()
     ->insert($view->fieldset()->setAttribute('template',$T('status_label'))
        ->insert($view->fieldsetSwitch('status', 'enabled', $view::FIELDSETSWITCH_EXPANDABLE)
            ->insert($view->selector('Policy', $view::SELECTOR_DROPDOWN))
        )
        ->insert($view->fieldsetSwitch('status', 'disabled'))
     );

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);

