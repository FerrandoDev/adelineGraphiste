<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
use Joomunited\WPMediaFolder\WpmfHelper;

/**
 * Class WpmfMediaFolderPostType
 * This class contains most of the functionality to organize post types into folders.
 */
class WpmfMediaFolderPostType
{
    /**
     * Collection on post ids
     *
     * @var array $postIds
     */
    private static $postIds;

    /**
     * Media_Folder_Option constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'createFolderTerms'), 15);
        add_action('admin_enqueue_scripts', array($this, 'foldersAdminStyles'));
        add_action('admin_footer', array($this, 'adminFooter'));
        add_action('wp_ajax_wpmf_folder_post_type', array($this, 'startProcess'));
        add_action('wpmf_create_folder_post_type', array($this, 'afterCreateFolder'), 10, 4);
        add_action('pre_get_posts', array($this, 'filterPostsByUnassignedFolder'));
        add_action('pre_get_posts', array($this, 'restrictPostsByTaxonomy'));

        add_action('add_meta_boxes', array($this, 'addCustomTaxonomyMetabox'));
        add_action('save_post', array($this, 'saveCustomTaxonomyRadioButtons'));
    }

    /**
     * Add custom taxonomy metabox
     *
     * @return void
     */
    public function addCustomTaxonomyMetabox()
    {
        global $typenow;
        if (WpmfHelper::isForThisPostType($typenow)) {
            $taxonomy = 'wpmf-folder-type-' . $typenow;
            $metabox_id = 'taxonomy_' . $taxonomy . '_metabox';
            $metabox_title =  esc_html__('Folders', 'wpmf');
            remove_meta_box($taxonomy . 'div', $typenow, 'side');
            add_meta_box($metabox_id, $metabox_title, array($this, 'customTaxonomyRadioButtons'), $typenow, 'side', 'core', array('taxonomy' => $taxonomy));
        }
    }

