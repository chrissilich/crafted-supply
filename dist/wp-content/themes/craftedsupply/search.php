<?php
/**
 * Search results page
 */
$context                  = Timber::context();
$context['search_query']  = get_search_query();
$context['results_count'] = $wp_query->found_posts;
$context['results_text']  = $context['results_count'] === 1 ? __( ' result', 'craftedsupply' ): __( ' results', 'craftedsupply' );
$context['total_pages']   = $wp_query->max_num_pages;

Timber::render( 'src/pages/search.twig', $context );

