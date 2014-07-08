<?php

/* 
 * @author Daniele Manassero <daniele.manassero@zirak.it>
 * @creation-date 06/06/2014
 */

class UserRestrictedBlogEntry extends BlogEntry {
    
    private static $allowed_children = array('UserRestrictedBlogEntry');
    
    private static $icon = "blog-users-permissions/images/blogpage-file.png";
    
    /*
     * This is an override of the populateDefaults() function in BlogEntry class
     * When a user create a new post, It populate the Author field with him username
     */
    public function populateDefaults(){
        parent::populateDefaults();
        
        if(Member::currentUser()){
            $username = Member::currentUser()->FirstName;
            
            if(Member::currentUser()->Surname){
                $username .= ' '.Member::currentUser()->Surname;
            }
            
            $this->owner->setField('Author', $username);
        }
    }
    
    /*
     * Add the attribute ReadOnly at the Author field in the BlogEntry form
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->makeFieldReadonly('Author');
        return $fields;
    }
    
    /*
     * @return boolean True if the current user can create a post.
     */
    function canCreate($member = null) {
        if(Permission::check('BLOGMANAGEMENT')){
            return true;
        }else{
            return false;
        }
    }
    
    /*
     * @return boolean True if the current user can create this post.
     */
    private function checkBlogEntryPermissions(){
        $authorsId = array();
        $sqlQuery = new SQLQuery();
        $sqlQuery->setFrom('SiteTree_versions');
        $sqlQuery->selectField('AuthorID');
        $sqlQuery->addWhere('RecordID = '.$this->ID);
        $sqlQuery->setOrderBy('ID DESC');
        $rawSQL = $sqlQuery->sql();
        $result = $sqlQuery->execute();
        foreach($result as $row) {
            $authorsId[] = $row['AuthorID'];
        }
        $sqlQuery->setDelete(true);
        
        
        if((in_array(Member::currentUser()->ID, $authorsId)) || ($this->parent->OwnerID == Member::currentUser()->ID) || (Permission::check('ADMIN'))){
            return true;
        }else{
            return false;
        }
    }
    
    /*
     * @return boolean True if the current user can edit this post.
     * Only the Administrator can edit every post, otherwise the user can edit only his posts.
     */
    function canEdit($member = null) {
        return $this->checkBlogEntryPermissions();
    }
    
    /*
     * @return boolean True if the current user can publish this post.
     * Only the Administrator can publish every post, otherwise the user can publish only his posts.
     */
    function canPublish($member = null) {
        return $this->checkBlogEntryPermissions();
    }
    
    /*
     * @return boolean True if the current user can delete this post.
     * Only the Administrator can delete every post, otherwise the user can delete only his posts.
     */
    function canDelete($member = null) {
        return $this->checkBlogEntryPermissions();
    }
    
}


class UserRestrictedBlogEntry_Controller extends BlogEntry_Controller {
    
    private static $allowed_actions = array(
        'index',
        'unpublishPost',
        'PageComments',
        'SearchForm'
    );
    
}
