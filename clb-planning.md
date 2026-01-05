## Quick issue check (so this doesn’t get weird later)

1. **Genesis “entry meta” can be customized in two places**

* Some sites rely on Genesis Theme Settings (Customizer) for entry meta text, but you want plugin-controlled output via hooks so it only appears when contributors are assigned. That’s fine, but we should **treat Theme Settings as “off” for author meta** to avoid duplicate/conflicting output. Genesis explicitly supports editing entry meta text in settings, so we’ll override at runtime. ([my.studiopress.com][1])

2. **Contributor deletion = name loss unless we snapshot**
   You already chose belt + suspenders. Good. The only nuance: “deleted permanently” means the contributor post is gone, so we can’t even render the title. Snapshot solves that. We should store snapshot **by contributor ID + last-known name + last-known permalink** (permalink optional) and refresh it on every post save.

3. **Structured data must match what’s visible**
   If you show “By X” visibly, your Article schema should include those authors too, and if you show multiple, schema should include all of them. Google explicitly calls this out. ([Google for Developers][2])

4. **Reuse Yak archive markup, but avoid FacetWP wrappers**
   Your snippet includes `facetwp-template` and `<!--fwp-loop-->` stuff. We’ll reuse the *card markup* and classes (`yak-archive-card`, etc.) but not the FacetWP scaffolding.

5. **Relationship field scalability**
   ACF Relationship is fine for “many contributors,” but we should constrain it to the `contributor` CPT and enable search. If you ever hit hundreds/thousands, we can revisit (still usually OK).

---

# Planning document: Tomatillo Design “Contributors” Plugin (Yak + Genesis)

## 1. Goal

Replace WordPress/Genesis “post author” output with a Contributor system designed for external writers:

* Keep the post date visible.
* Hide the native WP author/byline entirely unless Contributor(s) are assigned.
* Allow one or many Contributors per post (sortable).
* Render:

  * **Entry meta (replacing Genesis entry meta area):** Date + “By {contributors}”
  * **Post footer panel:** profile thumbnail + short bio + links + affiliation (per contributor)
* Provide public Contributor profile pages at `/contributors/{slug}/` showing profile header + list of that contributor’s posts using existing Yak archive card markup.
* Add admin ergonomics: column + filter in Posts list.
* SEO: author linking + structured data aligned with visible authors.

## 2. Non-goals

* No contributor logins or submission workflow.
* No interactive contributor directory filtering.
* No migration tool for legacy “Authored by …” text inside content.
* No “per-post contributor roles” now (leave extension points).

---

## 3. Data model

### 3.1 Contributor CPT

Create CPT: `contributor`

* Public, publicly queryable.
* Rewrite slug base: `contributors` (so single pages are `/contributors/toby-posel/`).
* Supports:

  * `title` (Contributor display name)
  * `editor` (full bio content)
  * `thumbnail` (featured image headshot)
  * `revisions` (recommended)
  * `excerpt` optional (not required if short bio is a field)

Archive behavior (recommended based on your “yes”): enable `/contributors/` archive page that lists contributors. Keep it simple (non-interactive). (If you later decide you *don’t* want indexing of the archive, we can noindex it.)

### 3.2 Post ↔ Contributor relationship (ACF Pro)

On standard Posts, store assigned contributors with an ACF Relationship field:

* Field name: `td_contributors` (relationship)
* Allow multiple selections
* Enable sorting
* Filter by post type: `contributor`
* UI location: sidebar
* Provide “Add new” link for convenience

ACF Relationship fields are the correct primitive for this kind of CPT-to-CPT relationship. ([ACF][3])

### 3.3 Contributor fields (ACF)

On Contributor CPT:

* `td_short_bio` (textarea, short bio used in post footer panel)
* `td_affiliation` (text)
* `td_links` (repeater)

  * `label` (text)
  * `url` (url)

Link output best practices:

* External links open in new tab
* Include `rel="noopener noreferrer"` (and `rel="nofollow"` only if you explicitly want it)

### 3.4 Snapshot fields (hidden ACF on Posts)

To prevent name loss / preserve display when contributor is unpublished/deleted:

Add hidden ACF fields on Posts:

* `td_contributor_snapshots` (textarea storing JSON)

  * Array of objects, one per assigned contributor, e.g.:

    * `id` (int)
    * `name` (string)
    * `permalink` (string, last known)
* `td_contributor_snapshot_updated` (datetime, optional)

**Update rule:** On every post save, rebuild snapshot JSON from current `td_contributors` selection:

* If a contributor is assigned:

  * Store current title as `name`
  * Store current permalink as `permalink`
* If removed:

  * Remove from snapshot array
* If contributor is unpublished later:

  * Display uses live post title if available, but link only if `publish`. If not publish, fall back to snapshot name and omit link.

This meets your “remains until overwritten/updated” requirement because it updates on post save, not on contributor save (unless we choose to propagate, see extensions).

