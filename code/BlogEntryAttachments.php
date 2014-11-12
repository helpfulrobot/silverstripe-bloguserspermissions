<?php

/* 
 * @author Daniele Manassero <daniele.manassero@zirak.it>
 * @creation-date 12/11/2014
 */

class BlogEntryAttachments extends File {
    private static $belongs_many_many = array(
		'UsrRestrictBlogEntry' => 'UserRestrictedBlogEntry'
	);
}