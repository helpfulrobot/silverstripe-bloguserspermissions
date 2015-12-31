<?php

/* 
 * @author Daniele Manassero <daniele.manassero@zirak.it>
 * @creation-date 06/06/2014
 */

class UserRestrictedBlogTree extends BlogTree
{
    
    private static $allowed_children = array(
        'UserRestrictedBlogTree', 'UserRestrictedBlogHolder'
    );
    
    /*
     * 
     */
    private function checkBlogTreePermissions()
    {
        if (Permission::check('ADMIN')) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * @return boolean True if the current user is admin.
     * Only the Administrator can edit every tree
     */
    public function canEdit($member = null)
    {
        return $this->checkBlogTreePermissions();
    }
}
