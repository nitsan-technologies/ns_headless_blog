<?php
declare(strict_types = 1);

namespace NITSAN\NsHeadlessBlog\Controller;

use NITSAN\NsHeadlessBlog\Pagination\BlogPager;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use T3G\AgencyPack\Blog\Controller\PostController;
use Psr\Http\Message\ResponseInterface;
use T3G\AgencyPack\Blog\Utility\ArchiveUtility;
use T3G\AgencyPack\Blog\Service\MetaTagService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use T3G\AgencyPack\Blog\Domain\Model\Author;
use T3G\AgencyPack\Blog\Domain\Model\Category;
use T3G\AgencyPack\Blog\Domain\Model\Tag;

class BlogController extends PostController
{

    protected $contentObj;
    /**
     * Show a list of recent posts.
     */
    public function listRecentPostsAction(int $currentPage = 1): ResponseInterface
    {
        $maximumItems = (int) ($this->settings['lists']['posts']['maximumDisplayedItems'] ?? 0);
        $posts = (0 === $maximumItems)
            ? $this->postRepository->findAll()
            : $this->postRepository->findAllWithLimit($maximumItems);
        $pagination = $this->getPaginationItems($posts, $currentPage);

        $this->view->assign('type', 'recent');
        $this->view->assign('posts', $posts);
        $this->view->assign('pagination', $pagination);
        return $this->htmlResponse();
    }

    /**
     * Show a list of posts for a selected category.
     */
    public function listByDemandAction(): ResponseInterface
    {
        $repositoryDemand = $this->postRepositoryDemandFactory->createFromSettings($this->settings['demand'] ?? []);

        $this->view->assign('type', 'demand');
        $this->view->assign('demand', $repositoryDemand);
        $this->view->assign('posts', $this->postRepository->findByRepositoryDemand($repositoryDemand));
        $this->view->assign('pagination', []);
        return $this->htmlResponse();
    }

    /**
     * Show a number of latest posts.
     */
    public function listLatestPostsAction(): ResponseInterface
    {
        $maximumItems = (int) ($this->settings['latestPosts']['limit'] ?? 3);
        $posts = $this->postRepository->findAllWithLimit($maximumItems);

        $this->view->assign('type', 'latest');
        $this->view->assign('posts', $posts);
        return $this->htmlResponse();
    }

    public function listPostsByDateAction(?int $year = null, ?int $month = null, int $currentPage = 1): ResponseInterface
    {
        if ($year === null) {
            $posts = $this->postRepository->findMonthsAndYearsWithPosts();
            $this->view->assign('archiveData', ArchiveUtility::extractDataFromPosts($posts));
        } else {
            $dateTime = new \DateTimeImmutable(sprintf('%d-%d-1', $year, $month ?? 1));
            $posts = $this->postRepository->findByMonthAndYear($year, $month);
            $pagination = $this->getPaginationItems($posts, $currentPage);
            $this->view->assign('type', 'bydate');
            $this->view->assign('month', $month);
            $this->view->assign('year', $year);
            $this->view->assign('timestamp', $dateTime->getTimestamp());
            $this->view->assign('posts', $posts);
            $this->view->assign('pagination', $pagination);
            $title = str_replace([
                '###MONTH###',
                '###MONTH_NAME###',
                '###YEAR###',
            ], [
                $month,
                $dateTime->format('F'),
                $year,
            ], (string) LocalizationUtility::translate('meta.title.listPostsByDate', 'blog'));
            
            MetaTagService::set(MetaTagService::META_TITLE, (string) $title);
            MetaTagService::set(MetaTagService::META_DESCRIPTION, (string) LocalizationUtility::translate('meta.description.listPostsByDate', 'blog'));
        }
        return $this->htmlResponse();
    }

    /**
     * Show a list of posts by given category.
     */
    public function listPostsByCategoryAction(?Category $category = null, int $currentPage = 1): ResponseInterface
    {
        $contentObj = $this->request->getAttribute('currentContentObject');
        if ($category === null) {
           
            $referenceUid = $contentObj !== null ? (int) $contentObj->data['uid'] : null;
            if ($referenceUid !== null) {
                $categories = $this->categoryRepository->getByReference('tt_content', $referenceUid);
                if ($categories !== null && $categories->count() > 0) {
                    /** @var ?Category $category */
                    $category = $categories->getFirst();
                }
            }
        }

        if ($category !== null) {
            $posts = $this->postRepository->findAllByCategory($category);
            $pagination = $this->getPaginationItems($posts, $currentPage);
            $this->view->assign('type', 'bycategory');
            $this->view->assign('posts', $posts);
            $this->view->assign('pagination', $pagination);
            $this->view->assign('category', $category);
            MetaTagService::set(MetaTagService::META_TITLE, (string) $category->getTitle());
            MetaTagService::set(MetaTagService::META_DESCRIPTION, (string) $category->getDescription());
        } else {
            $this->view->assign('categories', $this->categoryRepository->findAll());
        }
        return $this->htmlResponse();
    }

    /**
     * Show a list of posts by given author.
     */
    public function listPostsByAuthorAction(?Author $author = null, int $currentPage = 1): ResponseInterface
    {
        if ($author !== null) {
            $posts = $this->postRepository->findAllByAuthor($author);
            $pagination = $this->getPaginationItems($posts, $currentPage);
            $this->view->assign('type', 'byauthor');
            $this->view->assign('posts', $posts);
            $this->view->assign('pagination', $pagination);
            $this->view->assign('author', $author);
            MetaTagService::set(MetaTagService::META_TITLE, (string) $author->getName());
            MetaTagService::set(MetaTagService::META_DESCRIPTION, (string) $author->getBio());
        } else {
            $this->view->assign('authors', $this->authorRepository->findAll());
        }
        return $this->htmlResponse();
    }

    /**
     * Show a list of posts by given tag.
     */
    public function listPostsByTagAction(?Tag $tag = null, int $currentPage = 1): ResponseInterface
    {
        if ($tag !== null) {
            $posts = $this->postRepository->findAllByTag($tag);
            $pagination = $this->getPaginationItems($posts, $currentPage);
            $this->view->assign('type', 'bytag');
            $this->view->assign('posts', $posts);
            $this->view->assign('pagination', $pagination);
            $this->view->assign('tag', $tag);
            MetaTagService::set(MetaTagService::META_TITLE, (string) $tag->getTitle());
            MetaTagService::set(MetaTagService::META_DESCRIPTION, (string) $tag->getDescription());
        } else {
            $this->view->assign('tags', $this->tagRepository->findAll());
        }
        return $this->htmlResponse();
    }

    protected function getPaginationItems(QueryResultInterface $objects, int $currentPage = 1): ?BlogPager
    {
        $maximumNumberOfLinks = (int) ($this->settings['lists']['pagination']['maximumNumberOfLinks'] ?? 0);
        $itemsPerPage = (int) ($this->settings['lists']['pagination']['headlessLimit'] ?? 10);
        $paginator = new QueryResultPaginator($objects, $currentPage, $itemsPerPage);
        return new BlogPager($paginator, $maximumNumberOfLinks, $currentPage);
    }
}
