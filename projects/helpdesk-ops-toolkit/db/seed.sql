-- =====================================================================
-- Helpdesk Ops Toolkit — seed data (portable: MySQL + SQLite)
-- Import after the schema:  mysql -u USER -p DBNAME < db/seed.sql
-- All sample data is fictional. Departments are generic on purpose.
-- =====================================================================

DELETE FROM hot_tickets;
DELETE FROM hot_assets;
DELETE FROM hot_kb_articles;
DELETE FROM hot_users;
DELETE FROM hot_departments;

-- ---- Departments ----------------------------------------------------
INSERT INTO hot_departments (name) VALUES
  ('IT'),
  ('Public Works'),
  ('Social Services'),
  ('Parks & Recreation'),
  ('Finance'),
  ('Human Resources'),
  ('Library Services'),
  ('Permits & Licensing');

-- ---- Users ----------------------------------------------------------
INSERT INTO hot_users (name, role, department_id) VALUES
  ('A. Rivera',   'support_agent', 1),
  ('M. Chen',     'support_agent', 1),
  ('D. Okafor',   'support_agent', 1),
  ('S. Patel',    'support_agent', 1),
  ('J. Nguyen',   'admin',         1),
  ('R. Delgado',  'staff',         2),
  ('T. Brooks',   'staff',         3),
  ('L. Marsh',    'staff',         4),
  ('K. Osei',     'staff',         5),
  ('P. Romano',   'staff',         6);

-- ---- Tickets (40) ---------------------------------------------------
INSERT INTO hot_tickets
  (title, description, category, priority, status, requester_name, requester_department, assigned_to, created_at, resolved_at)
