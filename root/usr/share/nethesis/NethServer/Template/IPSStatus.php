<?php

echo $view->header()->setAttribute('template', $T('report_Header'));

echo $view->panel()
   ->insert($view->selector('logs', $view::SELECTOR_DROPDOWN));
echo $view->buttonList($view::BUTTON_SUBMIT);

$host = $view['host'];
$logs = $view->getClientEventTarget('logs');

echo "<pre class='dashboard_ips'>";
echo $view->textLabel('report');
echo "</pre>";


$view->includeJavascript(
"(function ( $ ) {

    $('#dasboard_report_week').attr('src', '$host'+$('.$logs').val()+'/report.html');

    $('.$logs').change( function() {
        v = $(this).val()
        h = '$host';
        $('#dasboard_report_week').attr('src', h+v+'/report.html');
        console.log(h+v);
    });
       
} ( jQuery ));
");

$view->includeCss('
    pre.dashboard_ips {
        border: 2px solid #aaa;
        padding: 10px;
    }
');
