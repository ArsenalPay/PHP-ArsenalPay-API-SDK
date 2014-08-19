<?php
/** 
 * Шаблон скрипта проверки/колбэка.
 * Здесь показано как принять, проверить и использовать входные данные запроса. 
 **/

/* Пространство имен для PhpSdk */
namespace AMpay;

class CallBack {
    /** 
     * Функция handleCallBack($password) 
     * получает параметры запроса по протоколу HTTPS с помощью метода POST или GET,
     * проверяет наличие всех параметров и подпись запроса, 
     * после проверки возвращает код успешного получения запроса.
     * 
     * $password - пароль для проверки подписи запроса, передается по защищенному каналу.
     */
 
    public function handleCallBack( $password ) {
        /**
         * Массив для работы с параметрами запроса.
         */
        $keyArray = array(
            'ID',           /* Идентификатор ТСП */
            'FUNCTION',     /* Тип запроса */
            'RRN',          /* Идентификатор транзакции */
            'PAYER',        /* Идентификатор плательщика */
            'AMOUNT',       /* Сумма платежа */
            'ACCOUNT',      /* Номер получателя платежа (номер заказа, номер ЛС) на стороне ТСП */
            'STATUS',       /* Статус платежа: check - запрос на проверку номера получателя, */
                            /*                 payment - запрос на передачу статуса платежа */
            'DATETIME',     /* Дата и время в формате ISO-8601 (YYYY-MM-DDThh:mm:ss±hh:mm), УРЛ-кодированное */
            'SIGN',         /* Подпись запроса = md5(md5(ID).md(FUNCTION).md5(RRN).md5(PAYER).md5(AMOUNT).md5(ACCOUNT).md(STATUS).md5(PASSWORD)) */       
            ); 
        
        /**
         * Проверка на присутствие каждого из параметров и их значений в передаваемом запросе. 
         */
       
        foreach( $keyArray as $key ) {
            
            if( is_null( $val = $this->checkParameter( $key ) ) ){
                return "ERR_".$key;
            }
            $keyArray[$key] = $val;
        }
           
        /**
         * Проверка правильности подписи запроса.
         * Если подпись не верна, возвращается ERR_INVALID_SIGN.
         */
        if( !( $this->checkSign( $keyArray['SIGN'],$password,
                $keyArray['ID'], $keyArray['FUNCTION'], $keyArray['RRN'], $keyArray['PAYER'],
                $keyArray['AMOUNT'], $keyArray['ACCOUNT'], $keyArray['STATUS'] ) ) ) {
            return "ERR_INVALID_SIGN";
        }
        
        /**
         *  Все параметры запроса успешно получены. Используйте их для обработки запроса.
         * All parameters are successfully passed. You can use it to handleCallBack a callback. */
        
        /**
         * В зависимости от типа запроса (платеж/проверка получателя платежа)
         * Возвращается OK, либо YES.
         */
        $reply = $this->returnAnswer( $keyArray['FUNCTION'] );
        return $reply;
    } 
    
    /**
     * Функция возврата ответа о получении уведомления в зависимости от типа запроса.
     * $callBackType - тип запроса (payment/check)  
     * Ответ может быть изменен в соответствие с результатом обработки ТСП
     * полученных данных запроса.  
     */
    private function returnAnswer( $callBackType ){
        switch( $callBackType ){
                case 'check':
                   /** Запрос на проверку номера получателя
                   /* "YES" - получатель платежа существует в системе ТСП
                   /* "NO" - получатель не существует в системе ТСП
                   /* "ERR" - ошибка проверки получателя в системе ТСП
                    */
                   $answer = "YES";
                   //$answer = "NO";
                   //$answer = "ERR";
                   break;
                case 'payment':
                    /** Запрос на передачу статуса платежа
                    /* "OK" - Уведомление успешно получено системой ТСП
                    /* "ERR" - Ошибка получения уведомления системой ТСП
                     */
                    $answer = "OK";
                    //$answer = "ERR";
                    break;
                default:
                    $answer = "ERR_STATUS";     
        }  
        return $answer;
    }
     
    /**
     * Комментарии для формирования тестирующего класса PHPUnit для функции проверки подписи checkSign;
     *  
     * @assert ('356ea4adda1e0eae2fb73ce96f59f844','fffff','987698w7e','payment','12345','9147894125','520','233','payment') == true
     * @assert ('01318be4d94929f3133c74ef3ae4c09d','1536232jkib','987698w7e','payment','12345','9147894125','520','233','payment') == true
     * @assert ('417df99941b7afe2a0bf287197358575','dgs5-dk,lxh7','987698w7e','payment','12345','9147894125','520','233','payment') == true
     */
    
    /**
     * Функция проверки подписи.
     * $sign - подпись, передаваемая в запросе;
     * $pass - ключевое слово для формирования подписи, передаваемое в параметрах handleCallBack;
     * $ID, $callBackType, $transactionID,
     * $payerID, $payAmount, $payReceiverNum, $payStatus - 
     * остальные параметры из запроса, участаствующие в формировании подписи.
     * 
     */
    
    public function checkSign( $sign, $pass,
                               $ID, $callBackType, $transactionID, 
                               $payerID, $payAmount, $payReceiverNum, $payStatus ){
        
        $validSign = ( $sign === md5(md5($ID).
                md5($callBackType).md5($transactionID).
                md5($payerID).md5($payAmount).md5($payReceiverNum).
                md5($payStatus).md5($pass) ) )? true : false;
        return $validSign;        
    }
    
     /**
      * Функция проверки присутствия определенного параметра запроса и его значения 
      * в передаваемом массиве $_POST или $_GET
      * 
      * $key - проверяемый параметр
      * $value - значение параметра
      */
    
     private function checkParameter( $key ){
          if( empty( $_REQUEST[$key] )||!array_key_exists( $key,$_REQUEST ) ){
            return null; 
        } 
        else {
            $value = $_REQUEST[$key];
            return $value;
        }
    }
}

