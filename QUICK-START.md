# Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### Step 1: Activate Plugin
1. Go to WordPress Admin → Plugins
2. Find "Tomatillo Design Custom Authors"
3. Click "Activate"
4. Verify no error messages appear

### Step 2: Flush Permalinks
1. Go to Settings → Permalinks
2. Click "Save Changes" (don't change anything, just save)
3. This ensures `/contributors/` URLs work

### Step 3: Create Your First Contributor
1. In WordPress admin, find "Contributors" in the sidebar
2. Click "Add New"
3. Fill in:
   - **Title:** Contributor's full name (e.g., "Toby Posel")
   - **Content (Bio):** Full biographical text
   - **Featured Image:** Upload headshot photo (will appear circular)
   - **Short Bio:** 1-2 sentence version for post footers
   - **Affiliation:** Organization (optional)
   - **Links:** Add website, social media, etc.
4. Click "Publish"

### Step 4: Assign Contributor to a Post
1. Edit any post
2. In the right sidebar, find "Contributors" field
3. Search for and select your contributor
4. Click "Update" to save the post

### Step 5: View the Results
1. Visit the post on the frontend
2. You should see:
   - **Entry meta:** "January 5, 2026 • By Toby Posel"
   - **After content:** "About the Contributor" panel with photo and bio
3. Click the contributor's name to view their profile page

---

## ✨ Testing Multiple Contributors

Try assigning 2 or 3 contributors to a post to see Oxford comma formatting:
- **2 contributors:** "By Toby Posel and Jane Doe"
- **3 contributors:** "By Toby Posel, Jane Doe, and Sam Roe"

---

## 🔍 Verify Everything Works

### Check Entry Meta
On any post with contributors:
- Date should show
- "By [Names]" should appear
- Names should link to contributor profiles

### Check Contributor Profile
Visit `/contributors/toby-posel/` (or your contributor's slug):
- Profile header with photo and bio
- List of posts by that contributor
- Yak-style cards for each post

### Check Contributors Archive
Visit `/contributors/`:
- Grid of all contributors
- Each with photo, name, short bio
- "View Profile" links

### Check Admin
In Posts list:
- "Contributors" column should appear
- Shows assigned contributors
- Filter dropdown at top lets you filter by contributor

### Check Schema
1. View page source on a post with contributors
2. Search for `application/ld+json`
3. Should find Article schema with author array

---

## 🐛 Troubleshooting

### Contributors Menu Doesn't Appear
**Solution:** ACF Pro not installed/activated. Install it first.

### 404 on Contributor Pages
**Solution:** Permalinks not flushed. Go to Settings → Permalinks → Save Changes.

### No Entry Meta Change
**Solution:** Either no contributors assigned, or Genesis not active.

### "Missing Dependencies" Warning
**Solution:** Install/activate Genesis Framework and ACF Pro.

---

## 📚 Full Documentation

- **README.md** - Complete usage guide
- **TESTING-CHECKLIST.md** - Comprehensive test scenarios
- **VERIFICATION-REPORT.md** - Technical implementation details

---

## 💡 Tips

1. **Reordering Contributors:** Drag to reorder in the Contributors field
2. **Unpublished Contributors:** Set to Draft to show name without link
3. **External Links:** Always open in new tab automatically
4. **Snapshots:** Names preserved even if contributor deleted
5. **Admin Filter:** Great for finding all posts by a contributor

---

## ⚡ Common Workflows

### Adding a Guest Post
1. Create contributor for guest author
2. Assign to post
3. Save → automatically appears in byline and footer

### Batch Assigning Contributors
1. Use admin filter to find all posts needing a contributor
2. Quick Edit each one to assign contributor
3. Or bulk edit if multiple posts need same contributor

### Contributor Bio Updates
1. Edit contributor post
2. Update bio, links, or image
3. Changes appear immediately on all their posts

---

**You're all set!** The plugin is ready to use. If you encounter any issues, check the TESTING-CHECKLIST.md for detailed testing procedures.

