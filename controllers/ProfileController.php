<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/UserModel.php';

class ProfileController {

    public function showProfile(): void {
        requireLogin();
        $user = UserModel::findById((int) $_SESSION['user_id']);
        require __DIR__ . '/../views/profile/profile.php';
    }

    public function handleUpdate(): void {
        requireLogin();
        verifyCsrf();

        $userId  = (int) $_SESSION['user_id'];
        $user    = UserModel::findById($userId);
        $errors  = [];
        $updates = [];

        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');

        if (mb_strlen($name) < 2)
            $errors['name'] = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Invalid email address.';
        elseif (UserModel::emailExists($email, $userId))
            $errors['email'] = 'Email already in use by another account.';

        // Password change (optional)
        $currentPw = $_POST['current_password'] ?? '';
        $newPw     = $_POST['new_password']     ?? '';
        $confirmPw = $_POST['confirm_password'] ?? '';

        if (!empty($currentPw) || !empty($newPw) || !empty($confirmPw)) {
            if (!password_verify($currentPw, $user['password_hash']))
                $errors['current_password'] = 'Current password is incorrect.';
            elseif (mb_strlen($newPw) < 8)
                $errors['new_password'] = 'New password must be at least 8 characters.';
            elseif ($newPw !== $confirmPw)
                $errors['confirm_password'] = 'Passwords do not match.';
            else
                $updates['password_hash'] = password_hash($newPw, PASSWORD_BCRYPT);
        }

        // Profile picture
        if (!empty($_FILES['profile_picture']['name'])) {
            $file = $_FILES['profile_picture'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['profile_picture'] = 'Upload error, please try again.';
            } elseif ($file['size'] > MAX_FILE_SIZE) {
                $errors['profile_picture'] = 'Image must be under 2 MB.';
            } else {
                $mime = mime_content_type($file['tmp_name']);
                if (!in_array($mime, ALLOWED_MIME)) {
                    $errors['profile_picture'] = 'Only JPEG, PNG, GIF, WEBP are allowed.';
                } else {
                    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'user_' . $userId . '_' . time() . '.' . $ext;
                    $dest     = UPLOAD_DIR . $filename;
                    if (!move_uploaded_file($file['tmp_name'], $dest)) {
                        $errors['profile_picture'] = 'Could not save the image.';
                    } else {
                        // Delete old profile picture
                        if (!empty($user['profile_picture'])) {
                            $oldPath = UPLOAD_DIR . $user['profile_picture'];
                            if (file_exists($oldPath)) unlink($oldPath);
                        }
                        $updates['profile_picture'] = $filename;
                    }
                }
            }
        }

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            redirect('/profile');
        }

        $updates['name']  = $name;
        $updates['email'] = $email;
        UserModel::update($userId, $updates);

        // Keep session in sync
        $_SESSION['name'] = $name;

        setFlash('success', 'Profile updated successfully!');
        redirect('/profile');
    }
}
