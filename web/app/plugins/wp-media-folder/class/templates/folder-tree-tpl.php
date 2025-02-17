<?php

global $typenow;
if ($typenow === 'page') {
    $label = esc_html__('pages', 'wpmf');
} elseif ($typenow === 'post') {
    $label = esc_html__('posts', 'wpmf');
} else {
    $post_type_name  = $typenow;
    $post_types = get_post_types([ 'name' => $post_type_name], 'objects');
    if (!empty($post_types) && is_array($post_types) && isset($post_types[$post_type_name]) && isset($post_types[$post_type_name]->label)) {
        $label = $post_types[$post_type_name]->label;
    }
}

$folder_tree_status_option = wpmfGetOption('wpmf_folder_tree_status');
$class_name = '';
if (!empty($folder_tree_status_option) && (isset($folder_tree_status_option[$typenow]) && $folder_tree_status_option[$typenow] === 'hide') || !isset($folder_tree_status_option[$typenow])) {
    $class_name = 'wpmf-hide-folder-tree';
}
?>
<div class="wpmf-folder-post-type <?php echo esc_attr($class_name); ?>">
    <div class="wpmf-main-tree"></div>
</div>