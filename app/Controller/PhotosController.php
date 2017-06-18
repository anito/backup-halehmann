<?php

App::uses('AppController', 'Controller');

class PhotosController extends AppController {

  public $name = 'Photos';
  public $uses = array('User', 'Photo');
  
  public function beforeFilter() {
    $this->Auth->allowedActions = array('index', 'uri', 'dev');
    parent::beforeFilter();
  }

  public function index() {
    $this->Photo->recursive = 1;
    
    if ($this->Auth->user()) {
      $user_id = $this->Auth->user('id');
    } else {
      $user = $this->User->find('first', array('conditions' => array('User.username' => DEFAULT_USER)));
      $user_id = $user['User']['id'];
    }
    
    $photos = $this->Photo->findAllByUser_id((string)($user_id));
    $this->set('_serialize', $photos);
    $this->render(SIMPLE_JSON);
  }

  public function view($id = null) {
    if (!$id) {
      $this->flash(__('Invalid photo', true), array('action' => 'index'));
    }
    $this->set('photo', $this->Photo->read(null, $id));
  }

  public function add() {
    if (!empty($this->data)) {
      $this->Photo->create();
      $this->request->data['Photo']['id'] = null;
      if ($this->Auth->user()) {
        $merged = array_merge($this->request->data['Photo'], array('user_id' => $this->Auth->user('id')));
        $this->request->data = $merged;
        if ($this->Photo->save($this->request->data)) {
          $this->flash(__('Image saved.', true), array('action' => 'index'));
          $this->set('_serialize', array('id' => $this->Photo->id));
          $this->render(SIMPLE_JSON);
        } else {
          
        }
      }
    }
    $albums = $this->Photo->Album->find('list');
    $tags = $this->Photo->Tag->find('list');
    $this->set(compact('albums', 'tags'));
  }

  public function edit($id = null) {
    if (!$id && empty($this->request->data)) {
      $this->flash(sprintf(__('Invalid photo', true)), array('action' => 'index'));
    }
    if (!empty($this->request->data)) {

      if ($this->Photo->save($this->request->data)) {
        $this->Session->setFlash(__('The photo has been saved', true));
      } else {
        $this->Session->setFlash(__('The album could not be saved. Please, try again.', true));
      }
    }
    if (empty($this->request->data)) {
      $this->request->data = $this->Photo->read(null, $id);
    }
//    $albums = $this->Photo->Album->find('list');
//    $tags = $this->Photo->Tag->find('list');
//    $this->set(compact('albums', 'tags'));
  }

  public function delete($id = null) {
    if (!$id) {
      $this->flash(sprintf(__('Invalid image', true)), array('action' => 'index'));
    }
    if ($this->Photo->delete($id)) {
      // remove image from filesystem
      $this->remove($id);
      $this->set('_serialize', array('id' => $this->Photo->id));
      $this->render(SIMPLE_JSON);
//      $this->flash(__('Image deleted', true), array('action' => 'index'));
    } else {
      $this->flash(__('Image was not deleted', true), array('action' => 'index'));
      $this->redirect(array('action' => 'index'));
    }
  }

  public function remove($id) {
    $this->autoRender = false;

    if($this->Auth->user('id')) {

      $user_id = $this->Auth->user('id');
      
      App::import('Component', 'File');
      $file = new FileComponent();

      $path = PHOTOS . DS . $user_id . DS . $id;
      $lg_path = $path . DS . 'lg';
      $cache_path = $path . DS . 'cache';
      
      $oldies = glob($lg_path . DS . '*');
      foreach ($oldies as $o) {
        unlink($o);
      }
      $oldies = glob($cache_path . DS . '*');
      foreach ($oldies as $o) {
        unlink($o);
      }
      rmdir($lg_path);
      rmdir($cache_path);
      rmdir($path);
    }
  }

  
  public function recent($max = 10) {
    $this->autoRender = false;
    $this->Photo->recursive = 0;
    
    $json = array();
    if ($this->Auth->user('id')) {
      $user_id = $uid = $this->Auth->user('id');
      
      $params = array('conditions' => array(
                          'Photo.user_id' => $user_id,
                          'Photo.created >' => date('Y-m-d', strtotime('-200 weeks'))), //array of conditions
                      'order' => array('Photo.created DESC'), //string or array defining order
                      'limit' => $max, //int
                  );
              
      $recent = $this->Photo->find('all', $params);
      
    } else {
      $json = array('flash' => '<strong style="color:red">No valid user</strong>');
      $this->response->header("WWW-Authenticate: Negotiate");
    }
//    $this->log($recent, LOG_DEBUG);
    $this->set('_serialize', $recent);
    $this->render(SIMPLE_JSON);
  }

