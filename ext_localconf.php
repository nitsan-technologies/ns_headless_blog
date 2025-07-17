<?php
declare(strict_types=1);

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][T3G\AgencyPack\Blog\Controller\PostController::class] = [
    'className' => NITSAN\NsHeadlessBlog\Controller\BlogController::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][T3G\AgencyPack\Blog\Pagination\BlogPagination::class] = [
    'className' => NITSAN\NsHeadlessBlog\Pagination\BlogPager::class
];