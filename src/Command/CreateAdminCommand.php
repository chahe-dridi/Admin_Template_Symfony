<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create an admin user for the application',
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
            ->addArgument('email', InputArgument::OPTIONAL, 'Admin email address')
            ->addArgument('password', InputArgument::OPTIONAL, 'Admin password')
            ->addOption('promote', 'p', InputOption::VALUE_NONE, 'Promote an existing user to admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $promote = $input->getOption('promote');

        // Interactive mode if no arguments provided
        if (!$email) {
            $email = $io->ask('Enter admin email address');
        }

        // Check if user already exists
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($promote && $existingUser) {
            // Promote existing user to admin
            $existingUser->setRoles(['ROLE_ADMIN']);
            $this->entityManager->flush();
            
            $io->success(sprintf('User "%s" has been promoted to admin!', $email));
            return Command::SUCCESS;
        }

        if ($existingUser) {
            $io->error(sprintf('User with email "%s" already exists!', $email));
            $io->note('Use --promote option to promote this user to admin');
            return Command::FAILURE;
        }

        // Create new admin user
        if (!$password) {
            $password = $io->askHidden('Enter admin password (min 6 characters)');
            $confirmPassword = $io->askHidden('Confirm password');

            if ($password !== $confirmPassword) {
                $io->error('Passwords do not match!');
                return Command::FAILURE;
            }
        }

        if (strlen($password) < 6) {
            $io->error('Password must be at least 6 characters long!');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Admin user created successfully!');
        $io->table(
            ['Email', 'Role'],
            [[$email, 'ROLE_ADMIN']]
        );

        $io->note('You can now login at /login with these credentials');

        return Command::SUCCESS;
    }
}
