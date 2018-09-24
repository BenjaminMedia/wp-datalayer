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

            if (function_exists('get_field') && $post->post_type === 'contenthub_composite') {
                return get_field('kind', $post->ID);
            }

            return 'article';
        }

        return '';
    }

    public function contentAuthor()
    {
        $post = get_post();

        if (!isset($post)) {
            return '';
        }

        return get_the_author_meta('display_name', $post->post_author);
    }

    public function pageId()
    {
        $post = get_post();

        if (isset($post)) {
            return 'post-' . $this->getDanishArticle()->ID;
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

        if (is_tag()) {
            $tag = get_term_by('name', get_query_var('tag'), 'post_tag');

            if ($this->polylangActive()) {
                if ($danishTagID = pll_get_term($tag->term_id, pll_default_language())) {
                    //dd(get_category($danishCatID));
                    return 'tag-'. get_term_by('id', $danishTagID, 'post_tag')->term_id;
                }
            }

            return 'tag-'. $tag->term_id;
        }

        return '';
    }

    public function pageName()
    {
        if ($post = get_post()) {
            return $post->post_title;
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

        if (is_tag()) {
            return $this->getTag()->name;
        }

        return '';
    }

    public function contentPublication()
    {
        $post = get_post();

        if (!isset($post)) {
            return '';
        }

        return get_the_date("Y-m-d");
    }

    public function contentLastModified()
    {
        $post = get_post();

        if (!isset($post)) {
            return '';
        }

        $mod = get_the_modified_date("Y-m-d");
        if ($mod != $this->contentPublication()) {
            return $mod;
        }

        return '';
    }

    public function pageStatus()
    {
        // We somehow f**cked the is_404() on the old Willow.
        // Therefor please be aware, that we cannot use the function.
        // I'm going to write our own is_404 function...
        if (function_exists('pp_404_is_active')) {
            return !$this->is404() ? 'success' : 'error';
        }

        return is_404() ? 'success' : 'error';
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
        if (is_front_page()) {
            return 'frontpage';
        }

        // post & page
        if (is_singular() && is_single()) {
            // pages dose not have a category. Ship if it's a page
            if (is_object($this->category())) {
                if ($this->polylangActive()) {
                    if ($danishCatID = pll_get_term($this->rootCategory($this->category()->cat_ID)->cat_ID,
                        pll_default_language())) {
                        return get_category($danishCatID)->name;
                    }
                }

                return $this->category()->name;
            }
        }

        if (is_category()) {
            $catID = get_query_var('cat');
            $rootCat = $this->rootCategory($catID);

            // Print danish name if the category is linked to one
            if ($this->polylangActive()) {
                if ($danishCatID = pll_get_term($rootCat->cat_ID, pll_default_language())) {
                    return get_category($danishCatID)->name;
                }
            }

            // Fall back to native language
            return get_category($catID)->cat_name;
        }

        if (is_tag()) {
            return $this->getTag()->name;
        }

        return '';
    }

    public function pageSubPillar()
    {
        // category archive page
        if ($catID = get_query_var('cat')) {
            $category = get_category($catID);

            if ($category->parent > 0) {

                if ($danishCatID = pll_get_term($category->term_id, pll_default_language())) {
                    return get_category($danishCatID)->name;
                }

                return $category->name;
            }

            return '';
        }

        // contenthub, pages, and posts
        if ($post = get_post()) {
            $categoryArrayID = $post->post_category;

            // contenthub and posts
            if ($categoryID = array_shift($categoryArrayID)) {

                $category = get_category($categoryID);

                if ($category->parent > 0) {
                    return $category->name;
                }

            }
            return '';
        }

        // Tags do not have any pillars
        return '';
    }

    public function contentCommercialType()
    {
        $post = get_post();

        if (!isset($post)) {
            return '';
        }
        
        if ($this->polylangActive()) {
            if (($commercialType = get_field('commercial_type', $post->ID)) && (is_singular() || is_single())) {
                return $commercialType;
            }
        }

        return 'editorial';
    }

    public function contentTextLength()
    {
        if ($post = get_post()) {
            if ($post->post_type === 'contenthub_composite') {
                return $this->contenthubCompositeTextLength($post);
            }

            if ($post->post_type === 'page' || $post->post_type === 'post') {
                return str_word_count($post->post_content, 0, $this->charList);
            }

            return 0;
        }

        return 0;
    }

    private function contenthubCompositeTextLength(\WP_Post $post)
    {
        $wordCount = 0;

        if (!function_exists('get_fields')) {
            return $wordCount;
        }

        $compositeContentWidgets = get_fields($post->ID);

        // Time to count!
        $wordCount = $this->countWords($post->post_title) + $this->countWords($compositeContentWidgets['description']);

        // Find description on teaser
        foreach ($compositeContentWidgets['composite_content'] as $compositeWidget) {
            if ($compositeWidget['acf_fc_layout'] === 'image') {
                $image = get_post($compositeWidget['file'])->post_excerpt;
                $wordCount = $wordCount + $this->countWords($image);
            }

            if ($compositeWidget['acf_fc_layout'] === 'text_item') {
                $wordCount = $wordCount + $this->countWords($compositeWidget['body']);
            }

            if ($compositeWidget['acf_fc_layout'] === 'gallery' && $compositeWidget['display_hint'] === 'inline') {
                // Count the title
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

        if ($danishCategory = pll_get_term($categoryId[0], pll_default_language())) {
            return get_category($danishCategory);
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
        $defaultLocale = pll_default_language();

        if ($defaultLocale !== pll_get_post_language($post->ID)) {
            $translations = pll_get_post_translations($post->ID);

            $defaultTranslation = get_post(isset($translations) ? $translations[$defaultLocale] : null);


            //don't do anything if there's no translation on the default language
            if (!empty($defaultTranslation)) {
                return $defaultTranslation;
            }
        }

        return $post;
    }

    private function polylangActive()
    {
        return is_plugin_active('polylang-pro/polylang.php');
    }

    private function getTag()
    {
        $tag = get_term_by('name', get_query_var('tag'), 'post_tag');

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
