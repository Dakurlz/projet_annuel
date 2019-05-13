<?php

namespace Songfolio\Controllers;

use Songfolio\Core\View;
use Songfolio\Core\Validator;
use Songfolio\Core\Helper;

use Songfolio\Models\Events;
use Songfolio\Models\Categories;

class EventsController
{
    private $event;
    private $category;

    public function __construct(Events  $event, Categories $category)
    {
        $this->event = $event;
        $this->category = $category;
    }

    public function createEventsAction()
    {
        $configForm = $this->event->getFormEvents()['create'];
        $categories = $this->category->getAllBy(['type' => 'event']);
        $alert = self::push($configForm, 'create');
        $configForm['data']['category']['options'] = Categories::prepareCategoriesToSelect($categories);
        self::renderEventsView($alert, $configForm);
    }

     public function updateAction()
     {
        $id = $_REQUEST['id'] ?? '';
        $configForm = $this->event->getFormEvents()['update'];
        $configForm['values'] = (array)$this->event->getOneBy(['id' => $id]);
        $configForm['values']['start_time'] = date('H:i', strtotime($configForm['values']['start_date']));
        $configForm['values']['end_time'] = date('H:i', strtotime($configForm['values']['end_date']));
        $categories = $this->category->getAllBy(['type'=>'event']);
        $configForm['data']['category']['options'] = Categories::prepareCategoriesToSelect($categories);
        self::renderEventsView(null, $configForm);
     }

     
    public function updateEventsAction()
    {
        $configForm = $this->event->getFormEvents()['update'];
        $alert = self::push($configForm,  'update');
        self::listEventsAction($alert);
    }


    public function deleteAction()
    { 
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $this->event->delete(["id" => $id]);
            $alert = Helper::getAlertPropsByAction('delete', 'Événement', false);
        } else {
            $alert = Helper::setAlertError('Une erreur se produit ...');
        };
        
        self::listEventsAction($alert);
    }

    public function listEventsAction($alert = null): void
    {
        $events = $this->event->getAllData();
        $view = new View('admin/events/list', 'back');
        $view->assign('listEvents', $events);
        $view->assign('categories', $this->category->getAllBy(['type' => 'event']));
        if (!empty($alert)) $view->assign('alert', $alert);
    }

    public function renderEventsView($alert, array $configForm)
    {
        $view = new View('admin/events/create', 'back');
        if (!empty($alert)) $view->assign('alert', $alert);
        $view->assign('configFormEvent', $configForm);
    }

    private function push($configForm, $action)
    {
        $method = strtoupper($configForm["config"]["method"]);
        $data = $GLOBALS["_" . $method];
        // \debug($data);



        if (!empty($data)) {
            if ($_SERVER["REQUEST_METHOD"] !== $method || empty($data)) {
                return false;
            }
            $validator = new Validator($configForm, $data);
            $errors = $validator->getErrors();

            if (empty($errors) && (!$this->event->getOneBy(['displayName' => $data['displayName']]) || isset($_REQUEST['id']))) {
                $start_date = $data['start_date'];
                $start_time = $data['start_time'];
                $timestamp_start_date = date('Y-m-d H:i:s',strtotime("$start_date $start_time"));
        
                $end_date = $data['end_date'];
                $end_time = $data['end_time'];
                $timestamp_end_date = date('Y-m-d H:i:s',strtotime("$end_date $end_time"));

                isset($_REQUEST['id']) ? $this->event->__set('id', $_REQUEST['id']) : null;
                $fileName = Helper::uploadImage('public/uploads/events/', 'img_dir');

                $this->event->__set('displayName', $data['displayName']);
                $this->event->__set('type', (int)$data['category']);
                $this->event->__set('status', $data['status']);
                $this->event->__set('start_date', $timestamp_start_date);
                $this->event->__set('end_date', $timestamp_end_date);
                isset($fileName) ? $this->event->__set('img_dir', $fileName) : null;
                $this->event->__set('details', $data['details']);
                $this->event->__set('rate', (float)$data['rate']);
                // ADDRESS
                $this->event->__set('address', $data['address']);
                $this->event->__set('city', $data['city']);
                $this->event->__set('postal_code', $data['postal_code']);
                $this->event->save();

                return Helper::getAlertPropsByAction($action, 'Événement', false);
            } else {
                if (empty($errors)) {
                    return Helper::setAlertError('Événement existe déjà');
                }
                return Helper::setAlertErrors($errors);
            }
        }
        return false;
    }
}