VALUES
  ('Monitor will not power on', 'Second monitor at the front desk stopped displaying after a power outage.', 'hardware', 'medium', 'resolved', 'R. Delgado', 'Public Works', 'A. Rivera', '2026-05-12 08:42:00', '2026-05-12 10:15:00'),
  ('Cannot log in to timekeeping', 'Account locked after several failed attempts; needs unlock and reset.', 'account_access', 'high', 'closed', 'T. Brooks', 'Social Services', 'M. Chen', '2026-05-12 09:05:00', '2026-05-12 09:40:00'),
  ('Printer on 2nd floor jammed repeatedly', 'Shared printer jams on every job; likely worn feed roller.', 'hardware', 'low', 'closed', 'L. Marsh', 'Parks & Recreation', 'D. Okafor', '2026-05-13 11:20:00', '2026-05-15 14:30:00'),
  ('VPN disconnects every few minutes', 'Remote worker drops off VPN roughly every 5 minutes.', 'network', 'high', 'resolved', 'K. Osei', 'Finance', 'S. Patel', '2026-05-14 13:10:00', '2026-05-16 16:05:00'),
  ('New hire needs email and file access', 'Onboarding for a new caseworker starting Monday.', 'account_access', 'medium', 'closed', 'P. Romano', 'Human Resources', 'A. Rivera', '2026-05-15 10:00:00', '2026-05-15 15:20:00'),
  ('Spreadsheet macro throws an error', 'Budget workbook macro fails with a reference error since the update.', 'software', 'medium', 'resolved', 'K. Osei', 'Finance', 'M. Chen', '2026-05-18 09:30:00', '2026-05-19 11:00:00'),
  ('Wi-Fi weak in the annex', 'Signal drops in the back offices of the annex building.', 'network', 'low', 'open', 'L. Marsh', 'Parks & Recreation', NULL, '2026-05-19 14:45:00', NULL),
  ('Laptop battery drains in an hour', 'Field laptop no longer holds a charge; battery likely failing.', 'hardware', 'medium', 'in_progress', 'R. Delgado', 'Public Works', 'D. Okafor', '2026-05-20 08:15:00', NULL),
  ('Shared drive folder missing', 'A permits folder disappeared from the shared drive overnight.', 'software', 'high', 'resolved', 'T. Brooks', 'Permits & Licensing', 'S. Patel', '2026-05-21 10:50:00', '2026-05-21 13:15:00'),
  ('Request software install: PDF editor', 'Needs a licensed PDF editor to redact case documents.', 'software', 'low', 'closed', 'T. Brooks', 'Social Services', 'A. Rivera', '2026-05-22 09:10:00', '2026-05-26 10:00:00'),
  ('Desk phone has no dial tone', 'VoIP phone shows connected but has no dial tone.', 'network', 'medium', 'resolved', 'P. Romano', 'Human Resources', 'M. Chen', '2026-05-26 11:25:00', '2026-05-27 09:45:00'),
  ('Password reset for retiring staff', 'Temporary access needed during knowledge transfer.', 'account_access', 'low', 'closed', 'P. Romano', 'Human Resources', 'A. Rivera', '2026-05-27 15:30:00', '2026-05-28 08:20:00'),
  ('Keyboard keys sticking', 'Several keys stick on a front-counter keyboard.', 'hardware', 'low', 'closed', 'L. Marsh', 'Library Services', 'D. Okafor', '2026-05-28 13:00:00', '2026-05-29 10:10:00'),
  ('Cannot open shared calendar', 'Permission error when opening the department calendar.', 'software', 'medium', 'resolved', 'K. Osei', 'Finance', 'S. Patel', '2026-05-29 09:40:00', '2026-05-29 14:25:00'),
  ('Internet down in the whole wing', 'No connectivity across the east wing since this morning.', 'network', 'high', 'closed', 'R. Delgado', 'Public Works', 'J. Nguyen', '2026-06-01 08:05:00', '2026-06-01 11:30:00'),
  ('Docking station not detecting monitors', 'Laptop dock stopped driving external displays.', 'hardware', 'medium', 'in_progress', 'K. Osei', 'Finance', 'D. Okafor', '2026-06-02 10:20:00', NULL),
  ('Email flagged as full', 'Mailbox over quota; cannot send or receive.', 'software', 'medium', 'resolved', 'T. Brooks', 'Social Services', 'M. Chen', '2026-06-03 09:15:00', '2026-06-03 12:40:00'),
  ('Two-factor device replaced', 'Lost phone with the authenticator; needs re-enrollment.', 'account_access', 'high', 'closed', 'P. Romano', 'Human Resources', 'A. Rivera', '2026-06-04 08:50:00', '2026-06-04 10:05:00'),
  ('Scanner not recognized', 'Flatbed scanner no longer appears in the software.', 'hardware', 'low', 'open', 'L. Marsh', 'Permits & Licensing', NULL, '2026-06-05 14:10:00', NULL),
  ('Slow performance on shared app', 'Records app is sluggish for everyone in the office.', 'software', 'medium', 'in_progress', 'T. Brooks', 'Social Services', 'S. Patel', '2026-06-08 09:00:00', NULL),
  ('New monitor request for standing desk', 'Ergonomic setup needs a second monitor.', 'hardware', 'low', 'open', 'K. Osei', 'Finance', NULL, '2026-06-09 13:35:00', NULL),
  ('Guest Wi-Fi password for meeting', 'Need a temporary guest code for a council meeting.', 'network', 'low', 'closed', 'L. Marsh', 'Parks & Recreation', 'M. Chen', '2026-06-10 08:30:00', '2026-06-10 08:55:00'),
  ('Account access for transferred employee', 'Staff moved from Finance to HR; needs updated groups.', 'account_access', 'medium', 'resolved', 'P. Romano', 'Human Resources', 'A. Rivera', '2026-06-11 10:15:00', '2026-06-11 15:45:00'),
  ('Projector in conference room B fuzzy', 'Image out of focus and flickering during presentations.', 'hardware', 'low', 'open', 'R. Delgado', 'Public Works', NULL, '2026-06-12 11:05:00', NULL),
  ('Cannot print to network printer', 'Print jobs stay queued and never release.', 'network', 'medium', 'resolved', 'T. Brooks', 'Permits & Licensing', 'D. Okafor', '2026-06-15 09:20:00', '2026-06-15 13:10:00'),
  ('Software crashes on export', 'GIS tool crashes when exporting a large map.', 'software', 'high', 'in_progress', 'R. Delgado', 'Public Works', 'S. Patel', '2026-06-16 08:40:00', NULL),
  ('Request shared mailbox access', 'Team needs access to the intake shared mailbox.', 'account_access', 'low', 'closed', 'T. Brooks', 'Social Services', 'M. Chen', '2026-06-17 14:00:00', '2026-06-18 09:30:00'),
  ('Mouse double-clicking on single click', 'Faulty mouse registering double clicks.', 'hardware', 'low', 'closed', 'K. Osei', 'Finance', 'D. Okafor', '2026-06-18 10:25:00', '2026-06-18 11:00:00'),
  ('Website contact form not sending', 'Public contact form on the department site returns an error.', 'software', 'high', 'in_progress', 'L. Marsh', 'Library Services', 'M. Chen', '2026-06-19 09:10:00', NULL),
  ('VPN access for new remote role', 'Newly approved telework arrangement needs VPN.', 'account_access', 'medium', 'resolved', 'P. Romano', 'Human Resources', 'A. Rivera', '2026-06-22 08:35:00', '2026-06-22 12:15:00'),
  ('Conference phone echo', 'Callers report an echo on the conference room phone.', 'network', 'low', 'open', 'K. Osei', 'Finance', NULL, '2026-06-23 13:20:00', NULL),
  ('Replace failing hard drive', 'Desktop reporting imminent drive failure warnings.', 'hardware', 'high', 'in_progress', 'R. Delgado', 'Public Works', 'D. Okafor', '2026-06-24 09:05:00', NULL),
  ('Cannot access budget dashboard', 'Permission denied opening the finance dashboard.', 'software', 'medium', 'resolved', 'K. Osei', 'Finance', 'S. Patel', '2026-06-25 10:40:00', '2026-06-25 14:50:00'),
  ('Set up loaner laptop for event', 'Need a configured loaner for an offsite outreach event.', 'hardware', 'low', 'closed', 'L. Marsh', 'Parks & Recreation', 'A. Rivera', '2026-06-26 08:15:00', '2026-06-26 15:00:00'),
  ('Email rules not working', 'Inbox rules stopped filtering after the client update.', 'software', 'low', 'open', 'T. Brooks', 'Social Services', NULL, '2026-06-29 09:45:00', NULL),
  ('Network share slow to open', 'Opening files on the share takes 30+ seconds.', 'network', 'medium', 'in_progress', 'P. Romano', 'Human Resources', 'S. Patel', '2026-06-30 11:10:00', NULL),
  ('Unlock account after leave', 'Account disabled during extended leave; returning today.', 'account_access', 'medium', 'resolved', 'R. Delgado', 'Public Works', 'M. Chen', '2026-07-01 08:25:00', '2026-07-01 09:15:00'),
  ('Webcam not working for meetings', 'Built-in webcam not detected in the meeting app.', 'hardware', 'low', 'open', 'K. Osei', 'Finance', NULL, '2026-07-02 13:05:00', NULL),
  ('Bulk password resets after phishing test', 'Several accounts flagged during a phishing awareness test.', 'account_access', 'high', 'in_progress', 'P. Romano', 'Human Resources', 'J. Nguyen', '2026-07-06 08:50:00', NULL),
  ('Add printer for new office layout', 'Reorganized office needs a printer mapped for the pod.', 'other', 'low', 'open', 'L. Marsh', 'Library Services', NULL, '2026-07-08 10:30:00', NULL);

