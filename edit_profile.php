<?php
require_once __DIR__ . '/init.php';
require_auth();

$user = current_user();
$errors = [];
$success = null;

// Check if user has a profile
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

// Check if partner preferences exist
$stmt = $pdo->prepare("SELECT * FROM partner_preferences WHERE user_id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$partnerPrefs = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid request (CSRF). Please try again.';
    } else {
        try {
            // Basic Information
            $fullName = trim($_POST['full_name'] ?? '');
            $gender = $_POST['gender'] ?? '';
            $dob = $_POST['dob'] ?? '';
            $maritalStatus = $_POST['marital_status'] ?? '';
            $height = $_POST['height'] ?? '';
            $weight = $_POST['weight'] ?? '';
            $bodyType = $_POST['body_type'] ?? '';
            $complexion = $_POST['complexion'] ?? '';
            $bloodGroup = $_POST['blood_group'] ?? '';
            $disability = trim($_POST['disability'] ?? '');
            
            // Religion & Community
            $religion = $_POST['religion'] ?? '';
            $caste = $_POST['caste'] ?? '';
            $subCaste = $_POST['sub_caste'] ?? '';
            $motherTongue = $_POST['mother_tongue'] ?? '';
            $languagesKnown = implode(', ', $_POST['languages_known'] ?? []);
            
            // Education & Career
            $education = $_POST['education'] ?? '';
            $educationDetail = trim($_POST['education_detail'] ?? '');
            $occupation = $_POST['occupation'] ?? '';
            $occupationDetail = trim($_POST['occupation_detail'] ?? '');
            $annualIncome = $_POST['annual_income'] ?? '';
            $employedIn = $_POST['employed_in'] ?? '';
            $jobLocation = trim($_POST['job_location'] ?? '');
            
            // Family Information
            $familyStatus = $_POST['family_status'] ?? '';
            $familyType = $_POST['family_type'] ?? '';
            $familyValues = $_POST['family_values'] ?? '';
            $fatherName = trim($_POST['father_name'] ?? '');
            $fatherOccupation = trim($_POST['father_occupation'] ?? '');
            $motherName = trim($_POST['mother_name'] ?? '');
            $motherOccupation = trim($_POST['mother_occupation'] ?? '');
            $siblings = trim($_POST['siblings'] ?? '');
            $nativePlace = trim($_POST['native_place'] ?? '');
            
            // Contact Information
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $city = trim($_POST['city'] ?? '');
            $state = trim($_POST['state'] ?? '');
            $country = trim($_POST['country'] ?? '');
            $pincode = trim($_POST['pincode'] ?? '');
            
            // Lifestyle & About
            $aboutMe = trim($_POST['about_me'] ?? '');
            $hobbies = trim($_POST['hobbies'] ?? '');
            $diet = $_POST['diet'] ?? '';
            $smoke = $_POST['smoke'] ?? '';
            $drink = $_POST['drink'] ?? '';
            $partnerExpectations = trim($_POST['partner_expectations'] ?? '');
            
            // Partner Preferences
            $ageFrom = $_POST['age_from'] ?? '';
            $ageTo = $_POST['age_to'] ?? '';
            $heightFrom = $_POST['height_from'] ?? '';
            $heightTo = $_POST['height_to'] ?? '';
            $prefMaritalStatus = $_POST['pref_marital_status'] ?? '';
            $prefReligion = $_POST['pref_religion'] ?? '';
            $prefCaste = $_POST['pref_caste'] ?? '';
            $prefEducation = $_POST['pref_education'] ?? '';
            $prefOccupation = $_POST['pref_occupation'] ?? '';
            $prefAnnualIncome = $_POST['pref_annual_income'] ?? '';
            $prefCountry = $_POST['pref_country'] ?? '';
            $prefState = $_POST['pref_state'] ?? '';
            $prefCity = $_POST['pref_city'] ?? '';
            $prefDiet = $_POST['pref_diet'] ?? '';
            $prefSmoke = $_POST['pref_smoke'] ?? '';
            $prefDrink = $_POST['pref_drink'] ?? '';
            $prefBodyType = $_POST['pref_body_type'] ?? '';
            $prefComplexion = $_POST['pref_complexion'] ?? '';
            $otherPreferences = trim($_POST['other_preferences'] ?? '');
            
            // Calculate age from DOB
            $age = null;
            if (!empty($dob)) {
                $dobDate = new DateTime($dob);
                $today = new DateTime();
                $age = $today->diff($dobDate)->y;
            }
            
            // Handle file upload (profile photo)
            $photoPath = $profile['photo'] ?? null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $file = handle_file_upload('photo', 'profile_');
                if ($file) {
                    // Delete old photo if exists
                    if (!empty($profile['photo']) && file_exists(__DIR__ . '/' . $profile['photo'])) {
                        unlink(__DIR__ . '/' . $profile['photo']);
                    }
                    $photoPath = $file['path'];
                }
            }
            
            // Begin transaction
            $pdo->beginTransaction();
            
            if ($profile) {
                // Update existing profile
                $stmt = $pdo->prepare("UPDATE profiles SET 
                    photo = ?, full_name = ?, gender = ?, dob = ?, age = ?, marital_status = ?, 
                    height = ?, weight = ?, body_type = ?, complexion = ?, blood_group = ?, disability = ?,
                    religion = ?, caste = ?, sub_caste = ?, mother_tongue = ?, languages_known = ?,
                    education = ?, education_detail = ?, occupation = ?, occupation_detail = ?, annual_income = ?,
                    employed_in = ?, job_location = ?, family_status = ?, family_type = ?, family_values = ?,
                    father_name = ?, father_occupation = ?, mother_name = ?, mother_occupation = ?,
                    siblings = ?, native_place = ?, phone = ?, address = ?, city = ?, state = ?,
                    country = ?, pincode = ?, about_me = ?, hobbies = ?, diet = ?, smoke = ?, drink = ?,
                    partner_expectations = ?, is_profile_complete = 1, updated_at = NOW()
                    WHERE user_id = ?");
                
                $stmt->execute([
                    $photoPath, $fullName, $gender, $dob, $age, $maritalStatus, 
                    $height, $weight, $bodyType, $complexion, $bloodGroup, $disability,
                    $religion, $caste, $subCaste, $motherTongue, $languagesKnown,
                    $education, $educationDetail, $occupation, $occupationDetail, $annualIncome,
                    $employedIn, $jobLocation, $familyStatus, $familyType, $familyValues,
                    $fatherName, $fatherOccupation, $motherName, $motherOccupation,
                    $siblings, $nativePlace, $phone, $address, $city, $state,
                    $country, $pincode, $aboutMe, $hobbies, $diet, $smoke, $drink,
                    $partnerExpectations, $user['id']
                ]);
            } else {
                // Create new profile
                $stmt = $pdo->prepare("INSERT INTO profiles (
                    id, user_id, photo, full_name, gender, dob, age, marital_status, 
                    height, weight, body_type, complexion, blood_group, disability,
                    religion, caste, sub_caste, mother_tongue, languages_known,
                    education, education_detail, occupation, occupation_detail, annual_income,
                    employed_in, job_location, family_status, family_type, family_values,
                    father_name, father_occupation, mother_name, mother_occupation,
                    siblings, native_place, phone, address, city, state,
                    country, pincode, about_me, hobbies, diet, smoke, drink,
                    partner_expectations, created_at, updated_at, is_profile_complete
                ) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 1)");
                
                $stmt->execute([
                    $user['id'], $photoPath, $fullName, $gender, $dob, $age, $maritalStatus, 
                    $height, $weight, $bodyType, $complexion, $bloodGroup, $disability,
                    $religion, $caste, $subCaste, $motherTongue, $languagesKnown,
                    $education, $educationDetail, $occupation, $occupationDetail, $annualIncome,
                    $employedIn, $jobLocation, $familyStatus, $familyType, $familyValues,
                    $fatherName, $fatherOccupation, $motherName, $motherOccupation,
                    $siblings, $nativePlace, $phone, $address, $city, $state,
                    $country, $pincode, $aboutMe, $hobbies, $diet, $smoke, $drink,
                    $partnerExpectations
                ]);
            }
            
            // Handle partner preferences
            if ($partnerPrefs) {
                $stmt = $pdo->prepare("UPDATE partner_preferences SET 
                    age_from = ?, age_to = ?, height_from = ?, height_to = ?,
                    marital_status = ?, religion = ?, caste = ?, education = ?,
                    occupation = ?, annual_income = ?, country = ?, state = ?,
                    city = ?, diet = ?, smoke = ?, drink = ?, body_type = ?,
                    complexion = ?, other_preferences = ?, updated_at = NOW()
                    WHERE user_id = ?");
                
                $stmt->execute([
                    $ageFrom, $ageTo, $heightFrom, $heightTo,
                    $prefMaritalStatus, $prefReligion, $prefCaste, $prefEducation,
                    $prefOccupation, $prefAnnualIncome, $prefCountry, $prefState,
                    $prefCity, $prefDiet, $prefSmoke, $prefDrink, $prefBodyType,
                    $prefComplexion, $otherPreferences, $user['id']
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO partner_preferences (
                    user_id, age_from, age_to, height_from, height_to,
                    marital_status, religion, caste, education, occupation,
                    annual_income, country, state, city, diet, smoke, drink,
                    body_type, complexion, other_preferences
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $user['id'], $ageFrom, $ageTo, $heightFrom, $heightTo,
                    $prefMaritalStatus, $prefReligion, $prefCaste, $prefEducation, $prefOccupation,
                    $prefAnnualIncome, $prefCountry, $prefState, $prefCity, $prefDiet, $prefSmoke, $prefDrink,
                    $prefBodyType, $prefComplexion, $otherPreferences
                ]);
            }
            
            $pdo->commit();
            $success = 'Profile updated successfully!';
            
            // Refresh profile data
            $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ? LIMIT 1");
            $stmt->execute([$user['id']]);
            $profile = $stmt->fetch();
            
            $stmt = $pdo->prepare("SELECT * FROM partner_preferences WHERE user_id = ? LIMIT 1");
            $stmt->execute([$user['id']]);
            $partnerPrefs = $stmt->fetch();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Failed to update profile: ' . $e->getMessage();
            error_log('Profile update error: ' . $e->getMessage());
        }
    }
}

