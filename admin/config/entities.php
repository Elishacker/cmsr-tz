<?php
/**
 * Every manageable content type, described once.
 *
 * admin/crud.php reads these definitions and builds the list screen, the
 * create/edit form, validation and the delete action from them — so adding
 * a new managed table is a matter of adding one entry here.
 *
 * Field types:
 *   text, textarea, html, number, select, checkbox, date, email, url,
 *   image (media picker + upload), file (upload), slug, password
 */

declare(strict_types=1);

function entity_definitions(): array
{
    return [

        // -------------------------------------------------------------
        'slideshow' => [
            'label'        => 'Hero slide',
            'plural'       => 'Hero slides',
            'table'        => 'slideshow',
            'icon'         => 'bi-images',
            'description'  => 'The rotating banner at the top of the homepage.',
            'order'        => 'sort_order ASC, id ASC',
            'list_columns' => ['image' => 'Picture', 'heading' => 'Heading', 'eyebrow' => 'Sector', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['heading', 'eyebrow', 'description'],
            'fields' => [
                'image'       => ['type' => 'image', 'label' => 'Slide picture', 'required' => true, 'hint' => 'Wide landscape pictures work best (at least 1600px across).'],
                'eyebrow'     => ['type' => 'text', 'label' => 'Small label above the heading'],
                'heading'     => ['type' => 'text', 'label' => 'Heading', 'required' => true],
                'description' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 4],
                'btn1_text'   => ['type' => 'text', 'label' => 'Button 1 label', 'col' => 6],
                'btn1_link'   => ['type' => 'text', 'label' => 'Button 1 link', 'col' => 6, 'hint' => 'e.g. sector.php?slug=health'],
                'btn2_text'   => ['type' => 'text', 'label' => 'Button 2 label', 'col' => 6],
                'btn2_link'   => ['type' => 'text', 'label' => 'Button 2 link', 'col' => 6],
                'sort_order'  => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'   => ['type' => 'checkbox', 'label' => 'Show this slide on the website', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'impact_stats' => [
            'label'        => 'Impact counter',
            'plural'       => 'Impact counters',
            'table'        => 'impact_stats',
            'icon'         => 'bi-graph-up',
            'description'  => 'The four figures shown on the dark strip of the homepage and the About page.',
            'order'        => 'sort_order ASC, id ASC',
            'list_columns' => ['label' => 'Label', 'value' => 'Value', 'suffix' => 'Suffix', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['label'],
            'fields' => [
                'icon'       => ['type' => 'text', 'label' => 'Icon', 'default' => 'bi-graph-up', 'hint' => 'Bootstrap Icons name, e.g. bi-people', 'col' => 6],
                'value'      => ['type' => 'text', 'label' => 'Figure', 'required' => true, 'col' => 3],
                'suffix'     => ['type' => 'text', 'label' => 'Suffix', 'col' => 3, 'hint' => 'e.g. + or K+'],
                'label'      => ['type' => 'text', 'label' => 'Caption', 'required' => true],
                'sort_order' => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'  => ['type' => 'checkbox', 'label' => 'Show this counter', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'sectors' => [
            'label'        => 'Sector',
            'plural'       => 'Sectors',
            'table'        => 'sectors',
            'icon'         => 'bi-diagram-3',
            'description'  => 'The programme areas shown under “What We Do”. Each sector has its own page.',
            'order'        => 'sort_order ASC, id ASC',
            'list_columns' => ['image' => 'Picture', 'name' => 'Sector', 'slug' => 'Address', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['name', 'summary', 'tagline'],
            'view_url'     => 'sector.php?slug={slug}',
            'fields' => [
                'name'       => ['type' => 'text', 'label' => 'Sector name', 'required' => true, 'col' => 6],
                'slug'       => ['type' => 'slug', 'label' => 'Web address', 'from' => 'name', 'col' => 6, 'hint' => 'Used in the page link — change with care.'],
                'tagline'    => ['type' => 'text', 'label' => 'Tagline'],
                'icon'       => ['type' => 'text', 'label' => 'Icon', 'default' => 'bi-hearts', 'col' => 6, 'hint' => 'Bootstrap Icons name.'],
                'sort_order' => ['type' => 'number', 'label' => 'Display order', 'col' => 3, 'default' => 0],
                'is_active'  => ['type' => 'checkbox', 'label' => 'Published', 'col' => 3, 'default' => 1],
                'image'      => ['type' => 'image', 'label' => 'Sector picture'],
                'summary'    => ['type' => 'textarea', 'label' => 'Card summary', 'rows' => 3, 'hint' => 'Shown on the homepage and sector cards.'],
                'intro'      => ['type' => 'textarea', 'label' => 'Opening paragraph', 'rows' => 3],
                'body'       => ['type' => 'html', 'label' => 'Full page content', 'rows' => 16],
                'highlights' => ['type' => 'textarea', 'label' => 'What we deliver', 'rows' => 6, 'hint' => 'One bullet per line.'],
            ],
        ],

        // -------------------------------------------------------------
        'projects' => [
            'label'        => 'Project',
            'plural'       => 'Projects',
            'table'        => 'projects',
            'icon'         => 'bi-kanban',
            'description'  => 'Current and past projects and programmes.',
            'order'        => 'FIELD(status, "current", "upcoming", "completed"), sort_order ASC, id ASC',
            'list_columns' => ['image' => 'Picture', 'title' => 'Project', 'category' => 'Sector', 'status' => 'Status', 'duration' => 'Period', 'is_active' => 'Live'],
            'search'       => ['title', 'location', 'donor', 'summary'],
            'view_url'     => 'project.php?slug={slug}',
            'filters'      => [
                'status' => ['label' => 'Status', 'options' => ['current' => 'On-going', 'completed' => 'Completed', 'upcoming' => 'Upcoming']],
            ],
            'fields' => [
                'title'                  => ['type' => 'text', 'label' => 'Project title', 'required' => true],
                'slug'                   => ['type' => 'slug', 'label' => 'Web address', 'from' => 'title', 'col' => 6],
                'sector_id'              => ['type' => 'select', 'label' => 'Sector', 'col' => 6, 'options_sql' => 'SELECT id, name FROM sectors ORDER BY sort_order', 'empty' => '— none —'],
                'category'               => ['type' => 'text', 'label' => 'Category label', 'col' => 6, 'hint' => 'Shown when no sector is linked.'],
                'status'                 => ['type' => 'select', 'label' => 'Status', 'col' => 6, 'options' => ['current' => 'On-going', 'completed' => 'Completed', 'upcoming' => 'Upcoming'], 'default' => 'current'],
                'image'                  => ['type' => 'image', 'label' => 'Main picture'],
                'summary'                => ['type' => 'textarea', 'label' => 'Short summary', 'rows' => 3],
                'body'                   => ['type' => 'html', 'label' => 'Full description', 'rows' => 16],
                'location'               => ['type' => 'text', 'label' => 'Location', 'col' => 6],
                'donor'                  => ['type' => 'text', 'label' => 'Donor / development partner', 'col' => 6],
                'duration'               => ['type' => 'text', 'label' => 'Duration', 'col' => 4, 'hint' => 'e.g. 2 years (2024-2025)'],
                'start_year'             => ['type' => 'text', 'label' => 'Start year', 'col' => 4],
                'end_year'               => ['type' => 'text', 'label' => 'End year', 'col' => 4],
                'beneficiaries_direct'   => ['type' => 'text', 'label' => 'Direct beneficiaries', 'col' => 6],
                'beneficiaries_indirect' => ['type' => 'text', 'label' => 'Indirect beneficiaries', 'col' => 6],
                'is_featured'            => ['type' => 'checkbox', 'label' => 'Feature on the homepage', 'col' => 4],
                'is_active'              => ['type' => 'checkbox', 'label' => 'Published', 'col' => 4, 'default' => 1],
                'sort_order'             => ['type' => 'number', 'label' => 'Display order', 'col' => 4, 'default' => 0],
            ],
        ],

        // -------------------------------------------------------------
        'project_gallery' => [
            'label'        => 'Project photo',
            'plural'       => 'Project photos',
            'table'        => 'project_gallery',
            'icon'         => 'bi-image',
            'description'  => 'Extra pictures shown on a project page.',
            'order'        => 'project_id ASC, sort_order ASC',
            'list_columns' => ['image' => 'Picture', 'caption' => 'Caption', 'project_id' => 'Project', 'sort_order' => 'Order'],
            'search'       => ['caption'],
            'labels'       => ['project_id' => ['sql' => 'SELECT id, title FROM projects']],
            'fields' => [
                'project_id' => ['type' => 'select', 'label' => 'Project', 'required' => true, 'options_sql' => 'SELECT id, title FROM projects ORDER BY title'],
                'image'      => ['type' => 'image', 'label' => 'Picture', 'required' => true],
                'caption'    => ['type' => 'text', 'label' => 'Caption'],
                'sort_order' => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
            ],
        ],

        // -------------------------------------------------------------
        'news' => [
            'label'        => 'News article',
            'plural'       => 'News articles',
            'table'        => 'news',
            'icon'         => 'bi-newspaper',
            'description'  => 'Articles shown under “Latest”.',
            'order'        => 'news_date DESC, id DESC',
            'list_columns' => ['image' => 'Picture', 'title' => 'Headline', 'category' => 'Category', 'news_date' => 'Date', 'views' => 'Views', 'is_published' => 'Live'],
            'search'       => ['title', 'excerpt', 'body', 'tags'],
            'view_url'     => 'news-detail.php?slug={slug}',
            'fields' => [
                'title'        => ['type' => 'text', 'label' => 'Headline', 'required' => true],
                'slug'         => ['type' => 'slug', 'label' => 'Web address', 'from' => 'title', 'col' => 6],
                'news_date'    => ['type' => 'date', 'label' => 'Publication date', 'required' => true, 'col' => 6, 'default' => 'today'],
                'image'        => ['type' => 'image', 'label' => 'Article picture'],
                'category'     => ['type' => 'text', 'label' => 'Category', 'col' => 4, 'default' => 'News'],
                'author'       => ['type' => 'text', 'label' => 'Author', 'col' => 4, 'default' => 'CMSR-TZ'],
                'tags'         => ['type' => 'text', 'label' => 'Tags', 'col' => 4, 'hint' => 'Comma separated.'],
                'excerpt'      => ['type' => 'textarea', 'label' => 'Summary', 'rows' => 3],
                'body'         => ['type' => 'html', 'label' => 'Article body', 'rows' => 16],
                'is_published' => ['type' => 'checkbox', 'label' => 'Published', 'col' => 6, 'default' => 1],
                'is_featured'  => ['type' => 'checkbox', 'label' => 'Featured', 'col' => 6],
            ],
        ],

        // -------------------------------------------------------------
        'updates' => [
            'label'        => 'Short update',
            'plural'       => 'Short updates',
            'table'        => 'updates',
            'icon'         => 'bi-megaphone',
            'description'  => 'One-paragraph announcements shown in the “In brief” panels.',
            'order'        => 'update_date DESC, sort_order ASC',
            'list_columns' => ['title' => 'Title', 'update_date' => 'Date', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['title', 'body'],
            'fields' => [
                'title'       => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'update_date' => ['type' => 'date', 'label' => 'Date', 'required' => true, 'col' => 6, 'default' => 'today'],
                'link'        => ['type' => 'text', 'label' => 'Optional link', 'col' => 6],
                'body'        => ['type' => 'textarea', 'label' => 'Text', 'rows' => 4],
                'sort_order'  => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'   => ['type' => 'checkbox', 'label' => 'Show this update', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'pages' => [
            'label'        => 'Page',
            'plural'       => 'Static pages',
            'table'        => 'pages',
            'icon'         => 'bi-file-text',
            'description'  => 'The written content of About Us, How We Work, Vision & Mission, Support Us and Contact.',
            'order'        => 'id ASC',
            'list_columns' => ['title' => 'Page', 'page_key' => 'Key', 'subtitle' => 'Subtitle', 'is_active' => 'Live'],
            'search'       => ['title', 'body'],
            'can_create'   => false,
            'can_delete'   => false,
            'fields' => [
                'page_key'         => ['type' => 'text', 'label' => 'Page key', 'readonly' => true, 'col' => 6, 'hint' => 'Used by the code — do not change.'],
                'title'            => ['type' => 'text', 'label' => 'Page title', 'required' => true, 'col' => 6],
                'subtitle'         => ['type' => 'text', 'label' => 'Subtitle under the title'],
                'hero_image'       => ['type' => 'image', 'label' => 'Banner picture'],
                'body'             => ['type' => 'html', 'label' => 'Page content', 'rows' => 18],
                'meta_description' => ['type' => 'textarea', 'label' => 'Search engine description', 'rows' => 2],
                'is_active'        => ['type' => 'checkbox', 'label' => 'Published', 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'page_sections' => [
            'label'        => 'Page block',
            'plural'       => 'Page blocks',
            'table'        => 'page_sections',
            'icon'         => 'bi-layout-text-window',
            'description'  => 'Small tiles attached to a page, such as the four highlights on About Us.',
            'order'        => 'page_key ASC, sort_order ASC',
            'list_columns' => ['heading' => 'Heading', 'page_key' => 'Page', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['heading', 'body'],
            'fields' => [
                'page_key'   => ['type' => 'select', 'label' => 'Belongs to page', 'required' => true, 'options_sql' => 'SELECT page_key, title FROM pages ORDER BY id'],
                'heading'    => ['type' => 'text', 'label' => 'Heading', 'required' => true, 'col' => 6],
                'icon'       => ['type' => 'text', 'label' => 'Icon', 'col' => 6, 'hint' => 'A Bootstrap Icons name (bi-people), or the path to a picture for full-colour artwork (assets/images/icons/vision-eye.svg).'],
                'subheading' => ['type' => 'text', 'label' => 'Subheading'],
                'image'      => ['type' => 'image', 'label' => 'Picture (optional)'],
                'body'       => ['type' => 'textarea', 'label' => 'Text', 'rows' => 4],
                'sort_order' => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'  => ['type' => 'checkbox', 'label' => 'Show this block', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'board_members' => [
            'label'        => 'Board member',
            'plural'       => 'Board members',
            'table'        => 'board_members',
            'icon'         => 'bi-people',
            'description'  => 'The governing body of CMSR-TZ.',
            'order'        => 'sort_order ASC, id ASC',
            'list_columns' => ['photo' => 'Photo', 'name' => 'Name', 'position' => 'Position', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['name', 'position'],
            'fields' => [
                'name'       => ['type' => 'text', 'label' => 'Full name', 'required' => true, 'col' => 6],
                'position'   => ['type' => 'text', 'label' => 'Position', 'col' => 6, 'default' => 'Board Member'],
                'photo'      => ['type' => 'image', 'label' => 'Photograph', 'hint' => 'Leave empty to show the member’s initials.'],
                'bio'        => ['type' => 'textarea', 'label' => 'Short biography', 'rows' => 4],
                'sort_order' => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'  => ['type' => 'checkbox', 'label' => 'Show on the website', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'staff' => [
            'label'        => 'Team member',
            'plural'       => 'Leadership team',
            'table'        => 'staff',
            'icon'         => 'bi-person-badge',
            'description'  => 'The management team shown on the Board & Leadership page.',
            'order'        => 'sort_order ASC, id ASC',
            'list_columns' => ['photo' => 'Photo', 'name' => 'Name', 'position' => 'Position', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['name', 'position'],
            'fields' => [
                'name'       => ['type' => 'text', 'label' => 'Full name', 'required' => true, 'col' => 6],
                'position'   => ['type' => 'text', 'label' => 'Position', 'col' => 6],
                'photo'      => ['type' => 'image', 'label' => 'Photograph'],
                'bio'        => ['type' => 'textarea', 'label' => 'Short biography', 'rows' => 4],
                'email'      => ['type' => 'email', 'label' => 'E-mail', 'col' => 6],
                'sort_order' => ['type' => 'number', 'label' => 'Display order', 'col' => 3, 'default' => 0],
                'is_active'  => ['type' => 'checkbox', 'label' => 'Show', 'col' => 3, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'core_values' => [
            'label'        => 'Core value',
            'plural'       => 'Core values',
            'table'        => 'core_values',
            'icon'         => 'bi-award',
            'description'  => 'The values listed on the About Us page.',
            'order'        => 'sort_order ASC, id ASC',
            'list_columns' => ['title' => 'Value', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['title', 'description'],
            'fields' => [
                'title'       => ['type' => 'text', 'label' => 'Value', 'required' => true, 'col' => 6],
                'icon'        => ['type' => 'text', 'label' => 'Icon', 'col' => 6, 'default' => 'bi-check-circle'],
                'description' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 4],
                'sort_order'  => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'   => ['type' => 'checkbox', 'label' => 'Show', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'partners' => [
            'label'        => 'Partner',
            'plural'       => 'Partners & networks',
            'table'        => 'partners',
            'icon'         => 'bi-building',
            'description'  => 'Donors, implementing partners, government partners and the networks we belong to.',
            'order'        => 'sort_order ASC, id ASC',
            'list_columns' => ['logo' => 'Logo', 'name' => 'Name', 'type' => 'Type', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['name', 'short_name', 'description'],
            'filters'      => [
                'type' => ['label' => 'Type', 'options' => ['Donor' => 'Donor', 'Partner' => 'Partner', 'Government' => 'Government', 'Network' => 'Network']],
            ],
            'fields' => [
                'name'        => ['type' => 'text', 'label' => 'Full name', 'required' => true],
                'short_name'  => ['type' => 'text', 'label' => 'Short name / acronym', 'col' => 6],
                'type'        => ['type' => 'select', 'label' => 'Type', 'col' => 6, 'options' => ['Donor' => 'Donor', 'Partner' => 'Partner', 'Government' => 'Government', 'Network' => 'Network'], 'default' => 'Partner'],
                'logo'        => ['type' => 'image', 'label' => 'Logo'],
                'website'     => ['type' => 'url', 'label' => 'Website'],
                'description' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 4],
                'sort_order'  => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'   => ['type' => 'checkbox', 'label' => 'Show', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'locations' => [
            'label'        => 'Location',
            'plural'       => 'Where we work',
            'table'        => 'locations',
            'icon'         => 'bi-geo-alt',
            'description'  => 'Regions and districts listed on the “Where We Work” page.',
            'order'        => 'sort_order ASC, id ASC',
            'list_columns' => ['image' => 'Picture', 'region' => 'Region', 'districts' => 'Districts', 'sort_order' => 'Order', 'is_active' => 'Live'],
            'search'       => ['region', 'districts', 'description'],
            'fields' => [
                'region'      => ['type' => 'text', 'label' => 'Region', 'required' => true, 'col' => 6],
                'districts'   => ['type' => 'text', 'label' => 'Districts / places', 'col' => 6],
                'image'       => ['type' => 'image', 'label' => 'Picture'],
                'description' => ['type' => 'textarea', 'label' => 'What we do there', 'rows' => 5],
                'sort_order'  => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'   => ['type' => 'checkbox', 'label' => 'Show', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'resources' => [
            'label'        => 'Publication',
            'plural'       => 'Publications',
            'table'        => 'resources',
            'icon'         => 'bi-journal-text',
            'description'  => 'Documents offered for download on the Resources page.',
            'order'        => 'sort_order ASC, id ASC',
            'list_columns' => ['title' => 'Title', 'category' => 'Category', 'file_link' => 'File', 'is_active' => 'Live'],
            'search'       => ['title', 'description'],
            'fields' => [
                'title'       => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'category'    => ['type' => 'select', 'label' => 'Category', 'col' => 6, 'options' => ['Publication' => 'Publication', 'Policy' => 'Policy', 'Brochure' => 'Brochure', 'Report' => 'Report', 'Other' => 'Other'], 'default' => 'Publication'],
                'file_size'   => ['type' => 'text', 'label' => 'File size label', 'col' => 6, 'hint' => 'e.g. 2.4 MB'],
                'file_link'   => ['type' => 'file', 'label' => 'Document', 'hint' => 'PDF, Word or Excel. Leave empty to show a “Request a copy” link.'],
                'cover_image' => ['type' => 'image', 'label' => 'Cover picture (optional)'],
                'description' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 4],
                'sort_order'  => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'   => ['type' => 'checkbox', 'label' => 'Show', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'reports' => [
            'label'        => 'Annual report',
            'plural'       => 'Annual reports',
            'table'        => 'reports',
            'icon'         => 'bi-file-earmark-text',
            'description'  => 'The yearly implementation reports approved by the Board.',
            'order'        => 'year DESC, sort_order ASC',
            'list_columns' => ['title' => 'Title', 'year' => 'Year', 'file_link' => 'File', 'is_active' => 'Live'],
            'search'       => ['title', 'description'],
            'fields' => [
                'title'       => ['type' => 'text', 'label' => 'Title', 'required' => true, 'col' => 8],
                'year'        => ['type' => 'text', 'label' => 'Year', 'required' => true, 'col' => 4],
                'file_link'   => ['type' => 'file', 'label' => 'Report document'],
                'cover_image' => ['type' => 'image', 'label' => 'Cover picture (optional)'],
                'description' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 4],
                'sort_order'  => ['type' => 'number', 'label' => 'Display order', 'col' => 6, 'default' => 0],
                'is_active'   => ['type' => 'checkbox', 'label' => 'Show', 'col' => 6, 'default' => 1],
            ],
        ],

        // -------------------------------------------------------------
        'subscribers' => [
            'label'        => 'Subscriber',
            'plural'       => 'Newsletter subscribers',
            'table'        => 'subscribers',
            'icon'         => 'bi-envelope-check',
            'description'  => 'People who signed up for updates on the website.',
            'order'        => 'created_at DESC',
            'list_columns' => ['email' => 'E-mail', 'name' => 'Name', 'created_at' => 'Subscribed', 'is_active' => 'Active'],
            'search'       => ['email', 'name'],
            'can_create'   => false,
            'fields' => [
                'email'     => ['type' => 'email', 'label' => 'E-mail', 'required' => true, 'col' => 6],
                'name'      => ['type' => 'text', 'label' => 'Name', 'col' => 6],
                'is_active' => ['type' => 'checkbox', 'label' => 'Active', 'default' => 1],
            ],
        ],
    ];
}

/** One entity definition, or null when the key is unknown. */
function entity(string $key): ?array
{
    $all = entity_definitions();
    if (!isset($all[$key])) {
        return null;
    }
    return array_merge([
        'can_create' => true,
        'can_delete' => true,
        'search'     => [],
        'filters'    => [],
        'order'      => 'id DESC',
        'view_url'   => '',
        'labels'     => [],
    ], $all[$key], ['key' => $key]);
}