-- ---- Assets (15) ----------------------------------------------------
INSERT INTO hot_assets
  (asset_tag, type, make_model, assigned_to, assigned_department, status, acquired_date)
VALUES
  ('IT-LT-0101', 'laptop',     'Latitude 5440',        'R. Delgado', 'Public Works',        'in_use',  '2024-03-11'),
  ('IT-LT-0102', 'laptop',     'Latitude 5440',        'K. Osei',    'Finance',             'in_use',  '2024-03-11'),
  ('IT-LT-0103', 'laptop',     'ThinkPad T14',         NULL,         NULL,                  'surplus', '2021-06-02'),
  ('IT-DT-0201', 'desktop',    'OptiPlex 7010',        'T. Brooks',  'Social Services',     'in_use',  '2023-09-20'),
  ('IT-DT-0202', 'desktop',    'OptiPlex 7010',        'P. Romano',  'Human Resources',     'in_use',  '2023-09-20'),
  ('IT-DT-0203', 'desktop',    'EliteDesk 800 G6',     NULL,         NULL,                  'repair',  '2022-01-14'),
  ('IT-MN-0301', 'monitor',    'Dell P2422H 24"',      'R. Delgado', 'Public Works',        'in_use',  '2024-03-11'),
  ('IT-MN-0302', 'monitor',    'Dell P2422H 24"',      'K. Osei',    'Finance',             'in_use',  '2024-03-11'),
  ('IT-MN-0303', 'monitor',    'HP E24 G5 24"',        NULL,         NULL,                  'surplus', '2020-11-05'),
  ('IT-MN-0304', 'monitor',    'HP E24 G5 24"',        'L. Marsh',   'Parks & Recreation',  'in_use',  '2023-05-19'),
  ('IT-PR-0401', 'peripheral', 'Brother HL-L2400 Printer', NULL,     'Library Services',    'in_use',  '2023-02-28'),
  ('IT-PR-0402', 'peripheral', 'Logitech MX Keys',     'K. Osei',    'Finance',             'in_use',  '2024-07-01'),
  ('IT-PR-0403', 'peripheral', 'Epson DS-575W Scanner', NULL,        'Permits & Licensing', 'repair',  '2022-10-10'),
  ('IT-LT-0104', 'laptop',     'ThinkPad T14',         NULL,         NULL,                  'retired', '2019-04-22'),
  ('IT-DT-0204', 'desktop',    'OptiPlex 5000',        'T. Brooks',  'Social Services',     'in_use',  '2024-11-30');

