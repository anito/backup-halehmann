<?php

App::uses('AppController', 'Controller');

class GroupsController extends AppController {

    public $name = 'Groups';

    function beforeFilter() {
        $this->allowedGroups = array('Administrators');
        $this->Auth->allowedActions = array('login', 'logout', 'auth', 'ping', 'add');
        $this->layout = 'cake';
        parent::beforeFilter();
    }

    function beforeRender() {
        $this->response->disableCache();
        parent::beforeRender();
    }
    
    function _groupName() {
        if ($this->Auth->user('id')) {
            $group = $this->Group->findById($this->Auth->user('group_id'));
            return $group['Group']['name'];
        }
    }

    function index() {
        if (!$this->isAuthGroup()) {
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }
        $this->Group->recursive = 0;
        $this->set('groups', $this->paginate());
    }

    function view($id = null) {
        if (!$this->isAuthGroup()) {
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }
        if (!$id) {
            $this->Flash->error(__('Invalid group', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('group', $this->Group->read(null, $id));
    }

    function add() {
        if (!$this->isAuthGroup()) {
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }
        if (!empty($this->request->data)) {
            $this->Group->create();
            if ($this->Group->save($this->request->data)) {
                $this->Flash->success(__('The group has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The group could not be saved. Please, try again.', true));
            }
        }
    }

    function edit($id = null) {
        if (!$this->isAuthGroup()) {
            $this->redirect(array('action' => 'login'));
        }
        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__('Invalid group', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Group->save($this->request->data)) {
                $this->Flash->success(__('The group has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The group could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Group->read(null, $id);
        }
    }

    function delete($id = null) {
        if (!$this->isAuthGroup()) {
            $this->redirect(array('action' => 'login'));
        }
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for group', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Group->delete($id)) {
            $this->Session->setFlash(__('Group deleted', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Group was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

}

?>