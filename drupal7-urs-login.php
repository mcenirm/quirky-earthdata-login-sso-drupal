<?php

/**
 * @file
 */

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", FALSE);
header("Pragma: no-cache");


$urs_server = ""; if (isset($_SERVER['SERVER_NAME'])) {
  $urs_server = $_SERVER['SERVER_NAME'];
}
$urs_remote_user = ""; if (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
  $urs_remote_user = $_SERVER['REDIRECT_REMOTE_USER'];
}
$urs_affiliation = ""; if (isset($_SERVER['REDIRECT_URS_affiliation'])) {
  $urs_affiliation = $_SERVER['REDIRECT_URS_affiliation'];
}
$urs_country = ""; if (isset($_SERVER['REDIRECT_URS_affiliation'])) {
  $urs_country = $_SERVER['REDIRECT_URS_affiliation'];
}
$urs_email = ""; if (isset($_SERVER['REDIRECT_URS_email_address'])) {
  $urs_email = $_SERVER['REDIRECT_URS_email_address'];
}

$urs_first_name = ""; if (isset($_SERVER['REDIRECT_URS_first_name'])) {
  $urs_first_name = $_SERVER['REDIRECT_URS_first_name'];
}
$urs_last_name = ""; if (isset($_SERVER['REDIRECT_URS_last_name'])) {
  $urs_last_name = $_SERVER['REDIRECT_URS_last_name'];
}
$urs_org = ""; if (isset($_SERVER['REDIRECT_URS_organization'])) {
  $urs_org = $_SERVER['REDIRECT_URS_organization'];
}
$urs_study_area = ""; if (isset($_SERVER['REDIRECT_URS_study_area'])) {
  $urs_study_area = $_SERVER['REDIRECT_URS_study_area'];
}
$urs_uid = ""; if (isset($_SERVER['REDIRECT_URS_uid'])) {
  $urs_uid = $_SERVER['REDIRECT_URS_uid'];
}

$urs_query = ""; if (isset($_SERVER['QUERY_STRING'])) {
  $urs_query = $_SERVER['QUERY_STRING'];
}

// Step2, check if user exists in Drupal.
$user = user_load_by_name($urs_remote_user);
$account = $user;

if (!$user) {
  // Step3, user not exist, create user
  // This will generate a random password, you could set your own here
  // user_password(8);.
  $password = $urs_remote_user . "_187771969575";

  // Set up the user fields.
  $fields = array(
    'name' => $urs_remote_user,
    'mail' => $urs_email,
    'pass' => $password,
    'status' => 0,
    'init' => $urs_email,
    'field_first_name' => array(
      'und' => array(
        0 => array(
          'value' => $urs_first_name,
        ),
      ),
    ),
    'field_last_name' => array(
      'und' => array(
        0 => array(
          'value' => $urs_last_name,
        ),
      ),
    ),
    'field_institution_organization' => array(
      'und' => array(
        0 => array(
          'value' => $urs_org,
        ),
      ),
    ),
    'roles' => array(
      DRUPAL_AUTHENTICATED_RID => 'authenticated user',
    ),
  );

  // The first parameter is left blank so a new user is created.
  $account = user_save('', $fields);

  // If you want to send the welcome email, use the following code.
  // Manually set the password so it appears in the e-mail.
  $account->password = $fields['pass'];

  // Send the e-mail through the user module.
  drupal_mail('user', 'register_pending_approval', $urs_email, NULL, array('account' => $account), variable_get('site_mail', 'webteam@itsc.uah.edu'));

  $dest = "/portal/portal-account-created";
  header('Location: ' . $dest, TRUE, 302);
}
else {
  $dest = "/portal/";
  // Get user object if user exists.
  $account = $user;

  // Step4. Login user.
  $password = $urs_remote_user . "_187771969575";
  $uid = user_authenticate($urs_remote_user, $password);

  // First,check if user is blocked;.
  if (user_is_blocked($urs_remote_user)) {
    $dest = "/portal/user-blocked";

    drupal_set_message("Your Account is now Created, but Pending Sysadmin's Approval.");
    $dest = drupal_get_destination();
    drupal_goto('user/login', array('query' => $dest));

  }
  else {
    // Print "User is not blocked\n";.
    if ($uid == 0) {
      $dest = "user-blocked";
      drupal_goto($dest);
    }
    else {
      global $user;
      $user = user_load($uid);

      $login_array = array('name' => $urs_remote_user);
      user_login_finalize($login_array);
      // Step5, redirect to destination.
      $dstr = substr($urs_query, 12);
      if (strpos($dstr, "logout") > 0) {
        $dstr = "home";
      }
      $dest = $dstr;
      drupal_goto($dest);
    }
  }
}
