<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;
use SousMeow\Models\Category;
use SousMeow\Models\Collection;
use SousMeow\Models\Cookbook;
use SousMeow\Services\CollectionResolver;

/**
 * One shared index and one shared category-detail route serve every
 * category. There are no per-category controllers or templates.
 */
final class CategoryController
{
    public function index(): void
    {
        // Every surfaced Collection resolved once; start-here is pulled out
        // for its own section, the rest render as strips.
        $surfaced = CollectionResolver::surfaced(Collection::allVisible());
        $startHere = null;
        $strips = [];
        foreach ($surfaced as $entry) {
            if ($entry['collection']['slug'] === 'start-here') {
                $startHere = $entry;
            } else {
                $strips[] = $entry;
            }
        }

        View::render('categories/index', [
            'title'      => 'Browse by topic',
            'pageCss'    => ['marketplace', 'categories'],
            'categories' => Category::allVisibleWithCounts(),
            'startHere'  => $startHere,
            'strips'     => $strips,
        ]);
    }

    public function show(string $slug): void
    {
        $category = Category::findBySlug($slug);
        if ($category === null || (int) $category['is_visible'] !== 1) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Category not found']);
            return;
        }

        $level = trim((string) ($_GET['level'] ?? ''));
        $show = trim((string) ($_GET['show'] ?? ''));

        $categoryId = (int) $category['id'];
        $all = Cookbook::inCategory($categoryId);
        $cookbooks = Cookbook::inCategory(
            $categoryId,
            $level !== '' ? $level : null,
            $show !== '' ? $show : null
        );

        // Difficulty levels actually present in this category, in a stable
        // order, so the filter only offers real choices.
        $order = ['Beginner' => 0, 'Intermediate' => 1, 'Advanced' => 2];
        $levels = array_values(array_unique(array_map(
            static fn(array $c): string => (string) ($c['difficulty'] ?? ''),
            $all
        )));
        usort($levels, static fn(string $a, string $b): int => ($order[$a] ?? 9) <=> ($order[$b] ?? 9));

        $hasExecutable = array_filter($all, static fn(array $c): bool => (int) $c['is_executable'] === 1) !== [];
        $hasPreview = array_filter($all, static fn(array $c): bool => (int) $c['is_executable'] === 0) !== [];

        // Surfaced Collections that feature at least one Cookbook from this
        // category (computed from the unfiltered set so filters do not hide
        // the relationship).
        $categoryIds = array_map(static fn(array $c): int => (int) $c['id'], $all);
        $featuredIn = [];
        foreach (CollectionResolver::surfaced(Collection::allVisible()) as $entry) {
            $entryIds = array_map(static fn(array $c): int => (int) $c['id'], $entry['cookbooks']);
            if (array_intersect($entryIds, $categoryIds) !== []) {
                $featuredIn[] = $entry['collection'];
            }
        }

        View::render('categories/show', [
            'title'         => (string) $category['name'],
            'pageCss'       => ['marketplace', 'categories'],
            'category'      => $category,
            'cookbooks'     => $cookbooks,
            'totalInCategory' => count($all),
            'levels'        => $levels,
            'hasExecutable' => $hasExecutable,
            'hasPreview'    => $hasPreview,
            'featuredIn'    => $featuredIn,
            'filterLevel'   => $level,
            'filterShow'    => $show,
        ]);
    }
}
