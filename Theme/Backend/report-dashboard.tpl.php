<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Support
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View               $this
 * @var \Modules\Support\Models\Ticket[] $reports
 */
$reports = $this->data['reports'];

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Open'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table class="default sticky">
                <thead>
                    <td><?= $this->getHtml('Status'); ?>
                    <td class="full"><?= $this->getHtml('Title'); ?>
                    <td><?= $this->getHtml('Creator'); ?>
                    <td><?= $this->getHtml('Assigned'); ?>
                    <td><?= $this->getHtml('For'); ?>
                    <td><?= $this->getHtml('Created'); ?>
                <tbody>
                <?php
                    $c = 0;
                foreach ($this->data['open'] as $key => $report) : ++$c;
                    $url = UriFactory::build('{/base}/qualitymanagement/report/view?{?}&id=' . $report->id);
                ?>
                    <tr data-href="<?= $url; ?>">
                        <td><a href="<?= $url; ?>">
                            <span class="tag <?= $this->printHtml('task-status-' . $report->task->status); ?>">
                                <?= $this->getHtml('S' . $report->task->status, 'Tasks'); ?>
                            </span></a>
                        <td><a href="<?= $url; ?>"><?= $this->printHtml($report->task->title); ?></a>
                        <td><a class="content" href="<?= UriFactory::build('{/base}/profile/view?for=' . $report->task->createdBy->id); ?>"><?= $this->printHtml($report->task->createdBy->name1); ?> <?= $this->printHtml($report->task->createdBy->name2); ?></a>
                        <td><?php $responsibles = $report->task->getResponsible();
                            foreach ($responsibles as $responsible) : ?>
                            <a class="content" href="<?= UriFactory::build('{/base}/profile/view?for=' . $responsible->id); ?>">
                                <?= $this->printHtml($responsible->name1); ?> <?= $this->printHtml($responsible->name2); ?>
                            </a>
                            <?php endforeach; ?>
                        <td><a class="content"><?= $this->printHtml($report->task->for->name1); ?> <?= $this->printHtml($report->task->for->name2); ?>
                        <td><a href="<?= $url; ?>"><?= $this->printHtml($report->task->createdAt->format('Y-m-d H:i')); ?></a>
                <?php endforeach; if ($c == 0) : ?>
                    <tr><td colspan="7" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
        </section>

        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Reports'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table class="default sticky">
                <thead>
                    <td><?= $this->getHtml('Status'); ?>
                    <td class="full"><?= $this->getHtml('Title'); ?>
                    <td><?= $this->getHtml('Creator'); ?>
                    <td><?= $this->getHtml('Assigned'); ?>
                    <td><?= $this->getHtml('For'); ?>
                    <td><?= $this->getHtml('Created'); ?>
                <tbody>
                <?php
                    $c = 0;
                foreach ($this->data['reports'] as $key => $report) : ++$c;
                    $url = UriFactory::build('{/base}/qualitymanagement/report/view?{?}&id=' . $report->id);
                ?>
                    <tr data-href="<?= $url; ?>">
                        <td><a href="<?= $url; ?>">
                            <span class="tag <?= $this->printHtml('task-status-' . $report->task->status); ?>">
                                <?= $this->getHtml('S' . $report->task->status, 'Tasks'); ?>
                            </span></a>
                        <td><a href="<?= $url; ?>"><?= $this->printHtml($report->task->title); ?></a>
                        <td><a class="content" href="<?= UriFactory::build('{/base}/profile/view?for=' . $report->task->createdBy->id); ?>"><?= $this->printHtml($report->task->createdBy->name1); ?> <?= $this->printHtml($report->task->createdBy->name2); ?></a>
                        <td><?php $responsibles = $report->task->getResponsible();
                            foreach ($responsibles as $responsible) : ?>
                            <a class="content" href="<?= UriFactory::build('{/base}/profile/view?for=' . $responsible->id); ?>">
                                <?= $this->printHtml($responsible->name1); ?> <?= $this->printHtml($responsible->name2); ?>
                            </a>
                            <?php endforeach; ?>
                        <td><a class="content"><?= $this->printHtml($report->task->for->name1); ?> <?= $this->printHtml($report->task->for->name2); ?>
                        <td><a href="<?= $url; ?>"><?= $this->printHtml($report->task->createdAt->format('Y-m-d H:i')); ?></a>
                <?php endforeach; if ($c == 0) : ?>
                    <tr><td colspan="7" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>