---

## 4. Frontend rendering

### 4.1 Genesis/Yak integration strategy

Use Genesis hooks/filters to:

1. Remove default Genesis entry meta output for author (and potentially replace entire entry meta line).
2. Inject your replacement “entry meta” block:

   * date + byline (Oxford comma formatting)
3. Inject post-footer contributor panels after content.

Genesis supports entry meta customization; we’ll implement at hook level so it’s conditional (only if contributors assigned). ([my.studiopress.com][1])

### 4.2 Entry meta block (replaces Genesis entry meta)

Render in the entry header meta area (replacing the usual “posted on / by / comments” string):

**Format (decision):** combined date + author line.

**Recommended visible format (A++ “what people do”):**

* `December 16, 2025 • By Toby Posel`
* If multiple: `December 16, 2025 • By Toby Posel, Jane Doe, and Sam Roe`

Rules:

* Use Oxford comma.
* Use label “By”.
* Contributor names link to contributor pages only if contributor post status is `publish`, else plain text.
* Date remains always visible (your spec).

Accessibility:

* Date should be a `<time datetime="YYYY-MM-DD">` element.
* If using separators like `•`, ensure spacing and screen-reader clarity (or use CSS separators).

### 4.3 Post footer contributor panel (after content)

Immediately after the content, before categories/tags/comments, render a compact “About the contributor(s)” area:

For each contributor in assigned order:

* Thumbnail: contributor featured image (size = small square)
* Name: link if publish, else plain text
* Short bio: from `td_short_bio` (fallback: trimmed excerpt from full bio content if empty)
* Affiliation: from `td_affiliation` (optional)
* Links: list of labeled links (external opens new tab with best-practice rel attrs)

Markup/classes:

* Add Yak-prefixed classes (or plugin-prefixed + Yak-friendly) consistent with your system.
* Keep markup stable and style in plugin CSS file.

### 4.4 Contributor single page template

Contributor page should render:

1. Profile header:

* Featured image
* Name (title)
* Affiliation
* Full bio (post content)
* Links

2. Posts list (Yak archive cards)

* Query posts where `td_contributors` contains this contributor ID
* Sort newest first
* Paginate (match WP default or set per-page value)
* Render cards using your existing class structure:

Use the same internal structure (`yak-archive-card`, `yak-archive-entry`, `yak-archive-image`, `yak-archive-body`, `yak-entry-title`, `yak-entry-date`, `yak-entry-excerpt`) and the thumbnail-present/missing logic. **Do not include FacetWP wrappers**.

Card fields:

* Title (linked)
* Date (as in your markup)
* Excerpt
* Featured image if present

Pagination:

* Use standard WP pagination markup (Yak already styles `.pagination` patterns). Keep consistent with your snippet.

### 4.5 Contributor archive page (`/contributors/`)

Since you said “yes”: create a simple contributors index:

* List contributors alphabetically (or newest first—your call later)
* Card layout can be similar to post cards but with:

  * headshot thumbnail
  * name
  * short bio (optional)

If you later decide Google indexing isn’t desired, we can:

* add `noindex` via meta
* or disable archive and create a manual page

---

## 5. SEO and structured data

### 5.1 Internal linking

* Byline links to contributor profile pages (internal links).
* Footer panels link again (fine).

### 5.2 Article schema (JSON-LD recommended)

Add JSON-LD Article (or BlogPosting) structured data on single posts with contributors assigned:

* `datePublished`: from WP post date
* `dateModified`: from modified date (optional)
* `author`: array of Person objects for each contributor

  * `name`: contributor name only
  * `url`: contributor page URL (if publish; else omit url)
  * `sameAs`: optionally include contributor external links (if you want)

Google’s author markup guidance explicitly recommends including all visible authors and using `type` and `url` (or `sameAs`) for clarity. ([Google for Developers][2])

We must ensure the schema authors match the visible byline (don’t list authors in schema you don’t show on the page).

---

## 6. Admin UX

### 6.1 Posts list table

Add:

* Column “Contributors” that displays assigned contributor names (comma-separated), in order.
* If contributor publish: link to edit contributor in admin (optional) and/or frontend (optional).
* If not publish: show plain text.

### 6.2 Posts list filter

Add dropdown filter “Filter by Contributor”:

* Populated by published contributors (optionally include non-published if needed for cleanup).
* When selected, filter posts where relationship field contains that contributor ID.

### 6.3 Editor conveniences

* Relationship field in sidebar, searchable.
* “Add new contributor” link opens contributor creation screen in new tab.

---

## 7. Styling

Ship a plugin CSS file:

* Minimal but complete styling for:

  * entry meta line (spacing, typography, separators)
  * footer contributor panels (grid/flex, thumbnail sizing)
  * contributor profile header block
  * contributor posts list reuses Yak card styles (so keep CSS light there)

