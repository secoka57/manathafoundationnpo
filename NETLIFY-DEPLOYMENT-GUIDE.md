# Netlify Deployment Guide for Manatha Foundation Website

Your website is now ready to be deployed on Netlify! Here's what was done and how to deploy:

## ✅ Changes Made for Netlify Compatibility

### 1. Created `netlify.toml` Configuration File

- Added proper build configuration
- Set up redirects from old PHP files to HTML pages
- Added security headers for better protection

### 2. Updated Forms for Netlify Forms

- **contact.html**: Updated contact form to use Netlify Forms (removed PHP dependency)
- **volunteer.html**: Updated volunteer form to use Netlify Forms (removed PHP dependency)
- Both forms include:
  - `data-netlify="true"` for Netlify to detect
  - Honeypot spam protection
  - Proper form naming
  - Hidden bot field to prevent spam submissions

### 3. Forms that already worked:

- **donate.html**: Uses formsubmit.co - continues to work perfectly
- **index.html**: Uses formsubmit.co - continues to work perfectly

## 🚀 How to Deploy on Netlify

### Step 1: Push your code to GitHub/GitLab/Bitbucket

Make sure all the updated files are in your repository.

### Step 2: Connect to Netlify

1. Go to [Netlify](https://www.netlify.com/) and sign up/login
2. Click "Add new site" > "Import an existing project"
3. Connect your Git repository
4. Configure build settings:
   - **Build command**: Leave empty (it's a static site)
   - **Publish directory**: `./` (root)
5. Click "Deploy site"

### Step 3: Enable Netlify Forms

1. In your Netlify site dashboard, go to "Forms"
2. Netlify will automatically detect your forms
3. You'll receive email notifications when someone submits a form
4. You can view all submissions in the Netlify dashboard

### Step 4: Set up your custom domain (optional)

1. In Netlify dashboard, go to "Site settings" > "Domain management"
2. Add your custom domain (majitatalking.co.za)
3. Follow Netlify's DNS configuration instructions

## 📧 Form Notifications

- Contact form submissions will be sent to the email you set in Netlify
- Volunteer form submissions will also go to your Netlify-connected email
- Donation form continues to use formsubmit.co and sends to manathafoundation@gmail.com

## 🔒 Security

- Added security headers in netlify.toml
- Honeypot spam protection on all Netlify forms
- All external links have proper `rel="noopener"` attributes

## 📱 The website is fully responsive and includes:

- Mobile-friendly design
- All images and assets properly linked
- 404 page for missing pages
- SEO-friendly meta descriptions
- All external resources load from CDNs

Your website is now 100% ready for Netlify deployment! 🎉
