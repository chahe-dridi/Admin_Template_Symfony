# Quick Start Guide

## Testing the Authentication System

### 1. Clear Symfony Cache
```bash
php bin/console cache:clear
```

### 2. Create an Admin User

**Interactive mode (Recommended):**
```bash
php bin/console app:create-admin
```
Follow the prompts to enter email and password.

**Direct mode:**
```bash
php bin/console app:create-admin admin@example.com SecurePass123
```

**Promote existing user:**
```bash
php bin/console app:create-admin user@example.com --promote
```

### 3. Start the Symfony Server
```bash
symfony server:start
```
or
```bash
php -S localhost:8000 -t public
```

### 4. Test Registration
1. Open browser: `http://localhost:8000/register`
2. Register with a new email (e.g., `user@test.com`)
3. After registration, you should be logged in automatically
4. You should see the **User Dashboard** with your profile info

### 5. Test Login as Admin
1. Go to: `http://localhost:8000/logout` (if logged in)
2. Go to: `http://localhost:8000/login`
3. Login with admin credentials
4. You should see the **Admin Dashboard** with CRM statistics

### 6. Test Role-Based Navigation
- **As User**: Check the sidebar - you should see "My Dashboard", "My Profile", "Settings"
- **As Admin**: Check the sidebar - you should see "Reports", "Customers", "Leads", "Projects", etc.

### 7. Test Access Control
Try accessing these URLs as a regular user:
- `http://localhost:8000/admin/dashboard` → Should show "Access Denied"
- `http://localhost:8000/dashboard` → Should work fine

Try accessing as admin:
- Both should work

## Expected Behavior

### New User Registration Flow:
1. User fills registration form
2. User gets `ROLE_USER` automatically
3. User is logged in automatically
4. User is redirected to `/dashboard` (User Dashboard)

### Login Flow:
1. User enters credentials
2. System checks user role
3. **If ROLE_ADMIN** → Redirect to `/admin/dashboard`
4. **If ROLE_USER** → Redirect to `/dashboard`

### Navigation:
- **Admin Menu**: Full access to all features
- **User Menu**: Limited to personal dashboard and settings

## Quick Command Reference

```bash
# Create admin user
php bin/console app:create-admin admin@test.com Password123

# Promote existing user to admin
php bin/console app:create-admin user@test.com --promote

# List all users (via SQL)
php bin/console doctrine:query:sql "SELECT id, email, roles FROM user"

# Clear cache
php bin/console cache:clear

# Start server
symfony server:start

# Check routes
php bin/console debug:router
```

## Troubleshooting

### Problem: Can't login
**Solution**: Make sure you've run migrations:
```bash
php bin/console doctrine:migrations:migrate
```

### Problem: Access Denied errors
**Solution**: Check user roles in database:
```bash
php bin/console doctrine:query:sql "SELECT * FROM user"
```

### Problem: Page not styled correctly
**Solution**: Check if assets are accessible:
- Visit: `http://localhost:8000/css/bootstrap.min.css`
- If 404, check the `public/` directory

### Problem: CSRF token invalid
**Solution**: Clear sessions:
```bash
rm -rf var/cache/*
rm -rf var/sessions/*
php bin/console cache:clear
```

## What's Working ✅

- ✅ User registration with auto ROLE_USER assignment
- ✅ User login with role-based dashboard redirect
- ✅ Separate dashboards for users and admins
- ✅ Role-based navigation menus
- ✅ Styled login and registration pages
- ✅ User profile dropdown with role badge
- ✅ Access control (admin routes protected)
- ✅ Logout functionality
- ✅ Command to create admin users

## Next Steps 🚀

1. **Customize the dashboards** with real data
2. **Add user profile page** where users can edit their info
3. **Implement password reset** functionality
4. **Add email verification** for new registrations
5. **Create more role-based features** (e.g., ROLE_MANAGER)

---

**Everything is ready to go!** Start the server and test the authentication flow. 🎉
