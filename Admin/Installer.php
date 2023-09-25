<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\QualityManagement\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\QualityManagement\Admin;

use phpOMS\Module\InstallerAbstract;

/**
 * Installer class.
 *
 * @package Modules\QualityManagement\Admin
 * @license OMS License 2.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;
}
