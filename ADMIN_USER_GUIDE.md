# Deski Dine Admin Panel - User Guide

## 📍 Quick Navigation

### Frontend Content Management
These are located under **Frontend Content** menu in the admin sidebar:

| Section | Access URL | Purpose |
|---------|-----------|---------|
| Signature Platters | `/admin/signature-platters/index` | Manage featured platter dishes |
| Facebook Reels | `/admin/facebook-reels/index` | Manage Facebook reel links |
| About Section | `/admin/about/index` | Edit restaurant information |
| Contact Section | `/admin/contact/index` | Edit location & contact details |

---

## 🍽️ Managing Signature Platters

### What is it?
Featured platter dishes displayed on the homepage with images, descriptions, and features.

### How to Add a New Platter

1. Go to **Frontend Content → Signature Platters**
2. Click **"Add New"** button
3. Fill in the form:
   - **Title**: Platter name (e.g., "Royal Kacchi for 4")
   - **Subtitle**: Short category (e.g., "Signature Collection")
   - **Description**: Details about the platter
   - **Image**: Upload platter photo (webp, png, jpg - max 2MB)
   - **Features**: List key features (e.g., "Free Salad, Free Lassi")
   - **Status**: Active/Inactive
   - **Sort Order**: Display order (0 = first)
4. Click **"Save"**

### Features Field
Add features one by one and they'll appear as a bulleted list on the homepage:
- ✓ Free salad
- ✓ Served with rice
- ✓ Premium ingredients

### Editing/Deleting
- Click the **Edit** (pencil) icon to modify
- Click the **Delete** (trash) icon to remove
- Changes appear on homepage immediately

---

## 📱 Managing Facebook Reels

### What is it?
Links to Facebook videos displayed in a carousel on the homepage.

### How to Add a Reel

1. Go to **Frontend Content → Facebook Reels**
2. Click **"Add New"** button
3. Fill in the form:
   - **Title**: Video title (e.g., "Kitchen Tour")
   - **Facebook URL**: Direct link to the Facebook video/reel
   - **Thumbnail**: Upload preview image (webp, png, jpg - max 2MB)
   - **Status**: Active/Inactive
   - **Sort Order**: Display order
4. Click **"Save"**

### Finding Facebook URLs
1. Open your Facebook reel/video
2. Click **Share**
3. Copy the link from the share dialog
4. Paste it in the **Facebook URL** field

### Getting Thumbnails
- You can use a screenshot from the video
- Or use Facebook's built-in thumbnail preview
- Upload as jpg, png, or webp (max 2MB)

---

## ℹ️ Managing About Section

### What is it?
The restaurant's story and information displayed on homepage.

### How to Edit

1. Go to **Frontend Content → About Section**
2. Update any of these fields:
   - **Kicker**: Small label (e.g., "Our Heritage")
   - **Title**: Main heading
   - **Lead**: Short intro paragraph
   - **Paragraph**: Detailed description (supports HTML)
   - **Feature 1**: Title and icon
   - **Feature 2**: Title and icon
   - **Experience**: Years/number and text
   - **CTA Link**: Button target URL
   - **Image**: Upload main image (webp, png, jpg - max 3MB)
3. Click **"Save"**

