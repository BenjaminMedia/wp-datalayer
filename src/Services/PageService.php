<?php

namespace BonnierDataLayer\Services;

class PageService
{
    public function contentType()
    {
        if (is_category()) {
            return 'category';
        }

        if (is_tag()) {
            return 'tag';
        }

        if (is_front_page()) {
            return 'frontpage';
        }

        if (is_page()) {
            return 'panel';
        }

        if (is_singular() && is_single()) {
            global $post;

            if ($post->post_type === 'review') {
                return 'review';
            }

            if (function_exists('get_field') && $post->post_type === 'contenthub_composite') {
                return mb_strtolower(get_field('kind', $post->ID));
            }

            return 'article';
        }

        return null;
    }

    public function contentAuthor()
    {
        $post = get_post();

        if (!isset($post) || is_tag() || is_category()) {
            return null;
        }

        return get_the_author_meta('display_name', $post->post_author);
    }

    public function pageId()
    {
        // frontpage with no ID
        if (is_front_page() && is_home()) {
            return null;
        }

        if (is_category()) {
            $catID = get_query_var('cat');

            if ($this->polylangActive()) {
                if ($danishCatID = pll_get_term($catID, pll_default_language())) {
                    return 'category-' . get_category($danishCatID)->cat_ID;
                }
            }

            return 'category-'. $catID;
        }

        if (is_tag() && ($tag = $this->getTag())) {
            return 'tag-'. $tag->term_id;
        }

        $post = get_post();
        if (isset($post)) {
            return 'post-' . $this->getDanishArticle()->ID;
        }

        return null;
    }

    public function pageName()
    {
        if (is_front_page() && is_home()) {
            return 'frontpage';
        }

        if (is_category()) {
            $catID = get_query_var('cat');

            if ($this->polylangActive()) {
                // Print danish name if the category is linked to one
                if ($danishCatID = pll_get_term($catID, pll_default_language())) {
                    return get_category($danishCatID)->cat_name;
                }
            }

            // Fall back to native language
            return get_category($catID)->cat_name;
        }

        if (is_tag() && ($tag = $this->getTag())) {
            return $tag->name;
        }

        if ($post = get_post()) {
            return $post->post_title;
        }

        return null;
    }

    public function contentPublication()
    {
        $post = get_post();

        if (!isset($post) || is_tag() || is_category()) {
            return null;
        }

        return get_the_date("Y-m-d");
    }

    public function contentLastModified()
    {
        $post = get_post();

        if (!isset($post) || is_tag() || is_category()) {
            return null;
        }

        $mod = get_the_modified_date("Y-m-d");
        if ($mod != $this->contentPublication()) {
            return $mod;
        }

        return null;
    }

    public function pageStatus()
    {
        // We somehow f**cked the is_404() on the old Willow.
        // Therefor please be aware, that we cannot use the function.
        // I'm going to write our own is_404 function...
        if (function_exists('pp_404_is_active')) {
            return !$this->is404() ? 'success' : 'error';
        }

        return is_404() ? 'error' : 'success';
    }

    private function is404()
    {
        global $smart404page;

        if (function_exists('pll_get_post_translations')) {
            $postTranslations = pll_get_post_translations(get_queried_object_id());

            if (in_array((int)$smart404page->settings['404page_page_id'], $postTranslations)) {
                return true;
            }

            return false;
        }

        return is_404();
    }

    public function pagePillar()
    {
        // If one of them are true, it's the homepage
        // https://developer.wordpress.org/reference/functions/is_home/
        if (is_front_page() || is_home()) {
            return 'frontpage';
        }

        // post & page
        if (is_singular() && is_single()) {
            // pages dose not have a category. Ship if it's a page
            if (is_object($this->category())) {
                if ($this->polylangActive()) {
                    if ($danishCatID = pll_get_term($this->rootCategory($this->category()->cat_ID)->cat_ID,
                        pll_default_language())) {
                        return mb_strtolower(get_category($danishCatID)->name);
                    }
                }

                return mb_strtolower($this->category()->name);
            }
        }

        if (is_category()) {
            $catID = get_query_var('cat');
            $rootCat = $this->rootCategory($catID);

            // Print danish name if the category is linked to one
            if ($this->polylangActive()) {
                if ($danishCatID = pll_get_term($rootCat->cat_ID, pll_default_language())) {
                    return mb_strtolower(get_category($danishCatID)->name);
                }
            }

            // Fall back to native language
            return mb_strtolower(get_category($catID)->cat_name);
        }

        if (is_tag()) {
            return mb_strtolower($this->getTag()->name);
        }

        return null;
    }

    public function pageSubPillar()
    {
        if (is_tag()) {
            return null;
        }

        // category archive page
        if ($catID = get_query_var('cat')) {
            $category = get_category($catID);

            if ($category->parent > 0) {

                if (!$this->polylangActive()) {
                    return mb_strtolower($category->name);
                }

                if ($danishCatID = pll_get_term($category->term_id, pll_default_language())) {
                    return mb_strtolower(get_category($danishCatID)->name);
                }
            }

            return null;
        }

        // contenthub, pages, and posts
        if ($post = get_post()) {
            $categoryArrayID = $post->post_category;

            // contenthub and posts
            if ($categoryID = array_shift($categoryArrayID)) {

                $category = get_category($categoryID);

                if ($category->parent > 0) {
                    return mb_strtolower($category->name);
                }

            }
            return null;
        }

        // Tags do not have any pillars
        return null;
    }