Load CSS:

* On single posts (only when contributors exist)
* On contributor CPT single + archive

---

## 8. Plugin structure (recommended)

```
tomatillo-design-contributors/
	tomatillo-design-contributors.php
	includes/
		class-tdc-cpt.php
		class-tdc-acf.php
		class-tdc-snapshots.php
		class-tdc-genesis-hooks.php
		class-tdc-templates.php
		class-tdc-admin-columns.php
		class-tdc-admin-filters.php
		class-tdc-schema.php
	assets/
		css/
			contributors.css
	templates/
		single-contributor.php        (optional template override approach)
		archive-contributor.php       (optional)
	parts/
		contributor-meta.php          (entry meta rendering)
		contributor-panel.php         (post footer panels)
		contributor-card.php          (if you want contributor index cards)
```

Template strategy:

* Prefer Genesis-friendly rendering via hooks rather than hard template overrides.
* For contributor pages, either:

  * Provide template files and filter `template_include`, or
  * Render via `the_content` filter + shortcode-like blocks (less ideal)
    Template override is usually cleanest for CPTs.

---

## 9. Implementation notes (for the agent)

### 9.1 “Only display byline if assigned”

Logic:

* On single post render:

  * Get assigned contributors from ACF relationship field
  * If empty: do nothing; do not alter meta (or remove author output site-wide but don’t replace)
  * If non-empty:

    * Replace entry meta output with combined date + byline

### 9.2 Oxford comma formatting helper

Create helper `tdc_format_contributor_list($names)`:

* 1 name: `Toby Posel`
* 2 names: `Toby Posel and Jane Doe`
* 3+: `Toby Posel, Jane Doe, and Sam Roe`

### 9.3 Publish-only link rule

For each assigned contributor:

* If `get_post_status($id) === 'publish'`:

  * link to `get_permalink($id)`
* Else:

  * plain text using live title if available, else snapshot name

### 9.4 Snapshot updates

On `save_post` for posts:

* Bail on autosave/revisions.
* Read assigned contributor IDs.
* Build snapshot array objects:

  * id, name (title), permalink
* Store JSON into hidden ACF field meta (or standard post meta if preferable; you requested hidden ACF field, so do that).
* Snapshot is rewritten on every save so it stays aligned with current assignments.

(Extension option: if a contributor name changes, snapshots won’t update until each post is saved. That’s acceptable per your “until overwritten/updated” note. If later you want propagation, add a contributor save hook that finds linked posts and updates snapshots in batch.)

### 9.5 Contributor page post query

Query posts where ACF relationship contains the contributor ID.

* With relationship fields, the value is stored serialized; typical WP meta query uses `LIKE` with quoted ID.
* Paginate using `paged` query var.
* Render results using your Yak markup classes.

### 9.6 Performance considerations

* Cache contributor IDs and snapshot decode per request (avoid repeated `get_field` calls).
* Contributor page query should be indexed reasonably; if it grows huge, consider a lightweight cache layer or a secondary meta key strategy later.

---

## 10. Acceptance criteria

### Single post

* If no contributors: no author/byline anywhere; date remains.
* If contributors assigned:

  * Entry meta shows date + “By …” with Oxford comma; linked only for published contributors.
  * After content: contributor panels render in assigned order with thumbnail, short bio, affiliation, links.
  * If contributor is draft/private/trashed: name shows plain text, no link; snapshot name used if needed.

### Contributor page

* `/contributors/{slug}/` loads with profile header (photo, bio, affiliation, links).
* Below: paginated list of that contributor’s posts using Yak card markup.

### Admin

* Posts list shows Contributors column.
* Posts list filter by Contributor works.
* Editor can search/select/sort contributors in sidebar ACF field.
* “Add new contributor” convenience exists.

### SEO

* Visible authors match schema authors.
* Schema includes author name + URL when publish. ([Google for Developers][2])

---

## 11. Open decisions (small, but worth noting)

* Contributors archive (`/contributors/`) sort order (alphabetical vs newest).
* Whether `/contributors/` should be indexed or noindexed (SEO preference).
* Exact CSS class naming convention (plugin prefix vs `yak-` prefix; both can coexist).

---

If you want, next step is I’ll convert this into your usual “kickass agent-ready” format (tight bullets + implementation order + test checklist).

[1]: https://my.studiopress.com/documentation/usage/genesis-features/edit-the-entry-meta-and-footer-text-with-genesis/?utm_source=chatgpt.com "Edit the Entry Meta and Footer Text with Genesis"
[2]: https://developers.google.com/search/docs/appearance/structured-data/article?utm_source=chatgpt.com "Learn About Article Schema Markup | Google Search Central"
[3]: https://www.advancedcustomfields.com/blog/wordpress-custom-post-type-relationships/?utm_source=chatgpt.com "Mastering Advanced Custom Post Type Relationships in ..."
