# Deployment Instructions: KISE College Graduation Form

Follow these steps to deploy the graduation application form to `https://graduationform.kisecollege.ac.ke/`.

## 1. Setup Subdomain on cPanel
1. Login to your KISE College cPanel.
2. Navigate to **Domains** > **Subdomains**.
3. Create a new subdomain:
   - **Subdomain:** `graduationform`
   - **Domain:** `kisecollege.ac.ke`
   - **Document Root:** `public_html/graduationform` (or similar)

## 2. Setup Database
1. Go to **MySQL® Databases** in cPanel.
2. Create a new database (e.g., `kise_graduation_db`).
3. Create a new database user and set a strong password.
4. Add the user to the database with **All Privileges**.
5. Open **phpMyAdmin**, select your new database, and import the `schema.sql` file provided.

## 3. Configure the Application
1. Open the [config.php](file:///c:/Users/Administrator/OneDrive/Desktop/graduation_application_forms/config.php) file.
2. Replace the placeholders with your actual database details:
   ```php
   define('DB_USER', 'your_cpanel_db_username');
   define('DB_PASS', 'your_cpanel_db_password');
   define('DB_NAME', 'your_cpanel_db_name');
   ```

## 4. Upload Files
1. Use the cPanel **File Manager** or an FTP client (like FileZilla).
2. Upload the following files to the subdomain's document root folder:
   - `index.html`
   - `apply.html`
   - `style.css`
   - `script.js`
   - `submit.php`
   - `config.php`
   - `.htaccess` (This ensures all traffic is moved to **HTTPS**)
   - **Graduation Images:** `gradute1.jpeg`, `gradute2.jpeg`, `gradute3.jpeg`, `gradute4.png`, `gradute5.png`, `gradute6.png`.
3. Create a folder named `uploads` in the same directory and set its permissions to `777` or `755`.

## 5. Troubleshooting Animations on cPanel
If animations aren't as smooth as they are locally, check these three common causes:

### A. Missing Images
Ensure you uploaded all 6 image files (`gradute1` to `gradute6`) to the same folder as `index.html`. If the background images are missing, the slider will look empty and broken.

### B. Browser Caching
If you've updated your `style.css` or `script.js` files, your browser might still be using the old versions.
- **Fix:** Perform a **Hard Refresh** by pressing `Ctrl + F5` (Windows) or `Cmd + Shift + R` (Mac).
- **Pro Tip:** In `index.html`, add a version number to your links to force the server to load the new versions:
  ```html
  <link rel="stylesheet" href="style.css?v=1.1">
  <script src="script.js?v=1.1"></script>
  ```

### C. Image Size (Performance)
Some of your PNG files are quite large (nearly 2MB). This can cause "lag" while the browser waits for them to load.
- **Fix:** Compress your images using a tool like TinyPNG or convert the PNGs to JPEGs for faster loading times.

### D. File Case Sensitivity
Linux servers (like cPanel) are case-sensitive. Ensure your filenames are exactly `gradute1.jpeg`, not `Gradute1.JPEG`.

## 6. Enable SSL (HTTPS)
1. In cPanel, look for **SSL/TLS Status**.
2. Find your subdomain `graduationform.kisecollege.ac.ke`.
3. If it doesn't have a green lock icon, click **Run AutoSSL**. This will automatically generate a free SSL certificate for you.
4. The `.htaccess` file uploaded in step 4 will then automatically redirect all users to the secure `https://` version.

## 6. Importing Student Data from Spreadsheets
To use the "Smart Lookup" feature, you need to populate the `students_master` table.
1. **Prepare Spreadsheet**: Ensure your Excel or Google Sheet has these columns (in this exact order):
   - `admission_number`
   - `first_name`
   - `middle_name`
   - `last_name`
   - `course`
   - `certificate_level` (Degree, Diploma, or Certificate)
   - `email`
2. **Export to CSV**: Save your spreadsheet as a **CSV (Comma Separated Values)** file.
3. **Import via phpMyAdmin**:
   - Go to cPanel > **phpMyAdmin**.
   - Select the `mvwlkagz_kise_graduation_db` database.
   - Click on the `students_master` table.
   - Click the **Import** tab at the top.
   - Choose your CSV file.
   - Set "Format" to **CSV**.
   - Look for **"Column names in the first line"** (uncheck if your CSV has no header, check if it does).
   - Click **Go**.
