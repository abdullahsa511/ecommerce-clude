<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Components\User as UserComponent;
use App\Core\Models\User;
use App\Core\Repositories\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use PDO;

class UserTest extends TestCase
{
    private UserRepositoryInterface $userRepository;
    private UserComponent $userComponent;
    private ?PDO $db = null;
    private KernelCli $kernel;

    // Test data properties
    private array $userData;
    private array $userDataToDelete;
    private array $currentUserData;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize test data
        $this->userData = [
            'username' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'display_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT)
        ];

        $this->userDataToDelete = [
            'username' => 'todelete',
            'first_name' => 'To',
            'last_name' => 'Delete',
            'display_name' => 'To Delete',
            'email' => 'delete@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT)
        ];

        $this->currentUserData = [
            'username' => 'currentuser',
            'first_name' => 'Current',
            'last_name' => 'User',
            'display_name' => 'Current User',
            'email' => 'current@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT)
        ];
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        
        // Get dependencies from the container
        $this->db = $this->kernel->getContainer()->make(PDO::class);
        $this->userRepository = $this->kernel->getContainer()->make(UserRepositoryInterface::class);

        // Clean up any existing test data
        $this->cleanupTestData();
    }

    private function cleanupTestData(): void
    {
        $testEmails = [
            $this->userData['email'],
            'jane@example.com',
            'test@example.com',
            $this->currentUserData['email'],
            $this->userDataToDelete['email']
        ];
        foreach ($testEmails as $email) {
            $user = $this->userRepository->findByEmail($email);
            if ($user) {
                $this->userRepository->delete($user->user_id);
            }
        }
    }

    public function testFindUserById(): void
    {
        // Arrange - Create a test user
        $user = $this->userRepository->create($this->userData);
        $this->assertNotNull($user);

        // Create component with options
        $this->userComponent = new UserComponent($this->userRepository, ['user_id' => $user->user_id]);

        // Act
        $result = $this->userComponent->results();

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($user->user_id, $result['user_id']);
        $this->assertEquals($this->userData['display_name'], $result['display_name']);
        $this->assertEquals($this->userData['email'], $result['email']);

        // Cleanup
        $this->userRepository->delete($user->user_id);
    }

    public function testFindUserByEmail(): void
    {
        // Arrange - Create a test user
        $userData = [
            'username' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'display_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT)
        ];
        
        $user = $this->userRepository->create($userData);
        $this->assertNotNull($user);

        // Create component with options
        $this->userComponent = new UserComponent($this->userRepository, ['username' => 'jane@example.com']);

        // Act
        $result = $this->userComponent->results();

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($user->user_id, $result['user_id']);
        $this->assertEquals('Jane Doe', $result['display_name']);
        $this->assertEquals('jane@example.com', $result['email']);

        // Cleanup
        $this->userRepository->delete($user->user_id);
    }

    public function testUpdateUser(): void
    {
        // Arrange - Create a test user
        $user = $this->userRepository->create($this->userData);
        $this->assertNotNull($user);

        // Update user data
        $updateData = [
            'display_name' => 'Updated Test User'
        ];
        
        // Act
        $updated = $this->userRepository->update($user->user_id, $updateData);
        $updatedUser = $this->userRepository->find($user->user_id);

        // Assert
        $this->assertTrue($updated);
        $this->assertEquals('Updated Test User', $updatedUser->display_name);
        $this->assertEquals($this->userData['email'], $updatedUser->email);

        // Cleanup
        $this->userRepository->delete($user->user_id);
    }

    public function testDeleteUser(): void
    {
        // Arrange - Create a test user
        $user = $this->userRepository->create($this->userDataToDelete);
        $this->assertNotNull($user);

        // Act
        $deleted = $this->userRepository->delete($user->user_id);
        $deletedUser = $this->userRepository->find($user->user_id);

        // Assert
        $this->assertTrue($deleted);
        $this->assertNull($deletedUser);
    }

    public function testNoUserFound(): void
    {
        // Create component with non-existent user ID
        $this->userComponent = new UserComponent($this->userRepository, ['user_id' => 999999]);

        // Act
        $result = $this->userComponent->results();

        // Assert
        $this->assertEmpty($result);
    }

    public function testCurrentUser(): void
    {
        // Arrange - Create a test user
        $user = $this->userRepository->create($this->currentUserData);
        $this->assertNotNull($user);

        // Simulate current user session
        $_SESSION['user'] = [
            'user_id' => $user->user_id,
            'display_name' => $this->currentUserData['display_name'],
            'email' => $this->currentUserData['email']
        ];

        // Create component with no specific options
        $this->userComponent = new UserComponent($this->userRepository);

        // Act
        $result = $this->userComponent->results();

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($_SESSION['user'], $result);

        // Cleanup
        unset($_SESSION['user']);
        $this->userRepository->delete($user->user_id);
    }

    protected function tearDown(): void
    {
        // Clean up any remaining test data
        $this->cleanupTestData();
        
        // Close the database connection
        $this->db = null;
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 