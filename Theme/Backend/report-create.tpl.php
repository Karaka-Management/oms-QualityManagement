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

/**
 * @var \phpOMS\Views\View $this
 */
echo $this->data['nav']->render(); ?>
<div class="row">
    <div class="col-xs-6">
        <section class="portlet">
            <form action="<?= \phpOMS\Uri\UriFactory::build('{/api}qualitymanagement/report?csrf={$CSRF}'); ?>" method="post">
                <div class="portlet-head"><?= $this->getHtml('Report'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iTitle"><?= $this->getHtml('Title'); ?></label>
                        <input id="iTitle" name="name" type="text" required>
                    </div>

                    <div class="form-group">
                        <label for="iDescription"><?= $this->getHtml('Description'); ?></label>
                        <textarea required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="iBill"><?= $this->getHtml('Bill'); ?></label>
                        <input id="iBill" type="text" name="bill">
                    </div>

                    <div class="form-group">
                        <label for="iItem"><?= $this->getHtml('Item'); ?></label>
                        <input id="iItem" type="text" name="item">
                    </div>

                    <div class="form-group">
                        <label for="iLotSN"><?= $this->getHtml('LotSN'); ?></label>
                        <input id="iLotSN" type="text" name="lot">
                    </div>

                    <div class="form-group">
                        <label for="iAccount"><?= $this->getHtml('Account'); ?></label>
                        <input id="iAccount" type="text" name="account">
                    </div>

                    <div class="form-group">
                        <label for="iFile"><?= $this->getHtml('Files'); ?></label>
                        <input id="iFile" name="fileVisual" type="file" multiple><input id="iFileHidden" name="files" type="hidden">
                    </div>
                </div>
                <div class="portlet-foot">
                    <input type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>" name="create-report">
                </div>
            </form>
        </section>
    </div>
</div>