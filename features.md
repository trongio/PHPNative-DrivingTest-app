# Driving Test App - Feature Specification

## Overview

A fully offline NativePHP mobile application for Georgian driving license exam preparation. The app supports multiple user accounts, various test modes, and comprehensive progress tracking.

---

## 1. Core Architecture

### 1.1 Offline-First Design
- [x] App works 100% offline after initial installation
- [x] All questions, images, and signs stored locally in SQLite
- [x] No internet connection required for any functionality
- [x] Data persists across app restarts and updates

### 1.2 Multi-User System
- [x] Support for multiple local user accounts on same device
- [x] Each user has their own:
  - Test history
  - Saved questions
  - Statistics
  - Templates
  - Settings
- [x] Simple password protection (no 2FA needed)
- [x] User can only access their own account data
- [x] Account switching from login screen
- [x] Account creation/deletion managed by users themselves
- [x] No admin dashboard required

---

## 2. License Categories

### 2.1 License Hierarchy (Parent-Child Structure)
Licenses with same question pools are grouped under parent categories.
User interface shows **only parent licenses** for selection.

| Parent | Children | Description | Question Pool |
|--------|----------|-------------|---------------|
| **B** | B1 | Light vehicles, quadricycles, mopeds | Shared |
| **A** | A1, A2 | Motorcycles (all classes) | Shared |
| **C** | C1 | Trucks (all weights) | Shared |
| **D** | D1 | Buses (all sizes) | Shared |
| **T** | S | Tractors, self-propelled machines | Shared |

### 2.2 License Selection UX
- [x] User sees only parent categories (B, A, C, D, T)
- [x] Selecting parent automatically includes all children
- [x] Child categories inherit parent's question pool
- [x] Filter shows parent name (e.g., "B, B1" displayed together)

### 2.3 Category Features
- [x] Separate question pools per parent category
- [ ] Category-specific passing criteria
- [ ] User can select preferred category in settings
- [x] Some questions shared across all categories (general rules)
- [x] Questions tagged with applicable license types

---

## 3. Question Categories (თემები)

The app organizes questions into **46 thematic categories** covering all aspects of driving theory:
- Traffic signs (warning, prohibition, informational, directional)
- Road rules (priority, intersections, overtaking, lane movement)
- Special situations (highway, railway crossings, poor visibility)
- Vehicle operation (braking, towing, skidding)
- Legal aspects (legislation, fines, conventions)
- Safety (children, pedestrians, emergency situations, medical/first aid)
- And more...

*See database seeder for complete category list with Georgian names.*

---

## 4. Road Signs Reference (საგზაო ნიშნები)

A dedicated section for browsing and learning road signs separately from test mode.

### 4.1 Sign Categories
- [x] **მაფრთხილებელი** - Warning signs
- [x] **პრიორიტეტი** - Priority signs
- [x] **ამკრძალავი** - Prohibition signs
- [x] **მიმთითებელი** - Directional signs
- [x] **განსაკუთრებული მითითების** - Special instruction signs
- [x] **სერვისის** - Service signs
- [x] **საინფორმაციო** - Informational signs
- [x] **დამატებითი ინფორმაცია** - Additional information plates
- [x] **საგზაო მონიშვნები** - Road markings

### 4.2 Sign Display
- [x] Grid view of all signs with images
- [x] Sign code number (e.g., 1.1, 1.2, 2.1)
- [x] Sign name in Georgian
- [x] Filter by category (tabs)
- [x] Total sign count display (e.g., "სულ 252 ნიშანი")

### 4.3 Sign Details (Inline Expansion)
- [x] Tap/click sign to expand details inline (no page navigation)
- [x] Expanded view shows below the sign grid
- [x] Sign code and name as header (e.g., "2.1. მთავარი გზა")
- [x] Full description and meaning in Georgian
- [x] Usage rules and placement distances
- [x] Collapse by tapping again or tapping another sign
- [x] Link to related questions about this sign (optional)

### 4.4 Sign Search
- [x] Search signs by name
- [ ] Search by sign code
- [x] Quick filter buttons

### 4.5 Category Information
- [x] Description text for each sign category
- [x] Distance information for warning signs