-- ---- Knowledge base (6) --------------------------------------------
INSERT INTO hot_kb_articles (title, body, category, audience, created_at, updated_at) VALUES
  ('My monitor won''t turn on',
   '# My monitor won''t turn on\n\nBefore submitting a ticket, try these quick steps. Most dark-screen problems are fixed in under a minute.\n\n## Check the power\n- Make sure the monitor''s power button is on. A small light usually glows blue or amber when it has power.\n- Confirm the power cable is firmly seated at **both** the monitor and the wall outlet.\n- If it is plugged into a power strip, make sure the strip is switched on.\n\n## Check the connection to your computer\n- Wiggle the video cable at both ends and press it in firmly.\n- If you have a second cable, try swapping it.\n\n## Still dark?\nIf the light is on but the screen stays black, **submit a ticket** and include your monitor''s asset tag (the small IT- sticker on the back). We''ll take it from there.',
   'hardware', 'end_user', '2026-05-10 09:00:00', '2026-05-10 09:00:00'),

  ('How to request software access',
   '# How to request software access\n\nNeed a program installed, or access to a system you can''t open yet? Here''s how to ask for it.\n\n## What to include\n- The **name of the software** or system (for example, the budget dashboard).\n- **Why you need it** in one sentence — this helps us approve it quickly.\n- Your **department** and, if you know it, your supervisor''s name for approval.\n\n## How to submit\n1. Open the helpdesk and choose **Submit a ticket**.\n2. Set the category to **Account access**.\n3. Paste the details above into the description.\n\nMost access requests are handled within one to two business days. Requests that need supervisor approval may take a little longer.',
   'account_access', 'end_user', '2026-05-10 09:10:00', '2026-05-10 09:10:00'),

  ('Connecting to the office Wi-Fi',
   '# Connecting to the office Wi-Fi\n\nUse these steps to get your work device onto the network.\n\n## On a work laptop\n1. Click the Wi-Fi icon in the bottom corner of your screen.\n2. Choose the network named **Staff-Secure**.\n3. Sign in with your usual username and password.\n\n## Guests and visitors\nVisitors should use the **Guest** network. Ask the front desk for the day''s guest code — it changes daily for security.\n\n## If it won''t connect\n- Turn Wi-Fi off and back on.\n- Restart the laptop.\n- Still stuck? Submit a ticket with the **Network** category and tell us which building and floor you''re on.',
   'network', 'end_user', '2026-05-10 09:20:00', '2026-05-10 09:20:00'),

  ('Password reset and account lockout',
   '# Password reset and account lockout\n\nLocked out, or your password expired? Here''s what to do.\n\n## If you know your password but it expired\n- When prompted, choose a new password that is at least 12 characters and hasn''t been used before.\n\n## If you''re locked out\nAccounts lock automatically after several failed sign-in attempts, to protect your information.\n- Wait 15 minutes and try again, or\n- **Submit a ticket** with the **Account access** category so an agent can unlock it right away.\n\n## Keeping your account safe\n- Never share your password, even with IT — we will never ask for it.\n- If you think your account was used by someone else, tell us immediately.',
   'account_access', 'end_user', '2026-05-10 09:30:00', '2026-05-10 09:30:00'),

  ('Writing a support ticket that gets solved faster',
   '# Writing a support ticket that gets solved faster\n\nA clear ticket means a faster fix. Here''s what helps us most.\n\n## Include\n- **What happened**, in plain words ("the screen is black," not "hardware failure").\n- **When it started** and whether it happens every time.\n- Any **error message** — a photo of the screen is perfect.\n- Your **location**: building, floor, and desk or room.\n\n## Pick the right category\n- **Hardware** — physical equipment (monitor, laptop, printer).\n- **Software** — a program or website.\n- **Network** — Wi-Fi, internet, or phones.\n- **Account access** — passwords, logins, permissions.\n\nThat''s it. The more of this you can include up front, the fewer back-and-forth messages it takes to get you working again.',
   'general', 'end_user', '2026-05-10 09:40:00', '2026-05-10 09:40:00'),

  ('Standard laptop imaging checklist',
   '# Standard laptop imaging checklist (technical)\n\nInternal reference for support agents preparing a laptop for issue.\n\n## Before imaging\n- Record the **asset tag** and model in the asset tracker; set status to `in_use` once assigned.\n- Confirm the device is on the current hardware standard; flag end-of-life units for `surplus`.\n\n## Imaging\n1. Apply the current base image.\n2. Join the domain and confirm group policy applies.\n3. Install the standard software set and pending updates.\n4. Verify full-disk encryption is on and the recovery key is escrowed.\n\n## Handoff\n- Map the user''s department printer and shared drives.\n- Walk the user through Wi-Fi (`Staff-Secure`) and password expiry.\n- Log the issue in the asset tracker with the assigned user and department.',
   'hardware', 'technical', '2026-05-10 09:50:00', '2026-05-10 09:50:00');
