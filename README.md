# Tomatillo Design Custom Authors

A WordPress plugin that replaces native author bylines with a flexible Contributor custom post type system, designed for external writers and integrated with Genesis Framework and Advanced Custom Fields Pro.

## Features

- **Custom Contributor CPT**: Manage external contributors with profile pages
- **Flexible Relationships**: Assign one or multiple contributors per post
- **Genesis Integration**: Automatically overrides Genesis entry meta with custom byline
- **Smart Snapshots**: Preserves contributor names even if unpublished or deleted
- **SEO Optimized**: Includes JSON-LD Article schema with proper author attribution
- **Admin Friendly**: Contributors column and filter in Posts list
- **Profile Pages**: Public contributor pages with bio, links, and post archive
- **Yak Theme Compatible**: Uses matching card markup for consistent styling

## Requirements

- WordPress 5.8+
- Genesis Framework 3.0+
- Advanced Custom Fields Pro 6.0+
- PHP 7.4+

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure Genesis Framework and ACF Pro are activated
4. Visit Settings > Permalinks and click "Save Changes" to flush rewrite rules

## Usage

### Adding Contributors

1. Navigate to **Contributors** in the WordPress admin
2. Click **Add New**
3. Enter contributor name (title)
4. Add full bio content in the editor
5. Upload a featured image (headshot)
6. Fill in custom fields:
   - Short Bio (for post footer panels)
   - Affiliation (optional)
   - Links (repeater field for external links)
7. Publish

### Assigning Contributors to Posts

1. Edit a post
2. In the sidebar, find the **Contributors** field
3. Search and select contributor(s)
4. Drag to reorder if multiple contributors
5. Save/update post

### Entry Meta Display

When contributors are assigned:
- **1 contributor**: "Date • By Contributor Name"
- **2 contributors**: "Date • By Name and Name"
- **3+ contributors**: "Date • By Name, Name, and Name" (Oxford comma)

### Contributor Profile Pages

- Single: `/contributors/contributor-slug/`
- Archive: `/contributors/`

Each profile shows:
- Featured image (headshot)
- Full bio
- Affiliation
- External links
- List of posts by that contributor (using Yak card markup)

## Developer Information

### File Structure

```
tomatillo-design-custom-authors/
├── tomatillo-design-custom-authors.php  # Main plugin file
├── includes/                             # PHP classes
│   ├── class-tdc-cpt.php                # CPT registration
│   ├── class-tdc-acf.php                # ACF field registration
│   ├── class-tdc-snapshots.php          # Snapshot manager
│   ├── class-tdc-genesis-hooks.php      # Genesis integration
│   ├── class-tdc-templates.php          # Template loader
│   ├── class-tdc-admin-columns.php      # Admin columns
│   ├── class-tdc-admin-filters.php      # Admin filters
│   ├── class-tdc-schema.php             # Structured data
│   └── class-tdc-helpers.php            # Helper functions
├── templates/                            # Page templates
│   ├── single-contributor.php           # Single contributor
│   └── archive-contributor.php          # Contributors archive
├── parts/                                # Template parts
│   └── contributor-panel.php            # Post footer panel
├── assets/                               # Frontend assets
│   └── css/
│       └── contributors.css             # Plugin styles
└── tests/                                # PHPUnit tests
    ├── bootstrap.php
    ├── TestHelpers.php
    ├── TestSnapshots.php
    └── TestSchema.php
```

### Running Tests

```bash
composer install
./vendor/bin/phpunit
```

### Hooks & Filters

The plugin uses these Genesis hooks:
- `genesis_post_info` - Override entry meta
- `genesis_after_entry_content` - Inject contributor panels

### Helper Functions

- `tdc_format_contributor_list($names)` - Format list with Oxford comma
- `tdc_should_link_contributor($id)` - Check if contributor is published
- `tdc_get_contributor_name_html($id, $snapshot)` - Get name with optional link
- `tdc_build_contributor_snapshot($id)` - Build snapshot data
- `tdc_build_snapshots_array($ids)` - Build array of snapshots
- `tdc_get_snapshot_by_id($snapshots, $id)` - Find snapshot by ID

### Customization

All CSS classes use the `tdc-` prefix for easy overriding. Key classes:

- `.tdc-contributor-panels` - Post footer panels container
- `.tdc-contributor-panel` - Individual panel
- `.tdc-contributor-single` - Single contributor page
- `.tdc-contributor-card` - Archive card
- `.yak-archive-card` - Posts list cards (Yak compatible)

## Testing

See `TESTING-CHECKLIST.md` for complete manual acceptance testing procedures.

## Changelog

### 1.0.0
- Initial release
- Contributor CPT with ACF fields
- Genesis entry meta override
- Post footer contributor panels
- Single and archive templates
- Admin columns and filters
- JSON-LD structured data
- Snapshot system for deleted contributors

## Support

For issues or questions, contact Tomatillo Design.

## License

GPL-2.0+

