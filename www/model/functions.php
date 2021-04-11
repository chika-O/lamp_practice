<?php
// var_dump用
function dd($var){
  var_dump($var);
  exit();
}

// $urlへリダイレクト
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}


function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}


function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  // 空の配列(↑が配列の形のため、空の配列を返す)
  return array();
}

// セッションに渡された値を返す
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

// sessionの値をvalueに渡す
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

// sessionの値をerrorに渡す
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

// エラーを全て表示する
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

// エラーがある旨を伝える
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

// messageを配列に格納
function set_message($message){
  $_SESSION['__messages'][] = $message;
}

// ??
function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

// ユーザIDが入力されていることを確認
// セッションにユーザidが入っていることを返す
function is_logined(){
  return get_session('user_id') !== '';
}

// 画像ファイルのアップロード　
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}

// ランダムな文字列を取得
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

// ファイル保存場所の変更
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}


function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}


// 文字列の長さを決める
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  // 文字列の長さを取得
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

// 入力形式の正誤確認
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

// 入力形式の正誤確認
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

// 入力形式の正誤確認
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}


function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }
  $mimetype = exif_imagetype($image['tmp_name']);
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}

function h($str){
  return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

// トークンの生成
function get_csrf_token() {
  // トークンにランダムな文字列をセット
  $token = get_random_string(30);
  // セッションに保存
  set_session('csrf_token',$token);
  return $token;
}

// トークンの確認
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  return $token === get_session('csrf_token');
}


