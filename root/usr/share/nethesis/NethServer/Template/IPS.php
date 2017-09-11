<?php

echo $view->header()->setAttribute('template', $T('IPS_header'));

echo $view->panel()
     ->insert($view->fieldset()->setAttribute('template',$T('IPS_status_label'))
        ->insert($view->checkbox('status', 'enabled', $view::FIELDSETSWITCH_CHECKBOX)->setAttribute('uncheckedValue', 'disabled'))
     );


if (!$view['categories']) {
     echo "<div class='wspreline ui-state-warning ui-state-highlight noRules ruleCategories'><i class='fa'></i>".$T('no_rules_label')."</div>";
} else {
    $fieldset = $view->panel();
    foreach($view['categories'] as $category) {
        $fieldset->insert(
            $view->selector('Categories_'.$category, $view::SELECTOR_DROPDOWN | $view::LABEL_LEFT)->setAttribute('choices',  \Nethgui\Widget\XhtmlWidget::hashToDatasource($view['actions']))
        );
    }
    echo "<div class='ruleCategories'>".$T('RuleCategories_label')."</div>";
    echo "<div class='ruleColumns'>";
    echo $fieldset;
    echo "</div>";
}
$buttons =  $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
$buttons->insert($view->button('download', $view::BUTTON_LINK));
echo $buttons;

$view->includeCss("
.ruleColumns {
    column-count: 2;
    margin-left: 10px;
}
.ruleCategories {
    margin-bottom: 5px;
    font-weight: bold;
}
.noRules {
    border: 1px solid #f8893f;
    color: #592003;
    background-color: #fee4bd;
    margin: 8px;
    padding: .8em;
    display: inline-block;
}
");
