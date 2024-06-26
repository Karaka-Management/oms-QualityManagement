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

namespace Modules\QualityManagement\Views;

use Modules\Media\Models\Media;
use Modules\Media\Models\NullMedia;
use Modules\Profile\Models\ProfileMapper;
use Modules\Tasks\Models\TaskStatus;
use phpOMS\Localization\L11nManager;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Uri\UriFactory;
use phpOMS\Views\View;

/**
 * Task view class.
 *
 * @package Modules\QualityManagement
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class ReportView extends View
{
    /**
     * User profile image.
     *
     * @var Media
     * @since 1.0.0
     */
    public Media $defaultProfileImage;

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct(?L11nManager $l11n = null, ?RequestAbstract $request = null, ?ResponseAbstract $response = null)
    {
        $this->defaultProfileImage = new NullMedia();
        parent::__construct($l11n, $request, $response);
    }

    /**
     * Get the profile image
     *
     * If the profile doesn't have an image a random default image is used
     *
     * @param int $account Account
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getAccountImage(int $account) : string
    {
        /** @var \Modules\Profile\Models\Profile $profile */
        $profile = ProfileMapper::get()->with('image')->where('account', $account)->execute();

        if ($profile->id === 0 || $profile->image->getPath() === '') {
            return UriFactory::build($this->defaultProfileImage->getPath());
        }

        return UriFactory::build($profile->image->getPath());
    }

    /**
     * Get task status color.
     *
     * @param int $status Status
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getStatus(int $status) : string
    {
        if ($status === TaskStatus::OPEN) {
            return 'darkblue';
        } elseif ($status === TaskStatus::DONE) {
            return 'green';
        } elseif ($status === TaskStatus::WORKING) {
            return 'purple';
        } elseif ($status === TaskStatus::CANCELED) {
            return 'red';
        } elseif ($status === TaskStatus::SUSPENDED) {
            return 'yellow';
        }

        return 'black';
    }
}
