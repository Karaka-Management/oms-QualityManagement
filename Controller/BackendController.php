<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\QualityManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\QualityManagement\Controller;

use Modules\Media\Models\MediaMapper;
use Modules\QualityManagement\Models\ReportMapper;
use Modules\QualityManagement\Views\ReportView;
use Modules\Tasks\Models\AccountRelationMapper;
use Modules\Tasks\Models\TaskElementMapper;
use Modules\Tasks\Models\TaskMapper;
use Modules\Tasks\Models\TaskStatus;
use Modules\Tasks\Models\TaskType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;
use Modules\Profile\Models\SettingsEnum as ProfileSettingsEnum;
use phpOMS\Message\Http\RequestStatusCode;

/**
 * QualityManagement controller class.
 *
 * @package Modules\QualityManagement
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewReportDashboard(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/QualityManagement/Theme/Backend/report-dashboard');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1008502001, $request, $response);

        $view->data['reports'] = ReportMapper::getAnyRelatedToUser($request->header->account)
            ->with('task')
            ->with('task/createdBy')
            ->with('task/for')
            ->with('task/taskElements')
            ->with('task/taskElements/accRelation')
            ->with('task/taskElements/accRelation/relation')
            ->with('app')
            ->sort('task/createdAt', OrderType::DESC)
            ->limit(50)
            ->paginate(
                'id',
                $request->getData('ptype'),
                $request->getDataInt('offset')
            )
            ->executeGetArray();

        $openQuery = new Builder($this->app->dbPool->get(), true);
        $openQuery->innerJoin(TaskMapper::TABLE, TaskMapper::TABLE . '_d2_task')
            ->on(ReportMapper::TABLE . '_d1.qualitymgmt_report_task', '=', TaskMapper::TABLE . '_d2_task.task_id')
            ->innerJoin(TaskElementMapper::TABLE)
                ->on(TaskMapper::TABLE . '_d2_task.' . TaskMapper::PRIMARYFIELD, '=', TaskElementMapper::TABLE . '.task_element_task')
            ->innerJoin(AccountRelationMapper::TABLE)
                ->on(TaskElementMapper::TABLE . '.' . TaskElementMapper::PRIMARYFIELD, '=', AccountRelationMapper::TABLE . '.task_account_task_element')
            ->andWhere(AccountRelationMapper::TABLE . '.task_account_account', '=', $request->header->account);

        $view->data['open'] = ReportMapper::getAll()
            ->with('task')
            ->with('task/createdBy')
            ->where('task/type', TaskType::TEMPLATE, '!=')
            ->where('task/status', TaskStatus::OPEN)
            ->sort('task/createdAt', OrderType::DESC)
            ->query($openQuery)
            ->executeGetArray();

        $view->data['stats'] = ReportMapper::getStatOverview();

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewQualityReportCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/QualityManagement/Theme/Backend/report-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1008502001, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewQualityReport(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new ReportView($this->app->l11nManager, $request, $response);

        $view->data['report'] = ReportMapper::get()
            ->with('task')
            ->with('task/createdBy')
            ->with('task/tags')
            ->with('task/tags/title')
            ->with('task/taskElements')
            ->with('task/taskElements/createdBy')
            ->with('task/taskElements/media')
            ->with('task/attributes')
            ->with('task/for')
            ->where('id', (int) $request->getData('id'))
            ->where('task/tags/title/language', $request->header->l11n->language)
            ->execute();

        if ($view->data['report']->id === 0) {
            $response->header->status = RequestStatusCode::R_404;
            $view->setTemplate('/Web/Backend/Error/404');

            return $view;
        }

        $view->setTemplate('/Modules/QualityManagement/Theme/Backend/report-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1008502001, $request, $response);

        /** @var \Model\Setting $profileImage */
        $profileImage = $this->app->appSettings->get(names: ProfileSettingsEnum::DEFAULT_PROFILE_IMAGE, module: 'Profile');

        /** @var \Modules\Media\Models\Media $image */
        $image                     = MediaMapper::get()->where('id', (int) $profileImage->content)->execute();
        $view->defaultProfileImage = $image;

        $accGrpSelector               = new \Modules\Profile\Theme\Backend\Components\AccountGroupSelector\BaseView($this->app->l11nManager, $request, $response);
        $view->data['accGrpSelector'] = $accGrpSelector;

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewAuditList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/QualityManagement/Theme/Backend/audit-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1008502001, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewAudit(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/QualityManagement/Theme/Backend/audit-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1008502001, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewAuditCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/QualityManagement/Theme/Backend/audit-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1008502001, $request, $response);

        return $view;
    }
}
