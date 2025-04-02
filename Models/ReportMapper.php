<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\QualityManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\QualityManagement\Models;

use Modules\Tasks\Models\AccountRelationMapper;
use Modules\Tasks\Models\TaskElementMapper;
use Modules\Tasks\Models\TaskMapper;
use Modules\Tasks\Models\TaskStatus;
use Modules\Tasks\Models\TaskType;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Database\Mapper\ReadMapper;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Stdlib\Base\SmartDateTime;

/**
 * Report mapper class.
 *
 * @package Modules\QualityManagement\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Report
 * @extends DataMapperFactory<T>
 */
final class ReportMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'qualitymgmt_report_id'   => ['name' => 'qualitymgmt_report_id',   'type' => 'int', 'internal' => 'id'],
        'qualitymgmt_report_task' => ['name' => 'qualitymgmt_report_task', 'type' => 'int', 'internal' => 'task'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'task' => [
            'mapper'   => TaskMapper::class,
            'external' => 'qualitymgmt_report_task',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'qualitymgmt_report';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'qualitymgmt_report_id';

    /**
     * Get general ticket stats
     *
     * @return array{total:int, unassigned:int, open:int, closed:int, inprogress:int}
     *
     * @since 1.0.0
     */
    public static function getStatOverview() : array
    {
        $start = SmartDateTime::startOfMonth();

        return [
            'total'      => self::count()->with('task')->where('task/createdAt', $start, '>=')->executeCount(),
            'unassigned' => self::count()->with('task')->where('for', null)->executeCount(),
            'open'       => self::count()->with('task')->where('task/status', TaskStatus::OPEN)->executeCount(),
            'closed'     => self::count()->with('task')->where('task/createdAt', $start, '>=')->where('task/status', TaskStatus::DONE)->where('task/status', TaskStatus::CANCELED, '=', 'OR')->where('task/status', TaskStatus::SUSPENDED, '=', 'OR')->executeCount(),
            'inprogress' => self::count()->with('task')->where('task/status', TaskStatus::WORKING)->executeCount(),
        ];
    }

    /**
     * Get tasks that have something to do with the user
     *
     * @param int $user User
     *
     * @return ReadMapper
     *
     * @since 1.0.0
     */
    public static function getAnyRelatedToUser(int $user) : ReadMapper
    {
        $query = new Builder(self::$db, true);
        $query->innerJoin(TaskMapper::TABLE, TaskMapper::TABLE . '_d2_task')
            ->on(self::TABLE . '_d1.qualitymgmt_report_task', '=', TaskMapper::TABLE . '_d2_task.task_id')
            ->innerJoin(TaskElementMapper::TABLE)
                ->on(TaskMapper::TABLE . '_d2_task.task_id', '=', TaskElementMapper::TABLE . '.task_element_task')
                ->on(TaskMapper::TABLE . '_d2_task.task_type', '!=', TaskType::TEMPLATE)
            ->innerJoin(AccountRelationMapper::TABLE)
                ->on(TaskElementMapper::TABLE . '.task_element_id', '=', AccountRelationMapper::TABLE . '.task_account_task_element')
            ->where(AccountRelationMapper::TABLE . '.task_account_account', '=', $user)
            ->orWhere(TaskMapper::TABLE . '_d2_task.task_created_by', '=', $user)
            ->groupBy(self::PRIMARYFIELD);

        // @todo Improving query performance by using raw queries and result arrays for large responses like this
        $sql = <<<SQL
        SELECT DISTINCT task.*, account.*
        FROM task
        INNER JOIN task_element ON task.task_id = task_element.task_element_task
        INNER JOIN task_account ON task_element.task_element_id = task_account.task_account_task_element
        INNER JOIN account ON task.task_created_by = account.account_id
        WHERE
            task.task_status != 1
            AND (
                task_account.task_account_account = {$user}
                OR task.task_created_by = {$user}
            )
        LIMIT 25;
        SQL;

        return self::getAll()->query($query);
    }
}
