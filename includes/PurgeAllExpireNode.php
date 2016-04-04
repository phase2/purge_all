<?php
/**
 * @file
 * Provides class that expires nodes with PURGEALL option.
 */

/**
 * This class wraps the Expire module's ExpireNode class.
 *
 * A section of the expire method is duplicated from ExpireNode,
 * as it covers validation behaviors that are not well-separated
 * from the functionality we need to extend.
 */
class PurgeAllExpireNode extends ExpireNode {

  /**
   * {@inheritdoc}
   */
  function expire($node, $action, $skip_action_check = FALSE) {
    // Duplicate Expire module validation logic.
    if (empty($node->nid) || empty($node->type)) {
      return;
    }

    // See if cache settings was overridden for this node type.
    $settings_overridden = variable_get('expire_node_override_defaults_' . $node->type);

    $variable_suffix = '';
    if (!empty($settings_overridden)) {
      // If page cache settings was overridden for this node type we
      // should add "_[NODE-TYPE]" to every variable name we use here.
      $variable_suffix = '_' . $node->type;
    }

    $enabled_actions = variable_get('expire_node_actions' . $variable_suffix, array());
    $enabled_actions = array_filter($enabled_actions);

    // Stop further expiration if executed action is not selected by admin.
    if (!in_array($action, $enabled_actions) && !$skip_action_check) {
      return;
    }
    // End of code duplication. All above is required for Expire compatibility.

    // Purge if configured to do so and expire would trigger Purge module.
    if (variable_get('purge_all_expire_node' . $variable_suffix, FALSE)) {
      // Our implementation only covers external purging.
      // We sidestep the ExpireApi::executeExpiration() logic.
      if (variable_get('expire_status', EXPIRE_STATUS_DISABLED) == EXPIRE_STATUS_ENABLED_EXTERNAL) {
        purge_all_purge();
      }
    }
    else {
      parent::expire($node, $action, $skip_action_check);
    }
  }
}