    public function contentCommercialType()
    {
        $post = get_post();

        // Tags and category can't have a commercial type
        if (is_tag() || is_category()) {
            return null;
        }

        // Make sure there's a post
        if (!isset($post)) {
            return null;
        }

        // Pages and frontpages should not have a contentCommercialType.
        // is_page is broken. Do not use!
        if ($post->post_type == 'page' || is_front_page()) {
            return null;
        }

        if ($this->polylangActive()) {
            if (($commercialType = get_field('commercial_type', $post->ID)) && (is_singular() || is_single())) {
                return mb_strtolower($commercialType);
            }
        }

        return 'editorial';
    }

    public function contentTextLength()
    {
        // Default behavior types. Review is for productsearch
        $defaultTypes = ['page', 'post', 'review'];

        // Do not count on categories and tags
        if (is_category() || is_tag()) {
            return null;
        }

        // If it's a frontpage with lates posts
        if (is_front_page() && is_home()) {
            return null;
        }

        if ($post = get_post()) {
            if ($post->post_type === 'contenthub_composite') {
                return $this->contenthubCompositeTextLength($post);
            }

            if (is_front_page()) {
                return null;
            }

            if (in_array($post->post_type, $defaultTypes)) {
                return $this->countWords($post->post_content);
            }
        }

        return null;
    }

    private function contenthubCompositeTextLength(\WP_Post $post)
    {
        $wordCount = 0;

        if (!function_exists('get_fields')) {
            return $wordCount;
        }

        $compositeFields = get_fields($post->ID);

        // Time to count!
        $wordCount = $wordCount + $this->countWords($post->post_title);

        $description = $compositeFields['description'] ?? null;

        if ($description && !empty($description)) {
            $wordCount = $wordCount + $this->countWords($description);
        }

        $contentWidgets = $compositeFields['composite_content'] ?? [];

        // Find description on teaser
        foreach ($contentWidgets as $compositeWidget) {
            if ($compositeWidget['acf_fc_layout'] === 'image') {
                $image = get_post($compositeWidget['file'])->post_excerpt;
                $wordCount = $wordCount + $this->countWords($image);
            }

            if ($compositeWidget['acf_fc_layout'] === 'text_item') {
                $wordCount = $wordCount + $this->countWords($compositeWidget['body']);
            }

            if ($compositeWidget['acf_fc_layout'] === 'gallery' && $compositeWidget['display_hint'] === 'inline') {
                $wordCount = $wordCount + $this->countWords($compositeWidget['title']);

                foreach ($compositeWidget['images'] as $image) {
                    $wordCount = $wordCount + $this->countWords($image['description']);
                }
            }
        }


        return $wordCount;
    }

    private function countWords($string) {
        $charList = 'âÂéÉèÈêÊøØóÓòÒôÔäÄåÅöÖæÆ:/.';
        return str_word_count($string, 0, $charList);
    }

    private function category()
    {
        // Pages do NOT have categories!
        if (is_page()) {
            return null;
        }

        $categoryId = get_post()->post_category;

        if (empty($categoryId)) {
            return null;
        }

        if (function_exists('pll_get_term')) {
            if ($danishCategory = pll_get_term($categoryId[0], pll_default_language())) {
                return get_category($danishCategory);
            }
        }

        // fallback if the category is not translated
        return get_category($categoryId[0]);
    }

    private function rootCategory($category_id)
    {
        $rootCategory = null;
        while ($category_id) {
            $cat = get_category($category_id);
            $category_id = $cat->category_parent;
            $rootCategory = $cat;
        }

        return $rootCategory;
    }

    private function getDanishArticle()
    {
        $post = get_post();

        if (!function_exists('pll_default_language')) {
            return $post;
        }

        $defaultLocale = pll_default_language();

        if ($defaultLocale !== pll_get_post_language($post->ID)) {
            $translations = pll_get_post_translations($post->ID);

            $defaultTranslationId = isset($translations[$defaultLocale]) ? $translations[$defaultLocale] : null;

            // Don't do anything if there's no translation on the default language
            if (!empty($defaultTranslationId) && $defaultTranslatedPost = get_post($defaultTranslationId)) {
                return $defaultTranslatedPost;
            }
        }

        return $post;
    }

    private function polylangActive()
    {
        return $this->isPluginActive('polylang-pro/polylang.php');
    }

    private function isPluginActive($plugin) {
        return in_array($plugin, (array) get_option('active_plugins', []));
    }

    private function getTag()
    {
        $tag = get_queried_object();

        if (! $tag instanceof \WP_Term) {
            return null;
        }

        if (!$this->polylangActive()) {
            return $tag;
        }

        if ($danishTagID = pll_get_term($tag->term_id, pll_default_language())) {
            return get_term_by('id', $danishTagID, 'post_tag');
        }

        // If danishTagID should somehow return false, return the $tag as fallback
        return $tag;
    }
}
