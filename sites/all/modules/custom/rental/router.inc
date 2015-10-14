<?php

function router()
{
    $path = request_path();
    switch ($path) {
        case 'dashboard':
            $page = array(
                '#markup' => theme('dashboard', array()),
            );
            break;
        case '':
            $page = array(
                '#markup' => theme('dashboard', array()),
            );
            break;
        case 'accounts':
            $variables = array();
            $page = array(
                '#markup' => theme('accounts', $variables),
            );
            break;
        case 'income':
            $variables = array();
            $page = array(
                '#markup' => theme('income', $variables),
            );
            break;
        case 'billing':
            $variables = array();
            $page = array(
                '#markup' => theme('billing', $variables),
            );
            break;
        case 'units':
            $variables = array();
            $page = array(
                '#markup' => theme('units', $variables),
            );
            break;
        case 'tenants':
            $variables = array();
            $page = array(
                '#markup' => theme('tenants', $variables),
            );
            break;
        case 'debtors':
            $variables = array();
            $page = array(
                '#markup' => theme('debtors', $variables),
            );
            break;
        case 'reports':
            $variables = array();
            $page = array(
                '#markup' => theme('reports', $variables),
            );
            break;
        case 'expenses':
            $variables = array();
            $page = array(
                '#markup' => theme('expenses', $variables),
            );
            break;
        case 'transactions':
            $variables = array();
            $page = array(
                '#markup' => theme('transactions', $variables),
            );
            break;
        case 'mytransactions':
            $variables = array();
            $page = array(
                '#markup' => theme('mytransactions', $variables),
            );
            break;
        case 'mybilling':
            $variables = array();
            $page = array(
                '#markup' => theme('mybilling', $variables),
            );
            break;
        case 'receipt':
            $variables = array();
            $page = array(
                '#markup' => theme('receipt', $variables),
            );
            break;
        case 'invoice':
            $variables = array();
            $page = array(
                '#markup' => theme('invoice', $variables),
            );
            break;
        case 'voucher':
            $variables = array();
            $page = array(
                '#markup' => theme('voucher', $variables),
            );
            break;
        case 'unit-history':
            $variables = array();
            $page = array(
                '#markup' => theme('unit-history', $variables),
            );
            break;
        case 'backups':
            $variables = array();
            $page = array(
                '#markup' => theme('backups', $variables),
            );
            break;
        case 'mypayments':
            $variables = array();
            $page = array(
                '#markup' => theme('mypayments', $variables),
            );
            break;
        case 'unit-expenses':
            $variables = array();
            $page = array(
                '#markup' => theme('unit-expenses', $variables),
            );
            break;
        case 'unit-profit-loss':
            $variables = array();
            $page = array(
                '#markup' => theme('unit-profit-loss', $variables),
            );
            break;
        case 'unit-name-profit-loss':
            $variables = array();
            $page = array(
                '#markup' => theme('unit-name-profit-loss', $variables),
            );
            break;
        case 'unit-types':
            $variables = array();
            $page = array(
                '#markup' => theme('unit-types', $variables),
            );
            break;
        case 'unit-groups':
            $variables = array();
            $page = array(
                '#markup' => theme('unit-groups', $variables),
            );
            break;
        case 'notifications':
            $variables = array();
            $page = array(
                '#markup' => theme('notifications', $variables),
            );
            break;
        case 'starter-license':
            if (!empty($_GET['license'])) {
                # check whether license is correct, if it is correct execute below
                $file = 'rental.txt';
                file_put_contents($file, $_GET['license']);
                drupal_goto('dashboard', array());
                # if it is not correct, return to license request page
            } else {
                $variables = array();
                $page = array(
                    '#markup' => theme('support', $variables),
                );
            }
            break;
        default:
            $page = array(
                '#markup' => "<div>Hello, world</div>",
            );
            break;
    }

    return $page;
}