### Supported Icons
Use Bootstrap icon names (e.g., "bi bi-fire", "bi bi-patch-check-fill"):
- `bi bi-fire` - Fire icon
- `bi bi-patch-check-fill` - Checkmark
- `bi bi-heart-fill` - Heart
- [View more icons](https://icons.getbootstrap.com/)

---

## 📍 Managing Contact Section

### What is it?
Address, hours, phone, and map displayed on homepage.

### How to Edit

1. Go to **Frontend Content → Contact Section**
2. Update these fields:
   - **Section Title**: Heading (e.g., "Visit Us")
   - **Section Subtitle**: Subheading
   - **Restaurant Name**: Your business name
   - **Address**: Street address (supports multiple lines)
   - **Hours**: Opening hours (e.g., "Mon-Sun: 11:00 AM - 11:00 PM")
   - **Phone**: Contact number
   - **Email**: Email address (optional)
   - **Map Embed**: Google Maps iframe code
   - **Map Link**: Google Maps URL for directions
   - **Facebook URL**: Facebook page link
   - **Instagram URL**: Instagram profile link
3. Click **"Save"**

### How to Get Google Maps Embed

1. Go to [Google Maps](https://maps.google.com)
2. Search for your restaurant location
3. Click **Share** → **Embed a map**
4. Copy the entire `<iframe>` tag
5. Paste only the `src` attribute value (the URL) in the **Map Embed** field
   - Example: `https://www.google.com/maps/embed?pb=...`

### Example Configuration
```
Section Title: Visit Us
Subtitle: We look forward to welcoming you
Name: Degchi Dine
Address: Boropool Circle, Halishahar, Chittagong
Hours: Mon - Sun: 11:00 AM - 11:00 PM
Phone: +880 1234 567 890
Map Link: https://maps.google.com/?q=Degchi+Dine
Facebook: https://www.facebook.com/DegchiDine
```

---

## 🖼️ Image Upload Tips

### Best Practices
- **Format**: Use webp for smallest file size, png for transparency, jpg for photos
- **Size**: Keep images under max size (2-3MB depending on section)
- **Resolution**: 1200×800px minimum for best quality
- **Optimization**: Use image optimization tools before uploading

### Optimization Tools
- [TinyPNG](https://tinypng.com/) - Compress images
- [CloudConvert](https://cloudconvert.com/) - Convert formats
- [Pixlr](https://pixlr.com/) - Quick edits

### File Size Reference
- Platter images: max 2MB
- Reel thumbnails: max 2MB
- About image: max 3MB

---

## 🔍 DataTables Features

### Searching
1. Type in the **Search** box
2. Results update instantly

### Sorting
1. Click on any column header
2. Ascending/descending toggles

### Pagination
1. Use page numbers at bottom
2. Or select items per page

### Status
- **Green badge**: Active (visible on frontend)
- **Red badge**: Inactive (hidden from frontend)

---

## ⚙️ Troubleshooting

### Images Not Uploading
**Problem**: Upload fails or shows error
**Solution**:
1. Check file size (must be under max)
2. Check file format (must be webp, png, or jpg)
3. Ensure file has no spaces in name
4. Try clearing browser cache
5. Check server permissions

### Changes Not Appearing on Homepage
**Problem**: Updated data doesn't show
**Solution**:
1. Clear cache: Contact server admin to run `php artisan cache:clear`
2. Hard refresh page: Press Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
3. Check **Status** field is set to **Active**
4. Wait a few seconds for cache to update (cache is 5 minutes)

### DataTables "Ajax Error"
**Problem**: Table shows error message
**Solution**:
1. Press F12 to open Developer Console
2. Look for error details
3. Common issues:
   - Server connection error → Check internet
   - 404 error → Page URL changed
   - 500 error → Server error (contact admin)

### Reel Link Not Working
**Problem**: Video won't play
**Solution**:
1. Verify Facebook URL is correct
2. Test URL works in browser first
3. Ensure Facebook post is public (not private)
4. Try clearing browser cache

---

## 📱 Frontend Preview

### Signature Platters Section
- Displays as **image carousel** with text
- Users can scroll through different platters
- Each shows image, name, subtitle, description, features
- Button links to complete menu

### Facebook Reels Section
- Displays as **reel carousel**
- Shows thumbnail images
- Click to open in Facebook
- Ordered by Sort Order

### About Section
- Large **banner image** on right
- Content on left with features
- Experience badge overlay
- "Read Full Journey" button

### Contact Section
- **Info card** on left with address, hours, phone
- **Google Map** on right
- Social media links
- "Get Directions" button

---

## 🎨 Customization Tips

### To Change Section Icons
Edit the view files and change Bootstrap icon classes:
- Signature Platters: `fa-solid fa-concierge-bell`
- Reels: `bi bi-camera-reels`
- About: `bi bi-heart-fill`
- Contact: `fa-solid fa-map-location-dot`

### To Reorder Sections on Homepage
Edit `resources/views/index.blade.php` and reorder the `<section>` tags.

### To Add More Features to About
Edit the About form in admin to add `about_feature_3_*` fields.

---

## 📞 Support

### Common Questions

**Q: How many platters can I add?**
A: Unlimited. Only active (status = true) platters appear on homepage.

**Q: Can I schedule content to go live later?**
A: Not currently. Use the Status toggle to activate/deactivate.

**Q: What happens when I delete a platter?**
A: It's removed permanently from database. Image file is also deleted.

**Q: Can I upload videos directly?**
A: No, Facebook Reels are links only. Thumbnails are optional.

**Q: Can I use HTML in About description?**
A: Yes! Use full HTML with proper tags for formatting.

---

## 🔒 Access & Permissions

**Requirements to manage these sections:**
- Admin login required
- "Frontend Content" permission (or Admin role)
- Same permissions as other admin features

**Tip**: Ensure your user account has admin or appropriate role assigned.

---

## 📊 Data Maintenance

### Weekly Tasks
- Review platter orders and update sort order if needed
- Check Facebook links still work
- Verify images display correctly

### Monthly Tasks
- Archive old/outdated content
- Check database size
- Review image storage usage

### Quarterly Tasks
- Update seasonal content
- Refresh images
- Audit user permissions

---

## 🚀 Performance Tips

1. **Optimize images** before uploading to reduce file size
2. **Use webp format** for best compression
3. **Limit featured items** - Don't add too many at once
4. **Set proper sort order** - Improves frontend performance
5. **Deactivate unused items** - Reduces database queries

---

## ✅ Checklist Before Going Live

- [ ] All sections have at least 1 active item
- [ ] All images uploaded and displaying
- [ ] All links tested and working
- [ ] Status set to Active where needed
- [ ] Sort order properly configured
- [ ] Homepage preview looks good
- [ ] Mobile view verified
- [ ] Contact information is current
- [ ] Social media links are correct
- [ ] About section text is finalized

---

**Last Updated**: June 6, 2026
**Version**: 1.0 Final