  public function uri($width = 550, $height = 550, $square = 2) {
    $json = array();
    if ($this->Auth->user()) {
      $user_id = $this->Auth->user('id');
    } else {
      $user = $this->User->find('first', array('conditions' => array('User.username' => DEFAULT_USER)));
      $user_id = $user['User']['id'];
    }
    $uid = $user_id;
    
    if (!empty($this->data)) {
//      $this->log('dataarray', LOG_DEBUG);
//      $this->log($this->data, LOG_DEBUG);
      foreach ($this->data as $data) {
//        $this->log('data id:', LOG_DEBUG);
        $id = $data['id'];
//        $this->log($id, LOG_DEBUG);
//        $this->log('data', LOG_DEBUG);
//        $this->log($data, LOG_DEBUG);
        $path = PHOTOS . DS . $uid . DS . $id . DS . 'lg' . DS . '*.*';
//        $this->log($path, LOG_DEBUG);
        $files = glob($path);
        if (!empty($files[0])) {
          $fn = basename($files[0]);
          $options = compact(array('uid', 'id', 'fn', 'width', 'height', 'square'));
          if($square == 4)
            $src = p($options);
          else
            $src = __p($options);

          $return = array($id => array('src' => $src));
          $json[] = $return;
        } else {
//          $this->log('files are empty', LOG_DEBUG);
        }
      }
    }
//    $this->log($this->data, LOG_DEBUG);
      
    $this->set('_serialize', $json);
    $this->render(SIMPLE_JSON);
  }

  
  function dev($method, $args) {
    if (!empty($this->data)) {
      $this->$method($args);
    }
  }
  
  ////
  // Rotate image
  ////
  public function rotate($degree) {
    $this->Photo->recursive = 0;

    App::import('Component', 'Darkroom');
    $darkroom = new DarkroomComponent();
    
    $uid = $this->Auth->user('id');
    foreach ($this->data as $data) {
      $id = $data['id'];
      $image = $this->Photo->read(null, $id);
      $images[] = $image;
      $path = PHOTOS . DS . $uid . DS . $id;
      $lg_local = $path . DS . 'lg' . DS . $image['Photo']['src'];
      $darkroom->rotate($lg_local, $degree);
      $this->clearCaches($image['Photo']['src'], $path);
    }
    $this->set('_serialize', $images);		
    $this->render(SIMPLE_JSON);
  }
  
  ////
  // Rotate image
  ////
  public function rotate_() {
    $ids = explode(',', $this->data['rotate']['id']);
    $degree = $this->data['rotate']['deg'];
    $images = $this->Image->findAll(aa('Image.id', $ids));
    foreach($images as $image) {
      // Paths
      $path = ALBUMS . DS . 'album-' . $image['Album']['id'];
      $lg_local = $path . DS . 'lg' . DS . $image['Image']['src'];
      $lg_original = ensureOriginal($lg_local, $image['Album']['id']);
      $this->Kodak->rotate($lg_original, $lg_local, $degree);
      $this->Image->clearCaches($image['Image']['src'], $path);
    }
    $this->set('images', $images);		
  }
  
  private function clearCaches($str, $path) {
    $caches = glob($path . DS . 'cache' . DS . $str . '*');
    if (!empty($caches)) {
      foreach($caches as $cache) {
        @unlink($cache);
      }
    }
  }
  
  private function _previewOptions($w = 300, $h = 300) {
    return array('width' => $w, 'height' => $h, 'square' => 3);
  }

  private function _use_preview($id, $use = false) {
    App::import('Component', 'File');
    $file = new FileComponent();

    define('TEMP_PATH', PHOTOS . DS . 'tmp');
    define('DEST_PATH', PHOTOS . DS . $id);
    $temp_files = glob(TEMP_PATH . DS . '*');
    if (count($temp_files) < 1)
      return;

    $fn = basename($temp_files[0]);
    $path_to_temp = TEMP_PATH . DS . $fn;
    $ext = $file->returnExt($fn);
    if ($use) {
      if (!is_dir(PHOTOS)) {
        $file->makeDir(PHOTOS);
      }
      if (!is_dir(DEST_PATH)) {
        $file->makeDir(DEST_PATH);
      } else {
        $oldies = glob(DEST_PATH . DS . 'original.*');
        foreach ($oldies as $o) {
          unlink($o);
        }
        $oldies = glob(DEST_PATH . DS . 'cache' . DS . '*');
        foreach ($oldies as $o) {
          unlink($o);
        }
      }

      $source = TEMP_PATH . DS . $fn;
      $dest = DEST_PATH . DS . $fn;
      copy($source, $dest);

      $this->Product->id = $id;
      $this->Product->saveField('image', $fn);
    }

    foreach ($temp_files as $o) {
      unlink($o);
    }
  }
}
