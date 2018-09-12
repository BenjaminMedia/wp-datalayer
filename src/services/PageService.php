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
}