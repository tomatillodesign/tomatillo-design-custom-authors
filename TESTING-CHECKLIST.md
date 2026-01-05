# Manual Acceptance Testing Checklist
## Tomatillo Design Custom Authors Plugin

This checklist corresponds to the acceptance criteria in section 10 of the planning document.

---

## Setup Prerequisites

- [ ] WordPress 5.8+ installed
- [ ] Genesis Framework 3.0+ active
- [ ] ACF Pro 6.0+ installed and activated
- [ ] Plugin activated successfully
- [ ] No PHP errors in debug log
- [ ] All PHPUnit tests passing (`./vendor/bin/phpunit`)

---

## Single Post Scenarios

### No Contributors Assigned
- [ ] Navigate to a post with no contributors assigned
- [ ] Entry meta shows date only (no byline)
- [ ] No "About the Contributor" panel appears after content
- [ ] No PHP errors or warnings
- [ ] Date remains visible and properly formatted

### Single Contributor
- [ ] Create a contributor post (published)
- [ ] Assign to a post
- [ ] Entry meta shows: "Date • By [Contributor Name]"
- [ ] Contributor name links to contributor profile page
- [ ] Footer panel displays with thumbnail, bio, affiliation, links
- [ ] External links open in new tab with proper rel attributes

### Two Contributors
- [ ] Assign 2 contributors to a post
- [ ] Entry meta shows: "Date • By [Name] and [Name]"
- [ ] No Oxford comma (just "and")
- [ ] Both names link to respective profiles
- [ ] Both contributor panels render in correct order

### Three or More Contributors
- [ ] Assign 3+ contributors to a post
- [ ] Entry meta shows Oxford comma: "Date • By [Name], [Name], and [Name]"
- [ ] All names link to profiles
- [ ] All panels render in assigned order

### Published Contributor
- [ ] Contributor status is "Published"
- [ ] Name links to `/contributors/slug/` URL
- [ ] Link is clickable and loads profile page
- [ ] Schema includes contributor with URL

### Draft/Private Contributor
- [ ] Set contributor status to "Draft" or "Private"
- [ ] Name displays as plain text (no link) in entry meta
- [ ] Name displays as plain text in footer panel
- [ ] Snapshot preserves contributor name
- [ ] Schema includes contributor name but omits URL

### Footer Panel Details
- [ ] Thumbnail displays correctly (if featured image exists)
- [ ] Thumbnail is circular and properly sized
- [ ] Short bio displays from `td_short_bio` field
- [ ] If short bio empty, excerpt fallback works
- [ ] Affiliation displays (if present)
- [ ] Links render as individual buttons
- [ ] External links have `target="_blank"` and `rel="noopener noreferrer"`
- [ ] Links are styled consistently

### Snapshot Functionality
- [ ] Create contributor, assign to post, save post
- [ ] Verify snapshot JSON stored in `td_contributor_snapshots` meta
- [ ] Unpublish contributor
- [ ] Reload post - name still displays (from snapshot)
- [ ] Permanently delete contributor
- [ ] Reload post - name still displays (from snapshot)

---

## Contributor Pages

### Single Contributor Profile
- [ ] Navigate to `/contributors/slug/`
- [ ] Profile header displays with featured image
- [ ] Name (title) displays prominently
- [ ] Affiliation displays (if present)
- [ ] Full bio content renders properly
- [ ] Links list displays correctly with proper attributes
- [ ] Posts section shows "Posts by [Name]" heading

### Contributor Posts List
- [ ] Posts by contributor display in Yak card format
- [ ] Cards show: thumbnail, title, date, excerpt
- [ ] Cards use proper classes: `yak-archive-card`, `yak-archive-entry`, etc.
- [ ] Cards do NOT include FacetWP wrappers
- [ ] Date uses proper `<time>` element with datetime attribute
- [ ] Post titles link to correct posts
- [ ] Excerpt displays (if present)
- [ ] Cards with images show thumbnail
- [ ] Cards without images display correctly

### Pagination
- [ ] If more than X posts (based on `posts_per_page`), pagination appears
- [ ] Pagination links work correctly
- [ ] Page 2, 3, etc. load correct posts
- [ ] Current page is highlighted
- [ ] "Previous" and "Next" links work

### Contributors Archive
- [ ] Navigate to `/contributors/` archive
- [ ] All published contributors display
- [ ] Contributors sorted alphabetically (or by publish date)
- [ ] Each card shows: thumbnail, name, short bio
- [ ] "View Profile →" link works
- [ ] Cards styled consistently
- [ ] No unpublished contributors appear

---

## Admin Experience

### Posts List Table
- [ ] Contributors column appears in Posts list
- [ ] Column shows assigned contributor names
- [ ] Names are comma-separated in assignment order
- [ ] Published contributors link to their profile page
- [ ] Draft contributors show as plain text with "(draft)" indicator
- [ ] Posts with no contributors show "—"

