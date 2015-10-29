<?php

class User extends ModelBase
{
    public $user_id;
    public $email;
    public $name;
    public $password;
    public $new_password;
    public $new_password_created_at;
    public $birth_date;
    public $mobile;
    public $gender;
    public $is_admin = 0;
    public $apply_key;

    public $create_time;
    public $last_modified_time;

    public $ci;
    public $di;
    public $ssn;

    public $loan_agreement_created_at;

    public $loan_alter_key;
    public $loan_alter_key_created_at;

    public $is_deleted = 'N';
    public $deleted_at;

    public function initialize() {
        parent::initialize();
    }


    public function save($data = null, $whiteList = null)
    {
        if(!$this->user_id) {
            $this->create_time = time();
        }
        $this->last_modified_time = time();

        return parent::save($data, $whiteList);
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model\ResultsetInterface|User[]
     */
    public static function find($parameters=null) {
        return parent::find($parameters);
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model|User
     */
    public static function findFirst($parameters=null) {

        $user = parent::findFirst($parameters);
        return $user;
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

    /**
     * @return bool
     */
    public function isProgressLoan() {
        $loan_application = LoanApplication::findFirst("user_id = {$this->user_id} AND cd_result = '0000' AND loan_application_status IN ('심사중','투자모집중','정상상환중','연체중','연체후상환중')");
        if($loan_application) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 대출 동의가 필요한 경우 : true
     * @return bool
     */
    public function isLoanAgreement() {
        $agreement_created_at = $this->loan_agreement_created_at;
        $current = time();
        $check_time = $agreement_created_at + (3600 * 24);

        if(!$agreement_created_at) {
            return true;
        }
        if(($check_time - $current) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function validLoanAlterKey() {
        if(!$this->loan_alter_key) {
            return false;
        }

        $loan_alter_key_created_at = $this->loan_alter_key_created_at;
        $check_time = $loan_alter_key_created_at + (3600 * 24);

        $current_time = time();

        if(!$loan_alter_key_created_at) {
            return false;
        }

        if(($check_time > $current_time)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 사용자가 대출상품에 투자한 금액
     * @param int $loan_application_id
     * @return int
     */
    public function getInvestmentAmount($loan_application_id=0) {
        $total = 0;
        $investments = Investment::find("loan_id = {$loan_application_id} AND user_id = {$this->user_id} AND investment_status IN ('입금완료', '입금예정') ");
        if(count($investments) > 0) {
            foreach($investments as $val) {
                $total += $val->getAmount();
            }
        }
        return $total;
    }

    /**
     * 사용자의 투자 가능 여부
     * 사용자가 투자 가능한 경우 : true
     * @return bool
     */
    public function availableInvest() {
        $loan_applications = LoanApplication::find("user_id = {$this->user_id} AND loan_application_status IN ('심사중', '투자모집중', '정상상환중', '연체중', '연체후상환중')");
        if(count($loan_applications) > 0) {
            return false;
        }
        return true;
    }

    /**
     * 첫 투자 인지 아닌지 확인
     * 첫 투자인 경우 : true
     * @return bool
     */
    public function isFirstInvestment() {
        $investments = Investment::find("user_id = {$this->user_id} AND investment_status IN ('입금완료', '입금예정')");
        if(count($investments) > 0) {
            return false;
        }
        return true;
    }

    /**
     * @return int|\Phalcon\Mvc\Model|\Phalcon\Mvc\Model\Resultset
     */
    public function isLoanRequest() {
        $loan_application = LoanApplication::findFirst("user_id = {$this->user_id} AND loan_application_status = '신청중' ORDER BY create_time DESC");
        if(!$loan_application) {
            return 0;
        }
        $current_time = time();
        $check_time = $loan_application->create_time + (3600 * 24);
        if($check_time > $current_time) {
            return $loan_application->loan_application_id;
        }
        return 0;
    }

    /**
     * @return null|stdClass
     */
    public function getRequestInvestList() {
        $obj = null;
        $investments = Investment::find("user_id = {$this->user_id} AND investment_status = '신청'");
        $loan_application_ids = array();
        $investment_amounts = array();

        if(count($investments) == 0) {
            return $obj;
        } else {
            $obj = new stdClass();
            foreach($investments as $key => $val) {
                $loan_application_ids[$key] = $val->loan_id;
                $investment_amounts[$key] = $val->investment_amount;
            }

            error_log(print_r($loan_application_ids, true));

            $obj->loan_application_ids = $loan_application_ids;
            $obj->investment_amounts = $investment_amounts;
        }
        return $obj;
    }

    /**
     * @param $loan_application_id
     * @return Investment|null|\Phalcon\Mvc\Model
     */
    public function isRequestInvestment($loan_application_id) {
        if(!$loan_application_id) {
            return null;
        }

        $investment = Investment::findFirst("user_id = {$this->user_id} AND loan_id = {$loan_application_id} AND investment_status = '신청'");

        if($investment) {
            return $investment;
        }
        return null;
    }

    public function beforeSave() {
        if($this->is_deleted == 'D') {
            $this->deleted_at = time();
            $this->email = date("YmdHis",$this->deleted_at).$this->email;
        }
    }

}