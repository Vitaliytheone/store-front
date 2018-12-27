<?php
namespace payments\methods;

use Yii;
use payments\BasePayment;
use app\models\panel\Payments;

/**
 * Class BaseBank
 * @package payments\methods
 */
abstract class BaseBank extends BasePayment {

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $accnum;

    /**
     * @var string
     */
    protected $accdisp;

    /**
     * @var resource
     */
    protected $ch;

    /**
     * @var string
     */
    public $balance;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var string
     */
    public $cookieFile = '@runtime/cookie.txt';

    /**
     * @var array
     */
    protected static $_payments;

    public function setLogin($user, $pass)
    {
        $this->username = $user;
        $this->password = $pass;
    }

    public abstract function setAccountNumber($accnum);

    public abstract function login();

    public abstract function getTransactions();

    /**
     * @inheritdoc
     */
    public function checkout($payment)
    {
        $day = Yii::$app->params['klikbcaDuration'];
        $date = ($day + 1) * 24 * 60 * 60;
        
        do {
            $this->amount = $payment->amount + (rand(1, 99) / 100);

            if (Payments::find()->andWhere([
                'type' => $payment->type,
                'amount' => $this->amount
            ])->andWhere('date < :date', [
                ':date' => time() - $date
            ])->exists()) {
                continue;
            }

            $payment->amount = $this->amount;
            $payment->save(false);

            break;
        } while(true);

        return static::returnSuccess();
    }

    /**
     * @inheritdoc
     */
    public function processing()
    {

    }

    /**
     * @return bool|string
     */
    public function getCookieFile()
    {
        $cookieFile = Yii::getAlias($this->cookieFile);

        if (!is_file($cookieFile)) {
            file_put_contents($cookieFile, '');
        }

        return $cookieFile;
    }
}