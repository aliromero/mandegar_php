<?php

namespace App;

trait PageTemplates
{
    /*
    |--------------------------------------------------------------------------
    | Page Templates for Backpack\PageManager
    |--------------------------------------------------------------------------
    |
    | Each page template has its own method, that define what fields should show up using the Backpack\CRUD API.
    | Use snake_case for naming and PageManager will make sure it looks pretty in the create/update form
    | template dropdown.
    |
    | Any fields defined here will show up after the standard page fields:
    | - select template
    | - page name (only seen by admins)
    | - page title
    | - page slug
    */

    private function services()
    {
        $this->crud->addField([   // CustomHTML
                        'name' => 'metas_separator',
                        'type' => 'custom_html',
                        'value' => '<br><h2>ساختار صفحه</h2><hr>',
                    ]);
        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'عنوان',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
        $this->crud->addField([
                        'name' => 'meta_description',
                        'label' => 'توضیحات',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
        $this->crud->addField([
                        'name' => 'meta_keywords',
                        'type' => 'textarea',
                        'label' => 'کلمات کلیدی',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
        $this->crud->addField([   // CustomHTML
                        'name' => 'content_separator',
                        'type' => 'custom_html',
                        'value' => '<br><h2>محتوا</h2><hr>',
                    ]);
        $this->crud->addField([
                        'name' => 'content',
                        'label' => 'متن',
                        'type' => 'wysiwyg',
                        'placeholder' => 'Your content here',
                    ]);
    }

    private function about_us()
    {
        $this->crud->addField([
                        'name' => 'content',
                        'label' => 'متن',
                        'type' => 'wysiwyg',
                        'placeholder' => 'Your content here',
                    ]);
    }
}
