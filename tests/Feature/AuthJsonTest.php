<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AuthJsonTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /**
     * Test JSON registration functionality.
     */
    public function test_user_can_register_with_json()
    {
        $fields = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];
        $response = $this->json('POST', '/api/register', $fields);

        // Assert the user was created in the database
        $this->assertDatabaseHas('users', [
            'email' => $fields['email'],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'user' => [
                        'name' => $fields['name'],
                        'email' => $fields['email'],
                    ],
                    'token' => true,
                ]
            ]);
    }

    /**
     * Test JSON login functionality.
     */
    public function test_user_can_login_with_json()
    {
        // Create a user
        $password = 'Password123';
        $fields = [
            'email' => 'testuser@example.com',
            'password' => Hash::make($password),
        ];
        $user = User::factory()->create($fields);

        // Attempt to log in via JSON
        $response = $this->json('POST', '/api/login', [
            'email' => $fields['email'],
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'access_token',
                    'token_type'
                ]
            ])->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'access_token' => true,
                    'token_type' => 'Bearer'
                ]
            ]);
    }

    /**
     * Test user cannot login with invalid credentials using a data provider.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidLoginProvider')]
    public function test_user_cannot_login_with_invalid_credentials_json(
        $email,
        $password,
        $expectedStatus,
        $expectedError
    ) {
        // Create a valid user
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => Hash::make('Password123'),
        ]);

        // Attempt to log in with various invalid credentials
        $response = $this->json('POST', '/api/login', [
            'email' => $email,
            'password' => $password,
        ]);

        // Assert the response status
        $response->assertStatus($expectedStatus);

        // Assert the response contains the expected error message
        $response->assertJson([
            'message' => $expectedError,
        ]);
    }

    /**
     * Data provider for invalid login scenarios.
     */
    public static function invalidLoginProvider()
    {
        return [
            // Correct email but wrong password
            'correct email, wrong password' => [
                'johndoe@example.com',  // email
                'wrongpassword',        // password
                401,                    // expected status
                'Email and password doesnt match',  // expected error message
            ],

            // Wrong email and wrong password
            'wrong email, wrong password' => [
                'wrongemail@example.com',
                'wrongpassword',
                401,
                'Your requested email is not registered yet',
            ],

            // Empty email
            'empty email' => [
                '',                     // empty email
                'password123',           // valid password
                422,                    // expected status for validation error
                'The email field is required.', // expected error message for email validation
            ],

            // Empty password
            'empty password' => [
                'johndoe@example.com',
                '',                     // empty password
                422,                    // expected status for validation error
                'The password field is required.', // expected error message for password validation
            ],

            // Empty email and password
            'empty email and password' => [
                '',                     // empty email
                '',                     // empty password
                422,                    // expected status for validation error
                'The email field is required. , The password field is required.', // expected error message for both fields
            ],
            // Email is not valid
            'invalid email format' => [
                'invalid-email-format',   // invalid email
                'password123',
                422,                      // expected status for validation error
                'The email field must be a valid email address.', // expected error message
            ],
            'email longer than 255 characters' => [
                str_repeat('a', 256) . '@example.com',  // email longer than 255 chars
                'password123',
                422,                                   // expected status for validation error
                'The email field must not be greater than 255 characters.', // expected error message
            ],
            'password longer than 255 characters' => [
                'johndoe@example.com',
                str_repeat('a', 256),                  // password longer than 255 chars
                422,                                   // expected status for validation error
                'The password field must not be greater than 255 characters.', // expected error message
            ],
        ];
    }

    /**
     * Test user cannot register with invalid inputs using a data provider.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidRegisterProvider')]
    public function test_user_cannot_register_with_invalid_inputs($name, $email, $password, $password_confirmation, $expectedStatus, $expectedError)
    {
        // Attempt to register with invalid data
        $response = $this->json('POST', '/api/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        // Assert the response status
        $response->assertStatus($expectedStatus);

        // Assert the response contains the expected error message
        $response->assertJsonValidationErrors($expectedError);
    }

    /**
     * Data provider for invalid registration scenarios.
     */
    public static function invalidRegisterProvider()
    {
        return [
            // Missing name field
            'missing name' => [
                '',                      // name
                'johndoe@example.com',    // email
                'password123',            // password
                'password123',            // password confirmation
                422,                      // expected status
                ['name' => 'The name field is required.'], // expected error message
            ],
            // Name too short
            'name too short' => [
                'J',                     // name (less than 2 characters)
                'johndoe@example.com',    // email
                'password123',            // password
                'password123',            // password confirmation
                422,                      // expected status
                ['name' => 'The name field must be at least 2 characters.'], // expected error message
            ],

            // Missing email field
            'missing email' => [
                'John Doe',               // name
                '',                       // email
                'password123',            // password
                'password123',            // password confirmation
                422,                      // expected status
                ['email' => 'The email field is required.'], // expected error message
            ],

            // Invalid email format
            'invalid email format' => [
                'John Doe',               // name
                'invalid-email-format',   // email
                'password123',            // password
                'password123',            // password confirmation
                422,                      // expected status
                ['email' => 'The email field must be a valid email address.'], // expected error message
            ],
            // Password is too short
            'password too short' => [
                'John Doe',               // name
                'johndoe@example.com',    // email
                'passs',                   // password (less than 6 characters)
                'passs',                   // password confirmation
                422,                      // expected status
                ['password' => 'The password field must be at least 6 characters.'], // expected error message
            ],

            // Password confirmation mismatch
            'password confirmation mismatch' => [
                'John Doe',               // name
                'johndoe@example.com',    // email
                'Password123',            // password
                'differentpassword',      // password confirmation mismatch
                422,                      // expected status
                ['password_confirmation' => 'The password confirmation field must match password.'], // expected error message
            ],

            // Email exceeds 255 characters
            'email longer than 255 characters' => [
                'John Doe',               // name
                str_repeat('a', 256) . '@example.com', // email
                'password123',            // password
                'password123',            // password confirmation
                422,                      // expected status
                ['email' => 'The email field must not be greater than 255 characters.'], // expected error message
            ],

            // Password exceeds 255 characters
            'password longer than 255 characters' => [
                'John Doe',               // name
                'johndoe@example.com',    // email
                str_repeat('a1A', 256),     // password (too long)
                str_repeat('a1A', 256),     // password confirmation
                422,                      // expected status
                ['password' => 'The password field must not be greater than 255 characters.'], // expected error message
            ],

            // Email already exists
            'email already exists' => [
                'John Doe',               // name
                'existinguser@example.com', // email
                'Password123',            // password
                'Password123',            // password confirmation
                422,                      // expected status
                ['email' => 'The email has already been taken.'], // expected error message
            ],
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
        // Create an existing user for the "email already exists" scenario
        User::factory()->create([
            'email' => 'existinguser@example.com',
        ]);
    }
}