### Filter by Contributor
- [ ] "All Contributors" dropdown appears above Posts list
- [ ] Dropdown populated with published contributors
- [ ] Dropdown sorted alphabetically
- [ ] Select a contributor and filter
- [ ] Only posts with that contributor display
- [ ] Filter persists across pagination

### Editor Experience
- [ ] ACF "Contributors" field appears in post sidebar
- [ ] Field is searchable
- [ ] Field allows multiple selections
- [ ] Field is sortable (drag to reorder)
- [ ] Field filters to `contributor` CPT only
- [ ] Can add new contributor from field (if ACF supports)
- [ ] Saving post triggers snapshot update

---

## SEO & Structured Data

### Schema Validation
- [ ] View source on single post with contributors
- [ ] Find `<script type="application/ld+json">` in `<head>`
- [ ] Schema includes `@context: "https://schema.org"`
- [ ] Schema includes `@type: "Article"`
- [ ] Schema includes `datePublished` from post date
- [ ] Schema includes `dateModified` from modified date
- [ ] Schema includes `author` array with Person objects

### Schema Authors Match Visible
- [ ] If 1 contributor shown, schema has 1 author
- [ ] If 3 contributors shown, schema has 3 authors
- [ ] Author names in schema match visible byline exactly
- [ ] Published contributors have `url` in schema
- [ ] Draft/unpublished contributors omit `url` in schema

### Google Rich Results Test
- [ ] Copy post URL
- [ ] Visit [Google Rich Results Test](https://search.google.com/test/rich-results)
- [ ] Paste URL and test
- [ ] Article schema validates successfully
- [ ] No errors in structured data

---

## Edge Cases

### Contributor Deleted Permanently
- [ ] Assign contributor to post, save
- [ ] Delete contributor permanently (trash → delete)
- [ ] Reload post frontend
- [ ] Snapshot name displays (not broken)
- [ ] No PHP errors

### Contributor Unpublished
- [ ] Assign published contributor to post
- [ ] Change contributor to Draft status
- [ ] Reload post frontend
- [ ] Name displays as plain text (no link)
- [ ] Footer panel shows name without link
- [ ] No PHP errors

### Post Saved Multiple Times
- [ ] Assign contributor to post, save
- [ ] Edit post, save again (no changes)
- [ ] Verify snapshots update correctly
- [ ] Change contributor, save
- [ ] Verify snapshots reflect new contributor

### Genesis Conflicts
- [ ] Check Genesis Theme Settings (Customizer)
- [ ] If entry meta customized in theme settings, verify plugin overrides it
- [ ] Entry meta should show plugin format (Date • By Contributors)
- [ ] No duplicate author bylines appear
- [ ] No conflicts with existing Genesis hooks

### No Contributors on Post
- [ ] Post with no contributors assigned
- [ ] Entry meta shows date only
- [ ] No "About the Contributor" section
- [ ] Schema should NOT include author array (or use default WP author)
- [ ] Page displays normally

---

## Visual/Styling Checks

### Responsive Design
- [ ] Test on mobile (< 768px width)
- [ ] Contributor panels stack properly
- [ ] Profile header stacks on mobile
- [ ] Archive grid becomes single column
- [ ] Yak cards display correctly
- [ ] All text remains readable

### Typography & Spacing
- [ ] Entry meta separator (•) displays with proper spacing
- [ ] Footer panels have adequate padding
- [ ] Profile header has proper margins
- [ ] Cards have consistent spacing
- [ ] Links are clearly clickable
- [ ] Hover states work on all interactive elements

### Browser Compatibility
- [ ] Test in Chrome
- [ ] Test in Firefox
- [ ] Test in Safari
- [ ] Test in Edge
- [ ] No layout issues in any browser

---

## Performance

- [ ] No slow queries logged
- [ ] Pages load in reasonable time (< 2s)
- [ ] No N+1 query issues with multiple contributors
- [ ] CSS loads only on relevant pages
- [ ] No JavaScript errors in console

---

## Final Checks

- [ ] All acceptance criteria from planning doc met
- [ ] No PHP warnings or errors in debug log
- [ ] No JavaScript console errors
- [ ] Plugin can be safely deactivated without errors
- [ ] Plugin can be reactivated without issues
- [ ] Permalinks flush properly (rewrite rules work)

---

## Notes

Use this space to document any issues found during testing:

```
Issue: [Description]
Steps to reproduce: [...]
Expected: [...]
Actual: [...]
Status: [Fixed/Pending/Deferred]
```

---

**Testing Completed By:** _______________  
**Date:** _______________  
**Sign-off:** _______________

