<?php
session_start();
if(isset($_SESSION['facebook_access_token'])){

  require_once __DIR__ . '/config.php';
  require_once __DIR__ . '/Facebook/autoload.php';
  $fb = new Facebook\Facebook([
    'app_id' => "{$app_id}",
    'app_secret' => "{$app_secret}",
    'default_graph_version' => "v2.6",
  ]);
  $accessToken = trim($_SESSION['facebook_access_token']);
  $fb->setDefaultAccessToken("{$accessToken}");

  if(isset($_POST["post_id"]) and is_numeric($_POST["post_id"])){
    try {
      $response = $fb->get('/me');
      $userNode = $response->getGraphNode();

      $getUserID = $userNode["id"];
      $getPostID = (filter_input(INPUT_POST,"post_id"));

      try{
        $getInfoRequest = $fb->get("/{$getUserID}_{$getPostID}/reactions");
        $arrayInfo = json_decode($getInfoRequest->getGraphEdge(),true);
        foreach($arrayInfo as $reactions){
          echo "<p>{$reactions['name']} - {$reactions['type']}</p>";
        }
      } catch(Facebook\Exceptions\FacebookResponseException $e){}

    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
  } else{
    print("<form action method='post'>");
    print("<input type='text' name='post_id' placeholder='post_id' autocomplete='off' />");
    print("<input type='submit' value='LOAD' />");
    print("</form>");
  }


} else{
  print("<a href='auth.php'>Click here to Login</a>");
}
?>
