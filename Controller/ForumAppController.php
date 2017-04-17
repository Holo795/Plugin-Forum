<?php
App::uses('CakeTime', 'Utility');
class ForumAppController extends AppController {

    public $components = [
        'Forum.ForumRender'
    ];

    public $atualTheme;

    protected $version = '1.1.4';

    protected function date($date){
        return $this->format(CakeTime::format($date, '%d %B %Y'));
    }

    protected function time($time){
        return $this->format(CakeTime::format($time, '%H:%M'));
    }

    protected function dateAndTime($date){
        return $this->format(CakeTime::format($date, '%d %B %Y %H:%M'));
    }

    protected function notification(){

    }

    protected function format($format) {
        $enDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $enMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'Décember'];
        $frDays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $frMonths = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        return str_replace($enMonths, $frMonths, str_replace($enDays, $frDays, $format));
    }

    protected function dateInscription($id){
        return $this->User->find('first', ['conditions' => ['id' => $id]])['User']['created'];
    }

    protected function lastSeen($id){
        return $this->User->find('first', ['fields' => 'forum-last_activity', 'conditions' => ['id' => $id]])['User']['forum-last_activity'];
    }

    protected function getIdSession(){
        return isset($_SESSION['user']) ? $_SESSION['user'] : false;
    }

    protected function logforum($idUser, $category, $action, $content){
        $this->loadModel('Forum.Historie');
        $this->Historie->add($this->Util->getIP(), $idUser, $category, $action, $content);
    }

    protected function gUBY($id){
        //Bricollage en attendant la prochaine maj cc @Eywek avec le $this->User->getUsernameById($id);
        $search_user = $this->User->find('first', array('conditions' => array('id' => $id)));
        return (!empty($search_user)) ? $search_user['User']['pseudo'] : '';
    }

    protected function forumRender($type, $value){
        return $this->ForumRender->index($type, $value);
    }

    public function theme(){
        //Hack for Justice Thème
        $theme = ($this->theme != 'Justice') ? 'container' : '';
        $this->atualTheme = $theme;
        return $theme;
    }

    protected function urlRew($url){
        $array = [
            '/' => '',
            '{' => '',
            '}' => '',
            '-' => '.',
            ':' => '',
            '?' => '',
            '#' => '',
            '@' => '',
            '~' => '',
            '`' => '',
            '\\' => '',
            ';' => '',
            'http://' => '',
            'www' => '',
            '<script' => '',
            '<?php' => '',
            '<?=' => ''
        ];
        $url = strtr($url, $array);
        return $url;
    }

    protected function core(){
        $array = [
            'version' => $this->version,
            'host' => env('SERVER_NAME')
        ];

        $json = json_encode($array);
        return $json;
    }

    protected function replaceSpace($string){
        return str_replace(" ", "-", $string);
    }

    protected function replaceHyppen($string){
        return str_replace("-", " ", $string);
    }

    protected function buildUri($type, $name, $id, $anchor =  ''){
        if(!empty($anchor)){
            return $this->base.'/'.$type.'/'.$this->replaceSpace($name).'.'.$id.'/#'.$anchor;
        }else{
            return $this->base.'/'.$type.'/'.$this->replaceSpace($name).'.'.$id.'/';
        }
    }

    protected function socialNetwork($id){
        $this->loadModel('Forum.Profile');
        $socialNetworks = $this->Profile->getSocial($id);
        return json_decode($socialNetworks, true);
    }

    /* TODO LIST
        * Notif mp + msg (new table -> type, id, to, notif)
    */
}