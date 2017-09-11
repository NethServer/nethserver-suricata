<?php

echo $view->header()->setAttribute('template', $T('IPS_header'));

echo $view->panel()
     ->insert($view->fieldset()->setAttribute('template',$T('IPS_status_label'))
        ->insert($view->checkbox('status', 'enabled', $view::FIELDSETSWITCH_CHECKBOX)->setAttribute('uncheckedValue', 'disabled'))
     );

#$fieldset = $view->fieldset()->setAttribute('template',$T('Rules_label'));
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
echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);

$view->includeCss("
.ruleColumns {
    column-count: 2;
    margin-left: 10px;
}
.ruleCategories {
    margin-bottom: 5px;
    font-weight: bold;
}
");
