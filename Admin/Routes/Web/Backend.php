<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\QualityManagement\Controller\BackendController;
use Modules\QualityManagement\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/qualitymanagement/report/list.*$' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewReportDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::REPORT_DASHBOARD,
            ],
        ],
    ],
    '^.*/qualitymanagement/report/view.*$' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewQualityReport',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::QUALITY_REPORT,
            ],
        ],
    ],
    '^.*/qualitymanagement/audit/list.*$' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewAuditList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::AUDIT_REPORT,
            ],
        ],
    ],
    '^.*/qualitymanagement/audit/view.*$' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewQuality',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::QUALITY_REPORT,
            ],
        ],
    ],

    '^.*/private/qualitymanagement/dashboard.*$' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewPrivateReportDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PRIVATE_DASHBOARD,
            ],
        ],
    ],
    '^.*/private/qualitymanagement/report.*$' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewPrivateReport',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PRIVATE_DASHBOARD,
            ],
        ],
    ],
];
