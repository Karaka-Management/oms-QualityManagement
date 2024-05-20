<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\QualityManagement\Controller\BackendController;
use Modules\QualityManagement\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/qualitymanagement/report/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewReportDashboard',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::REPORT_DASHBOARD,
            ],
        ],
    ],
    '^/qualitymanagement/report/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewQualityReport',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::QUALITY_REPORT,
            ],
        ],
    ],
    '^/qualitymanagement/report/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewQualityReportCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::QUALITY_REPORT,
            ],
        ],
    ],
    '^/qualitymanagement/audit/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewAuditList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::AUDIT_REPORT,
            ],
        ],
    ],
    '^/qualitymanagement/audit/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\QualityManagement\Controller\BackendController:viewAudit',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::QUALITY_REPORT,
            ],
        ],
    ],
];
