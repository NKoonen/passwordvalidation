<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_1($module)
{
    $result = true;

    foreach ([
        'additionalCustomerFormFields',
        'validateCustomerFormFields',
        'actionSubmitAccountBefore',
    ] as $hookName) {
        if (!$module->isRegisteredInHook($hookName)) {
            $result &= $module->registerHook($hookName);
        }
    }

    return $result;
}
