<?php

namespace App\Controllers;

use League\Uri\Components\Query;
use League\Uri\Uri;
use League\Uri\UriModifier;
use Sober\Controller\Controller;

use function \CoopLibraryFramework\Internationalization\get_language_list;

class App extends Controller
{
    public function siteName()
    {
        return get_bloginfo('name');
    }

    public function queriedResourceTerms()
    {
        $terms = [
            'language' => [],
            'lc_format' => [],
            'lc_goal' => [],
            'lc_region' => [],
            'lc_topic' => [],
        ];
        global $wp_query;
        if ($wp_query->tax_query) {
            foreach ($wp_query->tax_query->queries as $value) {
                foreach ($value['terms'] as $t) {
                    $terms[$value['taxonomy']][$t] = get_term_by('slug', $t, $value['taxonomy']);
                }
            }
        }
        return $terms;
    }

    public function availableLanguages()
    {
        if (function_exists('pll_the_languages') && function_exists('pll_current_language')) {
            $polylang_languages = pll_the_languages(['raw' => 1, 'hide_if_empty' => 0]);
            $available_languages = [];

            foreach ($polylang_languages as $lang) {
                $available_languages[$lang['slug']] = $lang['name'];
            }

            return $available_languages;
        }

        return ['en' => 'English'];
    }

    public function languages()
    {
        if (function_exists('pll_current_language')) {
            return get_language_list(pll_current_language('locale'));
        }
        return get_language_list('en_US');
    }

    public function currentLanguageName()
    {
        if (function_exists('pll_current_language')) {
            return pll_current_language('name');
        }
        return 'English';
    }

    public function currentLanguageLocale()
    {
        if (function_exists('pll_current_language')) {
            return pll_current_language('locale');
        }
        return 'en_US';
    }

    public function currentLanguage()
    {
        if (function_exists('pll_current_language')) {
            return pll_current_language();
        }
        return 'en';
    }

    public function foundPosts()
    {
        global $wp_query;
        return $wp_query->found_posts;
    }

    public function maxPages()
    {
        global $wp_query;
        return $wp_query->max_num_pages;
    }

    public function currentPage()
    {
        global $wp_query;
        $current_page = $wp_query->query_vars['paged'] > 1 ? $wp_query->query_vars['paged'] : 1;
        return $current_page;
    }

    public static function totalPosts($post_type = null)
    {
        if ($post_type) {
            return wp_count_posts($post_type)->publish;
        }

        return 0;
    }

    public static function sortUrl($order_by)
    {
        $uri = Uri::createFromString($_SERVER['REQUEST_URI']);
        return UriModifier::mergeQuery($uri, "order_by=$order_by");
    }

    public static function title()
    {
        if (is_front_page()) {
            return __(
                '<span class="pc-ff--sans pc-fw--normal">Platform Co-op</span><br />Resource Library',
                'coop-library'
            );
        }
        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return get_the_title($home);
            }
            return __('Latest Posts', 'coop-library');
        }

        if (is_search()) {
            return sprintf(__('Search results', 'coop-library'), get_search_query());
        }

        if (is_post_type_archive('lc_resource') || is_tax()) {
            return __('Browse all', 'coop-library');
        }

        if (is_404()) {
            return __('Not Found', 'coop-library');
        }

        return get_the_title();
    }

    public static function breadcrumb()
    {
        if (is_front_page()) {
            return false;
        }
        return sprintf('<a href="%1$s">%2$s</a>', get_home_url(), __('Home', 'coop-library'));
    }

    public static function termListUrl($taxonomy)
    {
        $slug = str_replace('lc_', '', $taxonomy) . 's';
        $page = get_page_by_path($slug);
        return get_permalink($page->ID);
    }
}