### 4.6 Notes/Remarks Filter (შენიშვნა)
- [x] Clickable note buttons (შენიშვნა 1, შენიშვნა 2, etc.)
- [x] Clicking a note:
  - Displays the note text/comment
  - Filters sign grid to show only signs mentioned in that note
  - Shows filtered count (e.g., "პრიორიტეტი, 2 ნიშანი")
- [x] Toggle back to show all signs in category
- [x] Multiple notes per category where applicable

---

## 5. Test Modes

### 5.1 Quick Test (სწრაფი ტესტი)
- [ ] Standard exam simulation
- [ ] Default: 30 questions, 30 minutes, 3 mistakes allowed
- [ ] Randomized questions from all categories
- [ ] Mirrors official exam format

### 5.2 Thematic Test (თემატური ტესტი)
- [ ] User selects specific categories to test
- [ ] Configurable question count (10-400)
- [ ] Focus on weak areas
- [ ] Can combine multiple categories
- [ ] Option to include inactive questions for comprehensive practice

### 5.3 Learning Mode / Question Browser (ბილეთები/სასწავლო რეჟიმი)
Browse and practice questions at your own pace without test pressure.

#### 5.3.1 Browsing Interface
- [x] Browse all questions without timer
- [x] Score counter displayed in top-right corner (correct/wrong)
- [x] Answer questions and see correct answer immediately
- [x] Visual feedback: green for correct, red for wrong
- [x] View detailed explanations for each question
- [x] Expandable explanation section per question

#### 5.3.2 Filtering Options
- [x] **Filter by License Type**: Select parent category (B, A, C, D, T)
  - Shows "B, B1" format when B selected
- [x] **Filter by Question Categories**: Select one or multiple of 46 categories
- [x] **Filter by Active Status**: Toggle to include/exclude inactive questions
  - Default: Show only active questions (is_active = true)
  - Option to show all questions (active + inactive)
  - Option to show only inactive questions (for review/learning)
  - Inactive questions visually distinguished (e.g., muted styling, badge)
- [x] **Select All / Deselect All** categories option
- [x] Filters can be combined (e.g., License B + Categories 45, 46 + Inactive)
- [x] Show question count matching current filters
- [x] Remember last used filters

#### 5.3.3 Pagination
- [x] Configurable questions per page (10, 20, 50, 100)
- [x] Previous / Next page navigation
- [x] Page number indicator (e.g., "გვ. 1")
- [x] Jump to specific page
- [x] Total questions count display (e.g., "921 ბილეთი")

#### 5.3.4 Question Status Toggles
- [ ] Toggle: Show/Hide already answered questions
- [x] Toggle: Show only questions answered wrong (session-based)
- [x] Toggle: Show only bookmarked questions
- [ ] Counter showing answered vs total (e.g., "162 ამოხსნილი")
- [ ] Mark questions as "learned" to hide them

#### 5.3.5 Learning Mode Actions
- [x] Bookmark/Save question while browsing
- [x] View question explanation
- [x] Navigate between questions (Previous/Next)
- [ ] Reset session score
- [ ] Switch to Test Mode with current filters

### 5.4 Custom Test from Saved Questions
- [ ] User creates test from their saved/bookmarked questions
- [ ] Prioritize questions with high wrong-answer count
- [ ] Configurable test parameters

---

## 6. Test Configuration

### 6.1 Timer Settings
- [ ] Default: 1 minute per question
- [ ] Configurable time per question (30s - 3min)
- [ ] Total test time = questions × time per question
- [ ] Timer continues past zero (goes negative)
- [ ] Negative time displayed in red
- [ ] Test marked as failed when time exceeds limit

### 6.2 Failure Threshold
- [ ] Default: 10% wrong answers = fail
- [ ] Configurable per test/template (5% - 30%)
- [ ] Example: 30 questions with 10% threshold = 3 mistakes allowed
- [ ] Real-time tracking during test

### 6.3 On Test Failure
- [ ] Show failure notification immediately
- [ ] Display current score and time
- [ ] Options:
  - **Continue Test** - Complete remaining questions for practice
  - **Finish Test** - End and save results
- [ ] Timer continues in negative (shown in red)
- [ ] Final results show "FAILED" status clearly

---

## 7. Templates

### 7.1 Template Creation
- [ ] User-defined test configurations
- [ ] Template settings:
  - Name
  - License category
  - Question count
  - Time per question
  - Failure threshold (default 10%)
  - Selected question categories
  - Include/exclude specific questions
- [ ] Save unlimited templates

