<?php

/* 
 * @author Daniele Manassero <daniele.manassero@zirak.it>
 * @creation-date 06/06/2014
 */

class UserRestrictedBlogHolder extends BlogHolder
{
    
    private static $icon = "blog-users-permissions/images/blogholder-file.png";
    
    private static $allowed_children = array(
        'UserRestrictedBlogEntry'
    );
    
    /*
     * @return boolean True if the current user can edit/delete the blog
     */
    private function checkBlogHolderPermissions()
    {
        $authorId = DB::query('SELECT AuthorID FROM SiteTree_versions WHERE RecordID = '.$this->ID.' ORDER BY ID DESC LIMIT 1')->value();
        
        if (($authorId == Member::currentUser()->ID) || ($this->OwnerID == Member::currentUser()->ID) || (Permission::check('ADMIN'))) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * @return boolean True if the current user can edit this blog.
     * Only the Administrator can edit every blog, otherwise the user can edit only his blog.
     */
    public function canEdit($member = null)
    {
        return $this->checkBlogHolderPermissions();
    }
    
    /*
     * $user_groups = List of group IDs to which the member belongs
     * $page_groups = List of groups that can edit this page
     * 
     * @return boolean True if the current user can add posts in this blog.
     */
    public function canAddChildren($member = null)
    {
        if (!Permission::check('ADMIN')) {
            $page_permission = false;
            $user_groups = Permission::groupList(Member::currentUser()->ID);
            
            foreach ($this->EditorGroups() as $key => $group) {
                foreach ($user_groups as $user_group) {
                    if ($user_group == $group->ID) {
                        $page_permission = true;
                        break;
                    }
                }
            }

            if (Permission::check('BLOGMANAGEMENT') && $page_permission) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}


class UserRestrictedBlogHolder_Controller extends BlogHolder_Controller
{
    
    private static $allowed_actions = array(
        'index',
        'tag',
        'date',
        'metaweblog',
        'postblog' => 'BLOGMANAGEMENT',
        'post',
        'BlogEntryForm' => 'BLOGMANAGEMENT',
    );
    
    /*
     * This is an override of the BlogEntryForm() function in BlogHolder_Controller class
     * The function control if the user can modify a post in front-end
     */
    public function BlogEntryForm()
    {
        $postId = 0;
        if ($this->request->latestParam('ID')) {
            $postId = (int) $this->request->latestParam('ID');
        }
        
        /*
         * I add the ($postID == 0) control for manage the edit form submit 
         * in the front-end blog template
         */
        if ($this->canEditFrontEndBlogEntry($postId) || ($postId == 0)) {
            return parent::BlogEntryForm();
        }
        
        $page = DataObject::get_by_id('BlogEntry', $postId);
        if ($page) {
            $this->redirect($page->Link());
        }
    }
    
    /*
     * @param Post ID
     * @return boolean True if the current user can edit this post.
     */
    public function canEditFrontEndBlogEntry($postId)
    {
        $authorId = DB::query('SELECT AuthorID FROM SiteTree_versions WHERE RecordID = '.$postId.' ORDER BY ID DESC LIMIT 1')->value();
        
        if (($authorId == Member::currentUser()->ID) || ($this->OwnerID == Member::currentUser()->ID) || (Permission::check('ADMIN'))) {
            return true;
        } else {
            return false;
        }
    }
}
