<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Matches';
$activeNav = 'matches';
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Matches</div>
      <div class="sp-card__sub">Create matches, set locations, manage results, and publish standings.</div>
    </div>

    <div class="sp-actions">
      <div class="sp-viewtabs sp-viewtabs--light" data-viewtabs title="Switch view">
        <button type="button" data-view="list" class="active"><i class="fa-solid fa-list"></i> List</button>
        <button type="button" data-view="calendar"><i class="fa-solid fa-calendar-week"></i> Week</button>
      </div>
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgMatchCreate"><i class="fa-solid fa-plus"></i>&nbsp; Create Match</button>
      <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-location-dot"></i>&nbsp; Venues</button>
      <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-calendar-days"></i>&nbsp; Open Schedule</button>
    </div>
  </div>

  <div class="sp-card__bd">

    <!-- LIST VIEW -->
    <section data-viewpane="list">
    <div class="sp-kpis" style="margin-bottom:14px;">
      <div class="sp-kpi"><div class="label">Upcoming</div><div class="value">24</div><div class="meta">next 14 days</div></div>
      <div class="sp-kpi"><div class="label">Completed</div><div class="value">112</div><div class="meta">season total</div></div>
      <div class="sp-kpi"><div class="label">Needs Results</div><div class="value">7</div><div class="meta">awaiting score</div></div>
      <div class="sp-kpi"><div class="label">Cancelled</div><div class="value">3</div><div class="meta">weather / conflict</div></div>
    </div>

    <div class="sp-filterbar">
      <div class="sp-filterbar__left">
        <div class="sp-search">
          <i class="fa-solid fa-magnifying-glass icon"></i>
          <input data-table-search="#tblMatches" type="text" placeholder="Search teams, location, league…" />
        </div>

        <select class="sp-select" data-table-filter="#tblMatches" data-col="1">
          <option value="">All leagues</option>
          <option>U14</option>
          <option>U16</option>
          <option>U18</option>
        </select>

        <select class="sp-select" data-table-filter="#tblMatches" data-col="5">
          <option value="">Any status</option>
          <option>Upcoming</option>
          <option>Completed</option>
          <option>Cancelled</option>
        </select>
      </div>

      <div class="sp-filterbar__right">
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-file-export"></i>&nbsp; Export</button>
      </div>
    </div>

    <div style="height:12px"></div>

    <div class="sp-table-wrap" style="max-height: 560px; border:1px solid var(--line)">
      <table id="tblMatches" class="sp-table sp-table--light">
        <thead>
          <tr>
            <th style="width:140px">Date & Time</th>
            <th style="width:90px">League</th>
            <th>Match</th>
            <th>Location</th>
            <th style="width:120px">Score</th>
            <th style="width:120px">Status</th>
            <th style="width:220px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>2026-03-03</strong><div class="sp-card__sub">18:00</div></td>
            <td>U14</td>
            <td><strong>FC Miami Blue</strong> vs FC New York</td>
            <td>Field A (North Campus)</td>
            <td>—</td>
            <td><span class="sp-pill sp-pill--success">Upcoming</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">Edit</button>
                <button class="sp-btn-tag" type="button">Notify</button>
                <button class="sp-btn-tag danger" type="button">Cancel</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><strong>2026-03-10</strong><div class="sp-card__sub">19:30</div></td>
            <td>U16</td>
            <td><strong>LA United</strong> vs FC Miami Blue</td>
            <td>Downtown Arena</td>
            <td>2 : 1</td>
            <td><span class="sp-pill">Completed</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">Results</button>
                <button class="sp-btn-tag" type="button">Stats</button>
                <button class="sp-btn-tag danger" type="button">Revert</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><strong>2026-03-14</strong><div class="sp-card__sub">17:00</div></td>
            <td>U18</td>
            <td><strong>FC New York</strong> vs LA United</td>
            <td>Field C</td>
            <td>—</td>
            <td><span class="sp-pill sp-pill--danger">Cancelled</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag" type="button">Details</button>
                <button class="sp-btn-tag" type="button">Reschedule</button>
                <button class="sp-btn-tag danger" type="button">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="sp-alert" style="margin-top:12px">
      <strong>Space optimization:</strong> keep the match row compact (date, league, teams, status), and open a side panel for heavy editing (venue map, rosters, stats).
    </div>
    </section>

    <!-- CALENDAR VIEW -->
    <section data-viewpane="calendar" hidden>
      <div class="sp-filterbar" style="margin-bottom:12px;">
        <div class="sp-filterbar__left">
          <span class="sp-pill"><i class="fa-solid fa-calendar-days"></i>&nbsp; Week of Mar 2–8</span>
          <select class="sp-select" style="height:38px"><option>All leagues</option><option>U14</option><option>U16</option><option>U18</option></select>
          <select class="sp-select" style="height:38px"><option>All venues</option><option>Field A</option><option>Downtown Arena</option><option>Field C</option></select>
          <select class="sp-select" style="height:38px"><option>Any status</option><option>Upcoming</option><option>Completed</option><option>Cancelled</option></select>
        </div>
        <div class="sp-filterbar__right">
          <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-chevron-left"></i></button>
          <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-chevron-right"></i></button>
          <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-bullhorn"></i>&nbsp; Notify</button>
        </div>
      </div>

      <div class="sp-calendar" aria-label="Weekly match schedule">
        <div></div>
        <div class="day-hd">MON 2</div>
        <div class="day-hd">TUE 3</div>
        <div class="day-hd">WED 4</div>
        <div class="day-hd">THU 5</div>
        <div class="day-hd">FRI 6</div>
        <div class="day-hd">SAT 7</div>
        <div class="day-hd">SUN 8</div>

        <div class="time-col">
          <div class="time-slot">16:00</div>
          <div class="time-slot">18:00</div>
          <div class="time-slot">20:00</div>
        </div>

        <div class="day"></div>
        <div class="day">
          <div class="sp-event sp-event--match">
            <div class="t">U14 • FC Miami Blue vs FC New York</div>
            <div class="m">18:00 • Field A</div>
          </div>
        </div>
        <div class="day"></div>
        <div class="day"></div>
        <div class="day">
          <div class="sp-event sp-event--match">
            <div class="t">U16 • LA United vs FC Miami Blue</div>
            <div class="m">19:30 • Downtown Arena</div>
          </div>
        </div>
        <div class="day"></div>
        <div class="day"></div>
      </div>

      <div class="sp-alert" style="margin-top:12px">
        <strong>Admin autonomy idea:</strong> auto-generate fixtures from teams + home/away rules, then drag/drop (later) to resolve conflicts.
      </div>
    </section>
  </div>
</section>

<dialog id="dlgMatchCreate" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Create Match</div>
    <div class="sp-card__sub">UI-only. Add validation when wired to backend.</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-6"><label class="sp-card__sub">League</label><select class="sp-select" style="width:100%"><option>U14</option><option>U16</option><option>U18</option></select></div>
        <div class="sp-col-6"><label class="sp-card__sub">Venue</label><input class="sp-input" style="width:100%" placeholder="Field A" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Home</label><input class="sp-input" style="width:100%" placeholder="Team" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Away</label><input class="sp-input" style="width:100%" placeholder="Team" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Date</label><input class="sp-input" style="width:100%" placeholder="YYYY-MM-DD" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Time</label><input class="sp-input" style="width:100%" placeholder="HH:MM" /></div>
        <div class="sp-col-12"><label class="sp-card__sub">Notes</label><input class="sp-input" style="width:100%" placeholder="Referee, bracket round, etc." /></div>
      </div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Create (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