### 7.2 Template Management
- [ ] Edit existing templates
- [ ] Duplicate templates
- [ ] Delete templates
- [ ] Share templates between users (optional)

### 7.3 Category Selection in Templates
- [ ] Select from all 46 categories
- [ ] Select all / Deselect all
- [ ] Show question count per category
- [ ] Mix categories as needed

---

## 8. Question Saving & Bookmarks

### 8.1 Save Questions
- [x] Bookmark any question during test or learning mode
- [x] Quick save button on each question
- [ ] Organize saved questions by category
- [ ] Add personal notes to saved questions

### 8.2 Wrong Answer Tracking
- [x] Counter for each question: times answered wrong
- [x] Counter for times answered correctly
- [x] Accuracy percentage per question (in database)
- [ ] Sort saved questions by:
  - Most wrong answers
  - Recently added
  - Category
  - Difficulty (based on wrong count)

### 8.3 Saved Questions Actions
- [x] Review saved questions list (via bookmarked filter)
- [x] Remove questions from saved
- [ ] Create custom test from saved questions
- [ ] Clear wrong answer counter
- [ ] Export saved questions (optional)

---

## 9. Test History

### 9.1 History Records
Each completed test saves:
- [ ] Date and time
- [ ] Test type (Quick/Thematic/Custom/Template)
- [ ] Score (correct/total)
- [ ] Pass/Fail status
- [ ] Time taken (including negative time if any)
- [ ] Categories included
- [ ] Individual question results

### 9.2 History Actions
- [ ] **View** - See detailed results with all questions
- [ ] **Retake** - Start same test again (same questions)
- [ ] **Retake Similar** - Same configuration, different questions
- [ ] **Delete** - Remove from history
- [ ] Filter history by:
  - Date range
  - Pass/Fail
  - Test type
  - Category

### 9.3 Question Review
- [ ] See each question with user's answer
- [ ] Highlight correct/wrong answers
- [ ] Show correct answer for wrong responses
- [ ] View explanation for each question
- [ ] Save questions directly from history review

---

## 10. Statistics & Analytics

### 10.1 Overview Dashboard
- [ ] Total tests taken
- [ ] Overall pass rate (%)
- [ ] Total questions answered
- [ ] Overall accuracy (%)
- [ ] Current streak (days)
- [ ] Best streak record

### 10.2 Time-Based Statistics
- [ ] Tests per day/week/month
- [ ] Average score trend over time
- [ ] Time spent studying (daily/weekly/monthly)
- [ ] Progress graphs and charts
- [ ] Calendar view of activity

### 10.3 Category Mastery
- [ ] Accuracy % per category
- [ ] Questions answered per category
- [ ] Weakest categories (for targeted practice)
- [ ] Strongest categories
- [ ] Category improvement over time
- [ ] Visual mastery indicators (progress bars)

### 10.4 Question Analytics
- [ ] Most frequently wrong questions
- [ ] Questions never answered correctly
- [ ] Questions with improving accuracy
- [ ] Time spent per question (average)

### 10.5 Achievements & Milestones
- [ ] First test completed
- [ ] First passed test
- [ ] 10/50/100 tests completed
- [ ] 7-day streak
- [ ] 30-day streak
- [ ] Category mastery (90%+ in category)
- [ ] Perfect score achievement

---

## 11. User Interface

### 11.1 Main Navigation
- [x] Home/Dashboard
- [ ] Test Modes
- [x] Learning Mode (Question Browser)
- [x] Road Signs Reference
- [x] Saved Questions (via bookmarked filter)
- [ ] History
- [ ] Statistics
- [ ] Templates
- [x] Settings
- [x] User Profile/Switch

### 11.2 Test Interface
- [ ] Question number indicator (e.g., 15/30)
- [ ] Timer display (prominent)
- [ ] Timer turns red when negative
- [ ] Progress bar
- [ ] Current score
- [ ] Wrong answer count vs allowed
- [x] Question image (when applicable)
- [x] Answer options (2-4 choices)
- [x] Navigation: Previous/Next/Skip
- [ ] End test button
- [x] Bookmark/Save question button

### 11.3 Question Display
- [x] Clear question text
- [x] High-quality images for visual questions
- [x] Road signs displayed clearly
- [x] Answer options clearly numbered
- [x] Visual feedback on selection
- [x] Correct/Wrong indication after answer

