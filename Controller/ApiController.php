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

use Modules\Notification\Models\NotificationType;
use Modules\QualityManagement\Models\Report;
use Modules\QualityManagement\Models\ReportMapper;
use Modules\Tasks\Models\TaskElementMapper;
use Modules\Tasks\Models\TaskMapper;
use Modules\Tasks\Models\TaskStatus;
use Modules\Tasks\Models\TaskType;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;

/**
 * Api controller for the QualityManagement module.
 *
 * @package Modules\QualityManagement
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Validate report create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool> Returns the validation array of the request
     *
     * @since 1.0.0
     */
    private function validateReportCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['plain'] = !$request->hasData('plain'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create a report
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiReportCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateReportCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $report = $this->createReportFromRequest($request);
        $this->createModel($request->header->account, $report, ReportMapper::class, 'report', $request->getOrigin());

        $first = \reset($report->task->taskElements);
        if ($first !== false) {
            $this->app->moduleManager->get('Tasks', 'Api')->createNotifications($first, NotificationType::CREATE, $request);
        }

        $this->createStandardCreateResponse($request, $response, $report);
    }

    /**
     * Method to create report from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Report Returns the created report from the request
     *
     * @since 1.0.0
     */
    private function createReportFromRequest(RequestAbstract $request) : Report
    {
        $request->setData('redirect', 'qualitymanagement/report/view?for={$id}');
        $task       = $this->app->moduleManager->get('Tasks', 'Api')->createTaskFromRequest($request);
        $task->type = TaskType::HIDDEN;
        $task->unit ??= $this->app->unitId;

        return new Report($task);
    }

    /**
     * Api method to get a report
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiReportGet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var \Modules\QualityManagement\Models\Report $report */
        $report = ReportMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->createStandardReturnResponse($request, $response, $report);
    }

    /**
     * Api method to update a report
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiReportSet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var \Modules\QualityManagement\Models\Report $old */
        $old = ReportMapper::get()
            ->with('task')
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $new = $this->updateReportFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, ReportMapper::class, 'report', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update an report from a request
     *
     * @param RequestAbstract $request Request
     *
     * @return Report Returns the updated report from the request
     *
     * @since 1.0.0
     */
    private function updateReportFromRequest(RequestAbstract $request, Report $new) : Report
    {
        return $new;
    }

    /**
     * Validate report element create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool> Returns the validation array of the request
     *
     * @since 1.0.0
     */
    private function validateReportElementCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['status'] = !TaskStatus::isValidValue((int) $request->getData('status')))
            || ($val['due'] = !((bool) \strtotime((string) $request->getData('due'))))
            || ($val['report'] = !(\is_numeric($request->getData('report'))))
            || ($val['forward'] = !(\is_numeric($request->hasData('forward') ? $request->getData('forward') : $request->header->account)))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create a report element
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiReportElementCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateReportElementCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        // @question Consider to use the apiTaskElementCreate() function

        /** @var \Modules\QualityManagement\Models\Report $report */
        $report = ReportMapper::get()
            ->with('task')
            ->where('id', (int) ($request->getData('report')))
            ->execute();

        $element = $this->app->moduleManager->get('Tasks')->createTaskElementFromRequest($request, $report->task);

        $old = clone $report->task;

        $report->task->status   = $element->status;
        $report->task->priority = $element->priority;
        $report->task->due      = $element->due;

        $this->createModel($request->header->account, $element, TaskElementMapper::class, 'report_element', $request->getOrigin());
        $this->updateModel($request->header->account, $old, $report->task, TaskMapper::class, 'report', $request->getOrigin());

        $report->task->taskElements[] = $element;

        $this->createStandardCreateResponse($request, $response, $element);
    }

    /**
     * Api method to update a report
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiReportElementSet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $this->app->moduleManager->get('Tasks')->apiTaskElementSet($request, $response);
        $new = $response->getData($request->uri->__toString())['response'];

        //$this->updateModel($request->header->account, $report, $report, ReportMapper::class, 'report', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }
}
