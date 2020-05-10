<?php

namespace Drupal\corona_live_tracker\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Block of Cat Facts... you can't make this stuff up.
 *
 * @Block(
 *   id = "corona_world_tracker_block",
 *   admin_label = @Translation("Corona Live World Tracker Block")
 * )
 */
class CoronaWorldTracker extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'corona_live_world_map',
      '#coronaall' => [],
    ];
  }

}
