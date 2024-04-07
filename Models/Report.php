<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\QualityManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\QualityManagement\Models;

use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskType;

/**
 * Report class.
 *
 * @package Modules\QualityManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Report
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * The ticket is using a task.
     *
     * @var Task
     * @since 1.0.0
     */
    public Task $task;

    /**
     * Constructor.
     *
     * @param null|Task $task Creates the ticket from a task
     *
     * @since 1.0.0
     */
    public function __construct(?Task $task = null)
    {
        $this->task       = $task ?? new Task();
        $this->task->type = TaskType::HIDDEN;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'   => $this->id,
            'task' => $this->task,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
