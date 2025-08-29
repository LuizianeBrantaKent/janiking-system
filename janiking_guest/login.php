<?php include 'includes/guest_header.php'; ?>
<?php include 'includes/guest_navbar.php'; ?>

<!-- Login Page Section -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-stretch">
            <!-- Left Column: Hero with Image and Text -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="hero-left">
                    <div>
                        <h2 class="font-weight-bold mb-4">Welcome back to JaniKing Franchise Portal</h2>
                    </div>
                </div>
            </div>

            <!-- Right Column: Login Form -->
            <div class="col-md-6">
                <div class="login-form">
                    <h3 class="font-weight-bold mb-4">Login</h3>
                    <p class="lead mb-4">Please sign in to your account.</p>
                    <?php
                    session_start();
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        include '../db/connect.php'; // Updated path to /db/connect.php

                        $email = htmlspecialchars(trim($_POST['username']));
                        $password = trim($_POST['password']);
                        $role = htmlspecialchars(trim($_POST['role']));

                        $errors = [];

                        if (empty($email)) $errors[] = "Email is required.";
                        if (empty($password)) $errors[] = "Password is required.";
                        if (empty($role)) $errors[] = "Role is required.";
                        if (!in_array($role, ['Admin', 'Staff', 'Franchisee'])) $errors[] = "Invalid role.";

                        if (empty($errors)) {
                            if ($role === 'Franchisee') {
                                // Check users and franchisees table for franchisee
                                $stmt = $conn->prepare("SELECT u.user_id, u.email, u.password_hash, u.role, f.franchisee_id 
                                                        FROM users u 
                                                        LEFT JOIN franchisees f ON u.user_id = f.user_id 
                                                        WHERE u.email = ? AND f.franchisee_id IS NOT NULL AND u.status = 'Active'");
                                $stmt->execute([$email]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            } else {
                                // Check users table for Admin or Staff
                                $stmt = $conn->prepare("SELECT user_id, email, password_hash, role FROM users WHERE email = ? AND role = ? AND status = 'Active'");
                                $stmt->execute([$email, $role]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            }

                            if ($user && password_verify($password, $user['password_hash'])) {
                                // Start session and set user data
                                $_SESSION['user_id'] = $user['user_id'];
                                $_SESSION['role'] = $role; // Set based on selected role
                                $_SESSION['email'] = $user['email'];

                                // Redirect based on role
                                switch ($role) {
                                    case 'Admin':
                                        header('Location: /admin/admin_dash.php');
                                        break;
                                    case 'Staff':
                                        header('Location: /staff/staff_dash.php');
                                        break;
                                    case 'Franchisee':
                                        if ($user['franchisee_id']) {
                                            header('Location: /franchisee/franchisee_dash.php');
                                        } else {
                                            $errors[] = "No franchisee account found.";
                                        }
                                        break;
                                    default:
                                        $errors[] = "Invalid role redirection.";
                                }
                                if (empty($errors)) exit;
                            } else {
                                $errors[] = "Email/Username doesn't exist or password is incorrect.";
                            }
                        }

                        if (!empty($errors)) {
                            echo '<div class="alert alert-danger">';
                            foreach ($errors as $error) {
                                echo '<p>' . $error . '</p>';
                            }
                            echo '</div>';
                        }
                    }
                    ?>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="username">Email</label>
                            <input type="email" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="Admin">JaniKing Admin</option>
                                <option value="Staff">Staff</option>
                                <option value="Franchisee">Franchisee</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                    </form>
                    <p class="text-center mt-3">
                        <a href="/forgot_password.php">Forgot password?</a>
                    </p>
                    <p class="text-center mt-3">
                        Need Help? <a href="/contact_us.php">Contact Us</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- No JS needed for now -->
<?php include 'includes/guest_footer.php'; ?>