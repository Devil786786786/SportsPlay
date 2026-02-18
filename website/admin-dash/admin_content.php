<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'News & Content';
$activeNav = 'content';
include __DIR__ . '/../includes/admin_header.php';
?>

<div class="sp-split">
  <section class="sp-card">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">Announcements</div>
        <div class="sp-card__sub">Publish season updates, schedule changes, and club news.</div>
      </div>

      <div class="sp-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgPostCreate"><i class="fa-solid fa-plus"></i>&nbsp; New Post</button>
        <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-paper-plane"></i>&nbsp; Push Notification</button>
      </div>
    </div>

    <div class="sp-card__bd">
      <div class="sp-filterbar">
        <div class="sp-filterbar__left">
          <div class="sp-search">
            <i class="fa-solid fa-magnifying-glass icon"></i>
            <input data-table-search="#tblPosts" type="text" placeholder="Search posts…" />
          </div>
          <select class="sp-select" data-table-filter="#tblPosts" data-col="2">
            <option value="">Any status</option>
            <option>Published</option>
            <option>Draft</option>
            <option>Archived</option>
          </select>
        </div>
        <div class="sp-filterbar__right">
          <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-file-export"></i>&nbsp; Export</button>
        </div>
      </div>

      <div style="height:12px"></div>

      <div class="sp-table-wrap" style="max-height: 520px; border:1px solid var(--line)">
        <table id="tblPosts" class="sp-table sp-table--light">
          <thead>
            <tr>
              <th>Title</th>
              <th style="width:130px">Audience</th>
              <th style="width:120px">Status</th>
              <th style="width:160px">Updated</th>
              <th style="width:180px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Season Kickoff Info</strong><div class="sp-card__sub">Everything you need for week 1.</div></td>
              <td>All</td>
              <td><span class="sp-pill sp-pill--success">Published</span></td>
              <td>2026-02-12</td>
              <td>
                <div class="sp-actions">
                  <button class="sp-btn-tag primary" type="button">View</button>
                  <button class="sp-btn-tag" type="button">Edit</button>
                  <button class="sp-btn-tag danger" type="button">Archive</button>
                </div>
              </td>
            </tr>
            <tr>
              <td><strong>Field Change: Tue Games</strong><div class="sp-card__sub">Venue updated due to maintenance.</div></td>
              <td>U14</td>
              <td><span class="sp-pill sp-pill--warning">Draft</span></td>
              <td>2026-02-20</td>
              <td>
                <div class="sp-actions">
                  <button class="sp-btn-tag primary" type="button">Publish</button>
                  <button class="sp-btn-tag" type="button">Edit</button>
                  <button class="sp-btn-tag danger" type="button">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="sp-alert" style="margin-top:12px">
        <strong>Nice-to-have:</strong> auto-generate announcements from schedule changes (match rescheduled → notify affected teams & parents).
      </div>
    </div>
  </section>

  <section class="sp-card">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">Media Library</div>
        <div class="sp-card__sub">Logos, league images, and downloadable forms.</div>
      </div>
      <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-upload"></i>&nbsp; Upload</button>
    </div>

    <div class="sp-card__bd">
      <div class="sp-grid" style="grid-template-columns: repeat(2, 1fr); gap:12px;">
        <div class="sp-card" style="padding:14px; border:1px dashed var(--line); box-shadow:none;">
          <div style="font-weight:800">League logo</div>
          <div class="sp-card__sub">U14 Soccer</div>
          <div style="height:12px"></div>
          <button class="sp-btn sp-btn--ghost" type="button" style="width:100%">Replace</button>
        </div>
        <div class="sp-card" style="padding:14px; border:1px dashed var(--line); box-shadow:none;">
          <div style="font-weight:800">Waiver PDF</div>
          <div class="sp-card__sub">Required form</div>
          <div style="height:12px"></div>
          <button class="sp-btn sp-btn--ghost" type="button" style="width:100%">Replace</button>
        </div>
        <div class="sp-card" style="padding:14px; border:1px dashed var(--line); box-shadow:none;">
          <div style="font-weight:800">Sponsor banner</div>
          <div class="sp-card__sub">Homepage carousel</div>
          <div style="height:12px"></div>
          <button class="sp-btn sp-btn--ghost" type="button" style="width:100%">Replace</button>
        </div>
        <div class="sp-card" style="padding:14px; border:1px dashed var(--line); box-shadow:none;">
          <div style="font-weight:800">Coach handbook</div>
          <div class="sp-card__sub">PDF download</div>
          <div style="height:12px"></div>
          <button class="sp-btn sp-btn--ghost" type="button" style="width:100%">Replace</button>
        </div>
      </div>

      <div class="sp-alert" style="margin-top:12px">
        <strong>Space tip:</strong> keep media as "cards" and use an upload drawer (right side panel) for metadata and tags.
      </div>
    </div>
  </section>
</div>

<dialog id="dlgPostCreate" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">New Announcement</div>
    <div class="sp-card__sub">UI-only. Add rich text editor later.</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-12"><label class="sp-card__sub">Title</label><input class="sp-input" style="width:100%" placeholder="Season kickoff info" /></div>
        <div class="sp-col-12"><label class="sp-card__sub">Audience</label><select class="sp-select" style="width:100%"><option>All</option><option>U14</option><option>Coaches</option><option>Parents</option></select></div>
        <div class="sp-col-12"><label class="sp-card__sub">Content</label><input class="sp-input" style="width:100%" placeholder="Write your announcement…" /></div>
      </div>
      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Save Draft (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
