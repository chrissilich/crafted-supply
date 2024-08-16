<?php

namespace CraftedSupply\Modules\SEO;

/**
 * Move WordPress SEO Metabox to Bottom
 *
 * @return string
 */
add_filter( 'wpseo_metabox_prio', function(): string {
  return 'low';
} );
