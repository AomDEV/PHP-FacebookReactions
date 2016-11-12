<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => "{$app_id}",
  'app_secret' => "{$app_secret}",
  'default_graph_version' => "v2.6",
]);

$url = "http".(($_SERVER['SERVER_PORT']==443) ?"s://":"://").$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$helper = $fb->getRedirectLoginHelper();
if(!isset($_GET["code"])){
  $permissions = ['email', 'user_posts', 'publish_actions']; // optional
  $loginUrl = $helper->getLoginUrl($url, $permissions);
  header("Location: {$loginUrl}");
  print("Redirecting you to Facebook...");
} else{
  try {
    $accessToken = $helper->getAccessToken();
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }

  if (isset($accessToken)) {
    // Logged in!
    $_SESSION['facebook_access_token'] = (string) $accessToken;
    header("Location: index.php;");
    print("Connected to Facebook.");
    // Now you can redirect to another page and use the
    // access token from $_SESSION['facebook_access_token']
  } else{ print("Access Denied."); }
}
?>