// Get list of countries, states, etc. (you would typically have this in a database table)
$countries = ['Bangladesh', 'India', 'USA', 'UK', 'Canada', 'Australia'];
$states = ['Dhaka', 'Chittagong', 'Sylhet', 'Khulna', 'Rajshahi', 'Barisal', 'Rangpur'];
$religions = ['Islam', 'Hinduism', 'Christianity', 'Buddhism', 'Other'];
$educations = ['High School', 'Diploma', 'Bachelor', 'Master', 'PhD', 'Other'];
$occupations = ['Doctor', 'Engineer', 'Teacher', 'Business', 'Government Job', 'Private Job', 'Other'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= e($_SESSION['theme']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $profile ? 'Edit' : 'Create' ?> Profile - Matrimonial Studio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="container">
        <div class="card">
            <div class="profile-header">
                <?php if (!empty($profile['photo'])): ?>
                    <img src="<?= e($profile['photo']) ?>" alt="Profile Photo" class="profile-photo">
                <?php else: ?>
                    <div class="profile-photo placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
                <div class="profile-info">
                    <h1><?= $profile ? 'Edit Profile' : 'Create Your Profile' ?></h1>
                    <p class="helper">Fill in all the details to create a complete matrimonial profile.</p>
                </div>
            </div>

            <?php if ($errors): ?>
                <div class="alert error"><?= e(join('<br>', $errors)) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?= e($success) ?></div>
            <?php endif; ?>

            <form method="post" action="edit_profile.php" enctype="multipart/form-data" class="profile-form">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                
                <!-- Basic Information Section -->
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Basic Information</h3>
                    
                    <div class="form-row">
                        <label>Profile Photo</label>
                        <input type="file" name="photo" accept="image/*">
                        <?php if (!empty($profile['photo'])): ?>
                            <p class="helper">Current photo: <?= basename($profile['photo']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Full Name <span class="required">*</span></label>
                            <input class="input" type="text" name="full_name" value="<?= e($profile['full_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <label>Gender <span class="required">*</span></label>
                            <select class="input" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?= ($profile['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($profile['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($profile['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Date of Birth <span class="required">*</span></label>
                            <input class="input" type="date" name="dob" value="<?= e($profile['dob'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <label>Marital Status <span class="required">*</span></label>
                            <select class="input" name="marital_status" required>
                                <option value="">Select Status</option>
                                <option value="Never Married" <?= ($profile['marital_status'] ?? '') === 'Never Married' ? 'selected' : '' ?>>Never Married</option>
                                <option value="Divorced" <?= ($profile['marital_status'] ?? '') === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                                <option value="Widowed" <?= ($profile['marital_status'] ?? '') === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                                <option value="Separated" <?= ($profile['marital_status'] ?? '') === 'Separated' ? 'selected' : '' ?>>Separated</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Height (cm/ft)</label>
                            <input class="input" type="text" name="height" value="<?= e($profile['height'] ?? '') ?>" placeholder="e.g. 5'6\" or 167cm">
                        </div>
                        
                        <div class="form-row">
                            <label>Weight (kg/lbs)</label>
                            <input class="input" type="text" name="weight" value="<?= e($profile['weight'] ?? '') ?>" placeholder="e.g. 65kg or 143lbs">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Body Type</label>
                            <select class="input" name="body_type">
                                <option value="">Select Body Type</option>
                                <option value="Slim" <?= ($profile['body_type'] ?? '') === 'Slim' ? 'selected' : '' ?>>Slim</option>
                                <option value="Average" <?= ($profile['body_type'] ?? '') === 'Average' ? 'selected' : '' ?>>Average</option>
                                <option value="Athletic" <?= ($profile['body_type'] ?? '') === 'Athletic' ? 'selected' : '' ?>>Athletic</option>
                                <option value="Heavy" <?= ($profile['body_type'] ?? '') === 'Heavy' ? 'selected' : '' ?>>Heavy</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Complexion</label>
                            <select class="input" name="complexion">
                                <option value="">Select Complexion</option>
                                <option value="Very Fair" <?= ($profile['complexion'] ?? '') === 'Very Fair' ? 'selected' : '' ?>>Very Fair</option>
                                <option value="Fair" <?= ($profile['complexion'] ?? '') === 'Fair' ? 'selected' : '' ?>>Fair</option>
                                <option value="Wheatish" <?= ($profile['complexion'] ?? '') === 'Wheatish' ? 'selected' : '' ?>>Wheatish</option>
                                <option value="Dark" <?= ($profile['complexion'] ?? '') === 'Dark' ? 'selected' : '' ?>>Dark</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Blood Group</label>
                            <select class="input" name="blood_group">
                                <option value="">Select Blood Group</option>
                                <option value="A+" <?= ($profile['blood_group'] ?? '') === 'A+' ? 'selected' : '' ?>>A+</option>
                                <option value="A-" <?= ($profile['blood_group'] ?? '') === 'A-' ? 'selected' : '' ?>>A-</option>
                                <option value="B+" <?= ($profile['blood_group'] ?? '') === 'B+' ? 'selected' : '' ?>>B+</option>
                                <option value="B-" <?= ($profile['blood_group'] ?? '') === 'B-' ? 'selected' : '' ?>>B-</option>
                                <option value="AB+" <?= ($profile['blood_group'] ?? '') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                <option value="AB-" <?= ($profile['blood_group'] ?? '') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                <option value="O+" <?= ($profile['blood_group'] ?? '') === 'O+' ? 'selected' : '' ?>>O+</option>
                                <option value="O-" <?= ($profile['blood_group'] ?? '') === 'O-' ? 'selected' : '' ?>>O-</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Disability (if any)</label>
                            <input class="input" type="text" name="disability" value="<?= e($profile['disability'] ?? '') ?>" placeholder="None if no disability">
                        </div>
                    </div>
                </div>
                
                <!-- Religion & Community Section -->
                <div class="form-section">
                    <h3><i class="fas fa-praying-hands"></i> Religion & Community</h3>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Religion <span class="required">*</span></label>
                            <select class="input" name="religion" required>
                                <option value="">Select Religion</option>
                                <?php foreach ($religions as $religion): ?>
                                    <option value="<?= e($religion) ?>" <?= ($profile['religion'] ?? '') === $religion ? 'selected' : '' ?>><?= e($religion) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Caste</label>
                            <input class="input" type="text" name="caste" value="<?= e($profile['caste'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Sub Caste</label>
                            <input class="input" type="text" name="sub_caste" value="<?= e($profile['sub_caste'] ?? '') ?>">
                        </div>
                        
                        <div class="form-row">
                            <label>Mother Tongue <span class="required">*</span></label>
                            <input class="input" type="text" name="mother_tongue" value="<?= e($profile['mother_tongue'] ?? '') ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>Languages Known</label>
                        <div class="checkbox-group">
                            <?php 
                            $knownLangs = isset($profile['languages_known']) ? explode(', ', $profile['languages_known']) : [];
                            $languages = ['Bengali', 'English', 'Hindi', 'Arabic', 'Urdu', 'French', 'Spanish', 'Other'];
                            foreach ($languages as $lang): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="languages_known[]" value="<?= e($lang) ?>" 
                                        <?= in_array($lang, $knownLangs) ? 'checked' : '' ?>>
                                    <?= e($lang) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Education & Career Section -->
                <div class="form-section">
                    <h3><i class="fas fa-graduation-cap"></i> Education & Career</h3>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Highest Education <span class="required">*</span></label>
                            <select class="input" name="education" required>
                                <option value="">Select Education</option>
                                <?php foreach ($educations as $edu): ?>
                                    <option value="<?= e($edu) ?>" <?= ($profile['education'] ?? '') === $edu ? 'selected' : '' ?>><?= e($edu) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Education Detail</label>
                            <input class="input" type="text" name="education_detail" value="<?= e($profile['education_detail'] ?? '') ?>" placeholder="e.g. B.Sc in Computer Science">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Occupation <span class="required">*</span></label>
                            <select class="input" name="occupation" required>
                                <option value="">Select Occupation</option>
                                <?php foreach ($occupations as $occ): ?>
                                    <option value="<?= e($occ) ?>" <?= ($profile['occupation'] ?? '') === $occ ? 'selected' : '' ?>><?= e($occ) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Occupation Detail</label>
                            <input class="input" type="text" name="occupation_detail" value="<?= e($profile['occupation_detail'] ?? '') ?>" placeholder="e.g. Software Engineer at XYZ Company">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Annual Income</label>
                            <input class="input" type="text" name="annual_income" value="<?= e($profile['annual_income'] ?? '') ?>" placeholder="e.g. 500000">
                        </div>
                        
                        <div class="form-row">
                            <label>Employed In</label>
                            <select class="input" name="employed_in">
                                <option value="">Select Employment Type</option>
                                <option value="Government" <?= ($profile['employed_in'] ?? '') === 'Government' ? 'selected' : '' ?>>Government</option>
                                <option value="Private" <?= ($profile['employed_in'] ?? '') === 'Private' ? 'selected' : '' ?>>Private</option>
                                <option value="Business" <?= ($profile['employed_in'] ?? '') === 'Business' ? 'selected' : '' ?>>Business</option>
                                <option value="Self Employed" <?= ($profile['employed_in'] ?? '') === 'Self Employed' ? 'selected' : '' ?>>Self Employed</option>
                                <option value="Not Working" <?= ($profile['employed_in'] ?? '') === 'Not Working' ? 'selected' : '' ?>>Not Working</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>Job Location</label>
                        <input class="input" type="text" name="job_location" value="<?= e($profile['job_location'] ?? '') ?>" placeholder="City/Country where you work">
                    </div>
                </div>
                
                <!-- Family Information Section -->
                <div class="form-section">
                    <h3><i class="fas fa-home"></i> Family Information</h3>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Family Status</label>
                            <select class="input" name="family_status">
                                <option value="">Select Family Status</option>
                                <option value="Middle Class" <?= ($profile['family_status'] ?? '') === 'Middle Class' ? 'selected' : '' ?>>Middle Class</option>
                                <option value="Upper Middle Class" <?= ($profile['family_status'] ?? '') === 'Upper Middle Class' ? 'selected' : '' ?>>Upper Middle Class</option>
                                <option value="Rich" <?= ($profile['family_status'] ?? '') === 'Rich' ? 'selected' : '' ?>>Rich</option>
                                <option value="Affluent" <?= ($profile['family_status'] ?? '') === 'Affluent' ? 'selected' : '' ?>>Affluent</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Family Type</label>
                            <select class="input" name="family_type">
                                <option value="">Select Family Type</option>
                                <option value="Joint" <?= ($profile['family_type'] ?? '') === 'Joint' ? 'selected' : '' ?>>Joint</option>
                                <option value="Nuclear" <?= ($profile['family_type'] ?? '') === 'Nuclear' ? 'selected' : '' ?>>Nuclear</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>Family Values</label>
                        <select class="input" name="family_values">
                            <option value="">Select Family Values</option>
                            <option value="Traditional" <?= ($profile['family_values'] ?? '') === 'Traditional' ? 'selected' : '' ?>>Traditional</option>
                            <option value="Moderate" <?= ($profile['family_values'] ?? '') === 'Moderate' ? 'selected' : '' ?>>Moderate</option>
                            <option value="Liberal" <?= ($profile['family_values'] ?? '') === 'Liberal' ? 'selected' : '' ?>>Liberal</option>
                        </select>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Father's Name</label>
                            <input class="input" type="text" name="father_name" value="<?= e($profile['father_name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-row">
                            <label>Father's Occupation</label>
                            <input class="input" type="text" name="father_occupation" value="<?= e($profile['father_occupation'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Mother's Name</label>
                            <input class="input" type="text" name="mother_name" value="<?= e($profile['mother_name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-row">
                            <label>Mother's Occupation</label>
                            <input class="input" type="text" name="mother_occupation" value="<?= e($profile['mother_occupation'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>Siblings</label>
                        <textarea class="input" name="siblings" placeholder="Brothers/Sisters details"><?= e($profile['siblings'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <label>Native Place</label>
                        <input class="input" type="text" name="native_place" value="<?= e($profile['native_place'] ?? '') ?>" placeholder="Your ancestral home town/village">
                    </div>
                </div>
                
                <!-- Contact Information Section -->
                <div class="form-section">
                    <h3><i class="fas fa-address-book"></i> Contact Information</h3>
                    
                    <div class="form-row">
                        <label>Phone Number <span class="required">*</span></label>
                        <input class="input" type="tel" name="phone" value="<?= e($profile['phone'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <label>Address</label>
                        <textarea class="input" name="address" placeholder="Your current address"><?= e($profile['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>City</label>
                            <select class="input" name="city">
                                <option value="">Select City</option>
                                <?php foreach ($states as $state): ?>
                                    <option value="<?= e($state) ?>" <?= ($profile['city'] ?? '') === $state ? 'selected' : '' ?>><?= e($state) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>State</label>
                            <select class="input" name="state">
                                <option value="">Select State</option>
                                <?php foreach ($states as $state): ?>
                                    <option value="<?= e($state) ?>" <?= ($profile['state'] ?? '') === $state ? 'selected' : '' ?>><?= e($state) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Country</label>
                            <select class="input" name="country">
                                <option value="">Select Country</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?= e($country) ?>" <?= ($profile['country'] ?? '') === $country ? 'selected' : '' ?>><?= e($country) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Pincode/Zipcode</label>
                            <input class="input" type="text" name="pincode" value="<?= e($profile['pincode'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Lifestyle & About Section -->
                <div class="form-section">
                    <h3><i class="fas fa-heart"></i> Lifestyle & About</h3>
                    
                    <div class="form-row">
                        <label>About Me</label>
                        <textarea class="input" name="about_me" placeholder="Describe yourself, your personality, interests, etc."><?= e($profile['about_me'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <label>Hobbies & Interests</label>
                        <textarea class="input" name="hobbies" placeholder="Your hobbies, activities you enjoy"><?= e($profile['hobbies'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Diet</label>
                            <select class="input" name="diet">
                                <option value="">Select Diet</option>
                                <option value="Vegetarian" <?= ($profile['diet'] ?? '') === 'Vegetarian' ? 'selected' : '' ?>>Vegetarian</option>
                                <option value="Non-Vegetarian" <?= ($profile['diet'] ?? '') === 'Non-Vegetarian' ? 'selected' : '' ?>>Non-Vegetarian</option>
                                <option value="Eggetarian" <?= ($profile['diet'] ?? '') === 'Eggetarian' ? 'selected' : '' ?>>Eggetarian</option>
                                <option value="Vegan" <?= ($profile['diet'] ?? '') === 'Vegan' ? 'selected' : '' ?>>Vegan</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Smoke</label>
                            <select class="input" name="smoke">
                                <option value="">Select Smoking Habit</option>
                                <option value="No" <?= ($profile['smoke'] ?? '') === 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Occasionally" <?= ($profile['smoke'] ?? '') === 'Occasionally' ? 'selected' : '' ?>>Occasionally</option>
                                <option value="Yes" <?= ($profile['smoke'] ?? '') === 'Yes' ? 'selected' : '' ?>>Yes</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>Drink</label>
                        <select class="input" name="drink">
                            <option value="">Select Drinking Habit</option>
                            <option value="No" <?= ($profile['drink'] ?? '') === 'No' ? 'selected' : '' ?>>No</option>
                            <option value="Occasionally" <?= ($profile['drink'] ?? '') === 'Occasionally' ? 'selected' : '' ?>>Occasionally</option>
                            <option value="Yes" <?= ($profile['drink'] ?? '') === 'Yes' ? 'selected' : '' ?>>Yes</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <label>Partner Expectations</label>
                        <textarea class="input" name="partner_expectations" placeholder="What you're looking for in a partner"><?= e($profile['partner_expectations'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <!-- Partner Preferences Section -->
                <div class="form-section">
                    <h3><i class="fas fa-user-friends"></i> Partner Preferences</h3>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Age From</label>
                            <input class="input" type="number" name="age_from" value="<?= e($partnerPrefs['age_from'] ?? '') ?>" min="18" max="100">
                        </div>
                        
                        <div class="form-row">
                            <label>Age To</label>
                            <input class="input" type="number" name="age_to" value="<?= e($partnerPrefs['age_to'] ?? '') ?>" min="18" max="100">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Height From</label>
                            <input class="input" type="text" name="height_from" value="<?= e($partnerPrefs['height_from'] ?? '') ?>" placeholder="e.g. 5'2&quot;">
                        </div>
                        
                        <div class="form-row">
                            <label>Height To</label>
                            <input class="input" type="text" name="height_to" value="<?= e($partnerPrefs['height_to'] ?? '') ?>" placeholder="e.g. 6'0&quot;">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>Marital Status</label>
                        <select class="input" name="pref_marital_status">
                            <option value="">Any</option>
                            <option value="Never Married" <?= ($partnerPrefs['marital_status'] ?? '') === 'Never Married' ? 'selected' : '' ?>>Never Married</option>
                            <option value="Divorced" <?= ($partnerPrefs['marital_status'] ?? '') === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                            <option value="Widowed" <?= ($partnerPrefs['marital_status'] ?? '') === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                            <option value="Separated" <?= ($partnerPrefs['marital_status'] ?? '') === 'Separated' ? 'selected' : '' ?>>Separated</option>
                        </select>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Religion</label>
                            <select class="input" name="pref_religion">
                                <option value="">Any</option>
                                <?php foreach ($religions as $religion): ?>
                                    <option value="<?= e($religion) ?>" <?= ($partnerPrefs['religion'] ?? '') === $religion ? 'selected' : '' ?>><?= e($religion) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Caste</label>
                            <input class="input" type="text" name="pref_caste" value="<?= e($partnerPrefs['caste'] ?? '') ?>" placeholder="Any if no preference">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Education</label>
                            <select class="input" name="pref_education">
                                <option value="">Any</option>
                                <?php foreach ($educations as $edu): ?>
                                    <option value="<?= e($edu) ?>" <?= ($partnerPrefs['education'] ?? '') === $edu ? 'selected' : '' ?>><?= e($edu) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Occupation</label>
                            <select class="input" name="pref_occupation">
                                <option value="">Any</option>
                                <?php foreach ($occupations as $occ): ?>
                                    <option value="<?= e($occ) ?>" <?= ($partnerPrefs['occupation'] ?? '') === $occ ? 'selected' : '' ?>><?= e($occ) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>Annual Income</label>
                        <input class="input" type="text" name="pref_annual_income" value="<?= e($partnerPrefs['annual_income'] ?? '') ?>" placeholder="Any if no preference">
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Country</label>
                            <select class="input" name="pref_country">
                                <option value="">Any</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?= e($country) ?>" <?= ($partnerPrefs['country'] ?? '') === $country ? 'selected' : '' ?>><?= e($country) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>State</label>
                            <select class="input" name="pref_state">
                                <option value="">Any</option>
                                <?php foreach ($states as $state): ?>
                                    <option value="<?= e($state) ?>" <?= ($partnerPrefs['state'] ?? '') === $state ? 'selected' : '' ?>><?= e($state) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>City</label>
                        <input class="input" type="text" name="pref_city" value="<?= e($partnerPrefs['city'] ?? '') ?>" placeholder="Any if no preference">
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Diet</label>
                            <select class="input" name="pref_diet">
                                <option value="">Any</option>
                                <option value="Vegetarian" <?= ($partnerPrefs['diet'] ?? '') === 'Vegetarian' ? 'selected' : '' ?>>Vegetarian</option>
                                <option value="Non-Vegetarian" <?= ($partnerPrefs['diet'] ?? '') === 'Non-Vegetarian' ? 'selected' : '' ?>>Non-Vegetarian</option>
                                <option value="Eggetarian" <?= ($partnerPrefs['diet'] ?? '') === 'Eggetarian' ? 'selected' : '' ?>>Eggetarian</option>
                                <option value="Vegan" <?= ($partnerPrefs['diet'] ?? '') === 'Vegan' ? 'selected' : '' ?>>Vegan</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Smoke</label>
                            <select class="input" name="pref_smoke">
                                <option value="">Any</option>
                                <option value="No" <?= ($partnerPrefs['smoke'] ?? '') === 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Occasionally" <?= ($partnerPrefs['smoke'] ?? '') === 'Occasionally' ? 'selected' : '' ?>>Occasionally</option>
                                <option value="Yes" <?= ($partnerPrefs['smoke'] ?? '') === 'Yes' ? 'selected' : '' ?>>Yes</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>Drink</label>
                        <select class="input" name="pref_drink">
                            <option value="">Any</option>
                            <option value="No" <?= ($partnerPrefs['drink'] ?? '') === 'No' ? 'selected' : '' ?>>No</option>
                            <option value="Occasionally" <?= ($partnerPrefs['drink'] ?? '') === 'Occasionally' ? 'selected' : '' ?>>Occasionally</option>
                            <option value="Yes" <?= ($partnerPrefs['drink'] ?? '') === 'Yes' ? 'selected' : '' ?>>Yes</option>
                        </select>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-row">
                            <label>Body Type</label>
                            <select class="input" name="pref_body_type">
                                <option value="">Any</option>
                                <option value="Slim" <?= ($partnerPrefs['body_type'] ?? '') === 'Slim' ? 'selected' : '' ?>>Slim</option>
                                <option value="Average" <?= ($partnerPrefs['body_type'] ?? '') === 'Average' ? 'selected' : '' ?>>Average</option>
                                <option value="Athletic" <?= ($partnerPrefs['body_type'] ?? '') === 'Athletic' ? 'selected' : '' ?>>Athletic</option>
                                <option value="Heavy" <?= ($partnerPrefs['body_type'] ?? '') === 'Heavy' ? 'selected' : '' ?>>Heavy</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <label>Complexion</label>
                            <select class="input" name="pref_complexion">
                                <option value="">Any</option>
                                <option value="Very Fair" <?= ($partnerPrefs['complexion'] ?? '') === 'Very Fair' ? 'selected' : '' ?>>Very Fair</option>
                                <option value="Fair" <?= ($partnerPrefs['complexion'] ?? '') === 'Fair' ? 'selected' : '' ?>>Fair</option>
                                <option value="Wheatish" <?= ($partnerPrefs['complexion'] ?? '') === 'Wheatish' ? 'selected' : '' ?>>Wheatish</option>
                                <option value="Dark" <?= ($partnerPrefs['complexion'] ?? '') === 'Dark' ? 'selected' : '' ?>>Dark</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>Other Preferences</label>
                        <textarea class="input" name="other_preferences" placeholder="Any other preferences you have"><?= e($partnerPrefs['other_preferences'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">Save Profile</button>
                    <a href="profile.php" class="btn secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
    
    <script>
        // Form validation and enhancements
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.profile-form');
            
            form.addEventListener('submit', function(e) {
                let valid = true;
                
                // Validate required fields
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('error');
                        valid = false;
                    } else {
                        field.classList.remove('error');
                    }
                });
                
                // Validate age range
                const ageFrom = form.querySelector('[name="age_from"]');
                const ageTo = form.querySelector('[name="age_to"]');
                
                if (ageFrom.value && ageTo.value && parseInt(ageFrom.value) > parseInt(ageTo.value)) {
                    alert('"Age From" cannot be greater than "Age To"');
                    valid = false;
                }
                
                if (!valid) {
                    e.preventDefault();
                    alert('Please fill all required fields correctly.');
                }
            });
            
            // Real-time validation for required fields
            const fields = form.querySelectorAll('input, select, textarea');
            fields.forEach(field => {
                field.addEventListener('input', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('error');
                    } else {
                        this.classList.remove('error');
                    }
                });
            });
        });
        
    </script>
</body>
</html>