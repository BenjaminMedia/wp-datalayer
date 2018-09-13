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

            return 'Article';
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

        if (!isset($post)) {
            return '';
        }

        return $post->ID;
    }

    public function pageName()
    {
        $post = get_post();

        if (!isset($post)) {
            return '';
        }

        return $post->post_title;
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
}