### 11.4 Theme & Accessibility
- [x] Light/Dark mode
- [ ] Font size adjustment
- [ ] High contrast mode (optional)
- [x] Georgian language interface

---

## 12. Settings

### 12.1 Test Defaults
- [ ] Default time per question
- [ ] Default failure threshold
- [ ] Default question count
- [ ] Preferred license category
- [ ] Auto-advance after answer
- [ ] Sound effects on/off
- [ ] Vibration feedback on/off

### 12.2 Account Settings
- [x] Change password
- [x] Change display name
- [x] Delete account (with confirmation)
- [ ] Export user data (optional)
- [ ] Reset statistics

### 12.3 App Settings
- [x] Language (Georgian primary)
- [x] Theme (Light/Dark/System)
- [ ] Notification preferences
- [ ] Data management (clear cache)

---

## 13. Data Structure

All database tables are implemented ✅

### 13.1 License Categories Table ✅
- License ID (primary key)
- Code (e.g., "B", "B1", "A")
- Name (Georgian)
- Parent ID (nullable, for child licenses)
- Description
- Is Active

### 13.2 Question Categories Table ✅
- Category ID (primary key)
- Name (Georgian)
- Name (English)
- Sort Order
- Question Count (calculated)
- Is Active

### 13.3 Questions Table ✅
- Question ID (primary key)
- Question text (Georgian)
- Image path (nullable)
- Category ID (foreign key → Question Categories)
- License IDs (array of parent license IDs)
- Answer options (JSON array)
- Correct answer index (0-based)
- Explanation text (Georgian)
- Difficulty level (calculated from user stats)
- Is Active (boolean, allows filtering active/inactive questions)
- Created at
- Updated at

### 13.4 User Accounts Table ✅
- User ID (primary key)
- Username
- Display Name
- Password Hash
- Preferred License ID
- Settings (JSON)
- Created at
- Last login at

### 13.5 User Question Progress Table ✅
- Progress ID (primary key)
- User ID (foreign key)
- Question ID (foreign key)
- Times answered correctly
- Times answered wrong
- Accuracy percentage (calculated)
- Is bookmarked (boolean)
- Is learned (boolean, hides from browse)
- Personal notes (text)
- Last answered at
- First answered at

### 13.6 Test Templates Table ✅
- Template ID (primary key)
- User ID (foreign key)
- Name
- License ID (foreign key)
- Question Count
- Time per question (seconds)
- Failure threshold (percentage)
- Category IDs (JSON array)
- Excluded Question IDs (JSON array, optional)
- Created at
- Updated at

### 13.7 Test Results Table ✅
- Test ID (primary key)
- User ID (foreign key)
- Template ID (nullable, foreign key)
- Test type (enum: quick, thematic, custom, template)
- License ID
- Configuration (JSON)
- Questions with answers (JSON)
- Correct count
- Wrong count
- Total questions
- Score percentage
- Pass/Fail status (enum)
- Start time
- End time
- Time taken (seconds, can be negative)
- Created at

### 13.8 User Statistics Table (Aggregated) ✅
- User ID (primary key, foreign key)
- Total tests taken
- Total tests passed
- Total tests failed
- Total questions answered
- Total correct answers
- Overall accuracy
- Current streak (days)
- Best streak (days)
- Last activity date
- Total study time (seconds)
- Updated at

---

## 14. Future Considerations

### 14.1 Potential Additions
- [ ] Audio questions (for accessibility)
- [ ] Video explanations
- [ ] Practice exam scheduling/reminders
- [ ] Cloud sync (optional, for backup)
- [ ] Multiple language support
- [ ] Social features (compare with friends)
- [ ] Official exam date countdown

### 14.2 Out of Scope (Current Version)
- Online multiplayer
- Admin dashboard
- Payment/subscription system
- External API integration
- Real-time updates

---

## 15. Technical Requirements

### 15.1 Platform
- NativePHP Mobile (iOS & Android)
- Laravel 12 backend
- React 19 frontend (Inertia.js v2)
- SQLite local database

### 15.2 Performance
- App launch < 2 seconds
- Question load < 100ms
- Smooth animations (60fps)
- Minimal battery usage

### 15.3 Storage
- All questions stored locally
- Images optimized for mobile
- Estimated app size: 50-100MB

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 0.1 | 2025-01-15 | Initial feature specification |