    /**
     * Display custom taxonomy radio buttons
     *
     * @param WP_Post $post Current post object
     * @param array   $box  Meta box arguments
     *
     * @return void
     */
    public function customTaxonomyRadioButtons($post, $box)
    {
        $taxonomy = $box['args']['taxonomy'];
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'parent' => 0
        ));
        
        $post_terms = wp_get_post_terms($post->ID, $taxonomy, array('fields' => 'ids'));

        echo '<div id="taxonomy-' . esc_attr($taxonomy) . '" class="wpmf-folder-type-metabox categorydiv">';
        echo '<div id="' . esc_attr($taxonomy) . '-all" class="tabs-panel">';
        echo '<ul id="' . esc_attr($taxonomy) . '-checklist" class="categorychecklist form-no-clear">';
        echo '<li id="' . esc_attr($taxonomy) . '-0">';
        echo '<label class="selectit">';
        echo '<input type="radio" id="in-' . esc_attr($taxonomy) . '-0" name="tax_input[' . esc_attr($taxonomy) . '][]" value="0" checked="checked" />' . esc_html__('Unassigned', 'wpmf');
        echo '</label>';
        echo '</li>';
        
        $this->customTermsTree($terms, $post_terms, $taxonomy);

        echo '</ul>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Recursive function to display terms as a tree list
     *
     * @param WP_Term[] $terms      Array of term objects
     * @param int[]     $post_terms Array of term IDs associated with the post
     * @param string    $taxonomy   Taxonomy slug
     * @param integer   $depth      Current depth in the tree
     *
     * @return void
     */
    public function customTermsTree($terms, $post_terms, $taxonomy, $depth = 0)
    {
        if (!is_array($post_terms)) {
            return;
        }
        foreach ($terms as $term) {
            $checked = in_array($term->term_id, $post_terms) ? 'checked="checked"' : '';
            echo '<li id="' . esc_attr($taxonomy) . '-' . esc_attr($term->term_id) . '">';
            echo '<label class="selectit">';
            echo '<input type="radio" id="in-' . esc_attr($taxonomy) . '-' . esc_attr($term->term_id) . '" name="tax_input[' . esc_attr($taxonomy) . '][]" value="' . esc_attr($term->term_id) . '" ' . esc_attr($checked) . ' />' . esc_html($term->name);
            echo '</label>';

            $child_terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'parent' => $term->term_id
            ));

            if (!empty($child_terms)) {
                echo '<ul class="children">';
                $this->customTermsTree($child_terms, $post_terms, $taxonomy, $depth + 1);
                echo '</ul>';
            }

            echo '</li>';
        }
    }

    /**
     * Save custom taxonomy radio buttons
     *
     * @param integer $post_id The ID of the post being saved
     *
     * @return void
     */
    public function saveCustomTaxonomyRadioButtons($post_id)
    {
        global $typenow;
        if (WpmfHelper::isForThisPostType($typenow)) {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // phpcs:ignore
            if (!isset($_POST['tax_input'])) {
                return;
            }

            $wpmf_taxonomy = 'wpmf-folder-type-' . $typenow;
            // phpcs:ignore
            foreach ($_POST['tax_input'] as $taxonomy => $terms) {
                if ($taxonomy === $wpmf_taxonomy) {
                    $terms = (array) $terms;
                    if (!empty($terms)) {
                        wp_set_object_terms($post_id, (int)end($terms), $taxonomy);
                    } else {
                        wp_set_object_terms($post_id, array(), $taxonomy);
                    }
                }
            }
        }
    }


    /**
     * Add folders data to footer
     *
     * @return void
     */
    public function adminFooter()
    {
        if (self::isActiveForScreen()) {
            global $typenow;
            $taxonomy = 'wpmf-folder-type-'.$typenow;
            $terms    = self::getTermsHierarchical($taxonomy);
            include_once dirname(dirname(__FILE__)).'/class/templates/folder-tree-tpl.php';
        }
    }

    /**
     * Add Folder Styles
     *
     * @return void
     */
    public function foldersAdminStyles()
    {
        if (self::isActiveForScreen()) {
            global $typenow;
            $width = get_option('wpmf_dynamic_width_for_'.$typenow);
            $width = empty($width)||!is_numeric($width) ? 290 : intval($width);
            if ($width > 1200) {
                $width = 290;
            }

            $minimize_folder_tree_post_type = wpmfGetOption('wpmf_minimize_folder_tree_post_type');
            $folder_tree_status_option = wpmfGetOption('wpmf_folder_tree_status');
            $wpmf_folder_tree_status = 'hide';
            if (!empty($minimize_folder_tree_post_type) && $minimize_folder_tree_post_type) {
                $wpmf_folder_tree_status = 'hide';
            }
            if (!empty($folder_tree_status_option) && isset($folder_tree_status_option[$typenow])) {
                $wpmf_folder_tree_status = $folder_tree_status_option[$typenow];
            }
            $wpmf_folder_tree_status === 'show' ? $css = 'body.wp-admin #wpcontent {padding-left:'.($width + 20).'px}' : $css = 'body.wp-admin #wpcontent {padding-left: 25px} .wpmf-hide-folder-tree .wpmf-main-tree';
            wp_register_style('wpmf-css-handle', false);
            wp_enqueue_style('wpmf-css-handle');
            wp_add_inline_style('wpmf-css-handle', $css);

            $postType = $typenow;
            $this->loadAssets($postType);
        }
    }

    /**
     * Check is folder active for current screen
     *
     * @return boolean
     */
    public function isActiveForScreen()
    {
        global $typenow, $current_screen;

        if (WpmfHelper::isForThisPostType($typenow) && 'edit' == $current_screen->base) {
            return true;
        }

        return false;
    }

    /**
     * Run ajax
     *
     * @return void
     */
    public function startProcess()
    {
        if (empty($_REQUEST['wpmf_nonce'])
            || !wp_verify_nonce($_REQUEST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_REQUEST['task'])) {
            switch ($_REQUEST['task']) {
                case 'reload_folder_tree':
                    $this->reloadFolderTree();
                    break;
                case 'add_folder':
                    $this->addFolder();
                    break;
                case 'edit_folder':
                    $this->editFolder();
                    break;
                case 'delete_folder':
                    $this->deleteFolder();
                    break;
                case 'delete_multiple_folders':
                    $this->deleteMultipleFolders();
                    break;
                case 'move_folder':
                    $this->moveFolder();
                    break;
                case 'set_folder_color':
                    $this->setFolderColor();
                    break;
                case 'assign_folder_to_post':
                    $this->assignFolderToPost();
                    break;
                case 'get_folder_permissions':
                    $this->getFolderPermissions();
                    break;
                case 'save_folder_permissions':
                    $this->saveFolderPermissions();
                    break;
                case 'reorderfolder':
                    $this->reorderfolder();
                    break;
                case 'change_folder_tree_display_status':
                    $this->changeFolderTreeDisplayStatus();
                    break;
            }
        }
    }

    /**
     * Create folder taxonomies
     *
     * @return void
     */
    public function createFolderTerms()
    {
        $settings = get_option('wpmf_settings');
        if (isset($settings) && isset($settings['wpmf_active_folders_post_types'])) {
            $post_types = $settings['wpmf_active_folders_post_types'];
            $post_types = is_array($post_types) ? $post_types : [];
        } else {
            $post_types = array();
        }
        if (!empty($post_types)) {
            foreach ($post_types as $postType) {
                $taxonomy = 'wpmf-folder-type-'.$postType;

                $labels = [
                    'name'          => esc_html__('Folders', 'wpmf'),
                    'singular_name' => esc_html__('Folder', 'wpmf'),
                    'all_items'     => esc_html__('All Folders', 'wpmf'),
                    'edit_item'     => esc_html__('Edit Folder', 'wpmf'),
                    'update_item'   => esc_html__('Update Folder', 'wpmf'),
                    'add_new_item'  => esc_html__('Add New Folder', 'wpmf'),
                    'new_item_name' => esc_html__('Add folder name', 'wpmf'),
                    'menu_name'     => esc_html__('Folders', 'wpmf'),
                    'search_items'  => esc_html__('Search Folders', 'wpmf'),
                    'parent_item'   => esc_html__('Parent Folder', 'wpmf'),
                ];

                $args = array(
                    'label'                 => esc_html__('Folder', 'wpmf'),
                    'labels'                => $labels,
                    'show_tagcloud'         => false,
                    'hierarchical'          => true,
                    'public'                => false,
                    'show_ui'               => true,
                    'show_in_menu'          => false,
                    'show_in_rest'          => false,
                    'show_admin_column'     => true,
                    'update_count_callback' => '_update_post_term_count',
                    'query_var'             => true,
                    'rewrite'               => false,
                    'capabilities'          => array(
                        'manage_terms' => 'manage_categories',
                        'edit_terms'   => 'manage_categories',
                        'delete_terms' => 'manage_categories',
                        'assign_terms' => 'manage_categories',
                    ),
                );

                register_taxonomy(
                    $taxonomy,
                    $postType,
                    $args
                );

                switch ($postType) {
                    case 'post':
                        add_filter('manage_posts_columns', array($this, 'manageColumnsHead'));
                        add_action('manage_posts_custom_column', array($this, 'manageColumnsContent'), 10, 2);
                        break;
                    case 'page':
                        add_filter('manage_page_posts_columns', array($this, 'manageColumnsHead'));
                        add_action('manage_page_posts_custom_column', array($this, 'manageColumnsContent'), 10, 2);
                        break;
                    default:
                        add_filter('manage_edit-'.$postType.'_columns', array($this, 'manageColumnsHead'), 99999);
                        add_action('manage_'.$postType.'_posts_custom_column', array($this, 'manageColumnsContent'), 2, 2);
                        break;
                }
            }
        }
    }

    /**
     * Add a new folder via ajax
     *
     * @return void
     */
    public function addFolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['name']) && $_POST['name'] !== '') {
            $term = esc_attr(trim($_POST['name']));
        } else {
            $term = __('New folder', 'wpmf');
        }

        if (isset($_POST['folder_post_type_name']) && $_POST['folder_post_type_name'] !== '') {
            $taxonomy = esc_attr(trim($_POST['folder_post_type_name']));
        } else {
            die();
        }
        

        $termParent = (int)$_POST['parent'] | 0;
        $termParent = $this->getFolderParent($termParent, $termParent);

        $user_id  = get_current_user_id();
        $is_access = self::getAccess($termParent, $user_id, 'add_folder', $taxonomy);
        if (!$is_access) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('You not have a permission to create folder!', 'wpmf')));
        }

        // insert new term
        $inserted = wp_insert_term($term, $taxonomy, array('parent' => $termParent));
        if (is_wp_error($inserted)) {
            wp_send_json(array('status' => false, 'msg' => $inserted->get_error_message()));
        } else {
            // update term_group for new term
            $updateted = wp_update_term($inserted['term_id'], $taxonomy, array('term_group' => $user_id));
            $termInfos = get_term($updateted['term_id'], $taxonomy);
            $terms    = self::getTermsHierarchical($taxonomy);
            do_action('wpmf_create_folder_post_type', $inserted['term_id'], $term, $termParent, array('trigger' => 'folder_post_type_action'));
            wp_send_json(array(
                'status'           => true,
                'term'             => $termInfos,
                'categories'       => $terms,
            ));
        }
    }

    /**
     * After create folder
     *
     * @param integer $folder_id   Folder ID
     * @param string  $folder_name Folder name
     * @param integer $parent      Folder parent
     * @param array   $params      Params details
     *
     * @return void
     */
    public function afterCreateFolder($folder_id, $folder_name, $parent, $params)
    {
        $user_id  = get_current_user_id();
        // add permission
        $role = WpmfHelper::getRoles($user_id);
        if ($role === 'administrator') {
            $role = 0;
            $user_id = 0;
        }

        add_term_meta((int)$folder_id, 'inherit_folder', 1);
        add_term_meta((int)$folder_id, 'wpmf_folder_role_permissions', array($role, 'add_media', 'move_media', 'view_folder', 'add_folder', 'update_folder', 'remove_folder', 'view_media', 'remove_media', 'update_media'));
        add_term_meta((int)$folder_id, 'wpmf_folder_user_permissions', array($user_id, 'add_media', 'move_media', 'view_folder', 'add_folder', 'update_folder', 'remove_folder', 'view_media', 'remove_media', 'update_media'));
    }

    /**
     * Change a folder name from ajax request
     *
     * @return void
     */
    public function editFolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['folder_post_type_name']) && $_POST['folder_post_type_name'] !== '') {
            $taxonomy = esc_attr(trim($_POST['folder_post_type_name']));
        } else {
            die();
        }

        $folder_id = $this->getFolderParent($_POST['id'], $_POST['id']);
        $user_id  = get_current_user_id();
        $is_access = self::getAccess($folder_id, $user_id, 'update_folder', $taxonomy);
        if (!$is_access) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('You not have a permission to edit folder!', 'wpmf')));
        }

        $term_name = esc_attr($_POST['name']);
        if (!$term_name) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('Folder names can\'t be empty', 'wpmf')));
        }

        $type = get_term_meta((int)$folder_id, 'wpmf_drive_root_type', true);
        if (!empty($type)) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('Can\'t edit cloud root folder!', 'wpmf')));
        }

        // Retrieve term informations
        $term = get_term((int)$folder_id, $taxonomy);

        // check duplicate name
        $siblings = get_categories(
            array(
                'taxonomy' => $taxonomy,
                'fields'   => 'names',
                'get'      => 'all',
                'parent'   => $term->parent
            )
        );

        if (in_array($term_name, $siblings)) {
            // Another folder with the same name exists
            wp_send_json(array('status' => false, 'msg' => esc_html__('A folder already exists here with the same name. Please try with another name, thanks :)', 'wpmf')));
        }

        $updated_term = wp_update_term((int)$folder_id, $taxonomy, array('name' => $term_name));
        if ($updated_term instanceof WP_Error) {
            wp_send_json(array('status' => false, 'msg' => $updated_term->get_error_messages()));
        } else {
            // Retrieve more information than wp_update_term function returns
            $term_details = get_term($updated_term['term_id'], $taxonomy);

            /**
             * Update folder name
             *
             * @param integer Folder ID
             * @param string  Updated name
             */
            do_action('wpmf_update_folder_name', $folder_id, $term_name);
            wp_send_json(array('status' => true, 'details' => $term_details));
        }
    }

    /**
     * Delete folder via ajax
     *
     * @return void
     */
    public function deleteFolder()
    {
        if (empty($_POST['wpmf_nonce']) || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['folder_post_type_name']) && $_POST['folder_post_type_name'] !== '') {
            $taxonomy = esc_attr(trim($_POST['folder_post_type_name']));
        } else {
            die();
        }

        $folder_id = $this->getFolderParent($_POST['id'], $_POST['id']);
        $user_id  = get_current_user_id();
        $is_access = self::getAccess($folder_id, $user_id, 'remove_folder', $taxonomy);
        if (!$is_access) {
            wp_send_json(array('status' => false, 'error' => esc_html__('You don\'t have permission to delete this folder!', 'wpmf')));
        }

        // delete current folder
        $this->doRemoveChildFolders((int) $_POST['id'], $taxonomy, $user_id);
        if (wp_delete_term((int)$folder_id, $taxonomy)) {
            $terms    = self::getTermsHierarchical($taxonomy);
            wp_send_json(array(
                'status'           => true,
                'categories'       => $terms
            ));
        } else {
            wp_send_json(array('status' => false, 'error' => esc_html__('You don\'t have permission to delete this folder!', 'wpmf')));
        }
    }

    /**
     * Delete multiple folders
     *
     * @return void
     */
    public function deleteMultipleFolders()
    {
        if (empty($_POST['wpmf_nonce']) || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['folder_post_type_name']) && $_POST['folder_post_type_name'] !== '') {
            $taxonomy = esc_attr(trim($_POST['folder_post_type_name']));
        } else {
            die();
        }

        $folder_ids = isset($_POST['id']) ? $_POST['id'] : 0;
        if (empty($folder_ids)) {
            wp_send_json(array(
                'status'           => true
            ));
        }
        $return = $this->doRemoveFolders($folder_ids, $taxonomy);
        if ($return) {
            wp_send_json(array(
                'status' => false,
                'msg'    => 'limit'
            ));
        } else {
            $terms  = self::getTermsHierarchical($taxonomy);
            wp_send_json(
                array(
                    'status'           => true,
                    'categories'       => $terms
                )
            );
        }
    }

    /**
     * Do remove multiple folders
     *
     * @param string $folder_list Folder list id
     * @param string $taxonomy    Taxonomy
     *
     * @return boolean
     */
    public function doRemoveFolders($folder_list, $taxonomy)
    {
        $user_id  = get_current_user_id();
        // delete all subfolder and subfile
        $folders = explode(',', $folder_list);
        $sub_folders = array();
        foreach ($folders as $folder_id) {
            $is_access = self::getAccess($folder_id, $user_id, 'remove_folder', $taxonomy);
            if (!$is_access) {
                wp_send_json(array('status' => false, 'error' => esc_html__('You don\'t have permission to delete this folder!', 'wpmf')));
            }
            $this->doRemoveChildFolders($folder_id, $taxonomy, $user_id);
            if (!wp_delete_term((int)$folder_id, $taxonomy)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Do remove child folders
     *
     * @param string  $folder_id Parent folder id
     * @param string  $taxonomy  Taxonomy
     * @param integer $user_id   Current user id
     *
     * @return boolean
     */
    public function doRemoveChildFolders($folder_id, $taxonomy, $user_id)
    {
        $childs = get_term_children($folder_id, $taxonomy);
        if (!empty($childs)) {
            foreach ($childs as $key => $child) {
                $is_access = self::getAccess($child, $user_id, 'remove_folder', $taxonomy);
                if (!$is_access) {
                    wp_send_json(array('status' => false, 'error' => esc_html__('You don\'t have permission to delete this folder!', 'wpmf')));
                }
                wp_delete_term($child, $taxonomy);
            }
        }

        return true;
    }

    /**
     * Move a folder via ajax
     *
     * @return void
     */
    public function moveFolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['folder_post_type_name']) && $_POST['folder_post_type_name'] !== '') {
            $taxonomy = esc_attr(trim($_POST['folder_post_type_name']));
        } else {
            die();
        }

        $parent = $this->getFolderParent($_POST['id_category'], $_POST['id_category']);

        // Check that the folder we move into is not a child of the folder we're moving
        $wpmf_childs = $this->getFolderChild($_POST['id'], array(), $taxonomy);
        if (in_array((int)$parent, $wpmf_childs)) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('A folder already exists here with the same name. Please try with another name, thanks :)', 'wpmf')));
        }

        /*
         * Check if there is another folder with the same name
         * in the folder we moving into
         */
        $term     = get_term($parent);
        $siblings = get_categories(
            array(
                'taxonomy' => $taxonomy,
                'fields'   => 'names',
                'get'      => 'all',
                'parent'   => (int)$parent
            )
        );
        if (in_array($term->name, $siblings)) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('Error, can\'t move', 'wpmf')));
        }

        $r = wp_update_term((int) $_POST['id'], $taxonomy, array('parent' => (int)$parent));
        if ($r instanceof WP_Error) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('Error, can\'t move', 'wpmf')));
        } else {
            // Retrieve the updated folders hierarchy
            $terms = self::getTermsHierarchical($taxonomy);
            wp_send_json(
                array(
                    'status'           => true,
                    'categories'       => $terms
                )
            );
        }
    }

    /**
     * Assign folder to multiple posts, pages, ...
     *
     * @return void
     */
    public function assignFolderToPost()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['folder_post_type_name']) && $_POST['folder_post_type_name'] !== '') {
            $taxonomy = esc_attr(trim($_POST['folder_post_type_name']));
        } else {
            die();
        }

        if (isset($_POST['post_type']) && $_POST['post_type'] !== '') {
            $postType = esc_attr(trim($_POST['post_type']));
        } else {
            die();
        }

        if (isset($_POST['type']) && $_POST['type'] !== '') {
            $type = esc_attr(trim($_POST['type']));
        } else {
            die();
        }

        $term_id = $_POST['folder_id'];
        if ($term_id === '-1') {
            $term_id = 0;
        }
        $status  = true;

        $user_id  = get_current_user_id();
        $is_access = self::getAccess($term_id, $user_id, 'update_folder', $taxonomy);
        if (!$is_access) {
            wp_send_json(array('status' => false, 'error' => esc_html__('You don\'t have permission to add the item(s) to this folder!', 'wpmf')));
        }

        if ($type === 'single') {
            $postID = $_POST['post_id'];
            $terms = get_the_terms($postID, $taxonomy);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    wp_remove_object_terms($postID, $term->term_id, $taxonomy);
                }
            }
            wp_set_post_terms($postID, $term_id, $taxonomy, $status);
        } elseif ($type === 'multiple') {
            $postIDs = trim($_POST['post_id'], ',');
            $postArray = explode(',', $postIDs);
            if (is_array($postArray)) {
                foreach ($postArray as $postID) {
                    $terms = get_the_terms($postID, $taxonomy);
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            wp_remove_object_terms($postID, $term->term_id, $taxonomy);
                        }
                    }
                    wp_set_post_terms($postID, $term_id, $taxonomy, $status);
                }
            }
        }

        $terms = self::getTermsHierarchical($taxonomy);
        wp_send_json(array(
            'status'     => true,
            'categories' => $terms
        ));
    }

    /**
     * Ajax set folder color
     *
     * @return void
     */
    public function setFolderColor()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        $colors_option = wpmfGetOption('folder_color');
        if (isset($_POST['folder_id']) && isset($_POST['color'])) {
            if (empty($colors_option)) {
                $colors_option                      = array();
                $colors_option[$_POST['folder_id']] = $_POST['color'];
            } else {
                $colors_option[$_POST['folder_id']] = $_POST['color'];
            }
            wpmfSetOption('folder_color', $colors_option);
            wp_send_json(array('status' => true));
        }
        wp_send_json(array('status' => false));
    }

    /**
     * Save folder permissions
     *
     * @return void
     */
    public function getFolderPermissions()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            wp_send_json(array('status' => false));
        }

        if (empty($_POST['folder_id'])) {
            wp_send_json(array('status' => false));
        }

        $role_permissions = get_term_meta((int)$_POST['folder_id'], 'wpmf_folder_role_permissions');
        $user_permissions = get_term_meta((int)$_POST['folder_id'], 'wpmf_folder_user_permissions');
        $inherit_folder = get_term_meta((int)$_POST['folder_id'], 'inherit_folder', true);
        if ($inherit_folder === '' && $role_permissions === '' && $user_permissions === '') {
            $inherit_folder = 1;
        }
        wp_send_json(array('status' => true, 'role_permissions' => $role_permissions, 'user_permissions' => $user_permissions, 'inherit_folder' => $inherit_folder));
    }

    /**
     * Save folder permissions
     *
     * @return void
     */
    public function saveFolderPermissions()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            wp_send_json(array('status' => false));
        }

        if (empty($_POST['folder_id'])) {
            wp_send_json(array('status' => false));
        }

        $inherit_folder = isset($_POST['inherit_folder']) ? $_POST['inherit_folder'] : 1;
        update_term_meta((int)$_POST['folder_id'], 'inherit_folder', $inherit_folder);
        $role_permissions = (isset($_POST['role_permissions'])) ? json_decode(stripslashes($_POST['role_permissions']), true) : array();
        delete_term_meta((int)$_POST['folder_id'], 'wpmf_folder_role_permissions');
        foreach ($role_permissions as $role_permission) {
            add_term_meta((int)$_POST['folder_id'], 'wpmf_folder_role_permissions', $role_permission);
        }

        $user_permissions = (isset($_POST['user_permissions'])) ? json_decode(stripslashes($_POST['user_permissions']), true) : array();
        delete_term_meta((int)$_POST['folder_id'], 'wpmf_folder_user_permissions');
        foreach ($user_permissions as $user_permission) {
            add_term_meta((int)$_POST['folder_id'], 'wpmf_folder_user_permissions', $user_permission);
        }

        wp_send_json(array('status' => true));
    }

    /**
     * Ajax change folder tree display status
     *
     * @return void
     */
    public function changeFolderTreeDisplayStatus()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['status']) && isset($_POST['post_type'])) {
            $status_option = wpmfGetOption('wpmf_folder_tree_status');
            if (empty($status_option)) {
                $status_option                      = array();
                $status_option[$_POST['post_type']] = $_POST['status'];
            } else {
                $status_option[$_POST['post_type']] = $_POST['status'];
            }
            wpmfSetOption('wpmf_folder_tree_status', $status_option);
            wp_send_json(array('status' => true));
        }
        
        wp_send_json(array('status' => false));
    }

    /**
     * Get folder parent root ID
     *
     * @param integer $parent Current ID
     * @param integer $folder Folder ID
     *
     * @return mixed
     */
    public function getFolderParent($parent, $folder)
    {
        // $wpmf_active_media      = get_option('wpmf_active_media');
        // if ((int)$wpmf_active_media === 1 && empty($folder)) {
        //     $wpmfterm = $this->termRoot();
        //     if (!empty($wpmfterm)) {
        //         $parent = $wpmfterm['term_rootId'];
        //     }
        // }

        return $parent;
    }

    /**
     * Get folders by hierarchy
     *
     * @param string $taxonomy Taxonomy
     *
     * @return array
     */
    public function getTermsHierarchical($taxonomy)
    {
        global $wp_post_types;

        $terms = get_terms(
            array(
                'taxonomy'              => $taxonomy,
                'hide_empty'            => false,
                'parent'                => 0,
                'hierarchical'          => false
            )
        );

        $type = str_replace('wpmf-folder-type-', '', $taxonomy);

        $hierarchical_terms = [];
        
        $root = new stdClass();
        $unassign = new stdClass();
        if (isset($wp_post_types[$type])) {
            $root->label = esc_html__('All', 'wpmf') . ' ' .$wp_post_types[$type]->labels->name;
            $unassign->label = esc_html__('Unassigned', 'wpmf') . ' ' .$wp_post_types[$type]->labels->name;
        } else {
            $root->label = esc_html__('All', 'wpmf');
            $unassign->label = esc_html__('Unassigned', 'wpmf');
        }
        $root->id = 0;
        $root->term_id = 0;
        $root->parent_id = 0;
        $root->slug = '';
        $root->getTermAdminLink = self::getTermAdminLink(0, $taxonomy);
        $root->item_count = wp_count_posts($type)->publish + wp_count_posts($type)->draft + wp_count_posts($type)->future + wp_count_posts($type)->private + wp_count_posts($type)->pending;
        $root->lower_label = strtolower($root->label);
        $root->drive_type = '';
        $root->order = '';
        $hierarchical_terms[0] = $root;

        $args = array(
            'post_type' => $type,
            'post_status' => array('publish', 'draft', 'future', 'private', 'pending'),
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'operator' => 'NOT EXISTS'
                )
            ),
            'fields' => 'ids'
        );
        $queryUnassignPost = new WP_Query($args);
        $countUnassignPost = $queryUnassignPost->found_posts;

        $unassign->id = -1;
        $unassign->term_id = -1;
        $unassign->parent_id = 0;
        $unassign->slug = 'wpmf-unassigned';
        $unassign->getTermAdminLink = self::getTermAdminLink(-1, $taxonomy);
        $unassign->item_count = $countUnassignPost;
        $unassign->lower_label = strtolower($unassign->label);
        $unassign->drive_type = '';
        $unassign->order = '';

        $hierarchical_terms[-1] = $unassign;
        
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if (!empty($term) && isset($term->term_id)) {
                    $is_access = self::getAccess($term->term_id, get_current_user_id(), 'view_folder', $taxonomy);
                    $role_permissions = get_term_meta($term->term_id, 'wpmf_folder_role_permissions');
                    if (!$is_access) {
                        continue;
                    }
                    $folder_count = 0;
                    $postArray = self::getPostsExcludeChildTerms($type, $taxonomy, $term->term_id);

                    if (!empty($postArray)) {
                        $folder_count = count($postArray);
                    }

                    $order = get_term_meta($term->term_id, $taxonomy.'_order', true);

                    $term->label          = $term->name;
                    $term->lower_label    = strtolower($term->name);
                    $term->depth          = 0;
                    $term->id             = $term->term_id;
                    $term->parent_id      = $term->parent;
                    $term->item_count     = $folder_count;
                    $term->drive_type     = '';
                    $term->order          = $order ?? '';
                    $term->getTermAdminLink = self::getTermAdminLink($term->term_id, $taxonomy);
                    $hierarchical_terms[$term->term_id] = $term;
                    $hierarchical_terms   = self::getChildTerms($taxonomy, $hierarchical_terms, $term->term_id, $term->depth);
                }
            }
        }

        return $hierarchical_terms;
    }


    /**
     * Get Child folders
     *
     * @param string  $taxonomy           Taxonomy
     * @param string  $hierarchical_terms Hierarchical terms
     * @param integer $term_id            Term ID
     * @param integer $depth              Depth
     *
     * @return array
     */
    public static function getChildTerms($taxonomy, $hierarchical_terms, $term_id, $depth = 0)
    {
        $terms = get_terms(
            array(
                'taxonomy'              => $taxonomy,
                'hide_empty'            => false,
                'parent'                => $term_id,
                'hierarchical'          => false
            )
        );
        if (!empty($terms)) {
            $type = str_replace('wpmf-folder-type-', '', $taxonomy);
            $depth++;
            foreach ($terms as $term) {
                if (isset($term->name)) {
                    $is_access = self::getAccess($term->term_id, get_current_user_id(), 'view_folder', $taxonomy);
                    $role_permissions = get_term_meta($term->term_id, 'wpmf_folder_role_permissions');
                    if (!$is_access) {
                        continue;
                    }
                    $folder_count = 0;

                    $postArray = self::getPostsExcludeChildTerms($type, $taxonomy, $term->term_id);

                    if (!empty($postArray)) {
                        $folder_count = count($postArray);
                    }

                    $order = get_term_meta($term->term_id, $taxonomy.'_order', true);
                    $term->label          = $term->name;
                    $term->lower_label    = strtolower($term->name);
                    $term->depth          = $depth;
                    $term->id             = $term->term_id;
                    $term->parent_id      = $term->parent;
                    $term->item_count     = $folder_count;
                    $term->drive_type     = '';
                    $term->order          = $order ?? '';
                    $term->getTermAdminLink = self::getTermAdminLink($term->term_id, $taxonomy);
                    $hierarchical_terms[$term->term_id] = $term;
                    $hierarchical_terms   = self::getChildTerms($taxonomy, $hierarchical_terms, $term->term_id, $depth);
                }
            }
        }

        return $hierarchical_terms;
    }

    /**
     * Reload folder tree
     *
     * @param string  $type     Post type
     * @param string  $taxonomy Taxonomy
     * @param integer $term_id  Term ID
     *
     * @return array
     */
    public static function getPostsExcludeChildTerms($type, $taxonomy, $term_id)
    {
        // Retrieve all child terms of the specified parent term
        $child_terms = get_terms(array(
            'taxonomy'   => $taxonomy,
            'child_of'   => $term_id,
            'fields'     => 'ids',
            'hide_empty' => false
        ));

        // Include the parent term in the exclusion list
        $exclude_terms = $child_terms;

        // Query to get posts associated with the parent term but not the child terms
        $posts = get_posts(array(
            'posts_per_page' => -1,
            'post_type'      => $type,
            'post_status'    => array('publish', 'draft', 'future', 'private', 'pending'),
            'tax_query'      => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term_id,
                ),
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $exclude_terms,
                    'operator' => 'NOT IN',
                ),
            ),
        ));

        return $posts;
    }

    /**
     * Reload folder tree
     *
     * @return void
     */
    public function reloadFolderTree()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['folder_post_type_name']) && $_POST['folder_post_type_name'] !== '') {
            $taxonomy = esc_attr(trim($_POST['folder_post_type_name']));
        } else {
            die();
        }

        $terms = self::getTermsHierarchical($taxonomy);
        wp_send_json(
            array(
                'status'           => true,
                'categories'       => $terms
            )
        );
    }

    /**
     * Ajax custom order for folder
     *
     * @return void
     */
    public function reorderfolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['folder_post_type_name']) && $_POST['folder_post_type_name'] !== '') {
            $taxonomy = esc_attr(trim($_POST['folder_post_type_name']));
        } else {
            die();
        }

        if (isset($_POST['order'])) {
            $orders = (array) json_decode(stripslashes_deep($_POST['order']));
            if (is_array($orders) && !empty($orders)) {
                foreach ($orders as $position => $id) {
                    update_term_meta(
                        (int) $id,
                        $taxonomy.'_order',
                        (int) $position
                    );
                }
            }

            $terms = self::getTermsHierarchical($taxonomy);
            wp_send_json(
                array(
                    'status'           => true,
                    'categories'       => $terms
                )
            );
        }
    }

    /**
     * Generate a custom admin link for a specific term in a custom taxonomy.
     *
     * @param integer $term_id  The ID of the term
     * @param string  $taxonomy Taxonomy
     *
     * @return string
     */
    public static function getTermAdminLink($term_id, $taxonomy)
    {
        $type = str_replace('wpmf-folder-type-', '', $taxonomy);
        if ($term_id === 0) {
            return admin_url('edit.php?post_type='.$type);
        }

        if ($term_id > 0) {
            $term = get_term($term_id, $taxonomy);
            if (is_wp_error($term) || !$term) {
                return '';
            }
            $term_slug = $term->slug;
        } elseif ($term_id === -1) {
            $term_slug = 'wpmf-unassigned';
        }

        $admin_url = admin_url('edit.php');

        $query_args = array(
            'post_type' => $type,
            $taxonomy => $term_slug
        );

        $admin_link = add_query_arg($query_args, $admin_url);

        return $admin_link;
    }

    /**
     * Get access
     *
     * @param integer $term_id    Folder ID
     * @param integer $user_id    User ID
     * @param string  $capability Capability
     * @param string  $taxonomy   Taxonomy
     *
     * @return boolean
     */
    public static function getAccess($term_id, $user_id, $capability, $taxonomy)
    {
        $is_access = false;
        $roles = WpmfHelper::getAllRoles($user_id);
        if (in_array('administrator', $roles)) {
            return true;
        }

        if (empty($term_id)) {
            return false;
        }
        
        global $current_user;
        $term = get_term($term_id, $taxonomy);
        // inherit folder permissions
        $role_permissions = get_term_meta((int)$term_id, 'wpmf_folder_role_permissions', true);
        $user_permissions = get_term_meta((int)$term_id, 'wpmf_folder_user_permissions', true);
        $inherit_folder = get_term_meta((int)$term_id, 'inherit_folder', true);
        if ((($inherit_folder === '' && ($role_permissions === '' || empty($role_permissions[0])) && ($user_permissions === '' || empty($user_permissions[0]))) || !empty($inherit_folder)) && $term->parent !== 0) {
            $ancestors = get_ancestors($term_id, $taxonomy, 'taxonomy');
            if (!empty($ancestors)) {
                $t = false;
                foreach ($ancestors as $ancestor) {
                    $inherit_folder = get_term_meta((int)$ancestor, 'inherit_folder', true);
                    if ((int)$inherit_folder === 0) {
                        $t = true;
                        $term_id = $ancestor;
                        break;
                    }
                }

                if (!$t) {
                    $term_id = $ancestors[count($ancestors) - 1];
                }
            }
        }

        if ($capability !== 'view_folder' && !$term_id) {
            return false;
        }

        // only show role folder when access type is 'role'
        $access_type     = get_option('wpmf_create_folder');
        if ($access_type === 'role') {
            if (in_array($term->name, $roles) && strpos($term->slug, '-wpmf-role') !== false) {
                return true;
            }
        }

        // get access by role
        $permissions = get_term_meta((int)$term_id, 'wpmf_folder_role_permissions');
        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                if (!empty($permission[0]) && in_array($permission[0], $roles) && in_array($capability, $permission)) {
                    $is_access = true;
                    break;
                }
            }
        }

        if ($is_access) {
            return true;
        } else {
            // get access by user
            $permissions = get_term_meta((int)$term_id, 'wpmf_folder_user_permissions');
            if ($term->name === $current_user->user_login && (int) $term->term_group === (int) get_current_user_id()) {
                return true;
            }

            if (!empty($permissions)) {
                foreach ($permissions as $permission) {
                    if ((int)$permission[0] === get_current_user_id() && in_array($capability, $permission)) {
                        $is_access = true;
                        break;
                    }
                }
            }
        }

        return $is_access;
    }

    /**
     * Query post in folder
     *
     * @param object $query Params use to query post
     *
     * @return object $query
     */
    public function restrictPostsByTaxonomy($query)
    {
        if (!is_admin() || !$query->is_main_query() || !isset($query->query_vars['post_type'])) {
            return $query;
        }

        global $typenow;
        if (self::isActiveForScreen()) {
            $user_id  = get_current_user_id();
            $role = WpmfHelper::getRoles($user_id);
            $tax = 'wpmf-folder-type-'.$typenow;

            $current_term_id = get_queried_object_id();
            $parse_url = parse_url(site_url());
            $host = md5($parse_url['host']);
            if (isset($_COOKIE['lastAccessFolder_'.$host]) && is_int($current_term_id)) {
                $lastAccessFolder = $current_term_id;
                // phpcs:ignore
                if (isset($_GET[$tax]) && $_GET[$tax] == 'wpmf-unassigned') {
                    $lastAccessFolder = -1;
                }
                if ($current_term_id !== 0) {
                    setcookie('lastAccessFolder_'.$host, $lastAccessFolder, time() + (365 * 24 * 60 * 60), '/', COOKIE_DOMAIN);
                    $current_term_id = $lastAccessFolder;
                } else {
                    if (is_numeric($_COOKIE['lastAccessFolder_'.$host])) {
                        $current_term_id = $_COOKIE['lastAccessFolder_'.$host];
                    }
                }
            }

            if (!$current_term_id) {
                return $query;
            }

            if ($current_term_id === '-1') {
                $query->set('tax_query', array(
                    array(
                        'taxonomy' => $tax,
                        'field'    => 'term_id',
                        'terms'    => array(),
                        'operator' => 'NOT EXISTS'
                    ),
                ));

                return $query;
            }

            $current_term = get_term($current_term_id, $tax);
            if (is_wp_error($current_term) || !$current_term) {
                return $query;
            }

            $child_terms = get_terms(array(
                'taxonomy'   => $tax,
                'child_of'   => $current_term_id,
                'fields'     => 'ids',
                'hide_empty' => false,
            ));

            if (!empty($child_terms)) {
                $query->set('tax_query', array(
                    array(
                        'taxonomy' => $tax,
                        'field'    => 'term_id',
                        'terms'    => $current_term_id,
                        'include_children' => false,
                    )
                ));
            } else {
                $query->set('tax_query', array(
                    array(
                        'taxonomy' => $tax,
                        'field'    => 'term_id',
                        'terms'    => $current_term_id,
                    )
                ));
            }

            if ($role === 'administrator') {
                return $query;
            }

            $current_user = wp_get_current_user();
            $terms = self::getTermsHierarchical($tax);
            $access_terms = array();
            foreach ($terms as $key => $value) {
                if ($value->term_id !== 0) {
                    $access_terms[] = $value->term_id;
                }
            }
            
            if (!empty($access_terms)) {
                $tax_query = array(
                    array(
                        'taxonomy' => $tax,
                        'field' => 'term_id',
                        'terms' => $access_terms,
                    ),
                );

                $query->set('tax_query', $tax_query);
            }
        }

        return $query;
    }

    /**
     * Query unassigned posts in folder
     *
     * @param object $query Params use to query post
     *
     * @return object $query
     */
    public function filterPostsByUnassignedFolder($query)
    {
        if (!is_admin() || !$query->is_main_query() || !isset($query->query_vars['post_type'])) {
            return $query;
        }

        global $typenow;
        $tax = 'wpmf-folder-type-'.$typenow;
        // phpcs:ignore
        if (self::isActiveForScreen() && isset($_GET[$tax]) && $_GET[$tax] == 'wpmf-unassigned') {
            if (isset($query->query_vars['tax_query'])) {
                $tax_query = $query->query_vars['tax_query'];
                $tax_query = array_filter($tax_query, function ($tax) {
                    if (isset($tax['taxonomy']) && $tax['taxonomy'] === $tax) {
                        return false;
                    }
                    return true;
                });

                $query->set('tax_query', $tax_query);
            }

            if (isset($query->query_vars[$tax])) {
                $query->set($tax, '');
            }
            
            $tax_query = array(
                array(
                    'taxonomy' => $tax,
                    'field'    => 'term_id',
                    'terms'    => array(),
                    'operator' => 'NOT EXISTS'
                ),
            );
            $query->set('tax_query', $tax_query);
            $query->set('post_status', array('publish', 'draft', 'future', 'private', 'pending'));
        }
    }

    /**
     * Filter input data
     *
     * @param string $var The name of the variable to retrieve from the request
     *
     * @return mixed
     */
    public function getRequestVar($var)
    {
        $response = filter_input(INPUT_POST, $var);
        if (empty($response)) {
            $response = filter_input(INPUT_GET, $var);
        }

        return $response;
    }

    /**
     * Sanitize input options
     *
     * @param mixed  $value The value to be sanitized
     * @param string $type  The type of sanitization to apply ('int', 'email', or default to text).
     *
     * @return mixed
     */
    public static function sanitizeOptions($value, $type = '')
    {
        $value = stripslashes($value);
        if ($type == 'int') {
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        } elseif ($type == 'email') {
            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        } else {
            $value = sanitize_text_field($value);
        }

        return $value;
    }

    /**
     * Add Checkbox to table head for post types
     *
     * @param array  $defaults An array of default columns in the table head
     * @param string $d        A placeholder parameter with no current use
     *
     * @return array
     */
    public function manageColumnsHead($defaults, $d = '')
    {
        global $typenow;
        $type   = $typenow;
        $action = $this->getRequestVar('action');
        if ($action == 'inline-save') {
            $post_type = $this->getRequestVar('post_type');
            $type      = self::sanitizeOptions($post_type);
        }

        $options = wpmfGetOption('wpmf_active_folders_post_types');
        if (is_array($options) && in_array($type, $options)) {
            $columns = ([
                'wpmf_folder_move' => '<div class="wpmf-folder-move-multiple wpmf-folder-col" title="'.esc_html__('Move selected items', 'wpmf').'"><span class="dashicons dashicons-move"></span><div class="wpmf-folder-items"><input type="hidden" value=""></div></div>',
            ] + $defaults);
            return $columns;
        }

        return $defaults;
    }

    /**
     * Add Checkbox to table body content for page, post, attachments
     *
     * @param string  $column_name The name of the current column
     * @param integer $post_ID     The ID of the current post
     *
     * @return void
     */
    public function manageColumnsContent($column_name, $post_ID)
    {
        $postIDs = self::$postIds;
        if (!is_array($postIDs)) {
            $postIDs = [];
        }

        if (!in_array($post_ID, $postIDs)) {
            $postIDs[]     = $post_ID;
            self::$postIds = $postIDs;
            global $typenow;
            $type   = $typenow;
            $action = $this->getRequestVar('action');
            if ($action == 'inline-save') {
                $post_type = $this->getRequestVar('post_type');
                $type      = self::sanitizeOptions($post_type);
            }

            $options = wpmfGetOption('wpmf_active_folders_post_types');
            if (is_array($options) && in_array($type, $options)) {
                if ($column_name == 'wpmf_folder_move') {
                    $title = get_the_title();
                    if (strlen($title) > 20) {
                        $title = substr($title, 0, 20).'...';
                    }

                    echo "<div class='wpmf-folder-move-file' data-id='".esc_attr($post_ID)."'><span class='wpmf-folder-move dashicons dashicons-move' data-id='".esc_attr($post_ID)."'></span><span class='wpmf-folder-item' data-object-id='".esc_attr($post_ID)."'>".esc_attr($title).'</span></div>';
                }
            }
        }
    }

    /**
     * Get children categories
     *
     * @param integer $id_parent Parent of attachment
     * @param array   $lists     List childrens folder
     * @param string  $taxonomy  Taxonomy
     *
     * @return array
     */
    public function getFolderChild($id_parent, $lists, $taxonomy)
    {
        if (empty($lists)) {
            $lists = array();
        }
        $folder_childs = get_categories(
            array(
                'taxonomy'   => $taxonomy,
                'parent'     => (int) $id_parent,
                'hide_empty' => false
            )
        );
        if (count($folder_childs) > 0) {
            foreach ($folder_childs as $child) {
                $lists[] = $child->term_id;
                $lists   = $this->getFolderChild($child->term_id, $lists, $taxonomy);
            }
        }

        return $lists;
    }

    /**
     * Load styles
     *
     * @param string $post_type Post type name
     *
     * @return void
     */
    public function loadAssets($post_type)
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-droppable');
        wp_enqueue_style(
            'wpmf-material-icon',
            plugins_url('/assets/css/google-material-icon.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_style(
            'wpmf-jaofiletree',
            plugins_url('/assets/css/jaofiletree.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_style(
            'wpmf-style',
            plugins_url('/assets/css/style.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_style(
            'wpmf-mdl',
            plugins_url('/assets/css/modal-dialog/mdl-jquery-modal-dialog.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_style(
            'wpmf-deep_orange',
            plugins_url('/assets/css/modal-dialog/material.deep_orange-amber.min.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_style(
            'wpmf-style-tippy',
            plugins_url('/assets/js/tippy/tippy.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_script(
            'wpmf-material',
            plugins_url('/assets/js/modal-dialog/material.min.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );

        wp_enqueue_script(
            'wpmf-mdl',
            plugins_url('/assets/js/modal-dialog/mdl-jquery-modal-dialog.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );

        wp_enqueue_style(
            'wpmf-gallery-popup-style',
            plugins_url('/assets/css/display-gallery/magnific-popup.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_script(
            'wpmf-gallery-popup',
            plugins_url('/assets/js/display-gallery/jquery.magnific-popup.min.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION,
            true
        );

        wp_register_script(
            'wpmf-folder-post-type',
            plugins_url('/assets/js/folder-post-type.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );

        wp_enqueue_script('wpmf-folder-post-type');

        wp_enqueue_script(
            'wpmf-scrollbar',
            plugins_url('/assets/js/scrollbar/jquery.scrollbar.min.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );
        wp_enqueue_style(
            'wpmf-scrollbar',
            plugins_url('/assets/js/scrollbar/jquery.scrollbar.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_register_script(
            'wpmf-folder-post-type-tree',
            plugins_url('/assets/js/folder-tree-for-post-type.js', dirname(__FILE__)),
            array('wpmf-folder-post-type'),
            WPMF_VERSION
        );
        wp_enqueue_script('wpmf-folder-post-type-tree');

        wp_register_script(
            'wpmf-folder-snackbar',
            plugins_url('/assets/js/snackbar.js', dirname(__FILE__)),
            array('wpmf-folder-post-type'),
            WPMF_VERSION
        );
        wp_enqueue_script('wpmf-folder-snackbar');

        wp_enqueue_script(
            'wpmf-tippy-core',
            plugins_url('/assets/js/tippy/tippy-core.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );

        wp_enqueue_script(
            'wpmf-tippy',
            plugins_url('/assets/js/tippy/tippy.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );

        $params = $this->localizeScript($post_type);
        wp_localize_script('wpmf-folder-post-type', 'wpmf', $params);
        wp_enqueue_script('wplink');
        wp_enqueue_style('editor-buttons');
    }

    /**
     * Localize a script.
     * Works only if the script has already been added.
     *
     * @param string $post_type Post type name
     *
     * @return array
     */
    public function localizeScript($post_type)
    {
        global $pagenow, $wpdb, $typenow, $wp_roles, $current_user;
        $parse_url = parse_url(site_url());
        $host = md5($parse_url['host']);
        $hide_tree = wpmfGetOption('hide_tree');

        /**
         * Filter to set limit of the folder number loaded
         *
         * @param integer Limit folder number
         *
         * @return integer
         */
        $limit_folders_number = apply_filters('wpmf_limit_folders', 99999);

        $enable_folders = wpmfGetOption('enable_folders');
        $show_folder_id = wpmfGetOption('show_folder_id');
        // get colors folder option
        $colors_option = wpmfGetOption('folder_color');
        // get folder tree status option
        $minimize_folder_tree_post_type = wpmfGetOption('wpmf_minimize_folder_tree_post_type');
        $folder_tree_status_option = wpmfGetOption('wpmf_folder_tree_status');

        $admin_url = get_admin_url(null, basename($_SERVER['REQUEST_URI']));
        $getData = filter_input_array(INPUT_GET);
        if (is_null($getData)) {
            $admin_url = admin_url('edit.php?post_type='.$typenow);
        }

        // phpcs:ignore
        if (!isset($_GET['wpmf-folder-type-'.$post_type])) {
            $admin_url .= '&wpmf-folder-type-'.esc_attr($post_type).'=';
            $post_value  = filter_input(INPUT_GET, $post_type);
            if (!empty($post_value)) {
                $admin_url .= esc_attr($post_value);
            }
        }
        $current_url = $admin_url;

        $tax = 'wpmf-folder-type-'.$post_type;
        $terms    = self::getTermsHierarchical($tax);

        if (!empty($current_user->ID)) {
            $enable_permissions_settings = ((isset($current_user->allcaps['wpmf_enable_permissions_settings']) && $current_user->allcaps['wpmf_enable_permissions_settings']) || in_array('administrator', $current_user->roles));
        }
        
        $current_role = WpmfHelper::getRoles(get_current_user_id());

        $roles = $wp_roles->roles;
        $role_in = array();
        foreach ($roles as $role_name => $r) {
            if (isset($r['capabilities']['upload_files']) && $r['capabilities']['upload_files']) {
                $role_in[] = $role_name;
            }
        }
        unset($roles['administrator']);

        $args = array (
            'role__in' => $role_in,
            'order' => 'ASC',
            'orderby' => 'display_name',
            'fields' => array('ID', 'display_name')
        );
        $wp_user_query = new WP_User_Query($args);
        $users = $wp_user_query->get_results();

        $lastAccessFolder = 0;
        $current_term_id = get_queried_object_id();
        if (isset($_COOKIE['lastAccessFolder_'.$host]) && is_int($current_term_id)) {
            $lastAccessFolder = $current_term_id;
            // phpcs:ignore
            if (isset($_GET[$tax]) && $_GET[$tax] == 'wpmf-unassigned') {
                $lastAccessFolder = -1;
            }
        }

        $l18n            = $this->translation();
        $vars            = array(
            'site_url'              => site_url(),
            'current_url'           => $current_url,
            'host'                  => $host,
            'taxo'                  => $tax,
            'post_type'             => $post_type,
            'ajaxurl'               => admin_url('admin-ajax.php'),
            'colors'                => $colors_option,
            'hide_tree'             => $hide_tree,
            'minimize_folder_tree'  => $minimize_folder_tree_post_type,
            'folder_tree_status'    => $folder_tree_status_option,
            'limit_folders_number'  => $limit_folders_number,
            'enable_folders'        => $enable_folders,
            'show_folder_id'        => ((int) $show_folder_id === 1) ? true : false,
            'wpmf_categories'       => $terms,
            'wpmf_addon_active'     => (is_plugin_active('wp-media-folder-addon/wp-media-folder-addon.php')) ? 1 : 0,
            'wpmf_nonce'            => wp_create_nonce('wpmf_nonce'),
            'wpmf_pagenow'          => $pagenow,
            'wpmf_post_type'        => 'wpmf-folder-post-type',
            'wpmf_role'             => $current_role,
            'roles'                 => $roles,
            'users'                 => $users,
            'enable_permissions_settings' => $enable_permissions_settings,
            'lastAccessFolder'      => $lastAccessFolder
        );

        return array('l18n' => $l18n, 'vars' => $vars);
    }

    /**
     * Get translation string
     *
     * @return array
     */
    public function translation()
    {
        $l18n = array(
            'bulk_select'           => __('Bulk select folders', 'wpmf'),
            'view_folder'           => __('View folder', 'wpmf'),
            'add_folder'            => __('Add Folder', 'wpmf'),
            'update_folder'         => __('Update folder', 'wpmf'),
            'remove_folder'         => __('Remove folder', 'wpmf'),
            'inherit_folder'        => __('Inherit permissions', 'wpmf'),
            'add_media'             => __('Add item', 'wpmf'),
            'view_media'            => __('View item', 'wpmf'),
            'move_media'            => __('Move item', 'wpmf'),
            'remove_media'          => __('Remove item', 'wpmf'),
            'update_media'          => __('Update item', 'wpmf'),
            'permissions_list_of'   => __('Permission lists of', 'wpmf'),
            'folder'                => __('folder', 'wpmf'),
            'user'                  => __('User', 'wpmf'),
            'role'                  => __('Role', 'wpmf'),
            'add_role'              => __('Add another role permission', 'wpmf'),
            'add_user'              => __('Add another user permission', 'wpmf'),
            'change_folder'         => __('Move to / Multi folders', 'wpmf'),
            'create_folder'         => __('Add new folder', 'wpmf'),
            'load_more'             => __('Load More', 'wpmf'),
            'refresh'               => __('Refresh', 'wpmf'),
            'new_folder'            => __('New Folder', 'wpmf'),
            'media_folder'          => __('Media Library', 'wpmf'),
            'promt'                 => __('New folder name:', 'wpmf'),
            'edit_file_lb'          => __('Please enter a new name for the item:', 'wpmf'),
            'edit_media'            => __('Edit media', 'wpmf'),
            'title_media'           => __('Title', 'wpmf'),
            'caption_media'         => __('Caption', 'wpmf'),
            'alt_media'             => __('Alternative Text', 'wpmf'),
            'desc_media'            => __('Description', 'wpmf'),
            'new_folder_tree'       => __('NEW FOLDER', 'wpmf'),
            'alert_add'             => __('A folder already exists here with the same name. Please try with another name, thanks :)', 'wpmf'),
            'alert_delete_file'     => __('Are you sure to want to delete this file?', 'wpmf'),
            'update_file_msg'       => __('Update failed. Please try with another name, thanks :)', 'wpmf'),
            'alert_delete'          => __('Are you sure to want to delete this folder?', 'wpmf'),
            'delete_multiple_folder'=> __('Are you sure to want to delete %d folder? Note that some folders contain subfolders or files.', 'wpmf'),
            'alert_delete_all'      => __('Are you sure to want to delete this folder? Note that this folder contain subfolders or files.', 'wpmf'),
            'alert_delete1'         => __('This folder contains media and/or subfolders, please delete them before or activate the setting that allows to remove a folder with its media', 'wpmf'),
            'display_own_media'     => __('Display only my own medias', 'wpmf'),
            'create_gallery_folder' => __('Create a gallery from folder', 'wpmf'),
            'home'                  => __('Media Library', 'wpmf'),
            'youarehere'            => __('You are here', 'wpmf'),
            'back'                  => __('Back', 'wpmf'),
            'dragdrop'              => __('Drag and Drop me hover a folder', 'wpmf'),
            'pdf'                   => __('PDF', 'wpmf'),
            'other'                 => __('Other', 'wpmf'),
            'link_to'               => __('Link To', 'wpmf'),
            'duplicate_text'        => __('Duplicate', 'wpmf'),
            'wpmf_undo'             => __('Undo.', 'wpmf'),
            'wpmf_undo_remove'      => __('Folder removed.', 'wpmf'),
            'wpmf_undo_movefolder'  => __('Moved a folder.', 'wpmf'),
            'wpmf_undo_editfolder'  => __('Folder name updated', 'wpmf'),
            'cancel'                => __('Cancel', 'wpmf'),
            'create'                => __('Create', 'wpmf'),
            'save'                  => __('Save', 'wpmf'),
            'save_close'            => __('Save and close', 'wpmf'),
            'ok'                    => __('OK', 'wpmf'),
            'delete'                => __('Delete', 'wpmf'),
            'remove'                => __('Remove', 'wpmf'),
            'edit_folder'           => __('Rename', 'wpmf'),
            'proceed'               => __('proceed', 'wpmf'),
            'copy_folder_id'        => __('Copy folder ID: ', 'wpmf'),
            'copy_folderID_msg'     => __('Folder ID copied to clipboard', 'wpmf'),
            'change_color'          => __('Change color', 'wpmf'),
            'permissions_setting'   => __('Permissions settings', 'wpmf'),
            'information'           => __('Information', 'wpmf'),
            'clear_filters'         => __('Clear filters and sorting', 'wpmf'),
            'label_filter_order'    => __('Filter or order media', 'wpmf'),
            'label_remove_filter'   => __('Remove all filters', 'wpmf'),
            'wpmf_addfolder'        => __('Folder added', 'wpmf'),
            'wpmf_folder_adding'    => __('Adding folder...', 'wpmf'),
            'wpmf_folder_deleting'  => __('Removing folder...', 'wpmf'),
            'folder_editing'        => __('Editing folder...', 'wpmf'),
            'folder_moving'         => __('Moving folder...', 'wpmf'),
            'folder_moving_text'    => __('Moving folder', 'wpmf'),
            'moving'                => __('Moving', 'wpmf'),
            'label_apply'           => __('Apply', 'wpmf'),
            'folder_selection'      => __('New folder selection applied to media', 'wpmf'),
            'select_folder_label'   => __('Select folder:', 'wpmf'),
            'filter_label'          => __('Filtering', 'wpmf'),
            'sort_label'            => __('Sorting', 'wpmf'),
            'order_folder'          => array(
                'name-ASC'  => __('Name (Ascending)', 'wpmf'),
                'name-DESC' => __('Name (Descending)', 'wpmf'),
                'id-ASC'    => __('ID (Ascending)', 'wpmf'),
                'id-DESC'   => __('ID (Descending)', 'wpmf'),
                'custom'    => __('Custom order', 'wpmf'),
            ),
            'order_media'           => array(
                'date|asc'      => __('Date (Ascending)', 'wpmf'),
                'date|desc'     => __('Date (Descending)', 'wpmf'),
                'title|asc'     => __('Title (Ascending)', 'wpmf'),
                'title|desc'    => __('Title (Descending)', 'wpmf'),
                'size|asc'      => __('Size (Ascending)', 'wpmf'),
                'size|desc'     => __('Size (Descending)', 'wpmf'),
                'filetype|asc'  => __('File type (Ascending)', 'wpmf'),
                'filetype|desc' => __('File type (Descending)', 'wpmf'),
                'custom'        => __('Custom order', 'wpmf'),
            ),
            'colorlists'            => array(
                '#ac725e' => __('Chocolate ice cream', 'wpmf'),
                '#d06b64' => __('Old brick red', 'wpmf'),
                '#f83a22' => __('Cardinal', 'wpmf'),
                '#fa573c' => __('Wild strawberries', 'wpmf'),
                '#ff7537' => __('Mars orange', 'wpmf'),
                '#ffad46' => __('Yellow cab', 'wpmf'),
                '#42d692' => __('Spearmint', 'wpmf'),
                '#16a765' => __('Vern fern', 'wpmf'),
                '#7bd148' => __('Asparagus', 'wpmf'),
                '#b3dc6c' => __('Slime green', 'wpmf'),
                '#fbe983' => __('Desert sand', 'wpmf'),
                '#fad165' => __('Macaroni', 'wpmf'),
                '#92e1c0' => __('Sea foam', 'wpmf'),
                '#9fe1e7' => __('Pool', 'wpmf'),
                '#9fc6e7' => __('Denim', 'wpmf'),
                '#4986e7' => __('Rainy sky', 'wpmf'),
                '#9a9cff' => __('Blue velvet', 'wpmf'),
                '#b99aff' => __('Purple dino', 'wpmf'),
                '#8f8f8f' => __('Mouse', 'wpmf'),
                '#cabdbf' => __('Mountain grey', 'wpmf'),
                '#cca6ac' => __('Earthworm', 'wpmf'),
                '#f691b2' => __('Bubble gum', 'wpmf'),
                '#cd74e6' => __('Purple rain', 'wpmf'),
                '#a47ae2' => __('Toy eggplant', 'wpmf'),
            ),
            'placegolder_color'     => __('Custom color #8f8f8f', 'wpmf'),
            'bgcolorerror'          => __('Change background folder has failed', 'wpmf'),
            'search_folder'         => __('Search folders...', 'wpmf'),
            'msg_upload_folder'     => __('You are uploading media to folder: ', 'wpmf'),
            'addon_ajax_button'     => __('Use ajax link', 'wpmf'),
            'orderby' => esc_html__('Order by', 'wpmf'),
            'custom' => esc_html__('Custom', 'wpmf'),
            'random' => esc_html__('Random', 'wpmf'),
            'title' => esc_html__('Title', 'wpmf'),
            'order' => esc_html__('Order', 'wpmf'),
            'ascending' => esc_html__('Ascending', 'wpmf'),
            'descending' => esc_html__('Descending', 'wpmf'),
            'update_with_new_folder' => esc_html__('Update with new folder content', 'wpmf'),
            'yes' => esc_html__('Yes', 'wpmf'),
            'no' => esc_html__('No', 'wpmf'),
            'caption' => esc_html__('Caption', 'wpmf'),
            'custom_link' => esc_html__('Custom link', 'wpmf'),
            'create_gallery' => esc_html__('CREATE GALLERY', 'wpmf'),
            'select_folder' => esc_html__('Select a folder', 'wpmf'),
            'search_no_result' => esc_html__('Sorry, no folder found', 'wpmf'),
            'select_item' => esc_html__('Select items to move', 'wpmf'),
            'select_this_item' => esc_html__('Drag this item into a folder', 'wpmf'),
            'item_selected' => esc_html__('Item selected', 'wpmf'),
            'items_selected' => esc_html__('Items selected', 'wpmf'),
            'by_role' => esc_html__('By Role', 'wpmf'),
            'by_user' => esc_html__('By User', 'wpmf'),
            'select_role' => esc_html__('Select a role', 'wpmf'),
            'select_user' => esc_html__('Select a user', 'wpmf'),
        );

        return $l18n;
    }
}
