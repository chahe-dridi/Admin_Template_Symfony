# Authentication System Setup Guide

## What Has Been Implemented

### 1. **User Authentication & Authorization**
- ✅ Fixed login and registration controllers
- ✅ Auto-assign ROLE_USER to all new registrations
- ✅ Role-based dashboard routing (Admin vs User)
- ✅ Proper redirects after login based on user role

### 2. **Dashboards Created**

#### **User Dashboard** (`/dashboard`)
- Simple, clean interface for standard users
- Displays:
  - Welcome message with user email
  - Profile information (email, status, role)
  - Quick action buttons (Edit Profile, Change Password, Settings)
  - Recent activity feed
- Access: `ROLE_USER` and above

#### **Admin Dashboard** (`/admin/dashboard`)
- Full CRM dashboard with statistics
- Displays:
  - Invoices awaiting payment
  - Converted leads
  - Projects in progress
  - Conversion rate statistics
- Access: `ROLE_ADMIN` only

### 3. **Navigation & Header**
- ✅ Role-based menu items
  - **Admin users** see: Reports, Customers, Leads, Projects, Settings
  - **Regular users** see: My Dashboard, My Profile, Settings
- ✅ User profile dropdown with role badge
- ✅ Working logout functionality

### 4. **Styled Authentication Pages**
- ✅ Modern login page with card design
- ✅ Professional registration page
- ✅ Error handling and validation messages
- ✅ Links to switch between login/register

### 5. **Security Configuration**
- ✅ Access control rules set up:
  - `/login` and `/register` - Public
  - `/admin/*` - ROLE_ADMIN only
  - `/dashboard` - ROLE_USER and above

## Routes Available

| Route | URL | Access | Purpose |
|-------|-----|--------|---------|
| `app_home` | `/` | PUBLIC | Redirects to appropriate dashboard |
| `app_login` | `/login` | PUBLIC | Login page |
| `app_register` | `/register` | PUBLIC | Registration page |
| `app_logout` | `/logout` | AUTHENTICATED | Logout |
| `dashboard_user` | `/dashboard` | ROLE_USER | User dashboard |
| `dashboard_admin` | `/admin/dashboard` | ROLE_ADMIN | Admin dashboard |
| `dashboard_analytics` | `/dashboard/analytics` | AUTHENTICATED | Analytics page |

## How to Use

### 1. Register a New User
1. Go to `http://localhost:8000/register`
2. Enter email and password (min 6 characters)
3. Accept terms and conditions
4. Click "Create Account"
5. You'll be automatically logged in and redirected to the **User Dashboard**

### 2. Create an Admin User

**Option A: Using Symfony Console**
```bash
php bin/console app:create-admin
```
*(You'll need to create this command - see below)*

**Option B: Manually in Database**
```sql
-- After registering normally, update the user's roles
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'admin@example.com';
```

**Option C: Create Command File** (Recommended)
Create `src/Command/CreateAdminCommand.php`:

```php
<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create an admin user',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Admin user created successfully!');
        return Command::SUCCESS;
    }
}
```

Then run:
```bash
php bin/console app:create-admin admin@example.com SecurePassword123
```

### 3. Login
1. Go to `http://localhost:8000/login`
2. Enter your email and password
3. Click "Sign In"
4. **Admin users** → Redirected to `/admin/dashboard`
5. **Regular users** → Redirected to `/dashboard`

### 4. Logout
Click the logout button in:
- Header dropdown (user profile icon)
- Or directly visit: `http://localhost:8000/logout`

## Testing the System

### Test as Regular User
1. Register at `/register` with email: `user@test.com`
2. After login, you should see the simple user dashboard
3. Navigation shows only: My Dashboard, My Profile, Settings

### Test as Admin
1. Create admin account (see above)
2. Login with admin credentials
3. After login, you should see the admin CRM dashboard
4. Navigation shows: Reports, Customers, Leads, Projects, Settings

## Files Modified/Created

### Modified:
- ✅ `src/Controller/SecurityController.php` - Login redirect logic
- ✅ `src/Controller/RegistrationController.php` - Auto-assign ROLE_USER
- ✅ `src/Controller/DashboardController.php` - Role-based routing
- ✅ `src/Security/AppCustomAuthenticator.php` - Post-login redirects
- ✅ `config/packages/security.yaml` - Access control rules
- ✅ `templates/security/login.html.twig` - Styled login page
- ✅ `templates/registration/register.html.twig` - Styled registration page
- ✅ `templates/partials/_navigation.html.twig` - Role-based menu
- ✅ `templates/partials/_header.html.twig` - User profile dropdown

### Created:
- ✅ `templates/dashboard/user.html.twig` - User dashboard
- ✅ `templates/dashboard/admin.html.twig` - Admin dashboard

## Troubleshooting

### Issue: "Access Denied" after login
**Solution:** Check user roles in database:
```bash
php bin/console doctrine:query:sql "SELECT * FROM user"
```

### Issue: Redirect loop
**Solution:** Clear cache:
```bash
php bin/console cache:clear
```

### Issue: CSRF token error
**Solution:** Make sure sessions are working:
```bash
# Check var/sessions/ folder exists and is writable
ls -la var/sessions/
```

## Next Steps

1. **Create the admin command** (see Option C above)
2. **Customize dashboards** with real data from your repositories
3. **Add more user features** like:
   - Password reset
   - Email verification
   - User profile editing
   - Avatar upload
4. **Secure additional routes** in `security.yaml`
5. **Add more role-based features** as needed

## Notes

- All new registrations get `ROLE_USER` automatically
- Admin role must be assigned manually (database or command)
- The system supports multiple roles per user
- Role hierarchy can be configured in `security.yaml`

---

**Your authentication system is now fully functional!** 🎉

Users can register, login, and see appropriate dashboards based on their roles.
