# CMSR-TZ Website

Website and staff content-management portal for **CMSR-TZ — Community Mobilisation
for Reciprocal Development in Tanzania**, a non-governmental organisation
established in August 1997 and registered under Certificate No. 00NGO/R1/00411,
based in Dodoma City.

Built with PHP 8 + MySQL on XAMPP, using the Kind Heart Charity Bootstrap 5.2
theme supplied as the design reference.

---

## 1. Setting it up

1. **Start XAMPP** (Apache + MySQL).

2. **Create and load the database** — from the project root:

   ```bash
   /opt/lampp/bin/mysql -u root < database/schema.sql
   ```

   or in phpMyAdmin: *Import → choose `database/schema.sql`*. The script creates
   the `cmsr_tz` database, every table and all the starting content taken from
   the organisation's documents.

3. **Check the credentials** in `website/config/config.php` (`DB_USER`,
   `DB_PASS`) if your MySQL root user has a password.

4. **Open the site**: <http://localhost/CMSRTZ/website/>

5. **Sign in to the portal**: <http://localhost/CMSRTZ/website/admin/login.php>

   | Username | Password   | Role  |
   |----------|------------|-------|
   | `admin`  | `12345678` | Admin |

   > **Change this password immediately** under *My profile → Change password*.

6. **Before going live**, set `const DEBUG = false;` in `website/config/config.php`.

### Folder permissions

`uploads/` and `cache/` must be writable by the user Apache runs as
(`daemon` in XAMPP):

```bash
sudo chgrp -R daemon website/uploads website/cache
chmod -R 775 website/uploads website/cache
```

If you cannot change the group, `chmod -R 777 website/uploads website/cache`
works on a local XAMPP machine.

---

## 2. What is where

```
website/
├── index.php                 Homepage — hero, sectors, about, impact, projects, news
├── about.php                 Who we are, vision & mission, core values, how we work
├── board.php                 Board members and the leadership team
├── partners.php              Donors, partners, government partners and networks
├── what-we-do.php            All five sectors
├── sector.php?slug=…         One sector, with its projects
├── projects.php              Filterable project list + the 2014-2026 record table
├── project.php?slug=…        One project, with its photo gallery
├── news.php                  News list with search, categories and pagination
├── news-detail.php?slug=…    One article
├── resources.php             Annual reports and publications
├── gallery.php               Photo gallery, by album
├── where-we-work.php         Regions of operation + office map
├── contact.php               Contact details and enquiry form
├── donate.php                Support Our Work + giving enquiry form
├── subscribe.php             Newsletter sign-up handler
├── thumb.php                 On-the-fly image resizer with a disk cache
│
├── config/config.php         Database credentials, paths, session, upload limits
├── includes/                 db.php · functions.php · header.php · footer.php · page-hero.php
├── assets/                   css · js · fonts · images (theme + cmsr.css)
├── photos/                   The picture library supplied by the office
├── uploads/                  Staff uploads (media/ and files/)
├── cache/thumbs/             Generated image sizes — safe to delete at any time
│
└── admin/                    The staff portal
    ├── login.php · logout.php · index.php (dashboard)
    ├── crud.php              One screen that manages every content type
    ├── config/entities.php   The definition of every content type
    ├── includes/             auth.php · fields.php · header.php · footer.php
    ├── media.php             Picture library, uploads and the picker feed
    ├── overview.php          The homepage about block
    ├── settings.php          Contact details, social links, footer, giving details
    ├── messages.php          Inbox for contact and support enquiries
    ├── users.php             Staff accounts (administrators only)
    ├── profile.php           Own details and password
    └── activity.php          Audit log (administrators only)
```

---

## 3. The staff portal

Everything on the public site is editable without touching a file.

| Area | What it controls |
|---|---|
| **Hero slides** | The rotating banner on the homepage |
| **About block** | The picture and text below the sector shortcuts |
| **Impact counters** | The four figures on the dark strip |
| **Sectors** | The five programme areas and their full pages |
| **Projects** | Current and past projects, with fact sheets and beneficiary figures |
| **Project photos** | Extra pictures on a project page |
| **News articles / Short updates** | The Latest section and the “In brief” panels |
| **Static pages / Page blocks** | About Us, How We Work, Vision & Mission, Support Us, Contact |
| **Board members / Leadership team / Core values** | The governance pages |
| **Partners & networks** | Donors, partners, government partners and networks |
| **Where we work** | Regions and districts |
| **Publications / Annual reports** | Documents offered for download |
| **Media library** | Browse the supplied photos, upload new ones, copy paths |
| **Subscribers** | Newsletter sign-ups |
| **Site settings** | Address, e-mail, phone, social links, footer text, giving details |
| **Staff accounts** | Who may sign in, and what they may do |
| **Activity log** | Every sign-in and content change |

### Roles

| Role | May do |
|---|---|
| **Admin** | Everything, including staff accounts, settings and the activity log |
| **Editor** | Add, change and delete content; no accounts or settings |
| **Viewer** | Read-only access to the portal |

### Adding a new content type

Add one entry to `admin/config/entities.php` describing the table and its
fields. The list screen, the create/edit form, validation, publishing toggles,
search, filters and deletion are all generated from that definition — no new
page needs to be written.

---

## 4. Pictures

Camera originals in `photos/` are large (some over 9 MB), so no page ever links
to them directly. Every image goes through `thumb.php`, which resizes it once,
honours the EXIF orientation of phone photos and caches the result in
`cache/thumbs/`.

In templates, use the helper rather than a raw path:

```php
<img src="<?= h(img($row['image'], 700)) ?>" alt="…">
```

Deleting `cache/thumbs/` is always safe — the files are regenerated on demand.

---

## 5. Security notes

* Passwords are stored with `password_hash()` (bcrypt).
* Every form is protected by a CSRF token; sign-in is throttled after 8 failed
  attempts and sessions expire after 60 minutes of inactivity.
* All queries are prepared statements; output is escaped with `h()`.
* Rich-text fields are filtered through `safe_html()` — a small allow-list of tags.
* Uploads are checked by extension **and** by `getimagesize()`, are renamed, and
  `uploads/.htaccess` turns PHP off inside the folder.
* `config/`, `includes/`, `admin/config/` and `admin/includes/` are denied by
  `.htaccess`; the public folder listing is disabled.
* The public contact and support forms carry a hidden honeypot field.

---

## 6. Content sources

The seeded content comes from the organisation's own documents in `Docx/`:

* `ABOUT US - CMSRTZ 2026 Final.docx` — who we are, how we work, networks, approach
* `CMSR SECTORS 2026 Final.docx` — the five sector pages
* `SlidePicture- Preambles.docx` — the hero slide texts, vision and mission
* `Some past projects.docx` — the 2014-2026 project record
* `ANNUAL Final Reports -2025 -CMSR-TZ.docx` — board, leadership, core values,
  current programmes and the 2025 news items

Photographs come from `photos/` exactly as supplied by the office.
