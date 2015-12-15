<?php

class Post extends ModelBase
{
    public $post_id;
    public $user_id;
    public $category_id;
    public $title;
    public $sub_title;
    public $content;
    public $created_at;
    public $updated_at;

    public function initialize() {
        parent::initialize();
    }


    public function save($data = null, $whiteList = null)
    {
        if(!$this->post_id) {
            $this->created_at = time();
        }
        $this->updated_at = time();

        return parent::save($data, $whiteList);
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model\ResultsetInterface|Post[]
     */
    public static function find($parameters=null) {
        return parent::find($parameters);
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model|Post
     */
    public static function findFirst($parameters=null) {

        $post = parent::findFirst($parameters);
        return $post;
    }




    public function getName() {
        return $this->name;
    }

    public function getBirth() {
        return $this->birth_date;
    }

    public function getCertificateBirth() {
        $birth_date = $this->getBirth();
        return substr($birth_date, 2, 6);
    }

    public function getYear() {
        return substr($this->getBirth(),0, 4);
    }

    public function getMonth() {
        return substr($this->getBirth(),4, 2);
    }

    public function getDay() {
        return substr($this->getBirth(),6, 2);
    }

    public function getGenderNumber() {
        if($this->gender == "남자") {
            return 1;
        } else {
            return 2;
        }
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getH1() {
        return substr($this->getMobile(),0, 3);
    }

    public function getH2() {
        $mobile_length = $this->getMobileLength();
        $substr_length = 0;
        if($mobile_length == 11) {
            $substr_length = 4;
        }
        if($mobile_length == 10) {
            $substr_length = 3;
        }
        return substr($this->getMobile(),3, $substr_length);
    }

    public function getH3() {
        $mobile_length = $this->getMobileLength();
        $start_index = 0;
        if($mobile_length == 11) {
            $start_index = 7;
        }
        if($mobile_length == 10) {
            $start_index = 6;
        }
        return substr($this->getMobile(),$start_index, 4);
    }

    public function getMobileLength() {
        return strlen($this->getMobile());
    }

    /**
     * @param $number
     * @return stdClass
     */
    public static function isMobileNumber($number) {
        $result = new stdClass();
        $result->status = false;
        $result->message = "휴대폰번호를 정확히 입력해주세요.";

        $number = preg_replace('/[!#$%^&*()?+=\/-]/', "", $number);

        $number = preg_replace("/[^0-9]/", "", $number);

        if(preg_match("/[^0-9]/", $number)) {
            return $result;
        }

        $number = preg_replace("/[^0-9]/", "", $number);

        if(preg_match("/^01[0-9]{8,9}$/", $number)) {
            $result->status = true;
            $result->message = "성공";
            return $result;
        }
        else {
            return $result;
        }
    }

    public static function getMobileNumber($number) {
        $number = preg_replace('/[!#$%^&*()?+=\/-]/', "", $number);
        $number = preg_replace("/[^0-9]/", "", $number);
        return $number;
    }

    public static function nameValidCheck($name) {
        $result = new stdClass();
        $result->status = false;
        $result->message = "이름을 정확히 입력해주세요.";

//        for($i = 0; $i < strlen($name); $i++) {
//            if(ord($name[$i]) <= 0x80) {
//                return $result;
//            }
//        }

        if(preg_match('/[0-9!@#$%^&+=]+/', $name)) {
            return $result;
        } else {
            $result->status = true;
            $result->message = "성공";
            return $result;
        }
    }

    public static function emailValidCheck($email) {
        $result = new stdClass();
        $result->status = false;
        $result->message = Lang::$msg_email_required;

        if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
            return $result;
        } else {
            $result->status = true;
            $result->message = "성공";
            return $result;
        }
    }

    public static function passwordValidCheck($password) {
        $result = new stdClass();
        $result->status = false;
        $result->message = "8자리 이상,영문/숫자 조합"; // 최소 조건

        if(preg_match('/^.*(?=^.{8,}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/', $password)) {
            $result->status = true;
            $result->message = "성공";
            return $result;
        } else {
            if (!preg_match('/^[0-9A-Za-z]{8,}$/', $password) || !preg_match('/\d/', $password) || !preg_match('/[a-zA-Z]/', $password)) {
                return $result;
            } else {
                $result->status = true;
                $result->message = "성공";
                return $result;
            }
        }

    }


}